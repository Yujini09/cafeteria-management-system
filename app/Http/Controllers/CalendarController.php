<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\View\View;

class CalendarController extends Controller
{
public function index(Request $request): View
{
    // Default: current month
    $month = $request->input('month', now()->format('Y-m'));

    $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    // ✅ Fetch ALL approved reservations (for event list)
    $allApproved = \App\Models\Reservation::with('user')
        ->where('status', 'approved')
        ->orderBy('date', 'asc')
        ->get();

    // ✅ Only reservations in the selected month for calendar view
    $monthlyApproved = $allApproved->filter(function ($res) use ($startDate, $endDate) {
        return \Carbon\Carbon::parse($res->event_date)->between($startDate, $endDate);
    });

    return view('admin.calendar', [
        'allApproved' => $allApproved,      // for sidebar list
        'monthlyApproved' => $monthlyApproved, // for calendar grid
        'month' => $month,
    ]);
}

}
