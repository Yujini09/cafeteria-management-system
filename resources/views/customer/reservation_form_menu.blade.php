@extends('layouts.app')

@section('title', 'Menu Selection - CLSU RET Cafeteria')

@section('styles')

    .menu_selection-hero-bg {
        background-image: url('/images/banner1.jpg');
        background-size: cover;
        background-position: top;
    }
    .card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .menu-header {
        background-color: #1a5e3d;
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
    }
    .qty-btn {
        transition: background-color 0.15s ease;
    }
    .bg-clsu-green {
        background-color: #1a5e3d;
    }
    .hover\:bg-green-700:hover {
        background-color: #154d32;
    }
    .focus\:ring-clsu-green:focus {
        --tw-ring-color: #1a5e3d;
    }
    .text-ret-dark {
        color: #1a5e3d;
    }
    .border-ret-dark {
        border-color: #1a5e3d;
    }

    /* Day Navigation Styles */
    .day-nav-container {
        background: linear-gradient(135deg, #1a5e3d 0%, #2d7a52 100%);
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .day-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .day-tab {
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: 2px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 80px;
        text-align: center;
    }
    .day-tab:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }
    .day-tab.active {
        background: white;
        color: #1a5e3d;
        border-color: #1a5e3d;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .day-info {
        color: white;
        font-size: 0.9rem;
        margin-top: 8px;
        text-align: center;
    }

    /* Style for the Menu Selection Tabs */
    .menu-tab {
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 5px;
        transition: all 0.2s;
        border: 2px solid transparent;
        color: #333;
        font-size: 0.875rem;
        flex: 1;
        text-align: center;
    }
    .menu-tab.active {
        background-color: #1a5e3d;
        color: white;
        border-color: #1a5e3d;
        box-shadow: 0 2px 4px rgba(26, 94, 61, 0.4);
    }
    .menu-tab:not(.active):hover {
        background-color: #f0f0f0;
    }

    /* Meal Type Buttons */
    .meal-type-btn {
        padding: 6px 10px;
        cursor: pointer;
        border-radius: 4px;
        font-weight: 500;
        margin: 2px;
        transition: all 0.2s;
        border: 1px solid #d1d5db;
        color: #4b5563;
        font-size: 0.75rem;
        background-color: #f9fafb;
        text-align: center;
        flex: 1;
        min-width: 80px;
    }
    .meal-type-btn.active {
        background-color: #1a5e3d;
        color: white;
        border-color: #1a5e3d;
        box-shadow: 0 1px 2px rgba(26, 94, 61, 0.3);
    }
    .meal-type-btn:not(.active):hover {
        background-color: #e5e7eb;
        border-color: #9ca3af;
    }

    /* Enhanced styles */
    .meal-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .meal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
    }
    .meal-card.has-quantity {
        border-left-color: #1a5e3d;
        background-color: #f8fff9;
    }
    .quantity-indicator {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        background-color: #1a5e3d;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .menu-item-highlight {
        background: linear-gradient(90deg, #f0f9f0 0%, #ffffff 100%);
        border: 1px solid #1a5e3d;
    }

    /* Day content management */
    .day-content {
        display: none;
    }
    .day-content.active {
        display: block;
    }

    /* Green menu list styles */
    .green-menu-list {
        background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
        border: 2px solid #1a5e3d;
        border-radius: 10px;
        height: 735px;
        flex-direction: column; 
    }
    .green-menu-header {
        background: linear-gradient(135deg, #1a5e3d 0%, #2d7a52 100%);
        color: white;
        padding: 12px 16px;
        border-radius: 8px 8px 0 0;
    }
    .menu-list-item {
        border-left: 4px solid #1a5e3d;
        background-color: #ffffff;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }
    .menu-list-item:hover {
        background-color: #f0f9f0;
        transform: translateX(5px);
    }

    /* Fixed menu preview styles */
    .menu-preview-container {
        padding: 0;
    }
    .menu-tabs-container {
        padding: 8px;
        margin: 0;
        background-color: #e8f5e8;
        border-radius: 6px;
    }
    .menu-content-area {
        padding: 0 12px;
        margin: 8px 0 0 0;
        max-height: 520px;
        overflow-y: auto;
    }
    .compact-menu-item {
        padding: 12px;
        margin-bottom: 10px;
    }
    
    /* Search bar styles */
    .menu-search-container {
        padding: 12px 16px;
        background-color: #f8fff9;
        border-bottom: 1px solid #e8f5e8;
    }
    .menu-search-input {
        border: 1px solid #1a5e3d;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.875rem;
        width: 100%;
        background-color: white;
    }
    .menu-search-input:focus {
        outline: none;
        ring: 2px;
        ring-color: #1a5e3d;
        border-color: #1a5e3d;
    }
    .no-results {
        text-align: center;
        padding: 20px;
        color: #666;
        font-style: italic;
    }
    .search-highlight {
        background-color: #ffeb3b;
        padding: 1px 2px;
        border-radius: 2px;
    }
    .qty-error {
        border-color: #ef4444 !important;
        background-color: #fef2f2;
    }

    /* Price badge styles */
    .price-badge {
        background-color: #1a5e3d;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Confirmation Modal Styles */
    .confirmation-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .confirmation-modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 700px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }
    .summary-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 12px 0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
    .total-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    /* Notification Styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(150%);
        transition: transform 0.3s ease-in-out;
        max-width: 400px;
    }
    .notification.show {
        transform: translateX(0);
    }
    .notification.success {
        background-color: #10b981;
        border-left: 4px solid #059669;
    }
    .notification.error {
        background-color: #ef4444;
        border-left: 4px solid #dc2626;
    }
    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .notification-icon {
        flex-shrink: 0;
    }

    /* Meal type sections */
    .meal-type-section {
        display: none;
    }
    .meal-type-section.active {
        display: block;
    }

    /* Day summary badge */
    .day-summary-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #1a5e3d;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

@endsection

@section('content')

<!-- Reservation Banner Header -->
<section class="menu_selection-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-2 tracking-wider">
            Menu Selection
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Guaranteed delicious meals.
        </p>
    </div>
</section>

<!-- Menu Selection Section -->
<section class="py-10 bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-clsu-green rounded-full">
                        <span class="text-white font-bold">1</span>
                    </div>
                    <div class="ml-2 text-sm font-medium text-clsu-green">Reservation Details</div>
                </div>
                <div class="w-16 h-1 bg-clsu-green mx-2"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-clsu-green rounded-full">
                        <span class="text-white font-bold">2</span>
                    </div>
                    <div class="ml-2 text-sm font-medium text-clsu-green">Menu Selection</div>
                </div>
                <div class="w-16 h-1 bg-gray-300 mx-2"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                        <span class="text-gray-500 font-bold">3</span>
                    </div>
                    <div class="ml-2 text-sm font-medium text-gray-500">Confirmation</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Main Form (Menu Selection) - Occupies 8/12 columns on large screens --}}
            <div class="lg:col-span-8">
                <form action="{{ route('reservation.store') }}" method="POST" class="space-y-6" id="reservation-form">
                    @csrf

                    {{-- Day Navigation --}}
                    <div class="day-nav-container">
                        <div class="day-tabs" id="day-tabs">
                            <!-- Day tabs will be dynamically generated -->
                        </div>
                        <div class="day-info" id="day-info">
                            <!-- Day info will be dynamically updated -->
                        </div>
                    </div>

                    {{-- Menu Selection Grid --}}
                    <div class="card p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-clsu-green">Select Your Meals</h2>
                            <div class="text-sm text-gray-600">
                                <span id="total-pax-count" class="font-bold text-clsu-green">0</span> total pax selected
                                <span class="text-xs text-red-500 ml-2">(Minimum 10 pax per meal)</span>
                            </div>
                        </div>

                        @php
                            $meal_times = ['breakfast' => 'Breakfast', 'am_snacks' => 'A.M. Snacks', 'lunch' => 'Lunch', 'pm_snacks' => 'P.M. Snacks', 'dinner' => 'Dinner'];
                            $categories = ['standard' => 'Standard Menu', 'special' => 'Special Menu'];
                            
                            // Get default prices for fallback
                            $defaultStandardPrice = 150;
                            $defaultSpecialPrice = 200;

                            // Get date range from session or previous form
                            $startDate = session('reservation_data.start_date') ?? now()->format('Y-m-d');
                            $endDate = session('reservation_data.end_date') ?? now()->addDays(2)->format('Y-m-d');

                            // Parse day_times JSON if available
                            $dayTimes = [];
                            if (session('reservation_data.day_times')) {
                                $dayTimes = json_decode(session('reservation_data.day_times'), true);
                            }

                            // Calculate number of days
                            $start = new DateTime($startDate);
                            $end = new DateTime($endDate);
                            $numberOfDays = $end->diff($start)->days + 1;
                        @endphp

                        {{-- Hidden fields for date range --}}
                        <input type="hidden" id="start-date" value="{{ $startDate }}">
                        <input type="hidden" id="end-date" value="{{ $endDate }}">
                        <input type="hidden" id="number-of-days" value="{{ $numberOfDays }}">

                        {{-- Hidden fields for all menu prices --}}
                        <div id="menu-prices-data" style="display: none;">
                            @foreach($meal_times as $meal_key => $meal_label)
                                @php
                                    // Ensure we have valid prices with fallbacks
                                    $standardPrice = isset($menuPrices['standard'][$meal_key][0]) && is_numeric($menuPrices['standard'][$meal_key][0]->price) 
                                        ? $menuPrices['standard'][$meal_key][0]->price 
                                        : $defaultStandardPrice;
                                        
                                    $specialPrice = isset($menuPrices['special'][$meal_key][0]) && is_numeric($menuPrices['special'][$meal_key][0]->price)
                                        ? $menuPrices['special'][$meal_key][0]->price
                                        : $defaultSpecialPrice;
                                @endphp
                                <div data-meal-time="{{ $meal_key }}" 
                                     data-standard-price="{{ number_format($standardPrice, 2, '.', '') }}" 
                                     data-special-price="{{ number_format($specialPrice, 2, '.', '') }}">
                                </div>
                            @endforeach
                        </div>

                        {{-- Day Content Areas --}}
                        <div id="day-content-container">
                            <!-- Day content will be dynamically generated -->
                        </div>

                        {{-- Quick Actions --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="clear-current-day" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                    Clear Current Day
                                </button>
                                <button type="button" id="clear-all-days" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                    Clear All Days
                                </button>
                                <button type="button" id="set-standard-all-days" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                    Set 10 Pax for All Days
                                </button>
                                <button type="button" id="copy-to-all-days" class="px-4 py-2 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                                    Copy to All Days
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Notes and Summary Section --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- Notes Section --}}
                        <div class="card p-6 lg:col-span-2">
                            <h3 class="text-xl font-bold mb-4 border-b pb-2">Special Instructions</h3>
                            <textarea name="notes" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-clsu-green focus:border-clsu-green" placeholder="Add special instructions, allergy notes, or dietary restrictions..."></textarea>
                        </div>

                        {{-- Order Summary --}}
                        <div class="card p-6 bg-gradient-to-br from-gray-50 to-gray-100">
                            <h3 class="text-xl font-bold mb-4 border-b pb-2">Order Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span>Standard Menu Rate:</span>
                                    <span class="font-semibold" id="summary-standard-rate">
                                        @php
                                            $breakfastStandard = isset($menuPrices['standard']['breakfast'][0]) ? $menuPrices['standard']['breakfast'][0]->price : $defaultStandardPrice;
                                        @endphp
                                        ₱{{ number_format($breakfastStandard, 2) }} /head
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>Special Menu Rate:</span>
                                    <span class="font-semibold" id="summary-special-rate">
                                        @php
                                            $breakfastSpecial = isset($menuPrices['special']['breakfast'][0]) ? $menuPrices['special']['breakfast'][0]->price : $defaultSpecialPrice;
                                        @endphp
                                        ₱{{ number_format($breakfastSpecial, 2) }} /head
                                    </span>
                                </div>
                                <div class="border-t border-gray-300 pt-2 mt-2">
                                    <div class="flex justify-between font-bold">
                                        <span>Estimated Total:</span>
                                        <span id="estimated-total">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-xs text-gray-600">
                                <p>Final pricing may vary based on final selections and any additional requirements.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions (Back/Confirm) - Aligned with right sidebar --}}
                    <div class="flex justify-end pt-8">
                        <div class="w-full lg:w-1/3 flex justify-between space-x-4">
                            <button type="button" id="back-button" class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-150 shadow-lg font-semibold">
                                Back
                            </button>
                            <button type="button" id="confirm-button" class="px-8 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 shadow-lg font-semibold flex items-center">
                                Confirm
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Right Column (Menu Details and Payment) - Occupies 4/12 columns --}}
            <div class="lg:col-span-4 py-6 space-y-8">

                {{-- Menu Details Card - Fixed with compact layout and search --}}
                <div class="green-menu-list overflow-hidden">
                    <div class="green-menu-header">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold">Available Menus</h2>
                                <p class="text-sm mt-1 opacity-90">Browse our delicious meal options</p>
                            </div>
                            <div class="text-right py-5">
                                <div class="price-badge px-4">Standard: ₱{{ number_format($defaultStandardPrice, 2) }}</div>
                                <div class="price-badge px-4 mt-1">Special: ₱{{ number_format($defaultSpecialPrice, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Search Bar --}}
                    <div class="menu-search-container">
                        <div class="relative">
                            <input type="text" 
                                   id="menu-search" 
                                   placeholder="Search menus and items..." 
                                   class="menu-search-input">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="menu-preview-container">
                        {{-- Menu Tabs - Compact --}}
                        <div class="menu-tabs-container flex" id="menu-tabs-container">
                            <button type="button" class="menu-tab active" data-menu-category="standard">Standard Menu</button>
                            <button type="button" class="menu-tab" data-menu-category="special">Special Menu</button>
                        </div>

                        {{-- Meal Type Buttons --}}
                        <div class="flex flex-wrap p-2 bg-white border-b" id="meal-type-buttons">
                            <button type="button" class="meal-type-btn active" data-meal-type="all">All Meals</button>
                            <button type="button" class="meal-type-btn" data-meal-type="breakfast">Breakfast</button>
                            <button type="button" class="meal-type-btn" data-meal-type="am_snacks">AM Snacks</button>
                            <button type="button" class="meal-type-btn" data-meal-type="lunch">Lunch</button>
                            <button type="button" class="meal-type-btn" data-meal-type="pm_snacks">PM Snacks</button>
                            <button type="button" class="meal-type-btn" data-meal-type="dinner">Dinner</button>
                        </div>

                        {{-- Menu Details Containers - Fixed --}}
                        <div id="standard-menu-details" class="menu-content-area">
                            @if(isset($menus))
                                @foreach($menus as $meal_time => $types)
                                    @if(isset($types['standard']))
                                        <div class="meal-type-section {{ $loop->first ? 'active' : '' }}" data-meal-type="{{ $meal_time }}">
                                            @foreach($types['standard'] as $menu)
                                                <div class="menu-list-item compact-menu-item rounded-lg shadow-sm" data-searchable="{{ strtolower($menu->name) }} {{ strtolower($meal_time) }} @if($menu->items) @foreach($menu->items as $item) {{ strtolower($item->name) }} @endforeach @endif">
                                                    <h4 class="text-lg font-bold text-green-800 mb-2 menu-item-name">{{ $menu->name }}</h4>
                                                    <div class="text-xs text-green-600 mb-2 font-medium capitalize bg-green-50 px-2 py-1 rounded inline-block meal-time">{{ $meal_time }}</div>
                                                    <ul class="text-sm space-y-1 mt-2 menu-items-list">
                                                        @if($menu->items && count($menu->items) > 0)
                                                            @foreach($menu->items as $item)
                                                                <li class="flex items-center menu-item">
                                                                    <span class="text-green-500 mr-2">•</span>
                                                                    <span class="text-gray-700 item-name">{{ $item->name }}</span>
                                                                    @if($item->type)
                                                                        <span class="text-xs text-gray-500 ml-2">({{ $item->type }})</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            <li class="text-gray-500 text-sm">No items available</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="menu-list-item compact-menu-item rounded-lg">
                                    <p class="text-sm text-gray-600 text-center">No standard menus available.</p>
                                </div>
                            @endif
                            <div id="standard-no-results" class="no-results hidden">
                                No matching menus found in Standard Menu
                            </div>
                        </div>

                        <div id="special-menu-details" class="menu-content-area hidden">
                            @if(isset($menus))
                                @foreach($menus as $meal_time => $types)
                                    @if(isset($types['special']))
                                        <div class="meal-type-section {{ $loop->first ? 'active' : '' }}" data-meal-type="{{ $meal_time }}">
                                            @foreach($types['special'] as $menu)
                                                <div class="menu-list-item compact-menu-item rounded-lg shadow-sm border-green-300" data-searchable="{{ strtolower($menu->name) }} {{ strtolower($meal_time) }} @if($menu->items) @foreach($menu->items as $item) {{ strtolower($item->name) }} @endforeach @endif">
                                                    <h4 class="text-lg font-bold text-green-800 mb-2 menu-item-name">{{ $menu->name }}</h4>
                                                    <div class="text-xs text-green-600 mb-2 font-medium capitalize bg-green-50 px-2 py-1 rounded inline-block meal-time">{{ $meal_time }}</div>
                                                    <div class="text-xs font-semibold text-green-700 mb-2 bg-yellow-100 px-2 py-1 rounded inline-block">Premium Selection</div>
                                                    <ul class="text-sm space-y-1 mt-2 menu-items-list">
                                                        @if($menu->items && count($menu->items) > 0)
                                                            @foreach($menu->items as $item)
                                                                <li class="flex items-center menu-item">
                                                                    <span class="text-green-500 mr-2">•</span>
                                                                    <span class="text-gray-700 item-name">{{ $item->name }}</span>
                                                                    @if($item->type)
                                                                        <span class="text-xs text-gray-500 ml-2">({{ $item->type }})</span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            <li class="text-gray-500 text-sm">No items available</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="menu-list-item compact-menu-item rounded-lg">
                                    <p class="text-sm text-gray-600 text-center">No special menus available.</p>
                                </div>
                            @endif
                            <div id="special-no-results" class="no-results hidden">
                                No matching menus found in Special Menu
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Procedure Card --}}
                <div class="card p-6 border-l-4 border-clsu-green">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">Payment Methods</h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-clsu-green rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">1</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">CLSU Cashier</h4>
                                <p class="text-sm text-gray-600">On-site cash deposit</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-clsu-green rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">2</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Landbank Online</h4>
                                <p class="text-sm text-gray-600">Fund transfer</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-clsu-green rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-bold">3</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">GCash</h4>
                                <p class="text-sm text-gray-600">Transfer/deposit only</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirm Your Reservation</h3>
            <p class="text-sm text-gray-600">Please review your order summary before confirming</p>
        </div>

        <div class="border border-gray-200 rounded-lg p-6 mb-6">
            <h4 class="text-lg font-bold text-clsu-green mb-4">Reservation Summary</h4>
            
            <div id="modal-reservation-summary">
                <!-- Dynamic content will be inserted here -->
            </div>

            <div class="total-section">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">Total Amount:</span>
                    <span id="modal-total-amount" class="text-2xl font-bold text-clsu-green">₱0.00</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold">Important:</p>
                    <p class="mt-1">Your reservation will be pending until payment is confirmed. Please proceed to payment within 3 days to secure your booking.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeConfirmationModal()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-150 font-medium">
                Cancel
            </button>
            <button type="button" onclick="submitReservation()" class="px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                Confirm Reservation
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="confirmation-modal">
    <div class="confirmation-modal-content">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Reservation Successful!</h3>
            <p class="text-sm text-gray-600">Your reservation has been created successfully.</p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-green-800">
                    <p class="font-semibold">What happens next?</p>
                    <ul class="mt-2 space-y-1">
                        <li>• You will be redirected to your reservation details</li>
                        <li>• You'll receive a confirmation email</li>
                        <li>• Please proceed with payment within 3 days</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex justify-center">
            <button type="button" onclick="redirectToReservationDetails()" class="px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-medium">
                View Reservation Details
            </button>
        </div>
    </div>
</div>

<script>
    // Global variables for day management
    let currentDay = 1;
    let totalDays = 1;
    let dayData = {};

    // Make functions globally available first
    window.closeConfirmationModal = closeConfirmationModal;
    window.submitReservation = submitReservation;
    window.redirectToReservationDetails = redirectToReservationDetails;
    window.closeSuccessModal = closeSuccessModal;

    // Define global functions
    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.style.display = 'none';
    }

    function submitReservation() {
        // Close confirmation modal first
        closeConfirmationModal();
        
        // Show loading state
        const confirmButton = document.getElementById('confirm-button');
        const originalText = confirmButton.innerHTML;
        confirmButton.innerHTML = '<span class="animate-spin mr-2">⟳</span> Processing...';
        confirmButton.disabled = true;

        // Simulate processing delay
        setTimeout(() => {
            // Show success modal
            showSuccessModal();
            
            // Reset button state
            confirmButton.innerHTML = originalText;
            confirmButton.disabled = false;
        }, 1500);
    }

    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'none';
    }

    function redirectToReservationDetails() {
        // Close success modal
        closeSuccessModal();
        
        // Simply redirect to the reservation details page using Laravel route
        window.location.href = '{{ route('reservation_details') }}';
    }

    // Notification system
    function showNotification(message, type = 'success', duration = 5000) {
        // Remove existing notifications
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const icon = type === 'success' ? 
            '<svg class="notification-icon w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
            '<svg class="notification-icon w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        notification.innerHTML = `
            <div class="notification-content">
                ${icon}
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto remove after duration
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, duration);

        return notification;
    }

    // Get all menu prices from hidden data - improved version
    function initializeMenuPrices() {
        const menuPricesData = document.getElementById('menu-prices-data');
        const menuPrices = {};
        
        menuPricesData.querySelectorAll('div[data-meal-time]').forEach(priceElement => {
            const mealTime = priceElement.getAttribute('data-meal-time');
            const standardPrice = parseFloat(priceElement.getAttribute('data-standard-price')) || 150;
            const specialPrice = parseFloat(priceElement.getAttribute('data-special-price')) || 200;
            
            menuPrices[mealTime] = {
                standard: standardPrice,
                special: specialPrice
            };
        });
        
        return menuPrices;
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize day system
        initializeDaySystem();

        // Get menu prices
        const menuPrices = initializeMenuPrices();

        // --- DAY SYSTEM FUNCTIONS ---
        function initializeDaySystem() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            totalDays = parseInt(document.getElementById('number-of-days').value);
            
            // Generate day tabs
            generateDayTabs(startDate, totalDays);
            
            // Generate day content areas
            generateDayContentAreas(totalDays);
            
            // Initialize first day
            switchToDay(1);
        }

        function generateDayTabs(startDate, totalDays) {
            const dayTabsContainer = document.getElementById('day-tabs');
            const dayInfo = document.getElementById('day-info');
            const start = new Date(startDate);
            
            dayTabsContainer.innerHTML = '';
            
            for (let i = 1; i <= totalDays; i++) {
                const currentDate = new Date(start);
                currentDate.setDate(start.getDate() + i - 1);
                
                const tab = document.createElement('button');
                tab.type = 'button';
                tab.className = `day-tab ${i === 1 ? 'active' : ''}`;
                tab.textContent = `Day ${i}`;
                tab.dataset.day = i;
                tab.dataset.date = currentDate.toISOString().split('T')[0];
                
                // Add time information if available from dayTimes
                const dateKey = currentDate.toISOString().split('T')[0];
                @if(!empty($dayTimes) && is_array($dayTimes))
                    @foreach($dayTimes as $date => $times)
                        if (dateKey === '{{ $date }}') {
                            tab.dataset.startTime = '{{ $times["start_time"] ?? "07:00" }}';
                            tab.dataset.endTime = '{{ $times["end_time"] ?? "10:00" }}';
                        }
                    @endforeach
                @endif
                
                tab.addEventListener('click', () => switchToDay(i));
                dayTabsContainer.appendChild(tab);
            }
    
            updateDayInfo(1);
        }

        function updateDayInfo(day) {
        const dayInfo = document.getElementById('day-info');
        const activeTab = document.querySelector(`.day-tab[data-day="${day}"]`);
        
        if (activeTab) {
            const date = new Date(activeTab.dataset.date);
            const formattedDate = date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            let timeInfo = '';
            if (activeTab.dataset.startTime && activeTab.dataset.endTime) {
                timeInfo = ` | Time: ${activeTab.dataset.startTime} to ${activeTab.dataset.endTime}`;
            }
            
            dayInfo.innerHTML = `Currently viewing: <strong>${formattedDate}</strong>${timeInfo}`;
        }
    }


        function generateDayContentAreas(totalDays) {
            const container = document.getElementById('day-content-container');
            container.innerHTML = '';
            
            @php
                $meal_times = ['breakfast' => 'Breakfast', 'am_snacks' => 'A.M. Snacks', 'lunch' => 'Lunch', 'pm_snacks' => 'P.M. Snacks', 'dinner' => 'Dinner'];
                $categories = ['standard' => 'Standard Menu', 'special' => 'Special Menu'];
            @endphp
            
            for (let day = 1; day <= totalDays; day++) {
                const dayContent = document.createElement('div');
                dayContent.className = `day-content ${day === 1 ? 'active' : ''}`;
                dayContent.id = `day-${day}-content`;
                dayContent.dataset.day = day;
                
                let html = `<div class="space-y-4">`;
                
                @foreach ($meal_times as $meal_key => $meal_label)
                    @php
                        $standardPrice = isset($menuPrices['standard'][$meal_key][0]) ? $menuPrices['standard'][$meal_key][0]->price : $defaultStandardPrice;
                        $specialPrice = isset($menuPrices['special'][$meal_key][0]) ? $menuPrices['special'][$meal_key][0]->price : $defaultSpecialPrice;
                    @endphp
                    
                    html += `
                    <div class="meal-card bg-white p-4 rounded-lg border border-gray-200 relative" id="day-${day}-{{ $meal_key }}-card">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                            <div class="md:col-span-3">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-clsu-green rounded-full mr-3"></div>
                                    <label class="font-bold text-lg text-gray-800">{{ $meal_label }}</label>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                                <select name="reservations[${day}][{{ $meal_key }}][category]" class="category-select w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green bg-white" data-day="${day}" data-meal-time="{{ $meal_key }}">
                                    @foreach ($categories as $cat_key => $cat_label)
                                        <option value="{{ $cat_key }}" 
                                                @if($cat_key === 'standard' && $meal_key === 'breakfast') selected @endif>
                                            {{ $cat_label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Menu Choice</label>
                                <select name="reservations[${day}][{{ $meal_key }}][menu]" class="menu-select w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green bg-white" data-day="${day}" data-meal-time="{{ $meal_key }}">
                                    <!-- Menu options will be dynamically populated based on category selection -->
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Pax Quantity</label>
                                <div class="flex items-center">
                                    <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-l-md flex items-center justify-center text-lg font-bold" data-action="decrement" data-day="${day}" data-meal-time="{{ $meal_key }}">-</button>
                                    <input type="number" name="reservations[${day}][{{ $meal_key }}][qty]" value="0" min="10" max="100" class="quantity-input w-12 h-8 text-center border-t border-b border-gray-300 p-0 text-sm focus:ring-0 focus:border-gray-300 bg-white" data-day="${day}" data-meal-time="{{ $meal_key }}" readonly>
                                    <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-r-md flex items-center justify-center text-lg font-bold" data-action="increment" data-day="${day}" data-meal-time="{{ $meal_key }}">+</button>
                                </div>
                                <div class="text-xs text-red-500 mt-1 min-h-4 quantity-error" style="display: none;">
                                    Minimum 10 pax required
                                </div>
                            </div>
                        </div>
                    </div>`;
                @endforeach
                
                html += `</div>`;
                dayContent.innerHTML = html;
                container.appendChild(dayContent);
            }
            
            // Initialize event listeners for dynamically created elements
            initializeEventListeners();
            
            // Initialize menu options for all selects
            initializeMenuOptions();
        }

        function initializeMenuOptions() {
            // Initialize menu options for all category selects
            document.querySelectorAll('.category-select').forEach(select => {
                updateMenuOptions(select);
            });
        }

        function updateMenuOptions(categorySelect) {
            const day = categorySelect.dataset.day;
            const mealTime = categorySelect.dataset.mealTime;
            const selectedCategory = categorySelect.value;
            const menuSelect = document.querySelector(`select[data-day="${day}"][data-meal-time="${mealTime}"].menu-select`);
            
            // Clear existing options
            menuSelect.innerHTML = '';
            
            // Get the appropriate menu data based on category and meal time
            const menuData = getMenuData(mealTime, selectedCategory);
            
            if (menuData && menuData.length > 0) {
                // Add options from the selected category
                menuData.forEach(menu => {
                    const option = document.createElement('option');
                    option.value = menu.id;
                    option.textContent = menu.name;
                    option.setAttribute('data-price', menu.price);
                    option.setAttribute('data-category', selectedCategory);
                    option.setAttribute('data-menu-name', menu.name);
                    menuSelect.appendChild(option);
                });
            } else {
                // No menus available
                const option = document.createElement('option');
                option.value = '';
                option.textContent = `No ${selectedCategory} menus available`;
                menuSelect.appendChild(option);
            }
            
            // Trigger summary update
            updateSummary();
        }

        function getMenuData(mealTime, category) {
            // This function should return the appropriate menu data
            // You'll need to adjust this based on your actual data structure
            @php
                $menuData = [];
                foreach ($meal_times as $meal_key => $meal_label) {
                    if (isset($menus[$meal_key])) {
                        foreach ($categories as $cat_key => $cat_label) {
                            if (isset($menus[$meal_key][$cat_key])) {
                                foreach ($menus[$meal_key][$cat_key] as $menu) {
                                    $price = $cat_key === 'standard' ? 
                                        (isset($menuPrices['standard'][$meal_key][0]) ? $menuPrices['standard'][$meal_key][0]->price : $defaultStandardPrice) :
                                        (isset($menuPrices['special'][$meal_key][0]) ? $menuPrices['special'][$meal_key][0]->price : $defaultSpecialPrice);
                                    
                                    $menuData[$meal_key][$cat_key][] = [
                                        'id' => $menu->id,
                                        'name' => $menu->name,
                                        'price' => $price
                                    ];
                                }
                            }
                        }
                    }
                }
            @endphp

            // Return the appropriate menu data based on mealTime and category
            const menuData = @json($menuData);
            return menuData[mealTime] && menuData[mealTime][category] ? menuData[mealTime][category] : [];
        }

        function switchToDay(day) {
            // Update active tab
            document.querySelectorAll('.day-tab').forEach(tab => {
                tab.classList.toggle('active', parseInt(tab.dataset.day) === day);
            });
            
            // Update content visibility
            document.querySelectorAll('.day-content').forEach(content => {
                content.classList.toggle('active', parseInt(content.dataset.day) === day);
            });
            
            // Update current day
            currentDay = day;
            
            // Update day info
            updateDayInfo(day);
            
            // Update summary for current day
            updateSummary();
        }

        function updateDayInfo(day) {
            const dayInfo = document.getElementById('day-info');
            const activeTab = document.querySelector(`.day-tab[data-day="${day}"]`);
            
            if (activeTab) {
                const date = new Date(activeTab.dataset.date);
                const formattedDate = date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                dayInfo.textContent = `Currently viewing: ${formattedDate}`;
            }
        }

        function initializeEventListeners() {
            // Quantity buttons
            document.querySelectorAll('.qty-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const action = e.currentTarget.dataset.action;
                    const day = e.currentTarget.dataset.day;
                    const mealTime = e.currentTarget.dataset.mealTime;
                    
                    const input = document.querySelector(`input[data-day="${day}"][data-meal-time="${mealTime}"]`);
                    let value = parseInt(input.value);

                    if (action === 'increment') {
                        value = value < 100 ? value + 1 : 100;
                    } else if (action === 'decrement') {
                        value = value > 10 ? value - 1 : 10;
                    }
                    input.value = value;
                    
                    updateCardStyling(day, mealTime);
                    updateSummary();
                    validateQuantity(input);
                });
            });

            // Category change handlers - UPDATED
            document.querySelectorAll('.category-select').forEach(select => {
                select.addEventListener('change', function() {
                    updateMenuOptions(this);
                });
            });

            // Menu selection change handlers
            document.querySelectorAll('.menu-select').forEach(select => {
                select.addEventListener('change', function() {
                    updateSummary();
                });
            });
        }

        // Update card styling based on quantity
        function updateCardStyling(day, mealTime) {
            const input = document.querySelector(`input[data-day="${day}"][data-meal-time="${mealTime}"]`);
            const card = input.closest('.meal-card');
            if (parseInt(input.value) > 0) {
                card.classList.add('has-quantity');
            } else {
                card.classList.remove('has-quantity');
            }
        }

        // Validate quantity (minimum 10 pax)
        function validateQuantity(input) {
            const value = parseInt(input.value);
            const errorElement = input.closest('.md\\:col-span-2').querySelector('.quantity-error');
            
            if (value < 10 && value > 0) {
                input.classList.add('qty-error');
                errorElement.style.display = 'block';
                return false;
            } else {
                input.classList.remove('qty-error');
                errorElement.style.display = 'none';
                return true;
            }
        }

        // --- QUICK ACTION BUTTONS ---
        document.getElementById('clear-current-day').addEventListener('click', () => {
            document.querySelectorAll(`.day-content.active .quantity-input`).forEach(input => {
                input.value = 0;
                const day = input.dataset.day;
                const mealTime = input.dataset.mealTime;
                updateCardStyling(day, mealTime);
                validateQuantity(input);
            });
            updateSummary();
            showNotification('Current day cleared successfully!', 'success');
        });

        document.getElementById('clear-all-days').addEventListener('click', () => {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.value = 0;
                const day = input.dataset.day;
                const mealTime = input.dataset.mealTime;
                updateCardStyling(day, mealTime);
                validateQuantity(input);
            });
            updateSummary();
            showNotification('All days cleared successfully!', 'success');
        });

        document.getElementById('set-standard-all-days').addEventListener('click', () => {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.value = 10;
                const day = input.dataset.day;
                const mealTime = input.dataset.mealTime;
                updateCardStyling(day, mealTime);
                validateQuantity(input);
            });
            updateSummary();
            showNotification('10 pax set for all meals across all days!', 'success');
        });

        document.getElementById('copy-to-all-days').addEventListener('click', () => {
            const currentDayData = getCurrentDayData();
            
            // Apply to all other days
            for (let day = 1; day <= totalDays; day++) {
                if (day !== currentDay) {
                    applyDayData(day, currentDayData);
                }
            }
            
            updateSummary();
            showNotification('Current day selections copied to all other days!', 'success');
        });

        function getCurrentDayData() {
            const data = {};
            document.querySelectorAll(`.day-content.active .meal-card`).forEach(card => {
                const mealTime = card.id.replace(`day-${currentDay}-`, '').replace('-card', '');
                const categorySelect = card.querySelector('.category-select');
                const menuSelect = card.querySelector('.menu-select');
                const quantityInput = card.querySelector('.quantity-input');
                
                data[mealTime] = {
                    category: categorySelect.value,
                    menu: menuSelect.value,
                    quantity: quantityInput.value
                };
            });
            return data;
        }

        function applyDayData(targetDay, data) {
            Object.keys(data).forEach(mealTime => {
                const categorySelect = document.querySelector(`select[data-day="${targetDay}"][data-meal-time="${mealTime}"]`);
                const menuSelect = document.querySelector(`select[data-day="${targetDay}"][data-meal-time="${mealTime}"].menu-select`);
                const quantityInput = document.querySelector(`input[data-day="${targetDay}"][data-meal-time="${mealTime}"]`);
                
                if (categorySelect && menuSelect && quantityInput) {
                    categorySelect.value = data[mealTime].category;
                    // Update menu options first
                    updateMenuOptions(categorySelect);
                    
                    // Then set the menu value after a short delay to ensure options are populated
                    setTimeout(() => {
                        menuSelect.value = data[mealTime].menu;
                        quantityInput.value = data[mealTime].quantity;
                        
                        updateCardStyling(targetDay, mealTime);
                        validateQuantity(quantityInput);
                        updateSummary();
                    }, 100);
                }
            });
        }

        // Update summary information with correct pricing for all days - FIXED VERSION
        function updateSummary() {
            let totalPax = 0;
            let selectedMeals = 0;
            let estimatedTotal = 0;
            
            document.querySelectorAll('.quantity-input').forEach(input => {
                const value = parseInt(input.value);
                if (value > 0) {
                    totalPax += value;
                    selectedMeals++;
                    
                    const day = input.dataset.day;
                    const mealTime = input.dataset.mealTime;
                    const menuSelect = document.querySelector(`select[data-day="${day}"][data-meal-time="${mealTime}"].menu-select`);
                    
                    if (menuSelect && menuSelect.options[menuSelect.selectedIndex]) {
                        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                        const priceText = selectedOption.getAttribute('data-price');
                        
                        // Safely parse the price with fallback
                        let price = 0;
                        if (priceText) {
                            price = parseFloat(priceText);
                        } else {
                            // Fallback prices based on category
                            const categorySelect = document.querySelector(`select[data-day="${day}"][data-meal-time="${mealTime}"].category-select`);
                            const category = categorySelect ? categorySelect.value : 'standard';
                            price = category === 'special' ? 200 : 150; // Default prices
                        }
                        
                        // Ensure price is a valid number
                        if (!isNaN(price) && isFinite(price)) {
                            estimatedTotal += value * price;
                        } else {
                            console.warn('Invalid price found:', priceText);
                            estimatedTotal += value * 150; // Fallback to standard price
                        }
                    }
                }
            });
            
            document.getElementById('total-pax-count').textContent = totalPax;
            
            // Format the total amount safely
            let formattedTotal = '₱0.00';
            if (!isNaN(estimatedTotal) && isFinite(estimatedTotal)) {
                formattedTotal = `₱${estimatedTotal.toLocaleString('en-PH', { 
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2 
                })}`;
            }
            
            document.getElementById('estimated-total').textContent = formattedTotal;
        }

        // --- MEAL TYPE BUTTONS FUNCTIONALITY ---
        document.querySelectorAll('.meal-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active meal type button
                document.querySelectorAll('.meal-type-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const selectedMealType = this.dataset.mealType;
                const activeTab = document.querySelector('.menu-tab.active').dataset.menuCategory;
                const activeContainer = document.getElementById(`${activeTab}-menu-details`);
                
                // Show/hide meal type sections
                activeContainer.querySelectorAll('.meal-type-section').forEach(section => {
                    if (selectedMealType === 'all' || section.dataset.mealType === selectedMealType) {
                        section.classList.add('active');
                    } else {
                        section.classList.remove('active');
                    }
                });
                
                // Trigger search to apply current filter
                performSearch();
            });
        });

        // --- MENU TABS LOGIC ---
        const standardDetails = document.getElementById('standard-menu-details');
        const specialDetails = document.getElementById('special-menu-details');
        const menuTabs = document.querySelectorAll('.menu-tab');

        menuTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                menuTabs.forEach(t => {
                    t.classList.remove('active', 'bg-green-500', 'text-white');
                    t.classList.add('bg-green-200', 'text-green-800');
                });
                tab.classList.add('active', 'bg-green-500', 'text-white');
                tab.classList.remove('bg-green-200', 'text-green-800');

                if (tab.dataset.menuCategory === 'standard') {
                    standardDetails.classList.remove('hidden');
                    specialDetails.classList.add('hidden');
                } else {
                    standardDetails.classList.add('hidden');
                    specialDetails.classList.remove('hidden');
                }
                
                // Trigger search on tab change to apply current filter
                performSearch();
            });
        });

        // --- SEARCH FUNCTIONALITY ---
        const searchInput = document.getElementById('menu-search');
        
        searchInput.addEventListener('input', performSearch);
        
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activeTab = document.querySelector('.menu-tab.active').dataset.menuCategory;
            const activeContainer = activeTab === 'standard' ? standardDetails : specialDetails;
            const noResultsElement = document.getElementById(`${activeTab}-no-results`);
            
            let hasVisibleItems = false;
            
            // Get active meal type filter
            const activeMealType = document.querySelector('.meal-type-btn.active').dataset.mealType;
            
            // Search through all menu items in the active tab and meal type
            activeContainer.querySelectorAll('.meal-type-section').forEach(section => {
                // Skip sections that are not active based on meal type filter
                if (activeMealType !== 'all' && section.dataset.mealType !== activeMealType) {
                    return;
                }
                
                section.querySelectorAll('.menu-list-item').forEach(item => {
                    const searchableText = item.dataset.searchable.toLowerCase();
                    const menuName = item.querySelector('.menu-item-name');
                    const menuItems = item.querySelectorAll('.item-name');
                    const mealTime = item.querySelector('.meal-time');
                    
                    // Remove previous highlights
                    if (menuName) {
                        menuName.innerHTML = menuName.textContent;
                    }
                    menuItems.forEach(menuItem => {
                        menuItem.innerHTML = menuItem.textContent;
                    });
                    
                    // Check if item matches search
                    if (searchTerm === '' || searchableText.includes(searchTerm)) {
                        item.style.display = 'block';
                        hasVisibleItems = true;
                        
                        // Highlight matching text
                        if (searchTerm !== '') {
                            highlightText(menuName, searchTerm);
                            menuItems.forEach(menuItem => {
                                highlightText(menuItem, searchTerm);
                            });
                            if (mealTime) {
                                highlightText(mealTime, searchTerm);
                            }
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // Show/hide no results message
            if (hasVisibleItems || searchTerm === '') {
                noResultsElement.classList.add('hidden');
            } else {
                noResultsElement.classList.remove('hidden');
            }
        }
        
        function highlightText(element, searchTerm) {
            if (!element) return;
            
            const text = element.textContent;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlightedText = text.replace(regex, '<span class="search-highlight">$1</span>');
            element.innerHTML = highlightedText;
        }

        // --- FORM VALIDATION ---
        function validateForm() {
            let hasErrors = false;
            let hasSelectedMeals = false;
            
            document.querySelectorAll('.quantity-input').forEach(input => {
                if (!validateQuantity(input) && parseInt(input.value) > 0) {
                    hasErrors = true;
                }
                if (parseInt(input.value) > 0) {
                    hasSelectedMeals = true;
                }
            });
            
            if (!hasSelectedMeals) {
                showNotification('Please select at least one meal with minimum 10 pax.', 'error', 5000);
                return false;
            }
            
            if (hasErrors) {
                showNotification('Please ensure all selected meals have at least 10 pax.', 'error', 5000);
                return false;
            }
            
            return true;
        }

        // --- CONFIRMATION MODAL LOGIC ---
        document.getElementById('confirm-button').addEventListener('click', function() {
            if (validateForm()) {
                showConfirmationModal();
            }
        });

        function showConfirmationModal() {
            const modal = document.getElementById('confirmationModal');
            const summaryContainer = document.getElementById('modal-reservation-summary');
            let totalAmount = 0;
            
            // Clear previous summary
            summaryContainer.innerHTML = '';
            
            // Build reservation summary for all days
            for (let day = 1; day <= totalDays; day++) {
                let dayHasMeals = false;
                let dayContent = '';
                let dayTotal = 0;
                
                document.querySelectorAll(`.day-content[data-day="${day}"] .meal-card`).forEach(card => {
                    const mealTime = card.id.replace(`day-${day}-`, '').replace('-card', '');
                    const quantityInput = card.querySelector('.quantity-input');
                    const quantity = parseInt(quantityInput.value);
                    
                    if (quantity > 0) {
                        dayHasMeals = true;
                        const menuSelect = card.querySelector('.menu-select');
                        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                        const menuName = selectedOption.textContent;
                        const categorySelect = card.querySelector('.category-select');
                        const category = categorySelect ? categorySelect.value : 'standard';
                        const priceText = selectedOption.getAttribute('data-price');
                        const price = parseFloat(priceText) || (category === 'special' ? 200 : 150);
                        const mealTotal = quantity * price;
                        dayTotal += mealTotal;
                        
                        dayContent += `
                            <div class="summary-item">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 capitalize">${mealTime.replace('_', ' ')}</div>
                                        <div class="text-sm text-gray-600">${menuName} (${category})</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900">${quantity} pax × ₱${price.toFixed(2)}</div>
                                        <div class="font-bold text-clsu-green">₱${mealTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });
                
                if (dayHasMeals) {
                    const dayTab = document.querySelector(`.day-tab[data-day="${day}"]`);
                    const dayDate = new Date(dayTab.dataset.date).toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric' 
                    });
                    
                    const daySection = document.createElement('div');
                    daySection.className = 'mb-4';
                    daySection.innerHTML = `
                        <div class="font-bold text-lg text-clsu-green mb-2 border-b pb-2">Day ${day} (${dayDate})</div>
                        ${dayContent}
                        <div class="summary-item font-semibold border-t">
                            <div class="flex justify-between">
                                <span>Day ${day} Subtotal:</span>
                                <span class="text-clsu-green">₱${dayTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                            </div>
                        </div>
                    `;
                    summaryContainer.appendChild(daySection);
                    
                    totalAmount += dayTotal;
                }
            }
            
            // Update total amount in modal
            document.getElementById('modal-total-amount').textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            // Show modal
            modal.style.display = 'flex';
        }

        // --- BACK BUTTON FUNCTIONALITY ---
        document.getElementById('back-button').addEventListener('click', function() {
            window.history.back();
        });

        // Initialize category filters and summary
        document.querySelectorAll('.category-select').forEach(select => {
            select.dispatchEvent(new Event('change'));
        });
        updateSummary();
    });

    // Close modals when clicking outside
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmationModal();
        }
    });

    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSuccessModal();
        }
    });
</script>

@endsection