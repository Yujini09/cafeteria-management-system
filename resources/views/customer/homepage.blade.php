@extends('layouts.app')

@section('title', 'RET Cafeteria - CLSU')

@section('styles')
/* Page-specific styles */
.spices-bg {
    background-image: url('/images/spices.webp');
    background-size: cover;
    background-position: center;
}
.blue-curve {
    background: #1F2937;
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

#home {
    position: relative;
    isolation: isolate;
}

#home .z-0 { z-index: 0; }
#home .z-5 { z-index: 5; }
#home .z-10 { z-index: 10; }
#home .z-20 { z-index: 20; }

.hero-white-curve {
    background-color: white;
    border-radius: 0 50% 50% 0 / 0 60% 60% 0;
    width: 50%;
    height: 110%;
    top: -5%;
    right: 20%;
}

.slide {
    opacity: 0;
    transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1;
}

.slide.active {
    opacity: 1;
    z-index: 10;
}

.progress-bar {
    width: 33.333%;
    transition: width 4s linear;
}

.slideshow-active .progress-bar {
    animation: progress 12s linear infinite;
}

@keyframes progress {
    0%, 33.333% { width: 33.333%; }
    33.334%, 66.666% { width: 66.666%; }
    66.667%, 100% { width: 100%; }
}

.group:hover .slide.active img {
    transform: scale(1.05);
}

@media (max-width: 640px) {
    .relative .text-white h3 { font-size: 1.75rem; line-height: 1.2; }
    .relative .text-white p { font-size: 0.875rem; }
}
@endsection

@section('content')

<section id="home" class="relative py-16 bg-white text-black overflow-hidden">
    {{-- Blue Background --}}
    <div class="absolute top-0 right-0 h-full w-1/2 bg-[#1F2937] z-0"></div>
    
    {{-- White Curve --}}
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

            {{-- ACTIONS --}}
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                @auth
                    {{-- User is logged in --}}
                    <a href="{{ route('reservation_form') }}"
                    class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                        Reserve Now
                    </a>
                @else
                    {{-- Guest User --}}
                    <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 bg-clsu-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all">
                        Reserve Now
                    </a>
                @endauth
            </div>
        </div>

        <div class="flex-1 relative flex justify-center lg:justify-end min-h-[450px]">
            <div class="relative w-80 h-80 z-10 mt-16">
                <div class="absolute -translate-x-20 inset-0 w-full h-full border-[100px] border-white rounded-full"></div>
                <div class="absolute -translate-x-20 inset-[6px] w-[calc(100%-12px)] h-[calc(100%-12px)] border-[100px] border-[#1F2937] rounded-full"></div>                
                <div class="absolute -translate-x-20 inset-[18px] rounded-full overflow-hidden ">
                    <img src="{{ asset('images/plate.png') }}" alt="Food plate" class="w-full h-full object-cover" />
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
            <div class="bg-ret-dark text-white p-8 shadow-lg rounded-lg w-full">
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
        {{-- MENU ITEMS --}}
        <div class="bg-ret-dark text-white overflow-hidden shadow-lg aspect-square relative group">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4 transition-transform duration-500 group-hover:scale-105"
                 style="background-image: url('{{ asset('images/veg.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            <a href="{{ route('menu') }}" class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Vegetables & Salads</h3>
                <p class="text-base text-gray-300">Fresh vegetables and fruits.</p>
            </a>
        </div>

        <div class="bg-ret-green-light text-white overflow-hidden shadow-lg aspect-square relative group">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4 transition-transform duration-500 group-hover:scale-105"
                 style="background-image: url('{{ asset('images/sandwich.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            <a href="{{ route('menu') }}" class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Sandwiches & Snacks</h3>
                <p class="text-base text-gray-300">Ideal for in-between meals.</p>
            </a>
        </div>

        <div class="bg-cafeteria-orange text-white overflow-hidden shadow-lg aspect-square relative group">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4 transition-transform duration-500 group-hover:scale-105"
                 style="background-image: url('{{ asset('images/menudo.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            <a href="{{ route('menu') }}" class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Rice Meals & Main Courses</h3>
                <p class="text-base text-gray-300">Served with rice, featuring Filipino specialty.</p>
            </a>
        </div>

        <div class="bg-clsu-green text-white overflow-hidden shadow-lg aspect-square relative group">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-1/4 transition-transform duration-500 group-hover:scale-105"
                 style="background-image: url('{{ asset('images/juice.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            <a href="{{ route('menu') }}" class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Desserts & Beverages</h3>
                <p class="text-base text-gray-300">Sweet treats and variety of drinks.</p>
            </a>
        </div>

        <div class="bg-menu-orange text-white overflow-hidden shadow-lg aspect-square relative group">
            <div class="absolute inset-0 bg-contain bg-no-repeat bg-center transform translate-x-2/4 transition-transform duration-500 group-hover:scale-105"
                 style="background-image: url('{{ asset('images/tinola.png') }}');">
            </div>
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-colors"></div>
            <a href="{{ route('menu') }}" class="relative z-10 p-6 h-full flex flex-col justify-center items-start w-1/2">
                <h3 class="text-3xl font-bold mb-2 leading-tight">Soups & Side Dishes</h3>
                <p class="text-base text-gray-300">Warm and flavorful broths.</p>
            </a>
        </div>

        <a href="{{ route('menu') }}" class="block w-full h-full">
            <div class="bg-menu-dark text-white overflow-hidden shadow-lg flex flex-col items-center justify-center p-6 aspect-square group hover:bg-gray-800 transition-colors">
                <h3 class="text-3xl font-bold mb-4 group-hover:scale-105 transition-transform">And Much More</h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-16 h-16 mb-2 text-white group-hover:translate-x-2 transition-transform">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                </svg>
            </div>
        </a>
    </div>
</section>

<section class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="font-poppins text-center mb-16">
            <h2 class="text-4xl font-bold text-ret-dark mb-4">Our Services</h2>
        </div>

        <div class="relative overflow-hidden rounded-2xl shadow-xl h-[400px] md:h-[450px] group">
            {{-- Slide 1 --}}
            <div class="absolute inset-0 slide active">
                <div class="relative w-full h-full">
                    <img src="{{ asset('images/buffet.png') }}" alt="Buffet" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
                </div>
                <div class="absolute inset-0 flex items-center">
                    <div class="max-w-2xl pl-8 md:pl-16 text-white">
                        <div class="inline-block bg-clsu-green/90 backdrop-blur-sm px-4 py-1.5 rounded-full mb-4">
                            <span class="text-xs md:text-sm font-semibold tracking-widest">PREMIUM DINING</span>
                        </div>
                        <h3 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Elegant Buffet<br>Experience</h3>
                        <p class="text-base md:text-lg text-gray-200 mb-6 font-light max-w-lg">
                            Indulge in our exquisite buffet spread featuring freshly prepared dishes.
                        </p>
                        <a href="{{ route('menu') }}" class="inline-flex items-center gap-2 bg-white text-ret-dark px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all">
                            <span>Explore Menu</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Slide 2 --}}
            <div class="absolute inset-0 slide">
                <div class="relative w-full h-full">
                    <img src="{{ asset('images/catering.png') }}" alt="Catering" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-ret-dark/60 via-ret-dark/30 to-transparent"></div>
                </div>
                <div class="absolute inset-0 flex items-center">
                    <div class="max-w-2xl pl-8 md:pl-16 text-white">
                        <div class="inline-block bg-cafeteria-orange/90 backdrop-blur-sm px-4 py-1.5 rounded-full mb-4">
                            <span class="text-xs md:text-sm font-semibold tracking-widest">EVENT CATERING</span>
                        </div>
                        <h3 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Professional<br>Catering Services</h3>
                        <p class="text-base md:text-lg text-gray-200 mb-6 font-light max-w-lg">
                            From corporate events to private celebrations, we deliver exceptional experiences.
                        </p>
                        <a href="{{ route('reservation_form') }}" class="inline-flex items-center gap-2 bg-white text-ret-dark px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all">
                            <span>Book Service</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Slide 3 --}}
            <div class="absolute inset-0 slide">
                <div class="relative w-full h-full">
                    <img src="{{ asset('images/service.jpg') }}" alt="Service" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-clsu-green/60 via-clsu-green/30 to-transparent"></div>
                </div>
                <div class="absolute inset-0 flex items-center">
                    <div class="max-w-2xl pl-8 md:pl-16 text-white">
                        <div class="inline-block bg-ret-green-light/90 backdrop-blur-sm px-4 py-1.5 rounded-full mb-4">
                            <span class="text-xs md:text-sm font-semibold tracking-widest">COMPREHENSIVE</span>
                        </div>
                        <h3 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">Complete Dining<br>Solutions</h3>
                        <p class="text-base md:text-lg text-gray-200 mb-6 font-light max-w-lg">
                            Your one-stop solution for all dining needs since 1995.
                        </p>
                        <a href="{{ route('about') }}" class="inline-flex items-center gap-2 bg-white text-ret-dark px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all">
                            <span>Learn More</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Controls --}}
            <button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-sm text-white p-3 rounded-full hover:bg-white/20 transition-all z-20 prev-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/10 backdrop-blur-sm text-white p-3 rounded-full hover:bg-white/20 transition-all z-20 next-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>

            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-32 h-1 bg-white/20 rounded-full overflow-hidden z-20">
                <div class="h-full bg-white progress-bar"></div>
            </div>

            <div class="absolute bottom-6 right-6 flex items-center space-x-3 z-20">
                <div class="text-white font-medium text-xs md:text-sm">
                    <span class="current-slide">01</span> / <span class="total-slides">03</span>
                </div>
                <div class="flex space-x-1.5">
                    <button class="indicator w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white/50 hover:bg-white transition-all active"></button>
                    <button class="indicator w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white/50 hover:bg-white transition-all"></button>
                    <button class="indicator w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-white/50 hover:bg-white transition-all"></button>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="reservation" class="bg-white">
    <div class="py-20 bg-gray-900 relative overflow-hidden text-center text-white">
        <div class="absolute inset-0 opacity-30 reservation-bg"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 font-poppins">
            <h2 class="text-4xl font-bold mb-6">Reserve Your Spot at RET Cafeteria</h2>
            <p class="text-xl mb-8">Don't miss out. Reserve ahead and roll up when it's time to eat.</p>

            @auth
                <a href="{{ route('reservation_form') }}" class="inline-block">
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

<section id="testimonials" class="py-20 bg-white relative" 
         x-data="{ 
            showFeedbackModal: false, 
            active: 0, 
            count: {{ count($feedbacks) }},
            next() { if(this.count > 1) this.active = (this.active + 1) % this.count; },
            prev() { if(this.count > 1) this.active = (this.active - 1 + this.count) % this.count; }
         }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Section Header with Title and Button --}}
        <div class="text-center mb-16">
            <span class="text-clsu-green font-semibold text-sm uppercase tracking-wider mb-2 block">Testimonials</span>
            <h2 class="text-4xl md:text-4xl font-bold text-ret-dark mb-4">What Our Customers Say</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Share your experience and help us serve you better</p>
            
            @auth
                {{-- Logged in: Opens the review modal --}}
                <button @click="showFeedbackModal = true" 
                        class="mt-8 bg-clsu-green hover:bg-green-700 text-white font-semibold py-4 px-8 rounded-lg transition duration-300 shadow-lg hover:shadow-xl flex items-center gap-3 mx-auto group">
                    <i class="fas fa-pen"></i> Write a Review
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            @else
                {{-- Logged out: Redirects to login page --}}
                <a href="{{ route('login') }}" 
                   class="mt-8 inline-flex bg-clsu-green hover:bg-green-700 text-white font-semibold py-4 px-8 rounded-lg transition duration-300 shadow-lg hover:shadow-xl items-center gap-3 mx-auto group">
                    <i class="fas fa-sign-in-alt"></i> Login to Write a Review
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            @endauth
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-8 max-w-2xl mx-auto p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg text-sm font-medium flex items-center justify-between animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Feedback Display Logic --}}
        @if($feedbacks->isEmpty())
            {{-- Empty State --}}
            <div class="col-span-full bg-white p-16 rounded-2xl shadow-lg border border-gray-100 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-comments text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No reviews yet</h3>
                <p class="text-gray-600 mb-8">Be the first to share your experience with us!</p>
            </div>
        @else
            @php $totalCount = count($feedbacks); @endphp

            @if($totalCount == 1)
                {{-- Single item - centered with original card width --}}
                <div class="flex justify-center mt-8">
                    @foreach($feedbacks as $feedback)
                        <div class="w-full max-w-sm md:max-w-md bg-white rounded-2xl overflow-hidden relative group flex flex-col h-[400px] border-2 border-gray-100 hover:border-clsu-green/30 transition-all duration-300 shadow-xl">
                            {{-- Top Color Bar --}}
                            <div class="h-1.5 w-full bg-clsu-green"></div>
                            
                            <div class="p-8 flex flex-col h-full relative z-10">
                                {{-- Star Rating --}}
                                <div class="flex items-center gap-1 mb-4 flex-shrink-0">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= ($feedback->rating ?? 5))
                                            <span class="text-yellow-400 text-xl">★</span>
                                        @else
                                            <span class="text-gray-300 text-xl">★</span>
                                        @endif
                                    @endfor
                                </div>
                                
                                {{-- Testimonial Message with Enhanced Design --}}
                                <div class="flex-1 overflow-y-auto mb-4 pr-2 custom-scrollbar min-h-0">
                                    <div class="relative">
                                        {{-- Decorative large quotation mark --}}
                                        <div class="absolute -top-2 -left-2 text-6xl text-clsu-green/10 font-serif leading-none">"</div>
                                        
                                        {{-- Message container with subtle background and border --}}
                                        <div class="relative z-10 bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border-l-4 border-clsu-green shadow-sm">
                                            <p class="text-gray-700 leading-relaxed italic font-light text-base">
                                                {{ $feedback->message }}
                                            </p>
                                        </div>
                                        
                                        {{-- Small quotation mark at the end --}}
                                        <div class="absolute -bottom-2 -right-2 text-4xl text-clsu-green/10 font-serif rotate-180">"</div>
                                    </div>
                                </div>
                                
                                {{-- Author Info --}}
                                <div class="flex items-center gap-4 pt-4 border-t border-gray-100 flex-shrink-0">
                                    <div class="w-12 h-12 bg-clsu-green rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-md">
                                        {{ substr($feedback->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate">{{ $feedback->name }}</h4>
                                        <p class="text-sm text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-calendar-alt text-clsu-green text-xs"></i>
                                            {{ $feedback->created_at->format('F j, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($totalCount == 2)
                {{-- Two items - side by side with proper spacing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto mt-8">
                    @foreach($feedbacks as $feedback)
                        <div class="bg-white rounded-2xl overflow-hidden relative group flex flex-col h-[400px] border-2 border-gray-100 hover:border-clsu-green/30 transition-all duration-300 w-full shadow-xl">
                            {{-- Top Color Bar --}}
                            <div class="h-1.5 w-full bg-clsu-green"></div>
                            
                            <div class="p-8 flex flex-col h-full relative z-10">
                                {{-- Star Rating --}}
                                <div class="flex items-center gap-1 mb-4 flex-shrink-0">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= ($feedback->rating ?? 5))
                                            <span class="text-yellow-400 text-xl">★</span>
                                        @else
                                            <span class="text-gray-300 text-xl">★</span>
                                        @endif
                                    @endfor
                                </div>
                                
                                {{-- Testimonial Message with Enhanced Design --}}
                                <div class="flex-1 overflow-y-auto mb-4 pr-2 custom-scrollbar min-h-0">
                                    <div class="relative">
                                        {{-- Decorative large quotation mark --}}
                                        <div class="absolute -top-2 -left-2 text-6xl text-clsu-green/10 font-serif leading-none">"</div>
                                        
                                        {{-- Message container with subtle background and border --}}
                                        <div class="relative z-10 bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border-l-4 border-clsu-green shadow-sm">
                                            <p class="text-gray-700 leading-relaxed italic font-light text-base">
                                                {{ $feedback->message }}
                                            </p>
                                        </div>
                                        
                                        {{-- Small quotation mark at the end --}}
                                        <div class="absolute -bottom-2 -right-2 text-4xl text-clsu-green/10 font-serif rotate-180">"</div>
                                    </div>
                                </div>
                                
                                {{-- Author Info --}}
                                <div class="flex items-center gap-4 pt-4 border-t border-gray-100 flex-shrink-0">
                                    <div class="w-12 h-12 bg-clsu-green rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-md">
                                        {{ substr($feedback->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-gray-900 truncate">{{ $feedback->name }}</h4>
                                        <p class="text-sm text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-calendar-alt text-clsu-green text-xs"></i>
                                            {{ $feedback->created_at->format('F j, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Original 3D slider logic for 3+ items --}}
                <div class="relative w-full h-[450px] flex items-center justify-center mt-8" style="perspective: 1000px;">
                    
                    {{-- Left Arrow --}}
                    <button @click="prev()" class="absolute left-0 md:left-8 z-40 bg-clsu-green hover:bg-green-700 text-white w-12 h-12 rounded-lg shadow-lg flex items-center justify-center transition-all hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="relative w-full max-w-5xl h-full flex justify-center items-center">
                        @foreach($feedbacks as $index => $feedback)
                            <div class="absolute w-full max-w-sm md:max-w-md transition-all duration-500 ease-in-out cursor-pointer"
                                 style="will-change: transform, opacity;"
                                 :class="{
                                     'z-30 scale-100 opacity-100 translate-x-0 shadow-2xl': active === {{ $index }},
                                     'z-20 scale-[0.85] opacity-50 -translate-x-[65%] md:-translate-x-[85%] blur-[1px] hover:opacity-100 hover:blur-none': active === {{ ($index + 1) % count($feedbacks) }},
                                     'z-20 scale-[0.85] opacity-50 translate-x-[65%] md:translate-x-[85%] blur-[1px] hover:opacity-100 hover:blur-none': active === {{ ($index - 1 + count($feedbacks)) % count($feedbacks) }},
                                     'z-10 scale-75 opacity-0 pointer-events-none': active !== {{ $index }} && active !== {{ ($index + 1) % count($feedbacks) }} && active !== {{ ($index - 1 + count($feedbacks)) % count($feedbacks) }}
                                 }"
                                 @click="active = {{ $index }}">
                                
                                {{-- Card Structure with Fixed Height --}}
                                <div class="bg-white rounded-2xl overflow-hidden relative group flex flex-col h-[400px] border-2 border-gray-100 hover:border-clsu-green/30 transition-all duration-300 w-full" 
                                     :class="active === {{ $index }} ? 'shadow-xl border-clsu-green/30' : 'shadow-md'">
                                    
                                    {{-- Top Color Bar --}}
                                    <div class="h-1.5 w-full bg-clsu-green"></div>
                                    
                                    <div class="p-8 flex flex-col h-full relative z-10">
                                        {{-- Star Rating --}}
                                        <div class="flex items-center gap-1 mb-4 flex-shrink-0">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= ($feedback->rating ?? 5))
                                                    <span class="text-yellow-400 text-xl">★</span>
                                                @else
                                                    <span class="text-gray-300 text-xl">★</span>
                                                @endif
                                            @endfor
                                        </div>
                                        
                                        {{-- Testimonial Message with Enhanced Design --}}
                                        <div class="flex-1 overflow-y-auto mb-4 pr-2 custom-scrollbar min-h-0">
                                            <div class="relative">
                                                <div class="absolute -top-2 -left-2 text-6xl text-clsu-green/10 font-serif leading-none">"</div>
                                                <div class="relative z-10 bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border-l-4 border-clsu-green shadow-sm">
                                                    <p class="text-gray-700 leading-relaxed italic font-light text-base">
                                                        {{ $feedback->message }}
                                                    </p>
                                                </div>
                                                <div class="absolute -bottom-2 -right-2 text-4xl text-clsu-green/10 font-serif rotate-180">"</div>
                                            </div>
                                        </div>
                                        
                                        {{-- Author Info --}}
                                        <div class="flex items-center gap-4 pt-4 border-t border-gray-100 flex-shrink-0">
                                            <div class="w-12 h-12 bg-clsu-green rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-md">
                                                {{ substr($feedback->name, 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-gray-900 truncate">{{ $feedback->name }}</h4>
                                                <p class="text-sm text-gray-500 flex items-center gap-1">
                                                    <i class="fas fa-calendar-alt text-clsu-green text-xs"></i>
                                                    {{ $feedback->created_at->format('F j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Right Arrow --}}
                    <button @click="next()" class="absolute right-0 md:right-8 z-40 bg-clsu-green hover:bg-green-700 text-white w-12 h-12 rounded-lg shadow-lg flex items-center justify-center transition-all hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                {{-- Dot Indicators --}}
                <div class="flex justify-center flex-wrap gap-2 mt-8 max-w-lg mx-auto">
                    @foreach($feedbacks as $index => $feedback)
                        <button @click="active = {{ $index }}" 
                                class="h-2.5 rounded-full transition-all duration-300"
                                :class="active === {{ $index }} ? 'w-8 bg-clsu-green' : 'w-2.5 bg-gray-300 hover:bg-clsu-green/50'"></button>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- Custom Scrollbar Styles --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
    </style>

    {{-- === FEEDBACK MODAL FORM === --}}
    <div x-show="showFeedbackModal" 
         x-cloak 
         class="fixed inset-0 z-[999] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm p-4"
         x-transition.opacity>
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl transform transition-all overflow-hidden"
             @click.away="showFeedbackModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Top Bar --}}
            <div class="h-1.5 w-full bg-clsu-green"></div>
            
            <div class="p-8 relative">
                {{-- Close Button --}}
                <button type="button"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors bg-gray-100 hover:bg-gray-200 rounded-lg p-2 z-50 cursor-pointer"
                        @click="showFeedbackModal = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                {{-- Modal Header --}}
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-ret-dark">Share Your Experience</h3>
                    <p class="text-gray-600 mt-1">Your feedback helps us improve</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('feedback.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    {{-- Rating Stars --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Your Rating</label>
                        <div class="flex items-center gap-2" 
                             x-data="{ selectedRating: 0 }">
                            <div class="flex items-center gap-1 text-3xl">
                                @for($i = 1; $i <= 5; $i++)
                                    <span @click="selectedRating = {{ $i }}" class="cursor-pointer">
                                        <span x-show="selectedRating >= {{ $i }}" class="text-yellow-400">★</span>
                                        <span x-show="selectedRating < {{ $i }}" class="text-gray-300">★</span>
                                    </span>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-500" x-show="selectedRating > 0" x-text="'(' + selectedRating + '/5)'"></span>
                            <input type="hidden" name="rating" :value="selectedRating">
                        </div>
                    </div>

                    {{-- Name Field --}}
                    <div class="space-y-2">
                        <label for="feedback_name" class="block text-sm font-semibold text-gray-700">Your Name</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input type="text" id="feedback_name" name="name" required 
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-clsu-green focus:border-transparent transition-all bg-gray-50 focus:bg-white" 
                                   placeholder="Enter your name"
                                   value="{{ Auth::check() ? Auth::user()->name : '' }}"
                                   {{ Auth::check() ? 'readonly' : '' }}>
                        </div>
                    </div>
                    
                    {{-- Message Field --}}
                    <div class="space-y-2">
                        <label for="feedback_message" class="block text-sm font-semibold text-gray-700">Your Review</label>
                        <div class="relative">
                            <span class="absolute top-3 left-4 text-gray-400">
                                <i class="fas fa-comment text-sm"></i>
                            </span>
                            <textarea id="feedback_message" name="message" rows="4" required 
                                      class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-clsu-green focus:border-transparent transition-all bg-gray-50 focus:bg-white resize-none" 
                                      placeholder="Tell us about your experience..."></textarea>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-clsu-green hover:bg-green-700 text-white font-semibold py-4 px-4 rounded-xl transition duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                            <i class="fas fa-paper-plane"></i> Submit Review
                        </button>
                        <p class="text-xs text-gray-400 text-center mt-4">Your review will be displayed after moderation.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const progressBar = document.querySelector('.progress-bar');
    const currentSlideSpan = document.querySelector('.current-slide');
    const slideshowContainer = document.querySelector('.relative');
    let currentSlide = 0;
    let slideInterval;
    let isHovering = false;

    // Initialize
    if (slides.length > 0) {
        slides[0].classList.add('active');
        indicators[0].classList.add('active');
        if(slideshowContainer) slideshowContainer.classList.add('slideshow-active');
        startSlideshow();
    }

    function showSlide(index) {
        currentSlide = index;
        
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
        
        indicators.forEach(indicator => indicator.classList.remove('active'));
        indicators[index].classList.add('active');
        
        if(progressBar) progressBar.style.width = `${(index + 1) * 33.333}%`;
        if(currentSlideSpan) currentSlideSpan.textContent = String(index + 1).padStart(2, '0');
        
        if(slideshowContainer) {
            slideshowContainer.classList.remove('slideshow-active');
            void slideshowContainer.offsetWidth; // Trigger reflow
            slideshowContainer.classList.add('slideshow-active');
        }
    }

    function nextSlide() {
        let nextIndex = (currentSlide + 1) % slides.length;
        showSlide(nextIndex);
    }

    function prevSlide() {
        let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(prevIndex);
    }

    function startSlideshow() {
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 4000);
    }

    if(prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (!isHovering) {
                clearInterval(slideInterval);
                prevSlide();
                startSlideshow();
            }
        });
    }

    if(nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (!isHovering) {
                clearInterval(slideInterval);
                nextSlide();
                startSlideshow();
            }
        });
    }

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            if (!isHovering) {
                clearInterval(slideInterval);
                showSlide(index);
                startSlideshow();
            }
        });
    });

    if(slideshowContainer) {
        slideshowContainer.addEventListener('mouseenter', () => {
            isHovering = true;
            clearInterval(slideInterval);
            slideshowContainer.classList.remove('slideshow-active');
        });

        slideshowContainer.addEventListener('mouseleave', () => {
            isHovering = false;
            slideshowContainer.classList.add('slideshow-active');
            startSlideshow();
        });
    }
});
</script>
@endsection