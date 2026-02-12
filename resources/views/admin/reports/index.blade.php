@extends('layouts.sidebar')

@section('page-title', 'Reports')

@section('content')
<style>
/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--neutral-200);
    overflow: hidden;
}

/* Form Styles */
.modern-form {
    background: var(--neutral-50);
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--neutral-700);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.form-label-inline {
    margin-bottom: 0;
}

.form-row {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 640px) {
    .form-row {
        flex-direction: row;
        align-items: center;
    }
}

.form-select, .form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--neutral-300);
    border-radius: 10px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

.form-error {
    border-color: #dc2626 !important;
}

.error-message {
    font-size: 0.75rem;
    color: #dc2626;
    margin-top: 0.25rem;
}

/* Action Buttons */
.action-btn {
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
    background: var(--primary);
    color: white;
}

.action-btn:hover {
    background: var(--primary-light);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 70, 46, 0.2);
}

/* Quick Action Buttons */
.quick-action-btn {
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid var(--neutral-300);
    background: white;
    color: var(--neutral-700);
    text-align: center;
}

.quick-action-btn:hover {
    background: var(--neutral-50);
    border-color: var(--neutral-400);
    transform: translateY(-1px);
}

/* Icon Sizes */
.icon-sm {
    width: 14px;
    height: 14px;
}

.icon-md {
    width: 16px;
    height: 16px;
}

.icon-lg {
    width: 20px;
    height: 20px;
}

.section-divider {
    border-top: 1px solid var(--neutral-200);
    margin: 2rem 0;
}

</style>

<div class="modern-card menu-card admin-page-shell p-6 mx-auto max-w-full">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <svg class="icon-lg text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Reports</h1>
                <p class="header-subtitle">Generate and export various reports for approved reservations within a specific date range</p>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <form action="{{ route('admin.reports.generate') }}" method="POST" class="modern-form">
        @csrf

        <!-- Report Type -->
        <div class="form-group form-row">
            <label for="report_type" class="form-label form-label-inline">
                Report Type
            </label>
            <select id="report_type"
                    name="report_type"
                    class="form-select @error('report_type') form-error @enderror"
                    data-admin-select="true"
                    required>
                <option value="">Select Report Type</option>
                <option value="reservation" {{ old('report_type') == 'reservation' ? 'selected' : '' }}>Reservation Report</option>
                <option value="sales" {{ old('report_type') == 'sales' ? 'selected' : '' }}>Cafeteria Sales Report</option>
                <option value="inventory" {{ old('report_type') == 'inventory' ? 'selected' : '' }}>Inventory Usage Report</option>
                <option value="crm" {{ old('report_type') == 'crm' ? 'selected' : '' }}>CRM Report</option>
            </select>
            @error('report_type')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Start Date -->
            <div class="form-group">
                <label for="start_date" class="form-label">
                    Start Date
                </label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       value="{{ old('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                       class="form-input @error('start_date') form-error @enderror"
                       required>
                @error('start_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- End Date -->
            <div class="form-group">
                <label for="end_date" class="form-label">
                    End Date
                </label>
                <input type="date"
                       id="end_date"
                       name="end_date"
                       value="{{ old('end_date', now()->format('Y-m-d')) }}"
                       class="form-input @error('end_date') form-error @enderror"
                       required>
                @error('end_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Generate Button -->
        <div class="flex justify-center pt-2">
            <button type="submit" class="action-btn">
                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Generate Report
            </button>
        </div>
    </form>

    <!-- Quick Date Ranges -->
    <div class="section-divider"></div>
    
    <div class="mt-6">
        <h3 class="section-title">Quick Date Ranges</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button type="button"
                    onclick="setDateRange('today')"
                    class="quick-action-btn">
                Today
            </button>
            <button type="button"
                    onclick="setDateRange('week')"
                    class="quick-action-btn">
                This Week
            </button>
            <button type="button"
                    onclick="setDateRange('month')"
                    class="quick-action-btn">
                This Month
            </button>
            <button type="button"
                    onclick="setDateRange('year')"
                    class="quick-action-btn">
                This Year
            </button>
        </div>
    </div>
</div>

<script>
function setDateRange(range) {
    const today = new Date();
    let startDate, endDate;

    switch(range) {
        case 'today':
            startDate = endDate = today;
            break;
        case 'week':
            // Monday of current week
            const monday = new Date(today);
            monday.setDate(today.getDate() - today.getDay() + 1);
            startDate = monday;
            // Sunday of current week
            const sunday = new Date(monday);
            sunday.setDate(monday.getDate() + 6);
            endDate = sunday;
            break;
        case 'month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today.getFullYear(), 11, 31);
            break;
    }

    document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
}
</script>
@endsection
