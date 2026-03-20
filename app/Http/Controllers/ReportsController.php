<?php

namespace App\Http\Controllers;

use App\Exceptions\IncompatibleRecipeUnitException;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\MenuPrice;
use App\Models\InventoryItem;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
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
            'report_type' => 'required|in:reservation,inventory,crm',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportType = $validated['report_type'];
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        if ($request->isMethod('post')) {
            AuditTrail::record(
                Auth::id(),
                AuditDictionary::GENERATED_REPORT,
                AuditDictionary::MODULE_REPORTS,
                "generated {$reportType} report"
            );

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
        $reservations = Reservation::with(['user', 'items.menu']) 
            ->where('status', 'approved')
            ->whereNotNull('event_date')
            ->whereBetween('event_date', [$startDate, $endDate])
            ->orderBy('event_date')
            ->get();

        $totalReservations = $reservations->count();
        $totalParticipants = 0;
        $totalExpectedRevenue = 0;
        $highestPax = 0;

        $reservationData = $reservations->map(function ($reservation) use (&$totalParticipants, &$totalExpectedRevenue, &$highestPax) {
            $revenue = 0;
            
            foreach ($reservation->items as $item) {
                if ($item->menu) {
                    $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                    if($price <= 0) {
                        $price = ($item->menu->type == 'special' ? 200 : 150);
                    }
                    $revenue += $price * $item->quantity;
                }
            }
            if (method_exists($reservation, 'additionals') && $reservation->additionals) {
                $revenue += $reservation->additionals->sum('price');
            }

            $participants = (int) ($reservation->number_of_persons ?? 0);
            $totalParticipants += $participants;
            $totalExpectedRevenue += $revenue;
            
            if ($participants > $highestPax) {
                $highestPax = $participants;
            }

            return [
                'id' => $reservation->id,
                'event_name' => $reservation->event_name ?? 'N/A',
                'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                'department' => $reservation->department ?? ($reservation->user ? $reservation->user->department : 'N/A'),
                'participants' => $participants,
                'expected_revenue' => $revenue,
                'status' => ucfirst($reservation->status ?? 'pending'),
                'payment_status' => ucfirst($reservation->payment_status ?? 'unpaid'),
                'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
            ];
        });

        $reservationsByMonth = $this->buildReservationCountsByMonth($reservations);
        $revenueByDay = $reservationData->groupBy('event_date')->map(fn ($rows) => $rows->sum('expected_revenue'))->sortKeys();

        $kpiCards = [
            $this->buildKpiCard('Total Number of Reservations', number_format($totalReservations, 0), 'fas fa-calendar-check', 'kpi-primary', null),
            $this->buildKpiCard('Expected Revenue', $this->formatCurrencyWithPesoSign($totalExpectedRevenue), 'fas fa-money-bill-wave', 'kpi-success', null),
            $this->buildKpiCard('Highest Pax in a Res.', number_format($highestPax, 0), 'fas fa-user-plus', 'kpi-neutral', null),
        ];

        $charts = [
            'trend' => [
                'type' => 'line',
                'label' => 'Number of Reservations per Month',
                'labels' => $this->formatMonthChartLabels($reservationsByMonth),
                'values' => $reservationsByMonth->values()->all(),
            ],
            'breakdown' => [
                'type' => 'line',
                'label' => 'Revenue per Day (PHP)',
                'labels' => $revenueByDay->keys()->values()->all(),
                'values' => $revenueByDay->values()->all(),
            ]
        ];

        $insights = [
            'Total expected revenue: ' . $this->formatCurrencyWithPesoSign($totalExpectedRevenue),
            'Total participants across all events: ' . number_format($totalParticipants, 0),
            'Total number of reservations: ' . number_format($totalReservations, 0),
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

    private function generateInventoryReport($startDate, $endDate, Request $request)
    {
        $inventoryMetrics = $this->buildInventoryReportMetrics();
        $inventoryData = $inventoryMetrics['inventoryData'];
        $inventoryLowStockItems = $inventoryMetrics['lowStockItems'];
        $inventoryOutOfStockItems = $inventoryMetrics['outOfStockItems'];

        $kpiCards = [
            $this->buildKpiCard('Low Stock / Critical Items', number_format($inventoryMetrics['lowStockCriticalCount'], 0), 'fas fa-exclamation-triangle', 'kpi-warning', null),
            $this->buildKpiCard('Out of Stock Items', number_format($inventoryMetrics['outOfStockCount'], 0), 'fas fa-ban', 'kpi-neutral', null),
            $this->buildKpiCard('Total Items', number_format($inventoryMetrics['totalItems'], 0), 'fas fa-boxes', 'kpi-primary', null),
            $this->buildKpiCard('Commonly Used Items', number_format($inventoryMetrics['commonlyUsedCount'], 0), 'fas fa-list-ol', 'kpi-success', null),
        ];

        $charts = [
            'topContributors' => [
                'type' => 'bar',
                'label' => 'Top 5 Commonly Used Items',
                'labels' => $inventoryMetrics['topCommonItems']->keys()->values()->all(),
                'values' => $inventoryMetrics['topCommonItems']->values()->all(),
            ]
        ];

        $insights = [
            'Low stock / critical items: ' . number_format($inventoryMetrics['lowStockCriticalCount'], 0),
            'Out of stock items: ' . number_format($inventoryMetrics['outOfStockCount'], 0),
            'Total items: ' . number_format($inventoryMetrics['totalItems'], 0),
            'Commonly used items: ' . number_format($inventoryMetrics['commonlyUsedCount'], 0),
        ];

        $inventoryData = $this->paginateCollection($inventoryData, $request);

        return view('admin.reports.show', compact(
            'inventoryData',
            'startDate',
            'endDate',
            'kpiCards',
            'charts',
            'insights',
            'inventoryLowStockItems',
            'inventoryOutOfStockItems'
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
        $crmPresentation = $this->buildCrmReportPresentation($crmMetrics);
        $crmData = $crmMetrics['crmData'];
        $kpiCards = $crmPresentation['kpiCards'];
        $charts = $crmPresentation['charts'];
        $insights = $crmPresentation['insights'];

        $crmData = $this->paginateCollection($crmData, $request);

        return view('admin.reports.show', compact('crmData', 'startDate', 'endDate', 'kpiCards', 'charts', 'insights'))->with('reportType', 'crm');
    }

    private function calculateCrmMetrics(Collection $customers): array
    {
        $crmData = $customers->map(function ($customer) {
            $totalReservations = $customer->reservations->count();
            $approvedReservations = $customer->reservations->where('status', 'approved')->count();
            $totalSpent = $customer->reservations->where('status', 'approved')->sum(function ($reservation) {
                return $reservation->items->sum(function ($item) {
                    if (!$item->menu) return 0;
                    $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                    return $price * ($item->quantity ?? 0);
                });
            });

            return [
                'name' => $customer->name ?? 'N/A',
                'email' => $customer->email ?? 'N/A',
                'department' => $customer->department ?? 'N/A',
                'total_reservations' => $totalReservations,
                'approved_reservations' => $approvedReservations,
                'total_spent' => $totalSpent,
                'last_reservation' => $customer->reservations->max('event_date')?->format('Y-m-d') ?? 'N/A',
            ];
        })->filter(function ($customer) {
            return $customer['total_reservations'] > 0;
        })->values();

        $activeCustomers = $crmData->count();
        $activeOffices = $crmData
            ->pluck('department')
            ->filter(fn ($value) => is_string($value) && trim($value) !== '' && strtoupper(trim($value)) !== 'N/A')
            ->map(fn (string $value) => trim($value))
            ->unique()
            ->count();
        $totalSpend = (float) $crmData->sum('total_spent');
        $topCustomersByRevenue = $crmData
            ->sortByDesc('total_spent')
            ->take(5)
            ->mapWithKeys(fn ($row) => [$row['name'] => (float) $row['total_spent']]);

        $topCustomersByReservations = $crmData
            ->sortByDesc('total_reservations')
            ->take(5)
            ->mapWithKeys(fn ($row) => [$row['name'] => (int) $row['total_reservations']]);

        return [
            'crmData' => $crmData,
            'activeCustomers' => $activeCustomers,
            'activeOffices' => $activeOffices,
            'totalSpend' => $totalSpend,
            'topCustomersByRevenue' => $topCustomersByRevenue,
            'topCustomersByReservations' => $topCustomersByReservations,
        ];
    }

    private function buildCrmReportPresentation(array $crmMetrics): array
    {
        $topRevenueCustomerLabel = $crmMetrics['topCustomersByRevenue']->isNotEmpty()
            ? ($crmMetrics['topCustomersByRevenue']->keys()->first() . ' (' . $this->formatCurrencyWithPesoSign((float) $crmMetrics['topCustomersByRevenue']->first()) . ')')
            : 'N/A';

        $topReservationCustomerLabel = $crmMetrics['topCustomersByReservations']->isNotEmpty()
            ? ($crmMetrics['topCustomersByReservations']->keys()->first() . ' (' . number_format((int) $crmMetrics['topCustomersByReservations']->first(), 0) . ' reservations)')
            : 'N/A';

        return [
            'kpiCards' => [
                $this->buildKpiCard(
                    'Total Customers / Offices',
                    number_format((float) ($crmMetrics['activeCustomers'] ?? 0), 0) . ' / ' . number_format((float) ($crmMetrics['activeOffices'] ?? 0), 0),
                    'fas fa-users',
                    'kpi-primary',
                    null
                ),
            ],
            'charts' => [
                'trend' => [
                    'type' => 'bar',
                    'label' => 'Top 5 Customers by Revenue',
                    'labels' => ($crmMetrics['topCustomersByRevenue'] ?? collect())->keys()->values()->all(),
                    'values' => ($crmMetrics['topCustomersByRevenue'] ?? collect())->values()->all(),
                ],
                'topContributors' => [
                    'type' => 'bar',
                    'label' => 'Top 5 Customers by Reservations',
                    'labels' => ($crmMetrics['topCustomersByReservations'] ?? collect())->keys()->values()->all(),
                    'values' => ($crmMetrics['topCustomersByReservations'] ?? collect())->values()->all(),
                ],
            ],
            'insights' => [
                'Total customers / offices: ' . number_format((float) ($crmMetrics['activeCustomers'] ?? 0), 0) . ' / ' . number_format((float) ($crmMetrics['activeOffices'] ?? 0), 0),
                'Top customer by revenue: ' . $topRevenueCustomerLabel,
                'Top customer by reservations: ' . $topReservationCustomerLabel,
            ],
        ];
    }

    private function paginateCollection(Collection $rows, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $currentPage = max((int) $request->query('page', 1), 1);
        $items = $rows->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator($items, $rows->count(), $perPage, $currentPage, [
            'path' => route('admin.reports.generate'),
            'query' => collect($request->query())->except('page')->all(),
            'pageName' => 'page',
        ]);
    }

    private function buildReservationCountsByMonth(Collection $reservations): Collection
    {
        return $reservations
            ->filter(fn ($reservation) => !empty($reservation->event_date))
            ->groupBy(function ($reservation) {
                $eventDate = $reservation->event_date instanceof Carbon
                    ? $reservation->event_date
                    : Carbon::parse($reservation->event_date);

                return $eventDate->format('Y-m');
            })
            ->map(fn (Collection $rows) => $rows->count())
            ->sortKeys();
    }

    private function formatMonthChartLabels(Collection $monthlyCounts): array
    {
        return $monthlyCounts
            ->keys()
            ->map(function (string $monthKey) {
                return Carbon::createFromFormat('Y-m', $monthKey)->format('M Y');
            })
            ->values()
            ->all();
    }

    private function buildInventoryReportMetrics(): array
    {
        $items = InventoryItem::withCount('recipes')->orderBy('name')->get();

        $inventoryData = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name ?? 'N/A',
                'unit' => $item->unit ?? 'N/A',
                'stock_left' => (float) ($item->qty ?? 0),
                'recipe_links' => (int) ($item->recipes_count ?? 0),
            ];
        });

        $lowStockItems = $inventoryData->filter(function (array $item) {
            return $item['stock_left'] > 0 && $item['stock_left'] <= 10;
        })->values();

        $outOfStockItems = $inventoryData->filter(fn (array $item) => $item['stock_left'] <= 0)->values();

        $lowStockCriticalCount = $lowStockItems->count();
        $outOfStockCount = $outOfStockItems->count();
        $commonlyUsedCount = $inventoryData->filter(fn (array $item) => $item['recipe_links'] > 0)->count();

        $topCommonItems = $inventoryData
            ->filter(fn (array $item) => $item['recipe_links'] > 0)
            ->sortByDesc('recipe_links')
            ->take(5)
            ->mapWithKeys(fn (array $item) => [$item['name'] => $item['recipe_links']]);

        return [
            'inventoryData' => $inventoryData,
            'totalItems' => $items->count(),
            'lowStockCriticalCount' => $lowStockCriticalCount,
            'outOfStockCount' => $outOfStockCount,
            'commonlyUsedCount' => $commonlyUsedCount,
            'topCommonItems' => $topCommonItems,
            'lowStockItems' => $lowStockItems->all(),
            'outOfStockItems' => $outOfStockItems->all(),
        ];
    }

    private function buildKpiCard(string $label, string $value, string $icon, string $tone, ?float $delta): array
    {
        return ['label' => $label, 'value' => $value, 'icon' => $icon, 'tone' => $tone, 'delta' => $delta];
    }

    private function formatPercent(float $value): string { return number_format($value, 1) . '%'; }
    private function formatCurrencyWithPesoSign(float $value): string { return "\u{20B1}" . number_format($value, 2); }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:reservation,inventory,crm',
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
                $reservations = Reservation::with(['user', 'items.menu'])
                    ->where('status', 'approved')
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [$startDate, $endDate])
                    ->orderBy('event_date')
                    ->get();

                $totalReservations = $reservations->count();
                $totalParticipants = 0;
                $totalExpectedRevenue = 0;
                $highestPax = 0;

                $reservationData = $reservations->map(function ($reservation) use (&$totalParticipants, &$totalExpectedRevenue, &$highestPax) {
                    $revenue = 0;
                    foreach ($reservation->items as $item) {
                        if ($item->menu) {
                            $price = MenuPrice::getPriceMap()[$item->menu->type][$item->menu->meal_time] ?? 0;
                            if($price <= 0) $price = ($item->menu->type == 'special' ? 200 : 150);
                            $revenue += $price * $item->quantity;
                        }
                    }
                    if (method_exists($reservation, 'additionals') && $reservation->additionals) {
                        $revenue += $reservation->additionals->sum('price');
                    }
                    
                    $participants = (int) ($reservation->number_of_persons ?? 0);
                    $totalParticipants += $participants;
                    $totalExpectedRevenue += $revenue;

                    if ($participants > $highestPax) {
                        $highestPax = $participants;
                    }

                    return [
                        'id' => $reservation->id,
                        'event_name' => $reservation->event_name ?? 'N/A',
                        'event_date' => $reservation->event_date ? $reservation->event_date->format('Y-m-d') : 'N/A',
                        'customer_name' => $reservation->user ? $reservation->user->name : 'N/A',
                        'department' => $reservation->department ?? ($reservation->user ? $reservation->user->department : 'N/A'),
                        'participants' => $participants,
                        'expected_revenue' => $revenue,
                        'status' => ucfirst($reservation->status ?? 'pending'),
                        'payment_status' => ucfirst($reservation->payment_status ?? 'unpaid'),
                        'created_at' => $reservation->created_at ? $reservation->created_at->format('Y-m-d H:i') : 'N/A',
                    ];
                });

                $reservationsByMonth = $this->buildReservationCountsByMonth($reservations);
                $revenueByDay = $reservationData->groupBy('event_date')->map(fn ($rows) => $rows->sum('expected_revenue'))->sortKeys();

                $charts = [
                    'trend' => ['type' => 'line', 'label' => 'Number of Reservations per Month', 'labels' => $this->formatMonthChartLabels($reservationsByMonth), 'values' => $reservationsByMonth->values()->all()],
                    'breakdown' => ['type' => 'line', 'label' => 'Revenue per Day (PHP)', 'labels' => $revenueByDay->keys()->values()->all(), 'values' => $revenueByDay->values()->all()]
                ];

                $insights = [
                    'Total expected revenue: ' . $this->formatCurrencyWithPesoSign($totalExpectedRevenue),
                    'Total participants across all events: ' . number_format($totalParticipants, 0),
                    'Total number of reservations: ' . number_format($totalReservations, 0),
                ];

                $viewData['reservationData'] = $reservationData;
                $viewData['totalParticipants'] = $totalParticipants;
                $viewData['totalExpectedRevenue'] = $totalExpectedRevenue;
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts);
                $viewData['insights'] = $insights;
                $viewData['reportType'] = 'reservation';
                break;

            case 'inventory':
                $inventoryMetrics = $this->buildInventoryReportMetrics();
                $charts = ['topContributors' => ['type' => 'bar', 'label' => 'Top 5 Commonly Used Items', 'labels' => $inventoryMetrics['topCommonItems']->keys()->values()->all(), 'values' => $inventoryMetrics['topCommonItems']->values()->all()]];
                $insights = [
                    'Low stock / critical items: ' . number_format($inventoryMetrics['lowStockCriticalCount'], 0),
                    'Out of stock items: ' . number_format($inventoryMetrics['outOfStockCount'], 0),
                    'Total items: ' . number_format($inventoryMetrics['totalItems'], 0),
                    'Commonly used items: ' . number_format($inventoryMetrics['commonlyUsedCount'], 0),
                ];
                $viewData['inventoryData'] = $inventoryMetrics['inventoryData']; $viewData['inventoryLowStockItems'] = $inventoryMetrics['lowStockItems']; $viewData['inventoryOutOfStockItems'] = $inventoryMetrics['outOfStockItems']; $viewData['chartSvgs'] = $this->buildPdfChartSvgs($charts); $viewData['insights'] = $insights; $viewData['reportType'] = 'inventory';
                break;

            case 'crm':
                $customers = \App\Models\User::where('role', 'customer')->with(['reservations' => function ($query) use ($startDate, $endDate) { $query->whereNotNull('event_date')->whereBetween('event_date', [$startDate, $endDate])->with(['items.menu']); }])->get();
                $crmMetrics = $this->calculateCrmMetrics($customers);
                $crmPresentation = $this->buildCrmReportPresentation($crmMetrics);
                $viewData['crmData'] = $crmMetrics['crmData'];
                $viewData['chartSvgs'] = $this->buildPdfChartSvgs($crmPresentation['charts']);
                $viewData['insights'] = $crmPresentation['insights'];
                $viewData['reportType'] = 'crm';
                break;
        }

        AuditTrail::record(Auth::id(), AuditDictionary::EXPORTED_REPORT_PDF, AuditDictionary::MODULE_REPORTS, "exported {$reportType} report as PDF");
        $pdf = Pdf::loadView('admin.reports.pdf', $viewData)->setOption('isPhpEnabled', true)->setPaper('a4', 'landscape');
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $request->validate(['report_type' => 'required|in:reservation,inventory,crm', 'start_date' => 'required|date', 'end_date' => 'required|date|after_or_equal:start_date']);
        $reportType = $request->report_type; $startDate = Carbon::parse($request->start_date); $endDate = Carbon::parse($request->end_date);
        $filename = $reportType . '_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx';
        AuditTrail::record(Auth::id(), AuditDictionary::EXPORTED_REPORT_EXCEL, AuditDictionary::MODULE_REPORTS, "exported {$reportType} report as Excel");

        try {
            switch ($reportType) {
                case 'reservation':
                    return Excel::download(new \App\Exports\ReservationReportExport($startDate, $endDate), $filename);
                case 'inventory':
                    return Excel::download(new \App\Exports\InventoryReportExport($startDate, $endDate), $filename);
                case 'crm':
                    return Excel::download(new \App\Exports\CrmReportExport($startDate, $endDate), $filename);
                default:
                    abort(400, 'Invalid report type');
            }
        } catch (IncompatibleRecipeUnitException $e) {
            return back()->withErrors([
                'report_export' => array_merge(
                    ['Cannot export inventory report because incompatible recipe units were found.'],
                    $e->messages()
                ),
            ]);
        }
    }

    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        $notificationService = new NotificationService();
        $notificationService->createAdminNotification($action, $module, $description, $metadata);
    }

    private function buildPdfChartSvgs(array $charts): array
    {
        $result = [];
        foreach ($charts as $key => $chart) {
            $labels = array_values($chart['labels'] ?? []); $values = array_values($chart['values'] ?? []);
            if (empty($labels) || empty($values)) continue;
            $svg = $this->renderPdfChartSvg((string) ($chart['type'] ?? 'bar'), $labels, $values, 320, 140);
            if (!$svg) continue;
            $result[$key] = ['label' => (string) ($chart['label'] ?? ucfirst((string) $key)), 'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg)];
        }
        return $result;
    }

    private function renderPdfChartSvg(string $type, array $labels, array $values, int $width, int $height): ?string
    {
        $normalizedValues = []; foreach ($values as $value) { $normalizedValues[] = is_numeric($value) ? (float) $value : 0.0; }
        if (empty($normalizedValues)) return null;
        $type = strtolower($type);
        if ($type === 'line') return $this->renderPdfLineChartSvg($labels, $normalizedValues, $width, $height);
        if ($type === 'doughnut' || $type === 'pie') return $this->renderPdfPieChartSvg($labels, $normalizedValues, $width, $height, $type === 'doughnut');
        return $this->renderPdfBarChartSvg($labels, $normalizedValues, $width, $height);
    }

    private function renderPdfLineChartSvg(array $labels, array $values, int $width, int $height): string
    {
        $palette = $this->pdfPalette(); $left = 40.0; $right = $width - 14.0; $top = 14.0; $bottom = $height - 34.0;
        $plotWidth = max($right - $left, 1.0); $plotHeight = max($bottom - $top, 1.0);
        $count = count($values); $max = max($values); $min = min($values);
        if ($max === $min) { $max += 1.0; $min = max($min - 1.0, 0.0); }
        $polylinePoints = []; $markers = [];
        for ($i = 0; $i < $count; $i++) {
            $x = $count === 1 ? ($left + $plotWidth / 2.0) : ($left + ($plotWidth * ((float) $i / (float) ($count - 1))));
            $ratio = (($values[$i] - $min) / ($max - $min)); $y = $bottom - ($plotHeight * $ratio);
            $polylinePoints[] = $this->svgNum($x) . ',' . $this->svgNum($y);
            $markers[] = '<circle cx="' . $this->svgNum($x) . '" cy="' . $this->svgNum($y) . '" r="2.3" fill="' . $palette[0] . '" />';
        }
        $gridLines = '';
        for ($step = 0; $step <= 4; $step++) {
            $gy = $top + ($plotHeight * ((float) $step / 4.0)); $valueAtLine = $max - (($max - $min) * ((float) $step / 4.0));
            $gridLines .= '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($gy) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($gy) . '" stroke="#e5e7eb" stroke-width="1" />';
            $gridLines .= '<text x="' . $this->svgNum($left - 6.0) . '" y="' . $this->svgNum($gy + 3.5) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $this->escapeSvgText(number_format($valueAtLine, 0)) . '</text>';
        }
        $firstLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[0] ?? ''), 14));
        $lastLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[$count - 1] ?? ''), 14));
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />' . $gridLines . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($bottom) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" /><line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($top) . '" x2="' . $this->svgNum($left) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" /><polyline fill="none" stroke="' . $palette[0] . '" stroke-width="2.3" points="' . implode(' ', $polylinePoints) . '" />' . implode('', $markers) . '<text x="' . $this->svgNum($left) . '" y="' . $this->svgNum($height - 10.0) . '" text-anchor="start" font-size="9" fill="#6b7280">' . $firstLabel . '</text><text x="' . $this->svgNum($right) . '" y="' . $this->svgNum($height - 10.0) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $lastLabel . '</text></svg>';
    }

    private function renderPdfBarChartSvg(array $labels, array $values, int $width, int $height): string
    {
        $palette = $this->pdfPalette(); $left = 38.0; $right = $width - 12.0; $top = 14.0; $bottom = $height - 40.0;
        $plotWidth = max($right - $left, 1.0); $plotHeight = max($bottom - $top, 1.0);
        $count = max(count($values), 1); $max = max($values); if ($max <= 0.0) { $max = 1.0; }
        $gridLines = '';
        for ($step = 0; $step <= 4; $step++) {
            $gy = $top + ($plotHeight * ((float) $step / 4.0)); $valueAtLine = $max - ($max * ((float) $step / 4.0));
            $gridLines .= '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($gy) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($gy) . '" stroke="#e5e7eb" stroke-width="1" />';
            $gridLines .= '<text x="' . $this->svgNum($left - 6.0) . '" y="' . $this->svgNum($gy + 3.5) . '" text-anchor="end" font-size="9" fill="#6b7280">' . $this->escapeSvgText(number_format($valueAtLine, 0)) . '</text>';
        }
        $bars = ''; $barSlot = $plotWidth / (float) $count; $barWidth = max(min($barSlot * 0.64, 46.0), 10.0);
        for ($i = 0; $i < $count; $i++) {
            $value = (float) ($values[$i] ?? 0.0); $heightRatio = $max > 0 ? ($value / $max) : 0.0;
            $barHeight = $plotHeight * $heightRatio; $x = $left + ($barSlot * (float) $i) + (($barSlot - $barWidth) / 2.0); $y = $bottom - $barHeight;
            $label = $this->escapeSvgText($this->shortLabel((string) ($labels[$i] ?? ''), 10)); $barColor = $palette[$i % count($palette)];
            $bars .= '<rect x="' . $this->svgNum($x) . '" y="' . $this->svgNum($y) . '" width="' . $this->svgNum($barWidth) . '" height="' . $this->svgNum(max($barHeight, 0.0)) . '" rx="3" ry="3" fill="' . $barColor . '" />';
            $bars .= '<text x="' . $this->svgNum($x + ($barWidth / 2.0)) . '" y="' . $this->svgNum($bottom + 12.0) . '" text-anchor="middle" font-size="8.5" fill="#6b7280">' . $label . '</text>';
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />' . $gridLines . '<line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($bottom) . '" x2="' . $this->svgNum($right) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" /><line x1="' . $this->svgNum($left) . '" y1="' . $this->svgNum($top) . '" x2="' . $this->svgNum($left) . '" y2="' . $this->svgNum($bottom) . '" stroke="#d1d5db" stroke-width="1" />' . $bars . '</svg>';
    }

    private function renderPdfPieChartSvg(array $labels, array $values, int $width, int $height, bool $doughnut): string
    {
        $palette = $this->pdfPalette(); $cx = 140.0; $cy = (float) ($height / 2.0); $radius = min(78.0, max(($height / 2.0) - 16.0, 40.0));
        $cleanValues = []; foreach ($values as $value) { $cleanValues[] = max((float) $value, 0.0); }
        $total = array_sum($cleanValues);
        if ($total <= 0.0) { return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" /><text x="' . $this->svgNum($width / 2.0) . '" y="' . $this->svgNum($height / 2.0) . '" text-anchor="middle" font-size="12" fill="#6b7280">No chart data</text></svg>'; }
        $slices = ''; $legend = ''; $start = -90.0; $legendX = 260.0; $legendY = 24.0; $legendStep = 24.0;
        foreach ($cleanValues as $i => $value) {
            if ($value <= 0) continue;
            $angle = ($value / $total) * 360.0; $end = $start + $angle; $color = $palette[$i % count($palette)];
            if ($angle >= 359.99) { $slices .= '<circle cx="' . $this->svgNum($cx) . '" cy="' . $this->svgNum($cy) . '" r="' . $this->svgNum($radius) . '" fill="' . $color . '" />';
            } else {
                $x1 = $cx + ($radius * cos(deg2rad($start))); $y1 = $cy + ($radius * sin(deg2rad($start)));
                $x2 = $cx + ($radius * cos(deg2rad($end))); $y2 = $cy + ($radius * sin(deg2rad($end)));
                $largeArc = $angle > 180.0 ? 1 : 0;
                $slices .= '<path d="M ' . $this->svgNum($cx) . ' ' . $this->svgNum($cy) . ' L ' . $this->svgNum($x1) . ' ' . $this->svgNum($y1) . ' A ' . $this->svgNum($radius) . ' ' . $this->svgNum($radius) . ' 0 ' . $largeArc . ' 1 ' . $this->svgNum($x2) . ' ' . $this->svgNum($y2) . ' Z" fill="' . $color . '" />';
            }
            $percent = ($value / $total) * 100.0;
            $legendLabel = $this->escapeSvgText($this->shortLabel((string) ($labels[$i] ?? 'Item'), 22) . ' ' . number_format($percent, 1) . '%');
            $legend .= '<rect x="' . $this->svgNum($legendX) . '" y="' . $this->svgNum($legendY + ($legendStep * (float) $i)) . '" width="12" height="12" rx="2" ry="2" fill="' . $color . '" />';
            $legend .= '<text x="' . $this->svgNum($legendX + 18.0) . '" y="' . $this->svgNum($legendY + ($legendStep * (float) $i) + 10.0) . '" font-size="10" fill="#374151">' . $legendLabel . '</text>';
            $start = $end;
        }
        $centerHole = ''; if ($doughnut) { $centerHole = '<circle cx="' . $this->svgNum($cx) . '" cy="' . $this->svgNum($cy) . '" r="' . $this->svgNum($radius * 0.56) . '" fill="#ffffff" />'; }
        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '"><rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" />' . $slices . $centerHole . $legend . '</svg>';
    }

    private function pdfPalette(): array { return ['#00462E', '#0EA5E9', '#F97316', '#7C3AED', '#22C55E', '#EAB308', '#EF4444', '#14B8A6']; }
    private function shortLabel(string $value, int $limit): string { $value = trim($value); if ($value === '') return ''; if (mb_strlen($value) <= $limit) return $value; return mb_substr($value, 0, max($limit - 1, 1)) . '…'; }
    private function escapeSvgText(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8'); }
    private function svgNum(float $value): string { return number_format($value, 2, '.', ''); }
}
