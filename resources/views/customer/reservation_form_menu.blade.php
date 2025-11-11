@extends('layouts.app')

@section('title', 'Menu Selection - CLSU RET Cafeteria')

@section('styles')
{{-- Load Tailwind CSS and custom styles --}}
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
        background-color: #d9534f; /* Matches your Menu tab color */
        color: white;
        padding: 20px;
        border-radius: 12px 12px 0 0;
    }
    .qty-btn {
        transition: background-color 0.15s ease;
    }
    /* CLSU Green for primary buttons and focus states */
    .bg-clsu-green {
        background-color: #1a5e3d; /* Darker Green (Fictional CLSU Green) */
    }
    .hover\:bg-green-700:hover {
        background-color: #154d32;
    }
    .focus\:ring-clsu-green:focus {
        --tw-ring-color: #1a5e3d;
    }
    /* RET Orange for accents/titles */
    .text-ret-dark {
        color: #d9534f;
    }
    .border-ret-dark {
        border-color: #d9534f;
    }

    /* Style for the Menu Selection Tabs (based on image) */
    .menu-tab {
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 10px;
        transition: all 0.2s;
        border: 2px solid transparent;
        color: #333;
    }
    .menu-tab.active {
        background-color: #d9534f;
        color: white;
        border-color: #d9534f;
        box-shadow: 0 2px 4px rgba(217, 83, 79, 0.4);
    }
    .menu-tab:not(.active):hover {
        background-color: #f0f0f0;
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

<!-- Reservation Form - Menu Selection -->
<section class="py-10 bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Main Form (Menu Selection) - Occupies 7/12 columns on large screens --}}
            <div class="lg:col-span-7">
                <form action="{{ route('reservation.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Menu Selection Grid --}}
                    <div class="card p-6">
                        <h2 class="text-2xl font-bold mb-6 border-b pb-3 text-ret-dark">Meal Plan Selection</h2>

                        @php
                            $meal_times = ['breakfast' => 'Breakfast', 'am_snacks' => 'A.M. Snacks', 'lunch' => 'Lunch', 'pm_snacks' => 'P.M. Snacks', 'dinner' => 'Dinner'];
                            $categories = ['standard' => 'Standard Menu', 'special' => 'Special Menu'];
                        @endphp

                        <div class="hidden md:grid grid-cols-6 gap-4 text-sm font-semibold text-gray-700 mb-3">
                            <span class="col-span-1">Meal Time</span>
                            <span class="col-span-2">Meal Category</span>
                            <span class="col-span-2">Menu Choice</span>
                            <span class="col-span-1">Pax/Quantity</span>
                        </div>

                        {{-- Reservation Rows --}}
                        @foreach ($meal_times as $meal_key => $meal_label)
                            <div class="grid grid-cols-6 gap-4 items-center mb-6 p-3 border-b md:border-none last:border-b-0">
                                {{-- Meal Name --}}
                                <label for="{{ $meal_key }}_qty" class="col-span-6 md:col-span-1 font-extrabold text-lg text-gray-800">{{ $meal_label }}</label>

                                {{-- Meal Category Dropdown --}}
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Category</label>
                                    <select id="{{ $meal_key }}_category" name="reservations[{{ $meal_key }}][category]" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green">
                                        @foreach ($categories as $cat_key => $cat_label)
                                            <option value="{{ $cat_key }}" @if($cat_key === 'standard' && $meal_key === 'breakfast') selected @endif>{{ $cat_label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Menu Choice Dropdown --}}
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Menu Choice</label>
                                    <select id="{{ $meal_key }}_menu" name="reservations[{{ $meal_key }}][menu]" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green">
                                        @if(isset($menus[$meal_key]))
                                            @foreach ($menus[$meal_key] as $type => $menuList)
                                                <optgroup label="{{ ucfirst($type) }} Menu">
                                                    @foreach ($menuList as $menu)
                                                        <option value="{{ $menu->name }}">{{ $menu->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        @else
                                            <option value="">No menus available</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- Quantity (Pax) Input with +/- Buttons --}}
                                <div class="col-span-6 md:col-span-1 flex items-center justify-start md:justify-center mt-2 md:mt-0">
                                    <label class="block md:hidden text-xs font-medium text-gray-500 mr-2">Pax:</label>
                                    <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-l-md flex items-center justify-center text-xl font-bold" data-action="decrement" data-target="#{{ $meal_key }}_qty">-</button>
                                    <input type="number" id="{{ $meal_key }}_qty" name="reservations[{{ $meal_key }}][qty]" value="0" min="0" class="w-12 h-8 text-center border-t border-b border-gray-300 p-0 text-sm focus:ring-0 focus:border-gray-300" readonly>
                                    <button type="button" class="qty-btn bg-gray-200 text-gray-700 hover:bg-gray-300 w-8 h-8 rounded-r-md flex items-center justify-center text-xl font-bold" data-action="increment" data-target="#{{ $meal_key }}_qty">+</button>
                                </div>
                            </div>
                        @endforeach

                    </div>

                    {{-- Reservation Schedule and Notes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Schedule Card (Date/Time) --}}
                        <div class="card p-6">
                            <h3 class="text-xl font-bold mb-4 border-b pb-2">Reservation Schedule</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="reservation_date" class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" id="reservation_date" name="reservation_date" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green">
                                </div>
                                <div>
                                    <label for="reservation_time" class="block text-sm font-medium text-gray-700">Time</label>
                                    <input type="time" id="reservation_time" name="reservation_time" required class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm p-2.5 focus:ring-clsu-green focus:border-clsu-green">
                                </div>
                            </div>
                        </div>

                        {{-- Notes Section --}}
                        <div class="card p-6">
                            <h3 class="text-xl font-bold mb-4 border-b pb-2">Special Instructions / Notes</h3>
                            <textarea name="notes" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-clsu-green focus:border-clsu-green" placeholder="Add special instructions, allergy notes, or dietary restrictions..."></textarea>
                        </div>
                    </div>

                    {{-- Form Actions (Back/Confirm) --}}
                    <div class="flex justify-end space-x-4 pt-4">
                        {{-- BACK BUTTON MODIFICATION: Use JavaScript history.back() for form data persistence --}}
                        <button type="button" onclick="window.history.back()" class="px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-150 shadow-lg font-semibold">
                            Back
                        </button>
                        <button type="submit" class="px-8 py-3 bg-ret-dark text-white rounded-lg hover:bg-clsu-green transition duration-150 shadow-lg font-semibold">
                            Confirm Reservation
                        </button>
                    </div>

                </form>
            </div>

            {{-- Right Column (Menu Details and Payment) - Occupies 5/12 columns --}}
            <div class="lg:col-span-5 space-y-8">

                {{-- Menu Details Card --}}
                <div class="card">
                    <div class="menu-header">
                        <h2 class="text-2xl font-bold">Menu Details & Pricing</h2>
                        <p class="text-sm mt-1">Select a menu category to view details.</p>
                    </div>

                    <div class="p-6">
                        {{-- Price per head - Use dynamic values later --}}
                        <div class="flex justify-between items-baseline mb-4 p-2 border-b">
                            <span class="text-xl font-bold text-gray-800">Standard Menu Rate:</span>
                            <span class="text-2xl font-extrabold text-ret-dark">₱150.00 <span class="text-base font-normal text-gray-500">/head</span></span>
                        </div>

                        {{-- Menu Tabs --}}
                        <div class="flex flex-wrap mb-4" id="menu-tabs-container">
                            <button type="button" class="menu-tab active" data-menu-category="standard">Standard Menu</button>
                            <button type="button" class="menu-tab" data-menu-category="special">Special Menu</button>
                            {{-- Add more tabs here if needed --}}
                        </div>

                        {{-- Menu Details Containers --}}
                        <div id="standard-menu-details" class="space-y-4 pt-2">
                            @if(isset($menus))
                                @foreach($menus as $meal_time => $types)
                                    @if(isset($types['standard']))
                                        @foreach($types['standard'] as $menu)
                                            <div class="p-4 border border-green-200 bg-green-50 rounded-lg shadow-inner">
                                                <h4 class="text-lg font-bold text-clsu-green mb-2">{{ $menu->name }} ({{ ucfirst($meal_time) }} - Standard)</h4>
                                                <ul class="text-sm space-y-0.5 list-disc list-inside">
                                                    @if($menu->items)
                                                        @foreach($menu->items as $item)
                                                            <li>{{ $item->name }}</li>
                                                        @endforeach
                                                    @else
                                                        <li>No items available</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <div class="p-4 border border-gray-200 bg-gray-50 rounded-lg shadow-inner">
                                    <p class="text-sm text-gray-600">No standard menus available.</p>
                                </div>
                            @endif
                        </div>

                        <div id="special-menu-details" class="space-y-4 pt-2 hidden">
                            @if(isset($menus))
                                @foreach($menus as $meal_time => $types)
                                    @if(isset($types['special']))
                                        @foreach($types['special'] as $menu)
                                            <div class="p-4 border border-red-300 bg-red-50 rounded-lg shadow-inner">
                                                <h4 class="text-lg font-bold text-red-700 mb-2">{{ $menu->name }} ({{ ucfirst($meal_time) }} - Special) - Higher Price applies</h4>
                                                <ul class="text-sm space-y-0.5 list-disc list-inside">
                                                    @if($menu->items)
                                                        @foreach($menu->items as $item)
                                                            <li>{{ $item->name }}</li>
                                                        @endforeach
                                                    @else
                                                        <li>No items available</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            @else
                                <div class="p-4 border border-gray-200 bg-gray-50 rounded-lg shadow-inner">
                                    <p class="text-sm text-gray-600">No special menus available.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Payment Procedure Card --}}
                <div class="card p-6 border-l-4 border-clsu-green">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">Payment Procedure</h2>
                    <p class="mb-4 text-sm text-gray-700">Fees can be settled using the following payment options:</p>
                    <ol class="list-disc list-inside text-sm space-y-2 ml-4 mb-6 text-gray-700">
                        <li class="font-medium">**CLSU Cashier** (On-site cash deposit)</li>
                        <li class="font-medium">**Landbank Online Fund Transfer**</li>
                        <li class="font-medium">**GCash** (Transfer/deposit only)</li>
                    </ol>
                    <div class="text-xs font-semibold text-red-600 p-3 bg-red-100 rounded-lg border border-red-300">
                        IMPORTANT NOTE: Strict payment guidelines apply. Ensure compliance with the chosen method to confirm reservation.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. Quantity Buttons Logic ---
        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.currentTarget.dataset.action;
                const targetId = e.currentTarget.dataset.target;
                const input = document.querySelector(targetId);
                let value = parseInt(input.value);

                if (action === 'increment') {
                    value = value < 100 ? value + 1 : 100; // Cap at 100
                } else if (action === 'decrement') {
                    value = value > 0 ? value - 1 : 0; // Don't go below 0
                }
                input.value = value;
            });
        });

        // --- 2. Menu Tabs Logic (Show/Hide Details) ---
        const standardDetails = document.getElementById('standard-menu-details');
        const specialDetails = document.getElementById('special-menu-details');
        const menuTabs = document.querySelectorAll('.menu-tab');

        menuTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                menuTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                if (tab.dataset.menuCategory === 'standard') {
                    standardDetails.classList.remove('hidden');
                    specialDetails.classList.add('hidden');
                } else {
                    standardDetails.classList.add('hidden');
                    specialDetails.classList.remove('hidden');
                }
            });
        });

        // --- 3. Initial Date/Time Population (Simulate carrying data from previous form) ---
        // In a real application, the previous form would POST data to this route,
        // and Laravel would populate old() session data.
        // We'll simulate fetching from localStorage for persistence if the user presses 'Back'
        // on this page and returns later, but the primary 'Back' uses history.back() for this form state.

        // Get the current date and time for default values if not already set (This is for initial loading only)
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months start at 0!
        const dd = String(today.getDate()).padStart(2, '0');

        const defaultDate = `${yyyy}-${mm}-${dd}`;
        const defaultTime = "12:00"; // Default to noon

        const dateInput = document.getElementById('reservation_date');
        const timeInput = document.getElementById('reservation_time');

        // Set default values if empty
        if (!dateInput.value) {
            dateInput.value = defaultDate;
        }
        if (!timeInput.value) {
            timeInput.value = defaultTime;
        }

    });
</script>

@endsection
