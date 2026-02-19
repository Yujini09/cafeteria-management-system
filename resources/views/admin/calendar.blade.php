@extends('layouts.sidebar')
@section('page-title', 'Calendars')

@section('content')
<style>
/* Modern Card Styles */
.modern-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--neutral-200);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
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

.event-card-paid {
    background: #ecfdf3;
    border-color: #22c55e;
}

.event-card-paid:hover {
    border-color: #16a34a;
    box-shadow: 0 4px 16px rgba(22, 163, 74, 0.2);
}

.event-card-unpaid {
    background: #fffbeb;
    border-color: #f59e0b;
}

.event-card-unpaid:hover {
    border-color: #d97706;
    box-shadow: 0 4px 16px rgba(217, 119, 6, 0.2);
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
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.calendar-title-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.calendar-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
}

.calendar-month-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
    text-decoration: none;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.calendar-month-btn:hover {
    background: rgba(255, 255, 255, 0.28);
    border-color: rgba(255, 255, 255, 0.5);
}

.calendar-month-btn svg {
    width: 15px;
    height: 15px;
}

.calendar-legend {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.5rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.28);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    line-height: 1;
}

.legend-swatch {
    width: 0.72rem;
    height: 0.72rem;
    border-radius: 999px;
    flex-shrink: 0;
}

.legend-swatch-paid {
    background: #22c55e;
    box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.45);
}

.legend-swatch-unpaid {
    background: #ea580c;
    box-shadow: 0 0 0 1px rgba(194, 65, 12, 0.45);
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
    grid-template-columns: repeat(7, minmax(0, 1fr));
    grid-auto-rows: 132px;
    align-items: stretch;
}

.calendar-day {
    --day-pad-x: 0.75rem;
    border-right: 1px solid var(--neutral-200);
    border-bottom: 1px solid var(--neutral-200);
    padding: 0.6rem var(--day-pad-x);
    min-height: 132px;
    height: 132px;
    position: relative;
    transition: all 0.2s ease;
    background: white;
    overflow: visible;
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
    position: relative;
    z-index: 40;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.5rem;
    height: 1.5rem;
    padding: 0 0.25rem;
    border-radius: 0.375rem;
    background: #ffffff;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.08);
    font-weight: 600;
    color: var(--neutral-900);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.day-events {
    position: relative;
    z-index: 20;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

/* Event Badge */
.event-badge {
    --event-span: 1;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 0.375rem 0.5rem;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    width: calc((100% + ((var(--day-pad-x) * 2) + 1px)) * var(--event-span) - ((var(--day-pad-x) * 2) + 1px));
    max-width: none;
    position: relative;
    z-index: 20;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.event-badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 70, 46, 0.3);
}

.event-badge-unpaid {
    background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);
}

.event-badge-icon {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}

.event-badge-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Empty State */
.empty-state-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
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

/* Reservation Details Modal */
.reservation-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 80;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(2px);
}

.reservation-modal-overlay.open {
    display: flex;
}

.reservation-modal {
    width: 100%;
    max-width: 640px;
    background: white;
    border-radius: 16px;
    border: 1px solid var(--neutral-200);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
    overflow: hidden;
}

.reservation-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--neutral-200);
}

.reservation-modal-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--neutral-900);
}

.reservation-modal-close {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid var(--neutral-300);
    color: var(--neutral-600);
    background: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.reservation-modal-close:hover {
    background: var(--neutral-50);
    color: var(--neutral-800);
}

.reservation-modal-body {
    padding: 1.25rem;
}

.reservation-details-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.875rem 1rem;
}

.reservation-detail-item {
    border: 1px solid var(--neutral-200);
    border-radius: 10px;
    padding: 0.75rem;
    background: var(--neutral-50);
}

.reservation-detail-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--neutral-600);
    margin-bottom: 0.35rem;
}

.reservation-detail-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--neutral-900);
    word-break: break-word;
}

.reservation-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--neutral-200);
    background: var(--neutral-50);
}

.reservation-modal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    padding: 0.625rem 1rem;
    font-size: 0.8125rem;
    font-weight: 700;
    transition: all 0.2s ease;
}

.reservation-modal-btn-secondary {
    border: 1px solid var(--neutral-300);
    background: white;
    color: var(--neutral-700);
}

.reservation-modal-btn-secondary:hover {
    background: var(--neutral-100);
}

.reservation-modal-btn-primary {
    border: 1px solid transparent;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
}

.reservation-modal-btn-primary:hover {
    filter: brightness(1.05);
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

    .calendar-header {
        padding: 1rem 1rem 0.9rem;
        gap: 0.65rem;
    }

    .calendar-title-wrap {
        width: 100%;
    }

    .calendar-title {
        font-size: 1.25rem;
    }
    
    .calendar-body {
        grid-auto-rows: 96px;
    }

    .calendar-day {
        --day-pad-x: 0.5rem;
        min-height: 96px;
        height: 96px;
        padding: 0.45rem var(--day-pad-x);
    }
    
    .event-badge {
        font-size: 0.625rem;
        padding: 0.25rem 0.375rem;
    }

    .reservation-details-grid {
        grid-template-columns: 1fr;
    }
}


</style>

<div class="admin-page-shell modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">
    <!-- Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Calendar</h1>
            </div>
        </div>
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
                    @php
                        $eventCustomer = $event->contact_person ?? optional($event->user)->name ?? 'N/A';
                        $eventGuests = $event->guests ?? $event->number_of_persons ?? 0;
                        $eventEmail = $event->email ?? optional($event->user)->email ?? 'N/A';
                        $eventPhone = $event->contact_number ?? optional($event->user)->phone ?? 'N/A';
                        $eventDepartment = $event->department ?? optional($event->user)->department ?? 'N/A';
                        $eventVenue = $event->venue ?? 'N/A';
                        $eventName = $event->event_name ?? 'N/A';
                        $eventTime = !empty($event->event_time) ? $event->event_time : 'Not specified';
                        $eventStartDate = \Carbon\Carbon::parse($event->event_date ?? $event->date);
                        $eventEndDate = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : $eventStartDate->copy();
                        if ($eventEndDate->lt($eventStartDate)) {
                            $eventEndDate = $eventStartDate->copy();
                        }
                        $eventDateLabel = $eventStartDate->isSameDay($eventEndDate)
                            ? $eventStartDate->format('M d, Y')
                            : $eventStartDate->format('M d') . ' - ' . $eventEndDate->format('M d, Y');
                        $eventIsUnpaid = ($event->payment_status ?? 'pending') !== 'paid';
                    @endphp
                    <div class="event-card reservation-trigger {{ $eventIsUnpaid ? 'event-card-unpaid' : 'event-card-paid' }}"
                         data-reservation-id="{{ $event->id }}"
                         data-customer="{{ $eventCustomer }}"
                         data-event-name="{{ $eventName }}"
                         data-event-date="{{ $eventDateLabel }}"
                         data-event-time="{{ $eventTime }}"
                         data-venue="{{ $eventVenue }}"
                         data-guests="{{ $eventGuests }}"
                         data-department="{{ $eventDepartment }}"
                         data-email="{{ $eventEmail }}"
                         data-phone="{{ $eventPhone }}"
                         data-payment-status="{{ $event->payment_status ?? 'pending' }}"
                         data-show-url="{{ route('admin.reservations.show', $event) }}">
                        <div class="event-user">{{ $eventCustomer }}</div>
                        <div class="event-date">{{ $eventDateLabel }}</div>
                        <div class="event-guests">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                            </svg>
                            {{ $eventGuests }} guests
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
                <div class="calendar-title-wrap">
                    <a href="{{ route('admin.calendar', ['month' => \Carbon\Carbon::parse($month . '-01')->copy()->subMonthNoOverflow()->format('Y-m')]) }}"
                       class="calendar-month-btn"
                       aria-label="Previous month">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h2 class="calendar-title">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</h2>
                    <a href="{{ route('admin.calendar', ['month' => \Carbon\Carbon::parse($month . '-01')->copy()->addMonthNoOverflow()->format('Y-m')]) }}"
                       class="calendar-month-btn"
                       aria-label="Next month">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="calendar-legend" aria-label="Reservation status legend">
                    <span class="legend-item">
                        <span class="legend-swatch legend-swatch-paid" aria-hidden="true"></span>
                        Paid
                    </span>
                    <span class="legend-item">
                        <span class="legend-swatch legend-swatch-unpaid" aria-hidden="true"></span>
                        Unpaid
                    </span>
                </div>
            </div>

            @php
                $monthStart = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();
                $daysInMonth = $monthStart->daysInMonth;
                $startDay = $monthStart->dayOfWeek;
                $segmentEventsByDate = [];

                foreach ($monthlyApproved as $calendarEvent) {
                    $calendarEventStartRaw = $calendarEvent->event_date ?? $calendarEvent->date;
                    if (empty($calendarEventStartRaw)) {
                        continue;
                    }
                    $eventStart = \Carbon\Carbon::parse($calendarEventStartRaw)->startOfDay();
                    $eventEnd = $calendarEvent->end_date
                        ? \Carbon\Carbon::parse($calendarEvent->end_date)->startOfDay()
                        : $eventStart->copy();

                    if ($eventEnd->lt($eventStart)) {
                        $eventEnd = $eventStart->copy();
                    }

                    $displayStart = $eventStart->copy();
                    if ($displayStart->lt($monthStart)) {
                        $displayStart = $monthStart->copy();
                    }

                    $displayEnd = $eventEnd->copy();
                    if ($displayEnd->gt($monthEnd)) {
                        $displayEnd = $monthEnd->copy();
                    }

                    if ($displayEnd->lt($displayStart)) {
                        continue;
                    }

                    $segmentStart = $displayStart->copy();
                    while ($segmentStart->lte($displayEnd)) {
                        $remainingInWeek = 7 - $segmentStart->dayOfWeek;
                        $remainingDays = $segmentStart->diffInDays($displayEnd) + 1;
                        $segmentSpan = min($remainingInWeek, $remainingDays);
                        $segmentKey = $segmentStart->format('Y-m-d');

                        if (!isset($segmentEventsByDate[$segmentKey])) {
                            $segmentEventsByDate[$segmentKey] = [];
                        }

                        $segmentEventsByDate[$segmentKey][] = [
                            'event' => $calendarEvent,
                            'span' => $segmentSpan,
                        ];

                        $segmentStart->addDays($segmentSpan);
                    }
                }
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
                        $currentDateKey = $currentDate->format('Y-m-d');
                        $segmentEventsForDay = $segmentEventsByDate[$currentDateKey] ?? [];
                        $hasEvents = count($segmentEventsForDay) > 0;
                    @endphp
                    <div class="calendar-day {{ $hasEvents ? 'has-events' : '' }}">
                        <div class="day-number">{{ $d }}</div>
                        @if($hasEvents)
                            <div class="day-events">
                                @foreach($segmentEventsForDay as $segment)
                                    @php
                                        $ev = $segment['event'];
                                        $spanDays = (int) ($segment['span'] ?? 1);
                                        $dayEventCustomer = $ev->contact_person ?? optional($ev->user)->name ?? 'N/A';
                                        $dayEventGuests = $ev->guests ?? $ev->number_of_persons ?? 0;
                                        $dayEventEmail = $ev->email ?? optional($ev->user)->email ?? 'N/A';
                                        $dayEventPhone = $ev->contact_number ?? optional($ev->user)->phone ?? 'N/A';
                                        $dayEventDepartment = $ev->department ?? optional($ev->user)->department ?? 'N/A';
                                        $dayEventVenue = $ev->venue ?? 'N/A';
                                        $dayEventName = $ev->event_name ?? 'N/A';
                                        $dayEventTime = !empty($ev->event_time) ? $ev->event_time : 'Not specified';
                                        $dayEventStartDate = \Carbon\Carbon::parse($ev->event_date ?? $ev->date);
                                        $dayEventEndDate = $ev->end_date ? \Carbon\Carbon::parse($ev->end_date) : $dayEventStartDate->copy();
                                        if ($dayEventEndDate->lt($dayEventStartDate)) {
                                            $dayEventEndDate = $dayEventStartDate->copy();
                                        }
                                        $dayEventDateLabel = $dayEventStartDate->isSameDay($dayEventEndDate)
                                            ? $dayEventStartDate->format('M d, Y')
                                            : $dayEventStartDate->format('M d') . ' - ' . $dayEventEndDate->format('M d, Y');
                                        $dayEventIsUnpaid = ($ev->payment_status ?? 'pending') !== 'paid';
                                    @endphp
                                    <div class="event-badge reservation-trigger {{ $dayEventIsUnpaid ? 'event-badge-unpaid' : '' }}"
                                         style="--event-span: {{ max(1, $spanDays) }};"
                                         title="{{ $dayEventCustomer }} - {{ $dayEventGuests }} guests"
                                         data-reservation-id="{{ $ev->id }}"
                                         data-customer="{{ $dayEventCustomer }}"
                                         data-event-name="{{ $dayEventName }}"
                                         data-event-date="{{ $dayEventDateLabel }}"
                                         data-event-time="{{ $dayEventTime }}"
                                         data-venue="{{ $dayEventVenue }}"
                                         data-guests="{{ $dayEventGuests }}"
                                         data-department="{{ $dayEventDepartment }}"
                                         data-email="{{ $dayEventEmail }}"
                                         data-phone="{{ $dayEventPhone }}"
                                         data-payment-status="{{ $ev->payment_status ?? 'pending' }}"
                                         data-show-url="{{ route('admin.reservations.show', $ev) }}">
                                        <svg class="event-badge-icon" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="event-badge-label">{{ $dayEventCustomer }} ({{ $dayEventGuests }})</span>
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

<div id="reservationDetailModal" class="reservation-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="reservationDetailTitle" hidden>
    <div class="reservation-modal">
        <div class="reservation-modal-header">
            <h3 id="reservationDetailTitle" class="reservation-modal-title">Reservation Details</h3>
            <button type="button" id="reservationDetailClose" class="reservation-modal-close" aria-label="Close reservation details">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="reservation-modal-body">
            <div class="reservation-details-grid">
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Reservation ID</span>
                    <p class="reservation-detail-value" id="detailReservationId">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Customer</span>
                    <p class="reservation-detail-value" id="detailCustomer">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Event Name</span>
                    <p class="reservation-detail-value" id="detailEventName">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Date</span>
                    <p class="reservation-detail-value" id="detailEventDate">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Time</span>
                    <p class="reservation-detail-value" id="detailEventTime">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Venue</span>
                    <p class="reservation-detail-value" id="detailVenue">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Guests</span>
                    <p class="reservation-detail-value" id="detailGuests">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Department</span>
                    <p class="reservation-detail-value" id="detailDepartment">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Email</span>
                    <p class="reservation-detail-value" id="detailEmail">N/A</p>
                </div>
                <div class="reservation-detail-item">
                    <span class="reservation-detail-label">Phone</span>
                    <p class="reservation-detail-value" id="detailPhone">N/A</p>
                </div>
            </div>
        </div>
        <div class="reservation-modal-actions">
            <button type="button" id="reservationDetailCancel" class="reservation-modal-btn reservation-modal-btn-secondary">Close</button>
            <a id="reservationDetailLink" href="#" class="reservation-modal-btn reservation-modal-btn-primary">Open Reservation</a>
        </div>
    </div>
</div>

<script>
    const monthPicker = document.getElementById('month-picker');
    if (monthPicker) {
        monthPicker.addEventListener('change', function() {
            this.form.submit();
        });
    }

    const reservationModal = document.getElementById('reservationDetailModal');
    const reservationCloseBtn = document.getElementById('reservationDetailClose');
    const reservationCancelBtn = document.getElementById('reservationDetailCancel');
    const reservationLink = document.getElementById('reservationDetailLink');
    const detailFields = {
        reservationId: document.getElementById('detailReservationId'),
        customer: document.getElementById('detailCustomer'),
        eventName: document.getElementById('detailEventName'),
        eventDate: document.getElementById('detailEventDate'),
        eventTime: document.getElementById('detailEventTime'),
        venue: document.getElementById('detailVenue'),
        guests: document.getElementById('detailGuests'),
        department: document.getElementById('detailDepartment'),
        email: document.getElementById('detailEmail'),
        phone: document.getElementById('detailPhone'),
    };

    const setFieldText = (field, value) => {
        if (!field) return;
        field.textContent = value && String(value).trim().length ? value : 'N/A';
    };

    const closeReservationModal = () => {
        if (!reservationModal) return;
        reservationModal.classList.remove('open');
        reservationModal.hidden = true;
        document.body.classList.remove('overflow-hidden');
    };

    const openReservationModal = (trigger) => {
        if (!reservationModal || !trigger) return;
        const data = trigger.dataset || {};
        const guestsCount = Number(data.guests || 0);
        const guestsLabel = Number.isFinite(guestsCount) && guestsCount > 0
            ? `${guestsCount} guest${guestsCount === 1 ? '' : 's'}`
            : 'N/A';

        setFieldText(detailFields.reservationId, data.reservationId ? `#${data.reservationId}` : 'N/A');
        setFieldText(detailFields.customer, data.customer);
        setFieldText(detailFields.eventName, data.eventName);
        setFieldText(detailFields.eventDate, data.eventDate);
        setFieldText(detailFields.eventTime, data.eventTime);
        setFieldText(detailFields.venue, data.venue);
        setFieldText(detailFields.guests, guestsLabel);
        setFieldText(detailFields.department, data.department);
        setFieldText(detailFields.email, data.email);
        setFieldText(detailFields.phone, data.phone);

        if (reservationLink) {
            const showUrl = data.showUrl || '#';
            reservationLink.setAttribute('href', showUrl);
            reservationLink.classList.toggle('pointer-events-none', showUrl === '#');
            reservationLink.classList.toggle('opacity-60', showUrl === '#');
        }

        reservationModal.hidden = false;
        reservationModal.classList.add('open');
        document.body.classList.add('overflow-hidden');
    };

    if (reservationCloseBtn) {
        reservationCloseBtn.addEventListener('click', closeReservationModal);
    }
    if (reservationCancelBtn) {
        reservationCancelBtn.addEventListener('click', closeReservationModal);
    }
    if (reservationModal) {
        reservationModal.addEventListener('click', function(event) {
            if (event.target === reservationModal) {
                closeReservationModal();
            }
        });
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && reservationModal && !reservationModal.hidden) {
            closeReservationModal();
        }
    });

    // Keep hover effects on event badges.
    document.querySelectorAll('.event-badge').forEach((badge) => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = this.classList.contains('event-badge-unpaid')
                ? '0 2px 8px rgba(194, 65, 12, 0.35)'
                : '0 2px 8px rgba(0, 70, 46, 0.3)';
        });

        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Open reservation details from both calendar badges and sidebar cards.
    document.querySelectorAll('.reservation-trigger').forEach((trigger) => {
        trigger.addEventListener('click', function() {
            if (this.classList.contains('event-card')) {
                this.style.transform = 'translateY(-1px)';
                this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.12)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.08)';
                }, 150);
            }

            openReservationModal(this);
        });
    });
</script>
@endsection
