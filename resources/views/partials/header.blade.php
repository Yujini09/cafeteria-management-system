<style>
    .nav-link.active { color: #057C3C !important; position: relative; }
    .nav-link.active::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px; background-color: #057C3C; }
    .dropdown-item:hover { background-color: #f0fdf4; color: #15803d; }
</style>

<header class="bg-white shadow-sm sticky top-0 z-50"
    x-data="{ mobileMenuOpen: false, mobileReservationOpen: false, confirmLogout: false }"
    @keydown.escape.window="mobileMenuOpen = false; mobileReservationOpen = false; confirmLogout = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16 relative">
        <div class="flex items-center space-x-4">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/ret-logo-nav.png') }}" alt="RET Cafeteria Logo" class="h-12 w-auto" />
            </a>
        </div>

        <nav class="hidden md:flex space-x-8 text-ret-dark font-poppins font-medium absolute left-1/2 -translate-x-1/2">
            <a href="{{ url('/') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('/') ? 'active' : 'text-gray-600' }}">Home</a>
            <a href="{{ url('/about') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('about') ? 'active' : 'text-gray-600' }}">About</a>
            <a href="{{ url('/menu') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('menu') ? 'active' : 'text-gray-600' }}">Menu</a>
            <a href="{{ url('/contact') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('contact') ? 'active' : 'text-gray-600' }}">Contact Us</a>

            <div class="relative group flex items-center">
                <a href="#" class="nav-link text-gray-600 hover:text-ret-green-light flex items-center cursor-pointer py-1 {{ request()->is('reservation*') ? 'active' : '' }}">
                    Reservation
                    <svg class="w-4 h-4 ml-1 transform transition duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-0 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 transform z-50">
                    <div class="py-1">
                        <a href="{{ route('reservation_form') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition">Make a Reservation</a>
                        <a href="{{ route('reservation_details') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition">View My Reservations</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex items-center space-x-4 text-sm text-gray-600 font-poppins">
            @guest
                <a href="{{ route('login') }}" class="text-clsu-green hover:text-green-700 font-bold transition-colors duration-200 whitespace-nowrap">LOGIN</a>
            @endguest

            @auth
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 p-2 rounded-lg transition duration-150">
                        <div class="text-right hidden sm:block">
                            <span class="block text-sm font-semibold text-gray-800 leading-none">{{ explode(' ', Auth::user()->name)[0] }}</span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-green-100 border-2 border-green-200 flex items-center justify-center overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/clsu-logo.png') }}" class="w-full h-full object-cover">
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
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 ring-1 ring-black ring-opacity-5 z-50 origin-top-right"
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

        <div class="md:hidden flex items-center gap-2">
            @guest
                <a href="{{ route('login') }}" class="text-xs text-clsu-green hover:text-green-700 font-bold transition-colors duration-200 whitespace-nowrap">
                    LOGIN
                </a>
            @endguest
        </div>
    </div>
</header>
