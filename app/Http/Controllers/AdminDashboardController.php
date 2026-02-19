<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\InventoryItem;
use App\Models\ContactMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index(): View
    {
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $menusSold = ReservationItem::count();
        $lowStocks = InventoryItem::where('qty', '>', 0)
            ->where('qty', '<=', 5)
            ->get();
        $outOfStocks = InventoryItem::where('qty', 0)->get();
        $expiringSoon = InventoryItem::where('expiry_date', '<=', Carbon::now()->addDays(7))
            ->where('expiry_date', '>=', Carbon::now())
            ->get();

        $unreadCount = 0;
        if (Schema::hasTable('contact_messages')) {
            $unreadCount = ContactMessage::where('is_read', false)->count();
        }

        return view('admin.dashboard', compact(
            'totalReservations',
            'pendingReservations',
            'menusSold',
            'lowStocks',
            'outOfStocks',
            'expiringSoon',
            'unreadCount'
        ));
    }
}
