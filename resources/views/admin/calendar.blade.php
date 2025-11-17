@extends('layouts.sidebar')
@section('page-title', 'Calendars')

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
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
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

.header-icon svg {
    color: white;
    font-size: 1.25rem;
    width: 1.25rem;
    height: 1.25rem;
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

/* Calendar Grid Styles */
.calendar-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .calendar-grid {
        grid-template-columns: 320px 1fr;
    }
}

/* Events Sidebar */
.events-sidebar {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--neutral-200);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    height: fit-content;
}

.events-header {
    background: var(--neutral-50);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--neutral-200);
}

.events-body {
    padding: 1.5rem;
    max-height: 500px;
    overflow-y: auto;
}

/* Event Card */
.event-card {
    background: white;
    border: 1px solid var(--neutral-200);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border-color: var(--primary-light);
}

.event-card:last-child {
    margin-bottom: 0;
}

.event-user {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.event-date {
    font-size: 0.75rem;
    color: var(--neutral-600);
    margin-bottom: 0.5rem;
}

.event-guests {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    background: rgba(0, 70, 46, 0.1);
    color: var(--primary);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Calendar Container */
.calendar-container {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--neutral-200);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.calendar-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    padding: 1.5rem 2rem;
    color: white;
}

.calendar-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

/* Calendar Grid */
.calendar-days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border-bottom: 1px solid var(--neutral-200);
}

.weekday-header {
    background: var(--neutral-50);
    padding: 1rem 0.5rem;
    text-align: center;
    font-weight: 600;
    color: var(--neutral-700);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-right: 1px solid var(--neutral-200);
}

.weekday-header:last-child {
    border-right: none;
}

.calendar-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
}

.calendar-day {
    border-right: 1px solid var(--neutral-200);
    border-bottom: 1px solid var(--neutral-200);
    padding: 0.75rem;
    min-height: 100px;
    position: relative;
    transition: all 0.2s ease;
    background: white;
}

.calendar-day:hover {
    background: var(--neutral-50);
}

.calendar-day:nth-child(7n) {
    border-right: none;
}

.calendar-day.empty {
    background: var(--neutral-50);
}

.calendar-day.has-events {
    background: rgba(0, 70, 46, 0.03);
}

.day-number {
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

/* Event Badge */
.event-badge {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.375rem 0.5rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.event-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 70, 46, 0.3);
}

.event-badge-icon {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}

/* Empty State */
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

.empty-state-icon svg {
    width: 24px;
    height: 24px;
}

/* Month Picker */
.month-picker-form {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.month-picker-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--neutral-700);
}

.month-picker-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--neutral-300);
    border-radius: 10px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: white;
    cursor: pointer;
}

.month-picker-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.1);
}

/* Custom Scrollbar */
.events-body::-webkit-scrollbar {
    width: 6px;
}

.events-body::-webkit-scrollbar-track {
    background: var(--neutral-100);
    border-radius: 10px;
}

.events-body::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

.events-body::-webkit-scrollbar-thumb:hover {
    background: var(--primary-light);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .header-actions {
        margin-left: 0;
        width: 100%;
        justify-content: flex-end;
    }
    
    .calendar-day {
        min-height: 80px;
        padding: 0.5rem;
    }
    
    .event-badge {
        font-size: 0.625rem;
        padding: 0.25rem 0.375rem;
    }
}


</style>

<div class="modern-card p-6 mx-auto max-w-full">
    <!-- Header -->
    <div class="page-header">
        <div class="header-icon">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h1 class="header-title">Calendar</h1>
        <div class="header-actions">
            <!-- Month Picker -->
            <form method="GET" action="{{ route('admin.calendar') }}" class="month-picker-form">
                <label class="month-picker-label">Select Month:</label>
                <input type="month" name="month" value="{{ $month }}" id="month-picker"
                       class="month-picker-input">
            </form>
        </div>
    </div>

    <div class="calendar-grid">
        <!-- Sidebar List of Approved Events for the Month -->
        <div class="events-sidebar">
            <div class="events-header">
                <h2 class="font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approved Events ({{ $monthlyApproved->count() }})
                </h2>
            </div>
            <div class="events-body">
                @forelse($monthlyApproved as $event)
                    <div class="event-card">
                        <div class="event-user">{{ $event->user->name }}</div>
                        <div class="event-date">{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</div>
                        <div class="event-guests">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                            </svg>
                            {{ $event->guests }} guests
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">No approved reservations for this month.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-container">
            <div class="calendar-header">
                <h2 class="calendar-title">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h2>
            </div>

            @php
                $monthStart = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
                $daysInMonth = $monthStart->daysInMonth;
                $startDay = $monthStart->dayOfWeek;
            @endphp

            <div class="calendar-days-grid">
                <!-- Weekday headers -->
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                    <div class="weekday-header">
                        {{ $day }}
                    </div>
                @endforeach
            </div>

            <div class="calendar-body">
                <!-- Empty slots before first day -->
                @for($i = 0; $i < $startDay; $i++)
                    <div class="calendar-day empty"></div>
                @endfor

                <!-- Days with events -->
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $currentDate = \Carbon\Carbon::parse($month . '-' . str_pad($d, 2, '0', STR_PAD_LEFT));
                        $eventsForDay = $monthlyApproved->filter(fn($res) => \Carbon\Carbon::parse($res->event_date)->isSameDay($currentDate));
                        $hasEvents = $eventsForDay->count() > 0;
                    @endphp
                    <div class="calendar-day {{ $hasEvents ? 'has-events' : '' }}">
                        <div class="day-number">{{ $d }}</div>
                        @if($hasEvents)
                            <div class="space-y-1">
                                @foreach($eventsForDay as $ev)
                                    <div class="event-badge" title="{{ $ev->user->name }} - {{ $ev->guests }} guests">
                                        <svg class="event-badge-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $ev->user->name }} ({{ $ev->guests }})
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('month-picker').addEventListener('change', function() {
        this.form.submit();
    });

    // Add hover effects to event badges
    document.querySelectorAll('.event-badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 2px 8px rgba(0, 70, 46, 0.3)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Add click effect to event cards
    document.querySelectorAll('.event-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.12)';
            setTimeout(() => {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.08)';
            }, 150);
        });
    });
</script>
@endsection