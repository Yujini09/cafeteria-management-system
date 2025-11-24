@extends('layouts.app')

@section('title', 'Menu Selection - CLSU RET Cafeteria')

@section('styles')
<style>
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
</style>
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
                        @endphp

                        {{-- Hidden fields for all menu prices --}}
                        <div id="menu-prices-data" style="display: none;">
                            @foreach($meal_times as $meal_key => $meal_label)
                                @php
                                    $standardPrice = isset($menuPrices['standard'][$meal_key][0]) ? $menuPrices['standard'][$meal_key][0]->price : $defaultStandardPrice;
                                    $specialPrice = isset($menuPrices['special'][$meal_key][0]) ? $menuPrices['special'][$meal_key][0]->price : $defaultSpecialPrice;
                                @endphp
                                <div data-meal-time="{{ $meal_key }}" 
                                     data-standard-price="{{ $standardPrice }}" 
                                     data-special-price="{{ $specialPrice }}">
                                </div>
                            @endforeach
                        </div>

                        {{-- Reservation Rows - Redesigned as cards --}}
                        <div class="space-y-4">
                            @foreach ($meal_times as $meal_key => $meal_label)
                                @php
                                    $standardPrice = isset($menuPrices['standard'][$meal_key][0]) ? $menuPrices['standard'][$meal_key][0]->price : $defaultStandardPrice;
                                    $specialPrice = isset($menuPrices['special'][$meal_key][0]) ? $menuPrices['special'][$meal_key][0]->price : $defaultSpecialPrice;
                                @endphp
                                <div class="meal-card bg-white p-4 rounded-lg border border-gray-200 relative" id="{{ $meal_key }}-card">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                                        {{-- Meal Name --}}
                                        <div class="md:col-span-3">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-clsu-green rounded-full mr-3"></div>
                                                <label for="{{ $meal_key }}_qty" class="font-bold text-lg text-gray-800">{{ $meal_label }}</label>
                                            </div>
                                        </div>

                                        {{-- Meal Category Dropdown --}}
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                                            <select id="{{ $meal_key }}_category" name="reservations[{{ $meal_key }}][category]" class="category-select w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green bg-white" data-meal-time="{{ $meal_key }}">
                                                @foreach ($categories as $cat_key => $cat_label)
                                                    <option value="{{ $cat_key }}" 
                                                            @if($cat_key === 'standard' && $meal_key === 'breakfast') selected @endif>
                                                        {{ $cat_label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Menu Choice Dropdown - Fixed --}}
                                        <div class="md:col-span-4">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Menu Choice</label>
                                            <select id="{{ $meal_key }}_menu" name="reservations[{{ $meal_key }}][menu]" class="menu-select w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green bg-white" data-meal-time="{{ $meal_key }}">
                                                @if(isset($menus[$meal_key]) && count($menus[$meal_key]) > 0)
                                                    {{-- Standard Menu Options --}}
                                                    @if(isset($menus[$meal_key]['standard']))
                                                        <optgroup label="Standard Menu" data-category="standard" data-price="{{ $standardPrice }}">
                                                            @foreach ($menus[$meal_key]['standard'] as $menu)
                                                                <option value="{{ $menu->id }}" 
                                                                        data-price="{{ $standardPrice }}" 
                                                                        data-category="standard"
                                                                        data-menu-name="{{ $menu->name }}">
                                                                    {{ $menu->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                    
                                                    {{-- Special Menu Options --}}
                                                    @if(isset($menus[$meal_key]['special']))
                                                        <optgroup label="Special Menu" data-category="special" data-price="{{ $specialPrice }}">
                                                            @foreach ($menus[$meal_key]['special'] as $menu)
                                                                <option value="{{ $menu->id }}" 
                                                                        data-price="{{ $specialPrice }}" 
                                                                        data-category="special"
                                                                        data-menu-name="{{ $menu->name }}">
                                                                    {{ $menu->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @else
                                                    <option value="">No menus available for {{ $meal_label }}</option>
                                                @endif
                                            </select>
                                        </div>

                                        {{-- Quantity (Pax) Input with +/- Buttons --}}
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Pax Quantity</label>
                                            <div class="flex items-center">
                                                <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-l-md flex items-center justify-center text-lg font-bold" data-action="decrement" data-target="#{{ $meal_key }}_qty">-</button>
                                                <input type="number" id="{{ $meal_key }}_qty" name="reservations[{{ $meal_key }}][qty]" value="0" min="10" max="100" class="quantity-input w-12 h-8 text-center border-t border-b border-gray-300 p-0 text-sm focus:ring-0 focus:border-gray-300 bg-white" readonly>
                                                <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-r-md flex items-center justify-center text-lg font-bold" data-action="increment" data-target="#{{ $meal_key }}_qty">+</button>
                                            </div>
                                            <div class="text-xs text-red-500 mt-1 min-h-4 quantity-error" style="display: none;">
                                                Minimum 10 pax required
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Quick Actions --}}
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap gap-2">
                                <button type="button" id="clear-all-meals" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                    Clear All
                                </button>
                                <button type="button" id="set-standard-quantity" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                    Set 10 Pax for All
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

                        {{-- Menu Details Containers - Fixed --}}
                        <div id="standard-menu-details" class="menu-content-area">
                            @if(isset($menus))
                                @foreach($menus as $meal_time => $types)
                                    @if(isset($types['standard']))
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

<!-- Add this Success Modal after the Confirmation Modal -->
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
    
    // Simply redirect to the reservation details page
    window.location.href = '/reservation_details'; // Adjust this URL to match your route
}

    document.addEventListener('DOMContentLoaded', () => {
        // Get all menu prices from hidden data
        const menuPricesData = document.getElementById('menu-prices-data');
        const menuPrices = {};
        
        menuPricesData.querySelectorAll('div[data-meal-time]').forEach(priceElement => {
            const mealTime = priceElement.getAttribute('data-meal-time');
            menuPrices[mealTime] = {
                standard: parseFloat(priceElement.getAttribute('data-standard-price')),
                special: parseFloat(priceElement.getAttribute('data-special-price'))
            };
        });

        // --- 1. Quantity Buttons Logic with 10 pax minimum ---
        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.currentTarget.dataset.action;
                const targetId = e.currentTarget.dataset.target;
                const input = document.querySelector(targetId);
                let value = parseInt(input.value);

                if (action === 'increment') {
                    value = value < 100 ? value + 1 : 100; // Cap at 100
                } else if (action === 'decrement') {
                    value = value > 10 ? value - 1 : 10; // Don't go below 10
                }
                input.value = value;
                
                // Update card styling based on quantity
                updateCardStyling(input);
                updateSummary();
                validateQuantity(input);
            });
        });

        // Update card styling based on quantity
        function updateCardStyling(input) {
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

        // Update summary information with correct pricing
        function updateSummary() {
            let totalPax = 0;
            let selectedMeals = 0;
            let estimatedTotal = 0;
            
            document.querySelectorAll('.quantity-input').forEach(input => {
                const value = parseInt(input.value);
                if (value > 0) {
                    totalPax += value;
                    selectedMeals++;
                    
                    // Get the meal time from the input ID
                    const mealTime = input.id.replace('_qty', '');
                    const menuSelect = document.getElementById(`${mealTime}_menu`);
                    const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                    const price = parseFloat(selectedOption.getAttribute('data-price'));
                    
                    estimatedTotal += value * price;
                }
            });
            
            document.getElementById('total-pax-count').textContent = totalPax;
            document.getElementById('estimated-total').textContent = `₱${estimatedTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }

        // --- 2. Category Change Handler - Filter menus by category ---
        document.querySelectorAll('.category-select').forEach(select => {
            select.addEventListener('change', function() {
                const mealTime = this.dataset.mealTime;
                const selectedCategory = this.value;
                const menuSelect = document.getElementById(`${mealTime}_menu`);
                
                // Show/hide options based on category
                const options = menuSelect.querySelectorAll('option');
                options.forEach(option => {
                    if (option.parentElement.tagName === 'OPTGROUP') {
                        const optgroup = option.parentElement;
                        if (optgroup.dataset.category === selectedCategory) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
                
                // Select first available option in the category
                const availableOptions = menuSelect.querySelectorAll(`option[data-category="${selectedCategory}"]`);
                if (availableOptions.length > 0) {
                    menuSelect.value = availableOptions[0].value;
                }
                
                updateSummary();
            });
        });

        // --- 3. Menu Selection Change Handler - Update pricing ---
        document.querySelectorAll('.menu-select').forEach(select => {
            select.addEventListener('change', function() {
                updateSummary();
            });
        });

        // --- 4. Menu Tabs Logic (Show/Hide Details) ---
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

        // --- 5. Quick Action Buttons ---
        document.getElementById('clear-all-meals').addEventListener('click', () => {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.value = 0;
                updateCardStyling(input);
                validateQuantity(input);
            });
            updateSummary();
        });

        document.getElementById('set-standard-quantity').addEventListener('click', () => {
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.value = 10;
                updateCardStyling(input);
                validateQuantity(input);
            });
            updateSummary();
        });

        // --- 6. Search Functionality ---
        const searchInput = document.getElementById('menu-search');
        
        searchInput.addEventListener('input', performSearch);
        
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const activeTab = document.querySelector('.menu-tab.active').dataset.menuCategory;
            const activeContainer = activeTab === 'standard' ? standardDetails : specialDetails;
            const noResultsElement = document.getElementById(`${activeTab}-no-results`);
            
            let hasVisibleItems = false;
            
            // Search through all menu items in the active tab
            activeContainer.querySelectorAll('.menu-list-item').forEach(item => {
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

        // --- 7. Form Validation ---
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
                alert('Please select at least one meal with minimum 10 pax.');
                return false;
            }
            
            if (hasErrors) {
                alert('Please ensure all selected meals have at least 10 pax.');
                return false;
            }
            
            return true;
        }

        // --- 8. Confirmation Modal Logic ---
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
            
            // Build reservation summary
            document.querySelectorAll('.meal-card').forEach(card => {
                const mealTime = card.id.replace('-card', '');
                const quantityInput = document.getElementById(`${mealTime}_qty`);
                const quantity = parseInt(quantityInput.value);
                
                if (quantity > 0) {
                    const menuSelect = document.getElementById(`${mealTime}_menu`);
                    const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                    const menuName = selectedOption.getAttribute('data-menu-name');
                    const category = selectedOption.getAttribute('data-category');
                    const price = parseFloat(selectedOption.getAttribute('data-price'));
                    const mealTotal = quantity * price;
                    totalAmount += mealTotal;
                    
                    const summaryItem = document.createElement('div');
                    summaryItem.className = 'summary-item';
                    summaryItem.innerHTML = `
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
                    `;
                    summaryContainer.appendChild(summaryItem);
                }
            });
            
            // Update total amount in modal
            document.getElementById('modal-total-amount').textContent = `₱${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            // Show modal
            modal.style.display = 'flex';
        }

        // --- 9. Notification System ---
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

        // --- 11. Back Button Functionality ---
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