<!-- Header Section -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center space-x-4">
            <!-- Ensure your images path is correct, use /images/ret-logo-nav.png if needed -->
            <img src="{{ asset('images/ret-logo-nav.png') }}" alt="RET Cafeteria Logo" class="h-12 w-auto" />
        </div>

        <nav class="hidden md:flex space-x-8 text-ret-dark font-poppins font-medium">
            <a href="{{ url('/') }}" class="hover:text-ret-green-light py-1">Home</a>
            <a href="{{ url('/about') }}" class="text-gray-600 hover:text-ret-green-light py-1">About</a>
            <a href="{{ url('/menu') }}" class="text-gray-600 hover:text-ret-green-light py-1">Menu</a>
            <a href="{{ url('/contact') }}" class="text-gray-600 hover:text-ret-green-light py-1">Contact Us</a>

            <!-- RESERVATION DROPDOWN START -->
            <div class="relative group flex items-center">
                <!-- Dropdown Trigger Link -->
                <a 
                    href="#" 
                    class="text-gray-600 hover:text-ret-green-light flex items-center cursor-pointer py-1"
                >
                    Reservation
                    <svg class="w-4 h-4 ml-1 transform transition duration-300 group-hover:rotate-180" 
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>

                <!-- Dropdown Menu Content -->
                <div class="absolute left-1/2 -translate-x-1/2 top-full mt-0 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 
                            opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300 transform z-50">
                    <div class="py-1">
                        <a href="{{ url('/reservation') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150">
                            Make a Reservation
                        </a>
                        <a href="{{ url('/reservation_details') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150">
                            View My Reservations
                        </a>
                        <!-- If you need a quick link to menu/contact here -->
                        <a href="{{ url('/reservation/guidelines') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150">
                            Reservation Guidelines
                        </a>
                    </div>
                </div>
            </div>
            <!-- RESERVATION DROPDOWN END -->

        </nav>

        <div class="flex items-center space-x-4 text-sm text-gray-600 font-poppins">
            <span>Hi, Name</span>
            <div class="relative">
                <img src="https://placehold.co/24x24/CCCCCC/333333?text=N" alt="Notifications" class="w-6 h-6" />
            </div>
            <div class="w-8 h-8 bg-gray-600 rounded-full text-white flex items-center justify-center font-medium">
                <!-- Ensure your images path is correct, use /images/clsu-logo.png if needed -->
                <img src="images/clsu-logo.png" alt="User Profile" class="w-6 h-6 rounded-full" />
            </div>
        </div>
    </div>
</header>