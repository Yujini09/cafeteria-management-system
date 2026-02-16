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
    {!! \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles() !!}

    <script>
        (function () {
            try {
                const isDesktop = window.matchMedia('(min-width: 768px)').matches;
                const saved = sessionStorage.getItem('sidebarOpen');
                const isOpen = isDesktop && (saved === null ? true : saved === 'true');
                document.documentElement.setAttribute('data-sidebar-preinit', isOpen ? 'open' : 'closed');
            } catch (error) {
                const fallbackDesktop = window.matchMedia('(min-width: 768px)').matches;
                document.documentElement.setAttribute('data-sidebar-preinit', fallbackDesktop ? 'open' : 'closed');
            }
        })();
    </script>
    
    <style>
    :root {
        --sidebar-expanded-width: 16rem;
        --sidebar-collapsed-width: 5rem;
    }

    .sidebar-gradient {
        background: #f4f8f6;
        border-right: 1px solid #d9e4dd;
        box-shadow: 6px 0 20px rgba(15, 23, 42, 0.06);
        width: var(--sidebar-expanded-width);
        overflow-x: hidden;
    }

    .font-poppins { font-family: 'Poppins', sans-serif; }
    .font-fugaz { font-family: 'Fugaz One', cursive; }
    .font-damion { font-family: 'Damion', cursive; }

    .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100vh;
        padding: 0 0 1rem;
        justify-content: space-between;
    }

    .nav-section {
        flex: 0 1 auto;
        padding: 0.25rem 0.1rem 0.5rem;
    }

    .sidebar-shell {
        margin: 0.6rem 0.6rem 0;
        border-radius: 1rem;
        border: 1px solid #dbe7e0;
        background: #ffffff;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .sidebar-brand {
        margin: 0;
        border-radius: 0;
        min-height: 4rem;
        padding: 0 1rem;
        background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(0, 70, 46, 0.2);
    }

    .sidebar-brand img {
        width: auto;
        height: 3rem;
        max-width: 100%;
        object-fit: contain;
    }

    .section-header {
        margin: 0.95rem 0.8rem 0.45rem;
        font-size: 0.67rem;
        font-weight: 700;
        color: #6a7f72;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 0;
    }

    .section-header::before {
        display: none;
    }

    .menu-item {
        margin: 0.2rem 0.45rem;
        border-radius: 0.75rem;
        font-size: 0.86rem;
        font-weight: 600;
        color: #2f4b3f;
        border: 1px solid transparent;
        position: relative;
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .menu-item::after {
        display: none;
    }

    .menu-item:hover {
        color: #1e352b;
        background: #f1f6f3;
        border-color: #d6e3dc;
        transform: none;
    }

    .active-menu-item {
        background: #057C3C;
        border: 1px solid #057C3C;
        color: #ffffff;
        box-shadow: none;
    }

    .active-menu-item::after {
        opacity: 0;
    }

    .active-menu-item:hover {
        transform: none;
        background: #057C3C;
        color: #ffffff;
        border-color: #057C3C;
    }

    .active-menu-item:hover::after {
        opacity: 0;
    }

    .active-menu-item:hover .menu-icon {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    .menu-icon {
        width: 1.85rem;
        height: 1.85rem;
        border-radius: 0.6rem;
        display: grid;
        place-items: center;
        background: #f7fbf9;
        border: 1px solid #d7e4dd;
        color: #2f5846;
        transition: all 0.2s ease;
    }

    .menu-item:hover .menu-icon {
        background: #e8f2ed;
        color: #00462E;
        border-color: #c7dbcf;
    }

    .active-menu-item .menu-icon {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    .logout-section {
        flex-shrink: 0;
        margin: 0.75rem 0.75rem 0;
        padding-top: 0.8rem;
        border-top: 1px solid #dbe9e0;
    }

    .sidebar-collapsed .sidebar-shell {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }

    .sidebar-collapsed .sidebar-brand {
        padding-left: 0.35rem;
        padding-right: 0.35rem;
    }

    .sidebar-collapsed .sidebar-brand img {
        height: 2.1rem;
    }

    .sidebar-collapsed .role-badge {
        display: none;
    }

    .sidebar-collapsed .section-header {
        justify-content: center;
        margin: 0.9rem 0.35rem 0.4rem;
        gap: 0;
        font-size: 0;
        letter-spacing: 0;
    }

    .sidebar-collapsed .section-header::before {
        box-shadow: 0 0 0 2px rgba(5, 124, 60, 0.2);
    }

    .sidebar-collapsed .menu-item {
        margin: 0.2rem 0.35rem;
        justify-content: center;
        padding-left: 0.55rem;
        padding-right: 0.55rem;
    }

    .sidebar-collapsed .menu-item > span:not(.menu-icon) {
        display: none;
    }

    .sidebar-collapsed .logout-section {
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }

    .sidebar-collapsed .logout-section button {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .sidebar-collapsed .logout-text {
        display: none;
    }

    /* Custom Scrollbar for Sidebar */
    .sidebar-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar-scroll::-webkit-scrollbar-track {
        background: #edf4f0;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #bfd2c5;
        border-radius: 10px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: #9fbba9;
    }

    .sidebar-gradient { z-index: 50; }

    @media (max-width: 768px) {
        .menu-item {
            margin-left: 0.4rem;
            margin-right: 0.4rem;
            font-size: 0.85rem;
            border-radius: 0.8rem;
        }
        .sidebar-gradient {
            border-radius: 0 !important;
        }
        .sidebar-shell {
            border-radius: 0.9rem;
        }
        .sidebar-brand {
            min-height: 3.75rem;
            padding: 0 0.8rem;
        }
        .sidebar-brand img {
            width: auto;
            height: 2.35rem;
        }
    }

    @media (min-width: 768px) {
        .sidebar-expanded,
        html[data-sidebar-preinit='open'] .sidebar-gradient {
            width: var(--sidebar-expanded-width);
            transform: translateX(0);
        }

        .sidebar-collapsed,
        html[data-sidebar-preinit='closed'] .sidebar-gradient {
            width: var(--sidebar-collapsed-width);
            transform: translateX(0);
        }

        .main-content-wrapper.sidebar-offset,
        html[data-sidebar-preinit='open'] .main-content-wrapper {
            margin-left: var(--sidebar-expanded-width);
            width: calc(100% - var(--sidebar-expanded-width));
        }

        .main-content-wrapper.sidebar-collapsed-offset,
        html[data-sidebar-preinit='closed'] .main-content-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        html[data-sidebar-preinit='closed'] .sidebar-shell {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        html[data-sidebar-preinit='closed'] .sidebar-brand {
            padding-left: 0.35rem;
            padding-right: 0.35rem;
        }

        html[data-sidebar-preinit='closed'] .sidebar-brand img {
            height: 2.1rem;
        }

        html[data-sidebar-preinit='closed'] .role-badge {
            display: none;
        }

        html[data-sidebar-preinit='closed'] .section-header {
            justify-content: center;
            margin: 0.9rem 0.35rem 0.4rem;
            gap: 0;
            font-size: 0;
            letter-spacing: 0;
        }

        html[data-sidebar-preinit='closed'] .section-header::before {
            box-shadow: 0 0 0 2px rgba(5, 124, 60, 0.2);
        }

        html[data-sidebar-preinit='closed'] .menu-item {
            margin: 0.2rem 0.35rem;
            justify-content: center;
            padding-left: 0.55rem;
            padding-right: 0.55rem;
        }

        html[data-sidebar-preinit='closed'] .menu-item > span:not(.menu-icon) {
            display: none;
        }

        html[data-sidebar-preinit='closed'] .logout-section {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        html[data-sidebar-preinit='closed'] .logout-section button {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        html[data-sidebar-preinit='closed'] .logout-text {
            display: none;
        }
    }

    .floating-sidebar-toggle {
        position: fixed;
        top: 1rem;
        left: 1rem;
        z-index: 60;
        width: 2.85rem;
        height: 2.85rem;
        border-radius: 0.95rem;
        border: 1px solid #0b6d41;
        background: linear-gradient(135deg, #057C3C 0%, #00462E 100%);
        color: #ffffff;
        display: grid;
        place-items: center;
        box-shadow: 0 12px 24px rgba(0, 70, 46, 0.3);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .floating-sidebar-toggle:hover {
        transform: translateY(-1px);
        box-shadow: 0 15px 28px rgba(0, 70, 46, 0.34);
    }

    .floating-notification-shell {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 65;
    }

    .floating-notification-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.85rem;
        height: 2.85rem;
        padding: 0;
        border: 2px solid rgba(255, 255, 255, 0.95);
        border-radius: 9999px;
        background: linear-gradient(135deg, #057C3C 0%, #00462E 100%);
        color: #ffffff;
        box-shadow: 0 12px 24px rgba(0, 70, 46, 0.3);
        line-height: 1;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .floating-notification-button:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #06984a 0%, #005838 100%);
        box-shadow: 0 15px 28px rgba(0, 70, 46, 0.34);
    }

    .floating-notification-button:focus-visible {
        outline: 2px solid rgba(5, 124, 60, 0.35);
        outline-offset: 3px;
        border-radius: 9999px;
    }

    .floating-notification-button .notification-bell {
        font-size: 1.3rem;
    }

    .notification-dropdown {
        border: 1px solid #cfe5d8;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.18);
    }

    @media (max-width: 768px) {
        .floating-sidebar-toggle {
            top: 0.85rem;
            left: 0.85rem;
            width: 2.7rem;
            height: 2.7rem;
        }
        .floating-notification-shell {
            top: 0.85rem;
            right: 0.85rem;
        }

        .floating-notification-button {
            width: 2.7rem;
            height: 2.7rem;
        }

        .floating-notification-button .notification-bell {
            font-size: 1.2rem;
        }
    }

    .header-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    body.overflow-hidden .floating-sidebar-toggle,
    body.overflow-hidden .floating-notification-button {
        filter: blur(2px);
        opacity: 0.55;
        pointer-events: none;
    }
    .main-content-wrapper {
        width: 100%;
        margin-left: 0;
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    </style>
</head>

<body class="font-poppins antialiased text-sm"
      x-data="{
          confirmLogout: false,
          isDesktop: window.matchMedia('(min-width: 768px)').matches,
          openSidebar: (() => {
              try {
                  const desktop = window.matchMedia('(min-width: 768px)').matches;
                  const saved = sessionStorage.getItem('sidebarOpen');
                  if (!desktop) return false;
                  return saved === null ? true : saved === 'true';
              } catch (error) {
                  return window.matchMedia('(min-width: 768px)').matches;
              }
          })()
      }"
      x-init="const mediaQuery = window.matchMedia('(min-width: 768px)');
              const syncViewportState = () => {
                  isDesktop = mediaQuery.matches;
                  if (!isDesktop) {
                      openSidebar = false;
                      return;
                  }
                  const saved = sessionStorage.getItem('sidebarOpen');
                  openSidebar = saved === null ? true : saved === 'true';
              };
              syncViewportState();
              if (typeof mediaQuery.addEventListener === 'function') {
                  mediaQuery.addEventListener('change', syncViewportState);
              } else {
                  mediaQuery.addListener(syncViewportState);
              }
              requestAnimationFrame(() => document.documentElement.removeAttribute('data-sidebar-preinit'));
              $watch('openSidebar', value => {
                  if (isDesktop) {
                      sessionStorage.setItem('sidebarOpen', value ? 'true' : 'false');
                  }
              });
              const scrollKey = 'sidebarScrollTop';
              const sidebarScroll = $refs.sidebarScroll;
              if (sidebarScroll) {
                  const saved = sessionStorage.getItem(scrollKey);
                  if (saved !== null) sidebarScroll.scrollTop = parseInt(saved, 10) || 0;
                  sidebarScroll.addEventListener('scroll', () => {
                      sessionStorage.setItem(scrollKey, sidebarScroll.scrollTop);
                  }, { passive: true });
              }"
      :class="{ 'overflow-hidden': confirmLogout }"
      @keydown.escape.window="openSidebar = false; confirmLogout = false">

<div class="min-h-screen flex">

    <aside class="sidebar-gradient fixed inset-y-0 left-0 z-50 transform transition-all duration-300 -translate-x-full md:translate-x-0"
           :class="isDesktop ? (openSidebar ? 'sidebar-expanded translate-x-0' : 'sidebar-collapsed translate-x-0') : (openSidebar ? 'translate-x-0' : '-translate-x-full')"
           @click.capture="if (!isDesktop && $event.target.closest('a[href]')) { openSidebar = false; }">

        <div class="sidebar-content">
            <div class="sidebar-brand">
                <img src="{{ asset('images/ret-logoo.png') }}" alt="RET Cafeteria Logo">
            </div>

            <div class="sidebar-shell flex-1 overflow-hidden">
                <div class="h-full overflow-y-auto sidebar-scroll" x-ref="sidebarScroll">
                    <div class="role-badge mx-3 mt-3 mb-1 rounded-lg border border-[#d0e2d7] bg-[#f4faf6] px-3 py-2 text-[11px] uppercase tracking-[0.14em] text-[#6a7f72]">
                        Role:
                        <span class="text-[#057C3C] font-semibold">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>

                    <nav class="nav-section">
                        <div class="section-header">
                            Management
                        </div>

                        @if(Auth::user()->role === 'superadmin')
                            <a href="{{ route('superadmin.users') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('superadmin.users') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="far fa-user"></i>
                                </span>
                                <span>Manage Users</span>
                            </a>
                        @endif

                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                            <a href="{{ route('admin.dashboard') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.dashboard') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('admin.reservations') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.reservations*') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="far fa-calendar-check"></i>
                                </span>
                                <span>Reservations</span>
                            </a>

                            <a href="{{ route('admin.payments.index') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.payments.*') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </span>
                                <span>Payments</span>
                            </a>

                            <a href="{{ route('admin.reports.index') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.reports.index') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </span>
                                <span>Reports</span>
                            </a>

                            <a href="{{ route('admin.inventory.index') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.inventory.index') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-boxes-stacked"></i>
                                </span>
                                <span>Inventory</span>
                            </a>

                            <a href="{{ route('admin.menus.index', ['type' => 'standard', 'meal' => 'breakfast']) }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-utensils"></i>
                                </span>
                                <span>Manage Menus</span>
                            </a>

                            <a href="{{ route('admin.menus.prices') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.menus.prices') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="fas fa-peso-sign"></i>
                                </span>
                                <span>Manage Prices</span>
                            </a>

                            <a href="{{ route('admin.calendar') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.calendar') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="far fa-calendar-days"></i>
                                </span>
                                <span>Calendar</span>
                            </a>

                            <a href="{{ route('admin.messages.index') }}"
                               wire:navigate
                               class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('admin.messages.*') ? 'active-menu-item' : '' }}">
                                <span class="menu-icon">
                                    <i class="far fa-envelope"></i>
                                </span>
                                <span>Messages</span>
                            </a>
                        @endif

                        <div class="section-header">
                            Settings
                        </div>

                        <a href="{{ route('profile.edit') }}"
                           wire:navigate
                           class="menu-item flex items-center gap-3 px-4 py-2.5 transition-all duration-200 ease-out {{ request()->routeIs('profile.edit') ? 'active-menu-item' : '' }}">
                            <span class="menu-icon">
                                <i class="fas fa-gear"></i>
                            </span>
                            <span>Account Settings</span>
                        </a>
                    </nav>
                </div>
            </div>

            <div class="logout-section">
                <button @click="confirmLogout = true"
                        class="w-full flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-semibold text-white bg-[#dc2626] border border-[#b91c1c] shadow-sm hover:bg-[#b91c1c] hover:border-[#991b1b] transition-all duration-200">
                    <i class="fas fa-right-from-bracket"></i>
                    <span class="logout-text">Logout</span>
                </button>
            </div>
        </div>
    </aside>

    <button type="button"
            @click="openSidebar = !openSidebar"
            class="floating-sidebar-toggle"
            :aria-label="openSidebar ? 'Close sidebar' : 'Open sidebar'"
            :title="openSidebar ? 'Close sidebar' : 'Open sidebar'">
        <i class="fas text-base" :class="openSidebar ? 'fa-xmark' : 'fa-bars'"></i>
    </button>

    <div class="floating-notification-shell" x-data="notificationsPanel()" x-init="init()">
        <button @click="open = !open"
                :aria-expanded="open.toString()"
                class="floating-notification-button header-transition relative"
                aria-label="Toggle notifications">
            <i class="fa-regular fa-bell notification-bell" aria-hidden="true"></i>
            <span x-show="unreadCount > 0"
                  x-text="unreadCount > 99 ? '99+' : unreadCount"
                  class="absolute -top-1 -right-2 min-w-[1.25rem] h-5 px-1.5 rounded-full bg-[#FB3E05] text-white text-[0.65rem] font-semibold flex items-center justify-center shadow"
                  x-cloak></span>
        </button>
        <div x-show="open"
             @click.outside="open = false"
             x-transition.opacity.scale.90
             class="notification-dropdown absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-lg z-50 header-transition"
             x-cloak>
            <div class="p-4 border-b border-[#d8e9df] flex items-center justify-between">
                <span class="font-semibold text-[#184c35]">Notifications</span>
                <button type="button" class="text-xs text-[#2d7853] hover:text-[#00462E]" @click="markAllRead" x-show="unreadCount > 0">
                    Mark all read
                </button>
            </div>
            <ul class="max-h-72 overflow-y-auto">
                <template x-if="loading">
                    <li class="px-4 py-3 text-[#4a6d5a]">Loading notifications...</li>
                </template>
                <template x-if="!loading && items.length === 0">
                    <li class="px-4 py-3 text-[#4a6d5a]">No new notifications</li>
                </template>
                <template x-for="item in items" :key="item.id">
                    <li class="px-4 py-3 border-b border-[#edf4ef] last:border-b-0 hover:bg-[#f4faf6] transition-colors">
                        <div class="flex items-start gap-3">
                            <span class="mt-2 w-2 h-2 rounded-full"
                                  :class="item.read ? 'bg-gray-300' : 'bg-[#FB3E05]'"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[#163f2d]" x-text="item.actor"></p>
                                <p class="text-sm text-[#3f624f]" x-text="item.description"></p>
                                <p class="text-xs text-[#6f8f7e] mt-1" x-text="item.time"></p>
                                <button type="button"
                                        class="mt-2 text-xs text-[#057C3C] hover:text-[#00462E]"
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

    <div class="flex-1 flex flex-col main-content-wrapper" :class="isDesktop ? (openSidebar ? 'sidebar-offset' : 'sidebar-collapsed-offset') : ''">
        <main class="admin-shell admin-surface p-4 sm:p-6 overflow-y-auto flex-1 pt-20">
            @yield('content')
        </main>
    </div>
</div>

{{-- Unified admin toasts: success/error/warning. ESC to clear. --}}
<x-admin.ui.toast-container />

@if(session('success') && empty($disableAdminSuccessToast))
<script>document.addEventListener('livewire:navigated', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'success', message: @json(session('success')) } })); });</script>
@endif
@if(session('error'))
<script>document.addEventListener('livewire:navigated', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'error', message: @json(session('error')) } })); });</script>
@endif
@if(session('warning'))
<script>document.addEventListener('livewire:navigated', function() { window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: 'warning', message: @json(session('warning')) } })); });</script>
@endif

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

{!! \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts() !!}

<script>
// Global helper to emit admin toasts from pages or inline scripts
window.showAdminToast = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: type, message: message } }));
};

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
document.addEventListener('livewire:navigated', function() {
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

function wrapSelectField(select) {
    if (!select) return null;
    const existing = select.closest('.admin-select-field');
    if (existing) return existing;

    const field = document.createElement('div');
    field.className = 'admin-select-field';

    if (select.parentNode) {
        select.parentNode.insertBefore(field, select);
        field.appendChild(select);
    }

    return field;
}

function ensureSelectIcon(field, select) {
    if (!field || field.querySelector('.admin-select-icon')) return;

    const icon = document.createElement('i');
    icon.className = 'fas fa-chevron-down admin-select-icon';
    icon.setAttribute('aria-hidden', 'true');
    field.appendChild(icon);

    if (select) {
        select.classList.add('admin-select--icon');
    }
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

    const field = wrapSelectField(select);
    ensureSelectIcon(field, select);
}

function enhanceAdminSelects(root = document) {
    const selects = root.querySelectorAll('select[data-admin-select="true"]');
    selects.forEach((select) => enhanceAdminSelect(select));
}

document.addEventListener('livewire:navigated', () => {
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
</script>

{{-- Auto-logout when user is idle for 5 minutes (300000 ms) --}}
@auth
<script>
    (function(){
        const IDLE_TIMEOUT = 5 * 60 * 1000; // 5 minutes
        let idleTimer = null;

        function doLogout() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('logout') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            }).finally(() => {
                window.location.href = "{{ route('login') }}";
            });
        }

        function resetTimer() {
            if (idleTimer) clearTimeout(idleTimer);
            idleTimer = setTimeout(doLogout, IDLE_TIMEOUT);
        }

        ['mousemove','mousedown','keydown','scroll','touchstart'].forEach(evt => {
            document.addEventListener(evt, resetTimer, true);
        });

        // Start timer
        resetTimer();
    })();
</script>
@endauth
</body>
</html>
