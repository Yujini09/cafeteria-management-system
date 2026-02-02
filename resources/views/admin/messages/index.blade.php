@extends('layouts.sidebar')
@section('page-title', 'Messages')

@section('content')
<style>
/* Modern Design Variables - Matched from Reservations */
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

/* Status Badges */
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    border: 1px solid transparent;
}

.status-new {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.status-read {
    background: rgba(115, 115, 115, 0.1);
    color: #737373;
    border-color: rgba(115, 115, 115, 0.2);
}

/* Action Buttons */
.action-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
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

.action-btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-delete:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
}

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    gap: 1rem;
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
}

.header-subtitle {
    color: var(--neutral-500);
    font-size: 0.875rem;
}

/* Filter/Info Section */
.info-section {
    background: var(--neutral-50);
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

/* Sender Info Styles */
.sender-name {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
}

.sender-email {
    color: var(--neutral-500);
    font-size: 0.75rem;
    margin-top: 0.125rem;
}

/* Message Snippet */
.message-snippet {
    color: var(--neutral-600);
    font-size: 0.875rem;
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-unread {
    font-weight: 600;
    color: var(--neutral-900);
}

/* Icon Sizes */
.icon-sm { width: 14px; height: 14px; }
.icon-lg { width: 20px; height: 20px; }

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

/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-200);
}

[x-cloak] { display: none !important; }

/* Responsive */
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; }
    .modern-table th:nth-child(4), .modern-table td:nth-child(4) { display: none; }
}
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);" x-data="messageList()">
    
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <svg class="icon-lg text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Customer Messages</h1>
                <p class="header-subtitle">View and manage customer inquiries</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-700">Inbox Status:</span>
            @php
                $unreadCount = $messages->where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200">
                    {{ $unreadCount }} New Message{{ $unreadCount > 1 ? 's' : '' }}
                </span>
            @else
                <span class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full border border-gray-200">
                    All caught up
                </span>
            @endif
        </div>
    </div>

    <div class="overflow-auto max-h-96 modern-scrollbar">
        <table class="modern-table">
            <thead>
                <tr>
                    <th width="120">Status</th>
                    <th>Sender</th>
                    <th>Message Snippet</th>
                    <th width="150" class="hidden md:table-cell">Date</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                <tr>
                    <td>
                        @if(!$msg->is_read)
                            <span class="status-badge status-new">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New
                            </span>
                        @else
                            <span class="status-badge status-read">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Read
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="sender-name">{{ $msg->name }}</div>
                        <div class="sender-email">{{ $msg->email }}</div>
                    </td>
                    <td>
                        <div class="message-snippet {{ !$msg->is_read ? 'message-unread' : '' }}">
                            {{ Str::limit($msg->message, 50) }}
                        </div>
                    </td>
                    <td class="hidden md:table-cell text-gray-600">
                        {{ $msg->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.messages.show', $msg->id) }}" class="action-btn action-btn-view">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </a>
                            
                            <button type="button" 
                                    class="action-btn action-btn-delete"
                                    @click="openDeleteConfirmation({{ $msg->id }})">
                                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-lg font-semibold text-gray-900 mb-2">No Messages Found</p>
                            <p class="text-sm text-gray-500">Your inbox is currently empty.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($messages->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $messages->links() }}
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    <div x-cloak x-show="deleteConfirmationOpen" x-transition class="fixed inset-0 z-50 flex bg-black/40 items-center justify-center">
        <div @click="deleteConfirmationOpen=false" class="relative inset-0"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Delete Message</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to delete this message? This action cannot be undone.</p>
            
            <form id="delete-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="deleteConfirmationOpen=false">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('messageList', () => ({
            deleteConfirmationOpen: false,
            selectedMessageId: null,
            
            openDeleteConfirmation(id) {
                this.selectedMessageId = id;
                
                // ROBUST URL GENERATION:
                // 1. Generate a valid route with a dummy ID (e.g., 999999)
                let url = "{{ route('admin.messages.delete', 999999) }}";
                
                // 2. Replace the dummy ID with the actual message ID
                url = url.replace('999999', id);
                
                // 3. Set the form action
                document.getElementById('delete-form').action = url;
                
                this.deleteConfirmationOpen = true;
            }
        }));
    });
</script>
@endsection