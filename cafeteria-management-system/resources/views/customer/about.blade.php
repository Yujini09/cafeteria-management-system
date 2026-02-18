<!-- Load Font Awesome Link Here, outside of Blade sections, for proper HTML inclusion -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

@extends('layouts.app')

@section('title', 'About Us - CLSU RET Cafeteria')

@section('styles')
/* Custom background similar to the menu header for consistency */
.about-hero-bg {
    /* Assuming you have a general background image like spices.webp */
    background-image: url('/images/banner.jpg');
    background-size: cover;
    background-position: bottom;
}
.ret-dark-card {
    background-color: #1F2937; /* Dark blue/gray from your site */
}
.ret-red-text {
    color: #EF4444; /* Vibrant red/orange color */
}

/* Defining the custom green color for the icon */
.ret-green-light {
    color: #057C3C; 
}
@endsection

@section('content')

<!-- 1. HERO SECTION -->
<section class="about-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <!-- Overlay for better text contrast -->
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-2 tracking-wider">
            About Us
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Get to know us more!
        </p>
    </div>
</section>

<!-- 2. MAIN CONTENT (Logo and Mission) -->
<section class="py-16 bg-white text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center lg:items-start gap-10">
            
            <!-- Left Side: Logo and Slogan -->
            <div class="w-full lg:w-1/2 flex flex-col items-center lg:items-start">
                <div class="text-center lg:text-left">
                    <!-- Placeholder for the large logo image -->
                    <img src="{{ asset('images/caf-logo.png') }}"" alt="CLSU RET Cafeteria Logo" class="w-64 md:w-80 lg:w-96 mx-auto lg:mx-0 mb-4" onerror="this.src='https://placehold.co/400x200/FFFFFF/EF4444?text=CLSU+RET+Cafeteria+Logo';">
                    <h2 class="text-2xl italic font-serif mt-2 text-gray-600">
                        Home-cooked goodness, you'll keep coming back!
                    </h2>
                </div>
            </div>

            <!-- Right Side: Mission/Story Card -->
            <div class="w-full lg:w-1/2 ret-dark-card text-white p-8 rounded-xl shadow-2xl">
                <h3 class="text-3xl font-bold mb-4 ret-red-text">CLSU RET Cafeteria</h3>
                <p class="mb-4 text-gray-300">
                    At CLSU RET Cafeteria, we take pride in serving fresh, delicious, and high-quality meals to the CLSU community. As the official food concessionaire on campus, we ensure a convenient, efficient, and enjoyable dining experience for students, visitors, faculty, and staff.
                </p>
                <p class="mb-0 text-gray-300">
                    Beyond daily meals, we also offer professional catering services for special occasions, bringing exceptional flavors and seamless service to your events. Whether it's a simple gathering or a grand celebration, we've got your catering needs covered!
                </p>
                <p class="mt-4 font-semibold italic ret-green-light">
                    Great food. Great service. Always at CLSU RET Cafeteria.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- 3. SERVICES SECTION -->
<section class="py-16 bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-2">Services</h2>
        <p class="text-lg text-gray-500 mb-12">See what our customers have to say.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            
            <!-- Service Card 1: Catering Services (FIXED ICON) -->
            <div class="flex flex-col items-center p-6 bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                <div class="ret-green-light mb-4">
                    <!-- Catering Service Icon: Utensils (Font Awesome) -->
                    <i class="fas fa-utensils text-5xl"></i> 
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-800">Catering Services</h4>
                <p class="text-gray-600 text-sm">
                    Service catering for meetings, parties, and campus events.
                </p>
            </div>

            <!-- Service Card 2: Buffet Setups (FIXED ICON) -->
            <div class="flex flex-col items-center p-6 bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                <div class="ret-green-light mb-4">
                    <!-- Buffet Icon: Concierge Bell (Font Awesome) -->
                    <i class="fas fa-concierge-bell text-5xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-800">Buffet Setups</h4>
                <p class="text-gray-600 text-sm">
                    Enjoy flexible buffet options for any celebration or group gathering.
                </p>
            </div>

            <!-- Service Card 3: Online Reservation (FIXED ICON) -->
            <div class="flex flex-col items-center p-6 bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                <div class="ret-green-light mb-4">
                    <!-- Reservation Icon: Calendar Check (Font Awesome) -->
                    <i class="fas fa-calendar-check text-5xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-800">Online Reservation</h4>
                <p class="text-gray-600 text-sm">
                    Reserve your seat in just a few clicks—fast and hassle-free.
                </p>
            </div>

            <!-- Service Card 4: Real-Time Notifications (FIXED ICON) -->
            <div class="flex flex-col items-center p-6 bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition duration-300">
                <div class="ret-green-light mb-4">
                    <!-- Notifications Icon: Bell (Font Awesome) -->
                    <i class="fas fa-bell text-5xl"></i>
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-800">Real-Time Notifications</h4>
                <p class="text-gray-600 text-sm">
                    Stay updated on your orders and reservations.
                </p>
            </div>

        </div>
    </div>
</section>

@endsection
