<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\ReservationStatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Services\NotificationService;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        // Load user relationship for customer info
        $q = Reservation::with(['user']);
        if (in_array($status, ['pending','approved','declined', 'cancelled'], true)) {
            $q->where('status', $status);
        }

        $reservations = $q->latest()->paginate(10)->withQueryString();

        $counts = Reservation::selectRaw('status, COUNT(*) total')
            ->groupBy('status')->pluck('total','status');

        return view('admin.reservations.index', compact('reservations','status','counts'));
    }

    public function show(Reservation $reservation)
    {
        // Load all necessary relationships
        $reservation->load([
            'user',
            'items.menu.items', // menu items
            'items.menu.items.recipes.inventoryItem' // for inventory checks
        ]);
        
        return view('admin.reservations.show', ['r' => $reservation]);
    }
    /**
     * Check inventory availability for a reservation
     */
    public function checkInventory(Reservation $reservation)
    {
        $reservation->load(['items.menu.items.recipes.inventoryItem']);
        
        $insufficientItems = [];
        $guests = $reservation->guest_count;

        foreach ($reservation->items as $resItem) {
            $menu = $resItem->menu;
            $bundleQty = $resItem->quantity ?? 1;
            if (!$menu) continue;

            foreach ($menu->items as $food) {
                foreach ($food->recipes as $recipe) {
                    $ingredient = $recipe->inventoryItem;
                    if (!$ingredient) continue;
                    
                    $required = (float)($recipe->quantity_needed ?? 0) * $bundleQty * $guests;
                    if ($required <= 0) continue;
                    
                    $available = (float)($ingredient->qty ?? 0);
                    
                    if ($available < $required) {
                        $insufficientItems[] = [
                            'name' => $ingredient->name,
                            'required' => $required,
                            'available' => $available,
                            'shortage' => $required - $available,
                            'unit' => $ingredient->unit ?? 'units',
                        ];
                    }
                }
            }
        }

        return response()->json([
            'sufficient' => empty($insufficientItems),
            'insufficient_items' => $insufficientItems,
        ]);
    }

    public function approve(Request $request, Reservation $reservation)
    {
        // Check if this is a forced approval (override)
        $forceApprove = $request->input('force_approve', false);

        if (!$forceApprove && $reservation->status !== 'pending') {
            return redirect()
                ->route('admin.reservations.show', $reservation)
                ->with('error', 'Only pending reservations can be approved.');
        }

        // Block approvals that overlap an already approved reservation
        $overlap = $this->findOverlappingApprovedReservation($reservation);
        if ($overlap) {
            $conflictId = $overlap['reservation']->id ?? null;
            $conflictDate = $overlap['date'] ?? null;
            $conflictDateLabel = $conflictDate
                ? \Carbon\Carbon::parse($conflictDate)->format('M d, Y')
                : null;

            return redirect()
                ->route('admin.reservations.show', $reservation)
                ->with('overlap_warning', true)
                ->with('overlap_reservation_id', $conflictId)
                ->with('overlap_reservation_date', $conflictDateLabel)
                ->with('error', $conflictId
                    ? "This reservation overlaps with reservation #{$conflictId}."
                    : 'This reservation overlaps with an already approved reservation.');
        }

        // If not forced, check inventory availability
        if (!$forceApprove) {
            $reservation->load(['items.menu.items.recipes.inventoryItem']);
            $insufficientItems = $this->getInsufficientItems($reservation);
            
            if (!empty($insufficientItems)) {
                return redirect()
                    ->route('admin.reservations.show', $reservation)
                    ->with('inventory_warning', true)
                    ->with('insufficient_items', $insufficientItems);
            }
        }

        DB::transaction(function () use ($reservation) {
            $reservation->status = 'approved';
            $reservation->save();

            // Deduct inventory based on recipes (guard every relation)
            $guests = $reservation->guest_count;

            foreach ($reservation->items as $resItem) {
                $menu = $resItem->menu;
                $bundleQty = $resItem->quantity ?? 1;
                if (!$menu) continue;

                foreach ($menu->items as $food) {
                    foreach ($food->recipes as $recipe) {
                        $ingredient = $recipe->inventoryItem;
                        if (!$ingredient) continue;
                        $deduct = (float)($recipe->quantity_needed ?? 0) * $bundleQty * $guests;
                        if ($deduct <= 0) continue;
                        $ingredient->qty = max(0, ($ingredient->qty ?? 0) - $deduct);
                        $ingredient->save();
                    }
                }
            }
        });

        $this->notifyCustomer($reservation, 'approved');

        // Create notification for admins/superadmin
        $this->createAdminNotification('reservation_approved', 'reservations', "Reservation #{$reservation->id} has been approved", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('accepted', true)
            ->with('success', 'Reservation approved and inventory updated.');
    }

    /**
     * Get list of insufficient inventory items for a reservation
     */
    protected function getInsufficientItems(Reservation $reservation)
    {
        $insufficientItems = [];
        $guests = $reservation->guest_count;

        foreach ($reservation->items as $resItem) {
            $menu = $resItem->menu;
            $bundleQty = $resItem->quantity ?? 1;
            if (!$menu) continue;

            foreach ($menu->items as $food) {
                foreach ($food->recipes as $recipe) {
                    $ingredient = $recipe->inventoryItem;
                    if (!$ingredient) continue;
                    
                    $required = (float)($recipe->quantity_needed ?? 0) * $bundleQty * $guests;
                    if ($required <= 0) continue;
                    
                    $available = (float)($ingredient->qty ?? 0);
                    
                    if ($available < $required) {
                        $key = $ingredient->id;
                        if (!isset($insufficientItems[$key])) {
                            $insufficientItems[$key] = [
                                'name' => $ingredient->name,
                                'required' => 0,
                                'available' => $available,
                                'unit' => $ingredient->unit ?? 'units',
                            ];
                        }
                        $insufficientItems[$key]['required'] += $required;
                    }
                }
            }
        }

        // Calculate shortage for each item
        foreach ($insufficientItems as $key => $item) {
            $insufficientItems[$key]['shortage'] = $item['required'] - $item['available'];
        }

        return array_values($insufficientItems);
    }

    /**
     * Find an overlapping approved reservation (by date + time).
     */
    protected function findOverlappingApprovedReservation(Reservation $reservation): ?array
    {
        [$startDate, $endDate] = $this->getReservationDateRange($reservation);

        $query = Reservation::query()
            ->where('status', 'approved')
            ->whereDate('event_date', '<=', $endDate->format('Y-m-d'))
            ->whereDate(DB::raw('COALESCE(end_date, event_date)'), '>=', $startDate->format('Y-m-d'));

        if (!empty($reservation->id)) {
            $query->where('id', '!=', $reservation->id);
        }

        $candidates = $query->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $targetSlots = $this->getReservationSlots($reservation);

        foreach ($candidates as $other) {
            $otherSlots = $this->getReservationSlots($other);
            foreach ($targetSlots as $date => [$startA, $endA]) {
                if (!isset($otherSlots[$date])) {
                    continue;
                }

                [$startB, $endB] = $otherSlots[$date];
                if ($this->timeRangesOverlap($startA, $endA, $startB, $endB)) {
                    return ['reservation' => $other, 'date' => $date];
                }
            }
        }

        return null;
    }

    protected function getReservationDateRange(Reservation $reservation): array
    {
        $startDate = $reservation->event_date
            ? \Carbon\Carbon::parse($reservation->event_date)
            : \Carbon\Carbon::parse($reservation->date ?? $reservation->created_at);

        $endDate = $reservation->end_date
            ? \Carbon\Carbon::parse($reservation->end_date)
            : $startDate->copy();

        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        return [$startDate->startOfDay(), $endDate->startOfDay()];
    }

    protected function getReservationSlots(Reservation $reservation): array
    {
        [$startDate, $endDate] = $this->getReservationDateRange($reservation);

        $dayTimes = $reservation->day_times ?? [];
        if (is_string($dayTimes)) {
            $decoded = json_decode($dayTimes, true);
            $dayTimes = is_array($decoded) ? $decoded : [];
        }

        $fallbackRange = $reservation->event_time ?? $reservation->time ?? null;
        $slots = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $startTime = null;
            $endTime = null;
            $rangeString = $fallbackRange;

            if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                $timeData = $dayTimes[$dateKey];
                if (is_array($timeData)) {
                    $startTime = $timeData['start_time'] ?? $timeData['start'] ?? $timeData['time_start'] ?? null;
                    $endTime = $timeData['end_time'] ?? $timeData['end'] ?? $timeData['time_end'] ?? null;
                } elseif (is_string($timeData)) {
                    $rangeString = $timeData;
                }
            }

            [$startMin, $endMin] = $this->normalizeTimeRange($startTime, $endTime, $rangeString);
            $slots[$dateKey] = [$startMin, $endMin];
        }

        return $slots;
    }

    protected function normalizeTimeRange(?string $startTime, ?string $endTime, ?string $rangeString): array
    {
        $startMin = $this->parseTimeToMinutes($startTime);
        $endMin = $this->parseTimeToMinutes($endTime);

        if (($startMin === null || $endMin === null) && !empty($rangeString)) {
            [$rangeStart, $rangeEnd] = $this->parseRangeString($rangeString);
            if ($startMin === null) $startMin = $rangeStart;
            if ($endMin === null) $endMin = $rangeEnd;
        }

        // If still missing or invalid, treat as full-day to avoid false approvals.
        if ($startMin === null || $endMin === null || $endMin <= $startMin) {
            return [0, 1440];
        }

        return [$startMin, $endMin];
    }

    protected function parseRangeString(string $range): array
    {
        $parts = preg_split('/\s*-\s*/', trim($range));
        if (count($parts) >= 2) {
            return [
                $this->parseTimeToMinutes($parts[0]),
                $this->parseTimeToMinutes($parts[1])
            ];
        }

        if (count($parts) === 1) {
            return [$this->parseTimeToMinutes($parts[0]), null];
        }

        return [null, null];
    }

    protected function parseTimeToMinutes(?string $timeString): ?int
    {
        if ($timeString === null) return null;
        $timeString = trim($timeString);
        if ($timeString === '') return null;

        if (preg_match('/^\d{1,2}$/', $timeString)) {
            $hour = (int) $timeString;
            if ($hour >= 0 && $hour <= 23) {
                return $hour * 60;
            }
        }

        $formats = ['H:i', 'H:i:s', 'g:i A', 'g:iA', 'g A', 'gA', 'g:i a', 'g:ia'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $timeString);
            if ($dt !== false) {
                return ((int) $dt->format('H')) * 60 + (int) $dt->format('i');
            }
        }

        try {
            $dt = \Carbon\Carbon::parse($timeString);
            return ($dt->hour * 60) + $dt->minute;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function timeRangesOverlap(int $startA, int $endA, int $startB, int $endB): bool
    {
        return $startA < $endB && $startB < $endA;
    }

    public function decline(Request $request, Reservation $reservation)
    {
        $data = $request->validate(['reason' => 'required|string|max:1000']);

        $reservation->status = 'declined';
        $reservation->decline_reason = $data['reason'];
        $reservation->save();

        $this->notifyCustomer($reservation, 'declined', $data['reason']);

        // Create notification for admins/superadmin
        $this->createAdminNotification('reservation_declined', 'reservations', "Reservation #{$reservation->id} has been declined", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'reason' => $data['reason'],
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('declined', true)
            ->with('success', 'Reservation declined and customer notified.');
    }

    /** Email + SMS with graceful fallbacks (no crash if not configured locally) */
    protected function notifyCustomer(Reservation $reservation, string $status, ?string $reason = null): void
    {
        $notification = new ReservationStatusChanged($reservation, $status, $reason);

        // Email target: use loaded user or resolve relation, then fallback to reservation email
        $user = $reservation->user;
        if ($user) {
            $user->notify($notification);
        } elseif (!empty($reservation->email)) {
            NotificationFacade::route('mail', $reservation->email)->notify($notification);
        }

        // SMS (Vonage) only if configured
        $hasVonage = (bool) (config('services.vonage.key') && config('services.vonage.secret'));
        if ($hasVonage) {
            $phone = $reservation->contact_number
                ?? optional($reservation->user)->phone
                ?? optional($reservation->user)->mobile
                ?? null;
            if ($phone) {
                NotificationFacade::route('vonage', $phone)->notify($notification);
            }
        }
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        (new NotificationService())->createAdminNotification($action, $module, $description, $metadata);
    }

    public function create(Request $request)
        {
            // 1. Retrieve data from session (saved by postDetails)
            $reservationData = session('reservation_data');

            // Security check: If no session data exists, send them back to Step 1
            if (!$reservationData) {
                return redirect()->route('reservation_form')
                    ->with('error', 'Please fill out the reservation details first.');
            }

            // 2. Fetch menus
            $menus = \App\Models\Menu::with('items')->get()->groupBy(['meal_time', 'type']);

            // 3. Setup prices
            $menuPrices = [];
            $defaultStandardPrice = 150;
            $defaultSpecialPrice = 200;

            foreach ($menus as $meal_time => $types) {
                foreach ($types as $type => $menuList) {
                    if ($menuList->isNotEmpty()) {
                        $menuPrices[$type][$meal_time] = $menuList->map(function($menu) {
                            return (object)[
                                'price' => $menu->price ?? ($menu->type === 'special' ? 200 : 150)
                            ];
                        });
                    }
                }
            }

            return view('customer.reservation_form_menu', compact('menus', 'reservationData', 'menuPrices'));
        }

    public function store(Request $request)
    {
        // Get reservation data from session
        $reservationData = session('reservation_data', []);
        
        // Validate the incoming request data from the menu selection form
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'reservations' => 'required|array',
            'reservations.*.*.category' => 'required|string',
            'reservations.*.*.menu' => 'required|integer|exists:menus,id',
            'reservations.*.*.qty' => 'required|integer|min:0',
        ]);

        // Calculate total number of persons from menu selections
        $totalPersons = 0;
        foreach ($validated['reservations'] as $day => $meals) {
            foreach ($meals as $meal => $data) {
                $totalPersons += $data['qty'];
            }
        }

        // Extract time information from day_times JSON
        $dayTimes = isset($reservationData['day_times']) ? json_decode($reservationData['day_times'], true) : [];

        // Format times to ensure proper format before storing
        foreach ($dayTimes as $date => &$times) {
            if (isset($times['start_time'])) {
                // Convert "7" to "7:00" and "7:30" stays "7:30"
                $times['start_time'] = $this->formatTimeForStorage($times['start_time']);
            }
            if (isset($times['end_time'])) {
                $times['end_time'] = $this->formatTimeForStorage($times['end_time']);
            }
        }

        // Format event_time for display (use first day's start time)
        $eventTime = '';
        if ($dayTimes && count($dayTimes) > 0) {
            $firstDayKey = array_keys($dayTimes)[0];
            $firstDay = $dayTimes[$firstDayKey];
            if (isset($firstDay['start_time'])) {
                $eventTime = $firstDay['start_time'];
            }
        }

        $reservation = DB::transaction(function () use ($reservationData, $eventTime, $dayTimes, $totalPersons, $validated) {
            // Create the reservation with proper time format
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'event_name' => $reservationData['activity'] ?? 'Catering Reservation',
                'event_date' => $reservationData['start_date'] ?? now()->format('Y-m-d'),
                'end_date' => $reservationData['end_date'] ?? null,
                'event_time' => $eventTime, // Store formatted time
                'day_times' => $dayTimes, // Store the complete JSON for multi-day times
                'number_of_persons' => $totalPersons,
                'special_requests' => $validated['notes'] ?? null,
                'status' => 'pending',
                // Add additional fields
                'contact_person' => $reservationData['name'] ?? null,
                'department' => $reservationData['department'] ?? null,
                'address' => $reservationData['address'] ?? null,
                'email' => $reservationData['email'] ?? null,
                'contact_number' => $reservationData['phone'] ?? null,
                'venue' => $reservationData['venue'] ?? null,
                'project_name' => $reservationData['project_name'] ?? null,
                'account_code' => $reservationData['account_code'] ?? null,
            ]);

            // Save reservation items (menu selections)
            foreach ($validated['reservations'] as $day => $meals) {
                foreach ($meals as $meal => $data) {
                    if ($data['qty'] > 0) {
                        // Find menu by ID (not by name)
                        $menu = \App\Models\Menu::find($data['menu']);
                        

                        if (!$menu) {
                            continue;
                        }


                        // Extract day number from key (e.g., "day_1" -> 1)
                        $dayNumber = (int) str_replace('day_', '', $day);

                        \App\Models\ReservationItem::create([
                            'reservation_id' => $reservation->id,
                            'menu_id' => $menu->id,
                            'quantity' => $data['qty'],
                            'day_number' => $dayNumber, // Fixed: Use extracted day number
                            'meal_time' => $meal,
                        ]);


                    }
                }
            }

            return $reservation;
        });

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Placed Order',
            'module'      => 'reservations',
            'description' => 'placed an order',
        ]);

        // Create notification for admins/superadmin about new order
        $this->createAdminNotification('order_placed', 'reservations', "New reservation #{$reservation->id} has been placed", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'total_persons' => $totalPersons,
            'generated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        // Clear reservation data from session
        session()->forget('reservation_data');
        
        // Store receipt reservation ID in session for billing info
        session(['receipt_reservation_id' => $reservation->id]);

        // Return JSON response for AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reservation placed successfully!',
                'redirect_url' => route('reservation_details')
            ]);
        }


        return redirect()->route('reservation_details')
            ->with('success', 'Reservation placed successfully!');
    }

    /**
     * Format time for storage (convert "7" to "7:00", keep "7:30" as is)
     */
    protected function formatTimeForStorage($timeString)
    {
        if (empty($timeString)) {
            return $timeString;
        }
        
        // If it's just a number like "7" or "10", add ":00"
        if (preg_match('/^\d{1,2}$/', $timeString)) {
            return $timeString . ':00';
        }
        
        // If it's already in "HH:MM" format, return as is
        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString)) {
            return $timeString;
        }
        
        // If it's in "HH:MM:SS" format, remove seconds
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeString)) {
            return preg_replace('/:\d{2}$/', '', $timeString);
        }
        
        return $timeString;
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        // Ensure only the owner can cancel their reservation
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this reservation.');
        }

        // Only allow cancellation of pending reservations
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be cancelled.');
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        // Create audit trail
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Cancelled Order',
            'module'      => 'reservations',
            'description' => 'cancelled reservation #' . $reservation->id,
        ]);

        // Create notification for admins/superadmin about cancelled order
        $this->createAdminNotification('order_cancelled', 'reservations', "Reservation #{$reservation->id} has been cancelled by customer", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'total_persons' => $reservation->number_of_persons,
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()->back()->with('success', 'Reservation cancelled successfully.');
    }
    public function uploadReceipt(Request $request, Reservation $reservation)
{
    // Ensure user owns this reservation
    if ($reservation->user_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Unauthorized action.');
    }
    
    // Ensure reservation is approved
    if ($reservation->status !== 'approved') {
        return redirect()->back()->with('error', 'Only approved reservations can upload receipts.');
    }
    
    $request->validate([
        'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
    ]);
    
    // Store the receipt file
    $path = $request->file('receipt')->store('receipts', 'public');
    
    // Update reservation with receipt info
    $reservation->update([
        'receipt_path' => $path,
        'receipt_uploaded_at' => now(),
        'payment_status' => 'paid', // Change to 'under_review' if you want admin approval
    ]);
    
    // Create audit trail
    AuditTrail::create([
        'user_id'     => Auth::id(),
        'action'      => 'Uploaded Receipt',
        'module'      => 'reservations',
        'description' => 'uploaded payment receipt for reservation #' . $reservation->id,
    ]);
    
    return redirect()->back()->with('success', 'Receipt uploaded successfully!');
}
/**
     * Handle Step 1 (Details) submission and save to Session
     */
    public function postDetails(Request $request)
    {
        // 1. Validate the Step 1 inputs
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'day_times'  => 'required|json', // Validate that day_times is present
            // Basic fields
            'name'       => 'required|string',
            'email'      => 'required|email',
            'phone'      => 'required|string',
            'venue'      => 'required|string',
            'activity'   => 'required|string',
            'department' => 'required|string',
            'address'    => 'required|string',
            // Optionals
            'project_name' => 'nullable|string',
            'account_code' => 'nullable|string',
        ]);

        // Block date/time selections that overlap an approved reservation
        $dayTimes = json_decode($validated['day_times'], true) ?? [];
        $tempReservation = new Reservation();
        $tempReservation->event_date = $validated['start_date'];
        $tempReservation->end_date = $validated['end_date'];
        $tempReservation->day_times = $dayTimes;

        $overlap = $this->findOverlappingApprovedReservation($tempReservation);
        if ($overlap) {
            $conflictId = $overlap['reservation']->id ?? null;
            $conflictDate = $overlap['date'] ?? null;
            $conflictDateLabel = $conflictDate
                ? \Carbon\Carbon::parse($conflictDate)->format('M d, Y')
                : null;

            return redirect()
                ->back()
                ->withInput()
                ->with('reservation_conflict', true)
                ->with('conflict_reservation_id', $conflictId)
                ->with('conflict_reservation_date', $conflictDateLabel)
                ->with('error', $conflictId
                    ? "This date and time overlap with reservation #{$conflictId}."
                    : 'This date and time overlap with an existing approved reservation.');
        }

        // 2. Save all valid data into the session
        session(['reservation_data' => $validated]);

        // 3. Redirect to Step 2 (Menu Selection)
        return redirect()->route('reservation.create');
    }
}
