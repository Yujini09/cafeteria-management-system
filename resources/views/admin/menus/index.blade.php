@extends('layouts.sidebar')
@section('page-title','Menu Bundles')

@section('content')
@php
    $menuPrices = $priceMap ?? $prices ?? [
        'standard' => ['breakfast' => 150, 'am_snacks' => 150, 'lunch' => 300, 'pm_snacks' => 100, 'dinner' => 300],
        'special'  => ['breakfast' => 170, 'am_snacks' => 100, 'lunch' => 350, 'pm_snacks' => 150, 'dinner' => 350],
    ];
    $type = $type ?? request('type', 'standard');
    $meal = $meal ?? request('meal', 'breakfast');
@endphp

<style>
    [x-cloak] { display: none !important; }
    
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
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        display: inline-block;
        margin-bottom: 0.5rem;
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
    
    .form-label {
        font-family: 'Poppins', sans-serif;
        font-size: 0.875rem;
        display: block;
        font-weight: 500;
        margin-bottom: 0.25rem;
        color: #374151;
    }
    
    .form-input, .form-select, .form-textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.875rem;
        transition: all 0.2s;
        background-color: white;
    }
    
    .form-select {
        appearance: none;
    }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        ring: 2px;
        --tw-ring-color: #057C3C;
        border-color: transparent;
        box-shadow: 0 0 0 2px #057C3C;
    }
    
    .form-textarea {
        resize: vertical;
    }

    .primary-gradient {
        background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
    }
    
    .primary-color {
        color: #057C3C;
    }
    
    .icon-sm { width: 16px; height: 16px; }
    .icon-md { width: 20px; height: 20px; }
    .icon-lg { width: 24px; height: 24px; }
</style>

<div x-data='menuCreateModal({
        defaultType: @json($type),
        defaultMeal: @json($meal === "all" ? "breakfast" : $meal),
        prices: @json($menuPrices),
        inventoryItems: @json($inventoryItems ?? [])
     })'
     x-effect="document.body.classList.toggle('overflow-hidden', isCreateOpen || isEditOpen || isDeleteOpen)"
     @keydown.escape.window="isCreateOpen = false; isEditOpen = false; closeDelete()"
     class="space-y-6">

{{-- Header --}}
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 menu-card">
  <div class="flex items-start justify-between gap-4 flex-wrap w-full">
    <div class="flex items-center">
      <div class="w-12 h-12 primary-gradient rounded-lg flex items-center justify-center mr-3 shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Menu Bundles</h1>
        <p class="text-gray-600 mt-1 text-sm">Manage your cafeteria menu offerings</p>
      </div>
    </div>

    <div class="flex flex-col gap-3 ml-auto w-full sm:w-auto sm:items-end">
      <div class="relative w-full sm:w-64 md:w-72">
        <input type="search" id="searchInput" placeholder="Search menus..."
               class="admin-search-input w-full rounded-lg border border-gray-300 bg-white py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent"
               oninput="filterTable(this.value)" aria-label="Search menus">
        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: none;">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="flex flex-wrap gap-3 w-full sm:w-auto sm:justify-end">
        <button type="button" @click="openCreate()"
                class="primary-gradient hover:shadow-xl text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center transform hover:scale-105">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add Menu
        </button>
      </div>
    </div>
  </div>

  {{-- Type Tabs --}}
  <div class="flex gap-2 flex-wrap mt-6">
    @foreach($types as $key => $label)
      <a href="{{ route('admin.menus.index', ['type'=>$key,'meal'=>$meal]) }}"
         class="px-4 py-2 rounded-lg border-2 transition-all duration-300 type-tab {{ $type === $key ? 'active' : '' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  {{-- Meal Filter and Fixed Price Row --}}
  <div class="mt-4 flex items-center justify-between gap-4 flex-wrap">
    {{-- Meal Filter --}}
    <form method="GET" action="{{ route('admin.menus.index') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
      <input type="hidden" name="type" value="{{ $type }}">
      <label class="form-label whitespace-nowrap">Filter by Meal:</label>
      <div class="w-full sm:w-64">
        <select name="meal" data-admin-select="true" class="form-select" onchange="this.form.submit()">
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
      </div>
    </form>

    {{-- Fixed price pill (hide on "All") --}}
    @if($meal !== 'all' && !is_null($activePrice))
      <div class="flex items-center">
        <div class="inline-flex items-center px-4 py-3 rounded-lg price-pill shadow-sm border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50">
          <div class="text-center">
            <div class="flex items-center gap-2 text-sm font-bold text-green-900">
              <span>{{ ucfirst($type) }}</span>
              <span class="text-green-600">•</span>
              <span>{{ data_get($meals, $meal, 'Meal') }}</span>
            </div>
            <div class="flex items-center gap-1 mt-1">
              <strong class="text-lg text-green-600">₱{{ number_format($activePrice,2) }}</strong>
              <span class="text-xs text-green-700">/ head</span>
            </div>
          </div>
          <a href="{{ route('admin.menus.prices', ['type' => $type, 'meal' => $meal]) }}" 
             class="ml-3 p-2 bg-white border border-green-300 rounded-lg hover:bg-green-50 transition-colors duration-200 shadow-sm">
            <svg class="icon-md primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
          </a>
        </div>
      </div>
    @endif
  </div>

  {{-- Menus grid - 3 columns --}}
  @php
    $list = isset($currentMenus)
              ? $currentMenus
              : ($meal === 'all'
                  ? data_get($menusByDay ?? [], 'all', collect())
                  : data_get($menusByDay ?? [], $meal, collect()));
  @endphp

  <div class="grid gap-4 grid-cols-1 lg:grid-cols-3 mt-4">
    @forelse($list as $menu)
      <div id="menu-card-{{ $menu->id }}" data-search-card="true" class="menu-card rounded-xl p-4 h-full flex flex-col">
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <div class="meal-badge">{{ strtoupper(str_replace('_',' ', $menu->meal_time)) }}</div>
            <h2 class="font-semibold text-gray-900 mb-1 text-lg">Menu {{ $menu->id }}</h2>
            @if(!empty($menu->description))
              <p class="text-gray-600 mt-2 leading-relaxed text-sm">{{ $menu->description }}</p>
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
            <button type="button" @click='openEdit({{ $menu->id }}, @json($menu->name), @json($menu->description), @json($menu->type), @json($menu->meal_time), @json($editItems))'
                    class="p-1 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors duration-200">
              <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button type="button" @click='openDelete({{ $menu->id }}, @json("Menu ".$menu->id))'
                    class="p-1 bg-red-50 text-red-600 rounded hover:bg-red-100 transition-colors duration-200">
              <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="border-t border-gray-200 pt-3 mt-3 flex-1">
          @if($menu->items->count())
            <h4 class="font-semibold text-gray-700 mb-2 flex items-center text-sm">
              <svg class="w-3 h-3 mr-1 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
              </svg>
              Menu Items
            </h4>
            <ul class="space-y-2">
              @foreach($menu->items as $food)
                <li class="food-item p-2 rounded-md">
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <span class="font-medium text-gray-900 block text-sm">{{ $food->name }}</span>
                      <span class="text-gray-500 mt-0.5 text-xs">{{ ucfirst($food->type) }}</span>
                    </div>
                    <a href="{{ route('admin.recipes.index', $food) }}" 
                       class="primary-color hover:text-[#00462E] font-medium flex items-center transition-colors duration-200 ml-2 text-xs">
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
            <div class="text-center text-gray-500 py-4 flex-1 flex items-center justify-center">
              <div>
                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-sm">No items added yet</p>
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
        <h3 class="font-semibold text-gray-600 mb-1 text-lg">No menus found</h3>
        <p class="text-gray-500 mb-4 text-sm">Get started by creating your first menu bundle</p>
        <button type="button" @click="openCreate()"
                class="primary-gradient text-white px-6 py-2 rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Create Menu
        </button>
      </div>
    @endforelse
  </div>

  @if($currentMenus->hasPages())
    <div class="mt-6">
      {{ $currentMenus->links('components.pagination') }}
    </div>
  @endif

  {{-- Success Modal --}}
  <x-success-modal name="menu-create-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
    <p class="text-sm text-admin-neutral-600">Menu created successfully!</p>
  </x-success-modal>
  <x-success-modal name="menu-update-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
    <p class="text-sm text-admin-neutral-600">Menu updated successfully!</p>
  </x-success-modal>
  <x-success-modal name="menu-delete-success" title="Deleted" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
    <p class="text-sm text-admin-neutral-600">Menu deleted successfully.</p>
  </x-success-modal>

  {{-- CREATE MENU MODAL - 3 STEPS --}}
  <template x-teleport="body">
    <div x-cloak x-show="isCreateOpen" x-transition x-transition.opacity
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div @click="close()" class="absolute inset-0"></div>

      <div @click.stop class="relative bg-white w-full max-w-4xl rounded-2xl shadow-2xl p-0 transform transition-all duration-300 scale-95 max-h-[90vh] overflow-hidden flex flex-col"
           x-transition:enter="scale-100" x-transition:enter-start="scale-95">
         
        <div class="modal-header p-6 flex-shrink-0">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-10 h-10 primary-gradient rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <div>
                <h2 class="font-bold text-gray-900 text-2xl">Create New Menu</h2>
                <p class="text-gray-600 mt-1 text-sm">Add a new menu bundle to your cafeteria</p>
              </div>
            </div>
            <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-1 rounded hover:bg-gray-100" @click="close()">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          {{-- Step Indicator --}}
          <div class="flex items-center justify-between mt-6 px-2">
            <template x-for="(step, index) in [
              { num: 1, label: 'Menu Info' },
              { num: 2, label: 'Menu Items' },
              { num: 3, label: 'Recipes' }
            ]" :key="index">
              <div class="flex flex-col items-center flex-1">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-all duration-300',
                  currentStep >= step.num 
                    ? 'primary-gradient text-white' 
                    : 'bg-gray-200 text-gray-600'
                ]" x-text="step.num"></div>
                <span class="text-xs mt-2 font-medium" :class="currentStep >= step.num ? 'primary-color' : 'text-gray-500'" x-text="step.label"></span>
              </div>
              <template x-if="index < 2">
                <div :class="[
                  'h-1 flex-1 mx-2 rounded transition-all duration-300',
                  currentStep > step.num ? 'bg-[#057C3C]' : 'bg-gray-200'
                ]"></div>
              </template>
            </template>
          </div>
        </div>

        <div class="p-6 overflow-y-auto flex-1 min-h-0">
          <form x-ref="createForm" method="POST" action="{{ route('admin.menus.store') }}" class="space-y-6">
            @csrf

            {{-- STEP 1: Menu Info --}}
            <div x-show="currentStep === 1" x-transition class="space-y-4">
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Menu Information</h3>
                <p class="text-gray-600 text-sm mb-4">Enter the basic details for your menu</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="form-label">Menu Type <span class="text-red-500">*</span></label>
                  <select name="type" class="form-select" x-model="form.type" required data-admin-select="true">
                    <option value="">Select menu type</option>
                    <option value="standard">Standard Menu</option>
                    <option value="special">Special Menu</option>
                  </select>
                </div>

                <div>
                  <label class="form-label">Meal Time <span class="text-red-500">*</span></label>
                  <select name="meal_time" class="form-select" x-model="form.meal" required data-admin-select="true">
                    <option value="">Select meal time</option>
                    <option value="breakfast">Breakfast</option>
                    <option value="am_snacks">AM Snacks</option>
                    <option value="lunch">Lunch</option>
                    <option value="pm_snacks">PM Snacks</option>
                    <option value="dinner">Dinner</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="form-label">Display Name (Optional)</label>
                <input name="name" class="form-input" placeholder="e.g., Breakfast Menu" x-model="form.name">
                <p class="text-xs text-gray-500 mt-1">If left empty, the menu will be named automatically as "Menu #X"</p>
              </div>

              <div>
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="Short description of the menu..."></textarea>
              </div>

              <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="text-green-800 flex items-center text-xs">
                  <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                  Fixed price per head: <span class="font-semibold ml-1" x-text="priceText"></span>
                  <span class="text-green-600 ml-1">(auto-applied on save)</span>
                </div>
              </div>
            </div>

            {{-- STEP 2: Menu Items --}}
            <div x-show="currentStep === 2" x-transition class="space-y-4">
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Step 2: Menu Items</h3>
                <p class="text-gray-600 text-sm">Add item names (recipes will be added in step 3)</p>
              </div>

              <div class="border border-gray-200 rounded-lg p-4 bg-white">
                <div class="space-y-3">
                  <template x-for="(item, index) in form.items" :key="index">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                      <div class="flex-1">
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Item Name</label>
                        <input type="text" :name="'items[' + index + '][name]'" x-model="item.name" 
                               placeholder="Enter food name" class="form-input" required>
                      </div>
                      <div class="w-full sm:w-32">
                        <label class="text-xs font-medium text-gray-700 mb-1 block">Type</label>
                        <select :name="'items[' + index + '][type]'" x-model="item.type" 
                                class="form-select" data-admin-select="true">
                          <option value="food">Food</option>
                          <option value="drink">Drink</option>
                          <option value="dessert">Dessert</option>
                        </select>
                      </div>
                      <button type="button" @click="form.items.splice(index, 1)" 
                              class="self-end sm:self-auto p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </template>

                  <template x-if="form.items.length === 0">
                    <div class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                      </svg>
                      <p class="text-sm">No items yet. Click the button below to add items.</p>
                    </div>
                  </template>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                  <button type="button" @click="form.items.push({name: '', type: 'food', recipes: [], showRecipes: false})" 
                          class="w-full primary-color hover:text-[#00462E] font-medium transition-colors duration-200 flex items-center justify-center py-2 border border-dashed border-gray-300 rounded-lg hover:border-[#057C3C] text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Item
                  </button>
                </div>
              </div>

              <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="text-blue-800 flex items-start text-xs">
                  <svg class="w-4 h-4 mr-2 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>You'll add recipes and ingredients for each item in the next step. Make sure you add at least one item to proceed.</span>
                </div>
              </div>
            </div>

            {{-- STEP 3: Recipes --}}
            <div x-show="currentStep === 3" x-transition class="space-y-4">
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Step 3: Add Recipes</h3>
                <p class="text-gray-600 text-sm">Add ingredients for each menu item (all items are required)</p>
              </div>

              <div class="space-y-4">
                <template x-for="(item, index) in form.items" :key="index">
                  <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center mb-4 pb-4 border-b border-gray-300">
                      <div class="w-8 h-8 primary-gradient rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                        <span x-text="index + 1"></span>
                      </div>
                      <div>
                        <h4 class="font-semibold text-gray-900" x-text="item.name || 'Unnamed Item'"></h4>
                        <p class="text-gray-600 text-xs">Type: <span x-text="item.type.charAt(0).toUpperCase() + item.type.slice(1)"></span></p>
                      </div>
                    </div>

                    <div class="space-y-3">
                      <h5 class="font-medium text-gray-700 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 primary-color" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Ingredients
                      </h5>

                      <template x-if="item.recipes.length === 0">
                        <div class="text-center py-4 bg-white rounded-lg border border-dashed border-gray-300">
                          <p class="text-gray-500 text-sm">No ingredients added yet</p>
                        </div>
                      </template>

                      <template x-for="(recipe, rIndex) in item.recipes" :key="rIndex">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end bg-white p-3 rounded-lg">
                          <div class="flex-1 relative">
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Ingredient <span class="text-red-500">*</span></label>
                            <div class="relative">
                              <button type="button" 
                                      @click="form.openDropdowns[index + '_' + rIndex] = !form.openDropdowns[index + '_' + rIndex]"
                                      class="form-input w-full flex items-center justify-between hover:border-[#057C3C] text-left bg-white">
                                <span class="truncate text-sm">
                                  <template x-if="recipe.inventory_item_id">
                                    <span x-text="getIngredientLabel(recipe.inventory_item_id)"></span>
                                  </template>
                                  <template x-if="!recipe.inventory_item_id">
                                    <span class="text-gray-400">Select ingredient...</span>
                                  </template>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                              </button>
                              
                              <div x-show="form.openDropdowns[index + '_' + rIndex]" 
                                   @click.outside="form.openDropdowns[index + '_' + rIndex] = false" 
                                   class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-2xl z-[100] w-full flex flex-col max-h-72">
                                <div class="flex-shrink-0 border-b border-gray-300 p-2">
                                  <input type="text" 
                                         x-model="form.searchTerms[index + '_' + rIndex]" 
                                         @keydown.escape="form.openDropdowns[index + '_' + rIndex] = false"
                                         placeholder="Search ingredients..." 
                                         class="form-input w-full" 
                                         style="font-size: 0.875rem;">
                                </div>
                                <div class="overflow-y-auto flex-1">
                                  <template x-for="inv in allInventoryItems.filter(i => {
                                    const search = form.searchTerms[index + '_' + rIndex] || '';
                                    return !search || i.name.toLowerCase().includes(search.toLowerCase());
                                  })" :key="inv.id">
                                    <button type="button" 
                                            @click="recipe.inventory_item_id = inv.id; recipe.unit = inv.unit || ''; form.openDropdowns[index + '_' + rIndex] = false; form.searchTerms[index + '_' + rIndex] = '';"
                                            class="w-full text-left px-3 py-2 hover:bg-green-50 border-b border-gray-100 last:border-0 transition-colors text-sm">
                                      <span x-text="inv.name"></span>
                                    </button>
                                  </template>
                                  <template x-if="allInventoryItems.filter(i => {
                                    const search = form.searchTerms[index + '_' + rIndex] || '';
                                    return !search || i.name.toLowerCase().includes(search.toLowerCase());
                                  }).length === 0">
                                    <div class="px-3 py-4 text-center text-gray-500 text-sm">
                                      <template x-if="allInventoryItems.length === 0">
                                        <span>No ingredients available in inventory</span>
                                      </template>
                                      <template x-if="allInventoryItems.length > 0">
                                        <span>No ingredients matching your search</span>
                                      </template>
                                    </div>
                                  </template>
                                </div>
                              </div>
                            </div>
                            <input type="hidden" :name="'items[' + index + '][recipes][' + rIndex + '][inventory_item_id]'" :value="recipe.inventory_item_id" required>
                          </div>
                          <div class="w-full sm:w-24">
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Quantity</label>
                            <input type="number" :name="'items[' + index + '][recipes][' + rIndex + '][quantity_needed]'" 
                                   x-model="recipe.quantity_needed" placeholder="Qty" step="0.01" min="0.01" class="form-input" required>
                          </div>
                          <div class="w-full sm:w-24">
                            <label class="text-xs font-medium text-gray-700 mb-1 block">Unit</label>
                            <select :name="'items[' + index + '][recipes][' + rIndex + '][unit]'" 
                                    x-model="recipe.unit" class="form-select" data-admin-select="true" required>
                              <option value="">Select unit</option>
                              <optgroup label="Count">
                                <option value="Pieces">Pieces</option>
                                <option value="Packs">Packs</option>
                              </optgroup>
                              <optgroup label="Weight">
                                <option value="Kgs">Kgs</option>
                              </optgroup>
                              <optgroup label="Volume">
                                <option value="Liters">Liters</option>
                              </optgroup>
                            </select>
                          </div>
                          <button type="button" @click="item.recipes.splice(rIndex, 1)" 
                                  class="self-end sm:self-auto p-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                          </button>
                        </div>
                      </template>

                      <button type="button" @click="item.recipes.push({inventory_item_id: '', quantity_needed: '', unit: ''})" 
                              class="primary-color hover:text-[#00462E] text-sm font-medium flex items-center transition-colors duration-200 w-full justify-center py-2 border border-dashed border-gray-300 rounded-lg hover:border-[#057C3C] mt-2">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Ingredient
                      </button>
                    </div>
                  </div>
                </template>
              </div>

              <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <div class="text-green-800 flex items-start text-xs">
                  <svg class="w-4 h-4 mr-2 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span>Make sure to add at least one ingredient for each menu item before submitting.</span>
                </div>
              </div>
            </div>
          </form>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center gap-3 flex-shrink-0">
          <button type="button" @click="previousStep()" x-show="currentStep > 1"
                  class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
          </button>

          <div class="ml-auto flex gap-3">
            <button type="button" @click="close()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm text-sm">
              Cancel
            </button>

            <button type="button" 
                    @click="currentStep === 3 ? submitForm() : nextStep()" 
                    :disabled="!canProceed()"
                    :class="[
                      'px-6 py-2 rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center transform hover:scale-105 text-sm',
                      canProceed()
                        ? 'primary-gradient text-white'
                        : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                    ]">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="currentStep === 3" d="M5 13l4 4L19 7"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="currentStep < 3" d="M9 5l7 7-7 7"></path>
              </svg>
              <span x-text="currentStep === 3 ? 'Create Menu' : 'Next'"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  {{-- EDIT MENU MODAL --}}
  <template x-teleport="body">
    <div x-cloak x-show="isEditOpen" x-transition x-transition.opacity
         @keydown.escape.window="closeEdit()"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div @click="closeEdit()" class="absolute inset-0"></div>

      <div @click.stop class="relative bg-white w-full max-w-4xl rounded-2xl shadow-2xl p-0 transform transition-all duration-300 scale-95 max-h-[90vh] overflow-hidden"
           x-transition:enter="scale-100" x-transition:enter-start="scale-95">
         
        <div class="modal-header p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <div>
                <h2 class="font-bold text-gray-900 text-2xl">Edit Menu</h2>
                <p class="text-gray-600 mt-1 text-sm">Update menu bundle details</p>
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
                <label class="form-label">Menu Type</label>
                <select name="type" class="form-select" x-model="editForm.type" required data-admin-select="true">
                  <option value="standard">Standard Menu</option>
                  <option value="special">Special Menu</option>
                </select>
              </div>

              <div>
                <label class="form-label">Meal Time</label>
                <select name="meal_time" class="form-select" x-model="editForm.meal" required data-admin-select="true">
                  <option value="breakfast">Breakfast</option>
                  <option value="am_snacks">AM Snacks</option>
                  <option value="lunch">Lunch</option>
                  <option value="pm_snacks">PM Snacks</option>
                  <option value="dinner">Dinner</option>
                </select>
              </div>
            </div>

            <div>
              <label class="form-label">Display Name (Optional)</label>
              <input name="name" class="form-input" placeholder="e.g., Breakfast Menu" x-model="editForm.name">
              <p class="text-xs text-gray-500 mt-1">If left empty, the menu will be named automatically as "Menu #X"</p>
            </div>

            <div>
              <label class="form-label">Description (Optional)</label>
              <textarea name="description" class="form-textarea" rows="2" placeholder="Short description..."></textarea>
            </div>

            {{-- Menu Items --}}
            <div class="border border-gray-200 rounded-lg p-4 recipe-form">
              <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Menu Items
              </h3>
              <div class="space-y-3">
                <template x-for="(item, index) in editForm.items" :key="index">
                  <div class="bg-gray-50 p-3 rounded-lg space-y-3 border border-gray-200">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                      <input type="text" :name="'items[' + index + '][name]'" x-model="item.name" placeholder="Food name" class="form-input flex-1" required>
                      <select :name="'items[' + index + '][type]'" x-model="item.type" class="form-select" data-admin-select="true">
                        <option value="food">Food/Main Dish</option>
                        <option value="drink">Drink</option>
                        <option value="dessert">Dessert</option>
                      </select>
                      <button type="button" @click="editForm.items.splice(index, 1)" class="self-end sm:self-auto p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                    {{-- Recipes for this item --}}
                    <div class="border-t border-gray-300 pt-3">
                      <h4 class="font-medium text-gray-700 mb-2 flex items-center text-xs">
                        <svg class="w-3 h-3 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Recipes
                      </h4>
                      <div class="space-y-2">
                        <template x-for="(recipe, rIndex) in item.recipes" :key="rIndex">
                          <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
                            <div class="flex-1 relative" x-data="{ dropdownOpen: false }">
                              <button type="button" @click="dropdownOpen = !dropdownOpen"
                                      class="form-input flex items-center justify-between hover:border-blue-500 text-left text-xs">
                                <span class="truncate">
                                  <template x-if="recipe.inventory_item_id">
                                    <span x-text="getIngredientLabel(recipe.inventory_item_id)"></span>
                                  </template>
                                  <template x-if="!recipe.inventory_item_id">
                                    <span class="text-gray-400">Select Ingredient</span>
                                  </template>
                                </span>
                                <svg class="w-3 h-3 text-gray-400 flex-shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                              </button>
                              
                              <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded shadow-lg z-50 max-h-72" style="display: none;">
                                <div class="sticky top-0 bg-white border-b border-gray-300 p-1.5 z-50">
                                  <input type="text" x-model="searchTerm" @keydown.escape="dropdownOpen = false"
                                         placeholder="Search..." class="form-input" style="font-size: 0.75rem;">
                                </div>
                                <div class="overflow-y-auto max-h-60">
                                  <template x-for="inv in getAllInventoryItems().filter(i => i.name.toLowerCase().includes(searchTerm.toLowerCase()))" :key="inv.id">
                                    <button type="button" @click="recipe.inventory_item_id = inv.id; dropdownOpen = false; searchTerm = '';"
                                            class="w-full text-left px-2 py-1.5 hover:bg-blue-50 border-b border-gray-100 last:border-0 transition-colors text-xs">
                                      <span x-text="inv.name"></span>
                                    </button>
                                  </template>
                                  <template x-if="getAllInventoryItems().filter(i => i.name.toLowerCase().includes(searchTerm.toLowerCase())).length === 0">
                                    <div class="px-2 py-2 text-center text-gray-500 text-xs">No ingredients found</div>
                                  </template>
                                </div>
                              </div>
                              <input type="hidden" :name="'items[' + index + '][recipes][' + rIndex + '][inventory_item_id]'" :value="recipe.inventory_item_id" required>
                            </div>
                            <input type="number" :name="'items[' + index + '][recipes][' + rIndex + '][quantity_needed]'" x-model="recipe.quantity_needed" placeholder="Qty" step="0.01" min="0.01" class="form-input w-full sm:w-20" required>
                            <input type="text" :name="'items[' + index + '][recipes][' + rIndex + '][unit]'" x-model="recipe.unit" placeholder="Unit" class="form-input w-full sm:w-16" required>
                            <button type="button" @click="item.recipes.splice(rIndex, 1)" class="self-end sm:self-auto p-1 text-red-600 hover:text-red-800 transition-colors duration-200 rounded hover:bg-red-50">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                              </svg>
                            </button>
                          </div>
                        </template>
                        <button type="button" @click="item.recipes.push({inventory_item_id: '', quantity_needed: '', unit: ''})" class="text-blue-600 hover:text-blue-800 font-medium flex items-center transition-colors duration-200 text-xs">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                          Add Recipe
                        </button>
                      </div>
                    </div>
                  </div>
                </template>
                <button type="button" @click="editForm.items.push({name: '', type: 'food', recipes: []})" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200 flex items-center text-sm">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Add Item
                </button>
              </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
              <div class="text-blue-800 flex items-center text-xs">
                <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Fixed price per head: <span class="font-semibold ml-1" x-text="editPriceText"></span>
                <span class="text-blue-600 ml-1">(auto-applied on save)</span>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeEdit()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium shadow-sm text-sm">
                Cancel
              </button>
              <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center transform hover:scale-105 text-sm">
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
  </template>

  {{-- DELETE MENU MODAL --}}
  <template x-teleport="body">
    <div x-cloak x-show="isDeleteOpen" @keydown.escape.window="closeDelete()"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div
        x-show="isDeleteOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-red-950/40 backdrop-blur-sm"
        @click="closeDelete()"
        aria-hidden="true"
      ></div>

      <div
        x-show="isDeleteOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-md overflow-hidden rounded-2xl border border-red-200 bg-white shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="delete-title"
        aria-describedby="delete-desc"
        @click.stop
      >
        <div class="flex items-start justify-between gap-4 border-b border-red-100 bg-red-50 px-6 py-4">
          <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </span>
            <div>
              <h2 id="delete-title" class="text-lg font-semibold text-red-900">Delete Menu</h2>
              <p class="text-xs text-red-700">This action cannot be undone.</p>
            </div>
          </div>
          <button class="rounded-full p-1 text-red-600 hover:text-red-700"
                  @click="closeDelete()" aria-label="Close">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div id="delete-desc" class="px-6 py-5 text-sm text-red-700">
          Are you sure you want to delete <span class="font-semibold text-red-900" x-text="deleteName || 'this menu'"></span>?
          This action will permanently remove the menu from the system.
        </div>

        <form @submit.prevent="confirmDelete" class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-red-100 bg-red-50/60">
          @csrf
          @method('DELETE')

          <button type="button" @click="closeDelete()"
                  class="px-4 py-2 bg-white text-red-700 rounded-lg border border-red-200 hover:bg-red-50 transition-colors duration-200 font-medium text-sm">
            Cancel
          </button>

          <button type="submit"
                  class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm text-sm">
            Delete Menu
          </button>
        </form>
      </div>
    </div>
  </template>

</div>

@if(session('menu_success') && \Illuminate\Support\Str::contains(session('menu_success'), 'Menu created'))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(() => {
      window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'menu-create-success' }));
    });
  });
</script>
@endif
@if(session('menu_success') && \Illuminate\Support\Str::contains(session('menu_success'), 'Menu updated'))
<script>
  document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(() => {
      window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'menu-update-success' }));
    });
  });
</script>
@endif

@endsection
