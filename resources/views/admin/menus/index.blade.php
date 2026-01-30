@extends('layouts.sidebar')
@section('page-title','Menu Bundles')

@section('content')
@php
    // Price map (from controller or local fallback)
    $menuPrices = $priceMap ?? $prices ?? [
        'standard' => ['breakfast' => 150, 'am_snacks' => 150, 'lunch' => 300, 'pm_snacks' => 100, 'dinner' => 300],
        'special'  => ['breakfast' => 170, 'am_snacks' => 100, 'lunch' => 350, 'pm_snacks' => 150, 'dinner' => 350],
    ];
    $type = $type ?? request('type', 'standard');
    $meal = $meal ?? request('meal', 'breakfast');
@endphp

<style>
    [x-cloak]{ display:none !important; }
    
    /* Custom styles for menu bundles */
    .menu-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
    }
    
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e0;
    }
    
    .type-tab {
        transition: all 0.3s ease;
        font-weight: 600;
        border: 2px solid transparent;
        font-size: 0.875rem;
    }
    
    .type-tab.active {
        background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
        color: white;
        border-color: #00462E;
    }
    
    .type-tab:not(.active):hover {
        background: #e2e8f0;
        border-color: #cbd5e0;
    }
    
    .meal-badge {
        background: linear-gradient(135deg, #057C3C 0%, #059669 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
    }
    
    .price-pill {
        background: linear-gradient(135deg, #f0fff4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        color: #065f46;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .food-item {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    
    .food-item:hover {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-color: #bbf7d0;
    }
    
    .recipe-form {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<div x-data='menuCreateModal({
        defaultType: @json($type),
        defaultMeal: @json($meal === "all" ? "breakfast" : $meal),
        prices: @json($menuPrices)
     })'
     class="space-y-6">

{{-- Header --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 menu-card">
  <div class="flex items-center justify-between gap-4 flex-wrap w-full">
    <div class="flex items-center">
      <div class="w-12 h-12 bg-gradient-to-r from-[#00462E] to-[#057C3C] rounded-lg flex items-center justify-center mr-3 shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-size: 1.75rem;">Menu Bundles</h1>
        <p class="text-gray-600 mt-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Manage your cafeteria menu offerings</p>
      </div>
    </div>

    <button type="button"
            @click="openCreate()"
            class="bg-gradient-to-r from-[#00462E] to-[#057C3C] hover:from-[#057C3C] hover:to-[#00462E] text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl flex items-center transform hover:scale-105"
            style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Add Menu
    </button>
  </div>

  {{-- Type Tabs --}}
  <div class="flex gap-2 flex-wrap mt-6">
    @foreach($types as $key => $label)
      <a href="{{ route('admin.menus.index', ['type'=>$key,'meal'=>$meal]) }}"
         class="px-4 py-2 rounded-lg border-2 transition-all duration-300 type-tab {{ $type === $key ? 'active' : '' }}"
         style="font-family: 'Poppins', sans-serif;">
        {{ $label }}
      </a>
    @endforeach
  </div>

  {{-- Meal Filter and Fixed Price Row --}}
  <div class="mt-4 flex items-center justify-between gap-4 flex-wrap">
    {{-- Meal Filter --}}
    <form method="GET" action="{{ route('admin.menus.index') }}" class="flex items-center gap-3 flex-wrap">
      <input type="hidden" name="type" value="{{ $type }}">
      <label class="font-medium text-gray-700" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Filter by Meal:</label>
      <select name="meal"
              class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-300 bg-white shadow-sm"
              style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;"
              onchange="this.form.submit()">
        <option value="all" {{ $meal === 'all' ? 'selected' : '' }}>
          All Menus {{ !empty($totalCount) ? "($totalCount)" : '' }}
        </option>
        @foreach($meals as $key => $label)
          @php $count = data_get($counts ?? [], $key, 0); @endphp
          <option value="{{ $key }}" {{ $meal === $key ? 'selected' : '' }}>
            {{ $label }} {{ $count ? "($count)" : '' }}
          </option>
        @endforeach
      </select>
    </form>

    {{-- Fixed price pill (hide on "All") --}}
    @if($meal !== 'all' && !is_null($activePrice))
      <div class="flex items-center">
        <div class="inline-flex items-center px-4 py-3 rounded-lg price-pill shadow-sm border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50">
          <div class="text-center">
            <div class="flex items-center gap-2">
              <strong style="font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #065f46;">{{ ucfirst($type) }}</strong>
              <span class="text-green-600">•</span>
              <strong style="font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: #065f46;">{{ data_get($meals, $meal, 'Meal') }}</strong>
            </div>
            <div class="flex items-center gap-1 mt-1">
              <strong style="font-family: 'Poppins', sans-serif; font-size: 1.125rem; color: #059669;">₱{{ number_format($activePrice,2) }}</strong>
              <span style="font-family: 'Poppins', sans-serif; font-size: 0.75rem; color: #047857;">/ head</span>
            </div>
          </div>
          <a href="{{ route('admin.menus.prices', ['type' => $type, 'meal' => $meal]) }}" 
             class="ml-3 p-2 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition-colors duration-200 shadow-sm">
            <svg class="w-4 h-4 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
          </a>
        </div>
      </div>
    @endif
  </div>


  {{-- Menus grid - 3 columns --}}
  @php
    // Prefer controller-provided $currentMenus; fallback to $menusByDay
    $list = isset($currentMenus)
              ? $currentMenus
              : ($meal === 'all'
                  ? data_get($menusByDay ?? [], 'all', collect())
                  : data_get($menusByDay ?? [], $meal, collect()));
  @endphp

  <div class="grid gap-4 grid-cols-1 lg:grid-cols-3">
    @forelse($list as $menu)
      <div id="menu-card-{{ $menu->id }}"
           class="menu-card rounded-xl p-4 h-full flex flex-col">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <div class="px-2 py-1 rounded-full meal-badge inline-block mb-2">
              {{ strtoupper(str_replace('_',' ', $menu->meal_time)) }}
            </div>
            <h2 class="font-semibold text-gray-900 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.125rem;">Menu {{ $menu->id }}</h2>
            {{-- <div class="text-[#057C3C] font-medium" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              ₱{{ number_format($menu->price ?? ($menuPrices[$menu->type][$menu->meal_time] ?? 0), 2) }} / head
            </div> --}}
            @if(!empty($menu->description))
              <p class="text-gray-600 mt-2 leading-relaxed" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $menu->description }}</p>
            @endif
          </div>
          <div class="flex gap-1 ml-2">
            @php
              $editItems = $menu->items->map(fn($i) => [
                'name' => $i->name,
                'type' => $i->type,
                'recipes' => $i->recipes->map(fn($r) => [
                  'inventory_item_id' => $r->inventory_item_id,
                  'quantity_needed' => $r->quantity_needed,
                  'unit' => $r->unit
                ])->toArray()
              ])->toArray();
            @endphp
            <button type="button"
                    @click='openEdit({{ $menu->id }}, @json($menu->name), @json($menu->description), @json($menu->type), @json($menu->meal_time), @json($editItems))'
                    class="p-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button type="button"
                    @click='openDelete({{ $menu->id }}, @json("Menu ".$menu->id))'
                    class="p-1 bg-red-50 text-red-600 rounded hover:bg-red-100 transition-colors duration-200">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-3 mt-3 flex-1">
          @if($menu->items->count())
            <h4 class="font-semibold text-gray-700 mb-2 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              <svg class="w-3 h-3 mr-1 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
              Menu Items
            </h4>
            <ul class="space-y-2">
              @foreach($menu->items as $food)
                <li class="food-item p-2 rounded-md">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <span class="font-medium text-gray-900 block" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">{{ $food->name }}</span>
                      <span class="text-gray-500 mt-0.5" style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">{{ ucfirst($food->type) }}</span>
                    </div>
                    <a href="{{ route('admin.recipes.index', $food) }}" 
                       class="text-[#057C3C] hover:text-[#00462E] font-medium flex items-center transition-colors duration-200 ml-2"
                       style="font-size: 0.75rem;">
                      Recipe
                      <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                      </svg>
                    </a>
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center text-gray-500 py-4 flex-1 flex items-center justify-center" style="font-family: 'Poppins', sans-serif;">
              <div>
                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p style="font-size: 0.875rem;">No items added yet</p>
              </div>
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="col-span-full text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
        </div>
        <h3 class="font-semibold text-gray-600 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 1.125rem;">No menus found</h3>
        <p class="text-gray-500 mb-4" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Get started by creating your first menu bundle</p>
        <button type="button"
                @click="openCreate()"
                class="bg-gradient-to-r from-[#00462E] to-[#057C3C] hover:from-[#057C3C] hover:to-[#00462E] text-white px-6 py-2 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center"
                style="font-size: 0.875rem;">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Create Menu
        </button>
      </div>
    @endforelse
  </div>

  {{-- CREATE MENU MODAL --}}
  <div x-cloak x-show="isCreateOpen" x-transition
       class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
    <div @click="close()" class="absolute inset-0"></div>

    <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl p-0 transform transition-all duration-300 scale-95 max-h-[90vh] overflow-hidden"
         x-transition:enter="scale-100"
         x-transition:enter-start="scale-95">
         
      <div class="modal-header p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-[#00462E] to-[#057C3C] rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-size: 1.75rem;">Create New Menu</h2>
              <p class="text-gray-600 mt-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Add a new menu bundle to your cafeteria</p>
            </div>
          </div>
          <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded hover:bg-gray-100" @click="close()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <form x-ref="createForm" method="POST" action="{{ route('admin.menus.store') }}" class="space-y-4">
          @csrf

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Menu Type</label>
              <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" x-model="form.type" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                <option value="standard">Standard Menu</option>
                <option value="special">Special Menu</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Meal Time</label>
              <select name="meal_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" x-model="form.meal" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                <option value="breakfast">Breakfast</option>
                <option value="am_snacks">AM Snacks</option>
                <option value="lunch">Lunch</option>
                <option value="pm_snacks">PM Snacks</option>
                <option value="dinner">Dinner</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Display Name (Optional)</label>
            <input name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" placeholder="e.g., Breakfast Menu" x-model="form.name" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
          </div>

          <div>
            <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Description (Optional)</label>
            <textarea name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" rows="2"
                      placeholder="Short description of the menu..." style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;"></textarea>
          </div>

          {{-- Menu Items --}}
          <div class="border border-gray-200 rounded-lg p-4 recipe-form">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 1rem;">
              <svg class="w-4 h-4 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Menu Items
            </h3>
            <div class="space-y-3">
              <template x-for="(item, index) in form.items" :key="index">
                <div class="bg-gray-50 p-3 rounded-lg space-y-3 border border-gray-200">
                  <div class="flex gap-2 items-end">
                    <input type="text" :name="'items[' + index + '][name]'" x-model="item.name" placeholder="Food name" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                    <select :name="'items[' + index + '][type]'" x-model="item.type" class="border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                      <option value="food">Food/Main Dish</option>
                      <option value="drink">Drink</option>
                      <option value="dessert">Dessert</option>
                    </select>
                    <button type="button" @click="form.items.splice(index, 1)" class="p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                  {{-- Recipes for this item --}}
                  <div class="border-t border-gray-300 pt-3">
                    <h4 class="font-medium text-gray-700 mb-2 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                      <svg class="w-3 h-3 mr-1 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                      Recipes
                    </h4>
                    <div class="space-y-2">
                      <template x-for="(recipe, rIndex) in item.recipes" :key="rIndex">
                        <div class="flex gap-2 items-end">
                          <select :name="'items[' + index + '][recipes][' + rIndex + '][inventory_item_id]'" x-model="recipe.inventory_item_id" class="flex-1 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                            <option value="">Select Ingredient</option>
                            @foreach($inventoryItems as $inv)
                              <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                            @endforeach
                          </select>
                          <input type="number" :name="'items[' + index + '][recipes][' + rIndex + '][quantity_needed]'" x-model="recipe.quantity_needed" placeholder="Qty" step="0.01" min="0.01" class="w-20 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                          <input type="text" :name="'items[' + index + '][recipes][' + rIndex + '][unit]'" x-model="recipe.unit" placeholder="Unit" class="w-16 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                          <button type="button" @click="item.recipes.splice(rIndex, 1)" class="p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </button>
                        </div>
                      </template>
                      <button type="button" @click="item.recipes.push({inventory_item_id: '', quantity_needed: '', unit: ''})" class="text-[#057C3C] hover:text-[#00462E] font-medium flex items-center transition-colors duration-200"
                              style="font-size: 0.75rem;">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Recipe
                      </button>
                    </div>
                  </div>
                </div>
              </template>
              <button type="button" @click="form.items.push({name: '', type: 'food', recipes: []})" class="text-[#057C3C] hover:text-[#00462E] font-medium transition-colors duration-200 flex items-center"
                      style="font-size: 0.875rem;">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
              </button>
            </div>
          </div>

          <div class="bg-green-50 border border-green-200 rounded-lg p-3">
            <div class="text-green-800 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
              <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              Fixed price per head:
              <span class="font-semibold ml-1" x-text="priceText"></span>
              <span class="text-green-600 ml-1">(auto-applied on save)</span>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="close()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm"
                    style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-[#00462E] to-[#057C3C] hover:from-[#057C3C] hover:to-[#00462E] text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center transform hover:scale-105"
                    style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              Create Menu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- EDIT MENU MODAL --}}
  <div x-cloak x-show="isEditOpen" x-transition
       class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
    <div @click="closeEdit()" class="absolute inset-0"></div>

    <div class="relative bg-white w-full max-w-4xl rounded-2xl shadow-2xl p-0 transform transition-all duration-300 scale-95 max-h-[90vh] overflow-hidden"
         x-transition:enter="scale-100"
         x-transition:enter-start="scale-95">
         
      <div class="modal-header p-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <div>
              <h2 class="font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-size: 1.75rem;">Edit Menu</h2>
              <p class="text-gray-600 mt-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Update menu bundle details</p>
            </div>
          </div>
          <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded hover:bg-gray-100" @click="closeEdit()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
        <form method="POST" action="" x-ref="editForm" class="space-y-4">
          @csrf @method('PATCH')

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Menu Type</label>
              <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" x-model="editForm.type" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                <option value="standard">Standard Menu</option>
                <option value="special">Special Menu</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Meal Time</label>
              <select name="meal_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" x-model="editForm.meal" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                <option value="breakfast">Breakfast</option>
                <option value="am_snacks">AM Snacks</option>
                <option value="lunch">Lunch</option>
                <option value="pm_snacks">PM Snacks</option>
                <option value="dinner">Dinner</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Display Name (Optional)</label>
            <input name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" placeholder="e.g., Breakfast Menu" x-model="editForm.name" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
          </div>

          <div>
            <label class="block font-medium text-gray-700 mb-1" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">Description (Optional)</label>
            <textarea name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" rows="2"
                      placeholder="Short description..." style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;"></textarea>
          </div>

          {{-- Menu Items --}}
          <div class="border border-gray-200 rounded-lg p-4 recipe-form">
            <h3 class="font-semibold text-gray-900 mb-3 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 1rem;">
              <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Menu Items
            </h3>
            <div class="space-y-3">
              <template x-for="(item, index) in editForm.items" :key="index">
                <div class="bg-gray-50 p-3 rounded-lg space-y-3 border border-gray-200">
                  <div class="flex gap-2 items-end">
                    <input type="text" :name="'items[' + index + '][name]'" x-model="item.name" placeholder="Food name" class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                    <select :name="'items[' + index + '][type]'" x-model="item.type" class="border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
                      <option value="food">Food/Main Dish</option>
                      <option value="drink">Drink</option>
                      <option value="dessert">Dessert</option>
                    </select>
                    <button type="button" @click="editForm.items.splice(index, 1)" class="p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                  {{-- Recipes for this item --}}
                  <div class="border-t border-gray-300 pt-3">
                    <h4 class="font-medium text-gray-700 mb-2 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                      <svg class="w-3 h-3 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                      </svg>
                      Recipes
                    </h4>
                    <div class="space-y-2">
                      <template x-for="(recipe, rIndex) in item.recipes" :key="rIndex">
                        <div class="flex gap-2 items-end">
                          <select :name="'items[' + index + '][recipes][' + rIndex + '][inventory_item_id]'" x-model="recipe.inventory_item_id" class="flex-1 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                            <option value="">Select Ingredient</option>
                            @foreach($inventoryItems as $inv)
                              <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                            @endforeach
                          </select>
                          <input type="number" :name="'items[' + index + '][recipes][' + rIndex + '][quantity_needed]'" x-model="recipe.quantity_needed" placeholder="Qty" step="0.01" min="0.01" class="w-20 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                          <input type="text" :name="'items[' + index + '][recipes][' + rIndex + '][unit]'" x-model="recipe.unit" placeholder="Unit" class="w-16 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white" required style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
                          <button type="button" @click="item.recipes.splice(rIndex, 1)" class="p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </button>
                        </div>
                      </template>
                      <button type="button" @click="item.recipes.push({inventory_item_id: '', quantity_needed: '', unit: ''})" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200"
                              style="font-size: 0.75rem;">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Recipe
                      </button>
                    </div>
                  </div>
                </div>
              </template>
              <button type="button" @click="editForm.items.push({name: '', type: 'food', recipes: []})" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 flex items-center"
                      style="font-size: 0.875rem;">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
              </button>
            </div>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <div class="text-blue-800 flex items-center" style="font-family: 'Poppins', sans-serif; font-size: 0.75rem;">
              <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
              Fixed price per head:
              <span class="font-semibold ml-1" x-text="editPriceText"></span>
              <span class="text-blue-600 ml-1">(auto-applied on save)</span>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="closeEdit()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm"
                    style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center transform hover:scale-105"
                    style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              Update Menu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- DELETE MENU MODAL (teleported) --}}
  <template x-teleport="body">
    <div x-cloak x-show="isDeleteOpen"
         @keydown.escape.window="closeDelete()"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
      <div class="absolute inset-0" @click="closeDelete()"></div>

      <div x-transition
           class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 transform transition-all duration-300 scale-95"
           role="dialog" aria-modal="true"
           aria-labelledby="delete-title" aria-describedby="delete-desc"
           x-transition:enter="scale-100"
           x-transition:enter-start="scale-95">

        <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded hover:bg-gray-100"
                @click="closeDelete()" aria-label="Close">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <div class="flex items-center mb-4">
          <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          <h2 id="delete-title" class="font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-size: 1.125rem;">Delete Menu</h2>
        </div>

        <p id="delete-desc" class="text-gray-600 mb-4 leading-relaxed" style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
          Are you sure you want to delete
          <span class="font-semibold text-gray-900" x-text="deleteName || 'this menu'"></span>?
          This action cannot be undone and will permanently remove the menu from the system.
        </p>

        <form @submit.prevent="confirmDelete" class="flex justify-end gap-3">
          @csrf
          @method('DELETE')

          <button type="button" @click="closeDelete()"
                  class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm"
                  style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
            Cancel
          </button>

          <button type="submit"
                  class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center transform hover:scale-105"
                  style="font-family: 'Poppins', sans-serif; font-size: 0.875rem;">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Delete Menu
          </button>
        </form>
      </div>
    </div>
  </template>

</div>
</div>
{{-- Alpine component --}}
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('menuCreateModal', (opts = {}) => ({
      // State
      isCreateOpen: false,
      isEditOpen: false,
      isDeleteOpen: false,

      deleteId: null,
      deleteName: '',

      prices: opts.prices || {
        standard: { breakfast:150, am_snacks:150, lunch:300, pm_snacks:100, dinner:300 },
        special:  { breakfast:170, am_snacks:100, lunch:350, pm_snacks:150, dinner:350 },
      },

      form: {
        type:  opts.defaultType || 'standard',
        meal:  opts.defaultMeal || 'breakfast',
        name:  '',
        description: '',
        items: []
      },

      editForm: {
        id: null,
        type: 'standard',
        meal: 'breakfast',
        name: '',
        description: '',
        items: []
      },

      // Methods
      openCreate(type = null, meal = null) {
        if (type) this.form.type = type;
        if (meal) this.form.meal = meal;
        this.form.items = [];
        this.isCreateOpen = true;
      },
      close(){ this.isCreateOpen = false; },

      openEdit(id, name, description, type, meal, items = []) {
        this.editForm.id = id;
        this.editForm.name = name || '';
        this.editForm.description = description || '';
        this.editForm.type = type || 'standard';
        this.editForm.meal = meal || 'breakfast';
        this.editForm.items = (items || []).map(i => ({ name: i.name, type: i.type, recipes: i.recipes || [] }));

        // set form action safely
        this.$refs.editForm.action = `{{ url('/admin/menus') }}/${id}`;

        this.isEditOpen = true;
      },
      closeEdit(){ this.isEditOpen = false; },

      openDelete(id, name = 'this menu') {
        this.deleteId = id;
        this.deleteName = name || 'this menu';
        this.isDeleteOpen = true;
        document.body.style.overflow = 'hidden';
      },
      closeDelete() {
        this.isDeleteOpen = false;
        this.deleteId = null;
        this.deleteName = '';
        document.body.style.overflow = '';
      },

      // AJAX delete, stay on current page and remove the card
      async confirmDelete() {
        if (!this.deleteId) return;

        const url = `{{ url('/admin/menus') }}/${this.deleteId}`;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
          const res = await fetch(url, {
            method: 'POST', // Laravel DELETE via method spoofing
            headers: {
              'X-CSRF-TOKEN': token,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json, text/plain, */*',
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
          });

          if (!res.ok && res.status !== 204) {
            console.warn('Delete failed', await res.text());
          }

          const card = document.getElementById('menu-card-' + this.deleteId);
          if (card) card.remove();

          this.closeDelete(); // close modal, stay on page
        } catch (e) {
          console.error('Delete error', e);
          this.closeDelete();
        }
      },

      // Price helpers
      get priceText() {
        const t = this.form.type, m = this.form.meal;
        if (m === 'all') return 'varies by meal';
        const v = (this.prices[t] && this.prices[t][m]) ? this.prices[t][m] : 0;
        return '₱' + Number(v).toFixed(2) + ' / head';
      },
      get editPriceText() {
        const t = this.editForm.type, m = this.editForm.meal;
        if (m === 'all') return 'varies by meal';
        const v = (this.prices[t] && this.prices[t][m]) ? this.prices[t][m] : 0;
        return '₱' + Number(v).toFixed(2) + ' / head';
      },
    }));
  });
</script>
@endsection