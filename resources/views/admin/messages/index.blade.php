@extends('layouts.sidebar')
@section('page-title', 'Inbox')

@section('content')
<div x-data="inboxSystem()" x-init="init()" x-effect="document.body.classList.toggle('overflow-hidden', viewOpen || replyOpen || deleteOpen)" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto">
    
    {{-- Header Section --}}
    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-inbox" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Customer Inbox</h1>
                <p class="header-subtitle">Manage inquiries from the contact form.</p>
            </div>
        </div>

        {{-- Dynamic Stats Container --}}
        <div id="inbox-stats" class="header-actions w-full md:w-auto flex flex-wrap items-center justify-end gap-3">
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700">
                    <x-admin.ui.icon name="fa-envelope-open" size="xs" />
                    Unread: {{ $unreadCount }}
                </span>
            @endif
            <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                <x-admin.ui.icon name="fa-envelope" size="xs" />
                Total Messages: {{ $messages->total() }}
            </span>
        </div>
    </div>

    {{-- Filter & Sort Bar --}}
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-admin-neutral-400"></i>
            <input 
                type="text" 
                x-model="search" 
                placeholder="Search by name, email, or message..." 
                class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 pl-10 pr-10 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
            >
            <div x-show="isLoading" class="absolute right-4 top-1/2 -translate-y-1/2" style="display: none;">
                <i class="fas fa-circle-notch fa-spin text-admin-primary"></i>
            </div>
        </div>

        <div class="w-full sm:w-48 relative">
            <select x-model="sort" 
                class="w-full rounded-admin border border-admin-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary appearance-none">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="unread">Unread First</option>
            </select>
            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-admin-neutral-400 pointer-events-none"></i>
        </div>
    </div>

    {{-- Message List Container (Target for AJAX Replacement) --}}
    <div id="message-list-container" class="bg-white rounded-admin shadow-sm border border-admin-neutral-200 overflow-hidden relative">
        <div x-show="isLoading" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 transition-opacity duration-200" style="display: none;"></div>

        @forelse($messages as $msg)
            <div class="px-6 py-5 border-b border-admin-neutral-100 hover:bg-admin-neutral-50/80 transition-colors duration-admin group {{ $msg->status === 'UNREAD' ? 'bg-admin-primary-light/40' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        {{-- Row 1: Name + Status + Date --}}
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <h3 class="text-base font-semibold text-admin-neutral-900">{{ $msg->name }}</h3>
                            
                            {{-- STATUS BADGES --}}
                            @if($msg->status === 'UNREAD')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide border border-admin-primary/20 bg-admin-primary-light text-admin-primary">
                                    <span class="w-1.5 h-1.5 rounded-full bg-admin-primary"></span> New
                                </span>
                            @elseif($msg->status === 'REPLIED')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase bg-blue-50 text-blue-700 border border-blue-200 tracking-wide">
                                    <x-admin.ui.icon name="fa-reply" size="xs" /> Replied
                                </span>
                            @endif

                            <span class="text-xs text-admin-neutral-400 font-medium flex items-center gap-1.5 ml-auto sm:ml-0">
                                {{ $msg->created_at->format('M d, Y \a\t g:i A') }}
                            </span>
                        </div>

                        {{-- Row 2: Email --}}
                        <div class="text-sm font-medium text-admin-primary mb-2">{{ $msg->email }}</div>

                        {{-- Row 3: Message Preview --}}
                        <p class="text-admin-neutral-600 text-sm line-clamp-1 {{ $msg->status === 'UNREAD' ? 'font-medium' : '' }}">
                            {{ $msg->message }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pl-4">
                        <x-admin.ui.button.icon type="button" variant="secondary"
                            @click="openView({{ $msg->id }})"
                            title="View message"
                            class="text-admin-primary hover:bg-admin-primary-light hover:text-admin-primary">
                            <x-admin.ui.icon name="fa-eye" size="sm" />
                        </x-admin.ui.button.icon>

                        <x-admin.ui.button.icon type="button" variant="secondary"
                            @click="openReply({{ json_encode($msg) }})"
                            title="Reply to message"
                            class="text-blue-700 hover:bg-blue-50 hover:text-blue-700">
                            <x-admin.ui.icon name="fa-reply" size="sm" />
                        </x-admin.ui.button.icon>

                        <x-admin.ui.button.icon type="button" variant="danger"
                            @click="openDelete($event.currentTarget.dataset.messageId, $event.currentTarget.dataset.messageName)"
                            data-message-id="{{ $msg->id }}"
                            data-message-name="{{ $msg->name ?: 'this message' }}"
                            title="Delete message">
                            <x-admin.ui.icon name="fa-trash-alt" size="sm" />
                        </x-admin.ui.button.icon>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-admin-neutral-100 rounded-full flex items-center justify-center mx-auto mb-4 text-admin-neutral-400">
                    <x-admin.ui.icon name="fa-inbox" size="lg" />
                </div>
                <h3 class="text-lg font-semibold text-admin-neutral-900">No messages found</h3>
                <p class="text-sm text-admin-neutral-500 mt-1">Your inbox is empty or no messages match your search.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Container --}}
    <div id="pagination-container" class="mt-6">
        {{ $messages->appends(request()->query())->links() }}
    </div>

    <x-success-modal name="message-reply-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Reply sent successfully.</p>
    </x-success-modal>

    {{-- VIEW MODAL --}}
    <div x-show="viewOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="closeView()" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-lg bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 overflow-hidden"
             x-transition.scale.90
             @click.stop>
            <div class="px-6 py-4 border-b border-admin-neutral-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin bg-admin-primary-light text-admin-primary">
                        <x-admin.ui.icon name="fa-envelope-open-text" size="sm" />
                    </span>
                    <h3 class="text-lg font-semibold text-admin-neutral-900">Message Details</h3>
                </div>
                <button type="button" @click="closeView()" class="inline-flex h-9 w-9 items-center justify-center rounded-admin text-admin-neutral-500 hover:bg-admin-neutral-100 hover:text-admin-neutral-700 transition-colors duration-admin">
                    <x-admin.ui.icon name="fa-times" size="sm" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-admin-primary-light text-admin-primary flex items-center justify-center text-xl font-bold">
                        <span x-text="activeMsg.name ? activeMsg.name.charAt(0).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-admin-neutral-900 text-lg" x-text="activeMsg.name"></h4>
                        <p class="text-admin-primary text-sm font-medium" x-text="activeMsg.email"></p>
                    </div>
                </div>
                <div class="bg-admin-neutral-50 p-5 rounded-admin border border-admin-neutral-200">
                    <p class="text-sm text-admin-neutral-700 leading-relaxed whitespace-pre-wrap" x-text="activeMsg.message"></p>
                </div>
                <div class="text-xs text-admin-neutral-500 text-right">
                    Sent on <span x-text="formatDate(activeMsg.created_at)"></span>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                <x-admin.ui.button.secondary type="button" @click="closeView()">Close</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="button" @click="viewOpen = false; openReply(activeMsg)">
                    <x-admin.ui.icon name="fa-reply" size="sm" />
                    Reply
                </x-admin.ui.button.primary>
            </div>
        </div>
    </div>

    {{-- REPLY MODAL --}}
    <div x-show="replyOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="replyOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative z-10 w-full max-w-xl bg-white rounded-admin-lg shadow-admin-modal border border-admin-neutral-200 overflow-hidden"
             x-transition.scale.90
             @click.stop>
            <div class="px-6 py-4 border-b border-admin-neutral-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin bg-blue-50 text-blue-700">
                        <x-admin.ui.icon name="fa-paper-plane" size="sm" />
                    </span>
                    <h3 class="text-lg font-semibold text-admin-neutral-900">Send Reply</h3>
                </div>
                <button type="button" @click="replyOpen = false" class="inline-flex h-9 w-9 items-center justify-center rounded-admin text-admin-neutral-500 hover:bg-admin-neutral-100 hover:text-admin-neutral-700 transition-colors duration-admin">
                    <x-admin.ui.icon name="fa-times" size="sm" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-admin-neutral-700 mb-2">To</label>
                    <div class="px-4 py-2.5 bg-admin-neutral-50 rounded-admin border border-admin-neutral-200 text-admin-neutral-700 text-sm font-medium" x-text="activeMsg.email"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-admin-neutral-700 mb-2">Subject</label>
                    <input type="text"
                           x-model="replySubject"
                           class="w-full rounded-admin border border-admin-neutral-300 px-admin-input py-2.5 text-sm text-admin-neutral-700 focus:outline-none focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-admin-neutral-700 mb-2">Message</label>
                    <textarea x-model="replyBody"
                              rows="6"
                              class="w-full rounded-admin border border-admin-neutral-300 px-admin-input py-2.5 text-sm text-admin-neutral-700 resize-none focus:outline-none focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                              placeholder="Type your response here..."></textarea>
                </div>
            </div>

            <div class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                <x-admin.ui.button.secondary type="button" @click="replyOpen = false">Cancel</x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="button" @click="sendReply()" x-bind:disabled="isSending">
                    <span x-show="!isSending" class="inline-flex items-center gap-2">
                        <x-admin.ui.icon name="fa-paper-plane" size="sm" />
                        Send Reply
                    </span>
                    <span x-show="isSending" class="inline-flex items-center gap-2">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        Sending...
                    </span>
                </x-admin.ui.button.primary>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div x-cloak x-show="deleteOpen" @keydown.escape.window="closeDelete()"
         class="fixed inset-0 z-[140] flex items-center justify-center p-4">
        <div
            x-show="deleteOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-red-950/40 backdrop-blur-sm"
            @click="closeDelete()"
            aria-hidden="true"
        ></div>

        <div
            x-show="deleteOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md overflow-hidden rounded-2xl border border-red-200 bg-white shadow-2xl"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-message-title"
            aria-describedby="delete-message-desc"
            @click.stop
        >
            <div class="flex items-start justify-between gap-4 border-b border-red-100 bg-red-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </span>
                    <div>
                        <h2 id="delete-message-title" class="text-lg font-semibold text-red-900">Delete Message</h2>
                        <p class="text-xs text-red-700">This action cannot be undone.</p>
                    </div>
                </div>
                <button class="rounded-full p-1 text-red-600 hover:text-red-700"
                        @click="closeDelete()" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="delete-message-desc" class="px-6 py-5 text-sm text-red-700">
                Are you sure you want to delete <span class="font-semibold text-red-900" x-text="deleteName || 'this message'"></span>?
                This message will be permanently removed from the inbox.
            </div>

            <form method="POST"
                  :action="deleteAction"
                  @submit="deleteSubmitting = true"
                  class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-red-100 bg-red-50/60"
                  data-action-loading>
                @csrf
                @method('DELETE')

                <button type="button"
                        @click="closeDelete()"
                        class="px-4 py-2 bg-white text-red-700 rounded-lg border border-red-200 hover:bg-red-50 transition-colors duration-200 font-medium text-sm">
                    Cancel
                </button>

                <button type="submit"
                        x-bind:disabled="deleteSubmitting"
                        :class="{ 'opacity-60 cursor-not-allowed': deleteSubmitting }"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm text-sm">
                    <span x-show="!deleteSubmitting">Delete Message</span>
                    <span x-show="deleteSubmitting">Deleting...</span>
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    function inboxSystem() {
        return {
            search: '{{ request('search') }}',
            sort: '{{ request('sort', 'newest') }}',
            isLoading: false,
            debounce: null,
            viewOpen: false,
            replyOpen: false,
            deleteOpen: false,
            deleteSubmitting: false,
            isSending: false,
            activeMsg: {},
            replySubject: '',
            replyBody: '',
            shouldReload: false,
            deleteId: null,
            deleteName: '',
            deleteAction: '',

            // Initialize watcher for search
            init() {
                this.$watch('search', (value) => {
                    clearTimeout(this.debounce);
                    this.isLoading = true;
                    this.debounce = setTimeout(() => {
                        this.fetchMessages();
                    }, 400); // Wait 400ms after typing stops
                });

                this.$watch('sort', (value) => {
                    this.isLoading = true;
                    this.fetchMessages();
                });
            },

            // REAL-TIME SEARCH FETCH
            fetchMessages() {
                const params = new URLSearchParams({
                    search: this.search,
                    sort: this.sort
                });

                const url = `{{ route('admin.messages.index') }}?${params.toString()}`;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // 1. Target the specific container
                    const listContainer = document.getElementById('message-list-container');
                    
                    // 2. Replace Content
                    listContainer.innerHTML = doc.getElementById('message-list-container').innerHTML;
                    document.getElementById('inbox-stats').innerHTML = doc.getElementById('inbox-stats').innerHTML;
                    document.getElementById('pagination-container').innerHTML = doc.getElementById('pagination-container').innerHTML;
                    
                    // 3. CRITICAL FIX: Re-initialize Alpine.js on the new content
                    // This reconnects the @click handlers so buttons work again
                    if (window.Alpine) {
                        window.Alpine.initTree(listContainer);
                    }

                    // 4. Update URL without refreshing
                    window.history.pushState({}, '', `?${params.toString()}`);
                })
                .catch(err => {
                    console.error("Search error:", err);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },

            // --- Modal Logic Remains Same ---
            
            openView(id) {
                fetch(`/admin/messages/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        this.activeMsg = data;
                        this.viewOpen = true;
                        
                        // If it was UNREAD, mark flag to reload on close
                        if (data.status === 'UNREAD') {
                            this.shouldReload = true;
                        } else {
                            this.shouldReload = false;
                        }
                    });
            },

            closeView() {
                this.viewOpen = false;
                // Only refresh if status changed from UNREAD -> READ
                if(this.shouldReload) {
                    this.fetchMessages(); 
                    this.shouldReload = false;
                }
            },

            openReply(msg) {
                this.activeMsg = msg;
                this.replySubject = `Re: Inquiry from ${msg.name}`;
                this.replyBody = `Hi ${msg.name},\n\nThank you for reaching out to us.\n\n\nBest regards,\nSmart Cafeteria Team`;
                this.replyOpen = true;
            },

            openDelete(id, name = 'this message') {
                this.deleteId = id;
                this.deleteName = name || 'this message';
                this.deleteAction = `/admin/messages/${id}`;
                this.deleteSubmitting = false;
                this.deleteOpen = true;
            },

            closeDelete() {
                this.deleteOpen = false;
                this.deleteSubmitting = false;
                this.deleteId = null;
                this.deleteName = '';
                this.deleteAction = '';
            },

            sendReply() {
                if (!this.replyBody.trim()) {
                    alert('Please enter a message.');
                    return;
                }

                this.isSending = true;
                
                // Construct URL manually to avoid JS route error
                const replyUrl = `/admin/messages/${this.activeMsg.id}/reply`;
                
                fetch(replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: this.activeMsg.id,
                        subject: this.replySubject,
                        message: this.replyBody
                    })
                }).then(res => res.json())
                .then(data => {
                    if(data.success) {
                        this.replyOpen = false;
                        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'message-reply-success' }));
                        this.fetchMessages(); // Refresh list to update "Replied" status
                    } else {
                        alert('Failed to send reply: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('An error occurred while sending the reply.');
                })
                .finally(() => {
                    this.isSending = false;
                });
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-PH', { 
                    month: 'short', day: 'numeric', year: 'numeric', 
                    hour: '2-digit', minute: '2-digit' 
                });
            }
        }
    }
</script>
@endsection

