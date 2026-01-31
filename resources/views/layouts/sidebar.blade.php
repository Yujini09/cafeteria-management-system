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
    @livewireStyles
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <style>
    @keyframes slide-in-left {
        0% { transform: translateX(-100%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    
    .sidebar-gradient {
        background: linear-gradient(270deg,#1F2937  60%, #131820 100%);
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
        border-radius: 12px;
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
    
    .font-poppins {
        font-family: 'Poppins', sans-serif;
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
        margin-bottom: 0; /* Remove bottom margin */
        margin-right: 1rem;
        padding: 1rem 0.8rem 1rem 1rem; /* Add vertical padding */
    }

    /* Logo Glow Effect */
    .logo-glow {
        filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.6)) 
               drop-shadow(0 0 8px rgba(255, 255, 255, 0.4))
               drop-shadow(0 0 12px rgba(255, 255, 255, 0.2));
        transition: all 0.3s ease-in-out;
    }

    .logo-glow:hover {
        filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.8)) 
               drop-shadow(0 0 16px rgba(255, 255, 255, 0.6))
               drop-shadow(0 0 18px rgba(255, 255, 255, 0.4));
        transform: scale(1.05);
    }

    /* Alternative: White outline with glow */
    .logo-outline-glow {
        filter: drop-shadow(0 0 2px white) 
               drop-shadow(0 0 4px white)
               drop-shadow(0 0 6px rgba(255, 255, 255, 0.7))
               drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
        transition: all 0.3s ease-in-out;
    }

    .logo-outline-glow:hover {
        filter: drop-shadow(0 0 3px white) 
               drop-shadow(0 0 6px white)
               drop-shadow(0 0 9px rgba(255, 255, 255, 0.8))
               drop-shadow(0 0 14px rgba(255, 255, 255, 0.6));
    }

    /* Strong glow effect */
    .logo-strong-glow {
        filter: drop-shadow(0 0 2px white) 
               drop-shadow(0 0 4px rgba(255, 255, 255, 0.8))
               drop-shadow(0 0 8px rgba(255, 255, 255, 0.6))
               drop-shadow(0 0 16px rgba(255, 255, 255, 0.4));
        transition: all 0.4s ease-in-out;
    }

    .logo-strong-glow:hover {
        filter: drop-shadow(0 0 3px white) 
               drop-shadow(0 0 6px rgba(255, 255, 255, 0.9))
               drop-shadow(0 0 9px rgba(255, 255, 255, 0.7))
               drop-shadow(0 0 14px rgba(255, 255, 255, 0.5));
        transform: scale(1.02);
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
        background: linear-gradient(270deg, #1F2937 100%, #131820 60%);
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

    /* .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background: #EF4444;
        border-radius: 50%;
        border: 2px solid #1F2937;
    } */

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
      x-data="{ openSidebar: false, confirmLogout: false }"
      :class="{ 'overflow-hidden': openSidebar || confirmLogout }"
      @keydown.escape.window="openSidebar = false; confirmLogout = false">

<div class="min-h-screen flex">

    <!-- Mobile Overlay -->
    <div class="mobile-overlay md:hidden"
         :class="{ 'active': openSidebar }"
         @click="openSidebar = false"
         x-show="openSidebar"
         x-transition.opacity
         x-cloak>
    </div>

    <!-- Modern Gradient Sidebar -->
    <aside class="sidebar-gradient text-white w-64 fixed inset-y-0 left-0 z-50 transform md:translate-x-0 transition-all duration-300  backdrop-blur-md animate-slide-in-left"
           :class="openSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           x-cloak>

        <div class="sidebar-content">
            <!-- Top Section: Logo & Navigation -->
            <div class="flex-1 overflow-hidden">
                <!-- Logo Section - Moved to absolute top -->
                <div class="logo-section flex items-center justify-center border-b border-white/10">
                    <img src="{{ asset('images/ret-logo-nav.png') }}" 
                         alt="RET Cafeteria Logo" 
                        class="h-10 w-auto logo-strong-glow">
                </div>

                <!-- Navigation Menu -->
                <nav class="nav-section">
                    <div class="section-header text-xs px-6 py-1 font-semibold text-white/70 uppercase tracking-wider">
                            Management
                        </div>


                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.users') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('superadmin.users') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="far fa-user {{ request()->routeIs('superadmin.users') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>                  
                            Manage Users
                        </a>
                    @endif

                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                        <!-- Dashboard Section -->
                        
                        <a href="{{ route('admin.dashboard') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.dashboard') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="fas fa-chart-line {{ request()->routeIs('admin.dashboard') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.reservations') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.reservations') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="far fa-calendar-check {{ request()->routeIs('admin.reservations') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Reservations
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.reports.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="fas fa-chart-pie {{ request()->routeIs('admin.reports.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Reports
                        </a>

                        <a href="{{ route('admin.inventory.index') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.inventory.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="fas fa-boxes-stacked {{ request()->routeIs('admin.inventory.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Inventory
                        </a>

                        <a href="{{ route('admin.menus.index', ['type' => 'standard', 'meal' => 'breakfast']) }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="fas fa-utensils {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Manage Menus
                        </a>

                        <a href="{{ route('admin.menus.prices') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.menus.prices') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="fas fa-peso-sign {{ request()->routeIs('admin.menus.prices') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Manage Prices
                        </a>

                        <a href="{{ route('admin.calendar') }}"
                           class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('admin.calendar') ? 'active-menu-item' : '' }}"
                           @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                                <i class="far fa-calendar-days {{ request()->routeIs('admin.calendar') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                            </span>
                            Calendar
                        </a>
                    @endif

                    <!-- Account Settings Section -->
                    <div class="section-header text-xs px-6 font-semibold text-white/70 uppercase tracking-wider">
                        Settings
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="menu-item flex items-center px-10 py-2 transition-all duration-300 ease-in-out font-medium {{ request()->routeIs('profile.edit') ? 'active-menu-item' : '' }}"
                       @click="openSidebar = false"> <!-- Close sidebar on mobile click -->
                        <span class="flex items-center justify-center w-5 h-5 mr-3"> <!-- Original icon size -->
                            <i class="fas fa-gear {{ request()->routeIs('profile.edit') ? 'text-[#FB3E05]' : 'text-white' }}"></i> <!-- Changed to orange -->
                        </span>
                        Account Settings
                    </a>
                </nav>
            </div>

            <!-- Bottom Section: Logout Button -->
            <div class="logout-section">
                <button @click="confirmLogout = true"
                        class="w-full flex items-center justify-center gap-2 bg-white/20 text-white hover:bg-red-500/90 hover-glow transition-all duration-300 rounded-full px-4 py-2.5 font-semibold shadow-md"> <!-- Original padding -->
                    <i class="fas fa-right-from-bracket"></i> <!-- Original icon size -->
                    Logout
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Area with Connected Header -->
    <div class="flex-1 flex flex-col main-content-wrapper">
        <!-- Connected Header -->
        <header class="header-connected header-glass px-4 sm:px-6 py-3 fixed top-0 left-0 right-0 md:left-64 z-30 transition-all duration-300">
            <div class="flex items-center justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-4">
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

                <div class="flex items-center justify-end gap-3 sm:gap-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="notificationsPanel()" x-init="init()">
                        <button @click="open = !open"
                                :aria-expanded="open.toString()"
                                class="header-button p-2 rounded-full header-transition relative">
                            <!-- Bell icon -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0h6z"></path>
                            </svg>
                            <!-- Unread badge -->
                            <span x-show="unreadCount > 0"
                                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                                  class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1.5 rounded-full bg-red-500 text-white text-[0.65rem] font-semibold flex items-center justify-center shadow"
                                  x-cloak></span>
                        </button>
                        <div x-show="open"
                             @click.outside="open = false"
                             x-transition.opacity.scale.90
                             class="absolute right-0 mt-2 w-80 max-w-[90vw] bg-white border border-gray-200 rounded-lg shadow-xl z-50 header-transition"
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
                                                <p class="text-sm font-medium text-gray-900" x-text="item.actor"></p>
                                                <p class="text-sm text-gray-600" x-text="item.description"></p>
                                                <p class="text-xs text-gray-400 mt-1" x-text="item.time"></p>
                                                <button type="button"
                                                        class="mt-2 text-xs text-blue-600 hover:text-blue-800"
                                                        @click="toggleRead(item.id)">
                                                    <span x-text="item.read ? 'Mark as unread' : 'Mark as read'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="admin-shell p-4 sm:p-6 overflow-y-auto flex-1 mt-24 sm:mt-16 bg-gray-100">
            @yield('content')
        </main>
    </div>
</div>

{{-- Unified admin toasts: success/error/warning. ESC to clear. --}}
<x-admin.ui.toast-container />

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'success', message: @json(session('success')) } })); });</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'error', message: @json(session('error')) } })); });</script>
@endif
@if(session('warning'))
<script>document.addEventListener('DOMContentLoaded', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'warning', message: @json(session('warning')) } })); });</script>
@endif

<!-- Logout Confirmation Modal -->
<div x-show="confirmLogout"
     class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50"
     x-transition.opacity
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
    const normalizedQuery = query.toLowerCase().trim();
    const rows = document.querySelectorAll("table tbody tr");
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(normalizedQuery) ? "" : "none";
    });

    const cards = document.querySelectorAll('[data-search-card="true"]');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(normalizedQuery) ? "" : "none";
    });

    // Show/hide clear button based on input value
    const clearButton = document.getElementById('clearSearch');
    if (clearButton) {
        clearButton.style.display = normalizedQuery.length > 0 ? 'block' : 'none';
    }
}

// Clear search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearButton = document.getElementById('clearSearch');

    if (!searchInput || !clearButton) {
        return;
    }

    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        filterTable('');
        searchInput.focus();
    });
});

function notificationsPanel() {
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
            fetch('{{ url("/admin/notifications/mark-all-read") }}', {
                method: 'POST',
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

            fetch(`{{ url("/admin/notifications") }}/${id}/read`, {
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
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    this.items = this.items.map(item => item.id === id ? { ...item, read: nextRead } : item);
                    this.updateUnread();
                })
                .catch(error => {
                    console.error('Error toggling notification read:', error);
                });
        },
        fetchNotifications() {
            this.loading = true;
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
                    const uniqueNotifications = (data || []).reduce((seen, notification) => {
                        const exists = seen.find(n => n.id === notification.id);
                        if (!exists) {
                            seen.push(notification);
                        }
                        return seen;
                    }, []);

                    this.items = uniqueNotifications.map(notification => {
                        const metadata = notification.metadata || {};
                        const actor = metadata.updated_by || metadata.generated_by || metadata.created_by || metadata.deleted_by || metadata.added_by || metadata.removed_by || (notification.user ? notification.user.name : 'System');
                        return {
                            id: notification.id,
                            actor: actor || 'System',
                            description: notification.description || 'Unknown Action',
                            time: notification.created_at ? new Date(notification.created_at).toLocaleString() : 'Unknown Time',
                            read: Boolean(notification.read),
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

function enhanceAdminSelect(select) {
    if (!select || select.dataset.enhanced === 'true') return;

    const searchable = select.dataset.searchable === 'true';
    const searchPlaceholder = select.dataset.searchPlaceholder || 'Search options...';
    select.classList.add('admin-select');
    select.dataset.enhanced = 'true';

    if (searchable) {
        const wrapper = document.createElement('div');
        wrapper.className = 'admin-select-wrapper';
        wrapper.dataset.searchable = 'true';
        const searchInput = document.createElement('input');
        searchInput.type = 'search';
        searchInput.className = 'admin-select-search';
        searchInput.placeholder = searchPlaceholder;
        searchInput.setAttribute('aria-controls', select.id || '');
        searchInput.setAttribute('aria-label', select.getAttribute('aria-label') || searchPlaceholder);

        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                select.focus();
            }
        });

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            let matchCount = 0;
            const optionGroups = Array.from(select.querySelectorAll('optgroup'));
            const options = Array.from(select.options);

            options.forEach((option) => {
                if (option.value === '') {
                    option.hidden = false;
                    return;
                }
                const isMatch = option.text.toLowerCase().includes(query);
                option.hidden = query.length > 0 && !isMatch;
                if (!option.hidden) matchCount += 1;
            });

            optionGroups.forEach((group) => {
                const groupOptions = Array.from(group.querySelectorAll('option'));
                const hasVisible = groupOptions.some((opt) => !opt.hidden);
                group.hidden = !hasVisible;
            });

            if (query.length > 0 && matchCount === 0) {
                select.classList.add('admin-select-no-match');
            } else {
                select.classList.remove('admin-select-no-match');
            }
        });

        wrapper.appendChild(searchInput);

        if (select.parentNode) {
            select.parentNode.insertBefore(wrapper, select);
            wrapper.appendChild(select);
        }
    }
}

function enhanceAdminSelects(root = document) {
    const selects = root.querySelectorAll('select[data-admin-select="true"]');
    selects.forEach((select) => enhanceAdminSelect(select));
}

document.addEventListener('DOMContentLoaded', () => {
    enhanceAdminSelects(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType !== Node.ELEMENT_NODE) return;
                if (node.matches && node.matches('select[data-admin-select="true"]')) {
                    enhanceAdminSelect(node);
                } else {
                    enhanceAdminSelects(node);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
});

// Close sidebar when clicking outside on mobile
document.addEventListener('DOMContentLoaded', function() {
    // This is handled by Alpine.js now, but keeping for reference
});
</script>
@livewireScripts
</body>
</html>