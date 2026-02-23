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
.column-customer { width: 150px; }
.column-department { width: 180px; }
.column-status { width: 144px; min-width: 144px; }
.column-payment { width: 168px; }
.column-email { width: 140px; }
.column-date { width: 140px; }

/* Responsive Design */
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; }
    .header-content { width: 100%; }
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
        <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
            <div class="relative w-full sm:w-64 md:w-72">
                <input type="text"
                       inputmode="search"
                       autocomplete="off"
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

    <div id="reservationsTableHost" class="table-view-overlay-host">
        <div id="reservationsTableScroll" data-table-scroll class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
            <table class="modern-table table-fixed w-full">
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
                    <th class="column-id text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="column-customer text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="column-department text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Office/Dept</th>
                    <th class="column-status text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="column-payment text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="column-email text-left px-4 py-3 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
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
                        <td class="px-4 py-4 text-sm text-gray-600">
                            <span class="short-email" title="{{ $email }}">{{ $shortEmail }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-admin-neutral-600 whitespace-nowrap">
                            {{ $r->created_at->format('M d, Y H:i') }}
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
    function filterTable(query) {
        const lowerQuery = query.toLowerCase();
        const rows = document.querySelectorAll('.reservation-row');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(lowerQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        const clearBtn = document.getElementById('clearSearch');
        if(clearBtn) {
            clearBtn.style.display = query.length > 0 ? 'block' : 'none';
        }
    }

    // Bind global function so HTML input can trigger it
    window.filterTable = filterTable;

    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    
    if(clearSearch && searchInput) {
        clearSearch.addEventListener('click', () => {
            searchInput.value = '';
            filterTable('');
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

    document.addEventListener('DOMContentLoaded', initReservationsFloatingView);
})();
</script>

@endsection