{{-- resources/views/admin/reservations/show.blade.php --}}
@extends('layouts.sidebar')
@section('page-title','Reservation #'.$r->id)

@section('content')
<style>
/* Modern Card Styles */
.modern-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--neutral-100);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}


/* Action Buttons */
.action-btn {
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

.action-btn-approve {
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.action-btn-approve:hover {
    transform: translateY(-1px);
}

.action-btn-decline {
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-decline:hover {
    transform: translateY(-1px);
}

/* Back Button */
.back-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid var(--neutral-300);
    background: white;
    color: var(--neutral-700);
}

.back-btn:hover {
    background: var(--neutral-50);
    border-color: var(--neutral-400);
    transform: translateY(-1px);
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

/* Modal Styles */
.modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-200);
}

/* Back Button Container */
.back-button-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--neutral-200);
}


[x-cloak] { display: none !important; }
</style>

<div class="modern-card p-6 mx-auto max-w-full"
     x-data="reservationShow({
        accepted:@js(session('accepted',false)),
        declined:@js(session('declined',false)),
        inventoryWarning:@js(session('inventory_warning',false)),
        insufficientItems:@js(session('insufficient_items',[])),
        overlapWarning:@js(session('overlap_warning',false)),
        overlapReservationId:@js(session('overlap_reservation_id',null)),
        overlapDate:@js(session('overlap_reservation_date',null))
})"
     x-effect="document.body.classList.toggle('overflow-hidden', approveConfirmationOpen || declineConfirmationOpen || acceptedOpen || inventoryWarningOpen || declineOpen || overlapWarningOpen)"
     @keydown.escape.window="approveConfirmationOpen = false; declineConfirmationOpen = false; acceptedOpen = false; inventoryWarningOpen = false; declineOpen = false; overlapWarningOpen = false">
    
    <!-- Header -->
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
                <h1 class="header-title">Reservation #{{ $r->id }}</h1>
                <p class="header-subtitle">Review and manage reservation details</p>
            </div>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="info-card-title">Event Details</h2>
                </div>
<dl class="space-y-6 ">
    <div>
<div class="info-label">EVENT INFORMATION</div>
<div class="space-y-4 mt-3 grid grid-cols-1 md:grid-cols-2 text-sm">
    <!-- Left Column -->
    <div class="space-y-4">
        <!-- Event Name -->
        <div>
            <dt class="text-gray-500 font-medium">Event Name:</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $r->event_name ?? '—' }}</dd>
        </div>
        
        <!-- Date & Time -->
        <div>
            <dt class="text-gray-500 font-medium">Date & Time:</dt>
            <dd class="mt-1 text-gray-900">
                @php
                    $startDate = \Carbon\Carbon::parse($r->event_date);
                    $endDate = $r->end_date ? \Carbon\Carbon::parse($r->end_date) : $startDate;
                    
                    // Get day_times - already cast to array by Laravel
                    $dayTimes = $r->day_times ?? [];
                    
                    $days = $startDate->diffInDays($endDate) + 1;
                    
                    // Helper function to format time
                    function formatTimeForDisplay($timeString) {
                        if (empty($timeString) || trim($timeString) === '') {
                            return '';
                        }
                        
                        // Remove any extra spaces
                        $timeString = trim($timeString);
                        
                        // If it already looks like a time (contains : and AM/PM)
                        if (preg_match('/\d{1,2}:\d{2}\s*(AM|PM|am|pm)/i', $timeString)) {
                            // Convert to proper case
                            $timeString = strtoupper($timeString);
                            return $timeString;
                        }
                        
                        // Try to parse common time formats
                        $timeFormats = [
                            'H:i',      // 24-hour: 14:30
                            'H:i:s',    // 24-hour with seconds: 14:30:00
                            'g:i A',    // 12-hour: 2:30 PM
                            'g:i:s A',  // 12-hour with seconds: 2:30:00 PM
                            'g:i a',    // 12-hour lowercase: 2:30 pm
                        ];
                        
                        foreach ($timeFormats as $format) {
                            try {
                                $time = \Carbon\Carbon::createFromFormat($format, $timeString);
                                return $time->format('g:iA'); // Convert to 2:30PM format
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                        
                        // If all parsing fails, return as-is
                        return $timeString;
                    }
                    
                    // Generate date range
                    $dateRange = [];
                    for ($i = 0; $i < $days; $i++) {
                        $currentDate = $startDate->copy()->addDays($i);
                        $dateKey = $currentDate->format('Y-m-d');
                        $dateRange[$dateKey] = $currentDate;
                    }
                @endphp
                
                <div class="reservation-period">
                    <!-- Summary Line -->
                    <div class="period-summary mb-2 font-medium text-gray-900">
                        @if($days > 1)
                            {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                        @else
                            {{ $startDate->format('M d, Y') }}
                        @endif
                    </div>
                    
                    <!-- Detailed Date-Time List -->
                    <div class="datetime-display space-y-1">
                        @foreach($dateRange as $dateKey => $currentDate)
                            @php
                                $formattedDate = $currentDate->format('M d, Y');
                                $startTime = '';
                                $endTime = '';
                                
                                // Check if we have time data for this date
                                if (is_array($dayTimes) && isset($dayTimes[$dateKey])) {
                                    $timeData = $dayTimes[$dateKey];
                                    
                                    if (is_array($timeData)) {
                                        // Array structure
                                        $startTime = $timeData['start_time'] ?? $timeData['start'] ?? $timeData['time_start'] ?? '';
                                        $endTime = $timeData['end_time'] ?? $timeData['end'] ?? $timeData['time_end'] ?? '';
                                    } elseif (is_string($timeData)) {
                                        // String like "9:00 AM - 5:00 PM"
                                        $parts = explode(' - ', $timeData);
                                        if (count($parts) >= 2) {
                                            $startTime = trim($parts[0]);
                                            $endTime = trim($parts[1]);
                                        } elseif (count($parts) == 1) {
                                            $startTime = trim($parts[0]);
                                        }
                                    }
                                } elseif (is_string($dayTimes) && $days == 1) {
                                    // Single string for single day
                                    $parts = explode(' - ', $dayTimes);
                                    if (count($parts) >= 2) {
                                        $startTime = trim($parts[0]);
                                        $endTime = trim($parts[1]);
                                    } elseif (count($parts) == 1) {
                                        $startTime = trim($parts[0]);
                                    }
                                }
                                
                                // Also check event_time for single day
                                if (empty($startTime) && $days == 1 && !empty($r->event_time)) {
                                    $parts = explode(' - ', $r->event_time);
                                    if (count($parts) >= 2) {
                                        $startTime = trim($parts[0]);
                                        $endTime = trim($parts[1]);
                                    } elseif (count($parts) == 1) {
                                        $startTime = trim($parts[0]);
                                    }
                                }
                                
                                $formattedStartTime = formatTimeForDisplay($startTime);
                                $formattedEndTime = formatTimeForDisplay($endTime);
                                
                                $hasStartTime = !empty($formattedStartTime);
                                $hasEndTime = !empty($formattedEndTime);
                            @endphp
                            
                            <div class="datetime-item flex items-start">
                                <span class="date font-medium text-gray-700 min-w-[120px]">{{ $formattedDate }}</span>
                                <span class="time ml-2">
                                    @if($hasStartTime && $hasEndTime)
                                        {{ $formattedStartTime }} - {{ $formattedEndTime }}
                                    @elseif($hasStartTime)
                                        {{ $formattedStartTime }}
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
    
    <!-- Right Column -->
    <div class="space-y-4">
        <!-- Number of Persons -->
        <div>
            <dt class="text-gray-500 font-medium">Number of Persons:</dt>
            <dd class="mt-1 font-bold text-xl text-green-600">{{ $r->number_of_persons ?? '—' }}</dd>
        </div>
        
        <!-- Venue -->
        <div>
            <dt class="text-gray-500 font-medium">Venue:</dt>
            <dd class="mt-1 font-semibold text-gray-900">{{ $r->venue ?? '—' }}</dd>
        </div>
        
        <!-- Special Requests -->
        <div>
            <dt class="text-gray-500 font-medium">Special Requests:</dt>
            <dd class="mt-1 text-gray-900">
                {{ $r->special_requests ?? 'None' }}
            </dd>
        </div>
    </div>
</div>
    </div>
</dl>
            </div>

            <!-- Menus Ordered -->
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Selected Menus</h2>
                </div>
                
                @php
                    // Group items by day
                    $groupedItems = [];
                    foreach ($r->items as $item) {
                        $dayNumber = $item->day_number ?? 1;
                        if (!isset($groupedItems[$dayNumber])) {
                            $groupedItems[$dayNumber] = [];
                        }
                        $groupedItems[$dayNumber][] = $item;
                    }
                    ksort($groupedItems);
                    
                    // Calculate total amount
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
                            
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>Menu Item</th>
                                        <th>Meal</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dayItems as $item)
                                        @if($item->menu)
                                            @php
                                                $price = $item->menu->price ?? 150;
                                                $itemTotal = $item->quantity * $price;
                                                $totalAmount += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="font-medium text-gray-900">{{ $item->menu->name }}</div>
                                                    @if($item->menu->items && $item->menu->items->count() > 0)
                                                        <div class="text-xs text-gray-600 mt-1">
                                                            @foreach($item->menu->items as $menuItem)
                                                                <span class="inline-block bg-gray-100 rounded px-2 py-1 mr-1 mb-1">
                                                                    {{ $menuItem->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="capitalize">{{ str_replace('_', ' ', $item->meal_time) }}</td>
                                                <td class="font-bold">{{ $item->quantity }}</td>
                                                <td>₱{{ number_format($price, 2) }}</td>
                                                <td class="font-bold text-green-600">₱{{ number_format($itemTotal, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    
                    <!-- Total Amount -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-green-800">Subtotal:</div>
                                <div class="text-sm text-green-800">Service Fee:</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium">₱{{ number_format($totalAmount, 2) }}</div>
                                <div class="text-sm font-medium">₱0.00</div>
                            </div>
                        </div>
                        <div class="border-t border-green-300 mt-2 pt-2 flex justify-between items-center">
                            <div class="font-bold text-green-900">Total:</div>
                            <div class="font-bold text-xl text-green-900">₱{{ number_format($totalAmount, 2) }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p>No menus selected for this reservation.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h2 class="info-card-title">Customer Information</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Contact Person</dt>
                        <dd class="text-gray-900 font-medium">{{ $r->contact_person ?? optional($r->user)->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Department</dt>
                        <dd class="text-gray-900">{{ $r->department ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Email</dt>
                        <dd class="text-gray-900">{{ $r->email ?? optional($r->user)->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Phone</dt>
                        <dd class="text-gray-900">{{ $r->contact_number ?? optional($r->user)->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Address</dt>
                        <dd class="text-gray-900">{{ $r->address ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Project Name</dt>
                        <dd class="text-gray-900">{{ $r->project_name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Account Code</dt>
                        <dd class="text-gray-900">{{ $r->account_code ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            @if($r->status !== 'approved' && $r->status !== 'declined')
            <!-- Actions -->
            <div class="info-card" id="decline">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation Actions</h2>
                </div>

                <form method="POST" action="{{ route('admin.reservations.approve', $r) }}" class="mb-4" id="approveForm">
                    @csrf @method('PATCH')
                    <input type="hidden" name="force_approve" id="forceApproveInput" value="0">
                    <button type="button" @click="openApproveConfirmation()" class="action-btn action-btn-approve w-full justify-center">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Accept Reservation
                    </button>
                </form>

                <button type="button" @click="openDeclineConfirmation()" class="action-btn action-btn-decline w-full justify-center">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                    </svg>
                    Decline Reservation
                </button>
            </div>
            @endif

            @if($r->status === 'declined' && !empty($r->decline_reason))
            <!-- Decline Reason -->
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Decline Reason</h2>
                </div>
                <p class="text-sm text-gray-700">{{ $r->decline_reason }}</p>
            </div>
            @endif
            
            <!-- Reservation Info -->
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation Info</h2>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Created:</dt>
                        <dd class="text-gray-900">{{ $r->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    @if($r->updated_at->ne($r->created_at))
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Last Updated:</dt>
                        <dd class="text-gray-900">{{ $r->updated_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Approve Confirmation Modal --}}
    <div x-cloak x-show="approveConfirmationOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="approveConfirmationOpen=false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Approval</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to approve this reservation?</p>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="approveConfirmationOpen=false">Cancel</button>
                <button type="button" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium" @click="handleApprove()">Yes, Approve</button>
            </div>
        </div>
    </div>

    {{-- Decline Confirmation Modal --}}
    <div x-cloak x-show="declineConfirmationOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="declineConfirmationOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Confirm Decline</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to decline this reservation?</p>
            <div class="flex justify-center gap-3">
                <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="declineConfirmationOpen = false">Cancel</button>
                <button type="button" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium" @click="openDeclineForm()">Yes, Decline</button>
            </div>
        </div>
    </div>

    {{-- Overlap Warning Modal --}}
    <div x-cloak x-show="overlapWarningOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="overlapWarningOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-lg relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center mb-4">
                <svg class="w-10 h-10 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Schedule Conflict</h3>
                    <p class="text-sm text-gray-600">This reservation overlaps with an already approved reservation.</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 mb-5">
                This reservation overlaps with reservation
                <span class="font-semibold">#<span x-text="overlapReservationId ?? 'N/A'"></span></span>
                <template x-if="overlapDate">
                    <span> on <span x-text="overlapDate"></span></span>
                </template>.
                You can’t approve overlapping reservations. Would you like to proceed with declining this reservation?
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="overlapWarningOpen = false">Close</button>
                <button type="button" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium" @click="openDeclineFromOverlap()">Proceed to Decline</button>
            </div>
        </div>
    </div>
    

    {{-- Accepted popup --}}
    <div x-cloak x-show="acceptedOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="acceptedOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Reservation Accepted</h3>
            <p class="text-sm text-gray-600">Inventory was updated and the customer was notified.</p>
            <button class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium" @click="acceptedOpen = false">OK</button>
        </div>
    </div>
    {{-- Inventory Warning modal --}}
    <div x-cloak x-show="inventoryWarningOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="inventoryWarningOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto relative z-10"
             x-transition.scale.90
             @click.stop>
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors duration-200 z-20" @click="inventoryWarningOpen = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div class="flex items-center mb-4">
                <svg class="w-12 h-12 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900">Insufficient Inventory</h3>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">The following ingredients do not have enough quantity to fulfill this reservation:</p>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="text-left">Ingredient</th>
                            <th class="text-right">Required</th>
                            <th class="text-right">Available</th>
                            <th class="text-right">Shortage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in insufficientItems" :key="item.name">
                            <tr>
                                <td class="text-gray-900" x-text="item.name"></td>
                                <td class="text-right text-gray-700">
                                    <span x-text="item.required.toFixed(2)"></span>
                                    <span class="text-xs text-gray-500 ml-1" x-text="item.unit"></span>
                                </td>
                                <td class="text-right text-gray-700">
                                    <span x-text="item.available.toFixed(2)"></span>
                                    <span class="text-xs text-gray-500 ml-1" x-text="item.unit"></span>
                                </td>
                                <td class="text-right text-red-600 font-semibold">
                                    <span x-text="item.shortage.toFixed(2)"></span>
                                    <span class="text-xs ml-1" x-text="item.unit"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">What happens if you proceed?</p>
                        <p>If you approve this reservation, the inventory will be deducted as much as possible. Items with insufficient stock will be reduced to zero.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="inventoryWarningOpen = false">Cancel</button>
                <button type="button" @click="proceedWithApproval()" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 font-medium">
                    Proceed Anyway
                </button>
            </div>
        </div>
    </div>

    {{-- Decline Form Modal --}}
    <div x-cloak x-show="declineOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="declineOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-lg relative z-10"
             x-transition.scale.90
             @click.stop>
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors duration-200" @click="declineOpen = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h3 class="text-lg font-semibold mb-3">Decline Reservation</h3>
            <p class="text-sm text-gray-600 mb-6">Please provide a reason. The customer will receive this via email and SMS.</p>

            <form method="POST" action="{{ route('admin.reservations.decline', $r) }}" class="space-y-4">
                @csrf @method('PATCH')
                <div class="space-y-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason for declining</label>
                    <textarea name="reason" id="reason" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none" placeholder="Please provide a detailed reason for declining this reservation..."></textarea>
                    @error('reason') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 font-medium" @click="declineOpen = false">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
