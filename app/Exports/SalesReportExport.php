<?php

namespace App\Exports;

use App\Models\Reservation;
use App\Models\MenuPrice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $reservations = Reservation::with(['items.menu', 'user'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$this->startDate, $this->endDate])
            ->get();

        // Flatten sales data for Excel export - one row per item
        $salesData = collect();

        foreach ($reservations as $reservation) {
            $reservationTotal = 0;
            $items = [];

            foreach ($reservation->items as $item) {
                // Check if menu exists
                if (!$item->menu) {
                    continue;
                }

                $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                $itemTotal = $price * ($item->quantity ?? 0);
                $reservationTotal += $itemTotal;

                $items[] = [
                    'reservation_id' => $reservation->id,
                    'event_name' => $reservation->event_name ?? 'N/A',
                    'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                    'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                    'number_of_persons' => $reservation->number_of_persons ?? 0,
                    'menu_name' => $item->menu->name ?? 'N/A',
                    'type' => ucfirst($item->menu->type ?? 'standard'),
                    'meal_time' => ucfirst(str_replace('_', ' ', $item->menu->meal_time ?? 'lunch')),
                    'quantity' => $item->quantity ?? 0,
                    'unit_price' => $price,
                    'item_total' => $itemTotal,
                    'reservation_total' => $reservationTotal,
                ];
            }

            // Add all items to the collection
            foreach ($items as $item) {
                $salesData->push($item);
            }

            // If no items, add a row with reservation info and 0 total
            if (empty($items)) {
                $salesData->push([
                    'reservation_id' => $reservation->id,
                    'event_name' => $reservation->event_name ?? 'N/A',
                    'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                    'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                    'number_of_persons' => $reservation->number_of_persons ?? 0,
                    'menu_name' => 'N/A',
                    'type' => 'N/A',
                    'meal_time' => 'N/A',
                    'quantity' => 0,
                    'unit_price' => 0,
                    'item_total' => 0,
                    'reservation_total' => 0,
                ]);
            }
        }

        return $salesData;
    }

    public function headings(): array
    {
        return [
            'Reservation ID',
            'Event Name',
            'Event Date',
            'Customer Name',
            'Number of Persons',
            'Menu Item',
            'Type',
            'Meal Time',
            'Quantity',
            'Unit Price',
            'Item Total',
            'Reservation Total',
        ];
    }
}
