<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Smart Cafeteria'))</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fugaz+One&family=Damion&display=swap" rel="stylesheet" />

    <style>
        [x-cloak] { display: none !important; }
        @yield('styles')
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
    {!! \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles() !!}
</head>
<body class="font-poppins antialiased bg-gray-200" x-data="{ confirmLogout: false }" :class="{ 'overflow-hidden': confirmLogout }" @keydown.escape.window="confirmLogout = false">
    <div class="min-h-screen">
        
        @include('partials.header')

        <main>
            @yield('content') {{-- THIS IS WHERE PAGE CONTENT GOES --}}
        </main>

        @include('partials.footer')

    </div>

    {{-- Logout Confirmation Modal (same as admin) --}}
    <div x-show="confirmLogout"
         class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[100]"
         x-transition.opacity
         x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 text-black transform transition-all duration-300"
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
                <button type="button" @click="confirmLogout = false"
                        class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium">
                    Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium shadow-lg">
                        Yes, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    @yield('scripts')
    {!! \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts() !!}
    
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
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
