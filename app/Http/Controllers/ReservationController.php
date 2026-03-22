<?php

namespace App\Http\Controllers;

use App\Exports\AdminReservationsExport;
use App\Exceptions\IncompatibleRecipeUnitException;
use App\Models\Notification as InAppNotification;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ReservationAdditional;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\ReservationStatusChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
use App\Support\RecipeUnit;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Notifications\ReservationAdditionalAdded;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ReservationController extends Controller
{
    protected const RESERVATION_CREATE_COOLDOWN_MINUTES = 10;
    protected const RESERVATION_DAILY_CAP = 5;

    public function showReservationForm()
    {
        $reservationCreationLimit = null;

        if (Auth::check() && !session()->has('editing_reservation_id')) {
            $reservationCreationLimit = $this->getReservationCreationLimitState(Auth::id());
        }

        return view('customer.reservation_form', compact('reservationCreationLimit'));
    }

    public function index(Request $request)
    {
        [$status, $payment, $department, $createdSort, $createdFrom, $createdTo, $search] = $this->resolveAdminIndexFilters($request);
        $q = $this->buildAdminIndexQuery($status, $payment, $department, $createdFrom, $createdTo, $search);

        $reservations = $q->orderBy('created_at', $createdSort)->paginate(10)->withQueryString();
        $departmentOptions = $this->getAdminDepartmentOptions();

        return view('admin.reservations.index', compact(
            'reservations',
            'status',
            'payment',
            'department',
            'createdSort',
            'departmentOptions',
            'createdFrom',
            'createdTo',
            'search'
        ));
    }

    public function exportIndexPdf(Request $request)
    {
        [$status, $payment, $department, $createdSort, $createdFrom, $createdTo, $search] = $this->resolveAdminIndexFilters($request);

        $reservations = $this->buildAdminIndexQuery($status, $payment, $department, $createdFrom, $createdTo, $search)
            ->orderBy('created_at', $createdSort)
            ->get();

        $exportedBy = Auth::user()?->name ?? 'Unknown';
        $exportedAt = now();
        $filename = 'reservations_' . $exportedAt->format('Y-m-d_His') . '.pdf';

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::EXPORTED_RESERVATION_PDF,
            AuditDictionary::MODULE_RESERVATIONS,
            "exported reservations list as PDF by {$exportedBy}"
        );

        $pdf = Pdf::loadView('admin.reservations.list-pdf', [
            'reservations' => $reservations,
            'status' => $status,
            'payment' => $payment,
            'department' => $department,
            'createdSort' => $createdSort,
            'createdFrom' => $createdFrom,
            'createdTo' => $createdTo,
            'search' => $search,
            'exportedBy' => $exportedBy,
            'exportedAt' => $exportedAt,
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function exportIndexExcel(Request $request)
    {
        [$status, $payment, $department, $createdSort, $createdFrom, $createdTo, $search] = $this->resolveAdminIndexFilters($request);

        $reservations = $this->buildAdminIndexQuery($status, $payment, $department, $createdFrom, $createdTo, $search)
            ->orderBy('created_at', $createdSort)
            ->get();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::EXPORTED_REPORT_EXCEL,
            AuditDictionary::MODULE_RESERVATIONS,
            'exported reservations list as Excel'
        );

        return Excel::download(
            new AdminReservationsExport($reservations),
            'reservations_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

// Add this method to handle the OR Number submission
public function markPaid(\Illuminate\Http\Request $request, $id)
{
    $request->validate([
        'or_number' => 'required|string|max:255'
    ]);

    $reservation = \App\Models\Reservation::findOrFail($id);

    $reservation->update([
        'payment_status' => 'paid',
        'or_number' => $request->or_number
    ]);

    return back()->with('success', 'Reservation marked as paid successfully!');
}

    public function show(Reservation $reservation)
    {
        if (in_array(strtolower((string) $reservation->status), ['cancelled', 'canceled'], true)) {
            abort(404);
        }

        // Load all necessary relationships
        $reservation->load([
            'user',
            'items.menu.items', // menu items
            'items.menu.items.recipes.inventoryItem', // for inventory checks
            'additionals'
        ]);
        
        return view('admin.reservations.show', ['r' => $reservation]);
    }

    public function exportPdf(Reservation $reservation)
    {
        if (in_array(strtolower((string) $reservation->status), ['cancelled', 'canceled'], true)) {
            abort(404);
        }

        $reservation->loadMissing([
            'user',
            'items.menu.items',
            'additionals',
        ]);

        $menuGroups = [];
        $menuSubtotal = 0.0;

        foreach ($reservation->items->sortBy(function ($item) {
            $dayNumber = max(1, (int) ($item->day_number ?? 1));
            $mealTime = (string) ($item->meal_time ?? '');

            return sprintf('%05d-%s-%05d', $dayNumber, $mealTime, (int) $item->id);
        }) as $item) {
            if (!$item->menu) {
                continue;
            }

            $dayNumber = max(1, (int) ($item->day_number ?? 1));
            $price = $this->resolveReservationItemPrice($item);
            $quantity = (int) ($item->quantity ?? 0);
            $lineTotal = $quantity * $price;
            $menuSubtotal += $lineTotal;

            if (!isset($menuGroups[$dayNumber])) {
                $dayDate = $reservation->event_date
                    ? $reservation->event_date->copy()->addDays($dayNumber - 1)
                    : null;

                $menuGroups[$dayNumber] = [
                    'day_number' => $dayNumber,
                    'date' => $dayDate,
                    'items' => [],
                ];
            }

            $menuGroups[$dayNumber]['items'][] = [
                'menu_name' => $item->menu->name ?? 'N/A',
                'components' => $item->menu->items
                    ? $item->menu->items->pluck('name')->filter()->values()->all()
                    : [],
                'meal_time' => ucwords(str_replace('_', ' ', (string) ($item->meal_time ?? ''))),
                'quantity' => $quantity,
                'price' => $price,
                'total' => $lineTotal,
            ];
        }

        ksort($menuGroups);

        $additionals = $reservation->additionals
            ? $reservation->additionals->map(function ($additional) {
                return [
                    'name' => $additional->name ?: 'Additional Charge',
                    'price' => (float) ($additional->price ?? 0),
                ];
            })->values()->all()
            : [];

        $additionalsTotal = (float) ($reservation->additionals?->sum('price') ?? 0);
        $grandTotal = $menuSubtotal + $additionalsTotal;
        $paymentLabel = $this->formatReservationPaymentLabel($reservation);

        $exportedBy = Auth::user()?->name ?? 'Unknown';
        $filename = 'reservation_' . $reservation->id . '.pdf';

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::EXPORTED_RESERVATION_PDF,
            AuditDictionary::MODULE_RESERVATIONS,
            "exported reservation #{$reservation->id} as PDF by {$exportedBy}"
        );

        $pdf = Pdf::loadView('admin.reservations.pdf', [
            'reservation' => $reservation,
            'menuGroups' => $menuGroups,
            'menuSubtotal' => $menuSubtotal,
            'additionals' => $additionals,
            'additionalsTotal' => $additionalsTotal,
            'grandTotal' => $grandTotal,
            'paymentLabel' => $paymentLabel,
            'exportedBy' => $exportedBy,
            'exportedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
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
        if (
            !$reservationData ||
            empty($reservationData['start_date']) ||
            empty($reservationData['end_date']) ||
            empty($reservationData['day_times'])
        ) {
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
        if (Auth::check() && !session()->has('editing_reservation_id')) {
            $reservationCreationLimit = $this->getReservationCreationLimitState(Auth::id());

            if ($reservationCreationLimit['blocked']) {
                return redirect()->route('reservation_form')->withInput();
            }
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after:today',
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
        $invalidDayTimes = fn () => redirect()->back()
            ->withInput()
            ->withErrors(['day_times' => 'Please set a valid start and end time for every selected day.']);

        if (!is_array($dayTimes)) {
            return $invalidDayTimes();
        }

        $currentDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $timeData = $dayTimes[$dateKey] ?? null;

            if (!is_array($timeData)) {
                return $invalidDayTimes();
            }

            $startTime = $timeData['start_time'] ?? $timeData['start'] ?? null;
            $endTime = $timeData['end_time'] ?? $timeData['end'] ?? null;
            $startMinutes = $this->parseTimeToMinutes($startTime);
            $endMinutes = $this->parseTimeToMinutes($endTime);

            if ($startMinutes === null || $endMinutes === null || $startMinutes >= $endMinutes) {
                return $invalidDayTimes();
            }

            $currentDate->addDay();
        }

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
        $editingReservationId = session('editing_reservation_id');
        $isEditing = !empty($editingReservationId);
        $savedReservationId = null;
        $userId = Auth::id();

        if (empty($reservationData['start_date']) || empty($reservationData['end_date']) || empty($reservationData['day_times'])) {
            return redirect()->route('reservation_form')
                ->with('error', 'Please fill out the reservation details first.');
        }
        
        // Validate items
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'reservations' => 'required|array',
            'reservations.*.*.category' => 'required|string',
            'reservations.*.*.menu' => 'required|integer|exists:menus,id',
            'reservations.*.*.qty' => 'required|integer|min:0|max:1000',
        ]);

        $expectedDayCount = Carbon::parse($reservationData['start_date'])
            ->diffInDays(Carbon::parse($reservationData['end_date'])) + 1;

        for ($day = 1; $day <= $expectedDayCount; $day++) {
            $dayMeals = $validated['reservations'][$day] ?? $validated['reservations'][(string) $day] ?? null;

            if (!is_array($dayMeals)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'reservations' => 'Please select at least one menu for each selected date before confirming your reservation.',
                ]);
            }

            $hasSelectionForDay = collect($dayMeals)->contains(function ($mealData) {
                return (int) ($mealData['qty'] ?? 0) > 0;
            });

            if (!$hasSelectionForDay) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'reservations' => 'Please select at least one menu for each selected date before confirming your reservation.',
                ]);
            }
        }

        // Compute party size as the highest pax value across all selected day/meal slots.
        $maxPersons = 0;
        foreach ($validated['reservations'] as $day => $meals) {
            foreach ($meals as $meal => $data) {
                $maxPersons = max($maxPersons, (int) ($data['qty'] ?? 0));
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

        DB::transaction(function () use ($reservationData, $eventTime, $dayTimes, $maxPersons, $validated, $isEditing, $editingReservationId, $userId, &$savedReservationId) {
            if (!$isEditing && $userId) {
                $this->enforceReservationCreationLimits($userId);
            }

            
            // CHECK IF EDITING OR CREATING
            if ($isEditing) {
                $reservation = Reservation::find($editingReservationId);
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
                    'number_of_persons' => $maxPersons,
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
                    'event_date' => $reservationData['start_date'],
                    'end_date' => $reservationData['end_date'],
                    'event_time' => $eventTime,
                    'day_times' => $dayTimes,
                    'number_of_persons' => $maxPersons,
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
            $savedReservationId = $reservation->id;
        });

        if ($savedReservationId) {
            AuditTrail::record(
                Auth::id(),
                $isEditing ? AuditDictionary::UPDATED_RESERVATION : AuditDictionary::CREATED_RESERVATION,
                AuditDictionary::MODULE_RESERVATIONS,
                ($isEditing ? 'updated' : 'created') . " reservation #{$savedReservationId}"
            );
        }

        // Clear session data
        session()->forget(['reservation_data', 'editing_reservation_id']);

        $message = $isEditing ? 'Reservation updated successfully!' : 'Reservation placed successfully!';
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'redirect_url' => route('reservation_details')]);
        }

        return redirect()->route('reservation_details')->with('success', $message);
    }

    protected function enforceReservationCreationLimits(int $userId): void
    {
        $this->lockReservationCreationForUser($userId);

        $reservationCreationLimit = $this->getReservationCreationLimitState($userId);

        if ($reservationCreationLimit['blocked']) {
            throw ValidationException::withMessages([
                'reservations' => $reservationCreationLimit['message'],
            ]);
        }
    }

    protected function lockReservationCreationForUser(int $userId): void
    {
        DB::table('users')
            ->where('id', $userId)
            ->lockForUpdate()
            ->first();
    }

    protected function getReservationCreationLimitState(int $userId): array
    {
        $timezone = config('app.timezone', 'Asia/Manila');
        $now = Carbon::now($timezone);

        $latestReservation = Reservation::query()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->first(['created_at']);

        if ($latestReservation?->created_at) {
            $nextAllowedAt = $latestReservation->created_at
                ->copy()
                ->timezone($timezone)
                ->addMinutes(self::RESERVATION_CREATE_COOLDOWN_MINUTES);

            $remainingSeconds = $now->diffInSeconds($nextAllowedAt, false);

            if ($remainingSeconds > 0) {
                $remainingMinutes = max(1, (int) ceil($remainingSeconds / 60));
                $minuteLabel = $remainingMinutes === 1 ? 'minute' : 'minutes';

                return [
                    'blocked' => true,
                    'type' => 'cooldown',
                    'message' => "Please wait {$remainingMinutes} {$minuteLabel} before creating another reservation.",
                    'note' => 'If you placed an incorrect reservation, please cancel the previous one.',
                ];
            }
        }

        $activeReservationsToday = Reservation::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ])
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->count();

        if ($activeReservationsToday >= self::RESERVATION_DAILY_CAP) {
            return [
                'blocked' => true,
                'type' => 'daily_cap',
                'message' => "You've reached the maximum of ".self::RESERVATION_DAILY_CAP.' reservations for today.',
                'note' => 'If you placed an incorrect reservation, please cancel the previous one.',
            ];
        }

        return [
            'blocked' => false,
            'type' => null,
            'message' => null,
            'note' => null,
        ];
    }

    protected function formatTimeForStorage($timeString)
    {
        if (empty($timeString)) return $timeString;
        if (preg_match('/^\d{1,2}$/', $timeString)) return $timeString . ':00';
        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString)) return $timeString;
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeString)) return preg_replace('/:\d{2}$/', '', $timeString);
        return $timeString;
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
            ? Carbon::parse($reservation->event_date)
            : Carbon::parse($reservation->created_at);

        $endDate = $reservation->end_date
            ? Carbon::parse($reservation->end_date)
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

        $fallbackRange = $reservation->event_time;
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

        if ($startMin === null || $endMin === null || $endMin <= $startMin) {
            return [0, 1440];
        }

        return [$startMin, $endMin];
    }

    protected function parseRangeString(string $range): array
    {
        $parts = preg_split('/\s*-\s*/', trim($range));
        if (count($parts) >= 2) {
            return [$this->parseTimeToMinutes($parts[0]), $this->parseTimeToMinutes($parts[1])];
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
            if ($hour >= 0 && $hour <= 23) return $hour * 60;
        }

        $formats = ['H:i', 'H:i:s', 'g:i A', 'g:iA', 'g A', 'gA', 'g:i a', 'g:ia'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $timeString);
            if ($dt !== false) {
                return ((int) $dt->format('H')) * 60 + (int) $dt->format('i');
            }
        }

        try {
            $dt = Carbon::parse($timeString);
            return ($dt->hour * 60) + $dt->minute;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function timeRangesOverlap(int $startA, int $endA, int $startB, int $endB): bool
    {
        return $startA < $endB && $startB < $endA;
    }

    public function checkInventory(Reservation $reservation)
    {
        try {
            $usage = $this->buildInventoryUsage($reservation);
        } catch (IncompatibleRecipeUnitException $e) {
            return response()->json([
                'sufficient' => false,
                'error' => 'Inventory check failed due to incompatible recipe and stock units.',
                'incompatible_items' => $e->messages(),
            ], 422);
        }

        $insufficient = array_values(array_filter($usage, function ($item) {
            return ($item['shortage'] ?? 0) > 0;
        }));

        return response()->json([
            'sufficient' => count($insufficient) === 0,
            'insufficient_items' => $insufficient,
        ]);
    }

    public function approve(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be approved.');
        }

        $forceApprove = $request->boolean('force_approve');
        $forceOverlapApprove = $request->boolean('force_overlap_approve');

        $overlap = $this->findOverlappingApprovedReservation($reservation);
        if ($overlap && !$forceOverlapApprove) {
            return redirect()->back()->withInput()->with([
                'overlap_warning' => true,
                'overlap_reservation_id' => $overlap['reservation']->id ?? null,
                'overlap_reservation_date' => $overlap['date'] ?? null,
            ]);
        }

        try {
            $usage = $this->buildInventoryUsage($reservation);
        } catch (IncompatibleRecipeUnitException $e) {
            return redirect()->back()->withInput()->withErrors([
                'inventory_units' => $e->messages(),
            ]);
        }

        $insufficient = array_values(array_filter($usage, function ($item) {
            return ($item['shortage'] ?? 0) > 0;
        }));

        if (!$forceApprove && count($insufficient) > 0) {
            return redirect()->back()->withInput()->with([
                'inventory_warning' => true,
                'insufficient_items' => $insufficient,
            ]);
        }

        $performedByUserId = Auth::id();

        DB::transaction(function () use ($reservation, $usage, $performedByUserId) {
            foreach ($usage as $itemId => $row) {
                $required = (float) ($row['required'] ?? 0);
                if ($required <= 0) {
                    continue;
                }

                $inventoryItem = InventoryItem::whereKey($itemId)->lockForUpdate()->first();
                if (!$inventoryItem) {
                    continue;
                }

                $previousBalance = (float) ($inventoryItem->qty ?? 0);
                $newBalance = max(0, $previousBalance - $required);

                $inventoryItem->qty = $newBalance;
                $inventoryItem->save();

                InventoryUsageLog::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'item_name' => $inventoryItem->name ?? ($row['name'] ?? 'Unknown'),
                    'type' => InventoryUsageLog::TYPE_AUTO_DEDUCT,
                    'quantity_change' => round($required * -1, 3),
                    'new_balance' => round($newBalance, 3),
                    'reservation_id' => $reservation->id,
                    'user_id' => $performedByUserId,
                ]);
            }

            $reservation->update([
                'status' => 'approved',
                'decline_reason' => null,
                'payment_status' => $reservation->payment_status ?? 'pending',
            ]);
        });

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::APPROVED_RESERVATION,
            AuditDictionary::MODULE_RESERVATIONS,
            "approved reservation #{$reservation->id}"
        );

        $user = $reservation->user;
        if ($user) {
            NotificationFacade::send($user, new ReservationStatusChanged($reservation, 'approved'));
            (new NotificationService())->createUserNotification(
                $reservation->user_id,
                'reservation_approved',
                'reservations',
                "Reservation #{$reservation->id} approved",
                [
                    'reservation_id' => $reservation->id,
                    'url' => route('reservation.view', $reservation->id),
                    'link_label' => 'View Details',
                ]
            );
        }

        return redirect()->back()->with(['accepted' => true]);
    }

    public function decline(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be declined.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $reservation->update([
            'status' => 'declined',
            'decline_reason' => $data['reason'],
        ]);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::DECLINED_RESERVATION,
            AuditDictionary::MODULE_RESERVATIONS,
            "declined reservation #{$reservation->id}"
        );

        $user = $reservation->user;
        if ($user) {
            NotificationFacade::send($user, new ReservationStatusChanged($reservation, 'declined', $data['reason']));
            (new NotificationService())->createUserNotification(
                $reservation->user_id,
                'reservation_declined',
                'reservations',
                "Reservation #{$reservation->id} declined",
                [
                    'reservation_id' => $reservation->id,
                    'url' => route('reservation.view', $reservation->id),
                    'link_label' => 'View Details',
                ]
            );
        }

        return redirect()->back()->with(['declined' => true, 'success' => 'Reservation declined.']);
    }

    public function storeAdditional(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== 'approved') {
            return redirect()->back()->with('error', 'Additionals can only be added to approved reservations.');
        }

        if (($reservation->payment_status ?? 'pending') === 'paid') {
            return redirect()->back()->with('error', 'Additionals are locked once a reservation is marked as paid.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:140',
            'price' => 'required|numeric|min:0|max:999999.99',
        ]);

        $additional = $reservation->additionals()->create([
            'name' => $data['name'],
            'price' => $data['price'],
            'created_by' => Auth::id(),
        ]);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::ADDED_ADDITIONAL_CHARGE,
            AuditDictionary::MODULE_RESERVATIONS,
            "added additional charge #{$additional->id} to reservation #{$reservation->id}"
        );

        $reservation->loadMissing(['user']);
        $updatedGrandTotal = $this->calculateReservationGrandTotal($reservation);

        $user = $reservation->user;
        if ($user) {
            NotificationFacade::send($user, new ReservationAdditionalAdded($reservation, $additional, $updatedGrandTotal));
            (new NotificationService())->createUserNotification(
                $reservation->user_id,
                'reservation_additional_added',
                'reservations',
                sprintf(
                    'An additional charge of PHP %s was added to reservation #%d. Updated total: PHP %s.',
                    number_format((float) $additional->price, 2),
                    $reservation->id,
                    number_format($updatedGrandTotal, 2)
                ),
                [
                    'reservation_id' => $reservation->id,
                    'additional_id' => $additional->id,
                    'additional_name' => $additional->name,
                    'additional_amount' => (float) $additional->price,
                    'updated_total' => $updatedGrandTotal,
                    'url' => route('reservation.view', $reservation->id),
                    'link_label' => 'View Details',
                ],
                'Reservation Total Updated'
            );
        }

        return redirect()->back()->with('success', 'Additional item added.');
    }

    public function updateAdditional(Request $request, Reservation $reservation, ReservationAdditional $additional)
    {
        if ($additional->reservation_id !== $reservation->id) {
            abort(404);
        }

        if ($reservation->status !== 'approved') {
            return redirect()->back()->with('error', 'Additionals can only be updated for approved reservations.');
        }

        if (($reservation->payment_status ?? 'pending') === 'paid') {
            return redirect()->back()->with('error', 'Additionals are locked once a reservation is marked as paid.');
        }

        // Price is immutable after creation.
        if ($request->has('price') && (float) $request->input('price') !== (float) $additional->price) {
            return redirect()->back()->with('error', 'Additional price is locked after creation and cannot be changed.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:140',
        ]);

        $additional->update([
            'name' => $data['name'],
        ]);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::UPDATED_ADDITIONAL_CHARGE,
            AuditDictionary::MODULE_RESERVATIONS,
            "updated additional charge #{$additional->id} for reservation #{$reservation->id}"
        );

        return redirect()->back()->with('success', 'Additional item updated.');
    }

    public function deleteAdditional(Reservation $reservation, ReservationAdditional $additional)
    {
        if ($additional->reservation_id !== $reservation->id) {
            abort(404);
        }

        if ($reservation->status !== 'approved') {
            return redirect()->back()->with('error', 'Additionals can only be removed from approved reservations.');
        }

        if (($reservation->payment_status ?? 'pending') === 'paid') {
            return redirect()->back()->with('error', 'Additionals are locked once a reservation is marked as paid.');
        }

        $additionalId = $additional->id;
        $additional->delete();

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::DELETED_ADDITIONAL_CHARGE,
            AuditDictionary::MODULE_RESERVATIONS,
            "deleted additional charge #{$additionalId} from reservation #{$reservation->id}"
        );

        return redirect()->back()->with('success', 'Additional item removed.');
    }

    protected function buildInventoryUsage(Reservation $reservation): array
    {
        $reservation->loadMissing(['items.menu.items.recipes.inventoryItem']);

        $usage = [];
        $incompatibleUnits = [];

        foreach ($reservation->items as $reservationItem) {
            $menu = $reservationItem->menu;
            if (!$menu) {
                continue;
            }

            $reservationQty = (float) ($reservationItem->quantity ?? 0);
            if ($reservationQty <= 0) {
                continue;
            }

            foreach ($menu->items as $menuItem) {
                foreach ($menuItem->recipes as $recipe) {
                    $inventoryItem = $recipe->inventoryItem;
                    if (!$inventoryItem) {
                        continue;
                    }

                    $totalNeededRecipe = (float) ($recipe->quantity_needed ?? 0) * $reservationQty;
                    $recipeUnit = RecipeUnit::normalize($recipe->unit) ?? RecipeUnit::normalize($inventoryItem->unit);
                    $required = RecipeUnit::convertToStockUnit($totalNeededRecipe, $recipeUnit, $inventoryItem->unit);
                    if ($required === null) {
                        $incompatibleUnits[] = [
                            'context' => 'Reservation approval',
                            'menu_item' => $menuItem->name ?? ('Menu item #' . ($menuItem->id ?? '?')),
                            'ingredient' => $inventoryItem->name ?? ('Inventory item #' . ($inventoryItem->id ?? '?')),
                            'recipe_unit' => RecipeUnit::display($recipe->unit) ?: ((string) ($recipe->unit ?? 'unknown')),
                            'stock_unit' => RecipeUnit::display($inventoryItem->unit) ?: ((string) ($inventoryItem->unit ?? 'unknown')),
                        ];
                        continue;
                    }

                    if ($required <= 0) {
                        continue;
                    }

                    $id = $inventoryItem->id;
                    if (!isset($usage[$id])) {
                        $usage[$id] = [
                            'id' => $id,
                            'name' => $inventoryItem->name ?? 'Unknown',
                            'unit' => RecipeUnit::display($inventoryItem->unit) ?: ($inventoryItem->unit ?? ''),
                            'required' => 0.0,
                            'available' => (float) ($inventoryItem->qty ?? 0),
                            'shortage' => 0.0,
                        ];
                    }

                    $usage[$id]['required'] += $required;
                }
            }
        }

        if (!empty($incompatibleUnits)) {
            throw new IncompatibleRecipeUnitException($incompatibleUnits);
        }

        foreach ($usage as &$row) {
            $row['shortage'] = max(0, ($row['required'] ?? 0) - ($row['available'] ?? 0));
        }
        unset($row);

        return $usage;
    }

    protected function calculateReservationGrandTotal(Reservation $reservation): float
    {
        $menuTotal = (float) ($reservation->total_amount ?? 0);

        if ($menuTotal <= 0) {
            $reservation->loadMissing(['items.menu']);
            $menuTotal = 0.0;

            foreach ($reservation->items as $item) {
                if (!$item->menu) {
                    continue;
                }

                $menuTotal += ((int) ($item->quantity ?? 0)) * $this->resolveReservationItemPrice($item);
            }
        }

        $additionalsTotal = (float) $reservation->additionals()->sum('price');

        return $menuTotal + $additionalsTotal;
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) return redirect()->back()->with('error', 'Unauthorized.');
        if ($reservation->status !== 'pending') return redirect()->back()->with('error', 'Only pending reservations can be cancelled.');

        $reservationId = (int) $reservation->id;

        DB::transaction(function () use ($reservation, $reservationId) {
            $this->deleteReservationNotifications($reservationId);
            $reservation->delete();
        });

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::CANCELLED_RESERVATION,
            AuditDictionary::MODULE_RESERVATIONS,
            "cancelled reservation #{$reservationId} (deleted)"
        );

        if ((int) session('receipt_reservation_id', 0) === $reservationId) {
            session()->forget('receipt_reservation_id');
        }

        return redirect()->route('reservation_details')->with('success', 'Reservation cancelled and removed successfully.');
    }

    protected function resolveReservationItemPrice(ReservationItem $item): float
    {
        $price = (float) ($item->price ?? $item->menu->price ?? 150);

        if ($price <= 0) {
            $price = $item->menu && $item->menu->type === 'special' ? 200.0 : 150.0;
        }

        return $price;
    }

    protected function formatReservationPaymentLabel(Reservation $reservation): string
    {
        if (in_array($reservation->status, ['declined', 'cancelled'], true)) {
            return 'N/A';
        }

        return ($reservation->payment_status ?? 'unpaid') === 'paid' ? 'Paid' : 'Unpaid';
    }

    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        (new NotificationService())->createAdminNotification($action, $module, $description, $metadata);
    }

    protected function resolveAdminIndexFilters(Request $request): array
    {
        $status = $request->input('status');
        if ($status === '') {
            $status = null;
        }

        $payment = $request->input('payment');
        if ($payment === '') {
            $payment = null;
        }

        $department = $request->input('department');
        if ($department === '') {
            $department = null;
        }

        $createdSort = $request->input('created_sort', 'desc');
        if (!in_array($createdSort, ['asc', 'desc'], true)) {
            $createdSort = 'desc';
        }

        $createdFrom = $this->normalizeAdminIndexDateFilter($request->input('created_from'));
        $createdTo = $this->normalizeAdminIndexDateFilter($request->input('created_to'));
        $search = trim((string) $request->input('search', ''));

        if ($createdFrom !== null && $createdTo !== null && $createdFrom > $createdTo) {
            [$createdFrom, $createdTo] = [$createdTo, $createdFrom];
        }

        return [$status, $payment, $department, $createdSort, $createdFrom, $createdTo, $search !== '' ? $search : null];
    }

    protected function buildAdminIndexQuery(
        ?string $status,
        ?string $payment,
        ?string $department,
        ?string $createdFrom,
        ?string $createdTo,
        ?string $search = null
    ): Builder
    {
        $q = Reservation::with(['user'])
            ->whereNotIn('status', ['cancelled', 'canceled']);

        if (in_array($status, ['pending', 'approved', 'declined'], true)) {
            $q->where('status', $status);
        }

        if (in_array($payment, ['paid', 'unpaid'], true)) {
            $q->whereNotIn('status', ['declined', 'cancelled']);

            if ($payment === 'paid') {
                $q->where('payment_status', 'paid');
            } else {
                $q->where(function ($paymentQuery) {
                    $paymentQuery->whereNull('payment_status')
                        ->orWhere('payment_status', '!=', 'paid');
                });
            }
        }

        if (is_string($department) && $department !== '') {
            $q->where(function ($departmentQuery) use ($department) {
                $departmentQuery->where('department', $department)
                    ->orWhere(function ($fallbackQuery) use ($department) {
                        $fallbackQuery->whereNull('department')
                            ->whereHas('user', function ($userQuery) use ($department) {
                                $userQuery->where('department', $department);
                            });
                });
            });
        }

        if ($createdFrom !== null) {
            $q->whereDate('created_at', '>=', $createdFrom);
        }

        if ($createdTo !== null) {
            $q->whereDate('created_at', '<=', $createdTo);
        }

        if ($search !== null && $search !== '') {
            $searchLike = "%{$search}%";

            $q->where(function (Builder $searchQuery) use ($search, $searchLike) {
                $searchQuery->where('contact_person', 'like', $searchLike)
                    ->orWhere('email', 'like', $searchLike)
                    ->orWhere('department', 'like', $searchLike)
                    ->orWhere('status', 'like', $searchLike)
                    ->orWhere('event_name', 'like', $searchLike)
                    ->orWhere('venue', 'like', $searchLike)
                    ->orWhere('project_name', 'like', $searchLike)
                    ->orWhere('account_code', 'like', $searchLike)
                    ->orWhere('contact_number', 'like', $searchLike)
                    ->orWhereHas('user', function (Builder $userQuery) use ($searchLike) {
                        $userQuery->where('name', 'like', $searchLike)
                            ->orWhere('email', 'like', $searchLike)
                            ->orWhere('department', 'like', $searchLike);
                    });

                if (ctype_digit($search)) {
                    $searchQuery->orWhereKey((int) $search);
                }

                $searchDate = $this->normalizeAdminIndexDateFilter($search);
                if ($searchDate !== null) {
                    $searchQuery->orWhereDate('created_at', $searchDate)
                        ->orWhereDate('event_date', $searchDate);
                }

                $matchesUnpaid = preg_match('/\bunpaid\b/i', $search) === 1;
                $matchesPaid = !$matchesUnpaid && preg_match('/\bpaid\b/i', $search) === 1;

                if ($matchesPaid) {
                    $searchQuery->orWhere('payment_status', 'paid');
                } elseif ($matchesUnpaid) {
                    $searchQuery->orWhere(function (Builder $paymentQuery) {
                        $paymentQuery->whereNull('payment_status')
                            ->orWhere('payment_status', '!=', 'paid');
                    });
                }
            });
        }

        return $q;
    }

    protected function normalizeAdminIndexDateFilter(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function getAdminDepartmentOptions(): array
    {
        return Reservation::with(['user:id,department'])
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->get(['id', 'user_id', 'department'])
            ->map(function (Reservation $reservation) {
                return $reservation->department ?? optional($reservation->user)->department;
            })
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn (string $value) => trim($value))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected function deleteReservationNotifications(int $reservationId): void
    {
        InAppNotification::query()
            ->where('module', 'reservations')
            ->where(function (Builder $query) use ($reservationId) {
                $query->where('metadata->reservation_id', $reservationId)
                    ->orWhere('metadata->reservation_id', (string) $reservationId);
            })
            ->delete();
    }
}
