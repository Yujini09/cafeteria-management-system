@extends('layouts.app')

@section('title', 'Menu - CLSU RET Cafeteria')

@section('styles')
/* Page-specific styles are included in the section via in the layout */
.menu-hero-bg {
    background-image: url('/images/banner.jpg'); /* Use your existing class */
    background-size: cover;
    background-position: bottom;
}

.menu-tab-active {
    background-color: #EF4444; /* A vibrant red/orange color for the active tab */
    color: white;
    border-color: #EF4444;
}

/* Ensure these styles are loaded after any other styles */
.menu-tab {
    background-color: white;
    color: #1F2937;
    border: 1px solid #D1D5DB;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
}

/* Force the hover state with !important to override other styles */
.menu-tab:hover {
    background-color: #10B981 !important; /* Green background on hover */
    color: white !important; /* White text on hover */
    border-color: #10B981 !important; /* Green border on hover */
}

.menu-tab-active {
    background-color: #EF4444;
    color: white;
    border-color: #EF4444;
    transition: all 0.2s ease-in-out;
}

/* Active tab might also need a hover effect */
.menu-tab-active:hover {
    background-color: #DC2626 !important;
    color: white !important;
    border-color: #DC2626 !important;
}

/* Ensure only the default content (Breakfast) is visible initially */
#am-snacks-content, #lunch-content, #pm-snacks-content, #dinner-content {
    display: none;
}
@endsection

@section('content')

<section class="menu-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-bold mb-3 tracking-wider">
            Buffet Style Menu
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Delicious choices, all in one place.
        </p>
    </div>
</section>

<section class="py-12 bg-white text-ret-dark">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Menu Navigation Tabs -->
        <div class="flex flex-wrap justify-center space-x-2 md:space-x-4 mb-10">
            <!-- Ensure 'menu-tab-link' class and 'data-content-id' are present on all tabs -->
            <a href="#" id="tab-breakfast" data-content-id="breakfast-content" class="menu-tab-active menu-tab-link font-poppins text-lg font-semibold px-6 py-2 rounded-lg transition duration-300 shadow-md">
                Breakfast
            </a>
            <a href="#" id="tab-am-snacks" data-content-id="am-snacks-content" class="menu-tab menu-tab-link font-poppins text-lg font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                A.M. Snacks
            </a>
            <a href="#" id="tab-lunch" data-content-id="lunch-content" class="menu-tab menu-tab-link font-poppins text-lg font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                Lunch
            </a>
            <a href="#" id="tab-pm-snacks" data-content-id="pm-snacks-content" class="menu-tab menu-tab-link font-poppins text-lg font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                P.M. Snacks
            </a>
            <a href="#" id="tab-dinner" data-content-id="dinner-content" class="menu-tab menu-tab-link font-poppins text-lg font-semibold px-6 py-2 rounded-lg hover:bg-gray-100 transition duration-300">
                Dinner
            </a>
        </div>
        
        <!-- 1. BREAKFAST CONTENT -->
        <div id="breakfast-content" class="text-center">

            <h2 class="text-4xl font-extrabold text-red-600 mb-2">Breakfast</h2>

            <div class="flex flex-col lg:flex-row border-t border-gray-300">

                <div class="flex-1 p-6 lg:p-10 border-b lg:border-r lg:border-b-0 border-gray-300">
                    <h3 class="text-3xl font-bold mb-2">Standard Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱150 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['breakfast']['standard'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No standard breakfast menus available.</div>
                        @endforelse
                    </div>
                </div>

                <div class="flex-1 p-6 lg:p-10">
                    <h3 class="text-3xl font-bold mb-2">Special Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱170 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['breakfast']['special'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No special breakfast menus available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. A.M. SNACKS CONTENT -->
        <div id="am-snacks-content" class="text-center">

            <h2 class="text-4xl font-extrabold text-red-600 mb-2">A.M. Snacks</h2>

            <div class="flex flex-col lg:flex-row border-t border-gray-300">

                <div class="flex-1 p-6 lg:p-10 border-b lg:border-r lg:border-b-0 border-gray-300">
                    <h3 class="text-3xl font-bold mb-2">Standard Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱100 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['am_snacks']['standard'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No standard AM snacks menus available.</div>
                        @endforelse
                    </div>
                </div>

                <div class="flex-1 p-6 lg:p-10">
                    <h3 class="text-3xl font-bold mb-2">Special Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱150 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['am_snacks']['special'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No special AM snacks menus available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. LUNCH CONTENT (New) -->
        <div id="lunch-content" class="text-center">
            <h2 class="text-4xl font-extrabold text-red-600 mb-2">Lunch</h2>

            <div class="flex flex-col lg:flex-row border-t border-gray-300">
                
                <!-- LUNCH - STANDARD MENU -->
                <div class="flex-1 p-6 lg:p-10 border-b lg:border-r lg:border-b-0 border-gray-300">
                    <h3 class="text-3xl font-bold mb-2">Standard Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱300 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['lunch']['standard'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No standard lunch menus available.</div>
                        @endforelse
                    </div>
                </div>
                
                <!-- LUNCH - SPECIAL MENU -->
                <div class="flex-1 p-6 lg:p-10">
                    <h3 class="text-3xl font-bold mb-2">Special Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱350 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['lunch']['special'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No special lunch menus available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 4. P.M. SNACKS CONTENT -->
        <div id="pm-snacks-content" class="text-center">
             <h2 class="text-4xl font-extrabold text-red-600 mb-2">P.M. Snacks</h2>
             
             <div class="flex flex-col lg:flex-row border-t border-gray-300">
                
                <!-- P.M. SNACKS - STANDARD MENU -->
                <div class="flex-1 p-6 lg:p-10 border-b lg:border-r lg:border-b-0 border-gray-300">
                    <h3 class="text-3xl font-bold mb-2">Standard Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱100 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['pm_snacks']['standard'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No standard PM snacks menus available.</div>
                        @endforelse
                    </div>
                </div>
                
                <!-- P.M. SNACKS - SPECIAL MENU -->
                <div class="flex-1 p-6 lg:p-10">
                    <h3 class="text-3xl font-bold mb-2">Special Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱150 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['pm_snacks']['special'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No special PM snacks menus available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 5. DINNER CONTENT -->
        <div id="dinner-content" class="text-center">
             <h2 class="text-4xl font-extrabold text-red-600 mb-2">Dinner</h2>

             <div class="flex flex-col lg:flex-row border-t border-gray-300">
                
                <!-- DINNER - STANDARD MENU -->
                <div class="flex-1 p-6 lg:p-10 border-b lg:border-r lg:border-b-0 border-gray-300">
                    <h3 class="text-3xl font-bold mb-2">Standard Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱300 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['dinner']['standard'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No standard dinner menus available.</div>
                        @endforelse
                    </div>
                </div>
                
                <!-- DINNER - SPECIAL MENU -->
                <div class="flex-1 p-6 lg:p-10">
                    <h3 class="text-3xl font-bold mb-2">Special Menu</h3>
                    <p class="text-xl font-semibold text-gray-800 mb-6">₱300 /head</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-left">
                        @forelse($menus['dinner']['special'] ?? collect() as $menu)
                            <div>
                                <h4 class="text-2xl font-bold text-ret-dark border-b border-gray-200 pb-1 mb-3">{{ $menu->name ?? 'Menu' }}</h4>
                                @if($menu->description)
                                    <p class="text-gray-800 text-sm mb-2">{{ $menu->description }}</p>
                                @endif
                                <ul class="space-y-1 text-gray-900">
                                    @foreach($menu->items as $item)
                                        <li class="text-gray-900">{{ $item->name }} <span class="text-xs text-gray-700">({{ $item->type }})</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-700">No special dinner menus available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<script>
    // Get all tab links and content sections
    const tabs = document.querySelectorAll('.menu-tab-link');
    const contents = document.querySelectorAll('#breakfast-content, #am-snacks-content, #lunch-content, #pm-snacks-content, #dinner-content');

    // Function to handle tab switching
    function switchTab(event) {
        // Prevent default link behavior
        event.preventDefault();

        const clickedTab = event.currentTarget;
        const targetContentId = clickedTab.getAttribute('data-content-id');

        // 1. Deactivate all tabs and hide all content
        tabs.forEach(tab => {
            tab.classList.remove('menu-tab-active');
            tab.classList.add('menu-tab');
            tab.classList.remove('shadow-md');
        });

        contents.forEach(content => {
            content.style.display = 'none';
        });

        // 2. Activate the clicked tab
        clickedTab.classList.add('menu-tab-active');
        clickedTab.classList.remove('menu-tab');
        clickedTab.classList.add('shadow-md');

        // 3. Show the corresponding content
        const targetContent = document.getElementById(targetContentId);
        if (targetContent) {
            targetContent.style.display = 'block';
        }
    }

    // Attach the click handler to all tabs
    tabs.forEach(tab => {
        tab.addEventListener('click', switchTab);
    });
    
    // Set the initial active tab (Breakfast) on load
    document.addEventListener('DOMContentLoaded', () => {
        const initialTab = document.getElementById('tab-breakfast');
        if (initialTab) {
            // Manually trigger the state for the first tab
            initialTab.classList.add('menu-tab-active');
            initialTab.classList.remove('menu-tab');
            initialTab.classList.add('shadow-md');
            document.getElementById('breakfast-content').style.display = 'block';
        }
    });
</script>

@endsection