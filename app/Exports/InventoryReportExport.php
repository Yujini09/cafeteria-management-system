<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
        $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$this->startDate, $this->endDate])
            ->get();

        $inventoryUsage = [];

        foreach ($reservations as $reservation) {
            foreach ($reservation->items as $reservationItem) {
                $menu = $reservationItem->menu;
                
                // Check if menu exists
                if (!$menu) {
                    continue;
                }

                foreach ($menu->items as $menuItem) {
                    foreach ($menuItem->recipes as $recipe) {
                        $inventoryItem = $recipe->inventoryItem;
                        
                        // Check if inventory item exists
                        if (!$inventoryItem) {
                            continue;
                        }

                        $usedQuantity = ($recipe->quantity_needed ?? 0) * ($reservationItem->quantity ?? 0);

                        if (!isset($inventoryUsage[$inventoryItem->id])) {
                            $inventoryUsage[$inventoryItem->id] = [
                                'name' => $inventoryItem->name ?? 'N/A',
                                'unit' => $inventoryItem->unit ?? 'N/A',
                                'total_used' => 0,
                                'reservations_count' => 0,
                            ];
                        }

                        $inventoryUsage[$inventoryItem->id]['total_used'] += $usedQuantity;
                        $inventoryUsage[$inventoryItem->id]['reservations_count']++;
                    }
                }
            }
        }

        return collect($inventoryUsage);
    }

    public function headings(): array
    {
        return [
            'Inventory Item',
            'Unit',
            'Total Used',
            'Reservations Count',
        ];
    }

    public function map($inventoryItem): array
    {
        return [
            $inventoryItem['name'],
            $inventoryItem['unit'],
            $inventoryItem['total_used'],
            $inventoryItem['reservations_count'],
        ];
    }
}
