<!-- Add this CSS to your stylesheet -->
<style>
    /* Active state styles for navigation */
    .nav-link.active {
        color: #48bb78 !important; /* ret-green-light color */
        position: relative;
    }
    
    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #48bb78;
    }
    
    /* Active state for dropdown items */
    .dropdown-link.active {
        background-color: #f7faf7;
        color: #48bb78 !important;
        font-weight: 500;
    }
</style>

<!-- Header Section -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/ret-logo-nav.png') }}" alt="RET Cafeteria Logo" class="h-12 w-auto" />
        </div>

        <nav class="hidden md:flex space-x-8 text-ret-dark font-poppins font-medium">
            <a href="{{ url('/') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('/') ? 'active' : 'text-gray-600' }}">Home</a>
            <a href="{{ url('/about') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('about') ? 'active' : 'text-gray-600' }}">About</a>
            <a href="{{ url('/menu') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('menu') ? 'active' : 'text-gray-600' }}">Menu</a>
            <a href="{{ url('/contact') }}" class="nav-link hover:text-ret-green-light py-1 {{ request()->is('contact') ? 'active' : 'text-gray-600' }}">Contact Us</a>

            <!-- RESERVATION DROPDOWN START -->
            <div class="relative group flex items-center">
                <!-- Dropdown Trigger Link -->
                <a 
                    href="#" 
                    class="nav-link text-gray-600 hover:text-ret-green-light flex items-center cursor-pointer py-1 {{ request()->is('reservation_form') || request()->is('reservation/details') ? 'active' : '' }}"
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
                        <a href="{{ url('/reservation_form') }}" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150 {{ request()->is('reservation_form') ? 'active' : '' }}">
                            Make a Reservation
                        </a>
                        <a href="{{ url('/reservation_details') }}" class="dropdown-link block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-ret-green-light transition duration-150 {{ request()->is('reservation/details') ? 'active' : '' }}">
                            View My Reservations
                        </a>
                    </div>
                </div>
            </div>
            <!-- RESERVATION DROPDOWN END -->

        </nav>

        <div class="flex items-center space-x-4 text-sm text-gray-600 font-poppins">
            @guest
                <a href="{{ route('login') }}" class="text-clsu-green hover:text-green-700 font-bold transition-colors duration-200">
                    LOGIN
                </a>
            @endguest
            @auth
                <span>Hi, {{ Auth::user()->name }}</span>
                <div class="relative">
                    <img src="https://placehold.co/24x24/CCCCCC/333333?text=N" alt="Notifications" class="w-6 h-6" />
                </div>
                <div class="w-8 h-8 bg-gray-600 rounded-full text-white flex items-center justify-center font-medium">
                    <img src="images/clsu-logo.png" alt="User Profile" class="w-6 h-6 rounded-full" />
                </div>

                @if(Auth::user()->role == 'customer')
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                            LOGOUT
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</header>

<!-- Add this JavaScript for proper active state handling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update active states based on current URL
    function updateActiveStates() {
        const navLinks = document.querySelectorAll('.nav-link');
        const dropdownLinks = document.querySelectorAll('.dropdown-link');
        
        // Get current path
        const currentPath = window.location.pathname;
        
        // Remove active classes from all links
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (!link.classList.contains('text-gray-600') && link.getAttribute('href') !== '#') {
                link.classList.add('text-gray-600');
            }
        });
        
        dropdownLinks.forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to current page link
        navLinks.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (linkHref && linkHref !== '#') {
                const linkPath = new URL(linkHref, window.location.origin).pathname;
                if (linkPath === currentPath) {
                    link.classList.add('active');
                    link.classList.remove('text-gray-600');
                }
            }
        });
        
        // Handle dropdown links and their parent reservation link
        let reservationDropdownActive = false;
        
        dropdownLinks.forEach(link => {
            const linkPath = new URL(link.href, window.location.origin).pathname;
            if (linkPath === currentPath) {
                link.classList.add('active');
                reservationDropdownActive = true;
            }
        });
        
        // Only activate reservation parent link if a dropdown item is active
        const reservationLink = document.querySelector('a[href="#"]');
        if (reservationDropdownActive && reservationLink) {
            reservationLink.classList.add('active');
            reservationLink.classList.remove('text-gray-600');
        } else if (reservationLink) {
            reservationLink.classList.remove('active');
            reservationLink.classList.add('text-gray-600');
        }
    }
    
    // Update active states on page load
    updateActiveStates();
    
    // Update active states when navigating
    document.querySelectorAll('.nav-link, .dropdown-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault(); // Prevent default only for reservation parent link
            }
            // Small delay to allow page navigation
            setTimeout(updateActiveStates, 100);
        });
    });
    
    // Optional: Update active states when browser history changes
    window.addEventListener('popstate', updateActiveStates);
});
</script>