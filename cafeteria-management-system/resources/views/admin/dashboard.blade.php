@extends('layouts.sidebar')
@section('page-title', 'Admin')

@section('content')
<style>
/* 1. Modern Color Palette */
:root {
    --primary: #00462E;
    --primary-light: #057C3C;
    --accent: #FF6B35;
    --accent-light: #FF8E53;
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

/* 2. Glassmorphism Effects */
.glass-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
}

.glass-header {
    background: linear-gradient(135deg, rgba(0, 70, 46, 0.9) 0%, rgba(5, 124, 60, 0.9) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* 3. Modern Card Styles - Removed green top line */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: relative;
}

/* Removed the ::before pseudo-element that created the green line */
.modern-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
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

/* 4. Metric Cards with Gradient Backgrounds */
.metric-card-primary {
    background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.metric-card-primary::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 120px;
    height: 120px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.metric-card-warning {
    background: linear-gradient(135deg, #EA580C 0%, #FB923C 100%);
    color: white;
}

.metric-card-accent {
    background: linear-gradient(135deg, #FF6B35 0%, #FF8E53 100%);
    color: white;
}

/* 5. Status Elements - Modern Design */
.status-chip {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-chip::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.status-critical {
    background: rgba(220, 38, 38, 0.1);
    color: #DC2626;
}

.status-warning {
    background: rgba(217, 119, 6, 0.1);
    color: #D97706;
}

.status-low {
    background: rgba(234, 88, 12, 0.1);
    color: #EA580C;
}

.status-good {
    background: rgba(5, 124, 60, 0.1);
    color: #057C3C;
}

/* 6. Typography - Modern Scale */
.metric-value {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.5px;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--neutral-900);
    letter-spacing: -0.2px;
}

/* 7. Animation Keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}

.animate-slide-in {
    animation: slideInLeft 0.5s ease-out;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* 8. Icon Styles - Modern */
.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

/* 9. Inventory Items - Modern List */
.inventory-item-modern {
    padding: 0.875rem;
    border-radius: 12px;
    border: 1px solid var(--neutral-100);
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    background: white;
}

.inventory-item-modern:hover {
    border-color: var(--neutral-200);
    background: var(--neutral-50);
    transform: translateX(4px);
}

/* 10. Table Styles - Modern */
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
    position: sticky;
    top: 0;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--neutral-100);
    transition: background-color 0.2s ease;
}

.modern-table tr:last-child td {
    border-bottom: none;
}

.modern-table tr:hover td {
    background: var(--neutral-50);
}

/* 11. Custom Scrollbar - Modern */
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

/* 12. Empty State Styles */
.empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: var(--neutral-400);
}

.empty-state-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    background: var(--neutral-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* 13. Greeting Section - Modern with Updated Date Design */
.greeting-section {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}

.greeting-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
}

.greeting-content {
    position: relative;
    z-index: 2;
}

/* Updated Date Design */
.date-badge {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    text-align: center;
    min-width: 140px;
}

.date-day {
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.date-full {
    font-size: 0.875rem;
    font-weight: 700;
    color: white;
    letter-spacing: -0.2px;
}

/* 14. Progress Indicators */
.progress-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 0.5rem;
}

.progress-critical { background: #DC2626; }
.progress-warning { background: #D97706; }
.progress-low { background: #EA580C; }
.progress-good { background: #057C3C; }

/* 15. Days Left Status */
.days-left-chip {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.days-left-chip::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* 16. Font Size Consistency */
.greeting-title {
    font-size: 1.75rem;
    font-weight: 700;
}

.greeting-message {
    font-size: 0.875rem;
}

.metric-label {
    font-size: 0.875rem;
}

.metric-helper {
    font-size: 0.75rem;
}

.inventory-item-name {
    font-size: 0.875rem;
}

.inventory-item-detail {
    font-size: 0.75rem;
}

.table-data {
    font-size: 0.875rem;
}

.empty-state-text {
    font-size: 0.875rem;
}
</style>

@php
    $hour = now()->hour;
    if ($hour < 12) {
        $greeting = "Good morning";
        $message = "Ready to start your day? Here's your cafeteria overview.";
        $icon = "🌅";
    } elseif ($hour < 17) {
        $greeting = "Good afternoon";
        $message = "Hope you're having a productive day! Here's the latest update.";
        $icon = "☀️";
    } else {
        $greeting = "Good evening";
        $message = "Great work today! Here's your end-of-day summary.";
        $icon = "🌙";
    }
@endphp

<div class="w-full min-h-screen overflow-hidden space-y-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0" style="max-width: calc(100vw - 12rem);">
    
    <!-- Modern Greeting Section with Updated Date Design -->
    <div class="greeting-section text-white p-6 animate-fade-in">
        <div class="greeting-content">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="text-3xl animate-float">{{ $icon }}</div>
                    <div>
                        <h1 class="greeting-title mb-1">{{ $greeting }}, {{ Auth::user()->name }}</h1>
                        <p class="greeting-message text-green-100 opacity-90">{{ $message }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="date-badge">
                        <div class="date-day">{{ now()->format('l') }}</div>
                        <div class="date-full">{{ now()->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics - Modern Cards (No green top line on hover) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Total Reservations -->
        <div class="modern-card metric-card-primary p-6 animate-fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <div class="metric-value text-white">{{ $totalReservations }}</div>
                    <p class="metric-label text-green-100 font-medium mt-2">Total Reservations</p>
                </div>
                <div class="metric-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-100 metric-helper">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span>All time bookings</span>
            </div>
        </div>

        <!-- Pending Reservations -->
        <div class="modern-card metric-card-warning p-6 animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <div class="metric-value text-white">{{ $pendingReservations }}</div>
                    <p class="metric-label text-orange-100 font-medium mt-2">Pending Reservations</p>
                </div>
                <div class="metric-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-orange-100 metric-helper">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Awaiting confirmation</span>
            </div>
        </div>

        <!-- Menus Sold -->
        <div class="modern-card metric-card-accent p-6 animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
                <div>
                    <div class="metric-value text-white">{{ $menusSold }}</div>
                    <p class="metric-label text-orange-100 font-medium mt-2">Menus Sold</p>
                </div>
                <div class="metric-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-orange-100 metric-helper">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Today's sales</span>
            </div>
        </div>
    </div>

    <!-- Inventory Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items -->
        <div class="menu-card animate-slide-in">
            <div class="p-6 border-b border-neutral-100">
                <div class="flex items-center justify-between">
                    <h3 class="section-title">Low Stock Items</h3>
                    <span class="status-chip status-warning">{{ count($lowStocks) }} items</span>
                </div>
                <p class="text-neutral-500 metric-helper mt-1">Items that need restocking soon</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($lowStocks as $item)
                    <div class="inventory-item-modern">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="progress-dot progress-warning"></span>
                                <div>
                                    <span class="font-semibold text-neutral-900 inventory-item-name block">{{ $item->name }}</span>
                                    <span class="text-neutral-500 inventory-item-detail">{{ $item->qty }} {{ $item->unit }} remaining</span>
                                </div>
                            </div>
                            <span class="text-orange-700 font-semibold inventory-item-name">Low</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="empty-state-text">All items are well stocked</p>
                    </div>
                    @endforelse
                </div>
                @if(count($lowStocks) > 0)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.inventory.index') }}" class="text-primary hover:text-primary-light font-medium metric-helper transition-colors duration-200 inline-flex items-center">
                        Manage Inventory
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Out of Stock Items -->
        <div class="menu-card animate-slide-in" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-neutral-100">
                <div class="flex items-center justify-between">
                    <h3 class="section-title">Out of Stock</h3>
                    <span class="status-chip status-critical">{{ count($outOfStocks) }} items</span>
                </div>
                <p class="text-neutral-500 metric-helper mt-1">Items that need immediate attention</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($outOfStocks as $item)
                    <div class="inventory-item-modern">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="progress-dot progress-critical"></span>
                                <div>
                                    <span class="font-semibold text-neutral-900 inventory-item-name block">{{ $item->name }}</span>
                                    <span class="text-neutral-500 inventory-item-detail">Restock needed</span>
                                </div>
                            </div>
                            <span class="text-red-700 font-semibold inventory-item-name">Out of stock</span>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="empty-state-text">All items are available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Items -->
    <div class="menu-card animate-fade-in">
        <div class="p-6 border-b border-neutral-100">
            <div class="flex items-center justify-between">
                <h3 class="section-title">Items Expiring Soon</h3>
                <span class="status-chip status-warning">{{ count($expiringSoon) }} items</span>
            </div>
            <p class="text-neutral-500 metric-helper mt-1">Monitor items approaching expiration</p>
        </div>
        <div class="overflow-auto max-h-80 modern-scrollbar">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                        <th>Days Left</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expiringSoon as $item)
                    <tr>
                        <td class="font-semibold text-neutral-900 table-data">{{ $item->name }}</td>
                        <td class="text-neutral-600 table-data">{{ $item->qty }} {{ $item->unit }}</td>
                        <td class="font-medium text-neutral-900 table-data">
                            {{ \Carbon\Carbon::parse($item->expiry_date)->format('M j, Y') }}
                        </td>
                        <td>
                            @php
                                $daysLeft = (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->expiry_date));
                                $statusClass = $daysLeft <= 7 ? 'status-critical' : ($daysLeft <= 15 ? 'status-warning' : 'status-low');
                            @endphp
                            <span class="days-left-chip {{ $statusClass }}">
                                {{ $daysLeft }} days
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg class="w-6 h-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="empty-state-text">No items expiring soon</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Messages from Customer --}}
<div class="p-6 bg-white rounded-xl shadow-md border-l-4 border-green-500 flex items-center">
    <div class="mr-4 text-green-500">
        <i class="fas fa-envelope fa-2x"></i>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500">Contact Messages</p>
        <p class="text-2xl font-bold text-gray-800">{{ $unreadCount }} Unread</p>
        <a href="{{ route('admin.messages.index') }}" class="text-xs text-blue-500 hover:underline">View All Messages</a>
    </div>
</div>
@endsection