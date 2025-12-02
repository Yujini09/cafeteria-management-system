@extends('layouts.sidebar')
@section('page-title','Recipe: '.$menuItem->name)

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);">
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2" style="font-family: 'Poppins', sans-serif;">Recipe for: {{ $menuItem->name }}</h1>
            <div class="flex items-center text-gray-600" style="font-family: 'Poppins', sans-serif;">
                <svg class="w-5 h-5 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Bundle: {{ $menuItem->menu->name }}</span>
            </div>
        </div>
    </div>
</div>
    
  </div>

  {{-- Add Ingredient Form --}}
  <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-100 rounded-2xl p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center" style="font-family: 'Poppins', sans-serif;">
      <svg class="w-5 h-5 mr-2 text-[#057C3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Add Ingredient
    </h3>
    <form action="{{ route('admin.recipes.store', $menuItem) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
      @csrf
      <div class="md:col-span-5">
        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">Ingredient</label>
        <select name="inventory_item_id" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" required style="font-family: 'Poppins', sans-serif;">
          <option value="">-- Select Ingredient --</option>
          @foreach($inventory as $inv)
            <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->qty }} {{ $inv->unit }} left)</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-2" style="font-family: 'Poppins', sans-serif;">Quantity per Serving</label>
        <input type="number" step="0.001" name="quantity_needed" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent transition-all duration-200 bg-white" placeholder="0.000" required style="font-family: 'Poppins', sans-serif;">
      </div>
      <div class="md:col-span-3">
        <button class="w-full bg-gradient-to-r from-[#00462E] to-[#057C3C] hover:from-[#057C3C] hover:to-[#00462E] text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center transform hover:scale-105" style="font-family: 'Poppins', sans-serif;">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add/Update
        </button>
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
                    {{ $r->quantity_needed }} {{ $r->inventoryItem->unit }}
                  </span>
                  <span class="text-sm text-gray-500 ml-2" style="font-family: 'Poppins', sans-serif;">per serving</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <form action="{{ route('admin.recipes.destroy', [$menuItem,$r]) }}" method="POST" class="inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200 font-medium" style="font-family: 'Poppins', sans-serif;">
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