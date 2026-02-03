@extends('layouts.sidebar')
@section('page-title', 'Manage Users')

@section('content')
{{-- Design system: admin tokens from tailwind (admin-primary, rounded-admin, etc.). No inline overrides. --}}
<div x-data="{}" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-6 max-w-full overflow-hidden">
    {{-- Success/error shown as toasts from layout; no duplicated inline messages. --}}

    <div class="page-header items-start">
        <div class="header-content">
            <div class="header-icon">
                <x-admin.ui.icon name="fa-users" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">User Management</h1>
                <p class="header-subtitle">Manage admin accounts and access</p>
                <div class="mt-3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                        <x-admin.ui.icon name="fa-user-check" size="xs" />
                        Total Users: {{ $users->total() }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 w-full sm:w-auto">
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

            <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-start sm:justify-end">
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

    <div class="overflow-auto max-h-96 rounded-admin border border-admin-neutral-200">
        <table class="w-full border-collapse text-sm">
            <thead>
                <tr>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide w-14">#</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide">Name</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide">Email</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide">Role</th>
                    <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="hover:bg-admin-neutral-50 transition-colors duration-admin">
                    <td class="text-admin-neutral-500 py-4 px-4 border-b border-admin-neutral-100 font-semibold">
                        {{ ($users->firstItem() ?? 0) + $loop->index }}
                    </td>
                    <td class="font-semibold text-admin-neutral-900 py-4 px-4 border-b border-admin-neutral-100">{{ $user->name }}</td>
                    <td class="text-admin-neutral-600 py-4 px-4 border-b border-admin-neutral-100">{{ $user->email }}</td>
                    <td class="py-4 px-4 border-b border-admin-neutral-100">
                        <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold uppercase {{ $user->role === 'admin' ? 'bg-admin-primary-light text-admin-primary' : 'bg-admin-neutral-100 text-admin-neutral-600' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-4 px-4 border-b border-admin-neutral-100">
                        <div class="flex flex-wrap gap-2">
                            @if($user->role === 'admin')
                                <x-admin.ui.button.secondary type="button" class="!py-2 !px-3 text-xs" onclick="openEditModal({{ $user->id }}, '{{ addslashes(e($user->name)) }}', '{{ addslashes(e($user->email)) }}')">
                                    <x-admin.ui.icon name="fa-pen" size="sm" /> Edit
                                </x-admin.ui.button.secondary>
                                <a href="{{ route('superadmin.users.audit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-warning-light text-admin-warning border border-amber-200 hover:bg-amber-100 transition-colors duration-admin">
                                    <x-admin.ui.icon name="fa-file-alt" size="sm" /> Audit
                                </a>
                            @else
                                <a href="{{ route('superadmin.users.audit', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-admin text-xs font-semibold bg-admin-warning-light text-admin-warning border border-amber-200 hover:bg-amber-100 transition-colors duration-admin">
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
<x-admin.ui.modal name="addAdmin" title="Add New Admin" variant="confirmation" maxWidth="md">
    <form method="POST" action="{{ route('superadmin.users.store') }}" id="addAdminForm">
        @csrf
        <div class="space-y-4">
            <x-admin.forms.input name="name" label="Full Name" required />
            <x-admin.forms.input name="email" label="Email Address" type="email" required />
            <x-admin.forms.password name="password" label="Password" :showRequirements="true" required />
            <x-admin.forms.password name="password_confirmation" label="Confirm Password" required />
        </div>
    </form>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" @click="$dispatch('close-admin-modal', 'addAdmin'); $dispatch('open-admin-modal', 'createAdminConfirm')">Create Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="editUser" title="Edit Admin" variant="confirmation" maxWidth="md">
    <form id="editUserForm" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <x-admin.forms.input name="name" id="editName" label="Full Name" required />
            <x-admin.forms.input name="email" id="editEmail" label="Email Address" type="email" required />
        </div>
    </form>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" @click="$dispatch('close-admin-modal', 'editUser'); $dispatch('open-admin-modal', 'updateAdminConfirm')">Update Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="deleteConfirm" title="Delete User" variant="error" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">
        Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-admin-neutral-900"></span>?
        This action cannot be undone.
    </p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.danger type="button" onclick="confirmDelete()">Delete</x-admin.ui.button.danger>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="createAdminConfirm" title="Create New Admin" variant="confirmation" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">Are you sure you want to create this admin user? They will have administrative privileges.</p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'createAdminConfirm'); $dispatch('open-admin-modal', 'addAdmin')">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" onclick="document.getElementById('addAdminForm').submit()">Create Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="updateAdminConfirm" title="Update Admin" variant="confirmation" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">Are you sure you want to save the changes to this admin user?</p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" onclick="document.getElementById('editUserForm').submit()">Update Admin</x-admin.ui.button.primary>
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
    document.getElementById('editUserForm').action = `{{ url('superadmin/users') }}/${id}`;
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'editUser' }));
}

let deleteUserId = null;

function openDeleteModal(userId, userName) {
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'deleteConfirm' }));
}

function confirmDelete() {
    if (deleteUserId) {
        document.getElementById('deleteForm' + deleteUserId).submit();
    }
}

let allAudits = [];
let currentSortBy = 'created_at';
let currentSortDirection = 'desc';

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

function getSortIcon(column) {
    if (currentSortBy !== column) return '';
    return currentSortDirection === 'asc' ? '▲' : '▼';
}
</script>
@endsection
