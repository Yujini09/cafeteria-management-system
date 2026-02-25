@extends('layouts.sidebar')

@section('page-title', 'Reports')

@section('content')
@php
    $kpiCards = $kpiCards ?? [];
    $charts = $charts ?? [];
    $insights = $insights ?? [];
@endphp

<style>
.modern-card { background: white; border-radius: 16px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08); border: 1px solid var(--neutral-200); overflow: hidden; }
.summary-card { border-radius: 12px; border: 1px solid var(--neutral-200); padding: 0.9rem 1rem; transition: all 0.2s ease; background: white; display: flex; flex-direction: column; gap: 0.55rem; }
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.75rem; }
.summary-content { display: flex; align-items: center; gap: 0.7rem; min-width: 0; }
.summary-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); }
.kpi-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; }
.kpi-success { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; }
.kpi-warning { background: linear-gradient(135deg, #ea580c 0%, #fb923c 100%); color: white; }
.kpi-neutral { background: linear-gradient(135deg, #374151 0%, #6b7280 100%); color: white; }
.summary-icon { width: 34px; height: 34px; background: rgba(255, 255, 255, 0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.summary-metric { min-width: 0; }
.summary-value { font-size: 1.3rem; font-weight: 800; margin: 0; line-height: 1.1; }
.summary-label { font-size: 0.75rem; opacity: 0.9; margin: 0; line-height: 1.2; }
.summary-delta { font-size: 0.72rem; margin-top: 0; display: inline-flex; align-items: center; gap: 0.3rem; font-weight: 600; }
.summary-delta.positive { color: #14532d; background: rgba(34, 197, 94, 0.15); padding: 0.2rem 0.5rem; border-radius: 999px; }
.summary-delta.negative { color: #7f1d1d; background: rgba(248, 113, 113, 0.15); padding: 0.2rem 0.5rem; border-radius: 999px; }
.summary-delta.neutral { color: #1f2937; background: rgba(255, 255, 255, 0.25); padding: 0.2rem 0.5rem; border-radius: 999px; }
.chart-card { background: white; border: 1px solid var(--neutral-200); border-radius: 14px; padding: 0.75rem; }
.chart-title { font-size: 0.85rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.55rem; }
.chart-canvas-wrap { position: relative; height: 210px; width: 100%; display: flex; align-items: center; justify-content: center; }
.insight-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 0.45rem; }
.insight-list li { padding: 0.5rem 0.65rem; border: 1px solid var(--neutral-200); border-radius: 8px; background: var(--neutral-50); color: var(--neutral-700); font-size: 0.78rem; line-height: 1.25; }
.table-toolbar { display: flex; align-items: center; justify-content: flex-end; margin-bottom: 0.8rem; }
.table-search { width: min(100%, 260px); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 0.55rem 0.75rem; font-size: 0.82rem; }
.table-search:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1); }
.export-dropdown { position: relative; }
.export-menu { position: absolute; right: 0; top: calc(100% + 0.4rem); min-width: 170px; border: 1px solid var(--neutral-200); border-radius: 10px; background: #fff; box-shadow: 0 12px 24px rgba(15, 23, 42, 0.14); padding: 0.35rem; z-index: 20; }
.export-menu[hidden] { display: none; }
.export-menu-item { width: 100%; border: 0; background: transparent; border-radius: 8px; padding: 0.55rem 0.65rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; font-weight: 600; color: var(--neutral-700); cursor: pointer; text-align: left; }
.export-menu-item:hover { background: var(--neutral-50); }
.compact-table { font-size: 0.8rem; }
.compact-table th, .compact-table td { padding: 0.75rem 0.5rem; }
.compact-table th { font-size: 0.7rem; }
.text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
@media (max-width: 640px) { .kpi-grid { grid-template-columns: 1fr; } }
</style>

<div class="modern-card menu-card admin-page-shell p-6 mx-auto max-w-full">
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.reports.index') }}" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200" title="Back to Reports">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div class="header-icon">
                <i class="fas fa-chart-bar text-white"></i>
            </div>
            <div class="header-text">
                <h1 class="header-title">
                    @if(isset($reportType))
                        @switch($reportType)
                            @case('reservation') Reservations Report @break
                            @case('sales') Cafeteria Sales Report @break
                            @case('inventory') Current Inventory Stock Report @break
                            @case('crm') Customer Relationship Management Report @break
                            @default Report
                        @endswitch
                    @else Report @endif
                </h1>
                <p class="header-subtitle">Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="header-actions">
            <div class="export-dropdown" id="reportExportDropdown">
                <button type="button" id="reportExportToggle" class="btn-primary" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export mr-2"></i> Export <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>

                <div id="reportExportMenu" class="export-menu" hidden>
                    <form action="{{ route('admin.reports.export.pdf') }}" method="POST">
                        @csrf
                        @if(isset($reportType)) <input type="hidden" name="report_type" value="{{ $reportType }}"> @endif
                        <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="export-menu-item"><i class="fas fa-file-pdf text-red-600"></i> PDF</button>
                    </form>

                    <form action="{{ route('admin.reports.export.excel') }}" method="POST">
                        @csrf
                        @if(isset($reportType)) <input type="hidden" name="report_type" value="{{ $reportType }}"> @endif
                        <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="export-menu-item"><i class="fas fa-file-excel text-green-600"></i> Excel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($kpiCards))
        <div class="kpi-grid mb-8">
            @foreach($kpiCards as $card)
                <div class="summary-card {{ $card['tone'] ?? 'kpi-neutral' }}">
                    <div class="summary-content">
                        <div class="summary-icon"><i class="{{ $card['icon'] ?? 'fas fa-chart-line' }}"></i></div>
                        <div class="summary-metric">
                            <div class="summary-value">{{ $card['value'] ?? 'N/A' }}</div>
                            <p class="summary-label">{{ $card['label'] ?? 'Metric' }}</p>
                        </div>
                    </div>
                    @if(isset($card['delta']) && $card['delta'] !== null)
                        @php
                            $deltaClass = $card['delta'] > 0 ? 'positive' : ($card['delta'] < 0 ? 'negative' : 'neutral');
                            $deltaSign = $card['delta'] > 0 ? '+' : '';
                        @endphp
                        <span class="summary-delta {{ $deltaClass }}">{{ $deltaSign }}{{ number_format($card['delta'], 1) }}%</span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($charts))
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
            @if(isset($charts['trend']))
            <div class="chart-card">
                <h3 class="chart-title">{{ $charts['trend']['label'] ?? 'Trend' }}</h3>
                <div class="chart-canvas-wrap"><canvas id="trendChart"></canvas></div>
            </div>
            @endif

            @if(isset($charts['breakdown']))
            <div class="chart-card">
                <h3 class="chart-title">{{ $charts['breakdown']['label'] ?? 'Breakdown' }}</h3>
                <div class="chart-canvas-wrap"><canvas id="breakdownChart"></canvas></div>
            </div>
            @endif

            @if(isset($charts['topContributors']))
            <div class="chart-card">
                <h3 class="chart-title">{{ $charts['topContributors']['label'] ?? 'Top Contributors' }}</h3>
                <div class="chart-canvas-wrap"><canvas id="topContributorsChart"></canvas></div>
            </div>
            @endif

            <div class="chart-card">
                <h3 class="chart-title">Key Insights</h3>
                <ul class="insight-list">
                    @forelse($insights as $insight) <li>{{ $insight }}</li>
                    @empty <li>No insights available for this period.</li> @endforelse
                </ul>
            </div>
        </div>
    @endif

    <div class="mt-8">
        <div class="mb-6">
            <h2 class="section-title">
                @if(isset($reportType))
                    @switch($reportType)
                        @case('reservation') Detailed Reservations Report @break
                        @case('sales') Detailed Sales Report @break
                        @case('inventory') Detailed Inventory Stock Report @break
                        @case('crm') Detailed Customer Relationship Management Report @break
                        @default Detailed Report
                    @endswitch
                @else Detailed Report @endif
            </h2>
        </div>

        <div class="table-toolbar">
            <input id="reportTableSearch" type="text" inputmode="search" autocomplete="off" class="table-search" placeholder="Search table rows...">
        </div>

        <div class="overflow-auto modern-scrollbar">
            @if(isset($reportType) && $reportType == 'reservation')
                @if($reservationData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-chart-bar text-gray-400"></i></div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No reservation data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table id="reportTable" class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Customer</th>
                                <th>Department/Office</th>
                                <th>Persons</th>
                                <th>Expected Revenue</th>
                                <th>Status & Payment</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservationData as $reservation)
                            <tr>
                                <td class="font-semibold text-gray-900">#{{ $reservation['id'] }}</td>
                                <td class="font-medium text-gray-900 text-truncate" style="max-width: 150px;" title="{{ $reservation['event_name'] }}">{{ $reservation['event_name'] }}</td>
                                <td class="text-gray-600">{{ $reservation['event_date'] }}</td>
                                <td class="font-medium text-gray-900 text-truncate" style="max-width: 120px;" title="{{ $reservation['customer_name'] }}">{{ $reservation['customer_name'] }}</td>
                                <td class="text-gray-600 text-truncate" style="max-width: 100px;" title="{{ $reservation['department'] }}">{{ Str::limit($reservation['department'], 15) }}</td>
                                <td class="text-gray-600 font-bold">{{ $reservation['participants'] }}</td>
                                <td class="text-green-600 font-bold">&#8369;{{ number_format($reservation['expected_revenue'], 2) }}</td>
                                <td>
                                    {{-- MODIFIED TO SHOW PAYMENT STATUS --}}
                                    <span class="status-badge @if(str_starts_with(strtolower($reservation['status']), 'approved')) status-approved @else status-pending @endif">
                                        {{ $reservation['status'] }} - {{ $reservation['payment_status'] ?? 'Unpaid' }}
                                    </span>
                                </td>
                                <td class="text-gray-600">{{ $reservation['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(method_exists($reservationData, 'hasPages') && $reservationData->hasPages())
                        <div class="mt-6">{{ $reservationData->links('components.pagination') }}</div>
                    @endif
                @endif

            @elseif(isset($reportType) && $reportType == 'sales')
                @if($salesData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-chart-bar text-gray-400"></i></div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No sales data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table id="reportTable" class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Reservation</th>
                                <th style="width: 200px;">Event Details</th>
                                <th style="width: 300px;">Menu Items</th>
                                <th style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesData as $reservation)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-900">#{{ $reservation['reservation_id'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1 text-truncate" style="max-width: 110px;" title="{{ $reservation['customer_name'] }}">{{ $reservation['customer_name'] }}</div>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-900 text-truncate" style="max-width: 180px;" title="{{ $reservation['event_name'] }}">{{ $reservation['event_name'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $reservation['event_date'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation['number_of_persons'] }} persons</div>
                                </td>
                                <td>
                                    <div class="space-y-2 max-h-32 overflow-y-auto modern-scrollbar">
                                        @foreach($reservation['items'] as $item)
                                        <div class="text-sm p-2 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="font-semibold text-gray-900 text-truncate" style="max-width: 250px;" title="{{ $item['menu_name'] }}">{{ $item['menu_name'] }}</div>
                                            <div class="text-gray-500 text-xs mt-1">({{ $item['type'] }} - {{ $item['meal_time'] }})</div>
                                            <div class="mt-1 text-gray-600 text-xs">
                                                Qty: {{ $item['quantity'] }} x &#8369;{{ number_format($item['unit_price'], 2) }} =
                                                <span class="font-semibold text-green-600">&#8369;{{ number_format($item['total'], 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td><div class="text-lg font-bold text-green-600">&#8369;{{ number_format($reservation['reservation_total'], 2) }}</div></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(method_exists($salesData, 'hasPages') && $salesData->hasPages())
                        <div class="mt-6">{{ $salesData->links('components.pagination') }}</div>
                    @endif
                @endif

            @elseif(isset($reportType) && $reportType == 'inventory')
                @if($inventoryData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-chart-bar text-gray-400"></i></div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No inventory usage data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table id="reportTable" class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th>Inventory Item</th>
                                <th>Unit</th>
                                <th>Current Stock Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData as $item)
                            <tr>
                                <td class="font-semibold text-gray-900 text-truncate" style="max-width: 200px;" title="{{ $item['name'] }}">{{ $item['name'] }}</td>
                                <td class="text-gray-600">{{ $item['unit'] }}</td>
                                <td class="font-medium text-gray-900">
                                    <span class="{{ $item['stock_left'] <= 10 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ number_format($item['stock_left'], 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(method_exists($inventoryData, 'hasPages') && $inventoryData->hasPages())
                        <div class="mt-6">{{ $inventoryData->links('components.pagination') }}</div>
                    @endif
                @endif

            @elseif(isset($reportType) && $reportType == 'crm')
                @if($crmData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-chart-bar text-gray-400"></i></div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No customer relationship management data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table id="reportTable" class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total Reservations</th>
                                <th>Approved</th>
                                <th>Total Spent</th>
                                <th>Last Reservation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crmData as $customer)
                            <tr>
                                <td class="font-semibold text-gray-900 text-truncate" style="max-width: 150px;" title="{{ $customer['name'] }}">{{ $customer['name'] }}</td>
                                <td class="text-gray-600 text-truncate" style="max-width: 180px;" title="{{ $customer['email'] }}">{{ $customer['email'] }}</td>
                                <td class="font-medium text-gray-900">{{ $customer['total_reservations'] }}</td>
                                <td class="text-gray-600">{{ $customer['approved_reservations'] }}</td>
                                <td class="font-medium text-gray-900">&#8369;{{ number_format($customer['total_spent'], 2) }}</td>
                                <td class="text-gray-600">{{ $customer['last_reservation'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(method_exists($crmData, 'hasPages') && $crmData->hasPages())
                        <div class="mt-6">{{ $crmData->links('components.pagination') }}</div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>

@if(!empty($charts))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endif
<script>
(function () {
    const searchInput = document.getElementById('reportTableSearch');
    const reportTable = document.getElementById('reportTable');

    if (searchInput && reportTable) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLowerCase();
            reportTable.querySelectorAll('tbody tr').forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    const exportToggle = document.getElementById('reportExportToggle');
    const exportMenu = document.getElementById('reportExportMenu');
    const exportDropdown = document.getElementById('reportExportDropdown');

    if (exportToggle && exportMenu && exportDropdown) {
        exportToggle.addEventListener('click', function () {
            const isHidden = exportMenu.hasAttribute('hidden');
            if (isHidden) {
                exportMenu.removeAttribute('hidden');
                exportToggle.setAttribute('aria-expanded', 'true');
            } else {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('click', function (event) {
            if (!exportDropdown.contains(event.target)) {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    const charts = @json($charts ?? []);
    if (!window.Chart || !charts || Object.keys(charts).length === 0) {
        return;
    }

    const palette = ['#00462E', '#0EA5E9', '#F97316', '#7C3AED', '#22C55E', '#EAB308', '#EF4444', '#14B8A6'];

    function createDataset(config, index) {
        const type = config.type || 'bar';
        const values = Array.isArray(config.values) ? config.values : Object.values(config.values || {});
        const color = palette[index % palette.length];

        if (type === 'doughnut' || type === 'pie') {
            const colors = values.map(function (_, i) {
                return palette[i % palette.length];
            });
            return {
                label: config.label || '',
                data: values,
                backgroundColor: colors,
                borderColor: '#ffffff',
                borderWidth: 2
            };
        }

        return {
            label: config.label || '',
            data: values,
            borderColor: color,
            backgroundColor: type === 'line' ? 'rgba(0, 70, 46, 0.16)' : color,
            borderWidth: 2,
            tension: type === 'line' ? 0.28 : 0,
            fill: type === 'line'
        };
    }

    function renderChart(canvasId, config, index) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !config) return;

        const labels = Array.isArray(config.labels) ? config.labels : Object.values(config.labels || {});
        
        if (labels.length === 0) {
            const ctx = canvas.getContext('2d');
            ctx.font = "bold 13px sans-serif";
            ctx.fillStyle = "#9ca3af";
            ctx.textAlign = "center";
            ctx.fillText("No data available for this period", canvas.width / 2, canvas.height / 2);
            return;
        }

        const type = config.type || 'bar';
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            if (canvasId === 'topContributorsChart' && type === 'bar') {
                                const value = typeof context.parsed?.y === 'number' ? context.parsed.y : (typeof context.parsed?.x === 'number' ? context.parsed.x : Number(context.raw ?? 0));
                                const safeValue = Number.isFinite(value) ? value : 0;
                                const suffix = safeValue === 1 ? 'reservation' : 'reservations';
                                return `${context.label}: ${safeValue} ${suffix}`;
                            }
                            const value = typeof context.parsed?.y === 'number' ? context.parsed.y : (typeof context.parsed?.x === 'number' ? context.parsed.x : context.raw);
                            return `${config.label || ''}: ${value}`;
                        }
                    }
                }
            }
        };

        if (type !== 'doughnut' && type !== 'pie') {
            options.scales = {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            };
        }

        new Chart(canvas, {
            type: type,
            data: { labels: labels, datasets: [createDataset(config, index)] },
            options: options
        });
    }

    renderChart('trendChart', charts.trend, 0);
    renderChart('breakdownChart', charts.breakdown, 1);
    renderChart('topContributorsChart', charts.topContributors, 2);
})();
</script>
@endsection