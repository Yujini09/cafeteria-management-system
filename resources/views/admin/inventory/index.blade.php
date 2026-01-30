@extends('layouts.sidebar')
@section('page-title', 'Inventory Management')

@section('content')
<style>
/* Modern Design Variables */
:root {
    --primary: #00462E;
    --primary-light: #057C3C;
    --accent: #FF6B35;
    --neutral-50: #fafafa;
    --neutral-100: #f5f5f5;
    --neutral-200: #e5e5e5;
    --neutral-300: #d4d4d4;
    --neutral-400: #a3a3a3;
    --neutral-500: #737373;
    --neutral-600: #525252;
    --neutral-700: #404040;
    --neutral-800: #262626;
    --neutral-900: #171717;
}

/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
}

/* Modern Table Styles */
.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
}

.modern-table th {
    background: var(--neutral-50);
    font-weight: 600;
    color: var(--neutral-700);
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--neutral-200);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: sticky;
    top: 0;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--neutral-100);
    transition: all 0.2s ease;
}

.modern-table tr:last-child td {
    border-bottom: none;
}

.modern-table tr:hover td {
    background: var(--neutral-50);
}

/* Custom Scrollbar */
.modern-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.modern-scrollbar::-webkit-scrollbar-track {
    background: var(--neutral-100);
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--primary-light);
}

/* Button Styles */
.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 70, 46, 0.2);
}

.btn-secondary {
    background: var(--neutral-100);
    color: var(--neutral-700);
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: var(--neutral-200);
}

/* Action Buttons */
.action-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    text-decoration: none;
    border: 1px solid transparent;
}

.action-btn-edit {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.2);
}

.action-btn-edit:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-1px);
}

.action-btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-delete:hover {
    background: rgba(239, 68, 68, 0.2);
    transform: translateY(-1px);
}

/* Quantity Badge */
.quantity-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.quantity-low {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.quantity-medium {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.quantity-good {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
    border: 1px solid rgba(34, 197, 94, 0.2);
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: var(--neutral-400);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--neutral-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
    margin: 0;
}

.header-subtitle {
    color: var(--neutral-500);
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    margin-left: auto;
    flex-direction: column;
    align-items: flex-end;
}

/* Filter Section - Copied from Reservations */
.filter-section {
    background: var(--neutral-50);
    padding: 1.25rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
}

.filter-label {
    font-weight: 600;
    color: var(--neutral-700);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.filter-label-inline {
    margin-bottom: 0;
}

.filter-row {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

@media (min-width: 640px) {
    .filter-row {
        flex-direction: row;
        align-items: center;
    }
}

.select-with-arrows {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 100%;
}

.select-with-arrows .filter-select {
    appearance: none;
    padding-right: 2.75rem;
}

.select-arrows {
    position: absolute;
    right: 0.75rem;
    pointer-events: none;
    color: var(--neutral-500);
}

.select-arrows svg {
    width: 16px;
    height: 16px;
}

.filter-select {
    background: white;
    border: 1px solid var(--neutral-300);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

/* Form Styles */
.modern-form {
    background: var(--neutral-50);
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--neutral-700);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.form-select, .form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--neutral-300);
    border-radius: 10px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
}

.form-select:focus, .form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

.form-error {
    border-color: #dc2626 !important;
}

.error-message {
    font-size: 0.75rem;
    color: #dc2626;
    margin-top: 0.25rem;
}

/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    border: 1px solid var(--neutral-200);
}

.modal-input {
    width: 100%;
    padding: 0.875rem;
    border: 1px solid var(--neutral-300);
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.modal-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

/* Menu Card Styling for Inventory Sections */
.menu-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
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
</style>

<div x-data="{ 
    showCreateModal: false, 
    showEditModal: false, 
    showDeleteModal: false, /* ADDED STATE */
    editingItem: null, 
    deletingItem: null, /* ADDED STATE */
    updateRoute: '{{ route('admin.inventory.update', ':id') }}',
    deleteRoute: '{{ route('admin.inventory.destroy', ':id') }}' /* ADDED ROUTE */
}"
    x-effect="document.body.classList.toggle('overflow-hidden', showCreateModal || showEditModal || showDeleteModal)"
    @keydown.escape.window="showCreateModal = false; showEditModal = false; showDeleteModal = false; editingItem = null; deletingItem = null">
    <div class="modern-card menu-card admin-page-shell p-6 mx-auto max-w-full">
        <div class="page-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-boxes text-white"></i>
                </div>
                <div>
                    <h1 class="header-title">Inventory Management</h1>
                    <p class="header-subtitle">Manage and track your inventory items and quantities</p>
                </div>
            </div>
            <div class="header-actions w-full md:w-auto">
                <div class="relative w-full sm:w-64 md:w-72">
                    <input type="search"
                           id="searchInput"
                           placeholder="Search inventory items..."
                           class="admin-search-input w-full rounded-lg border border-gray-300 bg-white py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-[#057C3C] focus:border-transparent"
                           oninput="filterTable(this.value)"
                           aria-label="Search inventory items">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button id="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" style="display: none;">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
                    <button @click="showCreateModal = true" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Add Item
                    </button>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-col gap-4">
                <div class="filter-row w-full">
                    <label for="category" class="filter-label filter-label-inline">Filter by Category</label>
                    <div class="select-with-arrows w-full sm:w-64">
                        <select name="category" id="category" onchange="this.form.submit()" class="filter-select w-full" data-admin-select="true">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <span class="select-arrows" aria-hidden="true">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4M8 15l4 4 4-4"></path>
                            </svg>
                        </span>
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

        <div class="overflow-auto max-h-96 modern-scrollbar">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>
                            <a href="?sort=name" class="hover:text-gray-700 transition-colors duration-200">Item Name</a>
                        </th>
                        <th>
                            <a href="?sort=qty" class="hover:text-gray-700 transition-colors duration-200">Quantity</a>
                        </th>
                        <th>Unit</th>
                        <th>
                            <a href="?sort=expiry_date" class="hover:text-gray-700 transition-colors duration-200">Expiry Date</a>
                        </th>
                        <th>Category</th>
                        <th class="hidden md:table-cell">Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="font-semibold text-gray-900">
                                {{ $item->name }}
                            </td>

                            <td>
                                <span class="quantity-badge
                                    @if($item->qty <= 5) quantity-low
                                    @elseif($item->qty <= 10) quantity-medium
                                    @else quantity-good @endif">
                                    {{ $item->qty }}
                                </span>
                            </td>

                            <td class="text-gray-600">{{ $item->unit }}</td>
                            <td class="text-gray-600">{{ $item->expiry_date ?? 'N/A' }}</td>
                            <td class="text-gray-600">{{ $item->category }}</td>
                            <td class="text-gray-600 hidden md:table-cell">{{ $item->updated_at->diffForHumans() }}</td>

                            <td>
                                <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                    <button @click="editingItem = JSON.parse($el.dataset.item); showEditModal = true"
                                        data-item='@json($item)'
                                        class="action-btn action-btn-edit">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>

                                    {{-- MODIFIED: Change to button that opens delete modal --}}
                                    <button @click="deletingItem = JSON.parse($el.dataset.item); showDeleteModal = true"
                                        data-item='@json($item)'
                                        class="action-btn action-btn-delete">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-boxes text-gray-400"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-2">No inventory items found</p>
                                    <p class="text-sm text-gray-500">Start by adding your first item</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="showCreateModal" @click="showCreateModal = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="modern-modal w-full max-w-lg p-6 relative z-10" x-transition.scale.90>
            <button @click="showCreateModal = false"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-gray-900">Add Inventory Item</h2>

            <form action="{{ route('admin.inventory.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="create_name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="create_name" required class="modal-input">
                </div>

                <div>
                    <label for="create_category" class="form-label">Category</label>
                    <select name="category" id="create_category" required class="modal-input" data-admin-select="true">
                        <option value="">Select a category</option>
                        <option value="Perishable">Perishable</option>
                        <option value="Condiments">Condiments</option>
                        <option value="Frozen">Frozen</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div>
                    <label for="create_qty" class="form-label">Quantity</label>
                    <input type="number" name="qty" id="create_qty" min="1" required class="modal-input">
                </div>

                <div>
                    <label for="create_unit" class="form-label">Unit</label>
                    <select name="unit" id="create_unit" required class="modal-input" data-admin-select="true">
                        <option value="">Select a unit</option>
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

                <div>
                    <label for="create_expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" id="create_expiry_date" class="modal-input">
                    <small class="text-gray-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showCreateModal = false" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" @click="showEditModal = false; editingItem = null" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="modern-modal w-full max-w-lg p-6 relative z-10" x-transition.scale.90>
            <button @click="showEditModal = false; editingItem = null"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-gray-900">Edit Inventory Item</h2>

            <form x-bind:action="editingItem ? updateRoute.replace(':id', editingItem.id) : ''" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label for="edit_name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="edit_name" required x-bind:value="editingItem ? editingItem.name : ''" class="modal-input">
                </div>

                <div>
                    <label for="edit_category" class="form-label">Category</label>
                    <select name="category" id="edit_category" required x-bind:value="editingItem ? editingItem.category : ''" class="modal-input" data-admin-select="true">
                        <option value="">Select a category</option>
                        <option value="Perishable">Perishable</option>
                        <option value="Condiments">Condiments</option>
                        <option value="Frozen">Frozen</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <div>
                    <label for="edit_qty" class="form-label">Quantity</label>
                    <input type="number" name="qty" id="edit_qty" min="1" required x-bind:value="editingItem ? editingItem.qty : ''" class="modal-input">
                </div>

                <div>
                    <label for="edit_unit" class="form-label">Unit</label>
                    <select name="unit" id="edit_unit" required x-bind:value="editingItem ? editingItem.unit : ''" class="modal-input" data-admin-select="true">
                        <option value="">Select a unit</option>
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

                <div>
                    <label for="edit_expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" id="edit_expiry_date" x-bind:value="editingItem ? editingItem.expiry_date : ''" class="modal-input">
                    <small class="text-gray-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showEditModal = false; editingItem = null" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div x-show="showDeleteModal" @click="showDeleteModal = false; deletingItem = null" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="modern-modal w-full max-w-sm p-8 relative z-10 text-center" x-transition.scale.90>
            <button @click="showDeleteModal = false; deletingItem = null"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl">
                &times;
            </button>

            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            
            <h2 class="text-xl font-bold mb-2 text-gray-900">Confirm Deletion</h2>
            
            <p class="text-gray-600 mb-6 text-sm">
                Are you sure you want to delete 
                <strong class="text-gray-900" x-text="deletingItem ? deletingItem.name : 'this item'"></strong>? 
                This action cannot be undone.
            </p>

            <form x-bind:action="deletingItem ? deleteRoute.replace(':id', deletingItem.id) : '#'" method="POST">
                @csrf @method('DELETE')
                
                <div class="flex justify-center space-x-3">
                    <button type="button" @click="showDeleteModal = false; deletingItem = null" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection