@extends('layouts.sidebar')
@section('page-title', 'Reservations')

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
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
}

/* Modern Table Styles - Exact same as manage users */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem; /* Same font size */
}

.modern-table th {
    background: var(--neutral-50);
    font-weight: 600;
    color: var(--neutral-700);
    padding: 1rem; /* Same padding */
    text-align: left;
    border-bottom: 1px solid var(--neutral-200);
    font-size: 0.75rem; /* Same font size */
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
}

/* Right-aligned columns */
.modern-table th:nth-child(3),
.modern-table th:nth-child(4),
.modern-table th:nth-child(5) {
    text-align: right;
}

.modern-table td {
    padding: 1rem; /* Same padding */
    border-bottom: 1px solid var(--neutral-100);
    transition: all 0.2s ease;
}

/* Right-aligned columns */
.modern-table td:nth-child(3),
.modern-table td:nth-child(4),
.modern-table td:nth-child(5) {
    text-align: right;
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

/* Status Badges - Same size as role badges in manage users */
.status-badge {
    padding: 0.375rem 0.75rem; /* Same as role-badge */
    border-radius: 20px; /* Same as role-badge */
    font-size: 0.75rem; /* Same as role-badge */
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px; /* Same as role-badge */
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    border: 1px solid transparent;
}

.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border-color: rgba(245, 158, 11, 0.2);
}

.status-approved {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.status-declined {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

/* Action Buttons - Same as manage users */
.action-btn {
    padding: 0.5rem 0.75rem; /* Same padding */
    border-radius: 8px; /* Same radius */
    font-size: 0.75rem; /* Same font size */
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem; /* Same gap */
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
    cursor: pointer;
}

.action-btn-view {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.2);
}

.action-btn-view:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-1px);
}

.action-btn-approve {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.action-btn-approve:hover {
    background: rgba(34, 197, 94, 0.2);
    transform: translateY(-1px);
}

.action-btn-decline {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-decline:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
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

/* Empty State - Same as manage users */
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
    gap: 1rem;
    margin-bottom: 2rem; /* Same margin */
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

.header-text {
    flex: 1;
}

.header-title {
    font-size: 1.75rem; /* Same font size */
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
}

.header-subtitle {
    color: var(--neutral-500);
    font-size: 0.875rem;
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
    
    .action-btn {
        width: 100%;
        justify-content: center;
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

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);" x-data="reservationList()">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <svg class="icon-lg text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Reservations</h1>
                <p class="header-subtitle">Manage and review all reservation requests</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.reservations') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex-1">
                <label for="status" class="filter-label">Filter by Status</label>
                @php
                    $pending  = data_get($counts, 'pending', 0);
                    $approved = data_get($counts, 'approved', 0);
                    $declined = data_get($counts, 'declined', 0);
                @endphp
                <select name="status" id="status" onchange="this.form.submit()" class="filter-select w-full sm:w-64">
                    <option value="" {{ $status === null ? 'selected' : '' }}>All Reservations</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending ({{ $pending }})</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved ({{ $approved }})</option>
                    <option value="declined" {{ $status === 'declined' ? 'selected' : '' }}>Declined ({{ $declined }})</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-auto max-h-96 modern-scrollbar">
        <table class="modern-table">
            <thead>
                <tr>
                    <th class="column-id">ID</th>
                    <th class="column-customer">Customer</th>
                    <th class="column-status">Status</th>
                    <th class="column-email hidden md:table-cell">Email</th>
                    <th class="column-date hidden lg:table-cell">Created</th>
                    <th class="column-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                    @php
                        // Pull raw DB value (ignores any getStatusAttribute accessor that might force "approved")
                        $raw = $r->getRawOriginal('status');
                        $key = strtolower((string) $raw);

                        // Map supported forms (string or numeric enums)
                        $map = [
                            'pending'  => 'Pending',
                            'approved' => 'Approved',
                            'declined' => 'Declined',
                            '0' => 'Pending',
                            '1' => 'Approved',
                            '2' => 'Declined',
                        ];
                        $label = data_get($map, $key, ucfirst((string) $raw));

                        $statusClass = match ($label) {
                            'Approved' => 'status-approved',
                            'Declined' => 'status-declined',
                            default    => 'status-pending',
                        };

                        $statusIcon = match ($label) {
                            'Approved' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                            'Declined' => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                            default    => '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                        };

                        // Shorten email display - show first 15 characters + ...
                        $email = optional($r->user)->email ?? '—';
                        if ($email !== '—' && strlen($email) > 15) {
                            $shortEmail = substr($email, 0, 15) . '...';
                        } else {
                            $shortEmail = $email;
                        }
                    @endphp
                    <tr>
                        <td class="column-id">
                            <a href="{{ route('admin.reservations.show', $r) }}" class="id-link">#{{ $r->id }}</a>
                        </td>
                        <td class="column-customer">
                            <div class="customer-name">{{ optional($r->user)->name ?? '—' }}</div>
                            <div class="customer-department md:hidden">{{ optional($r->user)->department ?? '—' }}</div>
                        </td>
                        <td class="column-status">
                            <span class="status-badge {{ $statusClass }}">
                                {!! $statusIcon !!}
                                {{ $label }}
                            </span>
                        </td>
                        <td class="column-email hidden md:table-cell text-gray-600">
                            <span class="short-email" title="{{ $email }}">
                                {{ $shortEmail }}
                            </span>
                        </td>
                        <td class="column-date hidden lg:table-cell text-gray-600">
                            {{ $r->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="column-actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.reservations.show', $r) }}" class="action-btn action-btn-view">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                @if ($label === 'Pending')
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
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-gray-900 mb-2">No Reservations Found</p>
                                <p class="text-sm text-gray-500">Try adjusting your filter or check back later for new reservations</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Approve Confirmation Modal --}}
    <div x-cloak x-show="approveConfirmationOpen" x-transition class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
        <div @click="approveConfirmationOpen=false" class="relative inset-0 "></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Approval</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to approve this reservation?</p>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="approveConfirmationOpen=false">Cancel</button>
                <button type="button" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium" @click="redirectToShowPage()">Yes, Approve</button>
            </div>
        </div>
    </div>

    {{-- Decline Confirmation Modal --}}
    <div x-cloak x-show="declineConfirmationOpen" x-transition class="fixed inset-0 z-50 flex bg-black/40 items-center justify-center">
        <div @click="declineConfirmationOpen=false" class="relative inset-0 "></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Decline</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to decline this reservation?</p>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="declineConfirmationOpen=false">Cancel</button>
                <button type="button" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium" @click="redirectToShowPage()">Yes, Decline</button>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($reservations->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $reservations->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reservationList', () => ({
            approveConfirmationOpen: false,
            declineConfirmationOpen: false,
            selectedReservationId: null,
            actionType: null, // 'approve' or 'decline'
            
            openApproveConfirmation(reservationId) {
                this.selectedReservationId = reservationId;
                this.actionType = 'approve';
                this.approveConfirmationOpen = true;
            },
            
            openDeclineConfirmation(reservationId) {
                this.selectedReservationId = reservationId;
                this.actionType = 'decline';
                this.declineConfirmationOpen = true;
            },
            
            redirectToShowPage() {
                if (this.selectedReservationId) {
                    // Close both modals
                    this.approveConfirmationOpen = false;
                    this.declineConfirmationOpen = false;
                    
                    // Redirect to the reservation show page
                    const url = "{{ route('admin.reservations.show', ':id') }}".replace(':id', this.selectedReservationId);
                    
                    // Add hash for decline action if needed
                    if (this.actionType === 'decline') {
                        window.location.href = url + '#decline';
                    } else {
                        window.location.href = url;
                    }
                }
            }
        }));
    });
</script>
@endsection