@extends('layouts.sidebar')
@section('page-title', 'Reservations')

@section('content')
<style>
/* Right-aligned columns */
.modern-table th:nth-child(3),
.modern-table th:nth-child(4),
.modern-table th:nth-child(5) {
    text-align: right;
}

/* Right-aligned columns */
.modern-table td:nth-child(3),
.modern-table td:nth-child(4),
.modern-table td:nth-child(5) {
    text-align: right;
}

/* Status Badges - Same size as role badges in manage users */
.status-badge {
    border-radius: 20px; /* Same as role-badge */
    text-transform: uppercase;
    letter-spacing: 0.5px; /* Same as role-badge */
    gap: 0.375rem;
    border: 1px solid transparent;
}

/* Header Styles */
.page-header {
    flex-wrap: wrap;
}

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

/* Action Buttons Container - Single line */
.action-buttons {
    display: flex;
    gap: 0.375rem;
    align-items: center;
    flex-wrap: nowrap;
    justify-content: flex-start;
}

/* Column Widths for better alignment */
.column-id {
    width: 80px;
}

.column-customer {
    width: 150px;
}

.column-status {
    width: 120px;
}

.column-email {
    width: 140px;
}

.column-date {
    width: 140px;
}

.column-actions {
    width: 200px;
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
    
    .modern-table th:nth-child(4),
    .modern-table td:nth-child(4),
    .modern-table th:nth-child(5),
    .modern-table td:nth-child(5) {
        display: none;
    }
    
    .action-buttons {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
}

@media (max-width: 640px) {
    .action-buttons {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .action-buttons a,
    .action-buttons button {
        width: 100%;
        justify-content: center;
    }
}

/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-200);
}

[x-cloak] { display: none !important; }
</style>

<div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0"
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
        <div class="relative w-full sm:w-64 md:w-72 ml-auto">
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

    <!-- Filter Section -->
    <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
        <form method="GET" action="{{ route('admin.reservations') }}" class="flex flex-col gap-4">
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
    <div class="overflow-auto max-h-96 modern-scrollbar">
        <table class="modern-table">
            <thead>
                <tr>
                    <th class="column-id">#</th>
                    <th class="column-customer">Customer</th>
                    <th class="column-status">
                        <span class="inline-flex items-center gap-2">
                            <span>Status</span>
                            <x-admin.ui.icon name="fa-chevron-down" style="fas" size="sm" class="text-admin-neutral-400" />
                        </span>
                    </th>
                    <th class="column-email hidden md:table-cell">Email</th>
                    <th class="column-date hidden lg:table-cell">Created</th>
                    <th class="column-actions">Actions</th>
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
                            default => 'Pending'
                        };
                        
                        $statusClass = match($status) {
                            'approved' => 'status-approved',
                            'declined' => 'status-declined',
                            default => 'status-pending'
                        };
                        
                        $statusIcon = match($status) {
                            'approved' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                            'declined' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                            default => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        };

                        // Get customer name from contact_person field
                        $customerName = $r->contact_person ?? optional($r->user)->name ?? '—';
                        
                        // Get email from reservation or user
                        $email = $r->email ?? optional($r->user)->email ?? '—';
                        if ($email !== '—' && strlen($email) > 15) {
                            $shortEmail = substr($email, 0, 15) . '...';
                        } else {
                            $shortEmail = $email;
                        }
                    @endphp
                    <tr>
                        <td class="column-id">
                            <a href="{{ route('admin.reservations.show', $r) }}" class="id-link">{{ $r->id }}</a>
                        </td>
                        <td class="column-customer">
                            <div class="customer-name">{{ $customerName }}</div>
                            <div class="customer-department md:hidden">{{ $r->department ?? '—' }}</div>
                        </td>
                        <td class="column-status">
                            <span class="status-badge {{ $statusClass }}">
                                {!! $statusIcon !!}
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="column-email hidden md:table-cell text-admin-neutral-600">
                            <span class="short-email" title="{{ $email }}">
                                {{ $shortEmail }}
                            </span>
                        </td>
                        <td class="column-date hidden lg:table-cell text-admin-neutral-600">
                            {{ $r->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="column-actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.reservations.show', $r) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-secondary text-admin-secondary-text border border-admin-neutral-200 hover:bg-admin-secondary-hover transition-all duration-admin ease-out">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                {{-- @if ($status === 'pending')
                                    <button type="button" 
                                            class="action-btn action-btn-approve"
                                            @click="openApproveConfirmation({{ $r->id }})">
                                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                    <button type="button" 
                                            class="action-btn action-btn-decline"
                                            @click="openDeclineConfirmation({{ $r->id }})">
                                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Decline
                                    </button>
                                @endif --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
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

    {{-- Approve Confirmation Modal --}}
    <div x-cloak x-show="approveConfirmationOpen" x-transition.opacity class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click="approveConfirmationOpen=false" class="absolute inset-0"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Approval</h3>
            <p class="text-sm text-admin-neutral-600 mb-4">Are you sure you want to approve this reservation?</p>
            <div class="flex justify-center gap-3">
                <x-admin.ui.button.secondary type="button" @click="approveConfirmationOpen=false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="button" @click="redirectToShowPage()">Yes, Approve</x-admin.ui.button.primary>
            </div>
        </div>
    </div>

    {{-- Decline Confirmation Modal --}}
    <div x-cloak x-show="declineConfirmationOpen" x-transition.opacity class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click="declineConfirmationOpen=false" class="absolute inset-0"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Decline</h3>
            <p class="text-sm text-admin-neutral-600 mb-4">Are you sure you want to decline this reservation?</p>
            <div class="flex justify-center gap-3">
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

@endsection
