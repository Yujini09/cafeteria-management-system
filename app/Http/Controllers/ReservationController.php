<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\ReservationStatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Services\NotificationService;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = Reservation::with(['user']);
        if (in_array($status, ['pending','approved','declined', 'cancelled'], true)) {
            $q->where('status', $status);
        }
        $reservations = $q->latest()->paginate(10)->withQueryString();
        $counts = Reservation::selectRaw('status, COUNT(*) total')->groupBy('status')->pluck('total','status');
        return view('admin.reservations.index', compact('reservations','status','counts'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'items.menu.items', 'items.menu.items.recipes.inventoryItem']);
        return view('admin.reservations.show', ['r' => $reservation]);
    }

    // --- NEW: EDIT METHOD (Fixes your error) ---
    public function edit(Reservation $reservation)
    {
        // Security: Only allow editing if pending and owned by user
        if ($reservation->user_id !== Auth::id() || $reservation->status !== 'pending') {
            abort(403, 'Unauthorized action. You can only edit pending reservations.');
        }

        // Map database columns back to the session format expected by reservation_form
        $data = [
            'name'         => $reservation->contact_person,
            'department'   => $reservation->department,
            'address'      => $reservation->address,
            'email'        => $reservation->email,
            'phone'        => $reservation->contact_number,
            'activity'     => $reservation->event_name,
            'venue'        => $reservation->venue,
            'project_name' => $reservation->project_name,
            'account_code' => $reservation->account_code,
            // Dates
            'start_date'   => Carbon::parse($reservation->event_date)->format('Y-m-d'),
            'end_date'     => $reservation->end_date ? Carbon::parse($reservation->end_date)->format('Y-m-d') : null,
            // Day times (ensure it's valid JSON string for hidden input)
            'day_times'    => is_array($reservation->day_times) ? json_encode($reservation->day_times) : $reservation->day_times,
        ];

        // Store data in session to pre-fill forms
        session([
            'reservation_data' => $data,
            'editing_reservation_id' => $reservation->id // Mark that we are editing this ID
        ]);

        // Redirect to Step 1 (reservation_form) which will now read the session data
        return redirect()->route('reservation_form')->with('info', 'Please update your reservation details.');
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

    public function postDetails(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'day_times'  => 'required|json', 
            'name'       => 'required|string',
            'email'      => 'required|email',
            'phone'      => 'required|string',
            'venue'      => 'required|string',
            'activity'   => 'required|string',
            'department' => 'required|string',
            'address'    => 'required|string',
            'project_name' => 'nullable|string',
            'account_code' => 'nullable|string',
        ]);

        // Check for overlaps (excluding current reservation if editing)
        $dayTimes = json_decode($validated['day_times'], true) ?? [];
        $tempReservation = new Reservation();
        $tempReservation->event_date = $validated['start_date'];
        $tempReservation->end_date = $validated['end_date'];
        $tempReservation->day_times = $dayTimes;
        
        // If editing, exclude current ID from overlap check so it doesn't conflict with itself
        if (session('editing_reservation_id')) {
            $tempReservation->id = session('editing_reservation_id');
        }

        $overlap = $this->findOverlappingApprovedReservation($tempReservation);
        if ($overlap) {
            $conflictId = $overlap['reservation']->id ?? null;
            return redirect()->back()->withInput()->with('reservation_conflict', true)
                ->with('conflict_reservation_id', $conflictId);
        }

        session(['reservation_data' => $validated]);
        return redirect()->route('reservation.create');
    }

    // --- UPDATED: STORE METHOD (Handles Create AND Update) ---
    public function store(Request $request)
    {
        $reservationData = session('reservation_data', []);
        
        // Validate items
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'reservations' => 'required|array',
            'reservations.*.*.category' => 'required|string',
            'reservations.*.*.menu' => 'required|integer|exists:menus,id',
            'reservations.*.*.qty' => 'required|integer|min:0',
        ]);

        // Calculate total persons across all days/meals
        $totalPersons = 0;
        foreach ($validated['reservations'] as $day => $meals) {
            foreach ($meals as $meal => $data) {
                $totalPersons += $data['qty'];
            }
        }

        // Format times
        $dayTimes = isset($reservationData['day_times']) ? json_decode($reservationData['day_times'], true) : [];
        foreach ($dayTimes as $date => &$times) {
            if (isset($times['start_time'])) $times['start_time'] = $this->formatTimeForStorage($times['start_time']);
            if (isset($times['end_time'])) $times['end_time'] = $this->formatTimeForStorage($times['end_time']);
        }

        $eventTime = '';
        if ($dayTimes && count($dayTimes) > 0) {
            $firstDay = reset($dayTimes);
            if (isset($firstDay['start_time'])) $eventTime = $firstDay['start_time'];
        }

        DB::transaction(function () use ($reservationData, $eventTime, $dayTimes, $totalPersons, $validated) {
            
            // CHECK IF EDITING OR CREATING
            if (session('editing_reservation_id')) {
                $reservation = Reservation::find(session('editing_reservation_id'));
                if (!$reservation) {
                    abort(404, 'Reservation to edit not found.');
                }
                
                // Update existing record
                $reservation->update([
                    'event_name' => $reservationData['activity'],
                    'event_date' => $reservationData['start_date'],
                    'end_date' => $reservationData['end_date'],
                    'event_time' => $eventTime,
                    'day_times' => $dayTimes,
                    'number_of_persons' => $totalPersons,
                    'special_requests' => $validated['notes'] ?? null,
                    // Update contact details as well
                    'contact_person' => $reservationData['name'],
                    'department' => $reservationData['department'],
                    'address' => $reservationData['address'],
                    // Email is typically read-only in UI, but safe to map if present
                    'contact_number' => $reservationData['phone'],
                    'venue' => $reservationData['venue'],
                    'project_name' => $reservationData['project_name'],
                    'account_code' => $reservationData['account_code'],
                ]);

                // Clear old items to replace with new selection
                $reservation->items()->delete();

            } else {
                // Create New
                $reservation = Reservation::create([
                    'user_id' => Auth::id(),
                    'event_name' => $reservationData['activity'] ?? 'Catering Reservation',
                    'event_date' => $reservationData['start_date'] ?? now()->format('Y-m-d'),
                    'end_date' => $reservationData['end_date'] ?? null,
                    'event_time' => $eventTime,
                    'day_times' => $dayTimes,
                    'number_of_persons' => $totalPersons,
                    'special_requests' => $validated['notes'] ?? null,
                    'status' => 'pending',
                    'contact_person' => $reservationData['name'] ?? null,
                    'department' => $reservationData['department'] ?? null,
                    'address' => $reservationData['address'] ?? null,
                    'email' => $reservationData['email'] ?? null,
                    'contact_number' => $reservationData['phone'] ?? null,
                    'venue' => $reservationData['venue'] ?? null,
                    'project_name' => $reservationData['project_name'] ?? null,
                    'account_code' => $reservationData['account_code'] ?? null,
                ]);
            }

            // Save items (Logic is the same for Create and Update)
            foreach ($validated['reservations'] as $day => $meals) {
                foreach ($meals as $meal => $data) {
                    if ($data['qty'] > 0) {
                        $menu = \App\Models\Menu::find($data['menu']);
                        if (!$menu) continue;
                        $dayNumber = (int) str_replace('day_', '', $day);

                        ReservationItem::create([
                            'reservation_id' => $reservation->id,
                            'menu_id' => $menu->id,
                            'quantity' => $data['qty'],
                            'day_number' => $dayNumber,
                            'meal_time' => $meal,
                        ]);
                    }
                }
            }
            
            // Set ID for receipt redirect
            session(['receipt_reservation_id' => $reservation->id]);
        });

        // Clear session data
        session()->forget(['reservation_data', 'editing_reservation_id']);

        $message = session('editing_reservation_id') ? 'Reservation updated successfully!' : 'Reservation placed successfully!';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'redirect_url' => route('reservation_details')]);
        }

        return redirect()->route('reservation_details')->with('success', $message);
    }

    protected function formatTimeForStorage($timeString)
    {
        if (empty($timeString)) return $timeString;
        if (preg_match('/^\d{1,2}$/', $timeString)) return $timeString . ':00';
        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString)) return $timeString;
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeString)) return preg_replace('/:\d{2}$/', '', $timeString);
        return $timeString;
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) return redirect()->back()->with('error', 'Unauthorized.');
        if ($reservation->status !== 'pending') return redirect()->back()->with('error', 'Only pending reservations can be cancelled.');

        $reservation->status = 'cancelled';
        $reservation->save();

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Cancelled Order',
            'module'      => 'reservations',
            'description' => 'cancelled reservation #' . $reservation->id,
        ]);

        $this->createAdminNotification('order_cancelled', 'reservations', "Reservation #{$reservation->id} cancelled by customer", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()->back()->with('success', 'Reservation cancelled successfully.');
    }

    public function uploadReceipt(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) return redirect()->back()->with('error', 'Unauthorized action.');
        if ($reservation->status !== 'approved') return redirect()->back()->with('error', 'Only approved reservations can upload receipts.');
        
        $request->validate(['receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']);
        $path = $request->file('receipt')->store('receipts', 'public');
        
        $reservation->update([
            'receipt_path' => $path,
            'receipt_uploaded_at' => now(),
            'payment_status' => 'paid',
        ]);
        
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Uploaded Receipt',
            'module'      => 'reservations',
            'description' => 'uploaded payment receipt for reservation #' . $reservation->id,
        ]);
        
        return redirect()->back()->with('success', 'Receipt uploaded successfully!');
    }

    // --- HELPER METHODS ---

    public function checkInventory(Reservation $reservation)
    {
        $reservation->load(['items.menu.items.recipes.inventoryItem']);
        $insufficientItems = $this->getInsufficientItems($reservation);

        return response()->json([
            'sufficient' => empty($insufficientItems),
            'insufficient_items' => $insufficientItems,
        ]);
    }

    public function approve(Request $request, Reservation $reservation)
    {
        $forceApprove = $request->input('force_approve', false);

        if (!$forceApprove && $reservation->status !== 'pending') {
            return redirect()->route('admin.reservations.show', $reservation)->with('error', 'Only pending reservations can be approved.');
        }

        $overlap = $this->findOverlappingApprovedReservation($reservation);
        if ($overlap) {
            $conflictId = $overlap['reservation']->id ?? null;
            $conflictDate = $overlap['date'] ?? null;
            $conflictDateLabel = $conflictDate ? Carbon::parse($conflictDate)->format('M d, Y') : null;

            return redirect()->route('admin.reservations.show', $reservation)
                ->with('overlap_warning', true)
                ->with('overlap_reservation_id', $conflictId)
                ->with('overlap_reservation_date', $conflictDateLabel)
                ->with('error', $conflictId ? "Overlap with reservation #{$conflictId}." : 'Overlap detected.');
        }

        if (!$forceApprove) {
            $reservation->load(['items.menu.items.recipes.inventoryItem']);
            $insufficientItems = $this->getInsufficientItems($reservation);
            if (!empty($insufficientItems)) {
                return redirect()->route('admin.reservations.show', $reservation)
                    ->with('inventory_warning', true)
                    ->with('insufficient_items', $insufficientItems);
            }
        }

        DB::transaction(function () use ($reservation) {
            $reservation->status = 'approved';
            $reservation->save();
            $guests = $reservation->number_of_persons; // Changed from guest_count to number_of_persons based on DB schema
            foreach ($reservation->items as $resItem) {
                $menu = $resItem->menu;
                $bundleQty = $resItem->quantity ?? 1; // Actually usually individual qty per menu
                if (!$menu) continue;
                foreach ($menu->items as $food) {
                    foreach ($food->recipes as $recipe) {
                        $ingredient = $recipe->inventoryItem;
                        if (!$ingredient) continue;
                        
                        // NOTE: Logic depends on if quantity is PER PAX or PER BUNDLE. 
                        // Assuming quantity in items is total pax for that meal:
                        $deduct = (float)($recipe->quantity_needed ?? 0) * $resItem->quantity;
                        
                        if ($deduct <= 0) continue;
                        $ingredient->qty = max(0, ($ingredient->qty ?? 0) - $deduct);
                        $ingredient->save();
                    }
                }
            }
        });

        $this->notifyCustomer($reservation, 'approved');
        $this->createAdminNotification('reservation_approved', 'reservations', "Reservation #{$reservation->id} approved", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);

        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Approved.');
    }

    protected function getInsufficientItems(Reservation $reservation)
    {
        $insufficientItems = [];
        // Loop through items to calculate totals
        foreach ($reservation->items as $resItem) {
            $menu = $resItem->menu;
            if (!$menu) continue;
            foreach ($menu->items as $food) {
                foreach ($food->recipes as $recipe) {
                    $ingredient = $recipe->inventoryItem;
                    if (!$ingredient) continue;
                    
                    $required = (float)($recipe->quantity_needed ?? 0) * $resItem->quantity;
                    
                    if ($required <= 0) continue;
                    $available = (float)($ingredient->qty ?? 0);
                    if ($available < $required) {
                        $key = $ingredient->id;
                        if (!isset($insufficientItems[$key])) {
                            $insufficientItems[$key] = ['name' => $ingredient->name, 'required' => 0, 'available' => $available, 'unit' => $ingredient->unit ?? 'units'];
                        }
                        $insufficientItems[$key]['required'] += $required;
                    }
                }
            }
        }
        // Final check
        foreach ($insufficientItems as $key => $item) {
            $insufficientItems[$key]['shortage'] = $item['required'] - $item['available'];
        }
        return array_values($insufficientItems);
    }

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
        if ($candidates->isEmpty()) return null;
        
        $targetSlots = $this->getReservationSlots($reservation);
        foreach ($candidates as $other) {
            $otherSlots = $this->getReservationSlots($other);
            foreach ($targetSlots as $date => [$startA, $endA]) {
                if (!isset($otherSlots[$date])) continue;
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
        $startDate = $reservation->event_date ? Carbon::parse($reservation->event_date) : Carbon::parse($reservation->date ?? $reservation->created_at);
        $endDate = $reservation->end_date ? Carbon::parse($reservation->end_date) : $startDate->copy();
        if ($endDate->lt($startDate)) $endDate = $startDate->copy();
        return [$startDate->startOfDay(), $endDate->startOfDay()];
    }

    protected function getReservationSlots(Reservation $reservation): array
    {
        [$startDate, $endDate] = $this->getReservationDateRange($reservation);
        $dayTimes = $reservation->day_times ?? [];
        if (is_string($dayTimes)) { $decoded = json_decode($dayTimes, true); $dayTimes = is_array($decoded) ? $decoded : []; }
        
        $fallbackRange = $reservation->event_time ?? $reservation->time ?? null;
        $slots = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $startTime = null; $endTime = null; $rangeString = $fallbackRange;
            
            if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                $timeData = $dayTimes[$dateKey];
                if (is_array($timeData)) {
                    $startTime = $timeData['start_time'] ?? $timeData['start'] ?? $timeData['time_start'] ?? null;
                    $endTime = $timeData['end_time'] ?? $timeData['end'] ?? $timeData['time_end'] ?? null;
                } elseif (is_string($timeData)) { $rangeString = $timeData; }
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
        
        if ($startMin === null || $endMin === null || $endMin <= $startMin) return [0, 1440];
        return [$startMin, $endMin];
    }

    protected function parseRangeString(string $range): array
    {
        $parts = preg_split('/\s*-\s*/', trim($range));
        if (count($parts) >= 2) return [$this->parseTimeToMinutes($parts[0]), $this->parseTimeToMinutes($parts[1])];
        if (count($parts) === 1) return [$this->parseTimeToMinutes($parts[0]), null];
        return [null, null];
    }

    protected function parseTimeToMinutes(?string $timeString): ?int
    {
        if ($timeString === null) return null;
        $timeString = trim($timeString);
        if ($timeString === '') return null;
        if (preg_match('/^\d{1,2}$/', $timeString)) { $hour = (int) $timeString; if ($hour >= 0 && $hour <= 23) return $hour * 60; }
        
        $formats = ['H:i', 'H:i:s', 'g:i A', 'g:iA', 'g A', 'gA', 'g:i a', 'g:ia'];
        foreach ($formats as $format) { 
            $dt = \DateTime::createFromFormat($format, $timeString); 
            if ($dt !== false) return ((int) $dt->format('H')) * 60 + (int) $dt->format('i'); 
        }
        
        try { $dt = Carbon::parse($timeString); return ($dt->hour * 60) + $dt->minute; } catch (\Exception $e) { return null; }
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
        $this->createAdminNotification('reservation_declined', 'reservations', "Reservation #{$reservation->id} declined", [
            'reservation_id' => $reservation->id,
            'customer_name' => optional($reservation->user)->name ?? 'Unknown',
            'reason' => $data['reason'],
            'updated_by' => Auth::user()?->name ?? 'Unknown',
        ]);
        
        return redirect()->route('admin.reservations.show', $reservation)->with('success', 'Declined.');
    }

    protected function notifyCustomer(Reservation $reservation, string $status, ?string $reason = null): void
    {
        $notification = new ReservationStatusChanged($reservation, $status, $reason);
        $user = $reservation->user;
        if ($user) { $user->notify($notification); } 
        elseif (!empty($reservation->email)) { NotificationFacade::route('mail', $reservation->email)->notify($notification); }
        
        $hasVonage = (bool) (config('services.vonage.key') && config('services.vonage.secret'));
        if ($hasVonage) {
            $phone = $reservation->contact_number ?? optional($reservation->user)->phone ?? null;
            if ($phone) { NotificationFacade::route('vonage', $phone)->notify($notification); }
        }
    }

    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        (new NotificationService())->createAdminNotification($action, $module, $description, $metadata);
    }
}