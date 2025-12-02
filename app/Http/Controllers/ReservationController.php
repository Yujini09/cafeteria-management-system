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
use App\Models\Notification as NotificationModel;

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
        $guests = $reservation->guests ?? $reservation->attendees ?? $reservation->number_of_persons ?? 1;

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
            $guests = $reservation->guests ?? $reservation->attendees ?? $reservation->number_of_persons ?? 1;

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
            'updated_by' => Auth::user()->name,
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
        $guests = $reservation->guests ?? $reservation->attendees ?? $reservation->number_of_persons ?? 1;

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
            'updated_by' => Auth::user()->name,
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

        // Email target
        if ($reservation->relationLoaded('user') ? $reservation->user : $reservation->user()->exists()) {
            optional($reservation->user)->notify($notification);
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
        NotificationModel::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    public function create(Request $request)
    {
        // Capture date range and time selection from GET parameters
        $reservationData = [
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
            'day_times' => $request->query('day_times'),
            // Capture personal info if needed
            'name' => $request->query('name'),
            'department' => $request->query('department'),
            'address' => $request->query('address'),
            'email' => $request->query('email'),
            'phone' => $request->query('phone'),
            'activity' => $request->query('activity'),
            'venue' => $request->query('venue'),
            'project_name' => $request->query('project_name'),
            'account_code' => $request->query('account_code'),
        ];
        
        // Store in session for use in the form
        session(['reservation_data' => $reservationData]);
        
        // Fetch all menus grouped by meal_time and type, eager load 'items' relationship
        $menus = \App\Models\Menu::with('items')->get()->groupBy(['meal_time', 'type']);
        
        // Get default prices
        $menuPrices = [];
        $defaultStandardPrice = 150;
        $defaultSpecialPrice = 200;
        
        // Organize prices by meal time and type
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
        
        // Pass the menus and reservation data to the view
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
            'reservations.*.*.menu' => 'required|integer',
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
            'generated_by' => Auth::user()->name,
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
            'updated_by' => Auth::user()->name,
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
}