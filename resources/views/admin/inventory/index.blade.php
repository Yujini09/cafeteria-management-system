@extends('layouts.sidebar')
@section('page-title', 'Inventory Management')

@section('content')
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
</style>

<div x-data="{ 
    showCreateModal: false, 
    showEditModal: false, 
    showDeleteModal: false, 
    editingItem: null, 
    deletingItem: null, 
    updateRoute: '{{ route('admin.inventory.update', ':id') }}',
    deleteRoute: '{{ route('admin.inventory.destroy', ':id') }}'
}"
    x-init="window.addEventListener('close-inventory-modals', function() { showCreateModal = false; showEditModal = false; showDeleteModal = false; editingItem = null; deletingItem = null; })"
    x-effect="document.body.classList.toggle('overflow-hidden', showCreateModal || showEditModal || showDeleteModal)"
    @keydown.escape.window="showCreateModal = false; showEditModal = false; showDeleteModal = false; editingItem = null; deletingItem = null">

    <x-success-modal name="inventory-create-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item added successfully.</p>
    </x-success-modal>
    <x-success-modal name="inventory-update-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item updated successfully.</p>
    </x-success-modal>
    <x-success-modal name="inventory-delete-success" title="Deleted" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
        <p class="text-sm text-admin-neutral-600">Inventory item deleted successfully.</p>
    </x-success-modal>
    
    <div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full overflow-hidden flex flex-col">
        <div class="page-header items-start">
            <div class="header-content">
                <div class="header-icon">
                    <x-admin.ui.icon name="fa-boxes-stacked" style="fas" class="text-white w-6 h-6" />
                </div>
                <div class="header-text">
                    <h1 class="header-title">Inventory Management</h1>
                    <p class="header-subtitle">Manage and track your inventory items and quantities</p>
                </div>
            </div>
            <div class="header-actions w-full md:w-auto flex flex-col items-end gap-3">
                <div class="relative w-full sm:w-64 md:w-72">
                    <input type="text"
                           inputmode="search"
                           autocomplete="off"
                           id="searchInput"
                           placeholder="Search inventory items..."
                           class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                           oninput="filterTable(this.value)"
                           aria-label="Search inventory items">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600" style="display: none;">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                <x-admin.ui.icon name="fa-boxes-stacked" size="xs" />
                Total Items: {{ $items->total() }}
            </span>
        </div>

        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-col gap-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <label for="category" class="text-sm font-semibold text-admin-neutral-700">Filter by Category</label>
                        <div class="w-full sm:w-64">
                            <select name="category" id="category" onchange="this.form.submit()" class="admin-select w-full" data-admin-select="true">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex w-full sm:w-auto sm:justify-end">
                        <x-admin.ui.button.primary type="button" @click="showCreateModal = true">
                            <x-admin.ui.icon name="fa-plus" style="fas" size="sm" />
                            Add Item
                        </x-admin.ui.button.primary>
                    </div>
                </div>
                @if($sort)
                    <input type="hidden" name="sort" value="{{ $sort }}">
                @endif
                @if($direction)
                    <input type="hidden" name="direction" value="{{ $direction }}">
                @endif
            </form>
        </div>

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
                            <a href="?sort=name" class="hover:text-admin-neutral-700 transition-colors duration-200">Item Name</a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <a href="?sort=qty" class="hover:text-admin-neutral-700 transition-colors duration-200">Quantity</a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Unit</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <a href="?sort=expiry_date" class="hover:text-admin-neutral-700 transition-colors duration-200">Expiry Date</a>
                        </th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Category</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Last Updated</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        @php
                            $itemPayload = json_encode([
                                'id' => $item->id,
                                'name' => $item->name,
                                'category' => $item->category,
                                'qty' => $item->qty,
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
                                    {{ $item->qty }}
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
                                    <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No inventory items found</p>
                                    <p class="text-sm text-admin-neutral-500">Start by adding your first item</p>
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
                    <input type="number" name="qty" id="create_qty" min="1" required class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                </div>

                <div>
                    <label for="create_unit" class="block text-sm font-medium text-admin-neutral-700">Unit</label>
                    <select name="unit" id="create_unit" required class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a unit</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Packs">Packs</option>
                        <option value="Kgs">Kgs</option>
                        <option value="Liters">Liters</option>
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
                    <input type="number" name="qty" id="edit_qty" min="1" required x-bind:value="editingItem ? editingItem.qty : ''" class="w-full rounded-admin border px-admin-input py-2.5 text-sm text-admin-neutral-700 transition-colors duration-admin focus:outline-none focus:ring-2 border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20">
                </div>

                <div>
                    <label for="edit_unit" class="block text-sm font-medium text-admin-neutral-700">Unit</label>
                    <select name="unit" id="edit_unit" required x-bind:value="editingItem ? editingItem.unit : ''" class="admin-select w-full" data-admin-select="true">
                        <option value="">Select a unit</option>
                        <option value="Pieces">Pieces</option>
                        <option value="Packs">Packs</option>
                        <option value="Kgs">Kgs</option>
                        <option value="Liters">Liters</option>
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

    initInventoryFloatingActions();

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
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-update-success' }));
                setTimeout(function(){ location.reload(); }, 700);
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
            } else {
                stopLoading(deleteForm);
            }
        });
    }
});
</script>

@endsection
