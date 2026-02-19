@extends('layouts.sidebar')
@section('page-title', 'Payments')

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

.status-badge {
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    gap: 0.375rem;
    border: 1px solid transparent;
}

.status-submitted {
    background: #fef3c7;
    color: #92400e;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

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

.filter-section {
    background: var(--neutral-50);
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
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

.customer-name {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
}

.customer-department {
    color: var(--neutral-500);
    font-size: 0.75rem;
    margin-top: 0.125rem;
}

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

.column-id { width: 110px; }
.column-customer { width: 160px; }
.column-reference { width: 160px; }
.column-amount { width: 130px; }
.column-status { width: 120px; }
.column-date { width: 140px; }

.column-amount,
.column-amount .amount-cell {
    text-align: right;
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

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-content {
        width: 100%;
    }

}
</style>

<div class="modern-card admin-page-shell p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-file-invoice-dollar" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Payments</h1>
                <p class="header-subtitle">Review and verify payment submissions</p>
            </div>
        </div>
        <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
            <div class="relative w-full sm:w-64 md:w-72">
                <input type="search"
                       id="searchInput"
                       placeholder="Search payments..."
                       class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                       oninput="filterTable(this.value)"
                       aria-label="Search payments">
                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: none;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col gap-4">
            <div class="filter-row w-full">
                <label for="status" class="filter-label filter-label-inline">Filter by Status</label>
                <div class="select-with-arrows w-full sm:w-64">
                    <select name="status" id="status" onchange="this.form.submit()" class="filter-select w-full" data-admin-select="true">
                        <option value="" {{ $status === null ? 'selected' : '' }}>All Payments</option>
                        <option value="submitted" {{ $status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <span class="select-arrows" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </form>
    </div>

    <div id="paymentsTableHost" class="table-view-overlay-host">
        <div id="paymentsTableScroll" data-table-scroll class="overflow-auto max-h-96 modern-scrollbar">
            <table class="modern-table">
            <thead>
                <tr>
                    <th class="column-id">Reservation</th>
                    <th class="column-customer">Customer</th>
                    <th class="column-reference column-reference">Reference</th>
                    <th class="column-amount">Amount</th>
                    <th class="column-status">Status</th>
                    <th class="column-date">Submitted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    @php
                        $statusValue = strtolower($payment->status ?? 'submitted');
                        $statusLabel = match($statusValue) {
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            default => 'Submitted'
                        };

                        $statusClass = match($statusValue) {
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            default => 'status-submitted'
                        };

                        $statusIcon = match($statusValue) {
                            'approved' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                            'rejected' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                            default => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path></svg>'
                        };
                    @endphp
                    <tr class="payment-row hover:bg-admin-neutral-50 transition-colors duration-admin" data-view-url="{{ route('admin.payments.show', $payment) }}">
                        <td class="column-id">
                            <a href="{{ route('admin.reservations.show', $payment->reservation_id) }}" class="id-link">#{{ $payment->reservation_id }}</a>
                        </td>
                        <td class="column-customer">
                            <div class="customer-name">{{ $payment->user?->name ?? 'â€”' }}</div>
                            <div class="customer-department md:hidden">{{ $payment->department_office ?? 'â€”' }}</div>
                        </td>
                        <td class="column-reference text-gray-600">
                            {{ $payment->reference_number }}
                        </td>
                        <td class="column-amount">
                            <span class="amount-cell font-semibold text-gray-900">PHP {{ number_format($payment->amount, 2) }}</span>
                        </td>
                        <td class="column-status">
                            <span class="status-badge {{ $statusClass }}">
                                {!! $statusIcon !!}
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="column-date text-gray-600">{{ $payment->created_at?->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
        <a id="paymentsFloatingViewBtn"
           href="#"
           class="table-floating-view-btn"
           aria-label="View payment"
           aria-hidden="true">
            <svg class="icon-sm text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            View
        </a>
    </div>

    @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links('components.pagination') }}
        </div>
    @endif
</div>

<script>
(() => {
    function initPaymentsFloatingView() {
        const host = document.getElementById('paymentsTableHost');
        const scrollArea = document.getElementById('paymentsTableScroll');
        const button = document.getElementById('paymentsFloatingViewBtn');
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

    document.addEventListener('DOMContentLoaded', initPaymentsFloatingView);
    document.addEventListener('livewire:navigated', initPaymentsFloatingView);
})();
</script>
@endsection
