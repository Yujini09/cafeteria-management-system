@extends('layouts.app')

@section('title', 'RET Cafeteria - CLSU')

@section('styles')
/* Page-specific styles are included in the section via in the layout */
.spices-bg {
    background-image: url('/images/spices.webp');
    background-size: cover;
    background-position: center;
}
.blue-curve {
    background: #1F2937; /* gray-800 */
    border-radius: 50%;
    transform: rotate(12deg) scale(1.1);
}
.reservation-bg {
    background-image: url('/images/bg-1.jpg');
    background-size: cover;
    background-position: center;
}
body {
    overflow-x: hidden;
}

/* Hero section specific styles */
#home {
    position: relative;
    isolation: isolate;
}

/* Ensure proper z-index stacking */
#home .z-0 {
    z-index: 0;
}

#home .z-5 {
    z-index: 5;
}

#home .z-10 {
    z-index: 10;
}

#home .z-20 {
    z-index: 20;
}

/* Smooth transitions */
.rounded-full {
    transition: all 0.3s ease;
}

/* White curved shape that extends into the blue */
.hero-white-curve {
    background-color: white;
    border-radius: 0 50% 50% 0 / 0 60% 60% 0; /* Elliptical curve */
    width: 50%;
    height: 110%;
    top: -5%;
    right: 20%;
}

@endsection

@section('content')

<section id="home" class="relative py-16 bg-white text-black overflow-hidden"> <!-- Changed py-20 to py-16 -->
    {{-- Blue Background --}}
    <div class="absolute top-0 right-0 h-full w-1/2 bg-[#1F2937] z-0"></div>
    
    {{-- White Curve extending into blue --}}
    <div class="absolute hero-white-curve z-5"></div>

    <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row gap-10 items-center">
        <div class="flex-2 text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                <span class="text-clsu-green font-fugaz text-8xl">CLSU</span>
                <span class="text-ret-green-light font-fugaz text-8xl"> RET</span>
                <br>
                <span class="text-cafeteria-orange font-damion text-9xl">Cafeteria</span>
            </h1>
            <p class="text-2xl max-w-3xl mb-8 font-poppins italic opacity-80">
                Official Food Caterer of the University. Also offers food catering services for special occasions.
            </p>
            <p class="text-base mb-8 font-poppins italic opacity-70">
                Your meal, your way—fast, fresh, and convenient. Book Now!
            </p>

            {{-- ACTIONS: auth-aware buttons --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('reservation_form') }}"
                    class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                        Reserve Now
                    </a>
                @else
                    <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                        Reserve Now
                    </a>
                @endauth
            </div>
        </div>

        <div class="flex-1 relative flex justify-center lg:justify-end min-h-[450px]"> <!-- Changed min-h-[500px] to min-h-[450px] -->
            {{-- Nested Circles Container --}}
            <div class="relative w-80 h-80 z-10 mt-16"> <!-- Changed mt-20 to mt-16 -->
                <div class="absolute -translate-x-20 inset-0 w-full h-full border-[100px] border-white rounded-full"></div>
                <div class="absolute -translate-x-20 inset-[6px] w-[calc(100%-12px)] h-[calc(100%-12px)] border-[100px] border-[#1F2937] rounded-full"></div>                
                <div class="absolute -translate-x-20 inset-[18px] rounded-full overflow-hidden ">
                    <img src="{{ asset('images/plate.png') }}" alt="Food plate"
                         class="w-full h-full object-cover" />
                </div>
            </div>
        </div>
    </div>
</section>

    <section id="about" class="py-20 bg-white relative">
        <img src="{{ asset('images/spices.webp') }}" alt="Spices"
             class="hidden lg:block absolute top-0 left-0 w-[400px] h-auto transform -translate-x-1/3 -translate-y-[55%] z-0 opacity-100" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="flex flex-col items-start gap-8">
                <div class="bg-ret-dark text-white p-8 shadow-lg rounded-tl-lg rounded-tr-lg rounded-bl-lg rounded-br-lg w-full">
                    <h2 class="text-4xl font-bold mb-4">About us</h2>
                    <h3 class="text-2xl font-bold mb-6">CLSU RET Cafeteria</h3>
                    <p class="text-base mb-6">At CLSU RET Cafeteria, we take pride in serving fresh, delicious, and high-quality meals to the CLSU community.</p>
                    <p class="text-base mb-8">Beyond daily meals, we also offer professional catering services for special occasions.</p>
                    <p class="text-base mb-8">Great food. Great service. Always at CLSU RET Cafeteria.</p>
                </div>
                <a href="{{ url('/about') }}"
                   class="bg-clsu-green px-6 py-3 rounded-lg font-semibold text-white text-base hover:bg-green-700 transition duration-300 inline-block">
                    See more
                </a>
            </div>

            <div class="flex justify-center">
                <img src="{{ asset('images/resto.png') }}" alt="Cafeteria Building" class="w-90 h-90 object-contain" />
            </div>
        </div>
    </section>

<section id="menu" class="py-18 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins text-center mb-16">
        <h2 class="text-4xl font-bold text-ret-dark mb-4">Menus</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0">
        
        {{-- MENU ITEM 1: Vegetables & Salads --}}
        <div class="bg-ret-dark text-white overflow-hidden shadow-lg aspect-square relative">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4"
                 style="background-image: url('{{ asset('images/veg.png') }}');">
            </div>
            
            <div class="absolute inset-0 bg-black/20"></div>
            <a href="{{ route('menu') }}">
            <div class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Vegetables & Salads</h3>
                <p class="text-base text-gray-300">Fresh vegetables and fruits.</p>
            </div>
            </a>
        </div>

        {{-- MENU ITEM 2: Sandwiches & Snacks --}}
        <div class="bg-ret-green-light text-white overflow-hidden shadow-lg aspect-square relative">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4"
                 style="background-image: url('{{ asset('images/sandwich.png') }}');">
            </div>
            
            <div class="absolute inset-0 bg-black/20"></div>
            <a href="{{ route('menu') }}">
            <div class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Sandwiches & Snacks</h3>
                <p class="text-base text-gray-300">Ideal for in-between meals.</p>
            </div>
            </a>
        </div>

        {{-- MENU ITEM 3: Rice Meals & Main Courses --}}
        <div class="bg-cafeteria-orange text-white overflow-hidden shadow-lg aspect-square relative">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4"
                 style="background-image: url('{{ asset('images/menudo.png') }}');">
            </div>
            
            <div class="absolute inset-0 bg-black/20"></div>
            <a href="{{ route('menu') }}">
            <div class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Rice Meals & Main Courses</h3>
                <p class="text-base text-gray-300">Served with rice, featuring Filipino specialty.</p>
            </div>
            </a>
        </div>

        {{-- MENU ITEM 4: Desserts & Beverages --}}
        <div class="bg-clsu-green text-white overflow-hidden shadow-lg aspect-square relative">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-1/4"
                 style="background-image: url('{{ asset('images/juice.png') }}');">
            </div>
            
            <div class="absolute inset-0 bg-black/20"></div>
            <a href="{{ route('menu') }}">
            <div class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Desserts & Beverages</h3>
                <p class="text-base text-gray-300">Sweet treats and variety of drinks.</p>
            </div>
            </a>
        </div>

        {{-- MENU ITEM 5: Soups & Side Dishes --}}
        <div class="bg-menu-orange text-white overflow-hidden shadow-lg aspect-square relative">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4"
                 style="background-image: url('{{ asset('images/tinola.png') }}');">
            </div>
            
            <div class="absolute inset-0 bg-black/20"></div>
            <a href="{{ route('menu') }}">
            <div class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 href="{{ route('menu') }}" class="text-3xl font-bold mb-2 leading-tight">Soups & Side Dishes</h3>
                <p class="text-base text-gray-300">Warm and flavorful broths.</p>
            </div>
            </a>
        </div>

        {{-- MENU ITEM 6: And Much More --}}
        <a href="{{ route('menu') }}"
            class="block w-full h-full rounded-lg">
            <div class="bg-menu-dark text-white overflow-hidden shadow-lg flex flex-col items-center justify-center p-6 aspect-square">
                <h3 class="text-3xl font-bold mb-4">And Much More</h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-16 h-16 mb-2 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                </svg>
            </div>
        </a>
    </div>
</section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-16 font-poppins">
            <h2 class="text-4xl font-bold text-ret-dark mb-4">Best Seller</h2>
            <p class="text-xl text-gray-600">Most-Ordered Meals</p>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 font-poppins">
            <div class="bg-ret-dark text-white rounded-lg p-8 shadow-lg">
                <h3 class="text-4xl font-bold mb-2">Standard Menu</h3>
                <p class="text-base text-gray-300 mb-4">Breakfast</p>
                <h4 class="text-3xl font-bold text-cafeteria-orange mb-6">Menu 1</h4>
                <ul class="space-y-2 text-gray-300 list-disc list-inside marker:text-cafeteria-orange">
                    <li>Longanisa with Slice Tomato</li>
                    <li>Fried Egg Sunny Side Up</li>
                    <li>Rice</li>
                    <li>Tea/Coffee</li>
                    <li>Bottled Water</li>
                </ul>
            </div>

            <div class="bg-ret-dark text-white rounded-lg p-8 shadow-lg">
                <h3 class="text-4xl font-bold mb-2">Special Menu</h3>
                <p class="text-base text-gray-300 mb-4">Lunch</p>
                <h4 class="text-3xl font-bold text-cafeteria-orange mb-6">Menu 3</h4>
                <ul class="space-y-2 text-gray-300 list-disc list-inside marker:text-cafeteria-orange">
                    <li>Sinigang na Hipon</li>
                    <li>Fried Chicken</li>
                    <li>Gising-gising</li>
                    <li>Sliced Fruits</li>
                    <li>Rice</li>
                    <li>Bottled Water</li>
                </ul>
            </div>
        </div>
    </section>

<section id="reservation" class="bg-white">
    <div class="bg-gray-900 relative overflow-hidden text-center text-white">
        <div class="absolute inset-0 opacity-30 reservation-bg"></div>

        <div class="absolute top-10 left-10 w-16 h-16 opacity-20">
            <img src="{{ asset('images/spices.webp') }}" alt="Spices" class="w-full h-full object-cover rounded-full" />
        </div>
        <div class="absolute bottom-10 right-10 w-20 h-20 opacity-20">
            <img src="{{ asset('images/spices.webp') }}" alt="Spices" class="w-full h-full object-cover rounded-full" />
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins py-16">
            <h2 class="text-4xl font-bold mb-6">Reserve Your Spot at RET Cafeteria</h2>
            <p class="text-xl mb-8">Don't miss out. Reserve ahead and roll up when it's time to eat.</p>

            @auth
                <a href="{{ route('customer.home') }}" class="inline-block">
                    <button class="bg-clsu-green px-8 py-3 rounded-lg font-semibold text-white text-base hover:bg-green-700 transition duration-300">
                        Reserve Now
                    </button>
                </a>
            @else
                <a href="{{ route('reservation_form') }}" class="inline-block">
                    <button class="bg-clsu-green px-8 py-3 rounded-lg font-semibold text-white text-base hover:bg-green-700 transition duration-300">
                        Reserve Now
                    </button>
                </a>
            @endauth
        </div>
    </div>
</section>

@endsection