<nav x-data="{ open: false }" class="bg-green-600 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Left: logo + links --}}
            <div class="flex items-center gap-6">
                @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('superadmin.users') }}"><x-application-logo class="block h-9 w-auto" /></a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"><x-application-logo class="block h-9 w-auto" /></a>
                @else
                    <a href="{{ route('customer.home') }}"><x-application-logo class="block h-9 w-auto" /></a>
                @endif

                <div class="hidden sm:flex space-x-8">
                    @if(Auth::user()->role === 'superadmin')
                        <x-nav-link :href="route('superadmin.users')" :active="request()->routeIs('superadmin.users')">Manage Users</x-nav-link>
                    @elseif(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                    @else
                        <x-nav-link :href="route('customer.home')" :active="request()->routeIs('customer.home')">Home</x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Right: user dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm rounded-md text-white hover:text-gray-200">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open=!open" class="p-2 rounded-md text-white hover:text-gray-200 hover:bg-green-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': open,'inline-flex':!open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open,'inline-flex':open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block':open,'hidden':!open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'superadmin')
                <x-responsive-nav-link :href="route('superadmin.users')" :active="request()->routeIs('superadmin.users')">Manage Users</x-responsive-nav-link>
            @elseif(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('customer.home')" :active="request()->routeIs('customer.home')">Home</x-responsive-nav-link>
            @endif
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-green-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}"> @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
