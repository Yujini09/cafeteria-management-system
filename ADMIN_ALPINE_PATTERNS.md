<!-- ALPINE.JS & ADMIN COMPONENTS INTEGRATION GUIDE -->

This guide shows how to integrate Alpine.js with admin UI components for enhanced UX.

═══════════════════════════════════════════════════════════════════════════════
1. MODAL INTERACTIONS WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Basic Modal Open/Close

<!-- Button that opens modal -->
<x-admin.buttons.danger 
    @click="$dispatch('open-modal', { detail: 'deleteItem' })"
>
    Delete Item
</x-admin.buttons.danger>

<!-- Modal component -->
<x-admin.modals.modal 
    name="deleteItem"
    title="Delete Item?"
    type="error"
>
    <p>Are you sure you want to delete this item?</p>
    
    <x-slot:footer>
        <button @click="$dispatch('close-modal', { detail: 'deleteItem' })">
            Cancel
        </button>
        <x-admin.buttons.danger type="submit">Delete</x-admin.buttons.danger>
    </x-slot:footer>
</x-admin.modals.modal>

### Multiple Modals on Same Page

@foreach ($items as $item)
    <!-- Unique modal per item -->
    <x-admin.modals.modal 
        name="delete{{ $item->id }}"
        title="Delete {{ $item->name }}?"
        type="error"
    >
        <p>Delete this item permanently?</p>
        <x-slot:footer>
            <button @click="$dispatch('close-modal', { detail: 'delete{{ $item->id }}' })">
                Cancel
            </button>
            <form method="POST" action="/admin/items/{{ $item->id }}" style="display:inline;">
                @method('DELETE')
                @csrf
                <x-admin.buttons.danger type="submit">Delete</x-admin.buttons.danger>
            </form>
        </x-slot:footer>
    </x-admin.modals.modal>
    
    <!-- Trigger button -->
    <x-admin.buttons.danger 
        @click="$dispatch('open-modal', { detail: 'delete{{ $item->id }}' })"
    >
        Delete
    </x-admin.buttons.danger>
@endforeach


═══════════════════════════════════════════════════════════════════════════════
2. FORM STATE MANAGEMENT WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Form with Real-Time Validation Feedback

<form 
    method="POST" 
    action="/admin/users"
    x-data="{
        form: {
            name: '',
            email: '',
            password: ''
        },
        errors: {},
        isSubmitting: false,
        async validateField(field) {
            // Clear previous error
            delete this.errors[field];
            
            // Simple email validation
            if (field === 'email' && this.form.email && !this.form.email.includes('@')) {
                this.errors.email = 'Invalid email format';
            }
            
            // Password validation
            if (field === 'password' && this.form.password && this.form.password.length < 8) {
                this.errors.password = 'Password must be at least 8 characters';
            }
        },
        async submit() {
            this.isSubmitting = true;
            // Form submits normally (handled by Laravel)
        }
    }"
    @submit="submit()"
    class="space-y-6"
>
    @csrf
    
    <x-admin.forms.text-input 
        label="Full Name"
        name="name"
        placeholder="John Doe"
        required
        x-model="form.name"
        @blur="validateField('name')"
    />
    
    <x-admin.forms.text-input 
        label="Email"
        name="email"
        type="email"
        placeholder="user@example.com"
        required
        x-model="form.email"
        @blur="validateField('email')"
    />
    
    <x-admin.forms.password-input 
        label="Password"
        name="password"
        required
        showRequirements="true"
        x-model="form.password"
        @blur="validateField('password')"
    />
    
    <div class="flex gap-3 justify-start pt-6 border-t border-gray-200">
        <x-admin.buttons.secondary>Cancel</x-admin.buttons.secondary>
        <x-admin.buttons.primary type="submit" :disabled="Object.keys(errors).length > 0">
            <span x-show="!isSubmitting">Create User</span>
            <span x-show="isSubmitting">Creating...</span>
        </x-admin.buttons.primary>
    </div>
</form>


═══════════════════════════════════════════════════════════════════════════════
3. CONDITIONAL RENDERING WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Show/Hide Fields Based on Selection

<form x-data="{ selectedRole: 'user' }" class="space-y-6">
    @csrf
    
    <!-- Role selector -->
    <x-admin.forms.select 
        label="Role"
        name="role"
        :options="['user' => 'Regular User', 'admin' => 'Administrator']"
        x-model="selectedRole"
        required
    />
    
    <!-- Fields visible only for admin -->
    <template x-if="selectedRole === 'admin'">
        <div class="space-y-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-700 font-medium">Admin-specific settings</p>
            
            <x-admin.forms.select 
                label="Admin Level"
                name="admin_level"
                :options="['level1' => 'Level 1', 'level2' => 'Level 2', 'level3' => 'Level 3']"
            />
            
            <x-admin.forms.textarea 
                label="Admin Notes"
                name="admin_notes"
                placeholder="Internal notes about this admin..."
            />
        </div>
    </template>
</form>


═══════════════════════════════════════════════════════════════════════════════
4. TABLE ROW SELECTION WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Bulk Actions on Selected Rows

<div 
    x-data="{
        selectedRows: [],
        allSelected: false,
        toggleAll() {
            if (this.allSelected) {
                this.selectedRows = [];
                this.allSelected = false;
            } else {
                this.selectedRows = Array.from(document.querySelectorAll('input[name=item_id]'))
                    .map(el => el.value);
                this.allSelected = true;
            }
        },
        toggleRow(id) {
            if (this.selectedRows.includes(id)) {
                this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
            } else {
                this.selectedRows.push(id);
            }
            this.allSelected = this.selectedRows.length === this.getTotalRows();
        },
        getTotalRows() {
            return document.querySelectorAll('input[name=item_id]').length;
        },
        deleteSelected() {
            if (!confirm('Delete ' + this.selectedRows.length + ' items?')) return;
            // Submit form with selected IDs
            document.getElementById('bulkForm').submit();
        }
    }"
>
    <!-- Bulk Actions Bar (shown when items selected) -->
    <template x-if="selectedRows.length > 0">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 flex items-center justify-between">
            <p class="text-sm font-medium text-blue-900">
                <span x-text="selectedRows.length"></span> item(s) selected
            </p>
            <div class="space-x-2">
                <x-admin.buttons.secondary @click="selectedRows = []">
                    Clear Selection
                </x-admin.buttons.secondary>
                <x-admin.buttons.danger @click="deleteSelected()">
                    Delete Selected
                </x-admin.buttons.danger>
            </div>
        </div>
    </template>
    
    <!-- Table -->
    <form id="bulkForm" method="POST" action="/admin/items/bulk-delete" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf
        @method('POST')
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">
                            <input 
                                type="checkbox" 
                                @change="toggleAll()"
                                x-bind:checked="allSelected"
                                class="rounded"
                            />
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <input 
                                type="checkbox" 
                                name="item_id" 
                                value="{{ $item->id }}"
                                @change="toggleRow('{{ $item->id }}')"
                                x-bind:checked="selectedRows.includes('{{ $item->id }}')"
                                class="rounded"
                            />
                        </td>
                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $item->category }}</td>
                        <td class="px-6 py-3 text-right space-x-2">
                            <a href="/admin/items/{{ $item->id }}/edit" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                <x-admin.icons.icon type="edit" class="w-4 h-4" />
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>


═══════════════════════════════════════════════════════════════════════════════
5. TOAST NOTIFICATIONS WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Manual Toast Trigger

<div x-data="{ showToast: false, message: '', type: 'success' }">
    <!-- Toast -->
    <template x-if="showToast">
        <x-admin.notifications.toast 
            x-bind:type="type"
            x-bind:message="message"
        />
    </template>
    
    <!-- Button that triggers toast -->
    <x-admin.buttons.primary 
        @click="showToast = true; message = 'Action completed!'; type = 'success'; setTimeout(() => showToast = false, 5000)"
    >
        Show Success Toast
    </x-admin.buttons.primary>
</div>

### Form Submit with Toast

<div x-data="{ 
    submitted: false,
    async submitForm(event) {
        this.submitted = true;
        // Form auto-submits, server redirect will show session toast
    }
}">
    <form method="POST" action="/admin/users" @submit="submitForm(event)" class="space-y-6">
        @csrf
        
        <x-admin.forms.text-input label="Name" name="name" required />
        <x-admin.forms.text-input label="Email" name="email" type="email" required />
        
        <x-admin.buttons.primary type="submit" :disabled="submitted">
            <span x-show="!submitted">Create User</span>
            <span x-show="submitted">Creating...</span>
        </x-admin.buttons.primary>
    </form>
</div>


═══════════════════════════════════════════════════════════════════════════════
6. SEARCH & FILTER WITH ALPINE.JS
═══════════════════════════════════════════════════════════════════════════════

### Real-Time Table Search

<div 
    x-data="{
        searchQuery: '',
        get filteredItems() {
            const items = document.querySelectorAll('tbody tr');
            return Array.from(items).filter(row => {
                const text = row.textContent.toLowerCase();
                return text.includes(this.searchQuery.toLowerCase());
            });
        },
        get visibleCount() {
            return this.filteredItems.length;
        }
    }"
>
    <!-- Search Input -->
    <div class="mb-6">
        <x-admin.forms.text-input 
            name="search"
            placeholder="Search by name, email, or role..."
            x-model="searchQuery"
            helper="Filters table in real-time"
        />
    </div>
    
    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-sm">{{ $user->role }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- No Results Message -->
        <template x-if="visibleCount === 0">
            <div class="p-6 text-center">
                <p class="text-gray-500">No results found for "<span x-text="searchQuery"></span>"</p>
            </div>
        </template>
    </div>
    
    <!-- Results Count -->
    <p class="text-sm text-gray-600 mt-4">
        Showing <span x-text="visibleCount"></span> of <span x-text="@json(count($users))"></span> users
    </p>
</div>


═══════════════════════════════════════════════════════════════════════════════
7. COMMON ALPINE.JS PATTERNS FOR ADMIN
═══════════════════════════════════════════════════════════════════════════════

### Loading State on Button

<button 
    @click="loading = true; await save(); loading = false"
    :disabled="loading"
    x-data="{ loading: false }"
>
    <span x-show="!loading">Save</span>
    <span x-show="loading">
        <svg class="animate-spin h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        </svg>
        Saving...
    </span>
</button>

### Toggle Visibility

<div x-data="{ open: false }">
    <button @click="open = !open">
        <span x-text="open ? 'Hide' : 'Show'"></span>
    </button>
    
    <template x-if="open">
        <div class="p-4 bg-gray-50 rounded-lg">
            <!-- Hidden content -->
        </div>
    </template>
</div>

### Tab Navigation

<div x-data="{ activeTab: 'general' }">
    <div class="flex gap-2 mb-4">
        <button 
            @click="activeTab = 'general'"
            :class="{ 'bg-green-600 text-white': activeTab === 'general', 'bg-gray-200': activeTab !== 'general' }"
            class="px-4 py-2 rounded"
        >
            General
        </button>
        <button 
            @click="activeTab = 'advanced'"
            :class="{ 'bg-green-600 text-white': activeTab === 'advanced', 'bg-gray-200': activeTab !== 'advanced' }"
            class="px-4 py-2 rounded"
        >
            Advanced
        </button>
    </div>
    
    <template x-if="activeTab === 'general'">
        <div>General settings...</div>
    </template>
    <template x-if="activeTab === 'advanced'">
        <div>Advanced settings...</div>
    </template>
</div>

### Countdown Timer

<div x-data="{ 
    seconds: 5,
    init() {
        setInterval(() => {
            if (this.seconds > 0) this.seconds--;
        }, 1000);
    }
}"
x-init="init()"
>
    <p>Redirecting in <span x-text="seconds"></span> seconds...</p>
</div>

### Async Form Submission

<form 
    @submit.prevent="submitForm()"
    x-data="{
        async submitForm() {
            const formData = new FormData(this.$el);
            const response = await fetch('/admin/users', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                window.location.href = '/admin/users';
            } else {
                alert('Error submitting form');
            }
        }
    }"
>
    @csrf
    <!-- form fields -->
</form>


═══════════════════════════════════════════════════════════════════════════════
