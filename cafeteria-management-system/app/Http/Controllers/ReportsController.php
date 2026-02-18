<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\MenuPrice;
use App\Models\User;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\ReservationReportExport;
use App\Exports\CrmReportExport;
use Carbon\Carbon;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:reservation,sales,inventory,crm',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $request->report_type;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Log report generation
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Generated Report',
            'module'      => 'reports',
            'description' => 'generated a report',
        ]);

        // Create notification for admins/superadmin about report generation
        $this->createAdminNotification('report_generated', 'reports', ucfirst($reportType) . ' report has been generated', [
            'report_type' => $reportType,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'generated_by' => Auth::user()->name,
        ]);

        switch ($reportType) {
            case 'reservation':
                return $this->generateReservationReport($startDate, $endDate);
            case 'sales':
                return $this->generateSalesReport($startDate, $endDate);
            case 'inventory':
                return $this->generateInventoryReport($startDate, $endDate);
            case 'crm':
                return $this->generateCrmReport($startDate, $endDate);
            default:
                abort(400, 'Invalid report type');
        }
    }

    private function generateReservationReport($startDate, $endDate)
    {
        $reservations = Reservation::with(['user'])
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate, $endDate])
            ->orderBy('event_date')
            ->get();

        $reservationData = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'event_name' => $reservation->event_name ?? 'N/A',
                'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                'department' => $reservation->user ? $reservation->user->department : 'N/A',
                'number_of_persons' => $reservation->number_of_persons ?? 0,
                'status' => ucfirst($reservation->status ?? 'pending'),
                'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
            ];
        });

        return view('admin.reports.show', compact(
            'reservationData',
            'startDate',
            'endDate'
        ))->with('reportType', 'reservation');
    }

    private function generateSalesReport($startDate, $endDate)
    {
        // Get approved reservations within date range
        $reservations = Reservation::with(['items.menu', 'user'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        // Calculate sales data
        $salesData = [];
        $totalRevenue = 0;
        $totalReservations = $reservations->count();

        foreach ($reservations as $reservation) {
            $reservationTotal = 0;
            $items = [];

            foreach ($reservation->items as $item) {
                // Check if menu exists
                if (!$item->menu) {
                    continue;
                }

                $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                $itemTotal = $price * $item->quantity;
                $reservationTotal += $itemTotal;

                $items[] = [
                    'menu_name' => $item->menu->name ?? 'N/A',
                    'type' => ucfirst($item->menu->type ?? 'standard'),
                    'meal_time' => ucfirst(str_replace('_', ' ', $item->menu->meal_time ?? 'lunch')),
                    'quantity' => $item->quantity ?? 0,
                    'unit_price' => $price,
                    'total' => $itemTotal,
                ];
            }

            $salesData[] = [
                'reservation_id' => $reservation->id,
                'event_name' => $reservation->event_name ?? 'N/A',
                'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                'number_of_persons' => $reservation->number_of_persons ?? 0,
                'items' => $items,
                'reservation_total' => $reservationTotal,
            ];

            $totalRevenue += $reservationTotal;
        }

        $salesData = collect($salesData);

        return view('admin.reports.show', compact(
            'salesData',
            'totalRevenue',
            'totalReservations',
            'startDate',
            'endDate'
        ))->with('reportType', 'sales');
    }

    private function generateInventoryReport($startDate, $endDate)
    {
        // Get approved reservations within date range
        $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate, $endDate])
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

        $inventoryData = collect($inventoryUsage)->values();

        return view('admin.reports.show', compact(
            'inventoryData',
            'startDate',
            'endDate'
        ))->with('reportType', 'inventory');
    }

    private function generateCrmReport($startDate, $endDate)
    {
        $customers = \App\Models\User::where('role', 'customer')
            ->with(['reservations' => function ($query) use ($startDate, $endDate) {
                $query->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
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

        return view('admin.reports.show', compact(
            'crmData',
            'startDate',
            'endDate'
        ))->with('reportType', 'crm');
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:reservation,sales,inventory,crm',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $request->report_type;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $viewData = compact('startDate', 'endDate');
        $filename = $reportType . '_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';

        switch ($reportType) {
            case 'reservation':
                $reservations = Reservation::with(['user'])
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
                    ->orderBy('event_date')
                    ->get();

                $reservationData = $reservations->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'event_name' => $reservation->event_name ?? 'N/A',
                        'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                        'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                        'department' => $reservation->user ? $reservation->user->department : 'N/A',
                        'number_of_persons' => $reservation->number_of_persons ?? 0,
                        'status' => ucfirst($reservation->status ?? 'pending'),
                        'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
                    ];
                });

                $viewData['reservationData'] = $reservationData;
                $viewData['reportType'] = 'reservation';
                break;

            case 'sales':
                // Get approved reservations within date range
                $reservations = Reservation::with(['items.menu', 'user'])
                    ->where('status', 'approved')
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
                    ->get();

                // Calculate sales data
                $salesData = [];
                $totalRevenue = 0;
                $totalReservations = $reservations->count();

                foreach ($reservations as $reservation) {
                    $reservationTotal = 0;
                    $items = [];

                    foreach ($reservation->items as $item) {
                        // Check if menu exists
                        if (!$item->menu) {
                            continue;
                        }

                        $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                        $itemTotal = $price * $item->quantity;
                        $reservationTotal += $itemTotal;

                        $items[] = [
                            'menu_name' => $item->menu->name ?? 'N/A',
                            'type' => ucfirst($item->menu->type ?? 'standard'),
                            'meal_time' => ucfirst(str_replace('_', ' ', $item->menu->meal_time ?? 'lunch')),
                            'quantity' => $item->quantity ?? 0,
                            'unit_price' => $price,
                            'total' => $itemTotal,
                        ];
                    }

                    $salesData[] = [
                        'reservation_id' => $reservation->id,
                        'event_name' => $reservation->event_name ?? 'N/A',
                        'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                        'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                        'number_of_persons' => $reservation->number_of_persons ?? 0,
                        'items' => $items,
                        'reservation_total' => $reservationTotal,
                    ];

                    $totalRevenue += $reservationTotal;
                }

                $salesData = collect($salesData);

                $viewData['salesData'] = $salesData;
                $viewData['totalRevenue'] = $totalRevenue;
                $viewData['totalReservations'] = $totalReservations;
                $viewData['reportType'] = 'sales';
                break;

            case 'inventory':
                // Get approved reservations within date range
                $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
                    ->where('status', 'approved')
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
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

                $inventoryData = collect($inventoryUsage)->values();

                $viewData['inventoryData'] = $inventoryData;
                $viewData['reportType'] = 'inventory';
                break;

            case 'crm':
                $customers = \App\Models\User::where('role', 'customer')
                    ->with(['reservations' => function ($query) use ($startDate, $endDate) {
                        $query->whereNotNull('event_date')
                            ->whereBetween('event_date', [$startDate, $endDate])
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

                $viewData['crmData'] = $crmData;
                $viewData['reportType'] = 'crm';
                break;
        }

        // Log PDF export
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Exported Report PDF',
            'module'      => 'reports',
            'description' => 'exported a report PDF',
        ]);

        $pdf = Pdf::loadView('admin.reports.pdf', $viewData);

        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:reservation,sales,inventory,crm',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $request->report_type;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $filename = $reportType . '_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx';

        // Log Excel export
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Exported Report Excel',
            'module'      => 'reports',
            'description' => 'exported a report Excel',
        ]);

        switch ($reportType) {
            case 'reservation':
                return Excel::download(new \App\Exports\ReservationReportExport($startDate, $endDate), $filename);
            case 'sales':
                return Excel::download(new SalesReportExport($startDate, $endDate), $filename);
            case 'inventory':
                return Excel::download(new \App\Exports\InventoryReportExport($startDate, $endDate), $filename);
            case 'crm':
                return Excel::download(new \App\Exports\CrmReportExport($startDate, $endDate), $filename);
            default:
                abort(400, 'Invalid report type');
        }
    }

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        // Get all admin and superadmin users
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        
        // Create a notification for each admin/superadmin
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        }
    }
}
