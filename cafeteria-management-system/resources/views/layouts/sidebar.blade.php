<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Cafeteria') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Fugaz+One&family=Damion&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    
 <style>
    @keyframes slide-in-left {
        0% { transform: translateX(-100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    
    .sidebar-gradient {
        background: linear-gradient(#1F2937 100%);
    }
    
    .active-menu-item {
        background-color: #f5f5f5;
        color: #FB3E05; /* Changed to orange */
        border-top-left-radius: 50px;
        border-bottom-left-radius: 50px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        margin-right: 0px;
        margin-left: 10px;
        position: relative;
        z-index: 10;
    }
    
    .active-menu-item::before {
        content: '';
        position: absolute;
        top: -20px;
        right: 0;
        width: 20px;
        height: 20px;
        background: transparent;
        border-bottom-right-radius: 20px;
        box-shadow: 8px 8px 0 8px #f5f5f5;
    }
    
    .active-menu-item::after {
        content: '';
        position: absolute;
        bottom: -20px;
        right: 0;
        width: 20px;
        height: 20px;
        background: transparent;
        border-top-right-radius: 20px;
        box-shadow: 8px -8px 0 8px #f5f5f5;
    }
    
    .menu-item {
        margin-bottom: 0.125rem; /* Original spacing */
        border-radius: 0px;
        border-top-right-radius: 0%;
        border-bottom-right-radius: 0%;
        font-size: 0.95rem; /* Increased font size */
    }
    
    .menu-item:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(4px);
        color: #ffffff;
        margin-left: 10px;
    }
    
    .menu-item:hover i {
        color: #ffffff !important;
    }
    
    .active-menu-item:hover {
        background: #f5f5f5 !important;
        color: #FB3E05 !important; /* Orange for active hover */
        transform: none;
    }
    
    .active-menu-item:hover i {
        color: #FB3E05 !important; /* Orange for active icons on hover */
    }  
    
    .hover-glow:hover {
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }
    
    /* FONT CLASSES - ADDED */
    .font-poppins {
        font-family: 'Poppins', sans-serif;
    }
    
    .font-fugaz {
        font-family: 'Fugaz One', cursive;
    }
    
    .font-damion {
        font-family: 'Damion', cursive;
        font-style: italic;
    }
    
    .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100vh;
        padding: 0 0rem 1rem 0rem; /* Removed top padding */
        justify-content: space-between;
    }
    
    .nav-section {
        flex: 0 1 auto;
    }
    
    .logout-section {
        flex-shrink: 0;
        padding: 1rem 0rem 0rem 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 0.5rem;
        margin-right: 1rem;
        margin-left: 0;
    }
    
    .section-header {
        margin-top: 1.5rem; /* Original spacing */
        margin-bottom: 0.75rem; /* Original spacing */
        font-size: 0.8rem; /* Slightly larger section headers */
    }


    .logo-section {
        margin-bottom: 0;
        margin-right: 1rem;
        padding: 0.5rem 0.4rem 0.5rem 0.8rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    /* Mobile overlay */
    .mobile-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 40;
    }

    .mobile-overlay.active {
        display: block;
    }

    /* Ensure sidebar is above overlay */
    .sidebar-gradient {
        z-index: 50;
    }

    /* Mobile-specific styles */
    @media (max-width: 768px) {
        .active-menu-item {
            border-radius: 12px !important; /* Remove curved edges on mobile */
            margin-left: 1rem !important; /* Standard margin on mobile */
            margin-right: 1rem !important; /* Balanced margin */
        }
        
        .active-menu-item::before,
        .active-menu-item::after {
            display: none !important; /* Hide the curved pseudo-elements on mobile */
        }
        
        .menu-item {
            margin-left: 1rem;
            margin-right: 0rem;
            font-size: 0.85rem;
            border-radius: 12px; /* Consistent border radius on mobile */
        }
        
        .menu-item:hover {
            margin-left: 1rem; /* Consistent margin on hover for mobile */
            
        }
        
        .active-menu-item:hover {
            margin-left: 1rem !important; /* Consistent margin for active hover on mobile */
            margin-right: 1rem !important;
        }
        
        /* Adjust sidebar rounded corners for mobile */
        .sidebar-gradient {
            border-radius: 0 !important; /* Remove rounded corners on mobile */
        }
    }

    /* NEW: Connected Header Design */
    .header-connected {
        background: linear-gradient(#1F2937 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: white;
    }

    .header-search {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .header-search::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .header-search:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }

    .header-button {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        transition: all 0.3s ease;
    }

    .header-button:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Smooth transitions for header elements */
    .header-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Glass morphism effect for header */
    .header-glass {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Connected design - seamless transition from sidebar */
    .main-content-wrapper {
        width: 100%;
        margin-left: 0;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    @media (min-width: 768px) {
        .main-content-wrapper {
            margin-left: 16rem; /* 64 * 4 = 256px equivalent to w-64 */
            width: calc(100% - 16rem);
        }
    }
    </style>
</head>

<body class="font-poppins antialiased text-sm"
      x-data="{ openSidebar: false, confirmLogout: false }">

<div class="min-h-screen flex">

    <div class="mobile-overlay md:hidden" 
         :class="{ 'active': openSidebar }"
         @click="openSidebar = false"
         x-show="openSidebar"
         x-cloak>
    </div>

    <aside class="sidebar-gradient text-white w-64 fixed inset-y-0 left-0 z-50 transform md:translate-x-0 transition-all duration-300  backdrop-blur-md animate-slide-in-left"
           :class="openSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <div class="sidebar-content">
            <div class="flex-1 overflow-hidden">
                <div class="logo-section flex items-center justify-center border-b border-white/10">
                    <img src="{{ asset('images/ret-logoo.png') }}" 
                         alt="RET Cafeteria Logo" 
                        class="h-14 w-auto">
                </div>

                <nav class="nav-section">
                    <div class="section-header text-xs px-6 py-1 font-semibold text-white/70 uppercase tracking-wider">
                            Management
                        </div>


                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.users') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('superadmin.users') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="far fa-user {{ request()->routeIs('superadmin.users') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>                  
                            Manage Users
                        </a>
                    @endif

                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.dashboard') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-chart-line {{ request()->routeIs('admin.dashboard') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.messages.index') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.messages.*') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false">
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fas fa-envelope {{ request()->routeIs('admin.messages.*') ? 'text-[#FB3E05]' : 'text-white' }}"></i>
                            </span>
                            Messages
                            </a>

                        <a href="{{ route('admin.reservations') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.reservations') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="far fa-calendar-check {{ request()->routeIs('admin.reservations') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Reservations
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.reports.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-chart-pie {{ request()->routeIs('admin.reports.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Reports
                        </a>

                        <a href="{{ route('admin.inventory.index') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.inventory.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-boxes-stacked {{ request()->routeIs('admin.inventory.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Inventory
                        </a>

                        <a href="{{ route('admin.menus.index', ['type' => 'standard', 'meal' => 'breakfast']) }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-utensils {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Manage Menus
                        </a>

                        <a href="{{ route('admin.menus.prices') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.menus.prices') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-peso-sign {{ request()->routeIs('admin.menus.prices') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Manage Prices
                        </a>

                        <a href="{{ route('admin.calendar') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.calendar') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="far fa-calendar-days {{ request()->routeIs('admin.calendar') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                            Calendar
                        </a>
                    @endif

                    <div class="section-header text-xs px-6 font-semibold text-white/70 uppercase tracking-wider">
                        Settings
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('profile.edit') ? 'active-menu-item' : '' }}"
                       @click="openSidebar = false"> <span class="flex items-center justify-center w-5 h-5 mr-3"> <i class="fas fa-gear {{ request()->routeIs('profile.edit') ? 'text-[#FB3E05]' : 'text-white' }}"></i> </span>
                        Account Settings
                    </a>
                </nav>
            </div>

            <div class="logout-section">
                <button @click="confirmLogout = true"
                        class="w-full flex items-center justify-center gap-2 bg-white/20 text-white hover:bg-red-500/90 hover-glow transition-all duration-300 rounded-full px-4 py-2.5 font-semibold shadow-md"> <i class="fas fa-right-from-bracket"></i> Logout
                </button>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col main-content-wrapper">
        <header class="header-connected header-glass px-6 py-3 fixed top-0 right-0 z-30 transition-all duration-300"
                :class="openSidebar ? 'md:left-64' : 'md:left-0'"
                style="left: 0; right: 0;">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="openSidebar = !openSidebar"
                            class="md:hidden p-2 rounded-lg header-button header-transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="hidden md:block">
                        <h1 class="text-lg font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" 
                               id="searchInput" 
                               placeholder="Search tables, data, or content..."
                               class="header-search rounded-lg px-5 py-3 pl-10 pr-10 w-80 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-200 header-transition"
                               oninput="filterTable(this.value)">
                        <svg class="w-5 h-5 absolute left-3 top-2.5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button id="clearSearch" class="absolute right-3 top-2.5 text-white/70 hover:text-white transition-colors duration-200" style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="relative" x-data="{ openNotif: false }">
                        <button @click="openNotif = !openNotif"
                                class="header-button p-2 rounded-full header-transition relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5H9v6H5V7h10z"></path>
                            </svg>
                            <div class="notification-badge"></div>
                        </button>
                        <div x-show="openNotif"
                             @click.away="openNotif = false"
                             class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 header-transition"
                             x-cloak>
                            <div class="p-4 border-b border-gray-200 font-semibold text-gray-800">Notifications</div>
                            <ul class="max-h-60 overflow-y-auto" id="notifications-list">
                                <li class="px-4 py-3 hover:bg-gray-50 text-gray-600">Loading notifications...</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-6 overflow-y-auto flex-1 mt-16 bg-gray-100">
            @yield('content')
        </main>
    </div>
</div>

<div x-show="confirmLogout"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
     x-cloak>
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 text-black transform transition-all duration-300 scale-95"
         x-transition:enter="scale-100"
         x-transition:enter-start="scale-95">
        <div class="flex items-center mb-6">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold text-gray-900">Confirm Logout</h2>
            </div>
        </div>
        <p class="mb-8 text-gray-600">Are you sure you want to log out?</p>

        <div class="flex justify-end gap-3">
            <button @click="confirmLogout = false"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium shadow-lg">
                    Yes, Logout
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function filterTable(query) {
    let rows = document.querySelectorAll("table tbody tr");
    query = query.toLowerCase();
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(query) ? "" : "none";
    });

    // Show/hide clear button based on input value
    const clearButton = document.getElementById('clearSearch');
    if (query.length > 0) {
        clearButton.style.display = 'block';
    } else {
        clearButton.style.display = 'none';
    }
}

// Clear search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');

    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        filterTable('');
        searchInput.focus();
    });
});

// Load notifications when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

function loadNotifications() {
    fetch('{{ url("/admin/recent-notifications") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const list = document.getElementById('notifications-list');
            if (!data || data.length === 0) {
                list.innerHTML = '<li class="px-4 py-3 hover:bg-gray-50 text-gray-600">No new notifications</li>';
            } else {
                list.innerHTML = data.map(notification => {
                    const metadata = notification.metadata || {};
                    const actionUser = metadata.updated_by || metadata.generated_by || metadata.created_by || metadata.deleted_by || metadata.added_by || metadata.removed_by || (notification.user ? notification.user.name : 'Unknown User');
                    return `
                            <li class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">${actionUser}</p>
                                        <p class="text-sm text-gray-600">${notification.description || 'Unknown Action'}</p>
                                        <p class="text-xs text-gray-400">${notification.created_at ? new Date(notification.created_at).toLocaleString() : 'Unknown Time'}</p>
                                    </div>
                                </div>
                            </li>
                    `;
                }).join('');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const list = document.getElementById('notifications-list');
            list.innerHTML = '<li class="px-4 py-3 hover:bg-gray-50 text-gray-600">Error loading notifications</li>';
        });
}

// Refresh notifications every 30 seconds
setInterval(loadNotifications, 30000);

// Close sidebar when clicking outside on mobile
document.addEventListener('DOMContentLoaded', function() {
    // This is handled by Alpine.js now, but keeping for reference
});
</script>
</body>
</html>