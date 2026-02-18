@extends('layouts.sidebar')

@section('page-title', isset($reportType) ? ucfirst($reportType) . ' Report Results' : 'Report Results')

@section('content')
<style>
/* Modern Design Variables */
:root {
    --primary: #00462E;
    --primary-light: #057C3C;
    --accent: #FF6B35;
    --neutral-50: #fafafa;
    --neutral-100: #f5f5f5;
    --neutral-200: #e5e5e5;
    --neutral-300: #d4d4d4;
    --neutral-400: #a3a3a3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
}

/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
}

/* Modern Table Styles */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
}

.modern-table th {
    background: var(--neutral-50);
    font-weight: 600;
    color: var(--neutral-700);
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--neutral-200);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--neutral-100);
    transition: all 0.2s ease;
}

.modern-table tr:last-child td {
    border-bottom: none;
}

.modern-table tr:hover td {
    background: var(--neutral-50);
}

/* Custom Scrollbar */
.modern-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.modern-scrollbar::-webkit-scrollbar-track {
    background: var(--neutral-100);
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--primary-light);
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 70, 46, 0.2);
}

.btn-secondary {
    background: var(--neutral-100);
    color: var(--neutral-700);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: var(--neutral-200);
}

/* Summary Cards */
.summary-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
}

.summary-card-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
}

.summary-card-success {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
}

.summary-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}

.summary-value {
    font-size: 2rem;
    font-weight: 800;
    margin: 0.5rem 0;
}

.summary-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin: 0;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-approved {
    background: rgba(0, 70, 46, 0.1);
    color: #00462E;
    border: 1px solid rgba(0, 70, 46, 0.2);
}

.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.status-declined {
    background: rgba(161, 161, 161, 0.178);
    color: #2b2b2b;
    border: 1px solid rgba(51, 51, 51, 0.2);
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--neutral-400);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--neutral-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
    margin: 0;
}

.header-subtitle {
    color: var(--neutral-500);
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    margin-left: auto;
}

/* Section Styles */
.section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--neutral-900);
    margin-bottom: 1rem;
}

/* Text Truncate */
.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

/* Compact Table Styles */
.compact-table {
    font-size: 0.8rem;
}

.compact-table th,
.compact-table td {
    padding: 0.75rem 0.5rem;
}

.compact-table th {
    font-size: 0.7rem;
}

/* Menu Card Styling for Inventory Sections */
.menu-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
}

.menu-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: #cbd5e0;
}
</style>

<div class="modern-card manu-card p-6 mx-auto max-w-full" style="max-width: calc(100vw - 12rem);">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.reports.index') }}" 
               class=" w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200"
               title="Back to Reports">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                </a>
            <div class="header-icon">
                <i class="fas fa-chart-bar text-white"></i>
            </div>
            <div>
                <h1 class="header-title">
                    @if(isset($reportType))
                        @switch($reportType)
                            @case('reservation')
                                Reservation Report
                                @break
                            @case('sales')
                                Cafeteria Sales Report
                                @break
                            @case('inventory')
                                Inventory Usage Report
                                @break
                            @case('crm')
                                CRM Report
                                @break
                            @default
                                Report
                        @endswitch
                    @else
                        Sales Report
                    @endif
                </h1>
                <p class="header-subtitle">
                    Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                </p>
            </div>
        </div>
        <div class="header-actions">
            <form action="{{ route('admin.reports.export.pdf') }}" method="POST" class="inline">
                @csrf
                @if(isset($reportType))
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                @endif
                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                <button type="submit" class="btn-secondary">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </form>
            <form action="{{ route('admin.reports.export.excel') }}" method="POST" class="inline">
                @csrf
                @if(isset($reportType))
                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                @endif
                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Excel
                </button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($reportType) && $reportType == 'sales')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="summary-card summary-card-primary">
            <div class="summary-icon">
                <i class="fas fa-users text-white"></i>
            </div>
            <div class="summary-value">{{ $totalReservations }}</div>
            <p class="summary-label">Total Reservations</p>
        </div>

        <div class="summary-card summary-card-success">
            <div class="summary-icon">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="summary-value">₱{{ number_format($totalRevenue, 2) }}</div>
            <p class="summary-label">Total Sales</p>
        </div>
    </div>
    @endif

    <!-- Detailed Report Section -->
    <div class="mt-8">
        <div class="mb-6">
            <h2 class="section-title">
                @if(isset($reportType))
                    @switch($reportType)
                        @case('reservation')
                            Detailed Reservation Report
                            @break
                        @case('sales')
                            Detailed Sales Report
                            @break
                        @case('inventory')
                            Detailed Inventory Usage Report
                            @break
                        @case('crm')
                            Detailed CRM Report
                            @break
                        @default
                            Detailed Report
                    @endswitch
                @else
                    Detailed Sales Report
                @endif
            </h2>
        </div>

        <div class="overflow-auto modern-scrollbar">
            @if(isset($reportType) && $reportType == 'reservation')
                @if($reservationData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chart-bar text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No reservation data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Event Date</th>
                                <th>Customer</th>
                                <th>Department</th>
                                <th>Persons</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservationData as $reservation)
                            <tr>
                                <td class="font-semibold text-gray-900">#{{ $reservation['id'] }}</td>
                                <td class="font-medium text-gray-900 text-truncate" style="max-width: 150px;" title="{{ $reservation['event_name'] }}">
                                    {{ $reservation['event_name'] }}
                                </td>
                                <td class="text-gray-600">{{ $reservation['event_date'] }}</td>
                                <td class="font-medium text-gray-900 text-truncate" style="max-width: 120px;" title="{{ $reservation['customer_name'] }}">
                                    {{ $reservation['customer_name'] }}
                                </td>
                                <td class="text-gray-600 text-truncate" style="max-width: 100px;" title="{{ $reservation['department'] }}">
                                    {{ Str::limit($reservation['department'], 15) }}
                                </td>
                                <td class="text-gray-600">{{ $reservation['number_of_persons'] }}</td>
                                <td>
                                    <span class="status-badge 
                                        @if($reservation['status'] == 'approved') status-approved
                                        @elseif($reservation['status'] == 'pending') status-pending
                                        @else status-declined @endif">
                                        {{ $reservation['status'] }}
                                    </span>
                                </td>
                                <td class="text-gray-600">{{ $reservation['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @elseif(isset($reportType) && $reportType == 'inventory')
                @if($inventoryData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chart-bar text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No inventory usage data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th>Inventory Item</th>
                                <th>Unit</th>
                                <th>Total Used</th>
                                <th>Reservations Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryData as $item)
                            <tr>
                                <td class="font-semibold text-gray-900 text-truncate" style="max-width: 200px;" title="{{ $item['name'] }}">
                                    {{ $item['name'] }}
                                </td>
                                <td class="text-gray-600">{{ $item['unit'] }}</td>
                                <td class="font-medium text-gray-900">{{ number_format($item['total_used'], 2) }}</td>
                                <td class="text-gray-600">{{ $item['reservations_count'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @elseif(isset($reportType) && $reportType == 'crm')
                @if($crmData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chart-bar text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No CRM data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table class="modern-table compact-table">
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
                                <td class="font-semibold text-gray-900 text-truncate" style="max-width: 150px;" title="{{ $customer['name'] }}">
                                    {{ $customer['name'] }}
                                </td>
                                <td class="text-gray-600 text-truncate" style="max-width: 180px;" title="{{ $customer['email'] }}">
                                    {{ $customer['email'] }}
                                </td>
                                <td class="font-medium text-gray-900">{{ $customer['total_reservations'] }}</td>
                                <td class="text-gray-600">{{ $customer['approved_reservations'] }}</td>
                                <td class="font-medium text-gray-900">₱{{ number_format($customer['total_spent'], 2) }}</td>
                                <td class="text-gray-600">{{ $customer['last_reservation'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @else
                @if($salesData->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-chart-bar text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No sales data found</p>
                        <p class="text-sm text-gray-500">Try selecting a different date range</p>
                    </div>
                @else
                    <table class="modern-table compact-table">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Reservation</th>
                                <th style="width: 200px;">Event Details</th>
                                <th style="width: 300px;">Menu Items</th>
                                <th style="width: 100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesData as $reservation)
                            <tr>
                                <td>
                                    <div class="font-semibold text-gray-900">#{{ $reservation['reservation_id'] }}</div>
                                    <div class="text-sm text-gray-600 mt-1 text-truncate" style="max-width: 110px;" title="{{ $reservation['customer_name'] }}">
                                        {{ $reservation['customer_name'] }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-medium text-gray-900 text-truncate" style="max-width: 180px;" title="{{ $reservation['event_name'] }}">
                                        {{ $reservation['event_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">{{ $reservation['event_date'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation['number_of_persons'] }} persons</div>
                                </td>
                                <td>
                                    <div class="space-y-2 max-h-32 overflow-y-auto modern-scrollbar">
                                        @foreach($reservation['items'] as $item)
                                        <div class="text-sm p-2 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="font-semibold text-gray-900 text-truncate" style="max-width: 250px;" title="{{ $item['menu_name'] }}">
                                                {{ $item['menu_name'] }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-1">({{ $item['type'] }} - {{ $item['meal_time'] }})</div>
                                            <div class="mt-1 text-gray-600 text-xs">
                                                Qty: {{ $item['quantity'] }} × ₱{{ number_format($item['unit_price'], 2) }} = 
                                                <span class="font-semibold text-green-600">₱{{ number_format($item['total'], 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="text-lg font-bold text-green-600">
                                        ₱{{ number_format($reservation['reservation_total'], 2) }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection