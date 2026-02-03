<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\InventoryItem;
use App\Models\ContactMessage; // [1] Add this import
use Carbon\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']); // This will now allow superadmin too due to middleware change
    }

    public function index(): View
    {
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $menusSold = ReservationItem::sum('quantity');
        $lowStocks = InventoryItem::where('qty', '<', 5)->get();
        $outOfStocks = InventoryItem::where('qty', 0)->get();
        $expiringSoon = InventoryItem::where('expiry_date', '<=', Carbon::now()->addDays(7))
            ->where('expiry_date', '>=', Carbon::now())
            ->get();

        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.dashboard', compact(
            'totalReservations',
            'pendingReservations',
            'menusSold',
            'lowStocks',
            'outOfStocks',
            'expiringSoon',
            'unreadCount' // [3] Pass the variable to the view
        ));
    }
}
