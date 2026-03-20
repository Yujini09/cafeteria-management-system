<?php

namespace App\Exports;

use App\Exceptions\IncompatibleRecipeUnitException;
use App\Models\Reservation;
use App\Support\RecipeUnit;
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
        $incompatibleUnits = [];

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

                        $totalNeededRecipe = (float) ($recipe->quantity_needed ?? 0) * (float) ($reservationItem->quantity ?? 0);
                        $recipeUnit = RecipeUnit::normalize($recipe->unit) ?? RecipeUnit::normalize($inventoryItem->unit);
                        $usedQuantity = RecipeUnit::convertToStockUnit($totalNeededRecipe, $recipeUnit, $inventoryItem->unit);

                        if ($usedQuantity === null) {
                            $incompatibleUnits[] = [
                                'context' => 'Inventory export',
                                'menu_item' => $menuItem->name ?? ('Menu item #' . ($menuItem->id ?? '?')),
                                'ingredient' => $inventoryItem->name ?? ('Inventory item #' . ($inventoryItem->id ?? '?')),
                                'recipe_unit' => RecipeUnit::display($recipe->unit) ?: ((string) ($recipe->unit ?? 'unknown')),
                                'stock_unit' => RecipeUnit::display($inventoryItem->unit) ?: ((string) ($inventoryItem->unit ?? 'unknown')),
                            ];
                            continue;
                        }

                        if ($usedQuantity <= 0) {
                            continue;
                        }

                        if (!isset($inventoryUsage[$inventoryItem->id])) {
                            $inventoryUsage[$inventoryItem->id] = [
                                'name' => $inventoryItem->name ?? 'N/A',
                                'unit' => RecipeUnit::display($inventoryItem->unit) ?: ($inventoryItem->unit ?? 'N/A'),
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

        if (!empty($incompatibleUnits)) {
            throw new IncompatibleRecipeUnitException($incompatibleUnits);
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
