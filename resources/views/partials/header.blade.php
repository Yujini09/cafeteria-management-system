@php
    $reservationNavActive = request()->routeIs('reservation_form')
        || request()->routeIs('reservation.create')
        || request()->routeIs('reservation_form_menu')
        || request()->routeIs('reservation_details')
        || request()->routeIs('reservation.view')
        || request()->routeIs('reservation_view')
        || request()->is('reservations/*');

    $reservationListActive = request()->routeIs('reservation_details')
        || request()->routeIs('reservation.view')
        || request()->routeIs('reservation_view')
        || request()->is('reservations/*');
@endphp

<style>
    .nav-link.active { color: #057C3C !important; position: relative; }
    .nav-link.active::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px; background-color: #057C3C; }
    .dropdown-item:hover { background-color: #f0fdf4; color: #15803d; }

    .mobile-nav-toggle {
        width: 42px;
        height: 42px;
        border: 2px solid #ffffff;
        border-radius: 9999px;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        background: linear-gradient(135deg, #00462e 0%, #0b7d53 100%);
        box-shadow: 0 8px 18px rgba(0, 70, 46, 0.38), 0 0 0 2px rgba(255, 255, 255, 0.95);
    }

    .mobile-nav-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        display: none;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(1px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .mobile-nav-overlay.is-visible {
        display: block;
        opacity: 1;
        pointer-events: auto;
    }

    .mobile-nav-drawer {
        position: fixed;
        top: 0;
        left: 0;
        width: min(86vw, 320px);
        height: 100vh;
        z-index: 70;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        box-shadow: 8px 0 24px rgba(15, 23, 42, 0.2);
        transform: translateX(calc(-100% - 1rem));
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: transform 0.22s ease, opacity 0.2s ease, visibility 0s linear 0.22s;
    }

    .mobile-nav-drawer.is-open {
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: transform 0.22s ease, opacity 0.2s ease;
    }

    .mobile-nav-brand {
        margin: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.85rem;
        background: linear-gradient(135deg, #00462e 0%, #10b981 100%);
        border: 1px solid #d1fae5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
    }

    .mobile-nav-brand-logo {
        flex: 1;
        min-width: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-nav-close {
        width: 2.35rem;
        height: 2.35rem;
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .mobile-nav-close:hover,
    .mobile-nav-close:focus-visible {
        background: rgba(255, 255, 255, 0.24);
        transform: scale(1.03);
    }

    .mobile-nav-menu {
        flex: 1;
        overflow-y: auto;
        padding: 0 0.75rem 0.75rem;
    }

    .mobile-nav-link,
    .mobile-nav-subtoggle {
        width: 100%;
        border: 0;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.7rem;
        margin: 0.25rem 0;
        border-radius: 0.7rem;
        color: #334155;
        background: transparent;
        font-size: 0.9rem;
        font-weight: 500;
        text-align: left;
        transition: all 0.2s ease;
    }

    .mobile-nav-link:hover,
    .mobile-nav-subtoggle:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .mobile-nav-link.active,
    .mobile-nav-subtoggle.active {
        background: #00462e;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
    }

    .mobile-nav-icon {
        width: 1.8rem;
        height: 1.8rem;
        border-radius: 0.55rem;
        border: 1px solid #e2e8f0;
        background: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .mobile-nav-link.active .mobile-nav-icon,
    .mobile-nav-subtoggle.active .mobile-nav-icon {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    .mobile-nav-caret {
        margin-left: auto;
        transition: transform 0.2s ease;
    }

    .mobile-nav-sublinks {
        margin: 0.2rem 0 0.35rem 2.5rem;
        display: grid;
        gap: 0.2rem;
    }

    .mobile-nav-sublinks.is-collapsed {
        display: none;
    }

    .mobile-nav-sublink {
        text-decoration: none;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 0.55rem;
        padding: 0.45rem 0.6rem;
        transition: all 0.2s ease;
    }

    .mobile-nav-sublink:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .mobile-nav-sublink.active {
        background: #dcfce7;
        color: #14532d;
        font-weight: 600;
    }

    .mobile-nav-footer {
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
        padding: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }

    .mobile-nav-session-btn {
        width: 100%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 0.55rem;
        text-decoration: none;
        border-radius: 0.7rem;
        padding: 0.65rem 0.8rem;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #0f172a;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .mobile-nav-session-btn.login {
        background: #00462e;
        border-color: #00462e;
        color: #ffffff;
    }

    .mobile-nav-session-btn.logout {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    @media (min-width: 768px) {
        .mobile-nav-overlay,
        .mobile-nav-overlay.is-visible,
        .mobile-nav-drawer,
        .mobile-nav-drawer.is-open {
            display: none !important;
        }
    }
</style>

<header class="bg-white shadow-sm sticky top-0 z-50" data-mobile-nav-root>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-16 relative justify-start md:justify-between">
        <button type="button"
                class="mobile-nav-toggle relative z-20 shrink-0 inline-flex md:hidden"
                data-mobile-nav-toggle
                aria-expanded="false"
                aria-controls="mobile-nav-drawer"
                aria-label="Open menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path data-mobile-nav-open-icon stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M4 6h16M4 12h16M4 18h16"></path>
                <path data-mobile-nav-close-icon class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M6 6l12 12M18 6l-12 12"></path>
            </svg>
        </button>

        <div class="flex items-center space-x-4 absolute left-1/2 -translate-x-1/2 pointer-events-none md:static md:left-auto md:translate-x-0 md:pointer-events-auto">
            <a href="{{ url('/') }}" class="pointer-events-auto">
                <img src="{{ asset('images/ret-logo-nav.png') }}" alt="RET Cafeteria Logo" class="h-12 w-auto" />
            </a>
        </div>

        <nav class="hidden md:flex space-x-8 text-ret-dark font-poppins font-medium absolute left-1/2 -translate-x-1/2">
            <a href="{{ url('/') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('/') ? 'active' : 'text-gray-600' }}">Home</a>
            <a href="{{ url('/about') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('about') ? 'active' : 'text-gray-600' }}">About</a>
            <a href="{{ url('/menu') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('menu') ? 'active' : 'text-gray-600' }}">Menu</a>
            <a href="{{ url('/contact') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('contact') ? 'active' : 'text-gray-600' }}">Contact Us</a>

            <div class="relative group flex items-center">
                <a href="#" class="nav-link hover:text-ret-green-light flex items-center cursor-pointer py-1 {{ $reservationNavActive ? 'active' : 'text-gray-600' }}">
                    Reservation
                    <svg class="w-4 h-4 ml-1 transform transition duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-0 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 transform z-50">
                    <div class="py-1">
                        <a href="{{ route('reservation_form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition {{ request()->routeIs('reservation_form') || request()->routeIs('reservation.create') || request()->routeIs('reservation_form_menu') ? 'bg-green-50 text-ret-green-light font-medium' : '' }}">Make a Reservation</a>
                        <a href="{{ route('reservation_details') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition {{ $reservationListActive ? 'bg-green-50 text-ret-green-light font-medium' : '' }}">View My Reservations</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="ml-auto relative z-20 flex items-center text-sm text-gray-600 font-poppins">
            @guest
                <a href="{{ route('login') }}" class="inline-flex text-[0.82rem] md:text-sm text-clsu-green hover:text-green-700 font-bold transition-colors duration-200 whitespace-nowrap">LOGIN</a>
            @endguest

            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center space-x-2 md:space-x-3 focus:outline-none hover:bg-gray-50 p-1.5 md:p-2 rounded-lg transition duration-150">
                        <div class="text-right hidden md:block">
                            <span class="block text-sm font-semibold text-gray-800 leading-none">{{ explode(' ', Auth::user()->name)[0] }}</span>
                        </div>
                        <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-green-100 border-2 border-green-200 flex items-center justify-center overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover" alt="Profile picture">
                            @else
                                <img src="{{ asset('images/clsu-logo.png') }}" class="w-full h-full object-cover" alt="Default profile picture">
                            @endif
                        </div>
                        <svg class="w-4 h-4 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 max-w-[calc(100vw-1.5rem)] bg-white rounded-xl shadow-2xl py-2 ring-1 ring-black ring-opacity-5 z-50 origin-top-right"
                         style="display: none;">

                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Signed in as</p>
                            <p class="text-sm font-bold text-gray-900 truncate mt-1">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item group flex items-center px-4 py-2.5 text-sm text-gray-700 transition">
                                <i class="fa-regular fa-user w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600"></i>
                                Edit Profile
                            </a>
                            <a href="{{ route('reservation_details') }}" class="dropdown-item group flex items-center px-4 py-2.5 text-sm text-gray-700 transition">
                                <i class="fa-regular fa-calendar-check w-5 h-5 mr-2 text-gray-400 group-hover:text-green-600"></i>
                                My Reservations
                            </a>
                        </div>

                        <div class="py-1 border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full dropdown-item group flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-5 h-5 mr-2 text-red-400 group-hover:text-red-600"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>

    </div>

    <div class="mobile-nav-overlay md:hidden"
         data-mobile-nav-overlay
         aria-hidden="true"></div>

    <aside id="mobile-nav-drawer"
           class="mobile-nav-drawer md:hidden"
           data-mobile-nav-drawer
           aria-hidden="true">
        <div class="mobile-nav-brand">
            <a href="{{ url('/') }}" class="mobile-nav-brand-logo" data-mobile-nav-link>
                <img src="{{ asset('images/ret-logoo.png') }}" alt="RET Cafeteria Logo" class="h-10 w-auto">
            </a>
            <button type="button"
                    class="mobile-nav-close"
                    data-mobile-nav-close
                    aria-label="Close menu">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.25" d="M6 6l12 12M18 6l-12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="mobile-nav-menu font-poppins">
            <a href="{{ url('/') }}"
               class="mobile-nav-link {{ request()->is('/') ? 'active' : '' }}"
               data-mobile-nav-link>
                <span class="mobile-nav-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10.5L12 3l9 7.5V21a1 1 0 01-1 1h-5v-6h-6v6H4a1 1 0 01-1-1v-10.5z"></path></svg>
                </span>
                Home
            </a>

            <a href="{{ url('/about') }}"
               class="mobile-nav-link {{ request()->is('about') ? 'active' : '' }}"
               data-mobile-nav-link>
                <span class="mobile-nav-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"></path></svg>
                </span>
                About
            </a>

            <a href="{{ url('/menu') }}"
               class="mobile-nav-link {{ request()->is('menu') ? 'active' : '' }}"
               data-mobile-nav-link>
                <span class="mobile-nav-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3v18M16 3v18M4 8h16M4 16h16"></path></svg>
                </span>
                Menu
            </a>

            <a href="{{ url('/contact') }}"
               class="mobile-nav-link {{ request()->is('contact') ? 'active' : '' }}"
               data-mobile-nav-link>
                <span class="mobile-nav-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm0 2l8 6 8-6"></path></svg>
                </span>
                Contact Us
            </a>

            <button type="button"
                    class="mobile-nav-subtoggle {{ $reservationNavActive ? 'active' : '' }}"
                    data-mobile-nav-subtoggle
                    aria-expanded="{{ $reservationNavActive ? 'true' : 'false' }}">
                <span class="mobile-nav-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 2v3m8-3v3m-9 5h10m-12 12h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v13a2 2 0 002 2z"></path></svg>
                </span>
                Reservation
                <svg class="mobile-nav-caret w-4 h-4 {{ $reservationNavActive ? 'rotate-180' : '' }}" data-mobile-nav-caret fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div class="mobile-nav-sublinks {{ $reservationNavActive ? '' : 'is-collapsed' }}"
                 data-mobile-nav-sublinks>
                <a href="{{ route('reservation_form') }}"
                   class="mobile-nav-sublink {{ request()->routeIs('reservation_form') || request()->routeIs('reservation.create') || request()->routeIs('reservation_form_menu') ? 'active' : '' }}"
                   data-mobile-nav-link>
                    Make a Reservation
                </a>
                <a href="{{ route('reservation_details') }}"
                   class="mobile-nav-sublink {{ $reservationListActive ? 'active' : '' }}"
                   data-mobile-nav-link>
                    View My Reservations
                </a>
            </div>
        </nav>

        @auth
        <div class="mobile-nav-footer font-poppins">
            <form method="POST" action="{{ route('logout') }}" data-mobile-nav-form>
                @csrf
                <button type="submit" class="mobile-nav-session-btn logout">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9m4 8H7a2 2 0 01-2-2V6a2 2 0 012-2h6"></path></svg>
                    Logout
                </button>
            </form>
        </div>
        @endauth
    </aside>
</header>

@once
<script>
    (function () {
        const ROOT_SELECTOR = '[data-mobile-nav-root]';

        const resetMobileNavFallback = () => {
            const root = document.querySelector(ROOT_SELECTOR);
            if (!root) {
                document.body.classList.remove('overflow-hidden');
                return;
            }

            const overlay = root.querySelector('[data-mobile-nav-overlay]');
            const drawer = root.querySelector('[data-mobile-nav-drawer]');
            const toggle = root.querySelector('[data-mobile-nav-toggle]');
            const openIcon = root.querySelector('[data-mobile-nav-open-icon]');
            const closeIcon = root.querySelector('[data-mobile-nav-close-icon]');

            document.body.classList.remove('overflow-hidden');

            if (overlay) {
                overlay.classList.remove('is-visible');
                overlay.setAttribute('aria-hidden', 'true');
            }

            if (drawer) {
                drawer.classList.remove('is-open');
                drawer.setAttribute('aria-hidden', 'true');
            }

            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open menu');
            }

            if (openIcon) {
                openIcon.classList.remove('hidden');
            }

            if (closeIcon) {
                closeIcon.classList.add('hidden');
            }
        };

        const bindMobileNav = () => {
            const root = document.querySelector(ROOT_SELECTOR);
            if (!root) {
                document.body.classList.remove('overflow-hidden');
                return;
            }

            const toggle = root.querySelector('[data-mobile-nav-toggle]');
            const overlay = root.querySelector('[data-mobile-nav-overlay]');
            const drawer = root.querySelector('[data-mobile-nav-drawer]');
            const closeButton = root.querySelector('[data-mobile-nav-close]');
            const subToggle = root.querySelector('[data-mobile-nav-subtoggle]');
            const subLinks = root.querySelector('[data-mobile-nav-sublinks]');
            const caret = root.querySelector('[data-mobile-nav-caret]');
            const openIcon = root.querySelector('[data-mobile-nav-open-icon]');
            const closeIcon = root.querySelector('[data-mobile-nav-close-icon]');
            const navLinks = root.querySelectorAll('[data-mobile-nav-link]');
            const navForms = root.querySelectorAll('[data-mobile-nav-form]');

            if (!toggle || !overlay || !drawer) {
                resetMobileNavFallback();
                return;
            }

            const syncIcons = (isOpen) => {
                if (openIcon) {
                    openIcon.classList.toggle('hidden', isOpen);
                }

                if (closeIcon) {
                    closeIcon.classList.toggle('hidden', !isOpen);
                }
            };

            const setDrawerState = (isOpen) => {
                const nextOpen = Boolean(isOpen);
                overlay.classList.toggle('is-visible', nextOpen);
                drawer.classList.toggle('is-open', nextOpen);
                overlay.setAttribute('aria-hidden', nextOpen ? 'false' : 'true');
                drawer.setAttribute('aria-hidden', nextOpen ? 'false' : 'true');
                toggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
                toggle.setAttribute('aria-label', nextOpen ? 'Close menu' : 'Open menu');
                document.body.classList.toggle('overflow-hidden', nextOpen);
                syncIcons(nextOpen);
            };

            const setReservationState = (isExpanded) => {
                if (!subToggle || !subLinks) {
                    return;
                }

                const nextExpanded = Boolean(isExpanded);
                subToggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
                subLinks.classList.toggle('is-collapsed', !nextExpanded);

                if (caret) {
                    caret.classList.toggle('rotate-180', nextExpanded);
                }
            };

            if (root.dataset.mobileNavBound !== 'true') {
                toggle.addEventListener('click', () => {
                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    setDrawerState(!isOpen);
                });

                overlay.addEventListener('click', () => {
                    setDrawerState(false);
                });

                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        setDrawerState(false);
                    });
                }

                navLinks.forEach((link) => {
                    link.addEventListener('click', () => {
                        setDrawerState(false);
                    });
                });

                navForms.forEach((form) => {
                    form.addEventListener('submit', () => {
                        setDrawerState(false);
                    });
                });

                if (subToggle) {
                    subToggle.addEventListener('click', () => {
                        const isExpanded = subToggle.getAttribute('aria-expanded') === 'true';
                        setReservationState(!isExpanded);
                    });
                }

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        setDrawerState(false);
                    }
                });

                root.dataset.mobileNavBound = 'true';
            }

            setDrawerState(false);
            setReservationState(subToggle ? subToggle.getAttribute('aria-expanded') === 'true' : false);
        };

        if (!window.cmsGuestMobileNavInitialized) {
            document.addEventListener('DOMContentLoaded', bindMobileNav);
            document.addEventListener('livewire:navigated', bindMobileNav);
            window.addEventListener('pageshow', () => {
                resetMobileNavFallback();
                bindMobileNav();
            });
            window.addEventListener('pagehide', () => {
                document.body.classList.remove('overflow-hidden');
            });
            window.cmsGuestMobileNavInitialized = true;
        }
    })();
</script>
@endonce
