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
@endsection

@section('content')

    <!-- Success Modal -->
    @if(session('registered'))
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                <div class="mb-4">
                    <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Registration Successful!</h2>
                <p class="text-gray-600 mb-6">{{ session('registered') }}</p>
                <button onclick="closeModal()" class="bg-clsu-green hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">
                    Continue
                </button>
            </div>
        </div>
        <script>
            function closeModal() {
                document.getElementById('successModal').style.display = 'none';
            }
        </script>
    @endif

    <section id="home" class="relative py-20 bg-white text-black">
        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row gap-10 items-center">
            <div class="flex-2 text-center">
                <h1 class="text-5xl md:text-7xl font-bold mb-8 leading-tight">
                    <span class="text-clsu-green font-fugaz text-8xl">CLSU</span>
                    <span class="text-ret-green-light font-fugaz text-8xl"> RET</span>
                    <br>
                    <span class="text-cafeteria-orange font-damion text-9xl">Cafeteria</span>
                </h1>
                <p class="text-2xl mb-8 font-poppins italic opacity-80">
                    Official Food Caterer of the University. Also offers food catering services for special occasions.
                </p>
                <p class="text-base mb-8 font-poppins italic opacity-70">
                    Your meal, your way—fast, fresh, and convenient. Book Now!
                </p>

                {{-- ACTIONS: auth-aware buttons with functional Logout --}}
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('customer.home') }}"
                           class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                            Go to Menus
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 border border-gray-300 hover:bg-white/60 text-gray-800 px-6 py-3 rounded-lg font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                            Reserve Now
                        </a>
                    @endauth
                </div>
            </div>

            <div class="flex-1 relative flex justify-center lg:justify-end">
                <div class="relative w-80 h-80 z-30">
                    <div class="absolute inset-0 w-full h-full blue-curve -z-10"></div>
                    <img src="{{ asset('images/plate.webp') }}" alt="Food plate"
                         class="absolute inset-0 w-full h-full object-contain z-10" />
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="py-20 bg-white relative">
        <img src="{{ asset('images/spices.webp') }}" alt="Spices"
             class="hidden lg:block absolute top-0 left-0 w-[400px] h-auto transform -translate-x-1/3 -translate-y-[60%] z-0 opacity-100" />
        <img src="{{ asset('images/spices.webp') }}" alt="Spices"
             class="hidden lg:block absolute top-0 right-0 w-[400px] h-auto transform translate-x-1/3 -translate-y-[35%] z-0 opacity-100" />

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

<section id="menu" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins text-center mb-16">
        <h2 class="text-4xl font-bold text-ret-dark mb-4">Menus</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-0">
        
        {{-- MENU ITEM 1: Vegetables & Salads --}}
        <div class="bg-ret-dark text-white overflow-hidden shadow-lg aspect-square">
            <div class="flex h-full w-full">
                
                {{-- TEXT CONTENT (LEFT) --}}
                <div class="w-2/3 p-6 flex flex-col justify-center items-start text-left">
                    <h3 class="text-3xl font-bold mb-2 leading-tight">Vegetable & Salads</h3>
                    <p class="text-base text-gray-300">Fresh vegetables and fruits.</p>
                </div>
                
                {{-- IMAGE (RIGHT) --}}
                <div class="w-1/3 h-full overflow-hidden">
                    <img src="{{ asset('images/breakfast.png') }}" alt="Vegetables & Salads" class="h-full w-full object-cover transform scale-[1.3] origin-right" />
                </div>
                
            </div>
        </div>

        {{-- MENU ITEM 2: Sandwiches & Snacks --}}
        <div class="bg-ret-green-light text-white overflow-hidden shadow-lg aspect-square">
            <div class="flex h-full w-full">
                
                {{-- TEXT CONTENT (LEFT) --}}
                <div class="w-2/3 p-6 flex flex-col justify-center items-start text-left">
                    <h3 class="text-3xl font-bold mb-2 leading-tight">Sandwiches & Snacks</h3>
                    <p class="text-base text-gray-300">Ideal for in-between meals.</p>
                </div>
                
                {{-- IMAGE (RIGHT) --}}
                <div class="w-1/3 h-full overflow-hidden">
                    <img src="{{ asset('images/sandwich.png') }}" alt="Sandwiches & Snacks" class="h-full w-full object-cover" />
                </div>
                
            </div>
        </div>

        {{-- MENU ITEM 3: Rice Meals & Main Courses --}}
        <div class="bg-cafeteria-orange text-white overflow-hidden shadow-lg aspect-square">
            <div class="flex h-full w-full">
                
                {{-- TEXT CONTENT (LEFT) --}}
                <div class="w-2/3 p-6 flex flex-col justify-center items-start text-left">
                    <h3 class="text-3xl font-bold mb-2 leading-tight">Rice Meals & Main Courses</h3>
                    <p class="text-base text-gray-300">Served with rice, featuring Filipino specialty.</p>
                </div>
                
                {{-- IMAGE (RIGHT) --}}
                <div class="w-1/3 h-full overflow-hidden">
                    <img src="{{ asset('images/adobo.png') }}" alt="Rice Meals & Main Courses" class="h-full w-full object-cover" />
                </div>
                
            </div>
        </div>

        {{-- MENU ITEM 4: Desserts & Beverages (Applied the new structure) --}}
        <div class="bg-clsu-green text-white overflow-hidden shadow-lg aspect-square">
            <div class="flex h-full w-full">
                
                {{-- TEXT CONTENT (LEFT) --}}
                <div class="w-2/3 p-6 flex flex-col justify-center items-start text-left">
                    <h3 class="text-3xl font-bold mb-2 leading-tight">Desserts & Beverages</h3>
                    <p class="text-base text-gray-300">Sweet treats and variety of drinks.</p>
                </div>
                
                {{-- IMAGE (RIGHT) --}}
                <div class="w-1/3 h-full overflow-hidden">
                    <img src="{{ asset('images/juice.png') }}" alt="Desserts & Beverages" class="h-full w-full object-cover" />
                </div>
                
            </div>
        </div>

        {{-- MENU ITEM 5: Soups & Side Dishes (Applied the new structure) --}}
        <div class="bg-menu-orange text-white overflow-hidden shadow-lg aspect-square">
            <div class="flex h-full w-full">
                
                {{-- TEXT CONTENT (LEFT) --}}
                <div class="w-2/3 p-6 flex flex-col justify-center items-start text-left">
                    <h3 class="text-3xl font-bold mb-2 leading-tight">Soups & Side Dishes</h3>
                    <p class="text-base text-gray-300">Warm and flavorful broths.</p>
                </div>
                
                {{-- IMAGE (RIGHT) --}}
                <div class="w-1/3 h-full overflow-hidden">
                    <img src="{{ asset('images/tinola.webp') }}" alt="Soups & Side Dishes" class="h-full w-full object-cover" />
                </div>
                
            </div>
        </div>

        {{-- MENU ITEM 6: And Much More (Kept as a full block link) --}}
        <a href="{{ route('menu') }}"
            class="block w-full h-full transition duration-300 transform hover:scale-[1.03] hover:shadow-2xl rounded-lg">
            <div class="bg-menu-dark text-white overflow-hidden shadow-lg flex flex-col items-center justify-center p-6 aspect-square">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-16 h-16 mb-4 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                </svg>
                <h3 class="text-4xl font-bold mb-2">And Much More</h3>
            </div>
        </a>
    </div>
</section>

    <section class="py-30 bg-white">
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

    <section id="reservation" class="bg-white py-20">
        <div class="py-20 bg-gray-900 relative overflow-hidden text-center text-white">
            <div class="absolute inset-0 opacity-30 reservation-bg"></div>

            <div class="absolute top-10 left-10 w-16 h-16 opacity-20">
                <img src="{{ asset('images/spices.webp') }}" alt="Spices" class="w-full h-full object-cover rounded-full" />
            </div>
            <div class="absolute bottom-10 right-10 w-20 h-20 opacity-20">
                <img src="{{ asset('images/spices.webp') }}" alt="Spices" class="w-full h-full object-cover rounded-full" />
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
                <h2 class="text-4xl font-bold mb-6">Reserve Your Spot at RET Cafeteria</h2>
                <p class="text-xl mb-8">Don't miss out. Reserve ahead and roll up when it's time to eat.</p>

                @auth
                    <a href="{{ route('customer.home') }}" class="inline-block">
                        <button class="bg-clsu-green px-8 py-3 rounded-lg font-semibold text-white text-base hover:bg-green-700 transition duration-300">
                            Order / Reserve
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

    <section id="contact" class="py-10 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center font-poppins">
            <h2 class="text-4xl font-bold text-ret-dark mb-4">Customer Support</h2>
            <p class="text-xl text-gray-600 mb-12">Have a question? We're here to help!</p>

            <div class="flex items-center justify-center">
                <div class="bg-white border border-gray-200 rounded-lg p-12 shadow-lg w-full md:w-2/3 lg:w-1/2 text-center">
                    <div class="flex items-center justify-center mb-4 relative">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-ret-dark mb-2">
                        <a href="#contact" class="hover:underline">Contact Us →</a>
                    </h3>
                    <p class="text-gray-600">Reach out to us for any additional queries</p>
                </div>
            </div>
        </div>
    </section>

@endsection
