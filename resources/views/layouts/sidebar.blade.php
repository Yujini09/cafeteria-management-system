<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/Cafeteria-logo.png') }}">


    <title>{{ config('app.name', 'Smart Cafeteria') }}</title>

    <script>
        (function () {
            try {
                var desktopQuery = window.matchMedia('(min-width: 768px)');
                var storageKey = desktopQuery.matches ? 'cms.sidebar.desktop.open' : 'cms.sidebar.mobile.open';
                var stored = window.localStorage.getItem(storageKey);
                var root = document.documentElement;
                root.classList.remove('sidebar-prefers-open', 'sidebar-prefers-closed');
                if (stored === '0' || stored === 'false') {
                    root.classList.add('sidebar-prefers-closed');
                } else if (stored === '1' || stored === 'true') {
                    root.classList.add('sidebar-prefers-open');
                }
            } catch (error) {}
        })();
    </script>

    {{-- FontAwesome and Google Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles() !!}

    <style>
        /* Google Font Link */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            /* --- Theme Colors --- */
            --sidebar-bg: #ffffff;
            --sidebar-border: #eef2f6;
            --text-default: #334155;
            --text-hover: #0f172a;
            --bg-hover: #f8fafc;
            --active-bg: #00462E; 
            --active-text: #ffffff;
            --header-text: #64748b;
            --icon-bg-default: #f1f5f9;
            --icon-border-default: #e2e8f0;
            --icon-color-default: #475569;
            --main-bg: #ffffff; 

            /* --- Dimensions --- */
            --sidebar-width: 278px;
            --sidebar-collapsed-width: 88px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--main-bg);
            overflow-x: hidden;
            height: 100vh;
        }

        body.sidebar-state-initializing .sidebar,
        body.sidebar-state-initializing .home-section {
            transition: none !important;
        }

        /* --- SIDEBAR CONTAINER --- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            height: 100dvh;
            width: var(--sidebar-width);
            background: transparent;
            border-right: none;
            z-index: 100;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: none;
            padding: 0.65rem 0.55rem;
            overflow: hidden;
        }

        html.sidebar-prefers-closed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        body.sidebar-modal-active .sidebar {
            filter: blur(2.5px);
            pointer-events: none;
            user-select: none;
        }

        .sidebar.close {
            width: var(--sidebar-collapsed-width);
        }

        /* Allow tooltips/badges to escape sidebar in collapsed state */
        .sidebar.close {
            overflow: visible !important;
        }

        .sidebar.close .sidebar-menu,
        .sidebar.close .menu-list,
        .sidebar.close .menu-list-item {
            overflow: visible !important;
        }

        /* --- BRAND / LOGO AREA --- */
        .sidebar-brand {
            flex-shrink: 0;
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 0.75rem;
            background: linear-gradient(135deg, #00462E 0%, #10b981 100%);
            color: white;
            border: 1px solid var(--sidebar-border);
            border-radius: 14px;
        }

        .sidebar-brand img {
            height: 44px;
            max-width: 92%;
            width: auto;
            display: block;
            transition: all 0.3s ease;
        }

        .sidebar-toggle-row {
            position: absolute;
            top: 38px;
            right: 2px;
            left: auto;
            width: auto;
            transform: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            pointer-events: none;
            z-index: 120;
        }

        #sidebar-toggle-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            background: linear-gradient(135deg, var(--active-bg) 0%, #0b7d53 100%);
            font-size: 16px;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            color: #ffffff;
            border-radius: 9999px;
            transition: all 0.2s;
            box-shadow: 0 6px 14px rgba(0, 70, 46, 0.35), 0 0 0 2px rgba(255, 255, 255, 0.95);
            pointer-events: auto;
        }
        #sidebar-toggle-btn:hover {
            background: linear-gradient(135deg, #005238 0%, #0b8c5c 100%);
            box-shadow: 0 8px 18px rgba(0, 70, 46, 0.42), 0 0 0 2px rgba(255, 255, 255, 1);
            transform: scale(1.05);
        }
        #sidebar-toggle-btn:focus-visible {
            outline: 2px solid #ffffff;
            outline-offset: 2px;
        }

        .sidebar.close .sidebar-brand {
            display: none;
        }

        .sidebar.close .sidebar-toggle-row {
            position: relative;
            top: auto;
            right: auto;
            left: auto;
            width: 100%;
            min-height: 78px;
            margin-bottom: 0.45rem;
            border: 1px solid var(--sidebar-border);
            border-radius: 14px;
            background: linear-gradient(135deg, #00462E 0%, #10b981 100%);
            pointer-events: auto;
        }

        .sidebar.close #sidebar-toggle-btn {
            width: 40px;
            height: 40px;
        }

        /* --- NAVIGATION LIST --- */
        .sidebar-menu {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding: 0.45rem;
            margin-top: 0.45rem;
            margin-bottom: 0.45rem;
            scrollbar-width: thin;
            scrollbar-color: var(--sidebar-border) transparent;
            max-height: none;
            background: var(--sidebar-bg);
            border: 1px solid var(--sidebar-border);
            border-radius: 14px;
        }
        
        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--sidebar-border);
            border-radius: 4px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: var(--header-text);
        }

        /* Section Headers */
        .section-header {
            margin: 0.5rem 0.75rem 0.2rem;
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--header-text);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
            opacity: 0.8;
        }
        
        .sidebar.close .section-header {
            margin: 0;
            padding: 0;
            height: 0;
            opacity: 0;
            pointer-events: none;
            overflow: hidden;
        }
        
        .sidebar.close .section-header::after {
            display: none;
        }

        /* --- MENU ITEMS --- */
        .menu-list {
            list-style: none;
            padding: 0;
        }

        .menu-list-item {
            position: relative;
            margin: 3px 0;
        }

        .menu-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            text-decoration: none;
            min-height: 38px;
            padding: 0.35rem 0.45rem;
            border-radius: 10px;
            color: var(--text-default);
            font-weight: 500;
            font-size: 0.85rem;
            position: relative;
            transition: all 0.2s ease;
        }

        .sidebar.close .menu-link {
            justify-content: center;
            padding: 0.35rem 0.2rem;
        }

        .menu-link:hover {
            background: var(--bg-hover);
            color: var(--text-hover);
            transform: translateX(1px);
        }

        .menu-link.active {
            background: var(--active-bg);
            color: var(--active-text);
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
        }

        .menu-dropdown-toggle {
            width: 100%;
            border: 0;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .menu-caret {
            margin-left: auto;
            font-size: 0.75rem;
            color: var(--header-text);
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .menu-link:hover .menu-caret {
            color: var(--text-hover);
        }

        .menu-link.active .menu-caret {
            color: var(--active-text);
        }

        .menu-caret-open {
            transform: rotate(180deg);
        }

        .sidebar.close .menu-link.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0;
        }

        /* --- ICONS --- */
        .menu-icon {
            height: 28px;
            min-width: 28px;
            width: 28px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--icon-bg-default);
            border: 1px solid var(--icon-border-default);
            color: var(--icon-color-default);
            border-radius: 8px;
            margin-right: 0.55rem;
        }

        .sidebar.close .menu-icon {
            margin-right: 0;
        }

        .menu-link:hover .menu-icon {
            background: #ffffff;
            color: var(--active-bg);
            border-color: var(--active-bg);
        }
        .menu-link.active .menu-icon {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--active-text);
        }

        /* --- LINK TEXT --- */
        .link-text {
            font-weight: 500;
            white-space: nowrap;
            font-size: 0.85rem;
        }

        .submenu-list {
            list-style: none;
            padding: 0 0 0 0.55rem;
            margin: 0.2rem 0 0.3rem 0;
        }

        .submenu-list .menu-link {
            padding: 0.3rem 0.4rem;
        }

        .submenu-list .menu-icon {
            height: 28px;
            min-width: 28px;
            width: 28px;
            margin-right: 0.5rem;
        }

        .message-count-badge {
            margin-left: auto;
            min-width: 1.2rem;
            height: 1.2rem;
            padding: 0 0.35rem;
            border-radius: 9999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        .inventory-warning-badge {
            margin-left: auto;
            min-width: 1.2rem;
            height: 1.2rem;
            padding: 0 0.35rem;
            border-radius: 9999px;
            background: #f59e0b;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        .sidebar.close .link-text {
            opacity: 0;
            pointer-events: none;
            display: none;
        }

        /* --- NOTIFICATION BADGE --- */
        .menu-badge {
            background-color: #ef4444; /* red-500 */
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            margin-left: auto;
            line-height: 1;
            min-width: 1.25rem;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.25rem;
            transition: all 0.2s ease;
        }

        /* Collapsed state badge (APPLIED TO BOTH RED AND YELLOW BADGES) */
        .sidebar.close .menu-badge,
        .sidebar.close .inventory-warning-badge {
            position: absolute;
            top: 2px;
            right: 12px;
            margin: 0;
            padding: 0 4px;
            min-width: 18px;
            height: 18px;
            font-size: 0.65rem;
            border-radius: 99px;
            border: 2px solid var(--sidebar-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }

        /* Collapsed State + Hover: Floating tooltip */
        .sidebar.close .menu-list-item:hover .link-text {
            position: fixed !important;
            display: block !important;
            background: var(--active-bg);
            color: var(--active-text);
            padding: 0.3rem 0.6rem;
            border-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            opacity: 1 !important;
            pointer-events: none;
            z-index: 9999 !important;
            font-weight: 500;
            font-size: 0.8rem;
            white-space: nowrap;
            visibility: visible !important;
        }
        
        .sidebar.close .menu-list-item:hover .link-text::before {
            content: '';
            position: absolute;
            left: -4px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            border-right: 4px solid var(--active-bg);
        }

        /* --- PROFILE / LOGOUT SECTION --- */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 0.45rem;
            background: var(--sidebar-bg);
            border: 1px solid var(--sidebar-border);
            border-radius: 12px;
            margin-top: auto;
        }
        
        .role-badge {
            display: none;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 10px;
            background: #ef4444;
            color: white;
            font-weight: 500;
            font-size: 0.85rem;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            opacity: 0.9;
        }
        .logout-btn:hover { 
            background: #dc2626;
            opacity: 1;
        }
        
        .logout-btn i { font-size: 15px; }
        .logout-btn span { margin-left: 0.4rem; }

        .sidebar.close .logout-btn {
            padding: 0.4rem 0;
            justify-content: center;
            display: flex !important;
            background: #ef4444;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            margin: 0 auto;
        }

        .sidebar.close .logout-btn span {
            display: none !important;
        }

        .sidebar.close .logout-btn i {
            display: block !important;
            margin: 0;
            font-size: 15px;
        }

        .sidebar.close .sidebar-footer {
            padding: 0.4rem;
            display: flex;
            justify-content: center;
        }

        /* --- MAIN CONTENT ADJUSTMENT --- */
        .home-section {
            position: relative;
            background: var(--main-bg);
            min-height: 100vh;
            top: 0;
            left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
            padding: 0; 
            display: flex;
            flex-direction: column;
        }

        html.sidebar-prefers-closed .home-section {
            left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        .sidebar.close ~ .home-section {
            left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        /* Admin Shell - Content Container */
        .admin-shell {
            padding: 1.5rem; 
            flex: 1;
            overflow-y: auto;
        }

        /* Notification Bell Styles */
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
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 2px solid #ffffff;
            background: linear-gradient(135deg, #00462E 0%, #10b981 100%);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .notification-bell {
            font-size: 1.375rem;
            line-height: 1;
        }

        .mobile-sidebar-toggle {
            display: none;
            position: fixed;
            top: 0.65rem;
            left: 10px;
            right: auto;
            transform: none;
            z-index: 130;
            width: 42px;
            height: 42px;
            border: 2px solid #ffffff;
            border-radius: 9999px;
            cursor: pointer;
            color: #ffffff;
            background: linear-gradient(135deg, var(--active-bg) 0%, #0b7d53 100%);
            box-shadow: 0 8px 18px rgba(0, 70, 46, 0.38), 0 0 0 2px rgba(255, 255, 255, 0.95);
        }

        .mobile-sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 110;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(1px);
        }

        @media (max-width: 767.98px) {
            .sidebar-toggle-row {
                display: none;
            }

            .sidebar.close .sidebar-toggle-row {
                display: none;
            }

            .sidebar {
                width: var(--sidebar-width);
                transform: translateX(calc(-100% - 8px));
                z-index: 120;
                box-shadow: none;
                padding: 0.5rem;
                background: var(--sidebar-bg);
                border-right: 1px solid var(--sidebar-border);
            }

            .sidebar.close {
                width: var(--sidebar-width);
                transform: translateX(calc(-100% - 8px));
            }

            html.sidebar-prefers-open .sidebar {
                transform: translateX(0);
            }

            html.sidebar-prefers-closed .sidebar {
                width: var(--sidebar-width);
                transform: translateX(calc(-100% - 8px));
            }

            .sidebar:not(.close) {
                transform: translateX(0);
                box-shadow: 8px 0 24px rgba(15, 23, 42, 0.2);
            }

            .home-section,
            .sidebar.close ~ .home-section {
                left: 0;
                width: 100%;
            }

            .admin-shell {
                padding: 1rem;
            }

            .mobile-sidebar-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .mobile-sidebar-overlay {
                display: block;
            }
        }
    </style>
</head>

@php
    $menusAndPricingActive = (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') || request()->routeIs('admin.menus.prices');
@endphp

<body class="font-poppins antialiased text-sm sidebar-state-initializing"
      x-data="{
          confirmLogout: false,
          sidebarStorageDesktopKey: 'cms.sidebar.desktop.open',
          sidebarStorageMobileKey: 'cms.sidebar.mobile.open',
          isDesktop: window.matchMedia('(min-width: 768px)').matches,
          openSidebar: (() => {
              const isDesktopViewport = window.matchMedia('(min-width: 768px)').matches;
              const storageKey = isDesktopViewport ? 'cms.sidebar.desktop.open' : 'cms.sidebar.mobile.open';
              try {
                  const stored = window.localStorage.getItem(storageKey);
                  if (stored === '1' || stored === 'true') return true;
                  if (stored === '0' || stored === 'false') return false;
              } catch (error) {}
              return isDesktopViewport;
          })(),
          menusPricingOpen: {{ $menusAndPricingActive ? 'true' : 'false' }},
          readSidebarState(storageKey, fallbackValue) {
              try {
                  const stored = window.localStorage.getItem(storageKey);
                  if (stored === '1' || stored === 'true') return true;
                  if (stored === '0' || stored === 'false') return false;
              } catch (error) {}
              return fallbackValue;
          },
          writeSidebarState(storageKey, value) {
              try {
                  window.localStorage.setItem(storageKey, value ? '1' : '0');
              } catch (error) {}
          },
          syncRootSidebarState(value) {
              const root = document.documentElement;
              root.classList.toggle('sidebar-prefers-open', !!value);
              root.classList.toggle('sidebar-prefers-closed', !value);
          }
      }"
      x-init="
          const mq = window.matchMedia('(min-width: 768px)');
          const syncSidebarWithViewport = (event) => {
              const nextDesktop = event.matches;
              const viewportChanged = isDesktop !== nextDesktop;
              isDesktop = nextDesktop;
              if (viewportChanged) {
                  const key = isDesktop ? sidebarStorageDesktopKey : sidebarStorageMobileKey;
                  openSidebar = readSidebarState(key, isDesktop);
                  syncRootSidebarState(openSidebar);
              }
          };
          const initialKey = isDesktop ? sidebarStorageDesktopKey : sidebarStorageMobileKey;
          openSidebar = readSidebarState(initialKey, isDesktop);
          syncRootSidebarState(openSidebar);
          $watch('openSidebar', (value) => {
              const key = isDesktop ? sidebarStorageDesktopKey : sidebarStorageMobileKey;
              writeSidebarState(key, value);
              syncRootSidebarState(value);
          });
          if (mq.addEventListener) {
              mq.addEventListener('change', syncSidebarWithViewport);
          } else {
              mq.addListener(syncSidebarWithViewport);
          }
          requestAnimationFrame(() => { $el.classList.remove('sidebar-state-initializing'); });
      "
      x-on:keydown.escape.window="if (!isDesktop && openSidebar) openSidebar = false">

    <button type="button"
            class="mobile-sidebar-toggle"
            x-show="!isDesktop && !openSidebar"
            @click="openSidebar = true"
            aria-label="Open sidebar"
            x-cloak>
        <span aria-hidden="true">&gt;</span>
    </button>

    <div class="mobile-sidebar-overlay"
         x-show="!isDesktop && openSidebar"
         @click="openSidebar = false"
         x-transition.opacity
         x-cloak></div>

    @php
        // Fetch Counts for Notifications
        $unreadMessagesCount = $sidebarUnreadMessagesCount ?? 0;
        $pendingReservationsCount = $sidebarPendingReservationsCount ?? 0;
    @endphp

    <aside class="sidebar" :class="!openSidebar ? 'close' : ''">
        <div class="sidebar-brand">
            <img src="{{ asset('images/ret-logoo.png') }}" alt="RET Logo">
        </div>
        <div class="sidebar-toggle-row">
            <button type="button" id="sidebar-toggle-btn" @click="openSidebar = !openSidebar"
                    :aria-label="openSidebar ? 'Collapse sidebar' : 'Expand sidebar'">
                <span x-text="openSidebar ? '<' : '>'"></span>
            </button>
        </div>
        
        <div class="sidebar-menu" @click="if (!isDesktop && $event.target.closest('.menu-link')) { openSidebar = false; }">
            <ul class="menu-list">
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin')
                <li class="menu-list-item">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                        <span class="link-text">Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->role === 'superadmin')
                <li class="menu-list-item">
                    <a href="{{ route('superadmin.users') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('superadmin.users') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="far fa-user"></i></span>
                        <span class="link-text">Manage Users</span>
                    </a>
                </li>
                @endif
                <li class="menu-list-item">
                    <a href="{{ route('admin.reservations') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.reservations*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="far fa-calendar-check"></i></span>
                        <span class="link-text">Reservations</span>
                        {{-- Reservations Badge --}}
                        @if($pendingReservationsCount > 0)
                            <span class="menu-badge">
                                {{ $pendingReservationsCount > 99 ? '99+' : $pendingReservationsCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="menu-list-item">
                    <a href="{{ route('admin.inventory.index') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-boxes-stacked"></i></span>
                        <span class="link-text">Inventory</span>
                        <span
                            id="sidebarInventoryWarningBadge"
                            class="inventory-warning-badge {{ ($sidebarInventoryWarningCount ?? 0) > 0 ? '' : 'hidden' }}"
                            aria-label="{{ $sidebarInventoryWarningCount ?? 0 }} inventory warnings"
                        >
                            {{ $sidebarInventoryWarningCount > 99 ? '99+' : $sidebarInventoryWarningCount }}
                        </span>
                    </a>
                </li>
                <li class="menu-list-item" x-show="openSidebar" x-cloak>
                    <button type="button"
                            @click.stop="menusPricingOpen = !menusPricingOpen"
                            :aria-expanded="menusPricingOpen.toString()"
                            class="menu-link menu-dropdown-toggle {{ $menusAndPricingActive ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                        <span class="link-text">Menus &amp; Pricing</span>
                        <span class="menu-caret" :class="menusPricingOpen ? 'menu-caret-open' : ''">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </button>
                    <ul x-show="menusPricingOpen"
                        x-cloak
                        x-transition.opacity.duration.150ms
                        class="submenu-list">
                        <li class="menu-list-item">
                            <a href="{{ route('admin.menus.index', ['type' => 'standard', 'meal' => 'breakfast']) }}" wire:navigate
                               class="menu-link {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'active' : '' }}">
                                <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                                <span class="link-text">Manage Menus</span>
                            </a>
                        </li>
                        <li class="menu-list-item">
                            <a href="{{ route('admin.menus.prices') }}" wire:navigate
                               class="menu-link {{ request()->routeIs('admin.menus.prices') ? 'active' : '' }}">
                                <span class="menu-icon"><i class="fas fa-peso-sign"></i></span>
                                <span class="link-text">Prices</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-list-item" x-show="!openSidebar" x-cloak>
                    <a href="{{ route('admin.menus.index', ['type' => 'standard', 'meal' => 'breakfast']) }}" wire:navigate
                       class="menu-link {{ (request()->routeIs('admin.menus.*') && !request()->routeIs('admin.menus.prices')) || request()->routeIs('admin.recipes.index') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-utensils"></i></span>
                        <span class="link-text">Manage Menus</span>
                    </a>
                </li>
                <li class="menu-list-item" x-show="!openSidebar" x-cloak>
                    <a href="{{ route('admin.menus.prices') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.menus.prices') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-peso-sign"></i></span>
                        <span class="link-text">Prices</span>
                    </a>
                </li>
                <li class="menu-list-item">
                    <a href="{{ route('admin.reports.index') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-chart-pie"></i></span>
                        <span class="link-text">Reports</span>
                    </a>
                </li>
                <li class="menu-list-item">
                    <a href="{{ route('admin.calendar') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="far fa-calendar-days"></i></span>
                        <span class="link-text">Calendar</span>
                    </a>
                </li>
                <li class="menu-list-item">
                    <a href="{{ route('admin.messages.index') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="far fa-envelope"></i></span>
                        <span class="link-text">Messages</span>
                        {{-- Messages Badge --}}
                        @if($unreadMessagesCount > 0)
                            <span class="menu-badge">
                                {{ $unreadMessagesCount > 99 ? '99+' : $unreadMessagesCount }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="menu-list-item">
                    <a href="{{ route('admin.feedbacks.index') }}" wire:navigate
                    class="menu-link {{ request()->routeIs('admin.feedbacks.*') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-comments"></i></span>
                        <span class="link-text">Feedbacks</span>
                    </a>
                </li>
                <li class="menu-list-item">
                    <a href="{{ route('profile.edit') }}" wire:navigate
                       class="menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <span class="menu-icon"><i class="fas fa-gear"></i></span>
                        <span class="link-text">Account Settings</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-footer">
            <button @click="confirmLogout = true" class="logout-btn">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <section class="home-section">
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
                 class="notification-dropdown absolute right-0 mt-2 w-80 max-w-[90vw] bg-white rounded-lg z-50 header-transition shadow-lg border border-gray-200"
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
                                            class="mt-2 text-xs text-[#059669] hover:text-[#00462E]"
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

        <main class="admin-shell admin-surface">
            @yield('content')
        </main>
    </section>

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

    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin'], true))
        <x-admin.ui.modal name="inventory-alerts" variant="warning" maxWidth="lg">
            <div>
                <div class="flex items-start justify-between gap-4 border-b border-admin-neutral-100 pb-4">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin bg-admin-warning-light text-admin-warning">
                            <x-admin.ui.icon name="fa-box-open" size="sm" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-admin-neutral-900">Inventory Alerts</h2>
                            <p class="mt-1 text-sm text-admin-neutral-600">The following inventory items need attention.</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-admin text-admin-neutral-500 transition-colors duration-admin hover:bg-admin-neutral-100 hover:text-admin-neutral-700"
                        onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'inventory-alerts' }))"
                        aria-label="Close inventory alerts"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    id="adminInventoryAlertsList"
                    class="mt-4 max-h-80 space-y-3 overflow-y-auto"
                    aria-live="polite"
                    aria-busy="false"
                >
                    <p id="adminInventoryAlertsEmptyState" class="text-sm text-admin-neutral-600">No active inventory alerts.</p>
                </div>
            </div>

            <x-slot name="footer">
                <x-admin.ui.button.secondary
                    type="button"
                    onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'inventory-alerts' }))"
                >
                    Dismiss
                </x-admin.ui.button.secondary>
                <x-admin.ui.button.primary
                    type="button"
                    onclick="window.location.href='{{ route('admin.inventory.index') }}'"
                >
                    Proceed to Inventory
                </x-admin.ui.button.primary>
            </x-slot>
        </x-admin.ui.modal>
    @endif

    <div x-show="confirmLogout"
         class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[200]"
         x-transition.opacity
         x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 text-black transform transition-all duration-300 scale-95"
             @click.outside="confirmLogout = false">
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
    function positionTooltips() {
        if (!document.querySelector('.sidebar.close')) return;
        const menuItems = document.querySelectorAll('.sidebar.close .menu-list-item');
        menuItems.forEach(item => {
            item.removeEventListener('mouseenter', positionTooltipHandler);
            item.addEventListener('mouseenter', positionTooltipHandler);
        });
    }

    function positionTooltipHandler(e) {
        const item = this;
        const tooltip = item.querySelector('.link-text');
        if (!tooltip) return;
        const icon = item.querySelector('.menu-icon');
        if (!icon) return;
        const rect = icon.getBoundingClientRect();
        const sidebarWidth = 72;
        tooltip.style.left = (sidebarWidth + 10) + 'px';
        tooltip.style.top = (rect.top + (rect.height / 2) - 10) + 'px';
    }

    let sidebarModalBlurObserver = null;
    const ADMIN_MODAL_Z_INDEX = '220';

    function isElementVisible(el) {
        if (!el || el.hidden) return false;
        const style = window.getComputedStyle(el);
        if (style.display === 'none' || style.visibility === 'hidden') return false;
        const rect = el.getBoundingClientRect();
        return rect.width > 0 && rect.height > 0;
    }

    function elementLooksLikeModalOverlay(el) {
        if (!el) return false;
        if (el.classList.contains('reservation-modal-overlay')) return true;
        if (!(el.classList.contains('fixed') && el.classList.contains('inset-0'))) return false;

        const className = typeof el.className === 'string' ? el.className : '';
        return className.includes('backdrop-blur')
            || className.includes('justify-center')
            || className.includes('z-')
            || Boolean(el.querySelector('[aria-modal="true"]'));
    }

    function syncSidebarModalBlurState() {
        const overlays = document.querySelectorAll('.fixed.inset-0, .reservation-modal-overlay');
        let hasVisibleModal = false;

        overlays.forEach((el) => {
            if (!elementLooksLikeModalOverlay(el)) return;

            // Keep every detected modal overlay above the sidebar stack.
            el.style.setProperty('z-index', ADMIN_MODAL_Z_INDEX, 'important');

            if (isElementVisible(el)) {
                hasVisibleModal = true;
            }
        });

        document.body.classList.toggle('sidebar-modal-active', hasVisibleModal);
    }

    function initSidebarModalBlurSync() {
        if (!document.body) return;

        if (!sidebarModalBlurObserver) {
            sidebarModalBlurObserver = new MutationObserver(() => {
                syncSidebarModalBlurState();
            });

            sidebarModalBlurObserver.observe(document.body, {
                subtree: true,
                childList: true,
                attributes: true,
                attributeFilter: ['class', 'style', 'hidden', 'open', 'aria-hidden']
            });

            ['open-admin-modal', 'close-admin-modal', 'admin-modal-visibility'].forEach((eventName) => {
                window.addEventListener(eventName, syncSidebarModalBlurState);
            });
        }

        syncSidebarModalBlurState();
    }

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
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => ({ ...item, read: true }));
                    this.updateUnread();
                })
                .catch(error => console.error('Error marking all notifications read:', error));
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
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    this.items = this.items.map(item => item.id === id ? { ...item, read: nextRead } : item);
                    this.updateUnread();
                })
                .catch(error => console.error('Error toggling notification read:', error));
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
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    const uniqueNotifications = (data || []).reduce((seen, notification) => {
                        const exists = seen.find(n => n.id === notification.id);
                        if (!exists) seen.push(notification);
                        return seen;
                    }, []);
                    this.items = uniqueNotifications.map(notification => {
                        const metadata = notification.metadata || {};
                        const actor = metadata.updated_by || metadata.generated_by || metadata.created_by || (notification.user ? notification.user.name : 'System');
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
        const clearButton = document.getElementById('clearSearch');
        if (clearButton) {
            clearButton.style.display = normalizedQuery.length > 0 ? 'block' : 'none';
        }
    }
    
    document.addEventListener('livewire:navigated', function() {
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        if (!searchInput || !clearButton) return;
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            filterTable('');
            searchInput.focus();
        });
    });

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
        if (select) select.classList.add('admin-select--icon');
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
            searchInput.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown') select.focus();
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
        positionTooltips();
        initSidebarModalBlurSync();
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    setTimeout(positionTooltips, 100);
                }
            });
        });
        
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            observer.observe(sidebar, { attributes: true });
        }

        const observer2 = new MutationObserver((mutations) => {
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
        observer2.observe(document.body, { childList: true, subtree: true });
    });

    document.addEventListener('DOMContentLoaded', function() {
        positionTooltips();
        initSidebarModalBlurSync();
    });

    window.addEventListener('resize', function() {
        if (document.querySelector('.sidebar.close')) {
            setTimeout(positionTooltips, 100);
        }
    });
    </script>

    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin'], true))
        <script>
            (function () {
                if (window.__adminInventoryAlertsInitialized) {
                    return;
                }

                window.__adminInventoryAlertsInitialized = true;

                const alertsUrl = @json(route('admin.inventory.alerts'));
                const modalName = 'inventory-alerts';
                const pollIntervalMs = 20000;
                const storageKey = @json('inventory_alert_signature_' . auth()->id());
                const listElementId = 'adminInventoryAlertsList';
                const emptyStateId = 'adminInventoryAlertsEmptyState';
                const badgeId = 'sidebarInventoryWarningBadge';
                let pollingTimer = null;
                let requestInFlight = false;
                let fallbackSignature = '';

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function getStoredSignature() {
                    try {
                        return window.sessionStorage.getItem(storageKey) || '';
                    } catch (error) {
                        return fallbackSignature;
                    }
                }

                function setStoredSignature(signature) {
                    try {
                        if (signature) {
                            window.sessionStorage.setItem(storageKey, signature);
                        } else {
                            window.sessionStorage.removeItem(storageKey);
                        }
                    } catch (error) {
                        fallbackSignature = signature;
                    }
                }

                function getAlertSignature(alerts) {
                    return alerts
                        .map((alert) => `${alert.id}:${alert.status}`)
                        .sort()
                        .join('|');
                }

                function getStatusMeta(status) {
                    if (status === 'out_of_stock') {
                        return {
                            label: 'Out of stock',
                            classes: 'bg-admin-danger-light text-admin-danger',
                        };
                    }

                    if (status === 'low_stock') {
                        return {
                            label: 'Low stock',
                            classes: 'bg-admin-warning-light text-admin-warning',
                        };
                    }

                    return {
                        label: 'Expiring soon',
                        classes: 'bg-blue-100 text-blue-700',
                    };
                }

                function getDetailText(alert) {
                    if (alert.status === 'out_of_stock') {
                        return 'Stock: 0';
                    }

                    if (alert.status === 'low_stock') {
                        return `Stock: ${alert.current_stock} (min ${alert.min_stock})`;
                    }

                    return `Exp: ${alert.expiry_date || 'N/A'}`;
                }

                function getAlertGroup(status) {
                    if (status === 'out_of_stock') {
                        return 'out_of_stock';
                    }

                    if (status === 'low_stock') {
                        return 'low_stock';
                    }

                    return 'expiring_soon';
                }

                function getSafeNumber(value, fallback = 0) {
                    const numeric = Number(value);
                    return Number.isFinite(numeric) ? numeric : fallback;
                }

                function getSafeTime(value) {
                    const parsed = Date.parse(String(value || ''));
                    return Number.isFinite(parsed) ? parsed : Number.POSITIVE_INFINITY;
                }

                function updateSidebarInventoryBadge(count) {
                    const badge = document.getElementById(badgeId);

                    if (!badge) {
                        return;
                    }

                    const normalizedCount = Number.isFinite(count) ? Math.max(0, count) : 0;

                    badge.textContent = normalizedCount > 99 ? '99+' : String(normalizedCount);
                    badge.setAttribute('aria-label', `${normalizedCount} inventory warnings`);
                    badge.classList.toggle('hidden', normalizedCount === 0);
                }

                function renderInventoryAlerts(alerts) {
                    const list = document.getElementById(listElementId);

                    if (!list) {
                        return;
                    }

                    list.setAttribute('aria-busy', 'true');

                    if (!Array.isArray(alerts) || alerts.length === 0) {
                        list.innerHTML = `<p id="${emptyStateId}" class="text-sm text-admin-neutral-600">No active inventory alerts.</p>`;
                        list.setAttribute('aria-busy', 'false');
                        return;
                    }

                    const groupedAlerts = {
                        out_of_stock: [],
                        low_stock: [],
                        expiring_soon: [],
                    };

                    alerts.forEach((alert) => {
                        groupedAlerts[getAlertGroup(alert.status)].push(alert);
                    });

                    groupedAlerts.out_of_stock.sort((a, b) => String(a.name || '').localeCompare(String(b.name || '')));
                    groupedAlerts.low_stock.sort((a, b) => {
                        const stockDiff = getSafeNumber(a.current_stock, Number.POSITIVE_INFINITY) - getSafeNumber(b.current_stock, Number.POSITIVE_INFINITY);
                        if (stockDiff !== 0) {
                            return stockDiff;
                        }
                        return String(a.name || '').localeCompare(String(b.name || ''));
                    });
                    groupedAlerts.expiring_soon.sort((a, b) => {
                        const expDiff = getSafeTime(a.expiry_date) - getSafeTime(b.expiry_date);
                        if (expDiff !== 0) {
                            return expDiff;
                        }
                        return String(a.name || '').localeCompare(String(b.name || ''));
                    });

                    const sections = [
                        { key: 'low_stock', title: 'Low Stock', countClass: 'bg-admin-warning-light text-admin-warning' },
                        { key: 'out_of_stock', title: 'No Stock', countClass: 'bg-admin-danger-light text-admin-danger' },
                        { key: 'expiring_soon', title: 'Expiring Soon', countClass: 'bg-blue-100 text-blue-700' },
                    ];

                    list.innerHTML = sections
                        .filter((section) => groupedAlerts[section.key].length > 0)
                        .map((section) => {
                            const itemsHtml = groupedAlerts[section.key].map((alert) => {
                                return `
                                    <div class="rounded-admin-lg border border-admin-neutral-200 bg-admin-neutral-50 px-4 py-3">
                                        <div>
                                            <p class="text-sm font-semibold text-admin-neutral-900">${escapeHtml(alert.name)}</p>
                                            <p class="mt-1 text-xs text-admin-neutral-600">${escapeHtml(getDetailText(alert))}</p>
                                        </div>
                                    </div>
                                `;
                            }).join('');

                            return `
                                <section class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ${section.countClass}">
                                            ${escapeHtml(section.title)}
                                        </span>
                                        <span class="text-xs font-semibold text-admin-neutral-500">
                                            ${groupedAlerts[section.key].length}
                                        </span>
                                    </div>
                                    <div class="space-y-2">${itemsHtml}</div>
                                </section>
                            `;
                        })
                        .join('');

                    list.setAttribute('aria-busy', 'false');
                }

                function closeInventoryAlertsModal() {
                    window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: modalName }));
                }

                function openInventoryAlertsModal() {
                    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: modalName }));
                }

                function processInventoryAlerts(alerts, openOnChange) {
                    const normalizedAlerts = Array.isArray(alerts) ? alerts : [];
                    const signature = getAlertSignature(normalizedAlerts);
                    const previousSignature = getStoredSignature();

                    renderInventoryAlerts(normalizedAlerts);
                    updateSidebarInventoryBadge(normalizedAlerts.length);

                    if (!signature) {
                        setStoredSignature('');
                        closeInventoryAlertsModal();
                        return;
                    }

                    if (openOnChange && signature !== previousSignature) {
                        setStoredSignature(signature);
                        openInventoryAlertsModal();
                        return;
                    }

                    if (!previousSignature) {
                        setStoredSignature(signature);
                    }
                }

                function fetchInventoryAlerts(openOnChange = true) {
                    if (requestInFlight) {
                        return;
                    }

                    requestInFlight = true;

                    fetch(alertsUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error(`Inventory alerts request failed with status ${response.status}`);
                            }

                            return response.json();
                        })
                        .then((data) => {
                            processInventoryAlerts(data && Array.isArray(data.alerts) ? data.alerts : [], openOnChange);
                        })
                        .catch((error) => {
                            console.error('Error loading inventory alerts:', error);
                        })
                        .finally(() => {
                            requestInFlight = false;
                        });
                }

                function startInventoryAlertPolling() {
                    if (pollingTimer !== null) {
                        window.clearInterval(pollingTimer);
                    }

                    pollingTimer = window.setInterval(() => {
                        fetchInventoryAlerts(true);
                    }, pollIntervalMs);
                }

                window.refreshAdminInventoryAlerts = function (openOnChange = true) {
                    fetchInventoryAlerts(Boolean(openOnChange));
                };

                window.addEventListener('refresh-admin-inventory-alerts', () => {
                    fetchInventoryAlerts(true);
                });

                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        fetchInventoryAlerts(true);
                    }
                });

                window.addEventListener('focus', () => {
                    fetchInventoryAlerts(true);
                });

                document.addEventListener('livewire:navigated', () => {
                    fetchInventoryAlerts(true);
                });

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        fetchInventoryAlerts(true);
                        startInventoryAlertPolling();
                    }, { once: true });

                    return;
                }

                fetchInventoryAlerts(true);
                startInventoryAlertPolling();
            })();
        </script>
    @endif

    @auth
    <script>
        (function(){
            const IDLE_TIMEOUT = 5 * 60 * 1000;
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
            resetTimer();
        })();
    </script>
    @endauth
</body>
</html>
