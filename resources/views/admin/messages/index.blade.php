@extends('layouts.sidebar')
@section('page-title', 'Messages')

@section('content')
<div x-data="messageList()" x-effect="document.body.classList.toggle('overflow-hidden', deleteConfirmationOpen)">
    
    {{-- Success/Error Modals --}}
    <x-success-modal name="messages-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">{{ session('message_success') }}</p>
    </x-success-modal>
    
    <x-admin.ui.modal name="messages-error" title="Error" variant="error" maxWidth="sm">
        <p class="text-sm text-admin-neutral-700">{{ session('message_error') }}</p>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'messages-error')">
                Close
            </x-admin.ui.button.secondary>
        </x-slot>
    </x-admin.ui.modal>

    <div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full">
        
        <div class="page-header items-start">
            <div class="header-content">
                <div class="header-icon flex items-center justify-center">
                    {{-- FIXED: Replaced component with direct icon --}}
                    <i class="fas fa-envelope text-white text-lg"></i>
                </div>
                <div class="header-text">
                    <h1 class="header-title">Customer Messages</h1>
                    <p class="header-subtitle">View and manage customer inquiries</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 w-full md:w-auto items-end">
                <div class="relative w-full sm:w-64 md:w-72">
                    <input type="search"
                           id="searchInput"
                           placeholder="Search messages..."
                           class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                           oninput="filterTable(this.value)"
                           aria-label="Search messages">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600" style="display: none;">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
                    <x-admin.ui.button.secondary type="button" onclick="location.reload()">
                        Refresh
                    </x-admin.ui.button.secondary>
                </div>
            </div>
        </div>

        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-admin-neutral-700">Inbox Status:</span>
                @php
                    $unreadCount = $messages->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="inline-flex items-center gap-2 rounded-full border border-admin-success bg-admin-success-light px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-success">
                        {{-- FIXED: Replaced component with direct icon --}}
                        <i class="fas fa-envelope text-xs"></i>
                        {{ $unreadCount }} New Message{{ $unreadCount > 1 ? 's' : '' }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-100 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                        {{-- FIXED: Replaced component with direct icon --}}
                        <i class="fas fa-check text-xs"></i>
                        All caught up
                    </span>
                @endif
            </div>
        </div>

        <div class="overflow-auto max-h-96 modern-scrollbar">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="w-14">#</th>
                        <th>Status</th>
                        <th>Sender</th>
                        <th>Message</th>
                        <th class="hidden md:table-cell">Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $msg)
                        <tr>
                            <td class="text-admin-neutral-500 font-semibold">
                                {{ ($messages->firstItem() ?? 0) + $loop->index }}
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold uppercase {{ $msg->is_read ? 'bg-admin-neutral-100 text-admin-neutral-600 border-admin-neutral-200' : 'bg-admin-success-light text-admin-success border-admin-success' }}">
                                    {{-- FIXED: Dynamic Icon Logic --}}
                                    <i class="fas {{ $msg->is_read ? 'fa-check' : 'fa-envelope' }} text-xs"></i>
                                    {{ $msg->is_read ? 'Read' : 'New' }}
                                </span>
                            </td>
                            <td>
                                <div class="font-semibold text-admin-neutral-900">{{ $msg->name }}</div>
                                <div class="text-xs text-admin-neutral-600">{{ $msg->email }}</div>
                            </td>
                            <td class="{{ !$msg->is_read ? 'font-semibold text-admin-neutral-900' : 'text-admin-neutral-600' }}">
                                {{ Str::limit($msg->message, 60) }}
                            </td>
                            <td class="hidden md:table-cell text-admin-neutral-600">
                                {{ $msg->created_at->diffForHumans() }}
                            </td>
                            <td>
                                <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                    <x-admin.ui.button.secondary
                                        type="button"
                                        class="!py-2 !px-3 text-xs flex items-center gap-1"
                                        onclick="window.location='{{ route('admin.messages.show', $msg->id) }}'">
                                        {{-- FIXED: Icon --}}
                                        <i class="fas fa-file-alt text-xs"></i>
                                        View
                                    </x-admin.ui.button.secondary>
                                    
                                    <x-admin.ui.button.danger
                                        type="button"
                                        class="!py-2 !px-3 text-xs flex items-center gap-1"
                                        @click="openDeleteConfirmation({{ $msg->id }})">
                                        {{-- FIXED: Icon --}}
                                        <i class="fas fa-trash-alt text-xs"></i>
                                        Delete
                                    </x-admin.ui.button.danger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon flex items-center justify-center">
                                        {{-- FIXED: Empty state icon --}}
                                        <i class="fas fa-envelope text-admin-neutral-400 text-3xl"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No messages found</p>
                                    <p class="text-sm text-admin-neutral-500">Your inbox is currently empty.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="mt-6">
                {{ $messages->links('components.pagination') }}
            </div>
        @endif
    </div>

    <div x-show="deleteConfirmationOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div
            x-show="deleteConfirmationOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-red-950/40 backdrop-blur-sm"
            @click="deleteConfirmationOpen = false; selectedMessageId = null"
            aria-hidden="true"
        ></div>

        <div
            x-show="deleteConfirmationOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm overflow-hidden rounded-2xl border border-red-200 bg-white shadow-2xl"
            @click.stop
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-message-title"
            aria-describedby="delete-message-desc"
        >
            <div class="flex items-start justify-between gap-4 border-b border-red-100 bg-red-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </span>
                    <div>
                        <h2 id="delete-message-title" class="text-lg font-semibold text-red-900">Confirm Deletion</h2>
                        <p class="text-xs text-red-700">This action cannot be undone.</p>
                    </div>
                </div>
                <button @click="deleteConfirmationOpen = false; selectedMessageId = null"
                        class="rounded-full p-1 text-red-600 hover:text-red-700" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="delete-message-desc" class="px-6 py-5 text-sm text-red-700">
                Are you sure you want to delete this message? It will be removed from the inbox.
            </div>

            <form id="delete-form" method="POST" action=""
                  data-delete-template="{{ route('admin.messages.delete', 999999) }}"
                  class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-red-100 bg-red-50/60">
                @csrf
                @method('DELETE')
                <x-admin.ui.button.secondary type="button" @click="deleteConfirmationOpen = false; selectedMessageId = null">
                    Cancel
                </x-admin.ui.button.secondary>
                <x-admin.ui.button.danger type="submit">
                    Delete
                </x-admin.ui.button.danger>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        const hasSuccess = @json((bool) session('message_success'));
        const hasError = @json((bool) session('message_error'));

        if (hasSuccess) {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'messages-success' }));
        }

        if (hasError) {
            window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'messages-error' }));
        }
    });
</script>
@endsection
