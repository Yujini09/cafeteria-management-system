<?php

namespace App\Exports;

use App\Models\User;
use App\Models\MenuPrice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class CrmReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate->startOfDay();
        $this->endDate = $endDate->endOfDay();
    }

    public function collection()
    {
        $customers = User::where('role', 'customer')
            ->with(['reservations' => function ($query) {
                $query->whereNotNull('event_date')
                    ->whereBetween('event_date', [$this->startDate, $this->endDate])
                    ->with(['items.menu']);
            }])
            ->get();

        $crmData = $customers->map(function ($customer) {
            $totalReservations = $customer->reservations->count();
            $approvedReservations = $customer->reservations->where('status', 'approved')->count();
            $totalSpent = $customer->reservations->where('status', 'approved')->sum(function ($reservation) {
                return $reservation->items->sum(function ($item) {
                    // Check if menu exists
                    if (!$item->menu) {
                        return 0;
                    }
                    $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                    return $price * ($item->quantity ?? 0);
                });
            });

            return [
                'name' => $customer->name ?? 'N/A',
                'email' => $customer->email ?? 'N/A',
                'total_reservations' => $totalReservations,
                'approved_reservations' => $approvedReservations,
                'total_spent' => $totalSpent,
                'last_reservation' => $customer->reservations->max('event_date')?->format('Y-m-d') ?? 'N/A',
            ];
        })->filter(function ($customer) {
            return $customer['total_reservations'] > 0;
        });

        return $crmData;
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Email',
            'Total Reservations',
            'Approved Reservations',
            'Total Spent',
            'Last Reservation',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer['name'],
            $customer['email'],
            $customer['total_reservations'],
            $customer['approved_reservations'],
            $customer['total_spent'],
            $customer['last_reservation'],
        ];
    }
}
