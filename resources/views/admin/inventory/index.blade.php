@extends('layouts.sidebar')
@section('page-title', 'Inventory')

@section('content')
@php
    $hasActiveInventoryFilters = filled($search ?? '') || filled($category ?? '') || ($sort ?? 'name') !== 'name' || ($direction ?? 'asc') !== 'asc';
    $resetInventoryFiltersUrl = route('admin.inventory.index');
    $inventoryUnits = \App\Support\RecipeUnit::RECIPE_UNITS;
    $inventoryAutoDeducted = session('inventory_auto_deducted');
    $nextQtyDirection = $sort === 'qty' && $direction === 'asc' ? 'desc' : 'asc';
    $qtySortIcon = $sort === 'qty' && $direction === 'desc' ? 'fa-arrow-down' : 'fa-arrow-up';
    $qtySortIconClass = $sort === 'qty'
        ? 'text-admin-primary group-hover:text-admin-primary'
        : 'text-admin-neutral-400 group-hover:text-admin-neutral-600';
    $nextExpiryDirection = $sort === 'expiry_date' && $direction === 'asc' ? 'desc' : 'asc';
    $expirySortIcon = $sort === 'expiry_date' && $direction === 'desc' ? 'fa-arrow-down' : 'fa-arrow-up';
    $expirySortIconClass = $sort === 'expiry_date'
        ? 'text-admin-primary group-hover:text-admin-primary'
        : 'text-admin-neutral-400 group-hover:text-admin-neutral-600';
@endphp
<style>
.table-view-overlay-host {
    position: relative;
}

.table-floating-actions {
    position: absolute;
    right: 0.75rem;
    top: 0.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transform: translateX(8px);
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
    transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s ease;
    z-index: 30;
}

.table-floating-actions.is-visible {
    opacity: 1;
    pointer-events: auto;
    visibility: visible;
    transform: translateX(0);
}

.table-floating-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid transparent;
    border-radius: 9999px;
    padding: 0.35rem 0.65rem;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    transition: background 0.16s ease;
}

.table-floating-action-edit {
    background: var(--primary);
}

.table-floating-action-edit:hover {
    background: #003824;
}

.table-floating-action-delete {
    background: #dc2626;
}

.table-floating-action-delete:hover {
    background: #b91c1c;
}

.page-header {
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-content {
        width: 100%;
    }
}
</style>

<div x-data="{ 
    showCreateModal: false, 
    showEditModal: false, 
    showDeleteModal: false, 
    editingItem: null, 
    deletingItem: null, 
    createUnit: '',
    updateRoute: '{{ route('admin.inventory.update', ':id') }}',
    deleteRoute: '{{ route('admin.inventory.destroy', ':id') }}',
    requiresWholeQuantity(unit) {
        const normalized = this.normalizeInventoryUnit(unit);
        return normalized === 'pieces' || normalized === 'packs';
    },
    qtyStepForUnit(unit) {
        return this.requiresWholeQuantity(unit) ? '1' : '0.01';
    },
    normalizedQtyForInput(item) {
        if (!item) return '';
        const qty = Number(item.qty ?? 0);
        if (!Number.isFinite(qty)) return '';
        if (this.requiresWholeQuantity(item.unit)) return String(Math.round(qty));
        return String(Math.round(qty * 100) / 100);
    },
    normalizeInventoryUnit(unit) {
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
    }
}"
    x-init="window.addEventListener('close-inventory-modals', function() { showCreateModal = false; showEditModal = false; showDeleteModal = false; editingItem = null; deletingItem = null; createUnit = ''; })"
    x-effect="document.body.classList.toggle('overflow-hidden', showCreateModal || showEditModal || showDeleteModal)"
    @keydown.escape.window="showCreateModal = false; showEditModal = false; showDeleteModal = false; editingItem = null; deletingItem = null; createUnit = ''">

    <x-success-modal name="inventory-create-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item added successfully.</p>
    </x-success-modal>
    <x-success-modal name="inventory-update-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item updated successfully.</p>
    </x-success-modal>
    <x-success-modal name="inventory-delete-success" title="Deleted" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item deleted successfully.</p>
    </x-success-modal>

    <x-admin.ui.modal name="inventory-restock-auto-deduct" title="Auto-deduct Applied" variant="info" maxWidth="sm" icon="fa-box-open">
        <p class="text-sm text-admin-neutral-700">
            Restocked stock was automatically deducted for approved reservations with pending shortages.
        </p>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex items-center justify-between gap-3">
                <dt class="text-admin-neutral-500">Item:</dt>
                <dd id="inventoryAutoDeductedItem" class="font-semibold text-admin-neutral-900">-</dd>
            </div>
            <div class="flex items-center justify-between gap-3">
                <dt class="text-admin-neutral-500">Auto-deducted:</dt>
                <dd id="inventoryAutoDeductedQty" class="font-semibold text-admin-success">0</dd>
            </div>
            <div class="flex items-center justify-between gap-3">
                <dt class="text-admin-neutral-500">Reservations affected:</dt>
                <dd id="inventoryAutoDeductedCount" class="font-semibold text-admin-neutral-900">0</dd>
            </div>
        </dl>
        <x-slot name="footer">
            <x-admin.ui.button.primary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'inventory-restock-auto-deduct' }))">
                Close & Refresh
            </x-admin.ui.button.primary>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="inventoryUsageLogs" title="Inventory Usage Logs" icon="fa-file-lines" iconStyle="fas" variant="info" maxWidth="4xl">
        <button type="button"
                class="absolute top-4 right-4 rounded-full p-1.5 text-admin-neutral-400 hover:bg-admin-neutral-100 hover:text-admin-neutral-600 transition-colors duration-admin"
                @click="$dispatch('close-admin-modal', 'inventoryUsageLogs')"
                aria-label="Close inventory usage logs modal">
            <x-admin.ui.icon name="fa-xmark" size="sm" />
        </button>
        <div class="flex h-[calc(100vh-12rem)] max-h-[82vh] min-h-0 flex-col gap-4 overflow-y-auto pr-1 modern-scrollbar">
            <div class="shrink-0 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <p class="text-sm text-admin-neutral-700">Track automatic deductions and manual stock adjustments.</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-admin-neutral-600">
                            Total
                            <span id="inventoryUsageTotalCount" class="text-admin-neutral-900">0</span>
                        </span>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3">
                    <div class="relative">
                        <input type="text"
                               id="inventoryUsageSearchInput"
                               placeholder="Search item, user, or reservation reference..."
                               class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 pl-10 pr-10 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                               aria-label="Search inventory usage logs">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-admin-neutral-400">
                            <x-admin.ui.icon name="fa-magnifying-glass" size="sm" />
                        </span>
                        <button type="button"
                                id="inventoryUsageClearSearch"
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-admin-neutral-400 hover:bg-admin-neutral-100 hover:text-admin-neutral-600"
                                aria-label="Clear inventory usage search">
                            <x-admin.ui.icon name="fa-xmark" size="sm" />
                        </button>
                    </div>

                    <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-[minmax(12rem,14rem)_minmax(10rem,1fr)_minmax(10rem,1fr)_auto] xl:items-end">
                        <div class="flex flex-col gap-1">
                            <label for="inventoryUsageTypeFilter" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Type</label>
                            <select id="inventoryUsageTypeFilter" class="admin-select w-full" data-admin-select="true" aria-label="Filter inventory usage by type">
                                <option value="">All Types</option>
                                <option value="auto_deduct">Auto-deduct</option>
                                <option value="manual_adjustment">Manual adjustment</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="inventoryUsageDateFrom" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date from</label>
                            <input type="date"
                                   id="inventoryUsageDateFrom"
                                   class="w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 px-3 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                                   aria-label="Inventory usage date from">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label for="inventoryUsageDateTo" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date to</label>
                            <input type="date"
                                   id="inventoryUsageDateTo"
                                   class="w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 px-3 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                                   aria-label="Inventory usage date to">
                        </div>

                        <div class="flex items-end">
                            <x-admin.ui.button.secondary type="button" id="inventoryUsageResetFilters" class="w-full sm:w-auto sm:shrink-0">Reset</x-admin.ui.button.secondary>
                        </div>
                    </div>
                </div>
            </div>

            <div id="inventoryUsageTableContainer" class="min-w-0 shrink-0 overflow-x-auto rounded-admin border border-admin-neutral-200 bg-white modern-scrollbar">
                <table class="modern-table w-full table-fixed min-w-[76rem]">
                    <colgroup>
                        <col class="w-56">
                        <col class="w-72">
                        <col class="w-44">
                        <col class="w-40">
                        <col class="w-40">
                        <col class="w-36">
                        <col class="w-52">
                    </colgroup>
                    <thead class="sticky top-0 z-10 bg-admin-neutral-50">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                            <th class="px-4 py-3 border-b border-admin-neutral-200">
                                <button id="inventoryUsageSortByDateBtn" type="button" class="group inline-flex items-center gap-2">
                                    <span>Date/Time</span>
                                    <x-admin.ui.icon id="inventoryUsageSortIconDate" name="fa-arrow-down" size="xs" class="text-admin-neutral-400" />
                                </button>
                            </th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">Item</th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">Type</th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">Quantity Change</th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">New Balance</th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">Reference</th>
                            <th class="px-4 py-3 border-b border-admin-neutral-200">Performed By</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryUsageTableBody">
                        <tr>
                            <td colspan="7" class="py-10 text-center text-admin-neutral-500">Loading usage logs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="inventoryUsagePagination" class="hidden shrink-0 flex-col gap-3 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p id="inventoryUsagePaginationInfo" class="text-center text-xs leading-relaxed text-admin-neutral-500 sm:text-left"></p>
                <nav id="inventoryUsagePaginationNav" role="navigation" aria-label="Inventory usage pagination" class="flex w-full flex-wrap items-center justify-center gap-1 sm:w-auto sm:justify-end"></nav>
            </div>
        </div>
    </x-admin.ui.modal>
    
    <div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full overflow-hidden flex flex-col">
        <form method="GET" action="{{ route('admin.inventory.index') }}" id="inventoryFiltersForm">
            @if($sort)
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif
            @if($direction)
                <input type="hidden" name="direction" value="{{ $direction }}">
            @endif
            <button type="submit" class="sr-only">Apply inventory filters</button>

            <div class="page-header items-start">
                <div class="header-content">
                    <div class="header-icon">
                        <x-admin.ui.icon name="fa-boxes-stacked" style="fas" class="text-white w-6 h-6" />
                    </div>
                    <div class="header-text">
                        <h1 class="header-title">Inventory</h1>
                        <p class="header-subtitle">Manage and track your inventory items and quantities</p>
                    </div>
                </div>
                <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
                    <div class="relative w-full sm:w-64 md:w-72">
                        <input type="text"
                               inputmode="search"
                               autocomplete="off"
                               id="inventorySearchInput"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Search inventory items..."
                               class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                               aria-label="Search inventory items">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <button id="inventoryClearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600 {{ filled($search) ? '' : 'hidden' }}" aria-label="Clear inventory search">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                    <x-admin.ui.icon name="fa-boxes-stacked" size="xs" />
                    Total Items: {{ $items->total() }}
                </span>
                <div class="flex w-full sm:w-auto sm:justify-end">
                    <x-admin.ui.button.secondary type="button" id="inventoryUsageLogsBtn" class="w-full justify-center sm:w-auto">
                        <x-admin.ui.icon name="fa-file-lines" size="sm" />
                        Usage Logs
                    </x-admin.ui.button.secondary>
                </div>
            </div>

            <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <label for="inventoryCategoryFilter" class="text-sm font-semibold text-admin-neutral-700">Filter by Category</label>
                        <div class="w-full sm:w-64">
                            <select name="category" id="inventoryCategoryFilter" class="admin-select w-full" data-admin-select="true">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="{{ $resetInventoryFiltersUrl }}"
                           class="btn-secondary inline-flex w-full items-center justify-center sm:w-auto">
                            Reset
                        </a>
                    </div>
                    <div class="flex w-full sm:w-auto sm:justify-end">
                        <x-admin.ui.button.primary type="button" @click="showCreateModal = true">
                            <x-admin.ui.icon name="fa-plus" style="fas" size="sm" />
                            Add Item
                        </x-admin.ui.button.primary>
                    </div>
                </div>
            </div>
        </form>

        <div id="inventoryTableHost" class="table-view-overlay-host">
            <div id="inventoryTableScroll" class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
                <table class="modern-table table-fixed">
                <colgroup>
                    <col class="w-14">
                    <col class="w-64">
                    <col class="w-32">
                    <col class="w-24">
                    <col class="w-40">
                    <col class="w-40">
                    <col class="w-40">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide w-14 whitespace-nowrap overflow-hidden text-ellipsis">#</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'page' => null]) }}" class="hover:text-admin-neutral-700 transition-colors duration-200">Item Name</a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'qty', 'direction' => $nextQtyDirection, 'page' => null]) }}"
                               class="group inline-flex items-center gap-2 hover:text-admin-neutral-700 transition-colors duration-200"
                               aria-label="Sort by quantity">
                                <span>Quantity</span>
                                <x-admin.ui.icon name="{{ $qtySortIcon }}" style="fas" size="sm" class="{{ $qtySortIconClass }} transition-colors duration-admin" />
                            </a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Unit</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'expiry_date', 'direction' => $nextExpiryDirection, 'page' => null]) }}"
                               class="group inline-flex items-center gap-2 hover:text-admin-neutral-700 transition-colors duration-200"
                               aria-label="Sort by expiry date">
                                <span>Expiry Date</span>
                                <x-admin.ui.icon name="{{ $expirySortIcon }}" style="fas" size="sm" class="{{ $expirySortIconClass }} transition-colors duration-admin" />
                            </a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Category</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Last Updated</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        @php
                            $requiresWholeQuantity = \App\Support\RecipeUnit::requiresWholeQuantity($item->unit);
                            $itemPayload = json_encode([
                                'id' => $item->id,
                                'name' => $item->name,
                                'category' => $item->category,
                                'qty' => $requiresWholeQuantity
                                    ? (int) round((float) $item->qty)
                                    : round((float) $item->qty, 2),
                                'unit' => $item->unit,
                                'expiry_date' => $item->expiry_date,
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                        @endphp
                        <tr class="inventory-row hover:bg-admin-neutral-50 transition-colors duration-admin"
                            data-item-id="{{ $item->id }}"
                            data-item="{{ $itemPayload }}">
                            <td class="py-3 px-4 border-b border-admin-neutral-100 text-admin-neutral-500 font-semibold whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ ($items->firstItem() ?? 0) + $loop->index }}
                            </td>

                            <td class="py-3 px-4 border-b border-admin-neutral-100 font-semibold text-admin-neutral-900 whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $item->name }}
                            </td>

                            <td class="py-3 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">
                                @php
                                    $qtyClass = $item->qty <= 5 ? 'status-critical' : ($item->qty <= 10 ? 'status-warning' : 'status-good');
                                @endphp
                                <span class="status-badge {{ $qtyClass }} inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide">
                                    {{ \App\Support\RecipeUnit::formatStockQuantity($item->qty, $item->unit) }}
                                </span>
                            </td>

                            <td class="py-3 px-4 border-b border-admin-neutral-100 text-admin-neutral-600 whitespace-nowrap overflow-hidden text-ellipsis">{{ $item->unit }}</td>
                            <td class="py-3 px-4 border-b border-admin-neutral-100 text-admin-neutral-600 whitespace-nowrap overflow-hidden text-ellipsis">{{ $item->expiry_date ?? 'N/A' }}</td>
                            <td class="py-3 px-4 border-b border-admin-neutral-100 text-admin-neutral-600 whitespace-nowrap overflow-hidden text-ellipsis">{{ $item->category }}</td>
                            <td class="py-3 px-4 border-b border-admin-neutral-100 text-admin-neutral-600 whitespace-nowrap overflow-hidden text-ellipsis">{{ $item->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <x-admin.ui.icon name="fa-boxes-stacked" style="fas" class="text-admin-neutral-400 w-6 h-6" />
                                    </div>
                                    @if($hasActiveInventoryFilters)
                                        <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No inventory items match the current search or filters</p>
                                        <p class="text-sm text-admin-neutral-500">Try adjusting the search term or category.</p>
                                    @else
                                        <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No inventory items found</p>
                                        <p class="text-sm text-admin-neutral-500">Start by adding your first item</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
            <div id="inventoryFloatingActions" class="table-floating-actions" aria-hidden="true">
                <button id="inventoryFloatingEditBtn"
                        type="button"
                        class="table-floating-action-btn table-floating-action-edit"
                        data-item=""
                        @click="if ($el.dataset.item) { editingItem = JSON.parse($el.dataset.item); showEditModal = true }"
                        aria-label="Edit inventory item">
                    <x-admin.ui.icon name="fa-pen" style="fas" size="sm" class="text-white" />
                    Edit
                </button>
                <button id="inventoryFloatingDeleteBtn"
                        type="button"
                        class="table-floating-action-btn table-floating-action-delete"
                        data-item=""
                        @click="if ($el.dataset.item) { deletingItem = JSON.parse($el.dataset.item); showDeleteModal = true }"
                        aria-label="Delete inventory item">
                    <x-admin.ui.icon name="fa-trash-can" style="fas" size="sm" class="text-white" />
                    Delete
                </button>
            </div>
        </div>

        @if($items->hasPages())
            <div class="mt-6">
                {{ $items->links('components.pagination') }}
            </div>
        @endif
    </div>

    <div x-show="showCreateModal" @click="showCreateModal = false" class="fixed inset-0 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="w-full max-w-lg rounded-admin-lg bg-white shadow-admin-modal border border-admin-neutral-200 p-6 relative z-10" x-transition.scale.90>
            <button @click="showCreateModal = false"
                    class="absolute top-4 right-4 text-admin-neutral-500 hover:text-admin-neutral-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-admin-neutral-900">Add Inventory Item</h2>

            <form id="createInventoryForm" action="{{ route('admin.inventory.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="create_name" class="block text-sm font-medium text-admin-neutral-700">Item Name</label>
                    <input type="text" name="name" id="create_name" required class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20" autocomplete="off">
                </div>

                <div>
                    <label for="create_category" class="block text-sm font-medium text-admin-neutral-700">Category</label>
                    <select name="category" id="create_category" required class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a category</option>
                        <option value="Perishable">Perishable</option>
                        <option value="Condiments">Condiments</option>
                        <option value="Frozen">Frozen</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div>
                    <label for="create_qty" class="block text-sm font-medium text-admin-neutral-700">Quantity</label>
                    <input type="number" name="qty" id="create_qty" min="0" :step="qtyStepForUnit(createUnit)" required class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                </div>

                <div>
                    <label for="create_unit" class="block text-sm font-medium text-admin-neutral-700">Unit</label>
                    <select name="unit" id="create_unit" required x-model="createUnit" class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a unit</option>
                        @foreach ($inventoryUnits as $inventoryUnit)
                            <option value="{{ $inventoryUnit }}">{{ $inventoryUnit === 'pieces' ? 'piece/s' : $inventoryUnit }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="create_expiry_date" class="block text-sm font-medium text-admin-neutral-700">Expiry Date</label>
                    <input type="date" name="expiry_date" id="create_expiry_date" class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                    <small class="text-admin-neutral-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end gap-3">
                    <x-admin.ui.button.secondary type="button" @click="showCreateModal = false">
                        Cancel
                    </x-admin.ui.button.secondary>
                    <x-admin.ui.button.primary type="submit" data-loading-text="Saving Item...">
                        Save Item
                    </x-admin.ui.button.primary>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" @click="showEditModal = false; editingItem = null" class="fixed inset-0 bg-admin-neutral-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="w-full max-w-lg rounded-admin-lg bg-white shadow-admin-modal border border-admin-neutral-200 p-6 relative z-10" x-transition.scale.90>
            <button @click="showEditModal = false; editingItem = null"
                    class="absolute top-4 right-4 text-admin-neutral-500 hover:text-admin-neutral-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-admin-neutral-900">Edit Inventory Item</h2>

            <form id="editInventoryForm" x-bind:action="editingItem ? updateRoute.replace(':id', editingItem.id) : ''" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label for="edit_name" class="block text-sm font-medium text-admin-neutral-700">Item Name</label>
                    <input type="text" name="name" id="edit_name" required x-bind:value="editingItem ? editingItem.name : ''" class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20" autocomplete="off">
                </div>

                <div>
                    <label for="edit_category" class="block text-sm font-medium text-admin-neutral-700">Category</label>
                    <select name="category" id="edit_category" required x-bind:value="editingItem ? editingItem.category : ''" class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a category</option>
                        <option value="Perishable">Perishable</option>
                        <option value="Condiments">Condiments</option>
                        <option value="Frozen">Frozen</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div>
                    <label for="edit_qty" class="block text-sm font-medium text-admin-neutral-700">Quantity</label>
                    <input type="number" name="qty" id="edit_qty" min="0" :step="qtyStepForUnit(editingItem ? editingItem.unit : '')" required x-bind:value="normalizedQtyForInput(editingItem)" class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                </div>

                <div>
                    <label for="edit_unit" class="block text-sm font-medium text-admin-neutral-700">Unit</label>
                    <select name="unit" id="edit_unit" required x-bind:value="editingItem ? normalizeInventoryUnit(editingItem.unit) : ''" class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a unit</option>
                        @foreach ($inventoryUnits as $inventoryUnit)
                            <option value="{{ $inventoryUnit }}">{{ $inventoryUnit === 'pieces' ? 'piece/s' : $inventoryUnit }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="edit_expiry_date" class="block text-sm font-medium text-admin-neutral-700">Expiry Date</label>
                    <input type="date" name="expiry_date" id="edit_expiry_date" x-bind:value="editingItem ? editingItem.expiry_date : ''" class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                    <small class="text-admin-neutral-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end gap-3">
                    <x-admin.ui.button.secondary type="button" @click="showEditModal = false; editingItem = null">
                        Cancel
                    </x-admin.ui.button.secondary>
                    <x-admin.ui.button.primary type="submit" data-loading-text="Updating Item...">
                        Update Item
                    </x-admin.ui.button.primary>
                </div>
            </form>
        </div>
    </div>
    
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div
            x-show="showDeleteModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 bg-admin-neutral-900/50 backdrop-blur-sm"
            @click="showDeleteModal = false; deletingItem = null"
            aria-hidden="true"
        ></div>

        <div
            x-show="showDeleteModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-sm overflow-hidden rounded-admin-lg border border-admin-neutral-200 bg-white shadow-admin-modal"
            @click.stop
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-inventory-title"
            aria-describedby="delete-inventory-desc"
        >
            <div class="flex items-center justify-between gap-4 border-b border-admin-neutral-100 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-admin bg-admin-danger-light text-admin-danger">
                        <i class="fas fa-triangle-exclamation text-lg"></i>
                    </span>
                    <div>
                        <h2 id="delete-inventory-title" class="text-lg font-semibold text-admin-neutral-900">Confirm Deletion</h2>
                        <p class="text-xs text-admin-neutral-500">This action cannot be undone.</p>
                    </div>
                </div>
                <button @click="showDeleteModal = false; deletingItem = null"
                        class="rounded-full p-1 text-admin-neutral-400 hover:text-admin-neutral-600" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="delete-inventory-desc" class="px-6 py-5 text-sm text-admin-neutral-600">
                Are you sure you want to delete
                <span class="font-semibold text-admin-neutral-900" x-text="deletingItem ? deletingItem.name : 'this item'"></span>?
                This item will be removed from inventory.
            </div>

            <form id="deleteInventoryForm" x-bind:action="deletingItem ? deleteRoute.replace(':id', deletingItem.id) : '#'" x-bind:data-id="deletingItem ? deletingItem.id : ''" method="POST"
                  class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-admin-neutral-100 bg-admin-neutral-50">
                @csrf @method('DELETE')

                <x-admin.ui.button.secondary type="button" @click="showDeleteModal = false; deletingItem = null">
                    Cancel
                </x-admin.ui.button.secondary>
                <x-admin.ui.button.danger type="submit" data-loading-text="Deleting Item...">
                    Delete
                </x-admin.ui.button.danger>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('livewire:navigated', function() {
    const rootCloseEvent = new Event('close-inventory-modals');
    const inventoryUsageLogsEndpoint = @json(route('admin.inventory.usage-logs'));
    const inventoryAutoDeductedFlash = @json($inventoryAutoDeducted);
    const inventoryUsagePerPage = 10;
    let allInventoryUsageLogs = [];
    let filteredInventoryUsageLogs = [];
    let currentInventoryUsagePage = 1;
    let currentInventoryUsageSortDirection = 'desc';

    function inventoryUsageText(value) {
        return String(value ?? '').trim();
    }

    function inventoryUsageEscapeHtml(value) {
        return inventoryUsageText(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function inventoryUsageTypeLabel(type) {
        if (type === 'auto_deduct') return 'Auto-deduct';
        if (type === 'manual_adjustment') return 'Manual adjustment';
        return 'Unknown';
    }

    function inventoryUsageTypeBadgeClass(type) {
        if (type === 'auto_deduct') {
            return 'bg-admin-warning-light text-admin-warning border-amber-200';
        }

        if (type === 'manual_adjustment') {
            return 'bg-admin-primary-light text-admin-primary border-admin-neutral-200';
        }

        return 'bg-admin-neutral-100 text-admin-neutral-700 border-admin-neutral-200';
    }

    function inventoryUsageNormalizeUnit(unit) {
        const value = inventoryUsageText(unit).toLowerCase();
        if (!value) {
            return '';
        }

        const aliases = {
            pc: 'pieces',
            pcs: 'pieces',
            piece: 'pieces',
            pieces: 'pieces',
            pack: 'packs',
            packs: 'packs',
        };

        return aliases[value] || value;
    }

    function inventoryUsageRequiresWholeQuantity(unit) {
        const normalized = inventoryUsageNormalizeUnit(unit);
        return normalized === 'pieces' || normalized === 'packs';
    }

    function inventoryUsageFormatNumber(value, unit = '') {
        const number = Number(value);
        if (!Number.isFinite(number)) {
            return '0';
        }

        const requiresWholeQuantity = inventoryUsageRequiresWholeQuantity(unit);
        const normalizedValue = requiresWholeQuantity ? Math.round(number) : number;

        if (requiresWholeQuantity || Number.isInteger(normalizedValue)) {
            return normalizedValue.toLocaleString();
        }

        return normalizedValue.toLocaleString(undefined, {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
    }

    function inventoryUsageGetRelativeTimeLabel(date) {
        const elapsed = date.getTime() - Date.now();
        const units = [
            ['year', 1000 * 60 * 60 * 24 * 365],
            ['month', 1000 * 60 * 60 * 24 * 30],
            ['week', 1000 * 60 * 60 * 24 * 7],
            ['day', 1000 * 60 * 60 * 24],
            ['hour', 1000 * 60 * 60],
            ['minute', 1000 * 60],
        ];
        const formatter = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });

        for (const [unit, ms] of units) {
            if (Math.abs(elapsed) >= ms || unit === 'minute') {
                return formatter.format(Math.round(elapsed / ms), unit);
            }
        }

        return 'just now';
    }

    function inventoryUsageFormatDate(dateString) {
        const parsed = new Date(dateString || '');
        if (Number.isNaN(parsed.getTime())) {
            return { full: 'Unknown date', relative: 'Unknown', ts: 0 };
        }

        return {
            full: parsed.toLocaleString(),
            relative: inventoryUsageGetRelativeTimeLabel(parsed),
            ts: parsed.getTime(),
        };
    }

    function updateInventoryUsageCounters(total) {
        const totalEl = document.getElementById('inventoryUsageTotalCount');
        if (totalEl) {
            totalEl.textContent = String(total);
        }
    }

    function updateInventoryUsageSortIndicators() {
        const icon = document.getElementById('inventoryUsageSortIconDate');
        if (!icon) return;

        icon.classList.remove('fa-arrow-up', 'fa-arrow-down', 'text-admin-primary', 'text-admin-neutral-400');
        icon.classList.add(currentInventoryUsageSortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down', 'text-admin-primary');
    }

    function getInventoryUsageTotalPages(totalItems) {
        return Math.max(1, Math.ceil(totalItems / inventoryUsagePerPage));
    }

    function buildInventoryUsagePageItems(totalPages, currentPage) {
        const isSmallScreen = typeof window !== 'undefined' && window.matchMedia('(max-width: 639px)').matches;

        if (isSmallScreen) {
            if (totalPages <= 5) {
                return Array.from({ length: totalPages }, (_, index) => index + 1);
            }

            if (currentPage <= 3) {
                return [1, 2, 3, '...', totalPages - 1, totalPages];
            }

            if (currentPage >= totalPages - 2) {
                return [1, 2, '...', totalPages - 2, totalPages - 1, totalPages];
            }

            return [1, '...', currentPage - 1, currentPage, currentPage + 1, '...', totalPages];
        }

        if (totalPages <= 7) {
            return Array.from({ length: totalPages }, (_, index) => index + 1);
        }

        const items = [1];
        let start = Math.max(2, currentPage - 1);
        let end = Math.min(totalPages - 1, currentPage + 1);

        if (currentPage <= 3) {
            start = 2;
            end = 4;
        } else if (currentPage >= totalPages - 2) {
            start = totalPages - 3;
            end = totalPages - 1;
        }

        if (start > 2) {
            items.push('...');
        }

        for (let page = start; page <= end; page += 1) {
            items.push(page);
        }

        if (end < totalPages - 1) {
            items.push('...');
        }

        items.push(totalPages);
        return items;
    }

    function renderInventoryUsagePagination(totalItems) {
        const wrapper = document.getElementById('inventoryUsagePagination');
        const info = document.getElementById('inventoryUsagePaginationInfo');
        const nav = document.getElementById('inventoryUsagePaginationNav');

        if (!wrapper || !info || !nav) return;

        if (totalItems <= 0) {
            wrapper.classList.add('hidden');
            wrapper.classList.remove('flex');
            info.textContent = '';
            nav.innerHTML = '';
            return;
        }

        const totalPages = getInventoryUsageTotalPages(totalItems);
        if (currentInventoryUsagePage > totalPages) {
            currentInventoryUsagePage = totalPages;
        }

        const firstItem = (currentInventoryUsagePage - 1) * inventoryUsagePerPage + 1;
        const lastItem = Math.min(firstItem + inventoryUsagePerPage - 1, totalItems);
        info.innerHTML = `Showing <span class="font-semibold text-admin-neutral-700">${firstItem}</span> to <span class="font-semibold text-admin-neutral-700">${lastItem}</span> of <span class="font-semibold text-admin-neutral-700">${totalItems}</span> results`;

        const buttonBaseClass = 'inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg px-2.5 text-[11px] font-semibold sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs';
        const disabledClass = `${buttonBaseClass} border border-admin-neutral-200 bg-admin-neutral-50 text-admin-neutral-400 cursor-not-allowed`;
        const defaultClass = `${buttonBaseClass} border border-admin-neutral-200 bg-white text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50`;
        const activeClass = `${buttonBaseClass} border border-admin-primary bg-admin-primary text-white shadow-sm`;

        let navHtml = '';
        if (currentInventoryUsagePage === 1) {
            navHtml += `<span class="${disabledClass}" aria-hidden="true">&lt;</span>`;
        } else {
            navHtml += `<button type="button" class="${defaultClass}" aria-label="Previous page" data-page="${currentInventoryUsagePage - 1}">&lt;</button>`;
        }

        const pageItems = buildInventoryUsagePageItems(totalPages, currentInventoryUsagePage);
        pageItems.forEach((item) => {
            if (item === '...') {
                navHtml += '<span class="inline-flex h-8 min-w-[32px] items-center justify-center px-2.5 text-[11px] font-semibold text-admin-neutral-400 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs">...</span>';
                return;
            }

            if (item === currentInventoryUsagePage) {
                navHtml += `<span class="${activeClass}" aria-current="page">${item}</span>`;
                return;
            }

            navHtml += `<button type="button" class="${defaultClass}" aria-label="Go to page ${item}" data-page="${item}">${item}</button>`;
        });

        if (currentInventoryUsagePage >= totalPages) {
            navHtml += `<span class="${disabledClass}" aria-hidden="true">&gt;</span>`;
        } else {
            navHtml += `<button type="button" class="${defaultClass}" aria-label="Next page" data-page="${currentInventoryUsagePage + 1}">&gt;</button>`;
        }

        nav.innerHTML = navHtml;
        wrapper.classList.remove('hidden');
        wrapper.classList.add('flex');
    }

    function setInventoryUsageLoadingState() {
        const tbody = document.getElementById('inventoryUsageTableBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-12 px-4 text-center text-admin-neutral-500">
                    <div class="inline-flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                        <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                        <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                    </div>
                    <p class="mt-3 text-sm">Loading inventory usage logs...</p>
                </td>
            </tr>
        `;
        renderInventoryUsagePagination(0);
    }

    function setInventoryUsageErrorState() {
        const tbody = document.getElementById('inventoryUsageTableBody');
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-12 px-4 text-center">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-admin-danger-light text-admin-danger">
                        <i class="fas fa-triangle-exclamation text-base" aria-hidden="true"></i>
                    </div>
                    <p class="mt-3 font-semibold text-admin-neutral-900">Could not load usage logs</p>
                    <p class="text-sm text-admin-neutral-500">Please try again in a moment.</p>
                </td>
            </tr>
        `;
        renderInventoryUsagePagination(0);
    }

    function renderInventoryUsageTable(logs) {
        const tbody = document.getElementById('inventoryUsageTableBody');
        if (!tbody) return;

        const sortedLogs = [...logs].sort((a, b) => {
            const dateA = inventoryUsageFormatDate(a.created_at).ts;
            const dateB = inventoryUsageFormatDate(b.created_at).ts;

            if (dateA === dateB) {
                return 0;
            }

            if (currentInventoryUsageSortDirection === 'asc') {
                return dateA > dateB ? 1 : -1;
            }

            return dateA < dateB ? 1 : -1;
        });

        const totalItems = sortedLogs.length;
        renderInventoryUsagePagination(totalItems);

        if (totalItems === 0) {
            const hasFilters = Boolean(
                inventoryUsageText(document.getElementById('inventoryUsageSearchInput')?.value).length ||
                inventoryUsageText(document.getElementById('inventoryUsageTypeFilter')?.value).length ||
                inventoryUsageText(document.getElementById('inventoryUsageDateFrom')?.value).length ||
                inventoryUsageText(document.getElementById('inventoryUsageDateTo')?.value).length
            );

            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-12 px-4 text-center">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-admin-neutral-100 text-admin-neutral-400">
                            <i class="fas ${hasFilters ? 'fa-filter' : 'fa-file-lines'} text-base" aria-hidden="true"></i>
                        </div>
                        <p class="mt-3 font-semibold text-admin-neutral-900">${hasFilters ? 'No matching logs' : 'No usage logs found'}</p>
                        <p class="text-sm text-admin-neutral-500">${hasFilters ? 'Try changing your search or filters.' : 'Logs will appear when inventory quantities are adjusted.'}</p>
                    </td>
                </tr>
            `;
            return;
        }

        const totalPages = getInventoryUsageTotalPages(totalItems);
        if (currentInventoryUsagePage > totalPages) {
            currentInventoryUsagePage = totalPages;
        }

        const startIndex = (currentInventoryUsagePage - 1) * inventoryUsagePerPage;
        const pagedLogs = sortedLogs.slice(startIndex, startIndex + inventoryUsagePerPage);

        let rowsHtml = '';
        pagedLogs.forEach((log) => {
            const itemName = inventoryUsageText(log.item_name || log.inventory_item?.name || 'Unknown item');
            const typeValue = inventoryUsageText(log.type);
            const typeLabel = inventoryUsageTypeLabel(typeValue);
            const typeClass = inventoryUsageTypeBadgeClass(typeValue);
            const stockUnit = inventoryUsageText(log.inventory_item?.unit || '');
            const quantityChange = Number(log.quantity_change ?? 0);
            const quantitySign = quantityChange > 0 ? '+' : '';
            const quantityClass = quantityChange > 0
                ? 'text-admin-success'
                : (quantityChange < 0 ? 'text-admin-danger' : 'text-admin-neutral-600');
            const newBalance = log.new_balance === null || typeof log.new_balance === 'undefined'
                ? 'N/A'
                : inventoryUsageFormatNumber(log.new_balance, stockUnit);
            const reference = log.reservation_id ? `Reservation #${log.reservation_id}` : 'N/A';
            const performedBy = inventoryUsageText(log.user?.name || 'System');
            const dateInfo = inventoryUsageFormatDate(log.created_at);

            rowsHtml += `
                <tr class="border-b border-admin-neutral-100 last:border-b-0 hover:bg-admin-neutral-50 transition-colors duration-admin">
                    <td class="py-3 px-4 align-top whitespace-nowrap">
                        <p class="text-admin-neutral-800">${inventoryUsageEscapeHtml(dateInfo.full)}</p>
                        <p class="text-xs text-admin-neutral-500">${inventoryUsageEscapeHtml(dateInfo.relative)}</p>
                    </td>
                    <td class="py-3 px-4 align-top min-w-0">
                        <p class="font-semibold text-admin-neutral-900 whitespace-normal break-words">${inventoryUsageEscapeHtml(itemName)}</p>
                    </td>
                    <td class="py-3 px-4 align-top whitespace-nowrap">
                        <span class="inline-flex max-w-full items-center rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-normal break-words ${typeClass}">
                            ${inventoryUsageEscapeHtml(typeLabel)}
                        </span>
                    </td>
                    <td class="py-3 px-4 align-top whitespace-nowrap">
                        <span class="font-semibold ${quantityClass}">
                            ${inventoryUsageEscapeHtml(quantitySign + inventoryUsageFormatNumber(quantityChange, stockUnit))}
                        </span>
                    </td>
                    <td class="py-3 px-4 align-top whitespace-nowrap text-admin-neutral-700">
                        ${inventoryUsageEscapeHtml(newBalance)}
                    </td>
                    <td class="py-3 px-4 align-top min-w-0 text-admin-neutral-700">
                        <p class="whitespace-normal break-words">${inventoryUsageEscapeHtml(reference)}</p>
                    </td>
                    <td class="py-3 px-4 align-top min-w-0 text-admin-neutral-700">
                        <p class="whitespace-normal break-words">${inventoryUsageEscapeHtml(performedBy)}</p>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = rowsHtml;
    }

    function applyInventoryUsageFilters(resetPage = false) {
        const searchInput = document.getElementById('inventoryUsageSearchInput');
        const typeFilter = document.getElementById('inventoryUsageTypeFilter');
        const dateFromInput = document.getElementById('inventoryUsageDateFrom');
        const dateToInput = document.getElementById('inventoryUsageDateTo');
        const clearButton = document.getElementById('inventoryUsageClearSearch');

        const query = inventoryUsageText(searchInput?.value).toLowerCase();
        const typeValue = inventoryUsageText(typeFilter?.value);
        const dateFromValue = inventoryUsageText(dateFromInput?.value);
        const dateToValue = inventoryUsageText(dateToInput?.value);
        const fromDate = dateFromValue ? new Date(`${dateFromValue}T00:00:00`) : null;
        const toDate = dateToValue ? new Date(`${dateToValue}T23:59:59.999`) : null;

        filteredInventoryUsageLogs = allInventoryUsageLogs.filter((log) => {
            const itemName = inventoryUsageText(log.item_name || log.inventory_item?.name || '');
            const typeLabel = inventoryUsageTypeLabel(log.type);
            const reference = log.reservation_id ? `reservation #${log.reservation_id}` : '';
            const performedBy = inventoryUsageText(log.user?.name || 'System');
            const dateText = log.created_at ? new Date(log.created_at).toLocaleString() : '';
            const haystack = `${itemName} ${typeLabel} ${reference} ${performedBy} ${dateText}`.toLowerCase();

            const logDate = log.created_at ? new Date(log.created_at) : null;
            const matchesQuery = !query || haystack.includes(query);
            const matchesType = !typeValue || inventoryUsageText(log.type) === typeValue;
            const matchesFrom = !fromDate || (logDate && !Number.isNaN(logDate.getTime()) && logDate >= fromDate);
            const matchesTo = !toDate || (logDate && !Number.isNaN(logDate.getTime()) && logDate <= toDate);

            return matchesQuery && matchesType && matchesFrom && matchesTo;
        });

        if (resetPage) {
            currentInventoryUsagePage = 1;
        }

        renderInventoryUsageTable(filteredInventoryUsageLogs);
        updateInventoryUsageCounters(allInventoryUsageLogs.length);

        if (clearButton) {
            clearButton.classList.toggle('hidden', !query.length);
        }
    }

    async function loadInventoryUsageLogs() {
        setInventoryUsageLoadingState();
        updateInventoryUsageCounters(0);
        filteredInventoryUsageLogs = [];
        currentInventoryUsagePage = 1;

        try {
            const response = await fetch(inventoryUsageLogsEndpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const payload = await response.json();
            allInventoryUsageLogs = Array.isArray(payload?.logs) ? payload.logs : [];
            applyInventoryUsageFilters(true);
            updateInventoryUsageSortIndicators();
        } catch (error) {
            setInventoryUsageErrorState();
            updateInventoryUsageCounters(0);
            console.error('Error fetching inventory usage logs:', error);
        }
    }

    async function openInventoryUsageLogsModal() {
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventoryUsageLogs' }));
        await loadInventoryUsageLogs();
    }

    function initInventoryUsageLogsControls() {
        const openButton = document.getElementById('inventoryUsageLogsBtn');
        const searchInput = document.getElementById('inventoryUsageSearchInput');
        const typeFilter = document.getElementById('inventoryUsageTypeFilter');
        const dateFromInput = document.getElementById('inventoryUsageDateFrom');
        const dateToInput = document.getElementById('inventoryUsageDateTo');
        const clearButton = document.getElementById('inventoryUsageClearSearch');
        const resetButton = document.getElementById('inventoryUsageResetFilters');
        const sortByDateButton = document.getElementById('inventoryUsageSortByDateBtn');
        const paginationNav = document.getElementById('inventoryUsagePaginationNav');

        if (openButton && !openButton.dataset.bound) {
            openButton.addEventListener('click', openInventoryUsageLogsModal);
            openButton.dataset.bound = 'true';
        }

        if (searchInput && !searchInput.dataset.bound) {
            searchInput.addEventListener('input', () => applyInventoryUsageFilters(true));
            searchInput.dataset.bound = 'true';
        }

        if (typeFilter && !typeFilter.dataset.bound) {
            typeFilter.addEventListener('change', () => applyInventoryUsageFilters(true));
            typeFilter.dataset.bound = 'true';
        }

        if (dateFromInput && !dateFromInput.dataset.bound) {
            dateFromInput.addEventListener('change', () => applyInventoryUsageFilters(true));
            dateFromInput.dataset.bound = 'true';
        }

        if (dateToInput && !dateToInput.dataset.bound) {
            dateToInput.addEventListener('change', () => applyInventoryUsageFilters(true));
            dateToInput.dataset.bound = 'true';
        }

        if (clearButton && !clearButton.dataset.bound) {
            clearButton.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                }
                applyInventoryUsageFilters(true);
            });
            clearButton.dataset.bound = 'true';
        }

        if (resetButton && !resetButton.dataset.bound) {
            resetButton.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                if (typeFilter) typeFilter.value = '';
                if (dateFromInput) dateFromInput.value = '';
                if (dateToInput) dateToInput.value = '';
                applyInventoryUsageFilters(true);
            });
            resetButton.dataset.bound = 'true';
        }

        if (sortByDateButton && !sortByDateButton.dataset.bound) {
            sortByDateButton.addEventListener('click', () => {
                currentInventoryUsageSortDirection = currentInventoryUsageSortDirection === 'asc' ? 'desc' : 'asc';
                updateInventoryUsageSortIndicators();
                applyInventoryUsageFilters();
            });
            sortByDateButton.dataset.bound = 'true';
        }

        if (paginationNav && !paginationNav.dataset.bound) {
            paginationNav.addEventListener('click', (event) => {
                const button = event.target.closest('button[data-page]');
                if (!button) {
                    return;
                }

                const nextPage = Number(button.dataset.page || 1);
                const totalPages = getInventoryUsageTotalPages(filteredInventoryUsageLogs.length);
                const normalizedPage = Math.min(Math.max(1, nextPage), totalPages);

                if (normalizedPage === currentInventoryUsagePage) {
                    return;
                }

                currentInventoryUsagePage = normalizedPage;
                renderInventoryUsageTable(filteredInventoryUsageLogs);
            });
            paginationNav.dataset.bound = 'true';
        }

        if (!window.__inventoryUsagePaginationResizeBound) {
            const mediaQuery = window.matchMedia('(max-width: 639px)');
            let wasSmallScreen = mediaQuery.matches;
            window.addEventListener('resize', () => {
                if (mediaQuery.matches === wasSmallScreen) {
                    return;
                }

                wasSmallScreen = mediaQuery.matches;
                renderInventoryUsageTable(filteredInventoryUsageLogs);
            });
            window.__inventoryUsagePaginationResizeBound = 'true';
        }

        if (typeof enhanceAdminSelects === 'function') {
            enhanceAdminSelects(document);
        }

        updateInventoryUsageSortIndicators();
    }

    function initInventoryFloatingActions() {
        const host = document.getElementById('inventoryTableHost');
        const scrollArea = document.getElementById('inventoryTableScroll');
        const actions = document.getElementById('inventoryFloatingActions');
        const editBtn = document.getElementById('inventoryFloatingEditBtn');
        const deleteBtn = document.getElementById('inventoryFloatingDeleteBtn');
        if (!host || !scrollArea || !actions || !editBtn || !deleteBtn) return;
        if (host.dataset.floatingActionsBound === 'true') return;
        host.dataset.floatingActionsBound = 'true';

        let activeRow = null;

        const clearActionData = () => {
            editBtn.dataset.item = '';
            deleteBtn.dataset.item = '';
        };

        const hideActions = () => {
            activeRow = null;
            clearActionData();
            actions.classList.remove('is-visible');
            actions.setAttribute('aria-hidden', 'true');
        };

        const updateActionsPosition = () => {
            if (!activeRow || !scrollArea.contains(activeRow)) return;

            const hostRect = host.getBoundingClientRect();
            const scrollRect = scrollArea.getBoundingClientRect();
            const rowRect = activeRow.getBoundingClientRect();
            if (rowRect.bottom <= scrollRect.top || rowRect.top >= scrollRect.bottom) {
                hideActions();
                return;
            }

            const actionsHeight = actions.offsetHeight || 32;
            const proposedTop = rowRect.top - hostRect.top + ((rowRect.height - actionsHeight) / 2);
            const minTop = scrollRect.top - hostRect.top + 6;
            const maxTop = scrollRect.bottom - hostRect.top - actionsHeight - 6;
            const clampedTop = Math.max(minTop, Math.min(proposedTop, maxTop));
            actions.style.top = `${clampedTop}px`;
        };

        const showForRow = (row) => {
            if (!row) return;
            const itemPayload = row.dataset.item;
            if (!itemPayload) {
                hideActions();
                return;
            }

            activeRow = row;
            editBtn.dataset.item = itemPayload;
            deleteBtn.dataset.item = itemPayload;
            actions.classList.add('is-visible');
            actions.setAttribute('aria-hidden', 'false');
            updateActionsPosition();
        };

        scrollArea.addEventListener('pointermove', (event) => {
            const row = event.target.closest('tr[data-item]');
            if (row && scrollArea.contains(row)) {
                if (activeRow !== row) {
                    showForRow(row);
                } else {
                    updateActionsPosition();
                }
                return;
            }

            if (!actions.matches(':hover')) {
                hideActions();
            }
        });

        host.addEventListener('mouseleave', hideActions);

        scrollArea.addEventListener('scroll', () => {
            if (activeRow) {
                updateActionsPosition();
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            if (activeRow) {
                updateActionsPosition();
            }
        });

        scrollArea.addEventListener('focusin', (event) => {
            const row = event.target.closest('tr[data-item]');
            if (row) {
                showForRow(row);
            }
        });
    }

    const inventoryFiltersForm = document.getElementById('inventoryFiltersForm');
    const inventorySearchInput = document.getElementById('inventorySearchInput');
    const inventoryClearSearch = document.getElementById('inventoryClearSearch');
    const inventoryCategoryFilter = document.getElementById('inventoryCategoryFilter');

    if (inventoryFiltersForm && inventorySearchInput && !inventorySearchInput.dataset.bound) {
        let submitTimer = null;
        const submitInventoryFilters = () => {
            const floatingActions = document.getElementById('inventoryFloatingActions');
            if (floatingActions) {
                floatingActions.classList.remove('is-visible');
                floatingActions.setAttribute('aria-hidden', 'true');
            }

            inventoryFiltersForm.requestSubmit();
        };

        inventorySearchInput.addEventListener('input', () => {
            window.clearTimeout(submitTimer);
            submitTimer = window.setTimeout(submitInventoryFilters, 300);
        });

        inventorySearchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                window.clearTimeout(submitTimer);
                submitInventoryFilters();
            }
        });

        inventoryFiltersForm.addEventListener('submit', () => {
            window.clearTimeout(submitTimer);
        });

        inventorySearchInput.dataset.bound = 'true';
    }

    if (inventoryFiltersForm && inventoryClearSearch && inventorySearchInput && !inventoryClearSearch.dataset.bound) {
        inventoryClearSearch.addEventListener('click', () => {
            inventorySearchInput.value = '';
            inventoryFiltersForm.requestSubmit();
        });

        inventoryClearSearch.dataset.bound = 'true';
    }

    if (inventoryFiltersForm && inventoryCategoryFilter && !inventoryCategoryFilter.dataset.bound) {
        inventoryCategoryFilter.addEventListener('change', () => {
            inventoryFiltersForm.requestSubmit();
        });

        inventoryCategoryFilter.dataset.bound = 'true';
    }

    initInventoryFloatingActions();
    initInventoryUsageLogsControls();

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function emitAdminToast(message, type = 'success') {
        if (window.showAdminToast && typeof window.showAdminToast === 'function') {
            window.showAdminToast(message, type);
            return;
        }
        // Fallback: dispatch event that the layout's toast container listens for
        window.dispatchEvent(new CustomEvent('admin-toast', { detail: { type: type, message: message } }));
    }

    function formatAutoDeductQty(value, unit) {
        const numeric = Number(value);
        if (!Number.isFinite(numeric)) {
            return '0';
        }

        const normalizedUnit = inventoryUsageNormalizeUnit(unit);
        const requiresWholeQuantity = normalizedUnit === 'pieces' || normalizedUnit === 'packs';
        const normalizedValue = requiresWholeQuantity ? Math.round(numeric) : numeric;

        if (requiresWholeQuantity || Number.isInteger(normalizedValue)) {
            return normalizedValue.toLocaleString();
        }

        return normalizedValue.toLocaleString(undefined, {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
    }

    function openInventoryAutoDeductModal(summary) {
        const itemEl = document.getElementById('inventoryAutoDeductedItem');
        const qtyEl = document.getElementById('inventoryAutoDeductedQty');
        const countEl = document.getElementById('inventoryAutoDeductedCount');
        if (!itemEl || !qtyEl || !countEl) {
            return;
        }

        const itemName = inventoryUsageText(summary?.item_name || summary?.item?.name || 'Inventory item');
        const unit = inventoryUsageText(summary?.unit || summary?.item?.unit || '');
        const deductedTotal = Number(summary?.deducted_total ?? summary?.auto_deducted_quantity ?? 0);
        const deductionCount = Number(summary?.deduction_count ?? summary?.auto_deducted_reservations ?? 0);
        if (!Number.isFinite(deductedTotal) || deductedTotal <= 0) {
            return;
        }

        itemEl.textContent = itemName;
        qtyEl.textContent = `${formatAutoDeductQty(deductedTotal, unit)}${unit ? ` ${unit}` : ''}`;
        countEl.textContent = Number.isFinite(deductionCount) ? String(Math.max(0, Math.round(deductionCount))) : '0';

        window.__inventoryAutoDeductRefreshPending = true;
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-restock-auto-deduct' }));
    }

    if (!window.__inventoryAutoDeductVisibilityBound) {
        window.addEventListener('admin-modal-visibility', (event) => {
            const modalName = event?.detail?.name;
            const isOpen = Boolean(event?.detail?.open);
            if (modalName !== 'inventory-restock-auto-deduct' || isOpen) {
                return;
            }

            if (!window.__inventoryAutoDeductRefreshPending) {
                return;
            }

            window.__inventoryAutoDeductRefreshPending = false;
            setTimeout(() => { window.location.reload(); }, 120);
        });
        window.__inventoryAutoDeductVisibilityBound = true;
    }

    if (inventoryAutoDeductedFlash && Number(inventoryAutoDeductedFlash.deducted_total ?? 0) > 0) {
        openInventoryAutoDeductModal(inventoryAutoDeductedFlash);
    }

    async function submitForm(form) {
        const formData = new FormData(form);
        const token = getCsrfToken();
        try {
            const res = await fetch(form.action, {
                method: form.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!res.ok) {
                let err = null;
                try { err = await res.json(); } catch (e) { /* not JSON */ }
                console.error('Request failed', err || res.statusText);
                if (res.status === 404) {
                    emitAdminToast('Item not found or already deleted', 'error');
                } else {
                    emitAdminToast((err && err.message) ? err.message : 'Request failed', 'error');
                }
                return null;
            }

            let data = null;
            try { data = await res.json(); } catch (e) { /* no JSON, ignore */ }
            return data;

        } catch (e) {
            console.error('Network error', e);
            emitAdminToast(e.message || 'Network error', 'error');
            return null;
        }
    }

    function startLoading(form, event, fallbackText) {
        if (!window.cmsActionButtons || typeof window.cmsActionButtons.startFormSubmit !== 'function') {
            return true;
        }
        return window.cmsActionButtons.startFormSubmit(form, event ? event.submitter : null, fallbackText);
    }

    function stopLoading(form) {
        if (!window.cmsActionButtons || typeof window.cmsActionButtons.resetForm !== 'function') {
            return;
        }
        window.cmsActionButtons.resetForm(form);
    }

    // Create
    const createForm = document.getElementById('createInventoryForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            if (!startLoading(createForm, e, 'Saving Item...')) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            const result = await submitForm(createForm);
            if (result !== null) {
                createForm.reset();
                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-create-success' }));
                window.dispatchEvent(new CustomEvent('refresh-admin-inventory-alerts'));
                setTimeout(function(){ location.reload(); }, 900);
            } else {
                stopLoading(createForm);
            }
        });
    }

    // Edit
    const editForm = document.getElementById('editInventoryForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            if (!startLoading(editForm, e, 'Updating Item...')) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            const result = await submitForm(editForm);
            if (result !== null) {
                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('refresh-admin-inventory-alerts'));
                stopLoading(editForm);

                const deductedTotal = Number(result?.auto_deducted_quantity ?? 0);
                if (Number.isFinite(deductedTotal) && deductedTotal > 0) {
                    openInventoryAutoDeductModal({
                        item_name: result?.item?.name || '',
                        unit: result?.item?.unit || '',
                        deducted_total: deductedTotal,
                        deduction_count: Number(result?.auto_deducted_reservations ?? 0),
                    });
                } else {
                    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-update-success' }));
                    setTimeout(function(){ location.reload(); }, 700);
                }
            } else {
                stopLoading(editForm);
            }
        });
    }

    // Delete (in-place)
    const deleteForm = document.getElementById('deleteInventoryForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function(e) {
            if (!startLoading(deleteForm, e, 'Deleting Item...')) {
                e.preventDefault();
                return;
            }
            e.preventDefault();
            const result = await submitForm(deleteForm);
            if (result !== null) {
                const deletedId = deleteForm.dataset.id || (function(){
                    const m = (deleteForm.action||'').match(/\/admin\/inventory\/(\d+)/);
                    return m ? m[1] : null;
                })();
                if (deletedId) {
                    const rows = document.querySelectorAll('tr[data-item-id]');
                    for (const row of rows) {
                        if (String(row.dataset.itemId) === String(deletedId)) {
                            row.remove();
                            break;
                        }
                    }
                }

                const floatingActions = document.getElementById('inventoryFloatingActions');
                const floatingEdit = document.getElementById('inventoryFloatingEditBtn');
                const floatingDelete = document.getElementById('inventoryFloatingDeleteBtn');
                if (floatingActions) {
                    floatingActions.classList.remove('is-visible');
                    floatingActions.setAttribute('aria-hidden', 'true');
                }
                if (floatingEdit) floatingEdit.dataset.item = '';
                if (floatingDelete) floatingDelete.dataset.item = '';

                stopLoading(deleteForm);
                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-delete-success' }));
                window.dispatchEvent(new CustomEvent('refresh-admin-inventory-alerts'));
            } else {
                stopLoading(deleteForm);
            }
        });
    }
});
</script>

@endsection
