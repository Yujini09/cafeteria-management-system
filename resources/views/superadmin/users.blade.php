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
                       class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
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

            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600 mr-auto">
                    <x-admin.ui.icon name="fa-user-check" size="xs" />
                    Total Users: {{ $users->total() }}
                </span>
                <x-admin.ui.button.secondary type="button" onclick="openRecentActivitiesModal()">
                    <x-admin.ui.icon name="fa-file-alt" size="sm" />
                    Recent Activities
                </x-admin.ui.button.secondary>
                <x-admin.ui.button.primary type="button" @click="$dispatch('open-admin-modal', 'addAdmin')">
                    <x-admin.ui.icon name="fa-plus" size="sm" />
                    Add Admin
                </x-admin.ui.button.primary>
            </div>
        </div>
    </div>

    <div class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
        <table class="modern-table table-fixed">
            <colgroup>
                <col class="w-14">
                <col class="w-64">
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
                                    <x-admin.ui.icon name="fa-file-alt" size="sm" /> Audit
                                </a>
                            @else
                                <a href="{{ route('superadmin.users.audit', $user) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-warning-light text-admin-warning border border-amber-200 hover:bg-amber-100 transition-colors duration-admin">
                                    <x-admin.ui.icon name="fa-file-alt" size="sm" /> Audit
                                </a>
                            @endif
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" id="deleteForm{{ $user->id }}">
                                @csrf @method('DELETE')
                                <x-admin.ui.button.danger type="button" class="!py-2 !px-3 text-xs" onclick="openDeleteModal({{ $user->id }}, '{{ addslashes(e($user->name)) }}')">
                                    <x-admin.ui.icon name="fa-trash-alt" size="sm" /> Delete
                                </x-admin.ui.button.danger>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 px-4 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-admin-neutral-100 flex items-center justify-center">
                            <x-admin.ui.icon name="fa-exclamation-triangle" class="text-admin-neutral-400 w-8 h-8" />
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

<x-admin.ui.modal name="recentActivities" title="Recent Activities" variant="info" maxWidth="6xl">
    <div id="activitiesTableContainer" class="overflow-auto max-h-[60vh] rounded-admin border border-admin-neutral-200">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr>
                    <th class="bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase">User</th>
                    <th class="bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase cursor-pointer hover:bg-admin-neutral-100" onclick="sortBy('action')">Action</th>
                    <th class="bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase cursor-pointer hover:bg-admin-neutral-100" onclick="sortBy('module')">Module</th>
                    <th class="bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase cursor-pointer hover:bg-admin-neutral-100" onclick="sortBy('description')">Description</th>
                    <th class="bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-3 px-4 border-b border-admin-neutral-200 text-xs uppercase cursor-pointer hover:bg-admin-neutral-100" onclick="sortBy('created_at')">Date</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="5" class="py-8 text-center text-admin-neutral-500">Loading activities...</td></tr>
            </tbody>
        </table>
    </div>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Close</x-admin.ui.button.secondary>
    </x-slot:footer>
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
var currentSortBy = 'created_at';
var currentSortDirection = 'desc';

async function openRecentActivitiesModal() {
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'recentActivities' }));
    await loadActivities();
}

async function loadActivities() {
    const container = document.getElementById('activitiesTableContainer');
    const tbody = container.querySelector('tbody');

    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-admin-neutral-500">Loading activities...</td></tr>';
    }

    try {
        const response = await fetch('{{ url("superadmin/recent-audits") }}');
        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }
        const data = await response.json();
        allAudits = Array.isArray(data) ? data : [];

        if (allAudits.length === 0 && tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-admin-neutral-500">No recent activities found.</td></tr>';
            return;
        }

        renderTable();
    } catch (error) {
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-red-500">Error loading activities.</td></tr>';
        }
        console.error('Error fetching audits:', error);
    }
}

function renderTable() {
    // Sort the audits client-side
    const sortedAudits = [...allAudits].sort((a, b) => {
        let aVal, bVal;

        if (currentSortBy === 'created_at') {
            aVal = new Date(a.created_at || 0);
            bVal = new Date(b.created_at || 0);
        } else {
            aVal = (a[currentSortBy] ?? '').toString().toLowerCase();
            bVal = (b[currentSortBy] ?? '').toString().toLowerCase();
        }

        if (currentSortDirection === 'asc') {
            return aVal < bVal ? -1 : aVal > bVal ? 1 : 0;
        } else {
            return aVal > bVal ? -1 : aVal < bVal ? 1 : 0;
        }
    });

    let tbodyHtml = '';

    if (sortedAudits.length === 0) {
        tbodyHtml = `
            <tr>
                <td colspan="5">
                        <div class="empty-state py-8">
                        <div class="empty-state-icon">
                            <svg class="w-8 h-8 text-admin-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-admin-neutral-900 mb-2">No recent activities found</p>
                        <p class="text-sm text-admin-neutral-500">Activities will appear here as users interact with the system</p>
                    </div>
                </td>
            </tr>
        `;
    } else {
        sortedAudits.forEach(audit => {
            const date = new Date(audit.created_at).toLocaleString();
            tbodyHtml += `
                <tr>
                    <td class="font-semibold text-admin-neutral-900">${audit.user ? audit.user.name : 'Unknown'}</td>
                    <td class="text-admin-neutral-600">${audit.action}</td>
                    <td class="text-admin-neutral-600">${audit.module}</td>
                    <td class="text-admin-neutral-600">${audit.description}</td>
                    <td class="text-admin-neutral-600">${date}</td>
                </tr>
            `;
        });
    }

    document.querySelector('#activitiesTableContainer tbody').innerHTML = tbodyHtml;
}

function sortBy(column) {
    if (currentSortBy === column) {
        currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        currentSortBy = column;
        currentSortDirection = 'asc'; // Default to ascending for new column
    }
    renderTable();
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

    updateNameSortIcon();
    sortUserRows();
    applyUserFilters();
}

document.addEventListener('DOMContentLoaded', initUsersPage);
document.addEventListener('livewire:navigated', initUsersPage);
</script>
@endsection
