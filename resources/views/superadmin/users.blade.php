@extends('layouts.sidebar')
@section('page-title', 'Manage Users')
@php($disableAdminSuccessToast = true)

@section('content')
{{-- Design system: admin tokens from tailwind (admin-primary, rounded-admin, etc.). No inline overrides. --}}
<div x-data="{}" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 max-w-full overflow-hidden flex flex-col">
    {{-- Success handled via success modal; no duplicate inline messages. --}}

    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-users" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">User Management</h1>
                <p class="header-subtitle">Manage admin accounts and access</p>
            </div>
        </div>

        <div class="header-actions w-full md:w-auto flex flex-col items-end">
            <div class="relative w-full sm:w-64 md:w-72">
                <input type="search"
                       id="searchInput"
                       placeholder="Search users..."
                       class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 pl-10 pr-10 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                       oninput="filterTable(this.value)"
                       aria-label="Search users">
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
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
            <x-admin.ui.icon name="fa-user-check" size="xs" />
            Total Users: {{ $users->total() }}
        </span>
        <div class="ml-auto flex flex-wrap items-center gap-3">
            <x-admin.ui.button.secondary type="button" onclick="openRecentActivitiesModal()">
                <x-admin.ui.icon name="fa-file-lines" size="sm" />
                Recent Activities
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.primary type="button" @click="$dispatch('open-admin-modal', 'addAdmin')">
                <x-admin.ui.icon name="fa-plus" size="sm" />
                Add Admin
            </x-admin.ui.button.primary>
        </div>
    </div>

    <div class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
        <table class="modern-table table-fixed">
            <colgroup>
                <col class="w-14">
                <col class="w-48">
                <col class="w-72">
                <col class="w-48">
                <col class="w-64">
            </colgroup>
            <thead>
                <tr>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide w-14 whitespace-nowrap overflow-hidden text-ellipsis">#</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                        <button type="button" class="group inline-flex items-center gap-2" onclick="toggleUserNameSort()" aria-label="Sort by name">
                            <span>Name</span>
                            <x-admin.ui.icon id="nameSortIcon" name="fa-arrow-down" style="fas" size="sm" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
                        </button>
                    </th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Email</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                        <div class="flex flex-nowrap items-center gap-2">
                            <span>Role</span>
                            <div class="min-w-[9rem] relative">
                                <x-admin.forms.select name="roleFilter"
                                    :options="['admin' => 'Admin', 'customer' => 'Customer']"
                                    placeholder="All Roles"
                                    class="text-xs pr-8" />
                            </div>
                        </div>
                    </th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
            @forelse($users as $user)
                <tr class="hover:bg-admin-neutral-50 transition-colors duration-admin" data-user-row="true" data-user-name="{{ strtolower($user->name) }}" data-user-role="{{ $user->role }}">
                    <td class="text-admin-neutral-500 py-4 px-4 border-b border-admin-neutral-100 font-semibold">
                        {{ ($users->firstItem() ?? 0) + $loop->index }}
                    </td>
                    <td class="font-semibold text-admin-neutral-900 py-4 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->name }}</td>
                    <td class="text-admin-neutral-600 py-4 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->email }}</td>
                    <td class="py-4 px-4 border-b border-admin-neutral-100">
                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold uppercase {{ $user->role === 'admin' ? 'bg-admin-primary-light text-admin-primary' : 'bg-admin-neutral-100 text-admin-neutral-600' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-4 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">
                        <div class="flex flex-wrap gap-2">
                            @if($user->role === 'admin')
                                <x-admin.ui.button.secondary type="button" class="!py-2 !px-3 text-xs" onclick="openEditModal({{ $user->id }}, '{{ addslashes(e($user->name)) }}', '{{ addslashes(e($user->email)) }}')">
                                    <x-admin.ui.icon name="fa-pen" size="sm" /> Edit
                                </x-admin.ui.button.secondary>
                                <a href="{{ route('superadmin.users.audit', $user) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-warning-light text-admin-warning border border-amber-200 hover:bg-amber-100 transition-colors duration-admin">
                                    <x-admin.ui.icon name="fa-file-lines" size="sm" /> Audit
                                </a>
                            @else
                                <a href="{{ route('superadmin.users.audit', $user) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-warning-light text-admin-warning border border-amber-200 hover:bg-amber-100 transition-colors duration-admin">
                                    <x-admin.ui.icon name="fa-file-lines" size="sm" /> Audit
                                </a>
                            @endif
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" id="deleteForm{{ $user->id }}">
                                @csrf @method('DELETE')
                                <x-admin.ui.button.danger type="button" class="!py-2 !px-3 text-xs" onclick="openDeleteModal({{ $user->id }}, '{{ addslashes(e($user->name)) }}')">
                                    <x-admin.ui.icon name="fa-trash-can" size="sm" /> Delete
                                </x-admin.ui.button.danger>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 px-4 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-admin-neutral-100 flex items-center justify-center">
                            <x-admin.ui.icon name="fa-triangle-exclamation" class="text-admin-neutral-400 w-8 h-8" />
                        </div>
                        <p class="font-semibold text-admin-neutral-900 mb-1">No users found</p>
                        <p class="text-sm text-admin-neutral-500">Start by adding your first user to the system</p>
                    </td>
                </tr>
            @endforelse
                <tr id="usersEmptyState" class="hidden">
                    <td colspan="5" class="py-10 px-4 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-admin-neutral-100 flex items-center justify-center">
                            <x-admin.ui.icon name="fa-filter" class="text-admin-neutral-400 w-6 h-6" />
                        </div>
                        <p class="font-semibold text-admin-neutral-900 mb-1">No users match this filter</p>
                        <p class="text-sm text-admin-neutral-500">Try adjusting the role filter or search.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links('components.pagination') }}
        </div>
    @endif
</div>

{{-- Unified admin modals: overlay blur, ESC/click-outside, body scroll lock. --}}
@if(session('success'))
<x-success-modal name="users-success" title="Success!" maxWidth="sm" overlayClass="bg-admin-neutral-900/50">
    <p class="text-sm text-admin-neutral-600">{{ session('success') }}</p>
</x-success-modal>
@endif

<x-admin.ui.modal name="addAdmin" title="Add New Admin" variant="confirmation" maxWidth="md">
    <form method="POST" action="{{ route('superadmin.users.store') }}" id="addAdminForm" data-action-loading>
        @csrf
        <input type="hidden" name="form_context" value="add_admin">
        <div class="space-y-4">
            <x-admin.forms.input name="name" label="Full Name" required />
            <x-admin.forms.input name="email" label="Email Address" type="email" required />
            <p class="text-sm text-admin-neutral-500">
                A temporary password will be generated and sent to this email address.
            </p>
        </div>
    </form>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" @click="$dispatch('open-admin-modal', 'createAdminConfirm')">Create Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="editUser" title="Edit Admin" variant="confirmation" maxWidth="md">
    <form id="editUserForm" method="POST" data-action-loading>
        @csrf
        @method('PUT')
        <input type="hidden" name="form_context" value="edit_admin">
        <input type="hidden" name="edit_user_id" id="editUserId" value="{{ old('edit_user_id') }}">
        <div class="space-y-4">
            <x-admin.forms.input name="name" id="editName" label="Full Name" required />
            <x-admin.forms.input
                name="email"
                id="editEmail"
                label="Email Address"
                type="email"
                helper="Admin email address cannot be changed."
                disabled
                readonly
                class="bg-admin-neutral-100 text-admin-neutral-500 cursor-not-allowed"
            />
        </div>
    </form>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" @click="$dispatch('open-admin-modal', 'updateAdminConfirm')">Update Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="deleteConfirm" title="Delete User" variant="error" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">
        Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-admin-neutral-900"></span>?
        This action cannot be undone.
    </p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.danger type="button" onclick="confirmDelete(this)" data-loading-text="Deleting User...">Delete</x-admin.ui.button.danger>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="createAdminConfirm" title="Create New Admin" variant="confirmation" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">
        Are you sure you want to create this admin user? A temporary password will be emailed and they will be required to change it after login.
    </p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'createAdminConfirm')">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" onclick="submitAddAdminForm(this)" data-loading-text="Creating Admin...">Create Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="updateAdminConfirm" title="Update Admin" variant="confirmation" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">Are you sure you want to save the changes to this admin user?</p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'updateAdminConfirm')">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" onclick="submitEditUserForm(this)" data-loading-text="Updating Admin...">Update Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="recentActivities" title="Audit Feed" icon="fa-clock-rotate-left" iconStyle="fas" variant="info" maxWidth="6xl">
    <button type="button"
            class="absolute top-4 right-4 rounded-full p-1.5 text-admin-neutral-400 hover:bg-admin-neutral-100 hover:text-admin-neutral-600 transition-colors duration-admin"
            @click="$dispatch('close-admin-modal', 'recentActivities')"
            aria-label="Close recent activities modal">
        <x-admin.ui.icon name="fa-xmark" size="sm" />
    </button>
    <div class="flex h-[calc(100vh-12rem)] max-h-[82vh] min-h-0 flex-col gap-4">
        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-admin-neutral-700">Review recent actions across modules and admin users.</p>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-admin-neutral-600">
                        Total
                        <span id="activitiesTotalCount" class="text-admin-neutral-900">0</span>
                    </span>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 xl:flex-row xl:items-center">
                <div class="relative flex-1">
                    <input type="search"
                           id="activitiesSearchInput"
                           placeholder="Search user, action, module, description..."
                           class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 pl-10 pr-10 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                           aria-label="Search recent activities">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <button id="activitiesClearSearch"
                            type="button"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600"
                            aria-label="Clear activities search">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-wrap gap-2">
                    <select id="activitiesModuleFilter" class="admin-select min-w-[11rem]" data-admin-select="true" aria-label="Filter activities by module">
                        <option value="">All Modules</option>
                    </select>
                    <select id="activitiesActionFilter" class="admin-select min-w-[11rem]" data-admin-select="true" aria-label="Filter activities by action">
                        <option value="">All Actions</option>
                    </select>
                    <x-admin.ui.button.secondary type="button" id="activitiesResetFilters">Reset</x-admin.ui.button.secondary>
                </div>
            </div>
        </div>

        <div id="activitiesTableContainer" class="flex-1 min-h-0 overflow-auto rounded-admin border border-admin-neutral-200 bg-white">
            <table class="w-full min-w-[70rem] border-collapse text-sm table-fixed">
                <colgroup>
                    <col class="w-56">
                    <col class="w-40">
                    <col class="w-40">
                    <col class="w-[15rem]">
                    <col class="w-60">
                </colgroup>
                <thead class="bg-admin-neutral-50">
                    <tr>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">User</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Action</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Module</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide">Description</th>
                        <th class="sticky top-0 z-10 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <button type="button" class="inline-flex items-center gap-1.5 hover:text-admin-neutral-900 transition-colors duration-admin" onclick="sortBy('created_at')">
                                <span>Date</span>
                                <x-admin.ui.icon id="activitiesSortIconDate" name="fa-arrow-down" size="xs" class="text-admin-neutral-400" />
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody id="activitiesTableBody">
                    <tr>
                        <td colspan="5" class="py-10 text-center text-admin-neutral-500">Loading activities...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="activitiesPagination" class="hidden flex-wrap items-center justify-between gap-3 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2">
            <p id="activitiesPaginationInfo" class="text-xs text-admin-neutral-500"></p>
            <nav id="activitiesPaginationNav" role="navigation" aria-label="Recent activities pagination" class="inline-flex items-center gap-1"></nav>
        </div>
    </div>
</x-admin.ui.modal>

<script>
// Open modals via dispatch so unified admin modal component handles overlay, ESC, scroll lock.
function openEditModal(id, name, email) {
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    const editUserIdInput = document.getElementById('editUserId');
    if (editUserIdInput) {
        editUserIdInput.value = id;
    }
    document.getElementById('editUserForm').action = `{{ url('superadmin/users') }}/${id}`;
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'editUser' }));
}

function submitAddAdminForm(triggerButton = null) {
    const form = document.getElementById('addAdminForm');
    if (!form) return;

    if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
        return;
    }

    if (triggerButton && window.cmsActionButtons) {
        const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Creating Admin...');
        if (!started) return;
    }

    window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'createAdminConfirm' }));

    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
    } else {
        form.submit();
    }
}

function submitEditUserForm(triggerButton = null) {
    const form = document.getElementById('editUserForm');
    if (!form) return;

    if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
        return;
    }

    if (triggerButton && window.cmsActionButtons) {
        const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Updating Admin...');
        if (!started) return;
    }

    window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'updateAdminConfirm' }));

    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
    } else {
        form.submit();
    }
}

var deleteUserId = null;

function openDeleteModal(userId, userName) {
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'deleteConfirm' }));
}

function confirmDelete(triggerButton = null) {
    if (deleteUserId) {
        if (triggerButton && window.cmsActionButtons) {
            const started = window.cmsActionButtons.start(triggerButton, triggerButton.dataset.loadingText || 'Deleting User...');
            if (!started) return;
        }
        document.getElementById('deleteForm' + deleteUserId).submit();
    }
}

var userSortDirection = 'desc';

function toggleUserNameSort() {
    userSortDirection = userSortDirection === 'asc' ? 'desc' : 'asc';
    sortUserRows();
    updateNameSortIcon();
}

function sortUserRows() {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr[data-user-row="true"]'));
    rows.sort((a, b) => {
        const aName = (a.dataset.userName || '').toLowerCase();
        const bName = (b.dataset.userName || '').toLowerCase();
        const comparison = aName.localeCompare(bName);
        return userSortDirection === 'asc' ? comparison : -comparison;
    });

    rows.forEach(row => tbody.appendChild(row));
}

function updateNameSortIcon() {
    const icon = document.getElementById('nameSortIcon');
    if (!icon) return;

    icon.classList.remove('fa-arrow-up', 'fa-arrow-down');
    icon.classList.add(userSortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down');
}

function applyUserFilters() {
    const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    const role = document.getElementById('roleFilter')?.value || '';
    const rows = document.querySelectorAll('#usersTableBody tr[data-user-row="true"]');
    const emptyState = document.getElementById('usersEmptyState');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const roleMatch = !role || row.dataset.userRole === role;
        const searchMatch = text.includes(query);
        const isVisible = roleMatch && searchMatch;
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) {
            visibleCount += 1;
        }
    });

    if (emptyState && rows.length > 0) {
        emptyState.classList.toggle('hidden', visibleCount !== 0);
    }

    const clearButton = document.getElementById('clearSearch');
    if (clearButton) {
        clearButton.style.display = query.length > 0 ? 'block' : 'none';
    }
}

var allAudits = [];
var filteredAudits = [];
var currentSortBy = 'created_at';
var currentSortDirection = 'desc';
var currentActivitiesPage = 1;
var activitiesPerPage = 10;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function activityText(value) {
    return (value ?? '').toString().trim();
}

function getRelativeTimeLabel(date) {
    if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
        return 'Unknown';
    }

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

function formatActivityDate(dateString) {
    const parsed = new Date(dateString || '');
    if (Number.isNaN(parsed.getTime())) {
        return { full: 'Unknown date', relative: 'Unknown' };
    }

    return {
        full: parsed.toLocaleString(),
        relative: getRelativeTimeLabel(parsed),
    };
}

function getActionBadgeClass(action) {
    const normalized = activityText(action).toLowerCase();

    if (normalized.includes('delete') || normalized.includes('remove')) {
        return 'bg-admin-danger-light text-admin-danger border-red-200';
    }
    if (normalized.includes('create') || normalized.includes('add')) {
        return 'bg-admin-success-light text-admin-success border-admin-success';
    }
    if (normalized.includes('update') || normalized.includes('edit') || normalized.includes('change')) {
        return 'bg-admin-primary-light text-admin-primary border-admin-neutral-200';
    }
    if (normalized.includes('approve') || normalized.includes('accept')) {
        return 'bg-admin-success-light text-admin-success border-admin-success';
    }
    if (normalized.includes('decline') || normalized.includes('reject')) {
        return 'bg-admin-warning-light text-admin-warning border-amber-200';
    }

    return 'bg-admin-neutral-100 text-admin-neutral-700 border-admin-neutral-200';
}

function updateActivitiesCounters(total, visible) {
    const totalEl = document.getElementById('activitiesTotalCount');
    const visibleEl = document.getElementById('activitiesVisibleCount');

    if (totalEl) {
        totalEl.textContent = String(total);
    }
    if (visibleEl) {
        visibleEl.textContent = String(visible);
    }
}

function updateActivitiesSortIndicators() {
    const icon = document.getElementById('activitiesSortIconDate');
    if (!icon) return;

    icon.classList.remove('fa-arrow-up', 'fa-arrow-down', 'text-admin-primary', 'text-admin-neutral-400');
    icon.classList.add(currentSortDirection === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down', 'text-admin-primary');
}

function setActivitiesSelectOptions(selectEl, values, placeholder) {
    if (!selectEl) return;

    const previous = selectEl.value || '';
    selectEl.innerHTML = '';

    const baseOption = document.createElement('option');
    baseOption.value = '';
    baseOption.textContent = placeholder;
    selectEl.appendChild(baseOption);

    values.forEach((value) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        selectEl.appendChild(option);
    });

    selectEl.value = values.includes(previous) ? previous : '';
}

function getActivitiesTotalPages(totalItems) {
    return Math.max(1, Math.ceil(totalItems / activitiesPerPage));
}

function buildActivitiesPageItems(totalPages, currentPage) {
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

function renderActivitiesPagination(totalItems) {
    const wrapper = document.getElementById('activitiesPagination');
    const info = document.getElementById('activitiesPaginationInfo');
    const nav = document.getElementById('activitiesPaginationNav');

    if (!wrapper || !info || !nav) return;

    if (totalItems <= 0) {
        wrapper.classList.add('hidden');
        wrapper.classList.remove('flex');
        info.textContent = '';
        nav.innerHTML = '';
        return;
    }

    const totalPages = getActivitiesTotalPages(totalItems);
    if (currentActivitiesPage > totalPages) {
        currentActivitiesPage = totalPages;
    }

    const firstItem = (currentActivitiesPage - 1) * activitiesPerPage + 1;
    const lastItem = Math.min(firstItem + activitiesPerPage - 1, totalItems);

    info.innerHTML = `Showing <span class="font-semibold text-admin-neutral-700">${firstItem}</span> to <span class="font-semibold text-admin-neutral-700">${lastItem}</span> of <span class="font-semibold text-admin-neutral-700">${totalItems}</span> results`;

    const disabledClass = 'inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-admin-neutral-50 text-xs font-semibold text-admin-neutral-400 cursor-not-allowed';
    const defaultClass = 'inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-neutral-200 bg-white text-xs font-semibold text-admin-neutral-700 hover:bg-admin-neutral-50 transition-colors duration-150';
    const activeClass = 'inline-flex items-center justify-center min-w-[36px] h-9 px-3 rounded-lg border border-admin-primary bg-admin-primary text-xs font-semibold text-white shadow-sm';

    let navHtml = '';

    if (currentActivitiesPage === 1) {
        navHtml += `<span class="${disabledClass}">Prev</span>`;
    } else {
        navHtml += `<button type="button" class="${defaultClass}" onclick="goToActivitiesPage(${currentActivitiesPage - 1})">Prev</button>`;
    }

    const pageItems = buildActivitiesPageItems(totalPages, currentActivitiesPage);
    pageItems.forEach((item) => {
        if (item === '...') {
            navHtml += '<span class="inline-flex items-center justify-center min-w-[36px] h-9 px-3 text-xs font-semibold text-admin-neutral-400">...</span>';
            return;
        }

        if (item === currentActivitiesPage) {
            navHtml += `<span class="${activeClass}" aria-current="page">${item}</span>`;
            return;
        }

        navHtml += `<button type="button" class="${defaultClass}" aria-label="Go to page ${item}" onclick="goToActivitiesPage(${item})">${item}</button>`;
    });

    if (currentActivitiesPage >= totalPages) {
        navHtml += `<span class="${disabledClass}">Next</span>`;
    } else {
        navHtml += `<button type="button" class="${defaultClass}" onclick="goToActivitiesPage(${currentActivitiesPage + 1})">Next</button>`;
    }

    nav.innerHTML = navHtml;
    wrapper.classList.remove('hidden');
    wrapper.classList.add('flex');
}

function goToActivitiesPage(page) {
    const totalPages = getActivitiesTotalPages(filteredAudits.length);
    const nextPage = Math.min(Math.max(1, page), totalPages);
    if (nextPage === currentActivitiesPage) {
        return;
    }

    currentActivitiesPage = nextPage;
    renderActivitiesTable(filteredAudits);
}

function setActivitiesLoadingState() {
    const tbody = document.getElementById('activitiesTableBody');
    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="py-12 px-4 text-center text-admin-neutral-500">
                <div class="inline-flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                    <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                    <span class="h-2 w-2 rounded-full bg-admin-primary animate-pulse"></span>
                </div>
                <p class="mt-3 text-sm">Loading recent activities...</p>
            </td>
        </tr>
    `;
    renderActivitiesPagination(0);
}

function setActivitiesErrorState() {
    const tbody = document.getElementById('activitiesTableBody');
    if (!tbody) return;

    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="py-12 px-4 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-admin-danger-light text-admin-danger">
                    <i class="fas fa-triangle-exclamation text-base" aria-hidden="true"></i>
                </div>
                <p class="mt-3 font-semibold text-admin-neutral-900">Could not load activities</p>
                <p class="text-sm text-admin-neutral-500">Please try again in a moment.</p>
            </td>
        </tr>
    `;
    renderActivitiesPagination(0);
}

function populateActivitiesFilterOptions() {
    const moduleValues = Array.from(
        new Set(
            allAudits
                .map((audit) => activityText(audit.module))
                .filter((value) => value.length > 0)
        )
    ).sort((a, b) => a.localeCompare(b));

    const actionValues = Array.from(
        new Set(
            allAudits
                .map((audit) => activityText(audit.action))
                .filter((value) => value.length > 0)
        )
    ).sort((a, b) => a.localeCompare(b));

    setActivitiesSelectOptions(document.getElementById('activitiesModuleFilter'), moduleValues, 'All Modules');
    setActivitiesSelectOptions(document.getElementById('activitiesActionFilter'), actionValues, 'All Actions');
}

function renderActivitiesTable(audits) {
    const tbody = document.getElementById('activitiesTableBody');
    if (!tbody) return;

    const sortedAudits = [...audits].sort((a, b) => {
        let aVal;
        let bVal;

        if (currentSortBy === 'created_at') {
            aVal = new Date(a.created_at || 0).getTime();
            bVal = new Date(b.created_at || 0).getTime();
        } else {
            aVal = activityText(a[currentSortBy]).toLowerCase();
            bVal = activityText(b[currentSortBy]).toLowerCase();
        }

        if (aVal === bVal) {
            return 0;
        }

        if (currentSortDirection === 'asc') {
            return aVal > bVal ? 1 : -1;
        }

        return aVal < bVal ? 1 : -1;
    });

    const totalItems = sortedAudits.length;
    renderActivitiesPagination(totalItems);

    if (totalItems === 0) {
        const hasFilters = Boolean(
            activityText(document.getElementById('activitiesSearchInput')?.value).length ||
            activityText(document.getElementById('activitiesModuleFilter')?.value).length ||
            activityText(document.getElementById('activitiesActionFilter')?.value).length
        );

        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-12 px-4 text-center">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-admin-neutral-100 text-admin-neutral-400">
                        <i class="fas ${hasFilters ? 'fa-filter' : 'fa-clock-rotate-left'} text-base" aria-hidden="true"></i>
                    </div>
                    <p class="mt-3 font-semibold text-admin-neutral-900">${hasFilters ? 'No matching activities' : 'No recent activities found'}</p>
                    <p class="text-sm text-admin-neutral-500">${hasFilters ? 'Try changing your search or filters.' : 'Activities will appear here as users interact with the system.'}</p>
                </td>
            </tr>
        `;
        return;
    }

    const totalPages = getActivitiesTotalPages(totalItems);
    if (currentActivitiesPage > totalPages) {
        currentActivitiesPage = totalPages;
    }

    const startIndex = (currentActivitiesPage - 1) * activitiesPerPage;
    const pagedAudits = sortedAudits.slice(startIndex, startIndex + activitiesPerPage);

    let rowsHtml = '';

    pagedAudits.forEach((audit) => {
        const userName = activityText(audit.user && audit.user.name ? audit.user.name : 'System') || 'System';
        const action = activityText(audit.action) || 'N/A';
        const moduleName = activityText(audit.module) || 'General';
        const description = activityText(audit.description) || 'No description provided.';
        const dateInfo = formatActivityDate(audit.created_at);
        const actionBadgeClass = getActionBadgeClass(action);

        rowsHtml += `
            <tr class="border-b border-admin-neutral-100 last:border-b-0 hover:bg-admin-neutral-50 transition-colors duration-admin">
                <td class="py-3 px-4 align-top whitespace-nowrap overflow-hidden text-ellipsis">
                    <p class="font-semibold text-admin-neutral-900 truncate">${escapeHtml(userName)}</p>
                </td>
                <td class="py-3 px-4 align-top whitespace-nowrap overflow-hidden text-ellipsis">
                    <span class="inline-flex max-w-full items-center rounded-full border px-2.5 py-1 text-xs font-semibold truncate ${actionBadgeClass}">
                        ${escapeHtml(action)}
                    </span>
                </td>
                <td class="py-3 px-4 align-top whitespace-nowrap overflow-hidden text-ellipsis">
                    <span class="inline-flex max-w-full items-center rounded-full border border-admin-neutral-200 bg-admin-primary-light px-2.5 py-1 text-xs font-semibold text-admin-primary truncate">
                        ${escapeHtml(moduleName)}
                    </span>
                </td>
                <td class="py-3 px-4 align-top">
                    <p class="text-admin-neutral-700 whitespace-normal break-words max-w-2xl">${escapeHtml(description)}</p>
                </td>
                <td class="py-3 px-4 align-top whitespace-nowrap">
                    <p class="text-admin-neutral-800">${escapeHtml(dateInfo.full)}</p>
                    <p class="text-xs text-admin-neutral-500">${escapeHtml(dateInfo.relative)}</p>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = rowsHtml;
}

function applyActivitiesFilters(resetPage = false) {
    const searchInput = document.getElementById('activitiesSearchInput');
    const moduleFilter = document.getElementById('activitiesModuleFilter');
    const actionFilter = document.getElementById('activitiesActionFilter');
    const clearButton = document.getElementById('activitiesClearSearch');

    const query = activityText(searchInput?.value).toLowerCase();
    const moduleValue = activityText(moduleFilter?.value);
    const actionValue = activityText(actionFilter?.value);

    filteredAudits = allAudits.filter((audit) => {
        const userName = activityText(audit.user && audit.user.name ? audit.user.name : 'System');
        const action = activityText(audit.action);
        const moduleName = activityText(audit.module);
        const description = activityText(audit.description);
        const dateText = audit.created_at ? new Date(audit.created_at).toLocaleString() : '';

        const haystack = `${userName} ${action} ${moduleName} ${description} ${dateText}`.toLowerCase();
        const matchesQuery = !query || haystack.includes(query);
        const matchesModule = !moduleValue || moduleName === moduleValue;
        const matchesAction = !actionValue || action === actionValue;

        return matchesQuery && matchesModule && matchesAction;
    });

    if (resetPage) {
        currentActivitiesPage = 1;
    }

    renderActivitiesTable(filteredAudits);
    updateActivitiesCounters(allAudits.length, filteredAudits.length);

    if (clearButton) {
        clearButton.classList.toggle('hidden', !query.length);
    }
}

async function openRecentActivitiesModal() {
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'recentActivities' }));
    await loadActivities();
}

async function loadActivities() {
    setActivitiesLoadingState();
    updateActivitiesCounters(0, 0);
    filteredAudits = [];
    currentActivitiesPage = 1;

    try {
        const response = await fetch('{{ url("superadmin/recent-audits") }}');
        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        const data = await response.json();
        allAudits = Array.isArray(data) ? data : [];
        populateActivitiesFilterOptions();
        applyActivitiesFilters(true);
        updateActivitiesSortIndicators();
    } catch (error) {
        setActivitiesErrorState();
        updateActivitiesCounters(0, 0);
        console.error('Error fetching audits:', error);
    }
}

function initActivitiesControls() {
    const searchInput = document.getElementById('activitiesSearchInput');
    const moduleFilter = document.getElementById('activitiesModuleFilter');
    const actionFilter = document.getElementById('activitiesActionFilter');
    const clearButton = document.getElementById('activitiesClearSearch');
    const resetButton = document.getElementById('activitiesResetFilters');

    if (searchInput && !searchInput.dataset.bound) {
        searchInput.addEventListener('input', () => applyActivitiesFilters(true));
        searchInput.dataset.bound = 'true';
    }

    if (moduleFilter && !moduleFilter.dataset.bound) {
        moduleFilter.addEventListener('change', () => applyActivitiesFilters(true));
        moduleFilter.dataset.bound = 'true';
    }

    if (actionFilter && !actionFilter.dataset.bound) {
        actionFilter.addEventListener('change', () => applyActivitiesFilters(true));
        actionFilter.dataset.bound = 'true';
    }

    if (clearButton && !clearButton.dataset.bound) {
        clearButton.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }
            applyActivitiesFilters(true);
        });
        clearButton.dataset.bound = 'true';
    }

    if (resetButton && !resetButton.dataset.bound) {
        resetButton.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }
            if (moduleFilter) {
                moduleFilter.value = '';
            }
            if (actionFilter) {
                actionFilter.value = '';
            }
            applyActivitiesFilters(true);
        });
        resetButton.dataset.bound = 'true';
    }

    updateActivitiesSortIndicators();
}

function sortBy(column) {
    if (column !== 'created_at') {
        return;
    }

    if (currentSortBy === column) {
        currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        currentSortBy = 'created_at';
        currentSortDirection = 'desc';
    }
    updateActivitiesSortIndicators();
    applyActivitiesFilters();
}
function initUsersPage() {
    const hasSuccess = @json((bool) session('success'));
    if (hasSuccess) {
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'users-success' }));
    }

    const openEditAdmin = @json(old('form_context') === 'edit_admin');
    const editUserId = @json(old('edit_user_id'));
    const openAddAdmin = @json(old('form_context') === 'add_admin');
    const hasAddAdminErrors = @json($errors->has('name') || $errors->has('email'));
    if (openAddAdmin || (!openEditAdmin && hasAddAdminErrors)) {
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'addAdmin' }));
    }

    if (openEditAdmin && editUserId) {
        const editForm = document.getElementById('editUserForm');
        if (editForm) {
            editForm.action = `{{ url('superadmin/users') }}/${editUserId}`;
        }
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'editUser' }));
    }

    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter && !roleFilter.dataset.bound) {
        roleFilter.addEventListener('change', applyUserFilters);
        roleFilter.dataset.bound = 'true';
    }

    if (typeof window.filterTable === 'function') {
        window.filterTable = function(query) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && typeof query === 'string') {
                searchInput.value = query;
            }
            applyUserFilters();
        };
    }

    if (typeof enhanceAdminSelects === 'function') {
        enhanceAdminSelects(document);
    }

    initActivitiesControls();
    updateNameSortIcon();
    sortUserRows();
    applyUserFilters();
}

document.addEventListener('DOMContentLoaded', initUsersPage);
document.addEventListener('livewire:navigated', initUsersPage);
</script>
@endsection
