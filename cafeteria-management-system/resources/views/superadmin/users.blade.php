@extends('layouts.sidebar')
@section('page-title', 'Manage Users')

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
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 10px;
    transition: all 0.3s ease;
    position: relative;
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

.action-btn-audit {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border-color: rgba(245, 158, 11, 0.2);
}

.action-btn-audit:hover {
    background: rgba(245, 158, 11, 0.2);
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

/* Role Badge */
.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-admin {
    background: rgba(0, 70, 46, 0.1);
    color: var(--primary);
}

.role-user {
    background: rgba(107, 114, 128, 0.1);
    color: var(--neutral-600);
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

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
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

.header-icon i {
    color: white;
    font-size: 1.25rem;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    margin-left: auto;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #00462E 0%, #057C3C 100%);
}

</style>

<div class="modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="page-header">
        <div class="header-icon">
            <i class="far fa-user"></i>
        </div>
        <h1 class="header-title">User Management</h1>
        <div class="header-actions">
            <button onclick="openRecentActivitiesModal()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Recent Activities
            </button>
            <button onclick="document.getElementById('addAdminModal').classList.remove('hidden')" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Admin
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-auto max-h-96 modern-scrollbar">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="font-semibold text-gray-900">{{ $user->name }}</td>
                    <td class="text-gray-600">{{ $user->email }}</td>
                    <td>
                        <span class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-user' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <div class="flex flex-wrap gap-2">
                            @if($user->role === 'admin')
                                <button
                                    onclick="openEditModal({{ $user->id }}, '{{ e($user->name) }}', '{{ e($user->email) }}')"
                                    class="action-btn action-btn-edit">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <a href="{{ route('superadmin.users.audit', $user) }}"
                                   class="action-btn action-btn-audit">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Audit
                                </a>
                            @else
                                <a href="{{ route('superadmin.users.audit', $user) }}"
                                   class="action-btn action-btn-audit">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Audit
                                </a>
                            @endif

                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" class="inline" id="deleteForm{{ $user->id }}">
                                @csrf @method('DELETE')
                                <button type="button"
                                        onclick="openDeleteModal({{ $user->id }}, '{{ e($user->name) }}')"
                                        class="action-btn action-btn-delete">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-gray-900 mb-2">No users found</p>
                                <p class="text-sm text-gray-500">Start by adding your first user to the system</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: Add Admin --}}
<div id="addAdminModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-900">Add New Admin</h2>

        <form method="POST" action="{{ route('superadmin.users.store') }}" id="addAdminForm">
            @csrf
            <div class="space-y-4">
                <input type="text" name="name" placeholder="Full Name" class="modal-input" required>
                <input type="email" name="email" placeholder="Email Address" class="modal-input" required>
                <input type="password" name="password" placeholder="Password" class="modal-input" required>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="modal-input" required>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="document.getElementById('addAdminModal').classList.add('hidden')"
                        class="btn-secondary">Cancel</button>
                <button type="button" onclick="openCreateAdminModal()" class="btn-primary">Create Admin</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Admin --}}
<div id="editUserModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-900">Edit Admin</h2>

        <form id="editUserForm" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <input type="text" name="name" id="editName" class="modal-input" required>
                <input type="email" name="email" id="editEmail" class="modal-input" required>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')"
                        class="btn-secondary">Cancel</button>
                <button type="button" onclick="openUpdateAdminModal()" class="btn-primary">Update Admin</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Delete Confirmation --}}
<div id="deleteConfirmationModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete User</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-gray-900"></span>?
                This action cannot be undone.
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Cancel</button>
            <button type="button" onclick="confirmDelete()" class="btn-primary bg-red-600 hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

{{-- Modal: Create Admin Confirmation --}}
<div id="createAdminConfirmationModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Create New Admin</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to create this admin user? They will have administrative privileges.
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeCreateAdminModal()" class="btn-secondary">Cancel</button>
            <button type="button" onclick="confirmCreateAdmin()" class="btn-primary">Create Admin</button>
        </div>
    </div>
</div>

{{-- Modal: Update Admin Confirmation --}}
<div id="updateAdminConfirmationModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-md">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Update Admin</h3>
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to save the changes to this admin user?
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeUpdateAdminModal()" class="btn-secondary">Cancel</button>
            <button type="button" onclick="confirmUpdateAdmin()" class="btn-primary">Update Admin</button>
        </div>
    </div>
</div>

{{-- Modal: Recent Activities --}}
<div id="recentActivitiesModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="modern-modal p-6 w-full max-w-6xl max-h-[80vh] overflow-hidden">
        <h2 class="text-xl font-bold mb-4 text-gray-900">Recent Activities</h2>

        <div id="activitiesTableContainer" class="overflow-auto max-h-96 modern-scrollbar">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th class="cursor-pointer hover:bg-gray-100" onclick="sortBy('action')">Action</th>
                        <th class="cursor-pointer hover:bg-gray-100" onclick="sortBy('module')">Module</th>
                        <th class="cursor-pointer hover:bg-gray-100" onclick="sortBy('description')">Description</th>
                        <th class="cursor-pointer hover:bg-gray-100" onclick="sortBy('created_at')">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state py-8">
                                <p class="text-gray-500">Loading activities...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <button type="button" onclick="document.getElementById('recentActivitiesModal').classList.add('hidden')"
                    class="btn-secondary">Close</button>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, email) {
    document.getElementById('editUserModal').classList.remove('hidden');
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editUserForm').action = `{{ url('superadmin/users') }}/${id}`;
}

let deleteUserId = null;

function openDeleteModal(userId, userName) {
    deleteUserId = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteConfirmationModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteConfirmationModal').classList.add('hidden');
    deleteUserId = null;
}

function confirmDelete() {
    if (deleteUserId) {
        document.getElementById('deleteForm' + deleteUserId).submit();
    }
}

function openCreateAdminModal() {
    document.getElementById('createAdminConfirmationModal').classList.remove('hidden');
}

function closeCreateAdminModal() {
    document.getElementById('createAdminConfirmationModal').classList.add('hidden');
}

function confirmCreateAdmin() {
    document.getElementById('addAdminForm').submit();
}

function openUpdateAdminModal() {
    document.getElementById('updateAdminConfirmationModal').classList.remove('hidden');
}

function closeUpdateAdminModal() {
    document.getElementById('updateAdminConfirmationModal').classList.add('hidden');
}

function confirmUpdateAdmin() {
    document.getElementById('editUserForm').submit();
}

let allAudits = [];
let currentSortBy = 'created_at';
let currentSortDirection = 'desc';

async function openRecentActivitiesModal() {
    document.getElementById('recentActivitiesModal').classList.remove('hidden');
    await loadActivities();
}

async function loadActivities() {
    const container = document.getElementById('activitiesTableContainer');
    container.innerHTML = '<div class="empty-state py-8"><p class="text-gray-500">Loading activities...</p></div>';

    try {
        const response = await fetch('{{ url("superadmin/recent-audits") }}');
        allAudits = await response.json();

        if (allAudits.length === 0) {
            container.innerHTML = '<div class="empty-state py-8"><p class="text-gray-500">No recent activities found.</p></div>';
            return;
        }

        renderTable();
    } catch (error) {
        container.innerHTML = '<div class="empty-state py-8"><p class="text-red-500">Error loading activities.</p></div>';
        console.error('Error fetching audits:', error);
    }
}

function renderTable() {
    // Sort the audits client-side
    const sortedAudits = [...allAudits].sort((a, b) => {
        let aVal, bVal;

        if (currentSortBy === 'created_at') {
            aVal = new Date(a.created_at);
            bVal = new Date(b.created_at);
        } else {
            aVal = a[currentSortBy].toLowerCase();
            bVal = b[currentSortBy].toLowerCase();
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
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 mb-2">No recent activities found</p>
                        <p class="text-sm text-gray-500">Activities will appear here as users interact with the system</p>
                    </div>
                </td>
            </tr>
        `;
    } else {
        sortedAudits.forEach(audit => {
            const date = new Date(audit.created_at).toLocaleString();
            tbodyHtml += `
                <tr>
                    <td class="font-semibold text-gray-900">${audit.user ? audit.user.name : 'Unknown'}</td>
                    <td class="text-gray-600">${audit.action}</td>
                    <td class="text-gray-600">${audit.module}</td>
                    <td class="text-gray-600">${audit.description}</td>
                    <td class="text-gray-600">${date}</td>
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