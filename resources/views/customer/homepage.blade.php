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