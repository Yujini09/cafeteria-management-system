@extends('layouts.sidebar')
@section('page-title','Manage Menus')

@section('content')
<style>
  [x-cloak] { display: none !important; }

  .recipe-form-input,
  .recipe-form-select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    background: #ffffff;
    color: #111827;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .recipe-form-input:focus,
  .recipe-form-select:focus {
    outline: none;
    border-color: #057C3C;
    box-shadow: 0 0 0 3px rgba(5, 124, 60, 0.15);
  }

  .recipe-form-select {
    appearance: none;
  }

  .recipe-field-meta {
    min-height: 1rem;
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
  }

  .recipe-control-wrap {
    position: relative;
  }

  .recipe-control-wrap .recipe-form-input,
  .recipe-control-wrap .recipe-form-select {
    padding-right: 2.9rem;
  }

  .recipe-control-chevron {
    width: 1rem;
    height: 1rem;
    color: #6b7280;
    pointer-events: none;
    display: block;
    flex-shrink: 0;
  }

  .recipe-trigger-button {
    position: absolute;
    top: 50%;
    right: 0.5rem;
    width: 1.75rem;
    height: 1.75rem;
    padding: 0;
    border: 0;
    background: transparent;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    border-radius: 0.5rem;
    transition: color 0.2s ease, background-color 0.2s ease;
    z-index: 1;
  }

  .recipe-trigger-button:hover {
    color: #374151;
    background: rgba(243, 244, 246, 0.9);
  }
</style>
<script>
  if (typeof window.recipeIngredientForm !== 'function') {
    window.recipeIngredientForm = function (opts = {}) {
      return {
        allInventoryItems: Array.isArray(opts.inventoryItems) ? opts.inventoryItems : [],
        selectedInventoryId: opts.selectedInventoryId ? String(opts.selectedInventoryId) : '',
        ingredientSearch: opts.selectedIngredientLabel || '',
        stockUnit: opts.initialStockUnit || '',
        dropdownOpen: false,
        normalizeUnit(unit) {
          const value = String(unit || '').trim().toLowerCase();
          if (!value) return '';

          const aliases = {
            ml: 'ml',
            milliliter: 'ml',
            milliliters: 'ml',
            millilitre: 'ml',
            millilitres: 'ml',
            liter: 'liters',
            liters: 'liters',
            litre: 'liters',
            litres: 'liters',
            l: 'liters',
            g: 'g',
            gram: 'g',
            grams: 'g',
            kg: 'kgs',
            kgs: 'kgs',
            kilogram: 'kgs',
            kilograms: 'kgs',
            pc: 'pieces',
            pcs: 'pieces',
            piece: 'pieces',
            pieces: 'pieces',
            pack: 'packs',
            packs: 'packs',
          };

          return aliases[value] || value;
        },
        init() {
          if (!this.ingredientSearch && this.selectedInventoryId) {
            this.ingredientSearch = this.getIngredientLabel(this.selectedInventoryId) || '';
          }

          if (!this.stockUnit && this.selectedInventoryId) {
            this.stockUnit = this.getIngredientUnit(this.selectedInventoryId) || '';
          }
        },
        normalizeIngredientId(id) {
          if (id === null || id === undefined || id === '') return null;
          return String(id);
        },
        getIngredientLabel(id) {
          const item = this.allInventoryItems.find((inv) => this.normalizeIngredientId(inv?.id) === this.normalizeIngredientId(id));
          return item ? item.name : '';
        },
        getIngredientUnit(id) {
          const item = this.allInventoryItems.find((inv) => this.normalizeIngredientId(inv?.id) === this.normalizeIngredientId(id));
          return item ? this.normalizeUnit(item.unit) : '';
        },
        getAvailableIngredients(searchTerm = '') {
          const term = (searchTerm || '').toLowerCase();
          return this.allInventoryItems.filter((inv) => {
            const invId = this.normalizeIngredientId(inv?.id);
            if (!invId) return false;
            if (!term) return true;
            return (inv?.name || '').toLowerCase().includes(term);
          });
        },
        selectIngredient(item) {
          this.selectedInventoryId = String(item.id);
          this.ingredientSearch = item.name || '';
          this.stockUnit = this.getIngredientUnit(item.id) || '';
          this.dropdownOpen = false;
        },
        syncIngredientInput() {
          this.dropdownOpen = true;

          const typed = (this.ingredientSearch || '').toLowerCase();
          const current = (this.getIngredientLabel(this.selectedInventoryId) || '').toLowerCase();

          if (!typed) {
            this.selectedInventoryId = '';
            this.stockUnit = '';
            return;
          }

          if (this.selectedInventoryId && typed !== current) {
            this.selectedInventoryId = '';
            this.stockUnit = '';
          }
        },
      };
    };
  }
</script>
<div class="admin-page-shell bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
  {{-- Header --}}
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <!-- Back Arrow -->
        <a href="{{ route('admin.menus.index') }}" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <!-- Recipe Icon -->
        <div class="w-12 h-12 bg-gradient-to-r from-[#00462E] to-[#057C3C] rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
        </div>
        <!-- Text Content -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2" style="font-family: 'Poppins', sans-serif;">Manage Menus</h1>
            <div class="flex items-center text-gray-600" style="font-family: 'Poppins', sans-serif;">
                <svg class="w-5 h-5 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Recipe for: {{ $menuItem->name }} in {{ $menuItem->menu->name }}</span>
            </div>
        </div>
    </div>
</div>

  @if($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
      <p class="font-semibold text-red-900" style="font-family: 'Poppins', sans-serif;">Unable to save the ingredient.</p>
      <ul class="mt-2 space-y-1" style="font-family: 'Poppins', sans-serif;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Add Ingredient Form --}}
  @php
      $selectedInventory = $inventory->firstWhere('id', (int) old('inventory_item_id'));
      $selectedStockUnit = \App\Support\RecipeUnit::display($selectedInventory?->unit);
      $selectedInventoryName = $selectedInventory?->name ?? '';
      $recipeUnits = \App\Support\RecipeUnit::RECIPE_UNITS;
  @endphp
  <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-100 rounded-2xl p-6 mb-8"
       x-data='recipeIngredientForm({
           inventoryItems: @js($inventory->map(fn ($inv) => [
               "id" => $inv->id,
               "name" => $inv->name,
               "unit" => $inv->unit,
           ])->values()),
           selectedInventoryId: @js(old("inventory_item_id", "")),
           selectedIngredientLabel: @js($selectedInventoryName),
           initialStockUnit: @js($selectedStockUnit),
       })'
       x-init="init()">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center" style="font-family: 'Poppins', sans-serif;">
      <svg class="w-5 h-5 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Add Ingredient
    </h3>
    <form action="{{ route('admin.recipes.store', $menuItem) }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-start" data-action-loading>
      @csrf
      <div class="md:col-span-5">
        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">Ingredient</label>
        <div class="relative" @click.outside="dropdownOpen = false">
          <div class="recipe-control-wrap">
            <input type="text"
                   x-model="ingredientSearch"
                   @focus="dropdownOpen = true"
                   @blur="dropdownOpen = false"
                   @input="syncIngredientInput()"
                   @keydown.escape="dropdownOpen = false"
                   placeholder="Search ingredient..."
                   autocomplete="off"
                   class="recipe-form-input"
                   role="combobox"
                   aria-autocomplete="list"
                   :aria-expanded="dropdownOpen ? 'true' : 'false'"
                   aria-controls="recipe-ingredient-list"
                   style="font-family: 'Poppins', sans-serif;">
            <button type="button"
                    class="recipe-trigger-button"
                    @mousedown.prevent
                    @click="dropdownOpen = !dropdownOpen"
                    :aria-label="(dropdownOpen ? 'Close' : 'Open') + ' ingredient dropdown'">
              <svg class="recipe-control-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
          </div>

          <div x-cloak
               x-show="dropdownOpen"
               class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-300 rounded-lg shadow-2xl z-[100] w-full flex flex-col max-h-72">
            <div class="overflow-y-auto flex-1" id="recipe-ingredient-list">
              <template x-for="inv in getAvailableIngredients(ingredientSearch)" :key="inv.id">
                <button type="button"
                        @mousedown.prevent
                        @click="selectIngredient(inv)"
                        class="w-full text-left px-3 py-2 hover:bg-green-50 border-b border-gray-100 last:border-0 transition-colors text-sm"
                        style="font-family: 'Poppins', sans-serif;">
                  <span x-text="inv.name"></span>
                </button>
              </template>
              <template x-if="getAvailableIngredients(ingredientSearch).length === 0">
                <div class="px-3 py-4 text-center text-gray-500 text-sm" style="font-family: 'Poppins', sans-serif;">
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
        <input type="hidden" name="inventory_item_id" :value="selectedInventoryId" required>
        <p class="recipe-field-meta text-xs text-transparent select-none" aria-hidden="true">.</p>
      </div>
      <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">Qty / 1 Pax</label>
        <input type="number" step="0.001" min="0.001" name="quantity_needed" value="{{ old('quantity_needed') }}" class="recipe-form-input" placeholder="0.000" required style="font-family: 'Poppins', sans-serif;">
        <p class="recipe-field-meta text-xs text-transparent select-none" aria-hidden="true">.</p>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">Unit</label>
        <div class="recipe-control-wrap">
          <select name="unit" class="recipe-form-select" required style="font-family: 'Poppins', sans-serif;">
            <option value="">Select unit</option>
            @foreach($recipeUnits as $recipeUnit)
              <option value="{{ $recipeUnit }}" @selected(old('unit') === $recipeUnit)>{{ $recipeUnit === 'pieces' ? 'piece/s' : $recipeUnit }}</option>
            @endforeach
          </select>
          <span class="pointer-events-none recipe-trigger-button" aria-hidden="true">
            <svg class="recipe-control-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </span>
        </div>
        <p class="recipe-field-meta text-xs text-gray-500" x-text="'Stock unit: ' + (stockUnit || '-')"></p>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-transparent mb-2 select-none" aria-hidden="true" style="font-family: 'Poppins', sans-serif;">Action</label>
        <button type="submit" data-loading-text="Saving Ingredient..." class="h-[52px] w-full whitespace-nowrap bg-gradient-to-r from-[#00462E] to-[#057C3C] px-6 py-3 rounded-xl font-semibold text-white transition-all duration-300 shadow-lg hover:from-[#057C3C] hover:to-[#00462E] hover:shadow-xl flex items-center justify-center" style="font-family: 'Poppins', sans-serif;">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add/Update
        </button>
        <p class="recipe-field-meta text-xs text-transparent select-none" aria-hidden="true">.</p>
      </div>
    </form>
  </div>

  {{-- Ingredients Table --}}
  <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900 flex items-center" style="font-family: 'Poppins', sans-serif;">
        <svg class="w-5 h-5 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        Recipe Ingredients
      </h3>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider" style="font-family: 'Poppins', sans-serif;">Ingredient</th>
            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 uppercase tracking-wider" style="font-family: 'Poppins', sans-serif;">Quantity per Serving</th>
            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 uppercase tracking-wider" style="font-family: 'Poppins', sans-serif;">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($menuItem->recipes as $r)
            <tr class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                    </svg>
                  </div>
                  <span class="text-sm font-medium text-gray-900" style="font-family: 'Poppins', sans-serif;">{{ $r->inventoryItem->name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800" style="font-family: 'Poppins', sans-serif;">
                    {{ $r->quantity_needed }} {{ \App\Support\RecipeUnit::display($r->unit) ?: \App\Support\RecipeUnit::display($r->inventoryItem->unit) }}
                  </span>
                  <span class="text-sm text-gray-500 ml-2" style="font-family: 'Poppins', sans-serif;">per 1 pax</span>
                  <span class="text-xs text-gray-400 ml-2" style="font-family: 'Poppins', sans-serif;">Stock unit: {{ \App\Support\RecipeUnit::display($r->inventoryItem->unit) }}</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <form action="{{ route('admin.recipes.destroy', [$menuItem,$r]) }}" method="POST" class="inline" data-action-loading>
                  @csrf @method('DELETE')
                  <button type="submit" data-loading-text="Deleting Ingredient..." class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 font-medium" style="font-family: 'Poppins', sans-serif;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remove
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-500">
                  <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <p class="text-lg font-medium mb-2" style="font-family: 'Poppins', sans-serif;">No ingredients added yet</p>
                  <p class="text-sm" style="font-family: 'Poppins', sans-serif;">Start by adding ingredients to create your recipe</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Summary Card --}}
  @if($menuItem->recipes->count() > 0)
    <div class="mt-6 bg-gradient-to-r from-[#00462E] to-[#057C3C] rounded-2xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold mb-2" style="font-family: 'Poppins', sans-serif;">Recipe Summary</h3>
          <p class="text-green-100" style="font-family: 'Poppins', sans-serif;">
            {{ $menuItem->recipes->count() }} ingredient(s) in this recipe
          </p>
        </div>
        <div class="text-right">
          <p class="text-2xl font-bold" style="font-family: 'Poppins', sans-serif;">{{ $menuItem->recipes->count() }}</p>
          <p class="text-green-100 text-sm" style="font-family: 'Poppins', sans-serif;">Total Ingredients</p>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
