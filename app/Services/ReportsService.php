<?php

namespace App\Services;

use App\Exceptions\IncompatibleRecipeUnitException;
use App\Models\Reservation;
use App\Models\MenuPrice;
use App\Models\User;
use App\Support\RecipeUnit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportsService
{
    /**
     * Generate reservation report data
     */
    public function generateReservationReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Reservation::with(['user'])
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('event_date')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'event_name' => $reservation->event_name,
                    'event_date' => $reservation->event_date->format('Y-m-d'),
                    'customer_name' => $reservation->user->name,
                    'department' => $reservation->department ?? $reservation->user->department,
                    'number_of_persons' => $reservation->number_of_persons,
                    'status' => ucfirst($reservation->status),
                    'created_at' => $reservation->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    /**
     * Generate inventory report data
     */
    public function generateInventoryReport(Carbon $startDate, Carbon $endDate): Collection
    {
        $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        $inventoryUsage = [];
        $incompatibleUnits = [];

        foreach ($reservations as $reservation) {
            foreach ($reservation->items as $reservationItem) {
                $menu = $reservationItem->menu;
                if (!$menu) {
                    continue;
                }

                foreach ($menu->items as $menuItem) {
                    foreach ($menuItem->recipes as $recipe) {
                        $inventoryItem = $recipe->inventoryItem;
                        if (!$inventoryItem) {
                            continue;
                        }

                        $totalNeededRecipe = (float) ($recipe->quantity_needed ?? 0) * (float) ($reservationItem->quantity ?? 0);
                        $recipeUnit = RecipeUnit::normalize($recipe->unit) ?? RecipeUnit::normalize($inventoryItem->unit);
                        $usedQuantity = RecipeUnit::convertToStockUnit($totalNeededRecipe, $recipeUnit, $inventoryItem->unit);

                        if ($usedQuantity === null) {
                            $incompatibleUnits[] = [
                                'context' => 'Inventory report',
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
                                'name' => $inventoryItem->name,
                                'unit' => RecipeUnit::display($inventoryItem->unit) ?: ($inventoryItem->unit ?? ''),
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

        return collect($inventoryUsage)->values();
    }

    /**
     * Generate CRM report data
     */
    public function generateCrmReport(Carbon $startDate, Carbon $endDate): Collection
    {
        $customers = User::where('role', 'customer')
            ->with(['reservations' => function ($query) use ($startDate, $endDate) {
                $query->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate->startOfDay(), $endDate->endOfDay()]);
            }])
            ->get();

        return $customers->map(function ($customer) {
            $totalReservations = $customer->reservations->count();
            $approvedReservations = $customer->reservations->where('status', 'approved')->count();
            $totalSpent = $customer->reservations->where('status', 'approved')->sum(function ($reservation) {
                return $reservation->items->sum(function ($item) {
                    $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                    return $price * $item->quantity;
                });
            });

            return [
                'name' => $customer->name,
                'email' => $customer->email,
                'total_reservations' => $totalReservations,
                'approved_reservations' => $approvedReservations,
                'total_spent' => $totalSpent,
                'last_reservation' => $customer->reservations->max('event_date')?->format('Y-m-d') ?? 'N/A',
            ];
        })->filter(function ($customer) {
            return $customer['total_reservations'] > 0;
        });
    }
}
