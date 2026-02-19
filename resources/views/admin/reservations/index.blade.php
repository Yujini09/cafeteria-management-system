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

/* Status Badges - Same size as role badges in manage users */
.status-badge {
    border-radius: 20px; /* Same as role-badge */
    text-transform: uppercase;
    letter-spacing: 0.5px; /* Same as role-badge */
    gap: 0.375rem;
    border: 1px solid transparent;
}

.payment-pending {
    background: #fef3c7;
    color: #92400e;
}

.payment-under-review {
    background: #dbeafe;
    color: #1e40af;
}

.payment-paid {
    background: #dcfce7;
    color: #166534;
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

/* Filter Section */
.filter-section {
    background: var(--neutral-50);
    padding: 1.25rem; /* Adjusted padding */
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem; /* Same margin */
}

.filter-label {
    font-weight: 600;
    color: var(--neutral-700);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.filter-label-inline {
    margin-bottom: 0;
}

.filter-row {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 640px) {
    .filter-row {
        flex-direction: row;
        align-items: center;
    }
}

.select-with-arrows {
    position: relative;
    display: inline-flex;
    align-items: center;
    min-width: 0;
}

.select-with-arrows .filter-select {
    appearance: none;
    padding-right: 2.75rem;
}

.select-arrows {
    position: absolute;
    right: 0.75rem;
    pointer-events: none;
    color: var(--neutral-500);
}

.select-arrows svg {
    width: 16px;
    height: 16px;
}

/* Created sort dropdown */

/* Customer Info - Same font sizes as manage users */
.customer-name {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem; /* Same as table font */
}

.customer-department {
    color: var(--neutral-500);
    font-size: 0.75rem; /* Smaller like in manage users */
    margin-top: 0.125rem;
}

/* ID Link */
.id-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
    font-size: 0.875rem; /* Same as table font */
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

/* Icon Sizes - Same as manage users */
.icon-sm {
    width: 14px; /* Same as w-3.5 (14px) */
    height: 14px;
}

.icon-md {
    width: 16px; /* Same as w-4 (16px) */
    height: 16px;
}

.icon-lg {
    width: 20px; /* Same as in header */
    height: 20px;
}

/* Column Widths for better alignment */
.column-id {
    width: 80px;
}

.column-customer {
    width: 150px;
}

.column-department {
    width: 180px;
}

.column-status {
    width: 144px;
    min-width: 144px;
}

.column-payment {
    width: 168px;
}

.column-email {
    width: 140px;
}

.column-date {
    width: 140px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-content {
        width: 100%;
    }
    
}

/* Menu Card Styling for Inventory Sections */



/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-200);
}

[x-cloak] { display: none !important; }
</style>

<div class="modern-card admin-page-shell p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0"
     x-data="reservationList()"
     x-effect="document.body.classList.toggle('overflow-hidden', approveConfirmationOpen || declineConfirmationOpen)"
     @keydown.escape.window="approveConfirmationOpen = false; declineConfirmationOpen = false">
    <!-- Header -->
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
        <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
            <div class="relative w-full sm:w-64 md:w-72">
                <input type="search"
                       id="searchInput"
                       placeholder="Search reservations..."
                       class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                       oninput="filterTable(this.value)"
                       aria-label="Search reservations">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600" style="display: none;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="mb-4">
        <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
            <x-admin.ui.icon name="fa-calendar-check" size="xs" />
            Total Reservations: {{ $reservations->total() }}
        </span>
    </div>

    <!-- Filter Section -->
    <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
        <form method="GET" action="{{ route('admin.reservations') }}" class="flex flex-col gap-4">
            <input type="hidden" name="created_sort" value="{{ $createdSort }}">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label for="status" class="text-sm font-semibold text-admin-neutral-700">Filter by Status</label>
                @php
                    $pending  = data_get($counts, 'pending', 0);
                    $approved = data_get($counts, 'approved', 0);
                    $declined = data_get($counts, 'declined', 0);
                @endphp
                <div class="w-full sm:w-64">
                    <select name="status" id="status" onchange="this.form.submit()" class="admin-select w-full" data-admin-select="true">
                        <option value="" {{ $status === null ? 'selected' : '' }}>All Reservations</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending ({{ $pending }})</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved ({{ $approved }})</option>
                        <option value="declined" {{ $status === 'declined' ? 'selected' : '' }}>Declined ({{ $declined }})</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div id="reservationsTableHost" class="table-view-overlay-host">
        <div id="reservationsTableScroll" data-table-scroll class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
            <table class="modern-table table-fixed">
            <colgroup>
                <col class="w-14">
                <col class="w-64">
                <col class="w-56">
                <col class="w-36">
                <col class="w-44">
                <col class="w-48">
                <col class="w-48">
            </colgroup>
            <thead>
                <tr>
                    <th class="column-id text-left">ID</th>
                    <th class="column-customer text-left">Customer</th>
                    <th class="column-department text-left">Office/Department</th>
                    <th class="column-status text-left">Status</th>
                    <th class="column-payment text-left">Payment</th>
                    <th class="column-email text-left">Email</th>
                    <th class="column-date text-left">
                        @php
                            $nextCreatedSort = $createdSort === 'asc' ? 'desc' : 'asc';
                            $createdSortIcon = $createdSort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down';
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['created_sort' => $nextCreatedSort, 'page' => null]) }}"
                           class="group inline-flex items-center gap-2"
                           aria-label="Sort by created date">
                            <span>Created</span>
                            <x-admin.ui.icon name="{{ $createdSortIcon }}" style="fas" size="sm" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- In the table body section of admin/reservations/index.blade.php --}}
                @forelse($reservations as $r)
                    @php
                        $status = strtolower($r->status);
                        $statusLabel = match($status) {
                            'approved' => 'Approved',
                            'declined' => 'Declined',
                            'cancelled' => 'Cancelled', // <--- ADDED THIS
                            default => 'Pending'
                        };
                        
                        $statusClass = match($status) {
                            'approved' => 'bg-green-100 text-green-700',
                            'declined' => 'bg-red-100 text-red-700',
                            'cancelled' => 'bg-gray-100 text-gray-700', // <--- ADDED THIS
                            default => 'bg-yellow-100 text-yellow-700'
                        };
                        
                        $statusIcon = match($status) {
                            'approved' => 'fa-check',
                            'declined' => 'fa-xmark',
                            'cancelled' => 'fa-ban', // <--- ADDED THIS
                            default => 'fa-clock'
                        };

                        // Get customer name from contact_person field
                        $customerName = $r->contact_person ?? optional($r->user)->name ?? '—';
                        
                        $department = $r->department ?? optional($r->user)->department ?? 'N/A';
                        if ($department !== 'N/A' && strlen($department) > 15) {
                            $shortDepartment = substr($department, 0, 15) . '...';
                        } else {
                            $shortDepartment = $department;
                        }

                        // Get email from reservation or user
                        $email = $r->email ?? optional($r->user)->email ?? '—';
                        if ($email !== '—' && strlen($email) > 15) {
                            $shortEmail = substr($email, 0, 15) . '...';
                        } else {
                            $shortEmail = $email;
                        }
                    @endphp
                    <tr class="reservation-row hover:bg-admin-neutral-50 transition-colors duration-admin" data-view-url="{{ route('admin.reservations.show', $r) }}">
                        <td class="text-admin-neutral-500 font-semibold">
                            <a href="{{ route('admin.reservations.show', $r) }}" wire:navigate class="text-admin-primary font-semibold hover:text-admin-primary-light transition-colors duration-admin">
                                {{ $r->id }}
                            </a>
                        </td>
                        <td>
                            <div class="font-semibold text-admin-neutral-900">{{ $customerName }}</div>
                        </td>
                        <td class="column-department text-admin-neutral-600">
                            <span class="short-email" title="{{ $department }}">
                                {{ $shortDepartment }}
                            </span>
                        </td>
                        <td class="column-status">
                            <span class="status-badge {{ $statusClass }} inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-wide">
                                <x-admin.ui.icon name="{{ $statusIcon }}" style="fas" size="sm" />
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="column-payment">
                            @php
                                $paymentStatus = $r->payment_status ?? 'pending';
                                $paymentClass = match($paymentStatus) {
                                    'paid' => 'payment-paid',
                                    'under_review' => 'payment-under-review',
                                    default => 'payment-pending'
                                };
                                $paymentLabel = $paymentStatus === 'under_review' ? 'Under Review' : ucfirst($paymentStatus);
                            @endphp
                            <span class="status-badge {{ $paymentClass }}">{{ $paymentLabel }}</span>
                        </td>
                        <td class="column-email text-gray-600">
                            <span class="short-email" title="{{ $email }}">
                                {{ $shortEmail }}
                            </span>
                        </td>
                        <td class="column-date text-admin-neutral-600 whitespace-nowrap">{{ $r->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-admin-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No Reservations Found</p>
                                <p class="text-sm text-admin-neutral-500">Try adjusting your filter or check back later for new reservations</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <a id="reservationsFloatingViewBtn"
           href="#"
           wire:navigate
           class="table-floating-view-btn"
           aria-label="View reservation"
           aria-hidden="true">
            <x-admin.ui.icon name="fa-eye" style="fas" size="sm" class="text-white" />
            View
        </a>
    </div>

    {{-- Approve Confirmation Modal --}}
    <div x-cloak x-show="approveConfirmationOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="approveConfirmationOpen=false" class="absolute inset-0 bg-admin-neutral-900/50 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-sm rounded-admin-lg bg-white shadow-admin-modal border border-admin-neutral-200"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center gap-3 px-6 py-4 border-b border-admin-neutral-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-admin bg-admin-primary-light text-admin-primary">
                    <x-admin.ui.icon name="fa-check" style="fas" size="sm" />
                </span>
                <h3 class="text-lg font-semibold text-admin-neutral-900">Confirm Approval</h3>
            </div>
            <div class="px-6 py-4 text-sm text-admin-neutral-600">
                Are you sure you want to approve this reservation?
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                <x-admin.ui.button.secondary type="button" @click="approveConfirmationOpen=false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="button" @click="redirectToShowPage()">Yes, Approve</x-admin.ui.button.primary>
            </div>
        </div>
    </div>

    {{-- Decline Confirmation Modal --}}
    <div x-cloak x-show="declineConfirmationOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="declineConfirmationOpen=false" class="absolute inset-0 bg-admin-neutral-900/50 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-sm rounded-admin-lg bg-white shadow-admin-modal border border-admin-neutral-200"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center gap-3 px-6 py-4 border-b border-admin-neutral-100">
                <span class="flex h-10 w-10 items-center justify-center rounded-admin bg-admin-danger-light text-admin-danger">
                    <x-admin.ui.icon name="fa-triangle-exclamation" style="fas" size="sm" />
                </span>
                <h3 class="text-lg font-semibold text-admin-neutral-900">Confirm Decline</h3>
            </div>
            <div class="px-6 py-4 text-sm text-admin-neutral-600">
                Are you sure you want to decline this reservation?
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                <x-admin.ui.button.secondary type="button" @click="declineConfirmationOpen=false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.danger type="button" @click="redirectToShowPage()">Yes, Decline</x-admin.ui.button.danger>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($reservations->hasPages())
        <div class="mt-6">
            {{ $reservations->links('components.pagination') }}
        </div>
    @endif
</div>

<script>
(() => {
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

        scrollArea.addEventListener('focusin', (event) => {
            const row = event.target.closest('tr[data-view-url]');
            if (row) {
                showForRow(row);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initReservationsFloatingView);
    document.addEventListener('livewire:navigated', initReservationsFloatingView);
})();
</script>

@endsection
