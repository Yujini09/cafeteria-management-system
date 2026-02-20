<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\MenuPrice;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\ReservationReportExport;
use App\Exports\CrmReportExport;
use Carbon\Carbon;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function generate(Request $request)
    {
        if ($request->isMethod('get') && !$request->hasAny(['report_type', 'start_date', 'end_date'])) {
            return redirect()->route('admin.reports.index');
        }

        $validated = $request->validate([
            'report_type' => 'required|in:reservation,sales,inventory,crm',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $validated['report_type'];
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        if ($request->isMethod('post')) {
            // Log once when report is generated from the form.
            AuditTrail::record(
                Auth::id(),
                AuditDictionary::GENERATED_REPORT,
                AuditDictionary::MODULE_REPORTS,
                "generated {$reportType} report"
            );

            // Create notification once from initial generation.
            $this->createAdminNotification('report_generated', 'reports', ucfirst($reportType) . ' report has been generated', [
                'report_type' => $reportType,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'generated_by' => Auth::user()?->name ?? 'Unknown',
            ]);

            return redirect()->route('admin.reports.generate', [
                'report_type' => $reportType,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }

        switch ($reportType) {
            case 'reservation':
                return $this->generateReservationReport($startDate, $endDate, $request);
            case 'sales':
                return $this->generateSalesReport($startDate, $endDate, $request);
            case 'inventory':
                return $this->generateInventoryReport($startDate, $endDate, $request);
            case 'crm':
                return $this->generateCrmReport($startDate, $endDate, $request);
            default:
                abort(400, 'Invalid report type');
        }
    }

    private function generateReservationReport($startDate, $endDate, Request $request)
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
                // Prefer reservation's own department field when available
                'department' => $reservation->department ?? ($reservation->user ? $reservation->user->department : 'N/A'),
                'number_of_persons' => $reservation->number_of_persons ?? 0,
                'status' => ucfirst($reservation->status ?? 'pending'),
                'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
            ];
        });

        $currentTotalReservations = (float) $reservations->count();
        $currentApprovedCount = (float) $reservations->where('status', 'approved')->count();
        $currentAveragePartySize = (float) ($reservations->avg('number_of_persons') ?? 0);
        $currentPendingCount = (float) $reservations->where('status', 'pending')->count();

        $reservationsByDay = $reservationData
            ->groupBy('event_date')
            ->map(fn ($rows) => $rows->count())
            ->sortKeys();

        $statusBreakdown = $reservationData
            ->groupBy(fn ($row) => strtolower($row['status']))
            ->map(fn ($rows) => $rows->count())
            ->sortDesc();

        $departmentBreakdown = $reservationData
            ->groupBy(fn ($row) => $row['department'] ?: 'Unspecified')
            ->map(fn ($rows) => $rows->count())
            ->sortDesc()
            ->take(7);

        $busiestDayLabel = 'N/A';
        if ($reservationsByDay->isNotEmpty()) {
            $maxReservationsInDay = $reservationsByDay->max();
            $busiestDayKey = $reservationsByDay->search($maxReservationsInDay);
            if ($busiestDayKey !== false) {
                $busiestDayLabel = $busiestDayKey . ' (' . $maxReservationsInDay . ')';
            }
        }

        $topDepartmentLabel = $departmentBreakdown->isNotEmpty()
            ? ($departmentBreakdown->keys()->first() . ' (' . $departmentBreakdown->first() . ')')
            : 'N/A';

        $kpiCards = [
            $this->buildKpiCard(
                'Total Reservations',
                number_format($currentTotalReservations, 0),
                'fas fa-calendar-check',
                'kpi-primary',
                null
            ),
            $this->buildKpiCard(
                'Approved Reservations',
                number_format($currentApprovedCount, 0),
                'fas fa-check-circle',
                'kpi-success',
                null
            ),
            $this->buildKpiCard(
                'Avg Party Size',
                number_format($currentAveragePartySize, 1),
                'fas fa-users',
                'kpi-warning',
                null
            ),
            $this->buildKpiCard(
                'Pending Reservations',
                number_format($currentPendingCount, 0),
                'fas fa-clock',
                'kpi-neutral',
                null
            ),
        ];

        $charts = [
            'trend' => [
                'type' => 'line',
                'label' => 'Reservations per Day',
                'labels' => $reservationsByDay->keys()->values()->all(),
                'values' => $reservationsByDay->values()->all(),
            ],
            'breakdown' => [
                'type' => 'doughnut',
                'label' => 'Status Distribution',
                'labels' => $statusBreakdown->keys()->map(fn ($key) => ucfirst($key))->values()->all(),
                'values' => $statusBreakdown->values()->all(),
            ],
            'topContributors' => [
                'type' => 'bar',
                'label' => 'Top Departments by Reservations',
                'labels' => $departmentBreakdown->keys()->values()->all(),
                'values' => $departmentBreakdown->values()->all(),
            ],
        ];

        $insights = [
            'Busiest day: ' . $busiestDayLabel,
            'Top department: ' . $topDepartmentLabel,
            'Total reservations: ' . number_format($currentTotalReservations, 0),
        ];

        $reservationData = $this->paginateCollection($reservationData, $request);

        return view('admin.reports.show', compact(
            'reservationData',
            'startDate',
            'endDate',
            'kpiCards',
            'charts',
            'insights'
        ))->with('reportType', 'reservation');
    }

    private function generateSalesReport($startDate, $endDate, Request $request)
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

        $currentAverageOrderValue = $totalReservations > 0 ? $totalRevenue / $totalReservations : 0;
        $currentAverageGuests = (float) ($reservations->avg('number_of_persons') ?? 0);

        $dailyRevenue = [];
        $mealTimeRevenue = [];
        $menuRevenue = [];

        foreach ($salesData as $reservation) {
            $eventDate = $reservation['event_date'] ?? 'N/A';
            $dailyRevenue[$eventDate] = ($dailyRevenue[$eventDate] ?? 0) + (float) ($reservation['reservation_total'] ?? 0);

            foreach (($reservation['items'] ?? []) as $item) {
                $mealKey = $item['meal_time'] ?? 'Unknown';
                $mealTimeRevenue[$mealKey] = ($mealTimeRevenue[$mealKey] ?? 0) + (float) ($item['total'] ?? 0);

                $menuKey = $item['menu_name'] ?? 'Unknown Item';
                $menuRevenue[$menuKey] = ($menuRevenue[$menuKey] ?? 0) + (float) ($item['total'] ?? 0);
            }
        }

        ksort($dailyRevenue);
        arsort($mealTimeRevenue);
        arsort($menuRevenue);
        $topMenuRevenue = collect($menuRevenue)->take(7);

        $highestRevenueDay = 'N/A';
        if (!empty($dailyRevenue)) {
            $maxRevenueInDay = max($dailyRevenue);
            $maxRevenueDay = array_search($maxRevenueInDay, $dailyRevenue, true);
            if ($maxRevenueDay !== false) {
                $highestRevenueDay = $maxRevenueDay . ' (' . $this->formatCurrencyWithPesoSign($maxRevenueInDay) . ')';
            }
        }

        $topMenuLabel = $topMenuRevenue->isNotEmpty()
            ? ($topMenuRevenue->keys()->first() . ' (' . $this->formatCurrencyWithPesoSign((float) $topMenuRevenue->first()) . ')')
            : 'N/A';

        $kpiCards = [
            $this->buildKpiCard(
                'Total Revenue',
                $this->formatCurrencyWithPesoSign((float) $totalRevenue),
                'fas fa-money-bill-wave',
                'kpi-success',
                null
            ),
            $this->buildKpiCard(
                'Approved Reservations',
                number_format($totalReservations, 0),
                'fas fa-calendar-check',
                'kpi-primary',
                null
            ),
            $this->buildKpiCard(
                'Avg Order Value',
                $this->formatCurrencyWithPesoSign((float) $currentAverageOrderValue),
                'fas fa-receipt',
                'kpi-warning',
                null
            ),
            $this->buildKpiCard(
                'Avg Guests / Reservation',
                number_format($currentAverageGuests, 1),
                'fas fa-users',
                'kpi-neutral',
                null
            ),
        ];

        $charts = [
            'trend' => [
                'type' => 'line',
                'label' => 'Revenue per Day',
                'labels' => array_values(array_keys($dailyRevenue)),
                'values' => array_values($dailyRevenue),
            ],
            'breakdown' => [
                'type' => 'doughnut',
                'label' => 'Revenue by Meal Time',
                'labels' => array_values(array_keys($mealTimeRevenue)),
                'values' => array_values($mealTimeRevenue),
            ],
            'topContributors' => [
                'type' => 'bar',
                'label' => 'Top Menu Items',
                'labels' => $topMenuRevenue->keys()->values()->all(),
                'values' => $topMenuRevenue->values()->all(),
            ],
        ];

        $insights = [
            'Highest revenue day: ' . $highestRevenueDay,
            'Top menu item: ' . $topMenuLabel,
            'Average order value: ' . $this->formatCurrencyWithPesoSign((float) $currentAverageOrderValue),
        ];

        $salesData = $this->paginateCollection($salesData, $request);

        return view('admin.reports.show', compact(
            'salesData',
            'totalRevenue',
            'totalReservations',
            'startDate',
            'endDate',
            'kpiCards',
            'charts',
            'insights'
        ))->with('reportType', 'sales');
    }

    private function generateInventoryReport($startDate, $endDate, Request $request)
    {
        // Get approved reservations within date range
        $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get();

        $inventoryMetrics = $this->calculateInventoryMetrics($reservations);
        $inventoryData = $inventoryMetrics['inventoryData'];

        $topItemLabel = $inventoryData->isNotEmpty()
            ? ($inventoryData->first()['name'] . ' (' . number_format((float) $inventoryData->first()['total_used'], 2) . ')')
            : 'N/A';

        $kpiCards = [
            $this->buildKpiCard(
                'Total Quantity Used',
                number_format((float) $inventoryMetrics['totalUsed'], 2),
                'fas fa-boxes',
                'kpi-primary',
                null
            ),
            $this->buildKpiCard(
                'Total Items Used',
                number_format((float) $inventoryMetrics['totalItemsUsed'], 0),
                'fas fa-tags',
                'kpi-success',
                null
            ),
            $this->buildKpiCard(
                'Reservations Count',
                number_format((float) $inventoryMetrics['reservationsCount'], 0),
                'fas fa-calendar-check',
                'kpi-warning',
                null
            ),
            $this->buildKpiCard(
                'Avg Used / Reservation',
                number_format((float) $inventoryMetrics['averageUsedPerReservation'], 2),
                'fas fa-balance-scale',
                'kpi-neutral',
                null
            ),
        ];

        $charts = [
            'trend' => [
                'type' => 'line',
                'label' => 'Inventory Usage per Day',
                'labels' => $inventoryMetrics['dailyUsage']->keys()->values()->all(),
                'values' => $inventoryMetrics['dailyUsage']->values()->all(),
            ],
            'breakdown' => [
                'type' => 'doughnut',
                'label' => 'Usage by Unit',
                'labels' => $inventoryMetrics['unitUsage']->keys()->values()->all(),
                'values' => $inventoryMetrics['unitUsage']->values()->all(),
            ],
            'topContributors' => [
                'type' => 'bar',
                'label' => 'Top Inventory Items by Usage',
                'labels' => $inventoryMetrics['topItems']->keys()->values()->all(),
                'values' => $inventoryMetrics['topItems']->values()->all(),
            ],
        ];

        $insights = [
            'Most consumed item: ' . $topItemLabel,
            'Total quantity used: ' . number_format((float) $inventoryMetrics['totalUsed'], 2),
            'Average usage per reservation: ' . number_format((float) $inventoryMetrics['averageUsedPerReservation'], 2),
        ];

        $inventoryData = $this->paginateCollection($inventoryData, $request);

        return view('admin.reports.show', compact(
            'inventoryData',
            'startDate',
            'endDate',
            'kpiCards',
            'charts',
            'insights'
        ))->with('reportType', 'inventory');
    }

    private function generateCrmReport($startDate, $endDate, Request $request)
    {
        $customers = \App\Models\User::where('role', 'customer')
            ->with(['reservations' => function ($query) use ($startDate, $endDate) {
                $query->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
                    ->with(['items.menu']);
            }])
            ->get();

        $crmMetrics = $this->calculateCrmMetrics($customers);
        $crmData = $crmMetrics['crmData'];

        $topCustomerLabel = $crmMetrics['topCustomers']->isNotEmpty()
            ? ($crmMetrics['topCustomers']->keys()->first() . ' (' . $this->formatCurrencyWithPesoSign((float) $crmMetrics['topCustomers']->first()) . ')')
            : 'N/A';

        $kpiCards = [
            $this->buildKpiCard(
                'Active Customers',
                number_format((float) $crmMetrics['activeCustomers'], 0),
                'fas fa-user-friends',
                'kpi-primary',
                null
            ),
            $this->buildKpiCard(
                'Repeat Customer Rate',
                $this->formatPercent($crmMetrics['repeatRate']),
                'fas fa-redo',
                'kpi-success',
                null
            ),
            $this->buildKpiCard(
                'Total Customer Spend',
                $this->formatCurrencyWithPesoSign($crmMetrics['totalSpend']),
                'fas fa-wallet',
                'kpi-warning',
                null
            ),
            $this->buildKpiCard(
                'Avg Spend / Customer',
                $this->formatCurrencyWithPesoSign($crmMetrics['averageSpend']),
                'fas fa-chart-line',
                'kpi-neutral',
                null
            ),
        ];

        $charts = [
            'trend' => [
                'type' => 'line',
                'label' => 'Customer Activity per Day',
                'labels' => $crmMetrics['dailyCustomerActivity']->keys()->values()->all(),
                'values' => $crmMetrics['dailyCustomerActivity']->values()->all(),
            ],
            'breakdown' => [
                'type' => 'doughnut',
                'label' => 'One-time vs Repeat Customers',
                'labels' => ['One-time', 'Repeat'],
                'values' => [$crmMetrics['oneTimeCustomers'], $crmMetrics['repeatCustomers']],
            ],
            'topContributors' => [
                'type' => 'bar',
                'label' => 'Top Customers by Spend',
                'labels' => $crmMetrics['topCustomers']->keys()->values()->all(),
                'values' => $crmMetrics['topCustomers']->values()->all(),
            ],
        ];

        $insights = [
            'Top customer by spend: ' . $topCustomerLabel,
            'Repeat customers: ' . number_format((float) $crmMetrics['repeatCustomers'], 0),
            'Average spend per customer: ' . $this->formatCurrencyWithPesoSign($crmMetrics['averageSpend']),
        ];

        $crmData = $this->paginateCollection($crmData, $request);

        return view('admin.reports.show', compact(
            'crmData',
            'startDate',
            'endDate',
            'kpiCards',
            'charts',
            'insights'
        ))->with('reportType', 'crm');
    }

    private function calculateInventoryMetrics(Collection $reservations): array
    {
        $inventoryUsage = [];
        $dailyUsage = [];
        $unitUsage = [];
        $totalItemsUsed = 0;

        foreach ($reservations as $reservation) {
            $eventDateKey = $reservation->event_date ? $reservation->event_date->format('Y-m-d') : null;

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

                        $usedQuantity = (float) (($recipe->quantity_needed ?? 0) * ($reservationItem->quantity ?? 0));
                        if ($usedQuantity <= 0) {
                            continue;
                        }
                        $totalItemsUsed++;

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

                        if ($eventDateKey) {
                            $dailyUsage[$eventDateKey] = ($dailyUsage[$eventDateKey] ?? 0) + $usedQuantity;
                        }

                        $unitKey = $inventoryItem->unit ?? 'Unknown';
                        $unitUsage[$unitKey] = ($unitUsage[$unitKey] ?? 0) + $usedQuantity;
                    }
                }
            }
        }

        $inventoryData = collect($inventoryUsage)
            ->sortByDesc('total_used')
            ->values();

        ksort($dailyUsage);
        arsort($unitUsage);

        $totalUsed = (float) $inventoryData->sum('total_used');
        $reservationsCount = (int) $reservations->count();
        $averageUsedPerReservation = $reservationsCount > 0 ? $totalUsed / $reservationsCount : 0.0;

        $topItems = $inventoryData
            ->take(7)
            ->mapWithKeys(fn ($item) => [$item['name'] => $item['total_used']]);

        return [
            'inventoryData' => $inventoryData,
            'dailyUsage' => collect($dailyUsage),
            'unitUsage' => collect($unitUsage)->take(7),
            'topItems' => $topItems,
            'totalUsed' => $totalUsed,
            'totalItemsUsed' => $totalItemsUsed,
            'uniqueItems' => $inventoryData->count(),
            'reservationsCount' => $reservationsCount,
            'averageUsedPerReservation' => $averageUsedPerReservation,
        ];
    }

    private function calculateCrmMetrics(Collection $customers): array
    {
        $dailyCustomerActivity = [];

        $crmData = $customers->map(function ($customer) use (&$dailyCustomerActivity) {
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

            foreach ($customer->reservations as $reservation) {
                $eventDate = $reservation->event_date?->format('Y-m-d');
                if ($eventDate) {
                    $dailyCustomerActivity[$eventDate] = ($dailyCustomerActivity[$eventDate] ?? 0) + 1;
                }
            }

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
        })->values();

        ksort($dailyCustomerActivity);

        $activeCustomers = $crmData->count();
        $repeatCustomers = $crmData->filter(fn ($row) => ($row['total_reservations'] ?? 0) >= 2)->count();
        $oneTimeCustomers = max($activeCustomers - $repeatCustomers, 0);
        $repeatRate = $activeCustomers > 0 ? ((float) $repeatCustomers / (float) $activeCustomers) * 100 : 0.0;
        $totalSpend = (float) $crmData->sum('total_spent');
        $averageSpend = $activeCustomers > 0 ? $totalSpend / (float) $activeCustomers : 0.0;

        $topCustomers = $crmData
            ->sortByDesc('total_spent')
            ->take(7)
            ->mapWithKeys(fn ($row) => [$row['name'] => (float) $row['total_spent']]);

        return [
            'crmData' => $crmData,
            'dailyCustomerActivity' => collect($dailyCustomerActivity),
            'activeCustomers' => $activeCustomers,
            'repeatCustomers' => $repeatCustomers,
            'oneTimeCustomers' => $oneTimeCustomers,
            'repeatRate' => $repeatRate,
            'totalSpend' => $totalSpend,
            'averageSpend' => $averageSpend,
            'topCustomers' => $topCustomers,
        ];
    }

    private function paginateCollection(Collection $rows, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $currentPage = max((int) $request->query('page', 1), 1);
        $items = $rows->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $rows->count(),
            $perPage,
            $currentPage,
            [
                'path' => route('admin.reports.generate'),
                'query' => collect($request->query())->except('page')->all(),
                'pageName' => 'page',
            ]
        );
    }

    private function buildKpiCard(string $label, string $value, string $icon, string $tone, ?float $delta): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'icon' => $icon,
            'tone' => $tone,
            'delta' => $delta,
        ];
    }

    private function formatPercent(float $value): string
    {
        return number_format($value, 1) . '%';
    }

    private function formatCurrency(float $value): string
    {
        return 'PHP ' . number_format($value, 2);
    }

    private function formatCurrencyWithPesoSign(float $value): string
    {
        return "\u{20B1}" . number_format($value, 2);
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
        $viewData['generatedBy'] = Auth::user()?->name ?? 'Unknown';
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
                        // Prefer reservation's own department field when available
                        'department' => $reservation->department ?? ($reservation->user ? $reservation->user->department : 'N/A'),
                        'number_of_persons' => $reservation->number_of_persons ?? 0,
                        'status' => ucfirst($reservation->status ?? 'pending'),
                        'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
                    ];
                });

                $reservationsByDay = $reservationData
                    ->groupBy('event_date')
                    ->map(fn ($rows) => $rows->count())
                    ->sortKeys();

                $statusBreakdown = $reservationData
                    ->groupBy(fn ($row) => strtolower($row['status']))
                    ->map(fn ($rows) => $rows->count())
                    ->sortDesc();

                $departmentBreakdown = $reservationData
                    ->groupBy(fn ($row) => $row['department'] ?: 'Unspecified')
                    ->map(fn ($rows) => $rows->count())
                    ->sortDesc()
                    ->take(7);

                $charts = [
                    'trend' => [
                        'type' => 'line',
                        'label' => 'Reservations per Day',
                        'labels' => $reservationsByDay->keys()->values()->all(),
                        'values' => $reservationsByDay->values()->all(),
                    ],
                    'breakdown' => [
                        'type' => 'doughnut',
                        'label' => 'Status Distribution',
                        'labels' => $statusBreakdown->keys()->map(fn ($key) => ucfirst($key))->values()->all(),
                        'values' => $statusBreakdown->values()->all(),
                    ],
                    'topContributors' => [
                        'type' => 'bar',
                        'label' => 'Top Departments by Reservations',
                        'labels' => $departmentBreakdown->keys()->values()->all(),
                        'values' => $departmentBreakdown->values()->all(),
                    ],
                ];

                $busiestDayLabel = 'N/A';
                if ($reservationsByDay->isNotEmpty()) {
                    $maxReservationsInDay = $reservationsByDay->max();
                    $busiestDayKey = $reservationsByDay->search($maxReservationsInDay);
                    if ($busiestDayKey !== false) {
                        $busiestDayLabel = $busiestDayKey . ' (' . $maxReservationsInDay . ')';
                    }
                }

                $topDepartmentLabel = $departmentBreakdown->isNotEmpty()
                    ? ($departmentBreakdown->keys()->first() . ' (' . $departmentBreakdown->first() . ')')
                    : 'N/A';

                $insights = [
                    'Busiest day: ' . $busiestDayLabel,
                    'Top department: ' . $topDepartmentLabel,
                    'Total reservations: ' . number_format((float) $reservations->count(), 0),
                ];

                $viewData['reservationData'] = $reservationData;
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts);
                $viewData['insights'] = $insights;
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

                $dailyRevenue = [];
                $mealTimeRevenue = [];
                $menuRevenue = [];

                foreach ($salesData as $reservation) {
                    $eventDate = $reservation['event_date'] ?? 'N/A';
                    $dailyRevenue[$eventDate] = ($dailyRevenue[$eventDate] ?? 0) + (float) ($reservation['reservation_total'] ?? 0);

                    foreach (($reservation['items'] ?? []) as $item) {
                        $mealKey = $item['meal_time'] ?? 'Unknown';
                        $mealTimeRevenue[$mealKey] = ($mealTimeRevenue[$mealKey] ?? 0) + (float) ($item['total'] ?? 0);

                        $menuKey = $item['menu_name'] ?? 'Unknown Item';
                        $menuRevenue[$menuKey] = ($menuRevenue[$menuKey] ?? 0) + (float) ($item['total'] ?? 0);
                    }
                }

                ksort($dailyRevenue);
                arsort($mealTimeRevenue);
                arsort($menuRevenue);
                $topMenuRevenue = collect($menuRevenue)->take(7);

                $currentAverageOrderValue = $totalReservations > 0 ? $totalRevenue / $totalReservations : 0;
                $highestRevenueDay = 'N/A';
                if (!empty($dailyRevenue)) {
                    $maxRevenueInDay = max($dailyRevenue);
                    $maxRevenueDay = array_search($maxRevenueInDay, $dailyRevenue, true);
                    if ($maxRevenueDay !== false) {
                        $highestRevenueDay = $maxRevenueDay . ' (' . $this->formatCurrency($maxRevenueInDay) . ')';
                    }
                }

                $topMenuLabel = $topMenuRevenue->isNotEmpty()
                    ? ($topMenuRevenue->keys()->first() . ' (' . $this->formatCurrency((float) $topMenuRevenue->first()) . ')')
                    : 'N/A';

                $charts = [
                    'trend' => [
                        'type' => 'line',
                        'label' => 'Revenue per Day',
                        'labels' => array_values(array_keys($dailyRevenue)),
                        'values' => array_values($dailyRevenue),
                    ],
                    'breakdown' => [
                        'type' => 'doughnut',
                        'label' => 'Revenue by Meal Time',
                        'labels' => array_values(array_keys($mealTimeRevenue)),
                        'values' => array_values($mealTimeRevenue),
                    ],
                    'topContributors' => [
                        'type' => 'bar',
                        'label' => 'Top Menu Items',
                        'labels' => $topMenuRevenue->keys()->values()->all(),
                        'values' => $topMenuRevenue->values()->all(),
                    ],
                ];

                $insights = [
                    'Highest revenue day: ' . $highestRevenueDay,
                    'Top menu item: ' . $topMenuLabel,
                    'Average order value: ' . $this->formatCurrency((float) $currentAverageOrderValue),
                ];

                $viewData['salesData'] = $salesData;
                $viewData['totalRevenue'] = $totalRevenue;
                $viewData['totalReservations'] = $totalReservations;
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts);
                $viewData['insights'] = $insights;
                $viewData['reportType'] = 'sales';
                break;

            case 'inventory':
                // Get approved reservations within date range
                $reservations = Reservation::with(['items.menu.items.recipes.inventoryItem'])
                    ->where('status', 'approved')
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
                    ->get();

                $inventoryMetrics = $this->calculateInventoryMetrics($reservations);
                $inventoryData = $inventoryMetrics['inventoryData'];

                $charts = [
                    'trend' => [
                        'type' => 'line',
                        'label' => 'Inventory Usage per Day',
                        'labels' => $inventoryMetrics['dailyUsage']->keys()->values()->all(),
                        'values' => $inventoryMetrics['dailyUsage']->values()->all(),
                    ],
                    'breakdown' => [
                        'type' => 'doughnut',
                        'label' => 'Usage by Unit',
                        'labels' => $inventoryMetrics['unitUsage']->keys()->values()->all(),
                        'values' => $inventoryMetrics['unitUsage']->values()->all(),
                    ],
                    'topContributors' => [
                        'type' => 'bar',
                        'label' => 'Top Inventory Items by Usage',
                        'labels' => $inventoryMetrics['topItems']->keys()->values()->all(),
                        'values' => $inventoryMetrics['topItems']->values()->all(),
                    ],
                ];

                $topItemLabel = $inventoryData->isNotEmpty()
                    ? ($inventoryData->first()['name'] . ' (' . number_format((float) $inventoryData->first()['total_used'], 2) . ')')
                    : 'N/A';

                $insights = [
                    'Most consumed item: ' . $topItemLabel,
                    'Total quantity used: ' . number_format((float) $inventoryMetrics['totalUsed'], 2),
                    'Average usage per reservation: ' . number_format((float) $inventoryMetrics['averageUsedPerReservation'], 2),
                ];

                $viewData['inventoryData'] = $inventoryData;
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts);
                $viewData['insights'] = $insights;
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

                $crmMetrics = $this->calculateCrmMetrics($customers);
                $crmData = $crmMetrics['crmData'];

                $charts = [
                    'trend' => [
                        'type' => 'line',
                        'label' => 'Customer Activity per Day',
                        'labels' => $crmMetrics['dailyCustomerActivity']->keys()->values()->all(),
                        'values' => $crmMetrics['dailyCustomerActivity']->values()->all(),
                    ],
                    'breakdown' => [
                        'type' => 'doughnut',
                        'label' => 'One-time vs Repeat Customers',
                        'labels' => ['One-time', 'Repeat'],
                        'values' => [$crmMetrics['oneTimeCustomers'], $crmMetrics['repeatCustomers']],
                    ],
                    'topContributors' => [
                        'type' => 'bar',
                        'label' => 'Top Customers by Spend',
                        'labels' => $crmMetrics['topCustomers']->keys()->values()->all(),
                        'values' => $crmMetrics['topCustomers']->values()->all(),
                    ],
                ];

                $topCustomerLabel = $crmMetrics['topCustomers']->isNotEmpty()
                    ? ($crmMetrics['topCustomers']->keys()->first() . ' (' . $this->formatCurrency((float) $crmMetrics['topCustomers']->first()) . ')')
                    : 'N/A';

                $insights = [
                    'Top customer by spend: ' . $topCustomerLabel,
                    'Repeat customers: ' . number_format((float) $crmMetrics['repeatCustomers'], 0),
                    'Average spend per customer: ' . $this->formatCurrency((float) $crmMetrics['averageSpend']),
                ];

                $viewData['crmData'] = $crmData;
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts);
                $viewData['insights'] = $insights;
                $viewData['reportType'] = 'crm';
                break;
        }

        // Log PDF export
        AuditTrail::record(
            Auth::id(),
            AuditDictionary::EXPORTED_REPORT_PDF,
            AuditDictionary::MODULE_REPORTS,
            "exported {$reportType} report as PDF"
        );

        $pdf = Pdf::loadView('admin.reports.pdf', $viewData)
            ->setOption('isPhpEnabled', true)
            ->setPaper('a4', 'landscape');

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
        AuditTrail::record(
            Auth::id(),
            AuditDictionary::EXPORTED_REPORT_EXCEL,
            AuditDictionary::MODULE_REPORTS,
            "exported {$reportType} report as Excel"
        );

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
        $notificationService = new NotificationService();
        $notificationService->createAdminNotification($action, $module, $description, $metadata);
    }

    private function buildPdfChartSvgs(array $charts): array
    {
        $result = [];

        foreach ($charts as $key => $chart) {
            $labels = array_values($chart['labels'] ?? []);
            $values = array_values($chart['values'] ?? []);

            if (empty($labels) || empty($values)) {
                continue;
            }

            $svg = $this->renderPdfChartSvg(
                (string) ($chart['type'] ?? 'bar'),
                $labels,
                $values,
                320,
                140
            );

            if (!$svg) {
                continue;
            }

            $result[$key] = [
                'label' => (string) ($chart['label'] ?? ucfirst((string) $key)),
                'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg),
            ];
        }

        return $result;
    }

    private function renderPdfChartSvg(string $type, array $labels, array $values, int $width, int $height): ?string
    {
        $normalizedValues = [];
        foreach ($values as $value) {
            $normalizedValues[] = is_numeric($value) ? (float) $value : 0.0;
        }

        if (empty($normalizedValues)) {
            return null;
        }

        $type = strtolower($type);
        if ($type === 'line') {
            return $this->renderPdfLineChartSvg($labels, $normalizedValues, $width, $height);
        }

        if ($type === 'doughnut' || $type === 'pie') {
            return $this->renderPdfPieChartSvg($labels, $normalizedValues, $width, $height, $type === 'doughnut');
        }

        return $this->renderPdfBarChartSvg($labels, $normalizedValues, $width, $height);
    }

    private function renderPdfLineChartSvg(array $labels, array $values, int $width, int $height): string
    {
        $palette = $this->pdfPalette();
        $left = 40.0;
        $right = $width - 14.0;
        $top = 14.0;
        $bottom = $height - 34.0;
        $plotWidth = max($right - $left, 1.0);
        $plotHeight = max($bottom - $top, 1.0);
        $count = count($values);
        $max = max($values);
        $min = min($values);

        if ($max === $min) {
            $max += 1.0;
            $min = max($min - 1.0, 0.0);
        }

        $polylinePoints = [];
        $markers = [];
        for ($i = 0; $i < $count; $i++) {
            $x = $count === 1
                ? ($left + $plotWidth / 2.0)
                : ($left + ($plotWidth * ((float) $i / (float) ($count - 1))));
            $ratio = (($values[$i] - $min) / ($max - $min));
            $y = $bottom - ($plotHeight * $ratio);
            $polylinePoints[] = $this->svgNum($x) . ',' . $this->svgNum($y);
            $markers[] = '<circle cx="' . $this->svgNum($x) . '" cy="' . $this->svgNum($y) . '" r="2.3" fill="' . $palette[0] . '" />';
        }

        $gridLines = '';
        for ($step = 0; $step <= 4; $step++) {
            $gy = $top + ($plotHeight * ((float) $step / 4.0));
            $valueAtLine = $max - (($max - $min) * ((float) $step / 4.0));
            $gridLines .= '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($gy) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($gy) . '" stroke="#e5e7eb" stroke-width="1" />';
            $gridLines .= '<text x="' . $this->svgNum($left - 6.0) . '" y="' . $this->svgNum($gy + 3.5) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $this->escapeSvgText(number_format($valueAtLine, 0)) . '</text>';
        }

        $firstLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[0] ?? ''), 14));
        $lastLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[$count - 1] ?? ''), 14));

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />'
            . $gridLines
            . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($bottom) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" />'
            . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($top) . '" x2="' . $this->svgNum($left) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" />'
            . '<polyline fill="none" stroke="' . $palette[0] . '" stroke-width="2.3" points="' . implode(' ', $polylinePoints) . '" />'
            . implode('', $markers)
            . '<text x="' . $this->svgNum($left) . '" y="' . $this->svgNum($height - 10.0) . '" text-anchor="start" font-size="9" fill="#6b7280">' . $firstLabel . '</text>'
            . '<text x="' . $this->svgNum($right) . '" y="' . $this->svgNum($height - 10.0) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $lastLabel . '</text>'
            . '</svg>';
    }

    private function renderPdfBarChartSvg(array $labels, array $values, int $width, int $height): string
    {
        $palette = $this->pdfPalette();
        $left = 38.0;
        $right = $width - 12.0;
        $top = 14.0;
        $bottom = $height - 40.0;
        $plotWidth = max($right - $left, 1.0);
        $plotHeight = max($bottom - $top, 1.0);
        $count = max(count($values), 1);
        $max = max($values);
        if ($max <= 0.0) {
            $max = 1.0;
        }

        $gridLines = '';
        for ($step = 0; $step <= 4; $step++) {
            $gy = $top + ($plotHeight * ((float) $step / 4.0));
            $valueAtLine = $max - ($max * ((float) $step / 4.0));
            $gridLines .= '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($gy) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($gy) . '" stroke="#e5e7eb" stroke-width="1" />';
            $gridLines .= '<text x="' . $this->svgNum($left - 6.0) . '" y="' . $this->svgNum($gy + 3.5) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $this->escapeSvgText(number_format($valueAtLine, 0)) . '</text>';
        }

        $bars = '';
        $barSlot = $plotWidth / (float) $count;
        $barWidth = max(min($barSlot * 0.64, 46.0), 10.0);
        for ($i = 0; $i < $count; $i++) {
            $value = (float) ($values[$i] ?? 0.0);
            $heightRatio = $max > 0 ? ($value / $max) : 0.0;
            $barHeight = $plotHeight * $heightRatio;
            $x = $left + ($barSlot * (float) $i) + (($barSlot - $barWidth) / 2.0);
            $y = $bottom - $barHeight;
            $label = $this->escapeSvgText($this->shortLabel((string) ($labels[$i] ?? ''), 10));
            $barColor = $palette[$i % count($palette)];

            $bars .= '<rect x="' . $this->svgNum($x) . '" y="' . $this->svgNum($y) . '" width="' . $this->svgNum($barWidth) . '" height="' . $this->svgNum(max($barHeight, 0.0)) . '" rx="3" ry="3" fill="' . $barColor . '" />';
            $bars .= '<text x="' . $this->svgNum($x + ($barWidth / 2.0)) . '" y="' . $this->svgNum($bottom + 12.0) . '" text-anchor="middle" font-size="8.5" fill="#6b7280">' . $label . '</text>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />'
            . $gridLines
            . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($bottom) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" />'
            . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($top) . '" x2="' . $this->svgNum($left) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" />'
            . $bars
            . '</svg>';
    }

    private function renderPdfPieChartSvg(array $labels, array $values, int $width, int $height, bool $doughnut): string
    {
        $palette = $this->pdfPalette();
        $cx = 140.0;
        $cy = (float) ($height / 2.0);
        $radius = min(78.0, max(($height / 2.0) - 16.0, 40.0));

        $cleanValues = [];
        foreach ($values as $value) {
            $cleanValues[] = max((float) $value, 0.0);
        }

        $total = array_sum($cleanValues);
        if ($total <= 0.0) {
            return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
                . '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />'
                . '<text x="' . $this->svgNum($width / 2.0) . '" y="' . $this->svgNum($height / 2.0) . '" text-anchor="middle" font-size="12" fill="#6b7280">No chart data</text>'
                . '</svg>';
        }

        $slices = '';
        $legend = '';
        $start = -90.0;
        $legendX = 260.0;
        $legendY = 24.0;
        $legendStep = 24.0;

        foreach ($cleanValues as $i => $value) {
            if ($value <= 0) {
                continue;
            }

            $angle = ($value / $total) * 360.0;
            $end = $start + $angle;
            $color = $palette[$i % count($palette)];

            if ($angle >= 359.99) {
                $slices .= '<circle cx="' . $this->svgNum($cx) . '" cy="' . $this->svgNum($cy) . '" r="' . $this->svgNum($radius) . '" fill="' . $color . '" />';
            } else {
                $x1 = $cx + ($radius * cos(deg2rad($start)));
                $y1 = $cy + ($radius * sin(deg2rad($start)));
                $x2 = $cx + ($radius * cos(deg2rad($end)));
                $y2 = $cy + ($radius * sin(deg2rad($end)));
                $largeArc = $angle > 180.0 ? 1 : 0;
                $slices .= '<path d="M ' . $this->svgNum($cx) . ' ' . $this->svgNum($cy)
                    . ' L ' . $this->svgNum($x1) . ' ' . $this->svgNum($y1)
                    . ' A ' . $this->svgNum($radius) . ' ' . $this->svgNum($radius) . ' 0 ' . $largeArc . ' 1 ' . $this->svgNum($x2) . ' ' . $this->svgNum($y2)
                    . ' Z" fill="' . $color . '" />';
            }

            $percent = ($value / $total) * 100.0;
            $legendLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[$i] ?? 'Item'), 22) . ' ' . number_format($percent, 1) . '%');
            $legend .= '<rect x="' . $this->svgNum($legendX) . '" y="' . $this->svgNum($legendY + ($legendStep * (float) $i)) . '" width="12" height="12" rx="2" ry="2" fill="' . $color . '" />';
            $legend .= '<text x="' . $this->svgNum($legendX + 18.0) . '" y="' . $this->svgNum($legendY + ($legendStep * (float) $i) + 10.0) . '" font-size="10" fill="#374151">' . $legendLabel . '</text>';
            $start = $end;
        }

        $centerHole = '';
        if ($doughnut) {
            $centerHole = '<circle cx="' . $this->svgNum($cx) . '" cy="' . $this->svgNum($cy) . '" r="' . $this->svgNum($radius * 0.56) . '" fill="#ffffff" />';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />'
            . $slices
            . $centerHole
            . $legend
            . '</svg>';
    }

    private function pdfPalette(): array
    {
        return ['#00462E', '#0EA5E9', '#F97316', '#7C3AED', '#22C55E', '#EAB308', '#EF4444', '#14B8A6'];
    }

    private function shortLabel(string $value, int $limit): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, max($limit - 1, 1)) . '…';
    }

    private function escapeSvgText(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function svgNum(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
