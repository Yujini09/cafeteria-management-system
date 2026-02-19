<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        // Default: current month
        $month = $request->input('month', now()->format('Y-m'));

        $startDate = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Fetch all approved reservations (with user details)
        $allApproved = \App\Models\Reservation::with('user')
            ->where('status', 'approved')
            ->orderBy('event_date', 'asc')
            ->get();

        // Show reservations that overlap the selected month (supports multi-day events)
        $monthlyApproved = $allApproved->filter(function ($res) use ($startDate, $endDate) {
            $rawStartDate = $res->event_date ?? $res->date;
            if (empty($rawStartDate)) {
                return false;
            }

            $eventStart = \Carbon\Carbon::parse($rawStartDate)->startOfDay();
            $eventEnd = $res->end_date
                ? \Carbon\Carbon::parse($res->end_date)->startOfDay()
                : $eventStart->copy();

            if ($eventEnd->lt($eventStart)) {
                $eventEnd = $eventStart->copy();
            }

            // overlap check: start <= monthEnd && end >= monthStart
            return $eventStart->lte($endDate) && $eventEnd->gte($startDate);
        });

        return view('admin.calendar', [
            'allApproved' => $allApproved,
            'monthlyApproved' => $monthlyApproved,
            'month' => $month,
        ]);
    }
}
