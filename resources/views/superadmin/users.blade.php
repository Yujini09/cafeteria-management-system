@extends('layouts.sidebar')
@section('page-title', 'Manage Users')
@php
    $disableAdminSuccessToast = true;
@endphp

@section('content')
@php
    $hasActiveUserFilters = filled($search ?? '') || filled($roleFilter ?? '');
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
</style>
{{-- Design system: admin tokens from tailwind (admin-primary, rounded-admin, etc.). No inline overrides. --}}
<div x-data="{}" class="admin-page-shell bg-white rounded-admin-lg shadow-admin border border-admin-neutral-200 border-t-4 border-t-admin-primary p-4 sm:p-5 lg:p-6 max-w-full overflow-hidden flex flex-col">
    {{-- Success handled via success modal; no duplicate inline messages. --}}

    <form method="GET" action="{{ route('superadmin.users') }}" id="usersFiltersForm">
        <input type="hidden" name="created_sort" value="{{ $createdSort }}">
        <button type="submit" class="sr-only">Apply user filters</button>

        <div class="page-header items-start flex-col justify-start gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="header-content w-full min-w-0">
                <div class="header-icon">
                    <x-admin.ui.icon name="fa-users" style="fas" class="text-white w-6 h-6" />
                </div>
                <div class="header-text min-w-0">
                    <h1 class="header-title">Manage Users</h1>
                    <p class="header-subtitle">Manage admin accounts and access</p>
                </div>
            </div>

            <div class="header-actions w-full lg:w-auto flex flex-col items-stretch lg:items-end">
                <div class="relative w-full sm:w-64 md:w-72">
                    <input type="text"
                           inputmode="search"
                           autocomplete="off"
                           id="searchInput"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search users..."
                           class="admin-search-input w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 pl-10 pr-10 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                           aria-label="Search users">
                    <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-admin-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @if($hasActiveUserFilters)
                        <a href="{{ route('superadmin.users', ['created_sort' => $createdSort]) }}"
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-admin-neutral-400 hover:text-admin-neutral-600"
                           aria-label="Clear search and filters">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="inline-flex items-center justify-center text-center gap-2 rounded-full border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">
                <x-admin.ui.icon name="fa-user-check" size="xs" />
                Total Users: {{ $users->total() }}
            </span>
            <div class="flex w-full sm:w-auto sm:justify-end">
                <x-admin.ui.button.secondary type="button" class="w-full justify-center sm:w-auto" onclick="openRecentActivitiesModal()">
                    <x-admin.ui.icon name="fa-file-lines" size="sm" />
                    Recent Activities
                </x-admin.ui.button.secondary>
            </div>
        </div>

        <div class="rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-5 mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <label for="roleFilter" class="text-sm font-semibold text-admin-neutral-700">Filter by Role</label>
                    <div class="w-full sm:w-64">
                        <select name="role" id="roleFilter" class="admin-select w-full" data-admin-select="true">
                            <option value="" @selected($roleFilter === '')>All Roles</option>
                            <option value="admin" @selected($roleFilter === 'admin')>Admin</option>
                            <option value="customer" @selected($roleFilter === 'customer')>Customer</option>
                        </select>
                    </div>
                </div>
                <div class="flex w-full sm:w-auto sm:justify-end">
                    <x-admin.ui.button.primary type="button" @click="$dispatch('open-admin-modal', 'addAdmin')">
                        <x-admin.ui.icon name="fa-plus" size="sm" />
                        Add Admin
                    </x-admin.ui.button.primary>
                </div>
            </div>
        </div>
    </form>

    <div id="usersTableHost" class="table-view-overlay-host">
        <div id="usersTableScroll" class="flex-1 min-h-0 overflow-auto modern-scrollbar rounded-admin border border-admin-neutral-200">
            <table class="modern-table table-fixed min-w-[68rem]">
                <colgroup>
                    <col class="w-14">
                    <col class="w-48">
                    <col class="w-72">
                    <col class="w-40">
                    <col class="w-36">
                    <col class="w-44">
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
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Role</th>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Status</th>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wider">
                            <a href="{{ request()->fullUrlWithQuery(['created_sort' => $createdSort === 'asc' ? 'desc' : 'asc', 'page' => null]) }}"
                               class="group inline-flex items-center gap-2 hover:text-gray-700"
                               aria-label="Sort by created date">
                                <span>Created</span>
                                <x-admin.ui.icon name="{{ $createdSort === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="fas" size="sm" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                @forelse($users as $user)
                    @php
                        $userPayload = json_encode([
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'can_edit' => $user->role === 'admin',
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
                    @endphp
                    <tr class="hover:bg-admin-neutral-50 transition-colors duration-admin"
                        data-user-row="true"
                        data-user="{{ $userPayload }}"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ strtolower($user->name) }}"
                        data-user-role="{{ $user->role_filter_value }}"
                        data-user-status="{{ strtolower($user->account_status_label) }}">
                        <td class="text-admin-neutral-500 py-4 px-4 border-b border-admin-neutral-100 font-semibold">
                            {{ ($users->firstItem() ?? 0) + $loop->index }}
                        </td>
                        <td class="font-semibold text-admin-neutral-900 py-4 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->name }}</td>
                        <td class="text-admin-neutral-600 py-4 px-4 border-b border-admin-neutral-100 whitespace-nowrap overflow-hidden text-ellipsis">{{ $user->email }}</td>
                        <td class="py-4 px-4 border-b border-admin-neutral-100">
                            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold uppercase {{ $user->role_filter_value === 'admin' ? 'bg-admin-primary-light text-admin-primary' : 'bg-admin-neutral-100 text-admin-neutral-600' }}">
                                {{ $user->role_label }}
                            </span>
                        </td>
                        <td class="py-4 px-4 border-b border-admin-neutral-100">
                            <span class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold uppercase {{ $user->isPendingAccount() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $user->account_status_label }}
                            </span>
                        </td>
                        <td class="py-4 px-4 border-b border-admin-neutral-100 text-sm text-admin-neutral-600 whitespace-nowrap">
                            {{ $user->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="hidden">
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" id="deleteForm{{ $user->id }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-4 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-admin-neutral-100 flex items-center justify-center">
                                <x-admin.ui.icon name="fa-triangle-exclamation" class="text-admin-neutral-400 w-8 h-8" />
                            </div>
                            @if($hasActiveUserFilters)
                                <p class="font-semibold text-admin-neutral-900 mb-1">No users match the current search or filters</p>
                                <p class="text-sm text-admin-neutral-500">Try adjusting the search term or role filter.</p>
                            @else
                                <p class="font-semibold text-admin-neutral-900 mb-1">No users found</p>
                                <p class="text-sm text-admin-neutral-500">Start by adding your first user to the system</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="usersFloatingActions" class="table-floating-actions" aria-hidden="true">
            <button id="usersFloatingEditBtn"
                    type="button"
                    class="table-floating-action-btn table-floating-action-edit"
                    data-user=""
                    onclick="handleFloatingUserEdit(this)"
                    aria-label="Edit user">
                <x-admin.ui.icon name="fa-pen" style="fas" size="sm" class="text-white" />
                Edit
            </button>
            <button id="usersFloatingDeleteBtn"
                    type="button"
                    class="table-floating-action-btn table-floating-action-delete"
                    data-user=""
                    onclick="handleFloatingUserDelete(this)"
                    aria-label="Delete user">
                <x-admin.ui.icon name="fa-trash-can" style="fas" size="sm" class="text-white" />
                Delete
            </button>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links('components.pagination') }}
        </div>
    @endif
</div>

{{-- Unified admin modals: overlay blur, ESC/click-outside, body scroll lock. --}}
<x-success-modal name="users-success" title="Admin Created" maxWidth="sm" overlayClass="bg-admin-neutral-900/50" autoCloseMs="3000">
    <p id="usersSuccessMessage" class="text-sm text-admin-neutral-600">{{ session('success') ?? 'Temporary credentials were sent by email. The account becomes active when the Sign In email link is opened.' }}</p>
</x-success-modal>

<x-admin.ui.modal name="addAdmin" title="Add New Admin" variant="confirmation" maxWidth="md">
    <form method="POST" action="{{ route('superadmin.users.store') }}" id="addAdminForm" data-action-loading onsubmit="event.preventDefault(); submitAddAdminForm(document.getElementById('createAdminSubmitButton'));">
        @csrf
        <input type="hidden" name="form_context" value="add_admin">
        @php(
            $showAddAdminInlineStatus = session('error')
                && old('form_context') === 'add_admin'
                && !in_array(session('error_code'), ['email_not_found', 'email_check_unavailable'], true)
        )
        <div class="space-y-4">
            <div
                id="addAdminInlineStatus"
                class="rounded-admin border border-admin-danger bg-admin-danger-light px-3 py-2 text-sm text-admin-danger @unless($showAddAdminInlineStatus) hidden @endunless"
                role="alert"
            >
                @if($showAddAdminInlineStatus)
                    {{ session('error') }}
                @endif
            </div>

            <div class="rounded-admin border border-amber-200 bg-admin-warning-light px-3 py-2 flex items-start gap-2">
                <x-admin.ui.icon name="fa-triangle-exclamation" class="mt-0.5 w-4 h-4 text-admin-warning shrink-0" />
                <p class="text-sm text-admin-warning">
                    A temporary password and account details will be sent to this email address. The account becomes active when the admin opens the Sign In link from the email.
                </p>
            </div>

            <div class="space-y-1">
                <label for="addAdminName" class="block text-sm font-medium text-admin-neutral-700">
                    Full Name
                    <span class="text-admin-danger">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="addAdminName"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="w-full rounded-admin border px-admin-input py-2.5 text-sm transition-colors duration-admin focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' : 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20' }}"
                >
                <p
                    id="addAdminNameError"
                    class="min-h-[1.25rem] text-sm {{ $errors->has('name') ? 'text-admin-danger' : 'text-admin-neutral-500' }}"
                    role="alert"
                >
                    @error('name')
                        {{ $message }}
                    @else
                        &nbsp;
                    @enderror
                </p>
            </div>

            <div class="space-y-1">
                <label for="addAdminEmail" class="block text-sm font-medium text-admin-neutral-700">
                    Email Address
                    <span class="text-admin-danger">*</span>
                </label>
                <input
                    type="email"
                    name="email"
                    id="addAdminEmail"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-admin border px-admin-input py-2.5 text-sm transition-colors duration-admin focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20' : 'border-admin-neutral-300 focus:border-admin-primary focus:ring-admin-primary/20' }}"
                >
                <p
                    id="addAdminEmailError"
                    class="min-h-[1.25rem] text-sm {{ $errors->has('email') ? 'text-admin-danger' : 'text-admin-neutral-500' }}"
                    role="alert"
                >
                    @error('email')
                        {{ $message }}
                    @else
                        &nbsp;
                    @enderror
                </p>
            </div>
        </div>
    </form>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" id="addAdminCancelButton" @click="show = false">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" id="createAdminSubmitButton" onclick="submitAddAdminForm(this)" data-loading-text="Creating account...">Create Admin</x-admin.ui.button.primary>
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

<x-admin.ui.modal name="updateAdminConfirm" title="Update Admin" variant="confirmation" maxWidth="md">
    <p class="text-admin-neutral-600 text-sm">Are you sure you want to save the changes to this admin user?</p>
    <x-slot:footer>
        <x-admin.ui.button.secondary type="button" @click="$dispatch('close-admin-modal', 'updateAdminConfirm')">Cancel</x-admin.ui.button.secondary>
        <x-admin.ui.button.primary type="button" onclick="submitEditUserForm(this)" data-loading-text="Updating Admin...">Update Admin</x-admin.ui.button.primary>
    </x-slot:footer>
</x-admin.ui.modal>

<x-admin.ui.modal name="recentActivities" title="Audit Feed" icon="fa-clock-rotate-left" iconStyle="fas" variant="info" maxWidth="4xl">
    <button type="button"
            class="absolute top-4 right-4 rounded-full p-1.5 text-admin-neutral-400 hover:bg-admin-neutral-100 hover:text-admin-neutral-600 transition-colors duration-admin"
            @click="$dispatch('close-admin-modal', 'recentActivities')"
            aria-label="Close recent activities modal">
        <x-admin.ui.icon name="fa-xmark" size="sm" />
    </button>
    <div class="flex h-[calc(100vh-12rem)] max-h-[82vh] min-h-0 flex-col gap-4 overflow-y-auto pr-1 modern-scrollbar">
        <div class="shrink-0 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-sm text-admin-neutral-700">Review recent actions across modules and admin users.</p>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-admin-neutral-200 bg-white px-3 py-1.5 text-xs font-semibold text-admin-neutral-600">
                        Total
                        <span id="activitiesTotalCount" class="text-admin-neutral-900">0</span>
                    </span>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3">
                <div class="relative">
                    <input type="text"
                           inputmode="search"
                           autocomplete="off"
                           id="activitiesSearchInput"
                           placeholder="Search user, action, description..."
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

                <div class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-[minmax(12rem,14rem)_minmax(10rem,1fr)_minmax(10rem,1fr)_auto] xl:items-end">
                    <div class="flex flex-col gap-1">
                        <label for="activitiesActionFilter" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Action</label>
                        <select id="activitiesActionFilter" class="admin-select w-full" data-admin-select="true" aria-label="Filter activities by action">
                            <option value="">All Actions</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="activitiesDateFrom" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date from</label>
                        <input type="date"
                               id="activitiesDateFrom"
                               class="w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 px-3 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                               aria-label="Activities date from">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="activitiesDateTo" class="text-xs font-semibold uppercase tracking-wide text-admin-neutral-600">Date to</label>
                        <input type="date"
                               id="activitiesDateTo"
                               class="w-full rounded-admin border border-admin-neutral-300 bg-white py-2.5 px-3 text-sm text-admin-neutral-700 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                               aria-label="Activities date to">
                    </div>

                    <div class="flex items-end">
                        <x-admin.ui.button.secondary type="button" id="activitiesResetFilters" class="w-full sm:w-auto sm:shrink-0">Reset</x-admin.ui.button.secondary>
                    </div>
                </div>
            </div>
        </div>

        <div id="activitiesTableContainer" class="min-w-0 shrink-0 overflow-x-auto rounded-admin border border-admin-neutral-200 bg-white modern-scrollbar">
            <table class="modern-table table-fixed w-full min-w-[58rem]">
                <colgroup>
                    <col class="w-56">
                    <col class="w-48">
                    <col class="w-[30rem]">
                    <col class="w-64">
                </colgroup>
                <thead>
                    <tr>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">User</th>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Action</th>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">Description</th>
                        <th class="sticky top-0 bg-admin-neutral-50 font-semibold text-admin-neutral-700 text-left py-4 px-4 border-b border-admin-neutral-200 text-xs uppercase tracking-wide whitespace-nowrap overflow-hidden text-ellipsis">
                            <button type="button" class="group inline-flex items-center gap-2" onclick="sortBy('created_at')" aria-label="Sort by date">
                                <span>Date</span>
                                <x-admin.ui.icon id="activitiesSortIconDate" name="fa-arrow-down" size="xs" class="text-admin-neutral-400 group-hover:text-admin-neutral-600 transition-colors duration-admin" />
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody id="activitiesTableBody">
                    <tr>
                        <td colspan="4" class="py-10 text-center text-admin-neutral-500">Loading activities...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="activitiesPagination" class="hidden shrink-0 flex-col gap-3 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-3 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p id="activitiesPaginationInfo" class="text-center text-xs leading-relaxed text-admin-neutral-500 sm:text-left"></p>
            <nav id="activitiesPaginationNav" role="navigation" aria-label="Recent activities pagination" class="flex w-full flex-wrap items-center justify-center gap-1 sm:w-auto sm:justify-end"></nav>
        </div>
    </div>
</x-admin.ui.modal>

<script>
var createAdminSubmitting = false;
var addAdminFieldConfig = {
    name: { inputId: 'addAdminName', errorId: 'addAdminNameError' },
    email: { inputId: 'addAdminEmail', errorId: 'addAdminEmailError' },
};
var addAdminNeutralInputClasses = ['border-admin-neutral-300', 'focus:border-admin-primary', 'focus:ring-admin-primary/20'];
var addAdminInvalidInputClasses = ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20'];
var addAdminEmailCheckState = {
    timer: null,
    requestId: 0,
};

function normalizeAddAdminEmail(value) {
    return (value || '').toString().trim().toLowerCase();
}

function isValidEmailFormat(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalizeAddAdminEmail(value));
}

async function verifyAddAdminEmailExistsRealtime(force = false) {
    const form = document.getElementById('addAdminForm');
    const emailInput = document.getElementById('addAdminEmail');
    const csrfToken = form ? form.querySelector('input[name="_token"]')?.value : '';
    const email = normalizeAddAdminEmail(emailInput ? emailInput.value : '');

    if (!emailInput) {
        return { ok: true, errorCode: '', message: '' };
    }

    if (!email.length || !isValidEmailFormat(email)) {
        return { ok: true, errorCode: '', message: '' };
    }

    const requestId = addAdminEmailCheckState.requestId + 1;
    addAdminEmailCheckState.requestId = requestId;

    const payload = new FormData();
    payload.append('_token', csrfToken || '');
    payload.append('email', email);

    try {
        const response = await fetch('{{ route('superadmin.users.check-email') }}', {
            method: 'POST',
            body: payload,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        let data = {};
        try {
            data = await response.json();
        } catch (jsonError) {
            data = {};
        }

        if (requestId !== addAdminEmailCheckState.requestId) {
            return { ok: false, errorCode: 'stale', message: '' };
        }

        if (response.ok) {
            resetAddAdminFieldError('email');
            return { ok: true, errorCode: '', message: '' };
        }

        const responseErrorCode = (data && typeof data.error_code === 'string') ? data.error_code : '';
        const validationMessage = data && data.errors && Array.isArray(data.errors.email) && data.errors.email.length
            ? data.errors.email[0]
            : null;
        const errorMessage = validationMessage
            || (data && typeof data.message === 'string' && data.message.trim().length ? data.message.trim() : 'Email address/account could not be verified.');

        if (force || responseErrorCode === 'email_not_found') {
            setAddAdminFieldError('email', errorMessage);
        }

        return { ok: false, errorCode: responseErrorCode || 'email_check_failed', message: errorMessage };
    } catch (error) {
        if (requestId !== addAdminEmailCheckState.requestId) {
            return { ok: false, errorCode: 'stale', message: '' };
        }
        if (force) {
            setAddAdminFieldError('email', 'Could not verify this email address right now. Please input a valid email and try again.');
        }
        return { ok: false, errorCode: 'email_check_unavailable', message: 'Could not verify this email address right now. Please input a valid email and try again.' };
    }
}

function bindAddAdminEmailExistenceValidation() {
    const emailInput = document.getElementById('addAdminEmail');
    if (!emailInput || emailInput.dataset.existenceBound === 'true') return;

    emailInput.addEventListener('input', () => {
        setAddAdminInlineStatus('');

        if (addAdminEmailCheckState.timer) {
            clearTimeout(addAdminEmailCheckState.timer);
            addAdminEmailCheckState.timer = null;
        }

        const email = normalizeAddAdminEmail(emailInput.value);
        if (!email.length) {
            resetAddAdminFieldError('email');
            return;
        }

        if (!isValidEmailFormat(email)) {
            resetAddAdminFieldError('email');
            return;
        }

        addAdminEmailCheckState.timer = setTimeout(() => {
            verifyAddAdminEmailExistsRealtime(false);
        }, 450);
    });

    emailInput.addEventListener('blur', () => {
        verifyAddAdminEmailExistsRealtime(true);
    });

    emailInput.dataset.existenceBound = 'true';
}

function lockCreateAdminModals(locked) {
    const isLocked = Boolean(locked);
    ['addAdmin'].forEach((name) => {
        window.dispatchEvent(new CustomEvent('admin-modal-lock', {
            detail: { name, locked: isLocked },
        }));
    });
}

function setCreateAdminControlsDisabled(disabled) {
    [
        document.getElementById('createAdminSubmitButton'),
        document.getElementById('addAdminCancelButton'),
    ].forEach((button) => {
        if (!button) return;
        button.disabled = Boolean(disabled);
        if (button.disabled) {
            button.setAttribute('aria-disabled', 'true');
        } else {
            button.removeAttribute('aria-disabled');
        }
    });
}

function setAddAdminInlineStatus(message = '') {
    const inlineStatus = document.getElementById('addAdminInlineStatus');
    if (!inlineStatus) return;
    if (!message) {
        inlineStatus.textContent = '';
        inlineStatus.classList.add('hidden');
        return;
    }
    inlineStatus.textContent = message;
    inlineStatus.classList.remove('hidden');
}

function isAddAdminEmailFieldErrorCode(errorCode = '') {
    return errorCode === 'email_not_found' || errorCode === 'email_check_unavailable';
}

function resetAddAdminFieldError(fieldName) {
    const field = addAdminFieldConfig[fieldName];
    if (!field) return;

    const input = document.getElementById(field.inputId);
    const errorEl = document.getElementById(field.errorId);

    if (input) {
        input.classList.remove(...addAdminInvalidInputClasses);
        input.classList.add(...addAdminNeutralInputClasses);
        input.removeAttribute('aria-invalid');
    }

    if (errorEl) {
        errorEl.textContent = '\u00A0';
        errorEl.classList.remove('text-admin-danger');
        errorEl.classList.add('text-admin-neutral-500');
    }
}

function setAddAdminFieldError(fieldName, message) {
    const field = addAdminFieldConfig[fieldName];
    if (!field) return;

    const input = document.getElementById(field.inputId);
    const errorEl = document.getElementById(field.errorId);
    const errorMessage = typeof message === 'string' && message.trim().length
        ? message.trim()
        : 'Please review this field.';

    if (input) {
        input.classList.remove(...addAdminNeutralInputClasses);
        input.classList.add(...addAdminInvalidInputClasses);
        input.setAttribute('aria-invalid', 'true');
    }

    if (errorEl) {
        errorEl.textContent = errorMessage;
        errorEl.classList.remove('text-admin-neutral-500');
        errorEl.classList.add('text-admin-danger');
    }

}

function clearAddAdminFieldErrors() {
    Object.keys(addAdminFieldConfig).forEach((fieldName) => resetAddAdminFieldError(fieldName));
}

function applyAddAdminValidationErrors(errors = {}) {
    const errorEntries = Object.entries(errors || {});
    let firstInvalidInput = null;

    errorEntries.forEach(([fieldName, messages]) => {
        if (!addAdminFieldConfig[fieldName]) return;
        const firstMessage = Array.isArray(messages) && messages.length ? messages[0] : null;
        setAddAdminFieldError(fieldName, firstMessage);

        if (!firstInvalidInput) {
            const inputId = addAdminFieldConfig[fieldName].inputId;
            firstInvalidInput = document.getElementById(inputId);
        }
    });

    if (firstInvalidInput && typeof firstInvalidInput.focus === 'function') {
        firstInvalidInput.focus();
    }
}

function setCreateAdminSubmittingState(submitting, triggerButton = null) {
    createAdminSubmitting = Boolean(submitting);
    lockCreateAdminModals(createAdminSubmitting);
    setCreateAdminControlsDisabled(createAdminSubmitting);
    const submitButton = triggerButton || document.getElementById('createAdminSubmitButton');
    if (!submitButton || !window.cmsActionButtons) return;

    if (createAdminSubmitting) {
        window.cmsActionButtons.start(submitButton, submitButton.dataset.loadingText || 'Creating account...');
        return;
    }

    window.cmsActionButtons.stop(submitButton);
}

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

async function submitAddAdminForm(triggerButton = null) {
    const form = document.getElementById('addAdminForm');
    if (!form || createAdminSubmitting) return;

    if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
        return;
    }

    setAddAdminInlineStatus('');
    clearAddAdminFieldErrors();

    setCreateAdminSubmittingState(true, triggerButton);

    let successMessageToShow = '';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        let payload = {};
        try {
            payload = await response.json();
        } catch (jsonError) {
            payload = {};
        }

        const errorCode = (payload && typeof payload.error_code === 'string')
            ? payload.error_code
            : '';

        if (errorCode === 'email_not_found') {
            const emailNotFoundMessage = (payload && typeof payload.message === 'string' && payload.message.trim().length)
                ? payload.message.trim()
                : 'Email address/account could not be found. Please input a valid email.';
            setAddAdminFieldError('email', emailNotFoundMessage);
            return;
        }

        if (errorCode === 'email_check_unavailable') {
            const unavailableMessage = (payload && typeof payload.message === 'string' && payload.message.trim().length)
                ? payload.message.trim()
                : 'Could not verify this email account in real time. Please input a valid email and try again.';
            setAddAdminFieldError('email', unavailableMessage);
            return;
        }

        if (response.status === 422) {
            const validationErrors = payload.errors || {};
            applyAddAdminValidationErrors(validationErrors);
            const validationMessage = (payload && typeof payload.message === 'string' && payload.message.trim().length)
                ? payload.message.trim()
                : 'Please review the highlighted fields and try again.';
            if (!Object.keys(validationErrors).length) {
                if (isAddAdminEmailFieldErrorCode(errorCode)) {
                    setAddAdminFieldError('email', validationMessage);
                } else {
                    setAddAdminInlineStatus(validationMessage);
                }
            }
            return;
        }

        if (!response.ok) {
            const errorMessage = (payload && typeof payload.message === 'string' && payload.message.trim().length)
                ? payload.message.trim()
                : 'Email failed to send. Please try again.';
            setAddAdminInlineStatus(errorMessage);
            return;
        }

        const successMessage = (payload && typeof payload.message === 'string' && payload.message.trim().length)
            ? payload.message.trim()
            : 'Admin account created successfully. The account will become active when the Sign In link in the email is opened.';

        successMessageToShow = successMessage;
    } catch (error) {
        console.error('Error creating admin:', error);
        const networkMessage = 'Unable to reach the server. Please try again.';
        setAddAdminInlineStatus(networkMessage);
    } finally {
        setCreateAdminSubmittingState(false, triggerButton);
    }

    if (successMessageToShow) {
        const successMessageEl = document.getElementById('usersSuccessMessage');
        if (successMessageEl) {
            successMessageEl.textContent = successMessageToShow;
        }

        window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'addAdmin' }));
        window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'users-success' }));
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
var hideUsersFloatingActions = function() {};

function parseFloatingUserPayload(payload) {
    if (!payload) return null;
    try {
        return JSON.parse(payload);
    } catch (error) {
        return null;
    }
}

function handleFloatingUserEdit(button) {
    const payload = parseFloatingUserPayload(button ? button.dataset.user : '');
    if (!payload || !payload.can_edit) return;
    hideUsersFloatingActions();
    openEditModal(payload.id, payload.name, payload.email);
}

function handleFloatingUserDelete(button) {
    const payload = parseFloatingUserPayload(button ? button.dataset.user : '');
    if (!payload) return;
    hideUsersFloatingActions();
    openDeleteModal(payload.id, payload.name);
}

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
var userSortField = null;

function toggleUserNameSort() {
    if (userSortField !== 'name') {
        userSortField = 'name';
        userSortDirection = 'asc';
    } else {
        userSortDirection = userSortDirection === 'asc' ? 'desc' : 'asc';
    }
    sortUserRows();
    updateNameSortIcon();
}

function sortUserRows() {
    const tbody = document.getElementById('usersTableBody');
    if (!tbody) return;
    hideUsersFloatingActions();

    const rows = Array.from(tbody.querySelectorAll('tr[data-user-row="true"]'));
    if (userSortField !== 'name') {
        return;
    }

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

function initUsersFloatingActions() {
    const host = document.getElementById('usersTableHost');
    const scrollArea = document.getElementById('usersTableScroll');
    const actions = document.getElementById('usersFloatingActions');
    const editBtn = document.getElementById('usersFloatingEditBtn');
    const deleteBtn = document.getElementById('usersFloatingDeleteBtn');
    if (!host || !scrollArea || !actions || !editBtn || !deleteBtn) return;

    let activeRow = null;

    const clearActionData = () => {
        editBtn.dataset.user = '';
        deleteBtn.dataset.user = '';
        editBtn.hidden = false;
    };

    const hideActions = () => {
        activeRow = null;
        clearActionData();
        actions.classList.remove('is-visible');
        actions.setAttribute('aria-hidden', 'true');
    };

    hideUsersFloatingActions = hideActions;

    if (host.dataset.floatingActionsBound === 'true') {
        return;
    }

    host.dataset.floatingActionsBound = 'true';

    const updateActionsPosition = () => {
        if (!activeRow || !scrollArea.contains(activeRow) || activeRow.offsetParent === null) {
            hideActions();
            return;
        }

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

        const userPayload = row.dataset.user || '';
        const parsedUser = parseFloatingUserPayload(userPayload);
        if (!parsedUser) {
            hideActions();
            return;
        }

        activeRow = row;
        editBtn.dataset.user = userPayload;
        deleteBtn.dataset.user = userPayload;
        editBtn.hidden = !parsedUser.can_edit;
        editBtn.style.display = parsedUser.can_edit ? '' : 'none';
        actions.classList.add('is-visible');
        actions.setAttribute('aria-hidden', 'false');
        updateActionsPosition();
    };

    scrollArea.addEventListener('pointermove', (event) => {
        const row = event.target.closest('tr[data-user]')
            || document.elementsFromPoint(event.clientX, event.clientY)
                .find((element) => element instanceof HTMLElement && element.matches('tr[data-user]'));
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
        const row = event.target.closest('tr[data-user]');
        if (row) {
            showForRow(row);
        }
    });
}

var recentAuditsEndpoint = @json(route('superadmin.recent-audits'));
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

function updateActivitiesCounters(total) {
    const totalEl = document.getElementById('activitiesTotalCount');

    if (totalEl) {
        totalEl.textContent = String(total);
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

    const onEachSide = 3;
    const maxVisibleWithoutEllipsis = (onEachSide * 2) + 5;

    if (totalPages <= maxVisibleWithoutEllipsis) {
        return Array.from({ length: totalPages }, (_, index) => index + 1);
    }

    const pages = [];
    for (let page = 1; page <= totalPages; page += 1) {
        if (
            page === 1
            || page === totalPages
            || (page >= currentPage - onEachSide && page <= currentPage + onEachSide)
        ) {
            pages.push(page);
        }
    }

    const items = [];
    pages.forEach((page, index) => {
        const previous = index > 0 ? pages[index - 1] : null;
        if (previous !== null && page - previous > 1) {
            items.push('...');
        }
        items.push(page);
    });

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

    if (totalPages <= 1) {
        wrapper.classList.add('hidden');
        wrapper.classList.remove('flex');
        info.textContent = '';
        nav.innerHTML = '';
        return;
    }

    const firstItem = (currentActivitiesPage - 1) * activitiesPerPage + 1;
    const lastItem = Math.min(firstItem + activitiesPerPage - 1, totalItems);

    info.innerHTML = `Showing <span class="font-semibold text-admin-neutral-700">${firstItem}</span> to <span class="font-semibold text-admin-neutral-700">${lastItem}</span> of <span class="font-semibold text-admin-neutral-700">${totalItems}</span> results`;

    const buttonBaseClass = 'inline-flex h-8 min-w-[32px] items-center justify-center rounded-lg px-2.5 text-[11px] font-semibold sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs';
    const disabledClass = `${buttonBaseClass} border border-admin-neutral-200 bg-admin-neutral-50 text-admin-neutral-400 cursor-not-allowed`;
    const defaultClass = `${buttonBaseClass} border border-admin-neutral-200 bg-white text-admin-neutral-700 transition-colors duration-150 hover:bg-admin-neutral-50`;
    const activeClass = `${buttonBaseClass} border border-admin-primary bg-admin-primary text-white shadow-sm`;

    let navHtml = '';

    if (currentActivitiesPage === 1) {
        navHtml += `<span class="${disabledClass}">&lt;</span>`;
    } else {
        navHtml += `<button type="button" class="${defaultClass}" onclick="goToActivitiesPage(${currentActivitiesPage - 1})" aria-label="Previous page">&lt;</button>`;
    }

    const pageItems = buildActivitiesPageItems(totalPages, currentActivitiesPage);
    pageItems.forEach((item) => {
        if (item === '...') {
            navHtml += '<span class="inline-flex h-8 min-w-[32px] items-center justify-center px-2.5 text-[11px] font-semibold text-admin-neutral-400 sm:h-9 sm:min-w-[36px] sm:px-3 sm:text-xs">...</span>';
            return;
        }

        if (item === currentActivitiesPage) {
            navHtml += `<span class="${activeClass}" aria-current="page">${item}</span>`;
            return;
        }

        navHtml += `<button type="button" class="${defaultClass}" aria-label="Go to page ${item}" onclick="goToActivitiesPage(${item})">${item}</button>`;
    });

    if (currentActivitiesPage >= totalPages) {
        navHtml += `<span class="${disabledClass}">&gt;</span>`;
    } else {
        navHtml += `<button type="button" class="${defaultClass}" onclick="goToActivitiesPage(${currentActivitiesPage + 1})" aria-label="Next page">&gt;</button>`;
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
            <td colspan="4" class="py-12 px-4 text-center text-admin-neutral-500">
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
            <td colspan="4" class="py-12 px-4 text-center">
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
    const actionValues = Array.from(
        new Set(
            allAudits
                .map((audit) => activityText(audit.action))
                .filter((value) => value.length > 0)
        )
    ).sort((a, b) => a.localeCompare(b));

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
            activityText(document.getElementById('activitiesActionFilter')?.value).length ||
            activityText(document.getElementById('activitiesDateFrom')?.value).length ||
            activityText(document.getElementById('activitiesDateTo')?.value).length
        );

        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-12 px-4 text-center">
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
        const description = activityText(audit.description) || 'No description provided.';
        const dateInfo = formatActivityDate(audit.created_at);
        const actionBadgeClass = getActionBadgeClass(action);

        rowsHtml += `
            <tr class="hover:bg-admin-neutral-50 transition-colors duration-admin">
                <td class="py-4 px-4 border-b border-admin-neutral-100 align-top min-w-0">
                    <p class="font-semibold text-admin-neutral-900 whitespace-normal break-words">${escapeHtml(userName)}</p>
                </td>
                <td class="py-4 px-4 border-b border-admin-neutral-100 align-top min-w-0">
                    <span class="inline-flex max-w-full items-center rounded-full border px-2.5 py-1 text-xs font-semibold whitespace-normal break-words text-left leading-snug ${actionBadgeClass}">
                        ${escapeHtml(action)}
                    </span>
                </td>
                <td class="py-4 px-4 border-b border-admin-neutral-100 align-top min-w-0">
                    <p class="text-admin-neutral-700 whitespace-normal break-words">${escapeHtml(description)}</p>
                </td>
                <td class="py-4 px-4 border-b border-admin-neutral-100 align-top whitespace-nowrap">
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
    const actionFilter = document.getElementById('activitiesActionFilter');
    const dateFromInput = document.getElementById('activitiesDateFrom');
    const dateToInput = document.getElementById('activitiesDateTo');
    const clearButton = document.getElementById('activitiesClearSearch');

    const query = activityText(searchInput?.value).toLowerCase();
    const actionValue = activityText(actionFilter?.value);
    const dateFromValue = activityText(dateFromInput?.value);
    const dateToValue = activityText(dateToInput?.value);
    const fromDate = dateFromValue ? new Date(`${dateFromValue}T00:00:00`) : null;
    const toDate = dateToValue ? new Date(`${dateToValue}T23:59:59.999`) : null;

    filteredAudits = allAudits.filter((audit) => {
        const userName = activityText(audit.user && audit.user.name ? audit.user.name : 'System');
        const action = activityText(audit.action);
        const description = activityText(audit.description);
        const dateText = audit.created_at ? new Date(audit.created_at).toLocaleString() : '';
        const auditDate = audit.created_at ? new Date(audit.created_at) : null;

        const haystack = `${userName} ${action} ${description} ${dateText}`.toLowerCase();
        const matchesQuery = !query || haystack.includes(query);
        const matchesAction = !actionValue || action === actionValue;
        const matchesDateFrom = !fromDate || (auditDate && !Number.isNaN(auditDate.getTime()) && auditDate >= fromDate);
        const matchesDateTo = !toDate || (auditDate && !Number.isNaN(auditDate.getTime()) && auditDate <= toDate);

        return matchesQuery && matchesAction && matchesDateFrom && matchesDateTo;
    });

    if (resetPage) {
        currentActivitiesPage = 1;
    }

    renderActivitiesTable(filteredAudits);
    updateActivitiesCounters(allAudits.length);

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
    updateActivitiesCounters(0);
    filteredAudits = [];
    currentActivitiesPage = 1;

    try {
        const response = await fetch(recentAuditsEndpoint, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
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
        updateActivitiesCounters(0);
        console.error('Error fetching audits:', error);
    }
}

function initActivitiesControls() {
    const searchInput = document.getElementById('activitiesSearchInput');
    const actionFilter = document.getElementById('activitiesActionFilter');
    const dateFromInput = document.getElementById('activitiesDateFrom');
    const dateToInput = document.getElementById('activitiesDateTo');
    const clearButton = document.getElementById('activitiesClearSearch');
    const resetButton = document.getElementById('activitiesResetFilters');

    if (searchInput && !searchInput.dataset.bound) {
        searchInput.addEventListener('input', () => applyActivitiesFilters(true));
        searchInput.dataset.bound = 'true';
    }

    if (actionFilter && !actionFilter.dataset.bound) {
        actionFilter.addEventListener('change', () => applyActivitiesFilters(true));
        actionFilter.dataset.bound = 'true';
    }

    if (dateFromInput && !dateFromInput.dataset.bound) {
        dateFromInput.addEventListener('change', () => applyActivitiesFilters(true));
        dateFromInput.dataset.bound = 'true';
    }

    if (dateToInput && !dateToInput.dataset.bound) {
        dateToInput.addEventListener('change', () => applyActivitiesFilters(true));
        dateToInput.dataset.bound = 'true';
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
            if (actionFilter) {
                actionFilter.value = '';
            }
            if (dateFromInput) {
                dateFromInput.value = '';
            }
            if (dateToInput) {
                dateToInput.value = '';
            }
            applyActivitiesFilters(true);
        });
        resetButton.dataset.bound = 'true';
    }

    if (!window.__activitiesPaginationResizeBound) {
        const mediaQuery = window.matchMedia('(max-width: 639px)');
        let wasSmallScreen = mediaQuery.matches;
        window.addEventListener('resize', () => {
            if (mediaQuery.matches === wasSmallScreen) {
                return;
            }

            wasSmallScreen = mediaQuery.matches;
            renderActivitiesTable(filteredAudits);
        });
        window.__activitiesPaginationResizeBound = 'true';
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
    setCreateAdminSubmittingState(false);
    setAddAdminInlineStatus('');
    bindAddAdminEmailExistenceValidation();

    const sessionSuccessMessage = @json(session('success'));
    const successMessage = (typeof sessionSuccessMessage === 'string' && sessionSuccessMessage.trim().length)
        ? sessionSuccessMessage.trim()
        : '';

    if (successMessage) {
        const successMessageEl = document.getElementById('usersSuccessMessage');
        if (successMessageEl) {
            successMessageEl.textContent = successMessage;
        }
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

    const filtersForm = document.getElementById('usersFiltersForm');
    const searchInput = document.getElementById('searchInput');
    if (filtersForm && searchInput && !searchInput.dataset.bound) {
        let submitTimer = null;
        const submitFilters = () => {
            hideUsersFloatingActions();
            filtersForm.requestSubmit();
        };

        searchInput.addEventListener('input', () => {
            window.clearTimeout(submitTimer);
            submitTimer = window.setTimeout(submitFilters, 300);
        });

        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                window.clearTimeout(submitTimer);
                submitFilters();
            }
        });

        searchInput.dataset.bound = 'true';
    }

    const roleFilter = document.getElementById('roleFilter');
    if (filtersForm && roleFilter && !roleFilter.dataset.bound) {
        roleFilter.addEventListener('change', () => {
            hideUsersFloatingActions();
            filtersForm.requestSubmit();
        });
        roleFilter.dataset.bound = 'true';
    }

    if (typeof enhanceAdminSelects === 'function') {
        enhanceAdminSelects(document);
    }

    initUsersFloatingActions();
    initActivitiesControls();
    updateNameSortIcon();
}

document.addEventListener('DOMContentLoaded', initUsersPage);
document.addEventListener('livewire:navigated', initUsersPage);
</script>
@endsection
