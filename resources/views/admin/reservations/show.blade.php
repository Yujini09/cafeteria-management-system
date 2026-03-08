@extends('layouts.sidebar')
@section('page-title','Reservations')

@section('content')
<style>
/* Action Buttons */
.reservation-show-view .action-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
}

.reservation-show-view .action-btn-approve {
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.reservation-show-view .action-btn-approve:hover {
    transform: translateY(-1px);
}

.reservation-show-view .action-btn-decline {
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.reservation-show-view .action-btn-decline:hover {
    transform: translateY(-1px);
}

.reservation-show-view .reservation-actions-affix-anchor {
    position: static;
}

.reservation-show-view .reservation-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.reservation-show-view .reservation-table-wrap .modern-table {
    min-width: 640px;
}

.reservation-show-view .details-inline-row {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.reservation-show-view .details-inline-row-center {
    align-items: center;
}

.reservation-show-view .details-inline-row > dt {
    flex: 0 0 120px;
    max-width: 120px;
    white-space: nowrap;
}

.reservation-show-view .details-inline-row > dd {
    flex: 1 1 0;
    min-width: 0;
    margin: 0;
}

.reservation-show-view .event-details-list {
    margin-top: 1rem;
}

.reservation-show-view .event-details-grid,
.reservation-show-view .event-details-contact-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem 1.5rem;
}

.reservation-show-view .event-detail-row {
    min-width: 0;
    font-size: 0.96rem;
    line-height: 1.6;
    color: #111827;
}

.reservation-show-view .event-detail-row-wide {
    grid-column: 1 / -1;
}

.reservation-show-view .event-detail-label {
    color: #111827;
    font-weight: 700;
    display: inline;
    margin: 0;
}

.reservation-show-view .event-detail-value {
    margin: 0;
    color: #111827;
    font-weight: 400;
    line-height: 1.6;
    word-break: break-word;
    display: inline;
}

.reservation-show-view .event-detail-divider {
    margin: 1rem 0;
    border-top: 1px solid #e5e7eb;
}

.reservation-show-view .event-detail-time-list {
    display: grid;
    gap: 0.2rem;
    margin-top: 0.15rem;
}

.reservation-show-view .event-detail-time-row {
    color: #111827;
}

@media (min-width: 1024px) {
    .reservation-show-view .reservation-right-rail {
        align-self: start;
    }

    .reservation-show-view .reservation-actions-affix-anchor {
        position: sticky;
        top: 5.5rem;
        z-index: 20;
    }
}

@media (max-width: 1023px) {
    .reservation-show-view .reservation-actions-affix-anchor {
        position: static;
    }
}

@media (max-width: 768px) {
    .reservation-show-view {
        padding: 1rem !important;
    }

    .reservation-show-view .page-header {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }

    .reservation-show-view .header-content {
        display: grid;
        grid-template-columns: auto auto minmax(0, 1fr);
        gap: 0.75rem;
        align-items: center;
    }

    .reservation-show-view .header-icon,
    .reservation-show-view .header-content > .header-text {
        min-width: 0;
    }

    .reservation-show-view .header-actions {
        width: 100%;
        justify-content: flex-start !important;
        gap: 0.75rem;
    }

    .reservation-show-view .header-actions .btn-primary,
    .reservation-show-view .header-actions .status-badge {
        display: flex;
        width: 100%;
        justify-content: center;
        text-align: center;
    }

    .reservation-show-view .action-btn {
        white-space: normal;
        text-align: center;
        padding: 0.75rem 1rem;
    }

    .reservation-show-view .datetime-item {
        flex-direction: column;
        gap: 0.25rem;
    }

    .reservation-show-view .datetime-item .date {
        min-width: 0 !important;
    }

    .reservation-show-view .datetime-item .time {
        margin-left: 0 !important;
    }

    .reservation-show-view .reservation-stack-sm {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }

    .reservation-show-view .reservation-stack-sm .text-right {
        text-align: left;
        width: 100%;
    }

    .reservation-show-view .reservation-table-wrap .modern-table {
        min-width: 600px;
    }

    .reservation-show-view .reservation-table-wrap th,
    .reservation-show-view .reservation-table-wrap td:not(:first-child) {
        white-space: nowrap;
    }

    .reservation-show-view .reservation-table-wrap td:first-child {
        white-space: normal;
    }

    .reservation-show-view .additionals-item {
        flex-direction: row;
        align-items: flex-start;
    }

    .reservation-show-view .additionals-item .additionals-item-content {
        min-width: 0;
        flex: 1 1 auto;
    }

    .reservation-show-view .additionals-item .additionals-item-form {
        margin-left: auto;
        flex-shrink: 0;
    }

    .reservation-show-view .additionals-item .additionals-item-action {
        align-self: flex-start;
    }

    .reservation-show-view .details-inline-row {
        gap: 0.375rem;
    }

    .reservation-show-view .details-inline-row > dt {
        flex-basis: 110px;
        max-width: 110px;
    }

    .reservation-show-view .event-details-grid,
    .reservation-show-view .event-details-contact-grid {
        grid-template-columns: 1fr;
        gap: 0.875rem;
    }

    .reservation-show-view .event-detail-time-list {
        margin-top: 0.1rem;
    }
}

/* Icon Sizes */
.reservation-show-view .icon-sm { width: 14px; height: 14px; }
.reservation-show-view .icon-md { width: 16px; height: 16px; }
.reservation-show-view .icon-lg { width: 20px; height: 20px; }

.reservation-show-view .additionals-input {
    width: 100%;
    border: 1px solid var(--neutral-300);
    border-radius: 0.75rem;
    padding: 0.6rem 0.75rem;
    font-size: 0.875rem;
    background: #ffffff;
    color: var(--neutral-900);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.reservation-show-view .additionals-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.12);
}

.reservation-show-view .additionals-price-group {
    display: flex;
    align-items: center;
    width: 100%;
    border: 1px solid var(--neutral-300);
    border-radius: 0.75rem;
    background: #ffffff;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.reservation-show-view .additionals-price-group:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 70, 46, 0.12);
}

.reservation-show-view .additionals-currency {
    padding: 0.6rem 0.5rem 0.6rem 0.7rem;
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1;
    user-select: none;
}

.reservation-show-view .additionals-price-input {
    flex: 1 1 auto;
    min-width: 0;
    width: 100% !important;
    border: 0 !important;
    border-radius: 0 !important;
    padding: 0.6rem 0.75rem 0.6rem 0.35rem;
    font-size: 0.875rem;
    text-align: left;
    background: transparent !important;
    color: var(--neutral-900);
    -webkit-appearance: none;
    -moz-appearance: textfield;
    appearance: textfield;
    box-shadow: none !important;
}

.reservation-show-view .additionals-price-input:focus {
    outline: none;
    box-shadow: none;
}

.reservation-show-view .additionals-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: rgba(0, 70, 46, 0.1);
    color: var(--primary);
}

.reservation-show-view .additionals-form {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
    margin-bottom: 1.5rem;
}

.reservation-show-view .additionals-field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.reservation-show-view .additionals-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #4b5563;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.reservation-show-view .additionals-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    border: 0;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--primary);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.reservation-show-view .additionals-submit:hover {
    background: #003824;
    transform: translateY(-1px);
}

[x-cloak] { display: none !important; }
</style>

@php
    $additionalsTotal = $r->additionals ? $r->additionals->sum('price') : 0;
    $acceptedModal = (bool) session('accepted', false);
    $inventoryWarning = (bool) session('inventory_warning', false);
    $insufficientItems = session('insufficient_items', []);
    $overlapWarning = (bool) session('overlap_warning', false);
    $overlapReservationId = session('overlap_reservation_id');
    $overlapDate = session('overlap_reservation_date');
@endphp

<div class="admin-page-shell reservation-show-view p-4 sm:p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0">

    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.reservations') }}" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div class="header-icon">
                <svg class="icon-lg text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div class="header-text">
                <h1 class="header-title">Reservations</h1>
                <p class="header-subtitle">Reservation #{{ $r->id }} - Review and manage reservation details</p>
            </div>
        </div>

        <div class="header-actions flex flex-wrap items-center justify-end gap-3 w-full md:w-auto">
            <a href="{{ route('admin.reservations.export.pdf', $r) }}" class="btn-primary">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>

            <span class="status-badge {{ $r->status === 'approved' ? 'status-approved' : ($r->status === 'declined' ? 'status-declined' : 'status-pending') }}">
                @if($r->status === 'approved')
                    <svg class="icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @elseif($r->status === 'declined')
                    <svg class="icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                @else
                    <svg class="icon-sm" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                @endif
                {{ ucfirst($r->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="info-card-title">Event Details</h2>
                </div>
                <div class="event-details-list">
                    @php
                        $startDate = \Carbon\Carbon::parse($r->event_date);
                        $endDate = $r->end_date ? \Carbon\Carbon::parse($r->end_date) : $startDate;
                        $dayTimes = $r->day_times ?? [];
                        $days = $startDate->diffInDays($endDate) + 1;

                        $formatTimeForDisplay = function ($timeString) {
                            if (empty($timeString) || trim($timeString) === '') {
                                return '';
                            }

                            $timeString = trim($timeString);

                            if (preg_match('/\d{1,2}:\d{2}\s*(AM|PM|am|pm)/i', $timeString)) {
                                return strtoupper($timeString);
                            }

                            try {
                                return \Carbon\Carbon::createFromFormat('H:i', $timeString)->format('g:iA');
                            } catch (\Exception $e) {
                                return $timeString;
                            }
                        };

                        $dateRange = [];
                        for ($i = 0; $i < $days; $i++) {
                            $currentDate = $startDate->copy()->addDays($i);
                            $dateKey = $currentDate->format('Y-m-d');
                            $dateRange[$dateKey] = $currentDate;
                        }
                    @endphp

                    <dl class="event-details-grid text-sm">
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Event Name:</dt>
                            <dd class="event-detail-value"> {{ $r->event_name ?: '-' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Number of Persons:</dt>
                            <dd class="event-detail-value"> {{ $r->number_of_persons ?: '-' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Date &amp; Time:</dt>
                            <dd class="event-detail-value">
                                <div class="event-detail-time-list">
                                    @foreach($dateRange as $dateKey => $currentDate)
                                        @php
                                            $formattedDate = $currentDate->format('M d, Y');
                                            $startTime = '';
                                            $endTime = '';

                                            if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                                                $timeData = $dayTimes[$dateKey];
                                                if (is_array($timeData)) {
                                                    $startTime = $timeData['start_time'] ?? $timeData['start'] ?? '';
                                                    $endTime = $timeData['end_time'] ?? $timeData['end'] ?? '';
                                                } elseif (is_string($timeData)) {
                                                    $parts = explode(' - ', $timeData);
                                                    $startTime = trim($parts[0] ?? '');
                                                    $endTime = trim($parts[1] ?? '');
                                                }
                                            } elseif (is_string($dayTimes) && $days === 1) {
                                                $parts = explode(' - ', $dayTimes);
                                                $startTime = trim($parts[0] ?? '');
                                                $endTime = trim($parts[1] ?? '');
                                            }

                                            if (empty($startTime) && $days === 1 && !empty($r->event_time)) {
                                                $parts = explode(' - ', $r->event_time);
                                                $startTime = trim($parts[0] ?? '');
                                                $endTime = trim($parts[1] ?? '');
                                            }

                                            $formattedStartTime = $formatTimeForDisplay($startTime);
                                            $formattedEndTime = $formatTimeForDisplay($endTime);
                                        @endphp

                                        <div class="event-detail-time-row">
                                            <span>{{ $formattedDate }}:</span>
                                            <span>
                                                @if($formattedStartTime !== '')
                                                    {{ $formattedStartTime }}{{ $formattedEndTime !== '' ? ' - ' . $formattedEndTime : '' }}
                                                @else
                                                    No time specified
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Special Requests:</dt>
                            <dd class="event-detail-value"> {{ $r->special_requests ?: 'None' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Venue:</dt>
                            <dd class="event-detail-value"> {{ $r->venue ?: '-' }}</dd>
                        </div>
                    </dl>

                    <div class="event-detail-divider"></div>

                    <dl class="event-details-contact-grid text-sm">
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Contact Person:</dt>
                            <dd class="event-detail-value"> {{ $r->contact_person ?: optional($r->user)->name ?: '-' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Email:</dt>
                            <dd class="event-detail-value break-all"> {{ $r->email ?: optional($r->user)->email ?: '-' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Department:</dt>
                            <dd class="event-detail-value"> {{ $r->department ?: '-' }}</dd>
                        </div>
                        <div class="event-detail-row">
                            <dt class="event-detail-label">Phone:</dt>
                            <dd class="event-detail-value"> {{ $r->contact_number ?: optional($r->user)->phone ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
                @if(false)
                <dl class="space-y-6 event-details-list">
                    <div>
                        <div class="grid grid-cols-1 md:grid-cols-2 text-sm event-details-layout">
                            <div class="event-details-column">
                                <div class="details-inline-row">
                                    <dt class="text-gray-500 font-medium">Event Name:</dt>
                                    <dd class="mt-1 font-semibold text-gray-900">{{ $r->event_name ?? '—' }}</dd>
                                </div>
                                
                                <div class="details-inline-row">
                                    <dt class="text-gray-500 font-medium">Date & Time:</dt>
                                    <dd class="mt-1 text-gray-900">
                                        @php
                                            $startDate = \Carbon\Carbon::parse($r->event_date);
                                            $endDate = $r->end_date ? \Carbon\Carbon::parse($r->end_date) : $startDate;
                                            $dayTimes = $r->day_times ?? [];
                                            $days = $startDate->diffInDays($endDate) + 1;
                                            
                                            function formatTimeForDisplay($timeString) {
                                                if (empty($timeString) || trim($timeString) === '') return '';
                                                $timeString = trim($timeString);
                                                if (preg_match('/\d{1,2}:\d{2}\s*(AM|PM|am|pm)/i', $timeString)) return strtoupper($timeString);
                                                try {
                                                    return \Carbon\Carbon::createFromFormat('H:i', $timeString)->format('g:iA');
                                                } catch (\Exception $e) { return $timeString; }
                                            }
                                            
                                            $dateRange = [];
                                            for ($i = 0; $i < $days; $i++) {
                                                $currentDate = $startDate->copy()->addDays($i);
                                                $dateKey = $currentDate->format('Y-m-d');
                                                $dateRange[$dateKey] = $currentDate;
                                            }
                                        @endphp
                                        
                                        <div class="reservation-period">
                                            @if($days > 1)
                                                <div class="period-summary font-medium text-gray-900">
                                                    {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                                                </div>
                                            @endif
                                            
                                            <div class="datetime-display space-y-1">
                                                @foreach($dateRange as $dateKey => $currentDate)
                                                    @php
                                                        $formattedDate = $currentDate->format('M d, Y');
                                                        $startTime = '';
                                                        $endTime = '';
                                                        
                                                        if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                                                            $timeData = $dayTimes[$dateKey];
                                                            if (is_array($timeData)) {
                                                                $startTime = $timeData['start_time'] ?? $timeData['start'] ?? '';
                                                                $endTime = $timeData['end_time'] ?? $timeData['end'] ?? '';
                                                            } elseif (is_string($timeData)) {
                                                                $parts = explode(' - ', $timeData);
                                                                $startTime = trim($parts[0] ?? '');
                                                                $endTime = trim($parts[1] ?? '');
                                                            }
                                                        } elseif (is_string($dayTimes) && $days == 1) {
                                                            $parts = explode(' - ', $dayTimes);
                                                            $startTime = trim($parts[0] ?? '');
                                                            $endTime = trim($parts[1] ?? '');
                                                        }
                                                        
                                                        if (empty($startTime) && $days == 1 && !empty($r->event_time)) {
                                                            $parts = explode(' - ', $r->event_time);
                                                            $startTime = trim($parts[0] ?? '');
                                                            $endTime = trim($parts[1] ?? '');
                                                        }
                                                        
                                                        $formattedStartTime = formatTimeForDisplay($startTime);
                                                        $formattedEndTime = formatTimeForDisplay($endTime);
                                                    @endphp
                                                    
                                                    <div class="datetime-item flex items-start">
                                                        <span class="date font-medium text-gray-700 min-w-[120px]">{{ $formattedDate }}</span>
                                                        <span class="time ml-2">
                                                            @if(!empty($formattedStartTime))
                                                                {{ $formattedStartTime }} {{ !empty($formattedEndTime) ? '- ' . $formattedEndTime : '' }}
                                                            @else
                                                                <span class="text-gray-400">No time specified</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                            </div>
                            
                            <div class="event-details-column">
                                <div class="details-inline-row details-inline-row-center">
                                    <dt class="text-gray-500 font-medium">Number of Persons:</dt>
                                    <dd class="mt-1 font-bold text-xl text-green-600">{{ $r->number_of_persons ?? '—' }}</dd>
                                </div>
                                <div class="details-inline-row">
                                    <dt class="text-gray-500 font-medium">Venue:</dt>
                                    <dd class="mt-1 font-semibold text-gray-900">{{ $r->venue ?? '—' }}</dd>
                                </div>
                                <div class="details-inline-row">
                                    <dt class="text-gray-500 font-medium">Special Requests:</dt>
                                    <dd class="mt-1 text-gray-900">{{ $r->special_requests ?? 'None' }}</dd>
                                </div>
                                </div>

                                <div class="md:col-span-2 event-details-contact">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 event-details-contact-grid">
                                        <div class="event-details-column">
                                            <div class="details-inline-row">
                                        <dt class="text-gray-500 font-medium">Contact Person:</dt>
                                        <dd class="mt-1 font-semibold text-gray-900">{{ $r->contact_person ?? optional($r->user)->name ?? 'â€”' }}</dd>
                                    </div>
                                    <div class="details-inline-row">
                                        <dt class="text-gray-500 font-medium">Department:</dt>
                                        <dd class="mt-1 text-gray-900">{{ $r->department ?? 'â€”' }}</dd>
                                    </div>
                                        </div>
                                        <div class="event-details-column min-w-0">
                                            <div class="details-inline-row">
                                                <dt class="text-gray-500 font-medium">Email:</dt>
                                        <dd class="mt-1 text-gray-900 break-all">{{ $r->email ?? optional($r->user)->email ?? 'â€”' }}</dd>
                                    </div>
                                    <div class="details-inline-row">
                                        <dt class="text-gray-500 font-medium">Phone:</dt>
                                        <dd class="mt-1 text-gray-900">{{ $r->contact_number ?? optional($r->user)->phone ?? 'â€”' }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                </dl>
                @endif
            </div>

            {{-- 1. ISOLATED COMPONENT: Selected Menus --}}
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Selected Menus</h2>
                </div>
                
                @php
                    $groupedItems = [];
                    foreach ($r->items as $item) {
                        $dayNumber = $item->day_number ?? 1;
                        if (!isset($groupedItems[$dayNumber])) {
                            $groupedItems[$dayNumber] = [];
                        }
                        $groupedItems[$dayNumber][] = $item;
                    }
                    ksort($groupedItems);
                    
                    $totalAmount = 0;
                @endphp
                
                @if($r->items && $r->items->count() > 0)
                    @foreach($groupedItems as $dayNumber => $dayItems)
                        @php
                            $dayDate = \Carbon\Carbon::parse($r->event_date)->addDays($dayNumber - 1);
                            $formattedDate = $dayDate->format('M d, Y');
                        @endphp
                        
                        <div class="mb-6 bg-white rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-lg text-gray-900">
                                    Day {{ $dayNumber }}: {{ $formattedDate }}
                                </h3>
                            </div>
                            
                            <div class="reservation-table-wrap">
                                <table class="modern-table w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-sm text-gray-500 border-b border-gray-100">
                                        <th class="pb-2">Menu Item</th>
                                        <th class="pb-2">Meal</th>
                                        <th class="pb-2">Quantity</th>
                                        <th class="pb-2">Price</th>
                                        <th class="pb-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dayItems as $item)
                                        @if($item->menu)
                                            @php
                                                $price = $item->price ?? $item->menu->price ?? 150; 
                                                if($price <= 0) {
                                                    $price = ($item->menu->type == 'special' ? 200 : 150);
                                                }
                                                $itemTotal = $item->quantity * $price;
                                                $totalAmount += $itemTotal;
                                            @endphp
                                            <tr class="border-b border-gray-50 last:border-0">
                                                <td class="py-2">
                                                    <div class="font-medium text-gray-900">{{ $item->menu->name }}</div>
                                                    @if($item->menu->items && $item->menu->items->count() > 0)
                                                        <div class="text-xs text-gray-600 mt-1 whitespace-normal">
                                                            @foreach($item->menu->items as $menuItem)
                                                                <span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1 mb-1">
                                                                    {{ $menuItem->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="capitalize py-2">{{ str_replace('_', ' ', $item->meal_time) }}</td>
                                                <td class="font-bold py-2">{{ $item->quantity }}</td>
                                                <td class="py-2">₱{{ number_format($price, 2) }}</td>
                                                <td class="font-bold text-green-600 text-right py-2">₱{{ number_format($itemTotal, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                    
                    @php
                        $grandTotal = $totalAmount + $additionalsTotal;
                    @endphp
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4 relative">
                        <div class="reservation-stack-sm flex justify-between items-center">
                            <div>
                                <div class="text-sm text-green-800">Subtotal:</div>
                                <div class="text-sm text-green-800">Additionals:</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium">₱{{ number_format($totalAmount, 2) }}</div>
                                <div class="text-sm font-medium">₱{{ number_format($additionalsTotal, 2) }}</div>
                            </div>
                        </div>
                        <div class="reservation-stack-sm border-t border-green-300 mt-2 pt-2 flex justify-between items-center">
                            <div class="font-bold text-green-900">Total:</div>
                            <div class="font-bold text-xl text-green-900">₱{{ number_format($grandTotal, 2) }}</div>
                        </div>
                    </div>

                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No menus selected for this reservation.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6 reservation-right-rail">
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6-6h12"></path>
                    </svg>
                    <h2 class="info-card-title">Event Additionals</h2>
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    Add charges requested during the event. These are included in the total automatically.
                </p>

                @php
                    $additionalsLocked = ($r->payment_status ?? 'pending') === 'paid';
                    $canManageAdditionals = $r->status === 'approved' && ! $additionalsLocked;
                @endphp

                @if($canManageAdditionals)
                    <form method="POST" action="{{ route('admin.reservations.additionals.store', $r) }}" class="additionals-form" data-action-loading>
                        @csrf
                        <div class="additionals-field">
                            <label for="additional_name" class="additionals-label">Additional Name</label>
                            <input id="additional_name" name="name" type="text" placeholder="e.g., Soup" class="additionals-input" required>
                        </div>
                        <div class="additionals-field">
                            <label for="additional_price" class="additionals-label">Price</label>
                            <div class="additionals-price-group">
                                <span class="additionals-currency">₱</span>
                                <input id="additional_price" name="price" type="number" step="0.01" min="0" class="additionals-price-input" placeholder="0.00" required>
                            </div>
                        </div>
                        <button type="submit" class="additionals-submit" data-loading-text="Adding Additional...">Add Additional</button>
                    </form>
                @elseif($r->status === 'approved')
                    <div class="mb-6 rounded-admin border border-admin-neutral-200 bg-admin-neutral-50 px-4 py-3 text-sm text-admin-neutral-600">
                        Additionals are locked because this reservation has already been marked as paid.
                    </div>
                @endif

                @if($r->additionals && $r->additionals->count() > 0)
                    <div class="space-y-3">
                        @foreach($r->additionals as $additional)
                            <div class="additionals-item rounded-xl border border-gray-200 bg-white p-3 flex items-start justify-between gap-3">
                                <div class="additionals-item-content">
                                    <p class="text-sm font-semibold text-gray-900">{{ $additional->name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">&#8369;{{ number_format((float) $additional->price, 2) }}</p>
                                </div>
                                @if($canManageAdditionals)
                                    <form method="POST" action="{{ route('admin.reservations.additionals.destroy', [$r, $additional]) }}" class="additionals-item-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="additionals-item-action shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <div class="flex items-center justify-between gap-3">
                            <span class="min-w-0 flex-1 text-sm font-medium text-gray-700">Additionals Total:</span>
                            <span class="shrink-0 whitespace-nowrap text-right text-sm font-semibold text-green-700">&#8369;{{ number_format($additionalsTotal, 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        No additionals recorded.
                    </div>
                @endif
            </div>

            <div class="info-card hidden">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="info-card-title">Customer Information</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="details-inline-row">
                        <dt class="text-gray-500 font-medium">Contact Person:</dt>
                        <dd class="text-gray-900 font-medium">{{ $r->contact_person ?? optional($r->user)->name ?? '—' }}</dd>
                    </div>
                    <div class="details-inline-row">
                        <dt class="text-gray-500 font-medium">Department:</dt>
                        <dd class="text-gray-900">{{ $r->department ?? '—' }}</dd>
                    </div>
                    <div class="details-inline-row">
                        <dt class="text-gray-500 font-medium">Email:</dt>
                        <dd class="text-gray-900">{{ $r->email ?? optional($r->user)->email ?? '—' }}</dd>
                    </div>
                    <div class="details-inline-row">
                        <dt class="text-gray-500 font-medium">Phone:</dt>
                        <dd class="text-gray-900">{{ $r->contact_number ?? optional($r->user)->phone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            {{-- 2. ISOLATED COMPONENT: Reservation & Payment --}}
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation & Payment</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="details-inline-row">
                        <dt class="text-gray-500">Created:</dt>
                        <dd class="text-gray-900">{{ $r->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <div class="details-inline-row details-inline-row-center mb-3">
                            <dt class="text-gray-500 font-medium">Payment Status:</dt>
                            <dd>
                                @if(($r->payment_status ?? 'unpaid') === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 uppercase tracking-wide">
                                        <i class="fas fa-check-circle mr-1"></i> Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 uppercase tracking-wide">
                                        <i class="fas fa-clock mr-1"></i> Unpaid
                                    </span>
                                @endif
                            </dd>
                        </div>

                        @if(($r->payment_status ?? 'unpaid') === 'paid' && !empty($r->or_number))
                            <div class="details-inline-row details-inline-row-center mb-4">
                                <dt class="text-gray-500 font-medium">OR Number:</dt>
                                <dd class="text-gray-900 font-bold bg-gray-100 px-3 py-1 rounded border border-gray-200 break-all">
                                    {{ $r->or_number }}
                                </dd>
                            </div>
                        @endif

                        @if($r->status === 'approved' && ($r->payment_status ?? 'unpaid') !== 'paid')
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-mark-paid' }))" class="w-full action-btn action-btn-approve justify-center mt-2 focus:outline-none">
                                <i class="fas fa-receipt text-lg mr-2"></i> Mark as Paid (Enter OR)
                            </button>
                        @endif
                    </div>
                </dl>

                @if(false)
                {{-- Mark Paid Modal (Inside the x-data) --}}
                <div x-cloak x-show="markPaidOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                    <div @click="markPaidOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                    <div class="modern-modal p-6 w-full max-w-sm relative z-10" x-transition.scale.90>
                        <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-20" @click="markPaidOpen = false">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        <h3 class="text-lg font-bold mb-2">Mark as Paid</h3>
                        <p class="text-sm text-gray-600 mb-5">Please enter the Official Receipt (OR) Number from the cashier to mark this reservation as completely paid.</p>
                        <form method="POST" action="{{ route('admin.reservations.mark_paid', $r->id) }}" class="space-y-4">
                            @csrf 
                            <div class="space-y-2">
                                <label for="or_number" class="block text-sm font-semibold text-gray-700">Official Receipt (OR) Number</label>
                                <input type="text" name="or_number" id="or_number" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                        placeholder="e.g., OR-1029384"
                                        value="{{ old('or_number') }}">
                            </div>
                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium" @click="markPaidOpen = false">Cancel</button>
                                <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md flex items-center">
                                    <i class="fas fa-check mr-2"></i> Save Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                <x-admin.ui.modal name="reservation-mark-paid" title="Mark as Paid" variant="info" maxWidth="sm" icon="fa-receipt">
                    <p class="text-sm text-admin-neutral-600 mb-4">Enter the official receipt number from the cashier to mark this reservation as fully paid.</p>
                    <form id="reservation-mark-paid-form" method="POST" action="{{ route('admin.reservations.mark_paid', $r->id) }}" class="space-y-4" data-action-loading>
                        @csrf
                        <div>
                            <label for="or_number" class="block text-sm font-semibold text-admin-neutral-700 mb-2">Official Receipt (OR) Number</label>
                            <input
                                type="text"
                                name="or_number"
                                id="or_number"
                                required
                                value="{{ old('or_number') }}"
                                placeholder="e.g. OR-1029384"
                                class="block w-full rounded-admin border border-admin-neutral-300 bg-admin-neutral-50 px-3 py-2 text-sm text-admin-neutral-900 placeholder-admin-neutral-400 transition-all duration-200 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                            >
                        </div>
                    </form>
                    <x-slot name="footer">
                        <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-mark-paid' }))">
                            Cancel
                        </x-admin.ui.button.secondary>
                        <x-admin.ui.button.primary type="submit" form="reservation-mark-paid-form" data-loading-text="Saving Payment...">
                            Save Payment
                        </x-admin.ui.button.primary>
                    </x-slot>
                </x-admin.ui.modal>
            </div>

            @if($r->status !== 'approved' && $r->status !== 'declined')
            <div class="reservation-actions-affix-anchor" id="reservation-actions-anchor">
            <div class="info-card reservation-actions-card" id="decline">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation Actions</h2>
                </div>

                <form method="POST" action="{{ route('admin.reservations.approve', $r) }}" class="mb-4" id="approveForm" data-action-loading>
                    @csrf @method('PATCH')
                    <input type="hidden" name="force_approve" id="forceApproveInput" value="{{ old('force_approve', 0) }}">
                    <input type="hidden" name="force_overlap_approve" id="forceOverlapApproveInput" value="{{ old('force_overlap_approve', 0) }}">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-approve-confirmation' }))" class="action-btn action-btn-approve w-full justify-center">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Accept Reservation
                    </button>
                </form>

                <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-decline-confirmation' }))" class="action-btn action-btn-decline w-full justify-center">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                    Decline Reservation
                </button>
            </div>
            </div>
            @endif

            @if($r->status === 'declined' && !empty($r->decline_reason))
            <div class="info-card">
                <div class="info-card-header">
                    <h2 class="info-card-title text-red-600">Decline Reason</h2>
                </div>
                <p class="text-sm text-gray-700">{{ $r->decline_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Unified Reservation Modals --}}
    <x-admin.ui.modal name="reservation-approve-confirmation" title="Confirm Approval" variant="confirmation" maxWidth="sm" icon="fa-check">
        <p class="text-sm text-admin-neutral-700">Approve this reservation now?</p>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-approve-confirmation' }))">
                Cancel
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.primary
                type="submit"
                form="approveForm"
                data-loading-text="Approving Reservation..."
                onclick="document.getElementById('forceApproveInput').value = '0'; document.getElementById('forceOverlapApproveInput').value = '0';"
            >
                Approve
            </x-admin.ui.button.primary>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="reservation-decline-confirmation" title="Confirm Decline" variant="warning" maxWidth="sm" icon="fa-triangle-exclamation">
        <p class="text-sm text-admin-neutral-700">Continue to decline this reservation?</p>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-decline-confirmation' }))">
                Cancel
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.danger
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-decline-confirmation' })); window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-decline-reason' }));"
            >
                Continue
            </x-admin.ui.button.danger>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="reservation-decline-reason" title="Decline Reason" variant="error" maxWidth="lg" icon="fa-circle-xmark">
        <form id="reservation-decline-form" method="POST" action="{{ route('admin.reservations.decline', $r) }}" class="space-y-4" data-action-loading>
            @csrf
            @method('PATCH')
            <div>
                <label for="decline_reason" class="block text-sm font-semibold text-admin-neutral-700 mb-2">Reason</label>
                <textarea
                    id="decline_reason"
                    name="reason"
                    rows="4"
                    required
                    class="block w-full rounded-admin border border-admin-neutral-300 bg-admin-neutral-50 px-3 py-2 text-sm text-admin-neutral-900 placeholder-admin-neutral-400 transition-all duration-200 focus:ring-2 focus:ring-admin-primary/20 focus:border-admin-primary"
                >{{ old('reason') }}</textarea>
            </div>
        </form>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-decline-reason' }))">
                Cancel
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.danger type="submit" form="reservation-decline-form" data-loading-text="Submitting Decline...">
                Submit Decline
            </x-admin.ui.button.danger>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="reservation-inventory-warning" title="Inventory Warning" variant="warning" maxWidth="lg" icon="fa-box-open">
        <p class="text-sm text-admin-neutral-700 mb-4">Approving this reservation exceeds current inventory for the following items:</p>
        <ul class="list-disc list-inside space-y-2 text-sm text-admin-neutral-600">
            @foreach($insufficientItems as $item)
                <li>
                    <span class="font-semibold text-admin-neutral-900">{{ $item['name'] ?? 'Item' }}</span>:
                    Need {{ number_format((float) ($item['required'] ?? 0), 2) }},
                    Available <span class="font-semibold text-admin-danger">{{ number_format((float) ($item['available'] ?? 0), 2) }}</span>
                </li>
            @endforeach
        </ul>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-inventory-warning' }))">
                Cancel Approval
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.primary
                type="submit"
                form="approveForm"
                class="bg-admin-warning hover:bg-yellow-600 focus:ring-admin-warning"
                data-loading-text="Approving Anyway..."
                onclick="document.getElementById('forceApproveInput').value = '1';"
            >
                Approve Anyway
            </x-admin.ui.button.primary>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="reservation-overlap-warning" title="Schedule Overlap Detected" variant="warning" maxWidth="lg" icon="fa-calendar-xmark">
        <p class="text-sm text-admin-neutral-700 mb-3">
            There is already an approved reservation (ID: <span class="font-semibold">{{ $overlapReservationId ?? '-' }}</span>)
            scheduled on <span class="font-semibold">{{ $overlapDate ?? '-' }}</span>.
        </p>
        <p class="text-sm text-admin-neutral-600">Are you sure you want to approve this reservation on the same date?</p>
        <x-slot name="footer">
            <x-admin.ui.button.secondary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-overlap-warning' }))">
                Cancel Approval
            </x-admin.ui.button.secondary>
            <x-admin.ui.button.primary
                type="submit"
                form="approveForm"
                class="bg-orange-500 hover:bg-orange-600 focus:ring-orange-400"
                data-loading-text="Approving Anyway..."
                onclick="document.getElementById('forceOverlapApproveInput').value = '1';"
            >
                Approve Anyway
            </x-admin.ui.button.primary>
        </x-slot>
    </x-admin.ui.modal>

    <x-admin.ui.modal name="reservation-accepted" title="Reservation Approved" variant="confirmation" maxWidth="sm" icon="fa-check">
        <p class="text-sm text-admin-neutral-700">The customer will be notified.</p>
        <x-slot name="footer">
            <x-admin.ui.button.primary type="button" onclick="window.dispatchEvent(new CustomEvent('close-admin-modal', { detail: 'reservation-accepted' }))">
                Close
            </x-admin.ui.button.primary>
        </x-slot>
    </x-admin.ui.modal>
</div>

<script>
    (() => {
        const openReservationModalsFromState = () => {
            if (@json($acceptedModal)) {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-accepted' }));
            }

            if (@json($inventoryWarning)) {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-inventory-warning' }));
            }

            if (@json($overlapWarning)) {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-overlap-warning' }));
            }

            if (@json($errors->has('or_number'))) {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-mark-paid' }));
            }

            if (@json($errors->has('reason'))) {
                window.dispatchEvent(new CustomEvent('open-admin-modal', { detail: 'reservation-decline-reason' }));
            }
        };

        document.addEventListener('DOMContentLoaded', openReservationModalsFromState);
        document.addEventListener('livewire:navigated', openReservationModalsFromState);
    })();

</script>
@endsection
