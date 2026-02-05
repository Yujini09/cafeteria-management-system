@extends('layouts.sidebar')
@section('page-title', 'Reservations')

@section('content')

<div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0 overflow-hidden flex flex-col"
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
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                    <x-admin.ui.icon name="fa-calendar-check" size="xs" />
                    Total Reservations: {{ $reservations->total() }}
                </span>
            </div>
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
    <div class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
        <table class="modern-table table-fixed">
            <colgroup>
                <col class="w-14">
                <col class="w-64">
                <col class="w-40">
                <col class="w-64">
                <col class="w-48">
                <col class="w-48">
            </colgroup>
            <thead>
                <tr>
                    <th class="w-14">#</th>
                    <th class="whitespace-nowrap">Customer</th>
                    <th class="whitespace-nowrap">
                        <span class="inline-flex items-center gap-2">
                            <span>Status</span>
                            <x-admin.ui.icon name="fa-chevron-down" style="fas" size="sm" class="text-admin-neutral-400" />
                        </span>
                    </th>
                    <th class="hidden md:table-cell whitespace-nowrap">Email</th>
                    <th class="hidden lg:table-cell whitespace-nowrap">Created</th>
                    <th class="whitespace-nowrap">Actions</th>
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
                            'approved' => 'fa-check',
                            'declined' => 'fa-xmark',
                            default => 'fa-clock'
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
                    <tr class="hover:bg-admin-neutral-50 transition-colors duration-admin">
                        <td class="text-admin-neutral-500 font-semibold">
                            <a href="{{ route('admin.reservations.show', $r) }}" wire:navigate class="text-admin-primary font-semibold hover:text-admin-primary-light transition-colors duration-admin">
                                {{ $r->id }}
                            </a>
                        </td>
                        <td>
                            <div class="font-semibold text-admin-neutral-900">{{ $customerName }}</div>
                            <div class="text-xs text-admin-neutral-500 md:hidden">{{ $r->department ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="status-badge {{ $statusClass }} inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-wide">
                                <x-admin.ui.icon name="{{ $statusIcon }}" style="fas" size="sm" />
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="hidden md:table-cell text-admin-neutral-600">
                            <span class="max-w-[160px] truncate" title="{{ $email }}">
                                {{ $shortEmail }}
                            </span>
                        </td>
                        <td class="hidden lg:table-cell text-admin-neutral-600 whitespace-nowrap">
                            {{ $r->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.reservations.show', $r) }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-secondary text-admin-secondary-text border border-admin-neutral-200 hover:bg-admin-secondary-hover transition-all duration-admin ease-out">
                                    <x-admin.ui.icon name="fa-eye" style="fas" size="sm" />
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
                    <x-admin.ui.icon name="fa-exclamation-triangle" style="fas" size="sm" />
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

@endsection
