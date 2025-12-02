@extends('layouts.sidebar')
@section('page-title', 'Audit Trail')

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
    text-decoration: none;
}

.btn-secondary:hover {
    background: var(--neutral-200);
    transform: translateY(-1px);
}

/* Action Badges */
.action-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.action-create {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
}

.action-update {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
}

.action-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.action-login {
    background: rgba(168, 85, 247, 0.1);
    color: #9333ea;
}

.action-default {
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

/* Header Styles */
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1rem;
}

.header-content {
    flex: 1;
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

.header-text {
    flex: 1;
}

.header-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--neutral-900);
    letter-spacing: -0.5px;
    margin-bottom: 0.25rem;
}

.header-subtitle {
    color: var(--neutral-500);
    font-size: 0.875rem;
}

.user-info {
    background: var(--neutral-50);
    padding: 1rem 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-200);
    margin-bottom: 1.5rem;
}

.user-email {
    color: var(--neutral-600);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Module Badge */
.module-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--neutral-100);
    color: var(--neutral-700);
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

/* Description Text */
.description-text {
    color: var(--neutral-700);
    font-size: 0.875rem;
    line-height: 1.4;
    max-width: 300px;
}

/* Date Styling */
.date-text {
    color: var(--neutral-600);
    font-size: 0.875rem;
    font-weight: 500;
}

/* Icon Sizes */
.icon-sm {
    width: 14px;
    height: 14px;
}

.icon-md {
    width: 16px;
    height: 16px;
}

.icon-lg {
    width: 20px;
    height: 20px;
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

<div class="modern-card menu-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('superadmin.users') }}" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
            <div class="header-icon">
                <svg class="icon-lg text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Audit Trail</h1>
                <p class="header-subtitle">Complete history of user activities and system interactions</p>
            </div>
        </div>
        
    </div>

    <!-- User Information -->
    <div class="user-info">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                <p class="user-email">{{ $user->email }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Total Activities</p>
                <p class="font-semibold text-gray-900">{{ !empty($audits) && $audits->isNotEmpty() ? $audits->count() : 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Audit Table -->
    @if(!empty($audits) && $audits->isNotEmpty())
        <div class="overflow-auto max-h-96 modern-scrollbar">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audits as $log)
                        <tr>
                            <td>
                                @php
                                    $actionClass = 'action-default';
                                    $actionIcon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                    
                                    if (str_contains(strtolower($log->action), 'create') || str_contains(strtolower($log->action), 'add')) {
                                        $actionClass = 'action-create';
                                        $actionIcon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>';
                                    } elseif (str_contains(strtolower($log->action), 'update') || str_contains(strtolower($log->action), 'edit')) {
                                        $actionClass = 'action-update';
                                        $actionIcon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                                    } elseif (str_contains(strtolower($log->action), 'delete') || str_contains(strtolower($log->action), 'remove')) {
                                        $actionClass = 'action-delete';
                                        $actionIcon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                                    } elseif (str_contains(strtolower($log->action), 'login')) {
                                        $actionClass = 'action-login';
                                        $actionIcon = '<svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>';
                                    }
                                @endphp
                                <span class="action-badge {{ $actionClass }}">
                                    {!! $actionIcon !!}
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                <span class="module-badge">
                                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    {{ $log->module }}
                                </span>
                            </td>
                            <td>
                                <span class="description-text">{{ $log->description }}</span>
                            </td>
                            <td>
                                <span class="date-text">
                                    <svg class="icon-sm inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-lg font-semibold text-gray-900 mb-2">No Audit Records Found</p>
            <p class="text-sm text-gray-500">No activities have been recorded for this user yet</p>
        </div>
    @endif

</div>
@endsection