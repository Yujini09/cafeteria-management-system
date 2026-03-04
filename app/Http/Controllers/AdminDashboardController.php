<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Services\InventoryAlertService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(InventoryAlertService $inventoryAlertService): View
    {
        $totalReservations = Reservation::count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $menusSold = ReservationItem::count();
        $lowStocks = $inventoryAlertService->getLowStocks();
        $outOfStocks = $inventoryAlertService->getOutOfStocks();
        $expiringSoon = $inventoryAlertService->getExpiringSoon();
        $unreadCount = ContactMessage::unreadCount();

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
