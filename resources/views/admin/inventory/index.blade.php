@extends('layouts.sidebar')
@section('page-title', 'Inventory Management')

@section('content')
<style>
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

/* Header Styles */
.header-actions {
    flex-direction: column;
    align-items: flex-end;
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
    
    <div class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 mx-auto max-w-full">
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
            <div class="header-actions w-full md:w-auto">
                <div class="relative w-full sm:w-64 md:w-72">
                    <input type="search"
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
                <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
                    <x-admin.ui.button.primary type="button" @click="showCreateModal = true">
                        <x-admin.ui.icon name="fa-plus" style="fas" size="sm" />
                        Add Item
                    </x-admin.ui.button.primary>
                </div>
            </div>
        </div>

        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="flex flex-col gap-4">
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
                        <th class="w-14">#</th>
                        <th>
                            <a href="?sort=name" class="hover:text-admin-neutral-700 transition-colors duration-200">Item Name</a>
                        </th>
                        <th>
                            <a href="?sort=qty" class="hover:text-admin-neutral-700 transition-colors duration-200">Quantity</a>
                        </th>
                        <th>Unit</th>
                        <th>
                            <a href="?sort=expiry_date" class="hover:text-admin-neutral-700 transition-colors duration-200">Expiry Date</a>
                        </th>
                        <th>Category</th>
                        <th class="hidden md:table-cell">Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="text-admin-neutral-500 font-semibold">
                                {{ ($items->firstItem() ?? 0) + $loop->index }}
                            </td>

                            <td class="font-semibold text-admin-neutral-900">
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

                            <td class="text-admin-neutral-600">{{ $item->unit }}</td>
                            <td class="text-admin-neutral-600">{{ $item->expiry_date ?? 'N/A' }}</td>
                            <td class="text-admin-neutral-600">{{ $item->category }}</td>
                            <td class="text-admin-neutral-600 hidden md:table-cell">{{ $item->updated_at->diffForHumans() }}</td>

                            <td>
                                <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                    <x-admin.ui.button.secondary
                                        type="button"
                                        class="!py-2 !px-3 text-xs"
                                        @click="editingItem = JSON.parse($el.dataset.item); showEditModal = true"
                                        data-item='@json($item)'>
                                        <x-admin.ui.icon name="fa-pen" style="fas" size="sm" />
                                        Edit
                                    </x-admin.ui.button.secondary>

                                    {{-- MODIFIED: Change to button that opens delete modal --}}
                                    <x-admin.ui.button.danger
                                        type="button"
                                        class="!py-2 !px-3 text-xs"
                                        @click="deletingItem = JSON.parse($el.dataset.item); showDeleteModal = true"
                                        data-item='@json($item)'>
                                        <x-admin.ui.icon name="fa-trash-alt" style="fas" size="sm" />
                                        Delete
                                    </x-admin.ui.button.danger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
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

        @if($items->hasPages())
            <div class="mt-6">
                {{ $items->links('components.pagination') }}
            </div>
        @endif
    </div>

    <div x-show="showCreateModal" @click="showCreateModal = false" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="modern-modal w-full max-w-lg p-6 relative z-10" x-transition.scale.90>
            <button @click="showCreateModal = false"
                    class="absolute top-4 right-4 text-admin-neutral-500 hover:text-admin-neutral-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-admin-neutral-900">Add Inventory Item</h2>

            <form id="createInventoryForm" action="{{ route('admin.inventory.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="create_name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="create_name" required class="modal-input" autocomplete="off">
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
                    <small class="text-admin-neutral-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end gap-3">
                    <x-admin.ui.button.secondary type="button" @click="showCreateModal = false">
                        Cancel
                    </x-admin.ui.button.secondary>
                    <x-admin.ui.button.primary type="submit">
                        Save Item
                    </x-admin.ui.button.primary>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEditModal" @click="showEditModal = false; editingItem = null" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition.opacity x-cloak>
        <div @click.stop class="modern-modal w-full max-w-lg p-6 relative z-10" x-transition.scale.90>
            <button @click="showEditModal = false; editingItem = null"
                    class="absolute top-4 right-4 text-admin-neutral-500 hover:text-admin-neutral-800 text-xl">
                &times;
            </button>

            <h2 class="text-xl font-bold mb-4 text-admin-neutral-900">Edit Inventory Item</h2>

            <form id="editInventoryForm" x-bind:action="editingItem ? updateRoute.replace(':id', editingItem.id) : ''" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label for="edit_name" class="form-label">Item Name</label>
                    <input type="text" name="name" id="edit_name" required x-bind:value="editingItem ? editingItem.name : ''" class="modal-input" autocomplete="off">
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
                    <small class="text-admin-neutral-500 text-xs">Leave blank if not applicable.</small>
                </div>

                <div class="flex justify-end gap-3">
                    <x-admin.ui.button.secondary type="button" @click="showEditModal = false; editingItem = null">
                        Cancel
                    </x-admin.ui.button.secondary>
                    <x-admin.ui.button.primary type="submit">
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
            class="absolute inset-0 bg-red-950/40 backdrop-blur-sm"
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
            class="relative w-full max-w-sm overflow-hidden rounded-2xl border border-red-200 bg-white shadow-2xl"
            @click.stop
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-inventory-title"
            aria-describedby="delete-inventory-desc"
        >
            <div class="flex items-start justify-between gap-4 border-b border-red-100 bg-red-50 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                    </span>
                    <div>
                        <h2 id="delete-inventory-title" class="text-lg font-semibold text-red-900">Confirm Deletion</h2>
                        <p class="text-xs text-red-700">This action cannot be undone.</p>
                    </div>
                </div>
                <button @click="showDeleteModal = false; deletingItem = null"
                        class="rounded-full p-1 text-red-600 hover:text-red-700" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="delete-inventory-desc" class="px-6 py-5 text-sm text-red-700">
                Are you sure you want to delete
                <span class="font-semibold text-red-900" x-text="deletingItem ? deletingItem.name : 'this item'"></span>?
                This item will be removed from inventory.
            </div>

            <form id="deleteInventoryForm" x-bind:action="deletingItem ? deleteRoute.replace(':id', deletingItem.id) : '#'" x-bind:data-id="deletingItem ? deletingItem.id : ''" method="POST"
                  class="flex flex-wrap justify-end gap-3 px-6 py-4 border-t border-red-100 bg-red-50/60">
                @csrf @method('DELETE')

                <x-admin.ui.button.secondary type="button" @click="showDeleteModal = false; deletingItem = null">
                    Cancel
                </x-admin.ui.button.secondary>
                <x-admin.ui.button.danger type="submit">
                    Delete
                </x-admin.ui.button.danger>
            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rootCloseEvent = new Event('close-inventory-modals');

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

    // Create
    const createForm = document.getElementById('createInventoryForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const result = await submitForm(createForm);
            if (result !== null) {
                createForm.reset();
                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-create-success' }));
                setTimeout(function(){ location.reload(); }, 900);
            }
        });
    }

    // Edit
    const editForm = document.getElementById('editInventoryForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const result = await submitForm(editForm);
            if (result !== null) {
                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-update-success' }));
                setTimeout(function(){ location.reload(); }, 700);
            }
        });
    }

    // Delete (in-place)
    const deleteForm = document.getElementById('deleteInventoryForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const result = await submitForm(deleteForm);
            if (result !== null) {
                const deletedId = deleteForm.dataset.id || (function(){
                    const m = (deleteForm.action||'').match(/\/admin\/inventory\/(\d+)/);
                    return m ? m[1] : null;
                })();
                if (deletedId) {
                    // Find row that contains a button with matching data-item id
                    const rows = document.querySelectorAll('tbody tr');
                    for (const row of rows) {
                        const btn = row.querySelector('button[data-item]');
                        if (!btn) continue;
                        try {
                            const data = JSON.parse(btn.getAttribute('data-item'));
                            if (String(data.id) === String(deletedId)) {
                                row.remove();
                                break;
                            }
                        } catch (err) {
                            continue;
                        }
                    }
                }

                window.dispatchEvent(rootCloseEvent);
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'inventory-delete-success' }));
            }
        });
    }
});
</script>

@endsection
