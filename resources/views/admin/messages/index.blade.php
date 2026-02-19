@extends('layouts.sidebar')
@section('page-title', 'Inbox')

@section('content')
<div x-data="inboxSystem()" x-init="init()" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="bg-admin-primary p-3 rounded-xl shadow-md">
                <i class="fas fa-inbox text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-admin-neutral-900 tracking-tight">Customer Inbox</h1>
                <p class="text-sm text-admin-neutral-500">Manage inquiries from the contact form.</p>
            </div>
        </div>

        {{-- Dynamic Stats Container --}}
        <div id="inbox-stats" class="flex items-center gap-3">
            @if($unreadCount > 0)
                <span class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm font-bold shadow-sm animate-pulse">
                    {{ $unreadCount }} Unread
                </span>
            @endif
            <span class="px-4 py-2 bg-admin-neutral-100 text-admin-neutral-700 rounded-lg text-sm font-bold border border-admin-neutral-200">
                {{ $messages->total() }} Total
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
                class="w-full pl-12 pr-4 py-3 rounded-xl border border-admin-neutral-200 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary outline-none transition-all text-sm"
            >
            <div x-show="isLoading" class="absolute right-4 top-1/2 -translate-y-1/2" style="display: none;">
                <i class="fas fa-circle-notch fa-spin text-admin-primary"></i>
            </div>
        </div>

        <div class="w-full sm:w-48 relative">
            <select x-model="sort" 
                class="w-full px-4 py-3 rounded-xl border border-admin-neutral-200 bg-white outline-none text-sm font-medium text-gray-700 cursor-pointer focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary appearance-none">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="unread">Unread First</option>
            </select>
            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
        </div>
    </div>

    {{-- Message List Container (Target for AJAX Replacement) --}}
    <div id="message-list-container" class="bg-white rounded-2xl shadow-sm border border-admin-neutral-200 overflow-hidden relative">
        <div x-show="isLoading" class="absolute inset-0 bg-white/50 z-10 transition-opacity duration-200" style="display: none;"></div>

        @forelse($messages as $msg)
            <div class="p-6 border-b border-admin-neutral-100 hover:bg-admin-neutral-50 transition-colors group {{ $msg->status === 'UNREAD' ? 'bg-green-50/40' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        {{-- Row 1: Name + Status + Date --}}
                        <div class="flex flex-wrap items-center gap-3 mb-1">
                            <h3 class="text-base font-bold text-gray-900">{{ $msg->name }}</h3>
                            
                            {{-- STATUS BADGES --}}
                            @if($msg->status === 'UNREAD')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-green-100 text-green-700 tracking-wide">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> New
                                </span>
                            @elseif($msg->status === 'REPLIED')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-100 tracking-wide">
                                    <i class="fas fa-reply"></i> Replied
                                </span>
                            @endif

                            <span class="text-xs text-gray-400 font-medium flex items-center gap-1.5 ml-auto sm:ml-0 sm:before:content-['•'] sm:before:mx-1 sm:before:text-gray-300">
                                {{ $msg->created_at->format('M d, Y \a\t g:i A') }}
                            </span>
                        </div>

                        {{-- Row 2: Email --}}
                        <div class="text-sm font-semibold text-green-600 mb-2">{{ $msg->email }}</div>

                        {{-- Row 3: Message Preview --}}
                        <p class="text-gray-600 text-sm line-clamp-1 {{ $msg->status === 'UNREAD' ? 'font-medium' : '' }}">
                            {{ $msg->message }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 pl-4">
                        <button @click="openView({{ $msg->id }})" title="View" 
                            class="p-2.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <button @click="openReply({{ json_encode($msg) }})" title="Reply" 
                            class="p-2.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            <i class="fas fa-reply"></i>
                        </button>
                        
                        <form action="{{ route('admin.messages.delete', $msg->id) }}" method="POST" onsubmit="return confirm('Permanently delete this message?');">
                            @csrf @method('DELETE')
                            <button type="submit" title="Delete" 
                                class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No messages found</h3>
                <p class="text-sm text-gray-500 mt-1">Your inbox is empty or no messages match your search.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination Container --}}
    <div id="pagination-container" class="mt-6">
        {{ $messages->appends(request()->query())->links() }}
    </div>

    {{-- VIEW MODAL --}}
    <div x-show="viewOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 border border-gray-100" @click.away="closeView()">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-lg text-gray-900">Message Details</h3>
                <button @click="closeView()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl font-bold">
                        <span x-text="activeMsg.name ? activeMsg.name.charAt(0).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg" x-text="activeMsg.name"></h4>
                        <p class="text-green-600 text-sm font-medium" x-text="activeMsg.email"></p>
                    </div>
                </div>
                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="activeMsg.message"></p>
                </div>
                <div class="text-xs text-gray-400 text-right">
                    Sent on <span x-text="formatDate(activeMsg.created_at)"></span>
                </div>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button @click="closeView()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold text-sm transition-all">Close</button>
                <button @click="viewOpen = false; openReply(activeMsg)" class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold text-sm shadow-lg shadow-green-200 transition-all flex items-center gap-2">
                    <i class="fas fa-reply"></i> Reply Now
                </button>
            </div>
        </div>
    </div>

    {{-- REPLY MODAL --}}
    <div x-show="replyOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden border border-gray-100" @click.away="replyOpen = false">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-blue-50/50">
                <h3 class="font-bold text-lg text-blue-900 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Send Reply
                </h3>
                <button @click="replyOpen = false" class="text-blue-400 hover:text-blue-600 transition-colors"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">To</label>
                    <div class="px-4 py-3 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 text-sm font-medium" x-text="activeMsg.email"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subject</label>
                    <input type="text" x-model="replySubject" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Message</label>
                    <textarea x-model="replyBody" rows="6" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none resize-none text-sm transition-all placeholder-gray-400" placeholder="Type your response here..."></textarea>
                </div>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button @click="replyOpen = false" class="px-5 py-2.5 text-gray-600 font-bold text-sm hover:text-gray-800 transition-colors">Cancel</button>
                <button @click="sendReply()" :disabled="isSending" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-200 transition-all">
                    <span x-show="!isSending">Send Reply <i class="fas fa-arrow-right ml-1"></i></span>
                    <span x-show="isSending"><i class="fas fa-circle-notch fa-spin"></i> Sending...</span>
                </button>
            </div>
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
            isSending: false,
            activeMsg: {},
            replySubject: '',
            replyBody: '',
            shouldReload: false,

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
                        alert('Reply sent successfully!');
                        this.replyOpen = false;
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