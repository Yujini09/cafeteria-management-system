@extends('layouts.sidebar')
@section('page-title', 'Reservations')

@section('content')
<style>
.modern-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--neutral-200);
    overflow: hidden;
    transition: all 0.25s ease;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
}

/* Keep all table headers/cells left-aligned for consistent layout */
.modern-table th,
.modern-table td {
    text-align: left;
}

/* Add a little visual gap between Status and Payment columns */
.modern-table th.column-status,
.modern-table td.column-status {
    padding-right: 0.75rem;
    overflow: visible;
}

.modern-table th.column-payment,
.modern-table td.column-payment {
    padding-left: 1rem;
    padding-right: 1rem;
}

.column-status .status-badge {
    white-space: nowrap;
    overflow: visible;
}

/* Status Badges */
.status-badge {
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    gap: 0.375rem;
    border: 1px solid transparent;
}

/* NEW PAYMENT BADGE STYLES */
.payment-unpaid {
    background: #fef3c7;
    color: #92400e;
}

.payment-paid {
    background: #dcfce7;
    color: #166534;
}

.payment-na {
    background: #f3f4f6;
    color: #4b5563;
}

.payment-filter-control {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.payment-filter-native {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    padding-right: 0.75rem;
    opacity: 0;
    cursor: pointer;
    z-index: 1;
}

.payment-filter-label {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding-right: 0.3rem;
    color: inherit;
    pointer-events: none;
    user-select: none;
}

.payment-filter-label.is-active {
    color: #374151;
}

/* Filter Styles */
.filter-select {
    background: white;
    border: 1px solid var(--neutral-300);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

/* Header Styles */
.page-header {
    flex-wrap: wrap;
}

.export-dropdown {
    position: relative;
}

.export-menu {
    position: absolute;
    right: 0;
    top: calc(100% + 0.4rem);
    min-width: 170px;
    border: 1px solid var(--neutral-200);
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.14);
    padding: 0.35rem;
    z-index: 40;
}

.export-menu[hidden] {
    display: none;
}

.export-menu-item {
    width: 100%;
    border: 0;
    background: transparent;
    border-radius: 8px;
    padding: 0.55rem 0.65rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--neutral-700);
    cursor: pointer;
    text-align: left;
}

.export-menu-item:hover {
    background: var(--neutral-50);
}

/* Filter Section */
.filter-section {
    background: var(--neutral-50);
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
}

/* Customer Info */
.customer-name {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
}

/* ID Link */
.id-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
    font-size: 0.875rem;
}

.id-link:hover {
    color: var(--primary-light);
}

/* Short Email Display */
.short-email {
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.short-email:hover {
    overflow: visible;
    white-space: normal;
    background: white;
    position: relative;
    z-index: 10;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Floating View Button */
.table-view-overlay-host {
    position: relative;
}

.table-floating-view-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 9999px;
    border: 1px solid transparent;
    background: var(--primary);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    text-decoration: none;
    position: absolute;
    right: 0.75rem;
    top: 0.5rem;
    transform: translateX(6px);
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
    transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s ease;
    z-index: 30;
}

.table-floating-view-btn:hover {
    background: #003824;
    color: #ffffff;
}

.table-floating-view-btn.is-visible {
    opacity: 1;
    pointer-events: auto;
    visibility: visible;
    transform: translateX(0);
}

/* Column Widths */
.column-id { width: 80px; }
.column-customer { width: 140px; }
.column-department { width: 180px; }
.column-status { width: 144px; min-width: 144px; }
.column-payment { width: 168px; }
.column-email { width: 140px; }
.column-date { width: 140px; }

/* Responsive Design */
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; }
    .header-content { width: 100%; }
    .header-actions { width: 100%; }
    .export-dropdown { width: 100%; }
    #reservationsExportToggle { width: 100%; justify-content: center; }
    .export-menu { left: 0; right: 0; min-width: 0; }
}
</style>

<div class="modern-card admin-page-shell p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
    
    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-calendar-check" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Reservations</h1>
                <p class="header-subtitle">Manage and review all reservation requests</p>
            </div>
        </div>
    <div class="header-actions w-full md:w-auto flex flex-col items-stretch md:items-end gap-3">
        <div class="relative w-full sm:w-64 md:w-72">
            <input type="text"
                   inputmode="search"
                   autocomplete="off"
                   id="reservationsSearchInput"
                   name="search"
                   form="reservationsFiltersForm"
                   value="{{ $search }}"
                   placeholder="Search reservations..."
                   class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                   aria-label="Search reservations">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <button id="reservationsClearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600 {{ filled($search) ? '' : 'hidden' }}" aria-label="Clear reservations search">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <span class="inline-flex w-fit items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
            <x-admin.ui.icon name="fa-calendar-check" size="xs" />
            Total Reservations: {{ $reservations->total() }}
        </span>

        <div class="export-dropdown w-full sm:w-auto md:ml-auto" id="reservationsExportDropdown">
            <button type="button" id="reservationsExportToggle" class="btn-primary" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-file-export mr-2"></i> Export <i class="fas fa-chevron-down ml-2 text-xs"></i>
            </button>

            <div id="reservationsExportMenu" class="export-menu" hidden>
                <form action="{{ route('admin.reservations.export.list.pdf') }}" method="POST">
                    @csrf
                    @if($search !== null && $search !== '')
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if($status !== null)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    @if($payment !== null)
                        <input type="hidden" name="payment" value="{{ $payment }}">
                    @endif
                    @if($department !== null)
                        <input type="hidden" name="department" value="{{ $department }}">
                    @endif
                    @if($createdFrom !== null)
                        <input type="hidden" name="created_from" value="{{ $createdFrom }}">
                    @endif
                    @if($createdTo !== null)
                        <input type="hidden" name="created_to" value="{{ $createdTo }}">
                    @endif
                    <input type="hidden" name="created_sort" value="{{ $createdSort }}">
                    <button type="submit" class="export-menu-item">
                        <i class="fas fa-file-pdf text-red-600"></i> PDF
                    </button>
                </form>

                <form action="{{ route('admin.reservations.export.list.excel') }}" method="POST">
                    @csrf
                    @if($search !== null && $search !== '')
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if($status !== null)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    @if($payment !== null)
                        <input type="hidden" name="payment" value="{{ $payment }}">
                    @endif
                    @if($department !== null)
                        <input type="hidden" name="department" value="{{ $department }}">
                    @endif
                    @if($createdFrom !== null)
                        <input type="hidden" name="created_from" value="{{ $createdFrom }}">
                    @endif
                    @if($createdTo !== null)
                        <input type="hidden" name="created_to" value="{{ $createdTo }}">
                    @endif
                    <input type="hidden" name="created_sort" value="{{ $createdSort }}">
                    <button type="submit" class="export-menu-item">
                        <i class="fas fa-file-excel text-green-600"></i> Excel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
        <form method="GET" action="{{ route('admin.reservations') }}" id="reservationsFiltersForm" class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(12rem,14rem)_minmax(12rem,14rem)_minmax(10rem,1fr)_minmax(10rem,1fr)_auto] xl:items-end">
            <input type="hidden" name="created_sort" value="{{ $createdSort }}">
            @if($department !== null)
                <input type="hidden" name="department" value="{{ $department }}">
            @endif

            <div class="flex flex-col gap-1">
                <label for="statusFilter" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Status</label>
                <select name="status"
                        id="statusFilter"
                        class="admin-select w-full"
                        data-admin-select="true"
                        onchange="this.form.submit()"
                        aria-label="Filter reservations by status">
                    <option value="" {{ $status === null ? 'selected' : '' }}>All Status</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="declined" {{ $status === 'declined' ? 'selected' : '' }}>Declined</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="paymentFilter" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Payment</label>
                <select name="payment"
                        id="paymentFilter"
                        class="admin-select w-full"
                        data-admin-select="true"
                        onchange="this.form.submit()"
                        aria-label="Filter reservations by payment status">
                    <option value="" {{ $payment === null ? 'selected' : '' }}>All Types</option>
                    <option value="paid" {{ $payment === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $payment === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label for="created_from" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date from</label>
                <input type="date"
                       name="created_from"
                       id="created_from"
                       value="{{ $createdFrom }}"
                       max="{{ $createdTo ?: '' }}"
                       onchange="this.form.submit()"
                       class="w-full rounded-admin border border-admin-neutral-300 bg-white px-3 py-2.5 text-sm text-admin-neutral-700 focus:border-admin-primary focus:outline-none focus:ring-2 focus:ring-admin-primary/20"
                       aria-label="Filter reservations date from">
            </div>

            <div class="flex flex-col gap-1">
                <label for="created_to" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date to</label>
                <input type="date"
                       name="created_to"
                       id="created_to"
                       value="{{ $createdTo }}"
                       min="{{ $createdFrom ?: '' }}"
                       onchange="this.form.submit()"
                       class="w-full rounded-admin border border-admin-neutral-300 bg-white px-3 py-2.5 text-sm text-admin-neutral-700 focus:border-admin-primary focus:outline-none focus:ring-2 focus:ring-admin-primary/20"
                       aria-label="Filter reservations date to">
            </div>

            @php
                $resetFiltersUrl = route('admin.reservations', array_filter([
                    'created_sort' => $createdSort,
                    'department' => $department,
                ], static fn ($value) => $value !== null && $value !== ''));
            @endphp
            <div class="flex items-end">
                <a href="{{ $resetFiltersUrl }}"
                   class="btn-secondary inline-flex w-full items-center justify-center sm:w-auto">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div id="reservationsTableHost" class="table-view-overlay-host">
        <div id="reservationsTableScroll" data-table-scroll class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
            <table class="modern-table table-fixed w-full">
            <colgroup>
                <col class="w-14">
                <col class="w-44">
                <col class="w-56">
                <col class="w-36">
                <col class="w-44">
                <col class="w-48">
                <col class="w-48">
            </colgroup>
            <thead>
                <tr>
                    <th class="column-id text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="column-customer text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="column-department text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <form method="GET" action="{{ route('admin.reservations') }}" class="payment-filter-control">
                            <input type="hidden" name="created_sort" value="{{ $createdSort }}">
                            @if($search !== null && $search !== '')
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            @if($status !== null)
                                <input type="hidden" name="status" value="{{ $status }}">
                            @endif
                            @if($payment !== null)
                                <input type="hidden" name="payment" value="{{ $payment }}">
                            @endif
                            @if($createdFrom !== null)
                                <input type="hidden" name="created_from" value="{{ $createdFrom }}">
                            @endif
                            @if($createdTo !== null)
                                <input type="hidden" name="created_to" value="{{ $createdTo }}">
                            @endif
                            <select name="department"
                                    id="departmentColumnFilter"
                                    onchange="this.form.submit()"
                                    class="payment-filter-native"
                                    aria-label="Filter by office or department">
                                <option value="" {{ $department === null ? 'selected' : '' }}>All Office/Dept</option>
                                @foreach(($departmentOptions ?? []) as $departmentOption)
                                    <option value="{{ $departmentOption }}" {{ $department === $departmentOption ? 'selected' : '' }}>
                                        {{ $departmentOption }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="payment-filter-label {{ $department !== null ? 'is-active' : '' }}">
                                <span>Office/Dept</span>
                                <x-admin.ui.icon name="fa-chevron-down" style="fas" size="xs" class="text-admin-neutral-400" />
                            </span>
                        </form>
                    </th>
                    <th class="column-status text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="column-payment text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="column-date text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        @php
                            $nextCreatedSort = $createdSort === 'asc' ? 'desc' : 'asc';
                            $createdSortIcon = $createdSort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down';
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['created_sort' => $nextCreatedSort, 'page' => null]) }}"
                           class="group inline-flex items-center gap-2 hover:text-gray-700"
                           aria-label="Sort by created date">
                            <span>Created</span>
                            <x-admin.ui.icon name="{{ $createdSortIcon }}" style="fas" size="sm" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
                        </a>
                    </th>
                    <th class="column-email text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($reservations as $r)
                    @php
                        $statusText = strtolower($r->status);
                        $statusLabel = match($statusText) {
                            'approved' => 'Approved',
                            'declined' => 'Declined',
                            'cancelled' => 'Cancelled',
                            default => 'Pending'
                        };
                        
                        $statusClass = match($statusText) {
                            'approved' => 'bg-green-100 text-green-700',
                            'declined' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-700', 
                            default => 'bg-yellow-100 text-yellow-700'
                        };
                        
                        $statusIcon = match($statusText) {
                            'approved' => 'fa-check',
                            'declined' => 'fa-xmark',
                            'cancelled' => 'fa-ban', 
                            default => 'fa-clock'
                        };

                        $customerName = $r->contact_person ?? optional($r->user)->name ?? '—';
                        
                        $department = $r->department ?? optional($r->user)->department ?? 'N/A';
                        $shortDepartment = (strlen($department) > 15) ? substr($department, 0, 15) . '...' : $department;

                        $email = $r->email ?? optional($r->user)->email ?? '—';
                        $shortEmail = (strlen($email) > 15) ? substr($email, 0, 15) . '...' : $email;
                    @endphp
                    <tr class="reservation-row hover:bg-admin-neutral-50 transition-colors duration-admin" data-view-url="{{ route('admin.reservations.show', $r) }}">
                        <td class="px-4 py-4 text-sm font-semibold">
                            <a href="{{ route('admin.reservations.show', $r) }}" class="text-admin-primary hover:text-admin-primary-light transition-colors duration-admin">
                                {{ $r->id }}
                            </a>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-admin-neutral-900">{{ $customerName }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-admin-neutral-600">
                            <span class="short-email" title="{{ $department }}">{{ $shortDepartment }}</span>
                        </td>
                        <td class="px-4 py-4 column-status">
                            <span class="status-badge {{ $statusClass }} inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold">
                                <x-admin.ui.icon name="{{ $statusIcon }}" style="fas" size="sm" />
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4 column-payment">
                            {{-- NEW STRICTLY PAID OR UNPAID PAYMENT LOGIC --}}
                            @if(in_array($statusText, ['declined', 'cancelled']))
                                <span class="status-badge payment-na inline-flex items-center px-2.5 py-1 text-[11px] font-bold">N/A</span>
                            @elseif(($r->payment_status ?? 'unpaid') === 'paid')
                                <span class="status-badge payment-paid inline-flex items-center px-2.5 py-1 text-[11px] font-bold">Paid</span>
                                @if(!empty($r->or_number))
                                @endif
                            @else
                                <span class="status-badge payment-unpaid inline-flex items-center px-2.5 py-1 text-[11px] font-bold">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-admin-neutral-600 whitespace-nowrap">
                            {{ $r->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            <span class="short-email" title="{{ $email }}">{{ $shortEmail }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-12 text-center">
                                <div class="empty-state-icon flex justify-center mb-3">
                                    <svg class="w-10 h-10 text-admin-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-admin-neutral-900 mb-1">No Reservations Found</p>
                                <p class="text-sm text-admin-neutral-500">Try adjusting your filter or check back later.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <a id="reservationsFloatingViewBtn"
           href="#"
           class="table-floating-view-btn"
           aria-label="View reservation"
           aria-hidden="true">
            <x-admin.ui.icon name="fa-eye" style="fas" size="sm" class="text-white" />
            View
        </a>
    </div>

    @if($reservations->hasPages())
        <div class="mt-6">
            {{ $reservations->links('components.pagination') }}
        </div>
    @endif
</div>

<script>
(() => {
    const reservationsFiltersForm = document.getElementById('reservationsFiltersForm');
    const searchInput = document.getElementById('reservationsSearchInput');
    const clearSearch = document.getElementById('reservationsClearSearch');

    if (reservationsFiltersForm && searchInput) {
        let submitTimer = null;
        const submitReservationsFilters = () => {
            reservationsFiltersForm.requestSubmit();
        };

        searchInput.addEventListener('input', () => {
            window.clearTimeout(submitTimer);
            submitTimer = window.setTimeout(submitReservationsFilters, 300);
        });

        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                window.clearTimeout(submitTimer);
                submitReservationsFilters();
            }
        });

        reservationsFiltersForm.addEventListener('submit', () => {
            window.clearTimeout(submitTimer);
        });
    }

    if (clearSearch && searchInput && reservationsFiltersForm) {
        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            reservationsFiltersForm.requestSubmit();
        });
    }

    const createdFromInput = document.getElementById('created_from');
    const createdToInput = document.getElementById('created_to');

    if (createdFromInput && createdToInput) {
        const syncDateRangeBounds = () => {
            createdFromInput.max = createdToInput.value || '';
            createdToInput.min = createdFromInput.value || '';
        };

        ['input', 'change'].forEach((eventName) => {
            createdFromInput.addEventListener(eventName, syncDateRangeBounds);
            createdToInput.addEventListener(eventName, syncDateRangeBounds);
        });

        syncDateRangeBounds();
    }

    const exportToggle = document.getElementById('reservationsExportToggle');
    const exportMenu = document.getElementById('reservationsExportMenu');
    const exportDropdown = document.getElementById('reservationsExportDropdown');

    if (exportToggle && exportMenu && exportDropdown) {
        exportToggle.addEventListener('click', () => {
            const isHidden = exportMenu.hasAttribute('hidden');
            if (isHidden) {
                exportMenu.removeAttribute('hidden');
                exportToggle.setAttribute('aria-expanded', 'true');
            } else {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('click', (event) => {
            if (!exportDropdown.contains(event.target)) {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                exportMenu.setAttribute('hidden', '');
                exportToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    function initReservationsFloatingView() {
        const host = document.getElementById('reservationsTableHost');
        const scrollArea = document.getElementById('reservationsTableScroll');
        const button = document.getElementById('reservationsFloatingViewBtn');
        if (!host || !scrollArea || !button) return;
        if (host.dataset.floatingViewBound === 'true') return;
        host.dataset.floatingViewBound = 'true';

        let activeRow = null;

        const hideButton = () => {
            activeRow = null;
            button.classList.remove('is-visible');
            button.setAttribute('aria-hidden', 'true');
        };

        const updateButtonPosition = () => {
            if (!activeRow || !scrollArea.contains(activeRow)) return;

            const hostRect = host.getBoundingClientRect();
            const scrollRect = scrollArea.getBoundingClientRect();
            const rowRect = activeRow.getBoundingClientRect();
            if (rowRect.bottom <= scrollRect.top || rowRect.top >= scrollRect.bottom) {
                hideButton();
                return;
            }
            const buttonHeight = button.offsetHeight || 28;
            const proposedTop = rowRect.top - hostRect.top + ((rowRect.height - buttonHeight) / 2);
            const minTop = scrollRect.top - hostRect.top + 6;
            const maxTop = scrollRect.bottom - hostRect.top - buttonHeight - 6;
            const clampedTop = Math.max(minTop, Math.min(proposedTop, maxTop));
            button.style.top = `${clampedTop}px`;
        };

        const showForRow = (row) => {
            if (!row) return;
            activeRow = row;
            const viewUrl = row.dataset.viewUrl;
            if (viewUrl) {
                button.setAttribute('href', viewUrl);
            }
            button.classList.add('is-visible');
            button.setAttribute('aria-hidden', 'false');
            updateButtonPosition();
        };

        scrollArea.addEventListener('pointermove', (event) => {
            const row = event.target.closest('tr[data-view-url]');
            if (row && scrollArea.contains(row)) {
                if (activeRow !== row) {
                    showForRow(row);
                } else {
                    updateButtonPosition();
                }
                return;
            }

            if (!button.matches(':hover')) {
                hideButton();
            }
        });

        host.addEventListener('mouseleave', () => {
            hideButton();
        });

        scrollArea.addEventListener('scroll', () => {
            if (activeRow) {
                updateButtonPosition();
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            if (activeRow) {
                updateButtonPosition();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initReservationsFloatingView();
    });
})();
</script>

@endsection
