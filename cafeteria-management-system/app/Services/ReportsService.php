<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\MenuPrice;
use App\Models\User;
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
                    'department' => $reservation->user->department,
                    'number_of_persons' => $reservation->number_of_persons,
                    'status' => ucfirst($reservation->status),
                    'created_at' => $reservation->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    /**
     * Generate sales report data
     */
    public function generateSalesReport(Carbon $startDate, Carbon $endDate): array
    {
        $reservations = Reservation::with(['items.menu', 'user'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        $salesData = [];
        $totalRevenue = 0;
        $totalReservations = $reservations->count();

        foreach ($reservations as $reservation) {
            $reservationTotal = 0;
            $items = [];

            foreach ($reservation->items as $item) {
                $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                $itemTotal = $price * $item->quantity;
                $reservationTotal += $itemTotal;

                $items[] = [
                    'menu_name' => $item->menu->name,
                    'type' => ucfirst($item->menu->type),
                    'meal_time' => ucfirst(str_replace('_', ' ', $item->menu->meal_time)),
                    'quantity' => $item->quantity,
                    'unit_price' => $price,
                    'total' => $itemTotal,
                ];
            }

            $salesData[] = [
                'reservation_id' => $reservation->id,
                'event_name' => $reservation->event_name,
                'event_date' => $reservation->event_date->format('Y-m-d'),
                'customer_name' => $reservation->user->name,
                'number_of_persons' => $reservation->number_of_persons,
                'items' => $items,
                'reservation_total' => $reservationTotal,
            ];

            $totalRevenue += $reservationTotal;
        }

        return [
            'salesData' => collect($salesData),
            'totalRevenue' => $totalRevenue,
            'totalReservations' => $totalReservations,
        ];
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

        foreach ($reservations as $reservation) {
            foreach ($reservation->items as $reservationItem) {
                $menu = $reservationItem->menu;
                foreach ($menu->items as $menuItem) {
                    foreach ($menuItem->recipes as $recipe) {
                        $inventoryItem = $recipe->inventoryItem;
                        $usedQuantity = $recipe->quantity_needed * $reservationItem->quantity;

                        if (!isset($inventoryUsage[$inventoryItem->id])) {
                            $inventoryUsage[$inventoryItem->id] = [
                                'name' => $inventoryItem->name,
                                'unit' => $inventoryItem->unit,
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
