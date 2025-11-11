<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Smart Cafeteria'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'clsu-green': '#00462E',
                        'ret-green-light': '#057C3C',
                        'cafeteria-orange': '#FB3E05',
                        'ret-dark': '#1F2937',
                        'menu-orange': '#EA580C',
                        'menu-dark': '#131820',
                    },
                    fontFamily: {
                        fugaz: ['"Fugaz One"', 'sans-serif'],
                        damion: ['"Damion"', 'cursive'],
                        poppins: ['"Poppins"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Fugaz+One&family=Damion&display=swap" rel="stylesheet" />

    <style>
        @yield('styles')
    </style>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-poppins antialiased bg-gray-200">
    <div class="min-h-screen">
        
        @include('partials.header')

        <main>
            @yield('content') {{-- THIS IS WHERE PAGE CONTENT GOES --}}
        </main>

        @include('partials.footer')

    </div>
    
    @yield('scripts')
    
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
</body>
</html>