<style>
    /* Active state styles for navigation */
    .nav-link.active {
        color: #48bb78 !important; /* ret-green-light color */
        position: relative;
    }
    
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #48bb78;
    }
    
    /* Active state for dropdown items */
    .dropdown-link.active {
        background-color: #f7faf7;
        color: #48bb78 !important;
        font-weight: 500;
    }
</style>

<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16 relative">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/ret-logo-nav.png') }}" alt="RET Cafeteria Logo" class="h-12 w-auto" />
        </div>

        <nav class="hidden md:flex space-x-8 text-ret-dark font-poppins font-medium absolute left-1/2 -translate-x-1/2">
            <a href="{{ url('/') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('/') ? 'active' : 'text-gray-600' }}">Home</a>
            <a href="{{ url('/about') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('about') ? 'active' : 'text-gray-600' }}">About</a>
            <a href="{{ url('/menu') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('menu') ? 'active' : 'text-gray-600' }}">Menu</a>
            <a href="{{ url('/contact') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('contact') ? 'active' : 'text-gray-600' }}">Contact Us</a>

            <div class="relative group flex items-center">
                @guest
                    <a
                        href="{{ route('login') }}"
                        data-login-required="true"
                        class="nav-link text-gray-600 hover:text-ret-green-light flex items-center cursor-pointer py-1"
                    >
                        Reservation
                        <svg class="w-4 h-4 ml-1 transform transition duration-300 group-hover:rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                @else
                    <a
                        href="#"
                        class="nav-link text-gray-600 hover:text-ret-green-light flex items-center cursor-pointer py-1 {{ request()->is('reservation_form') || request()->is('reservation_form_menu') || request()->is('reservation_details') || request()->is('reservations/*') ? 'active' : '' }}"
                    >
                        Reservation
                        <svg class="w-4 h-4 ml-1 transform transition duration-300 group-hover:rotate-180"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                @endguest

                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-0 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 transform z-50">
                    <div class="py-1">
                        @auth
                            <a href="{{ route('reservation_form') }}" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150 {{ request()->routeIs('reservation_form') || request()->routeIs('reservation_form_menu') ? 'active' : '' }}">
                                Make a Reservation
                            </a>
                            <a href="{{ route('reservation_details') }}" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150 {{ request()->routeIs('reservation_details') || request()->routeIs('reservation_view') ? 'active' : '' }}">
                                View My Reservations
                            </a>
                        @else
                            <a href="{{ route('login') }}" data-login-required="true" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150">
                                Make a Reservation
                            </a>
                            <a href="{{ route('login') }}" data-login-required="true" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150">
                                View My Reservations
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            </nav>

        <div class="flex items-center space-x-4 text-sm text-gray-600 font-poppins" x-data="{ confirmLogout: false }">
            @guest
                <a href="{{ route('login') }}" class="text-clsu-green hover:text-green-700 font-bold transition-colors duration-200 whitespace-nowrap">
                    LOGIN
                </a>
            @endguest
            @auth
                <span class="whitespace-nowrap">Hi, {{ explode(' ', Auth::user()->name)[0] }}</span>
            
                <div class="w-8 h-8 bg-green-600 rounded-full text-white flex items-center justify-center font-medium">
                    <img src="{{ asset('images/clsu-logo.png') }}" alt="User Profile" class="w-8 h-8 rounded-full" />                
                </div>

                @if(Auth::user()->role == 'customer')
                    <div class="relative" x-data="customerNotifications()" x-init="init()">
                        <button @click="open = !open"
                                class="relative inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"></path>
                            </svg>
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                                  class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1.5 rounded-full bg-red-500 text-white text-[0.65rem] font-semibold flex items-center justify-center"
                                  x-cloak></span>
                        </button>
                        <div x-show="open"
                             @click.outside="open = false"
                             x-transition.opacity.scale.90
                             class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white border border-gray-200 rounded-lg shadow-xl z-50"
                             x-cloak>
                            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                <span class="font-semibold text-gray-800">Notifications</span>
                                <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="markAllRead" x-show="unreadCount > 0">
                                    Mark all read
                                </button>
                            </div>
                            <ul class="max-h-72 overflow-y-auto">
                                <template x-if="loading">
                                    <li class="px-4 py-3 text-gray-600">Loading notifications...</li>
                                </template>
                                <template x-if="!loading && items.length === 0">
                                    <li class="px-4 py-3 text-gray-600">No new notifications</li>
                                </template>
                                <template x-for="item in items" :key="item.id">
                                    <li class="px-4 py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <span class="mt-2 w-2 h-2 rounded-full"
                                                  :class="item.read ? 'bg-gray-300' : 'bg-blue-500'"></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-700" x-text="item.description"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="item.time"></p>
                                                <div class="mt-2 flex items-center gap-3">
                                                    <a x-show="item.url" :href="item.url" class="text-xs text-green-700 hover:text-green-900 font-semibold" x-text="item.linkLabel"></a>
                                                    <button type="button"
                                                            class="text-xs text-blue-600 hover:text-blue-800"
                                                            @click="toggleRead(item.id)">
                                                        <span x-text="item.read ? 'Mark as unread' : 'Mark as read'"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <button type="button" @click="confirmLogout = true" class="text-clsu-green hover:text-green-700 font-bold transition-colors duration-200 whitespace-nowrap">
                        LOGOUT
                    </button>

                    <div 
                        x-show="confirmLogout" 
                        style="display: none;" 
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                    >
                        <div 
                            class="bg-white rounded-lg shadow-xl w-96 p-6 mx-4 transform transition-all"
                            @click.away="confirmLogout = false"
                        >
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Confirm Logout</h3>
                            <p class="text-gray-600 mb-6">Are you sure you want to log out?</p>
                            
                            <div class="flex justify-end space-x-3">
                                <button 
                                    @click="confirmLogout = false" 
                                    class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200"
                                >
                                    Cancel
                                </button>
                                
                                <button 
                                    @click="document.getElementById('logout-form').submit()" 
                                    class="px-4 py-2 text-white bg-clsu-green hover:bg-green-700 rounded-lg transition duration-200"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endif
            @endauth
        </div>
    </div>
</header>

@guest
<div id="login-required-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white shadow-2xl border border-gray-100 p-6">
        <h3 class="text-xl font-bold text-gray-900">Login Required</h3>
        <p class="mt-2 text-sm text-gray-600">
            You are required to log in first before making a reservation.
        </p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                id="close-login-required-modal"
                class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200">
                Cancel
            </button>
            <a href="{{ route('login') }}"
                class="px-4 py-2 text-white bg-clsu-green hover:bg-green-700 rounded-lg transition duration-200">
                Proceed logging in
            </a>
        </div>
    </div>
</div>
@endguest

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginRequiredModal = document.getElementById('login-required-modal');
    const loginRequiredTriggers = document.querySelectorAll('[data-login-required="true"]');
    const closeLoginRequiredModalButton = document.getElementById('close-login-required-modal');

    if (!loginRequiredModal || loginRequiredTriggers.length === 0) {
        return;
    }

    function openLoginRequiredModal(event) {
        event.preventDefault();
        loginRequiredModal.classList.remove('hidden');
        loginRequiredModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeLoginRequiredModal() {
        loginRequiredModal.classList.add('hidden');
        loginRequiredModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    loginRequiredTriggers.forEach(trigger => {
        trigger.addEventListener('click', openLoginRequiredModal);
    });

    if (closeLoginRequiredModalButton) {
        closeLoginRequiredModalButton.addEventListener('click', closeLoginRequiredModal);
    }

    loginRequiredModal.addEventListener('click', function(event) {
        if (event.target === loginRequiredModal) {
            closeLoginRequiredModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !loginRequiredModal.classList.contains('hidden')) {
            closeLoginRequiredModal();
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update active states based on current URL
    function updateActiveStates() {
        const navLinks = document.querySelectorAll('.nav-link');
        const dropdownLinks = document.querySelectorAll('.dropdown-link');
        
        // Get current path
        const currentPath = window.location.pathname;
        
        // Remove active classes from all links
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (!link.classList.contains('text-gray-600') && link.getAttribute('href') !== '#') {
                link.classList.add('text-gray-600');
            }
        });
        
        dropdownLinks.forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to current page link
        navLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref && linkHref !== '#') {
                const linkPath = new URL(linkHref, window.location.origin).pathname;
                if (linkPath === currentPath) {
                    link.classList.add('active');
                    link.classList.remove('text-gray-600');
                }
            }
        });
        
        // Handle dropdown links and their parent reservation link
        let reservationDropdownActive = false;
        
        // Define reservation-related paths
        const reservationPaths = [
            '/reservation_form',
            '/reservation_form_menu',
            '/reservation_details',
            '/reservations'  // Base path for reservation views
        ];
        
        // Check if current path is a reservation-related path
        const isReservationPath = reservationPaths.some(path => 
            currentPath === path || 
            currentPath.startsWith(path + '/')
        );
        
        dropdownLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref) {
                const linkPath = new URL(linkHref, window.location.origin).pathname;
                
                // Check for exact match
                if (linkPath === currentPath) {
                    link.classList.add('active');
                    reservationDropdownActive = true;
                }
                // Check for reservation view pages (/reservations/{id})
                else if (linkPath === '/reservation_details' && currentPath.match(/^\/reservations\/\d+$/)) {
                    link.classList.add('active');
                    reservationDropdownActive = true;
                }
                // Check for reservation form menu pages
                else if (linkPath === '/reservation_form' && currentPath === '/reservation_form_menu') {
                    link.classList.add('active');
                    reservationDropdownActive = true;
                }
            }
        });
        
        // Only activate reservation parent link if on a reservation-related page
        const reservationLink = document.querySelector('a[href="#"]');
        if ((reservationDropdownActive || isReservationPath) && reservationLink) {
            reservationLink.classList.add('active');
            reservationLink.classList.remove('text-gray-600');
        } else if (reservationLink) {
            reservationLink.classList.remove('active');
            reservationLink.classList.add('text-gray-600');
        }
    }
    
    // Update active states on page load
    updateActiveStates();
    
    // Update active states when navigating
    document.querySelectorAll('.nav-link, .dropdown-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault(); // Prevent default only for reservation parent link
            }
            // Small delay to allow page navigation
            setTimeout(updateActiveStates, 100);
        });
    });
    
    // Update active states when browser history changes
    window.addEventListener('popstate', updateActiveStates);
    
    // Optional: Observe URL changes for single-page applications
    if (typeof history.pushState === 'function') {
        const originalPushState = history.pushState;
        history.pushState = function() {
            originalPushState.apply(this, arguments);
            setTimeout(updateActiveStates, 50);
        };
        
        const originalReplaceState = history.replaceState;
        history.replaceState = function() {
            originalReplaceState.apply(this, arguments);
            setTimeout(updateActiveStates, 50);
        };
    }
});
</script>

<script>
function customerNotifications() {
    return {
        open: false,
        items: [],
        loading: true,
        unreadCount: 0,
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000);
        },
        updateUnread() {
            this.unreadCount = this.items.filter(item => !item.read).length;
        },
        markAllRead() {
            fetch('{{ url("/customer/notifications/mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => ({ ...item, read: true }));
                    this.updateUnread();
                })
                .catch(error => {
                    console.error('Error marking all notifications read:', error);
                });
        },
        toggleRead(id) {
            const target = this.items.find(item => item.id === id);
            if (!target) return;

            const nextRead = !target.read;
            fetch(`{{ url("/customer/notifications") }}/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ read: nextRead })
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => item.id === id ? { ...item, read: nextRead } : item);
                    this.updateUnread();
                })
                .catch(error => {
                    console.error('Error toggling notification read:', error);
                });
        },
        fetchNotifications() {
            this.loading = true;
            fetch('{{ url("/customer/recent-notifications") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    this.items = (data || []).map(notification => {
                        const metadata = notification.metadata || {};
                        return {
                            id: notification.id,
                            description: notification.description || 'Notification',
                            time: notification.created_at ? new Date(notification.created_at).toLocaleString() : 'Unknown Time',
                            read: Boolean(notification.read),
                            url: metadata.url || null,
                            linkLabel: metadata.link_label || 'Pay Now',
                        };
                    });
                    this.updateUnread();
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    this.items = [];
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    };
}
</script>
