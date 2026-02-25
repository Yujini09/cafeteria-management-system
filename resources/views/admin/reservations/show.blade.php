@extends('layouts.sidebar')
@section('page-title','Reservation #'.$r->id)

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

/* Icon Sizes */
.reservation-show-view .icon-sm { width: 14px; height: 14px; }
.reservation-show-view .icon-md { width: 16px; height: 16px; }
.reservation-show-view .icon-lg { width: 20px; height: 20px; }

/* Modal Styles */
.reservation-show-view .modern-modal {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--neutral-200);
}

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

[x-cloak] { display: none !important; }
</style>

<div class="admin-page-shell reservation-show-view p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0"
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
     @keydown.escape.window="approveConfirmationOpen = false; declineConfirmationOpen = false; acceptedOpen = false; inventoryWarningOpen = false; declineOpen = false; overlapWarningOpen = false;">
    
    @php
        $additionalsTotal = $r->additionals ? $r->additionals->sum('price') : 0;
        $serviceFee = $r->service_fee ?? 0;
    @endphp

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

    {{-- Success Message for Payment updates --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-gray-500 font-medium">Event Name:</dt>
                                    <dd class="mt-1 font-semibold text-gray-900">{{ $r->event_name ?? '—' }}</dd>
                                </div>
                                
                                <div>
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
                                            <div class="period-summary mb-2 font-medium text-gray-900">
                                                @if($days > 1)
                                                    {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                                                @else
                                                    {{ $startDate->format('M d, Y') }}
                                                @endif
                                            </div>
                                            
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
                            
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-gray-500 font-medium">Number of Persons:</dt>
                                    <dd class="mt-1 font-bold text-xl text-green-600">{{ $r->number_of_persons ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 font-medium">Venue:</dt>
                                    <dd class="mt-1 font-semibold text-gray-900">{{ $r->venue ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 font-medium">Special Requests:</dt>
                                    <dd class="mt-1 text-gray-900">{{ $r->special_requests ?? 'None' }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </dl>
            </div>

            {{-- 1. ISOLATED COMPONENT: Selected Menus & Service Fee Edit --}}
            <div class="info-card" x-data="{ serviceFeeOpen: false }">
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
                                                        <div class="text-xs text-gray-600 mt-1">
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
                    @endforeach
                    
                    @php
                        $grandTotal = $totalAmount + $additionalsTotal + $serviceFee;
                    @endphp
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4 relative">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-sm text-green-800">Subtotal:</div>
                                <div class="text-sm text-green-800">Additionals:</div>
                                <div class="text-sm text-green-800 flex items-center">
                                    Service Fee: 
                                    <button type="button" @click="serviceFeeOpen = true" class="ml-2 text-green-600 hover:text-green-800 focus:outline-none">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium">₱{{ number_format($totalAmount, 2) }}</div>
                                <div class="text-sm font-medium">₱{{ number_format($additionalsTotal, 2) }}</div>
                                <div class="text-sm font-medium">₱{{ number_format($serviceFee, 2) }}</div>
                            </div>
                        </div>
                        <div class="border-t border-green-300 mt-2 pt-2 flex justify-between items-center">
                            <div class="font-bold text-green-900">Total:</div>
                            <div class="font-bold text-xl text-green-900">₱{{ number_format($grandTotal, 2) }}</div>
                        </div>
                    </div>

                    {{-- Edit Service Fee Modal --}}
                    <div x-cloak x-show="serviceFeeOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div @click="serviceFeeOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
                        <div class="modern-modal p-6 w-full max-w-sm relative z-10" x-transition.scale.90>
                            <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 z-20" @click="serviceFeeOpen = false">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <h3 class="text-lg font-bold mb-4">Edit Service Fee</h3>
                            <form method="POST" action="{{ route('admin.reservations.service_fee.update', $r->id) }}">
                                @csrf @method('PATCH')
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Service Fee Amount</label>
                                    <div class="additionals-price-group">
                                        <span class="additionals-currency">₱</span>
                                        <input name="service_fee" type="number" step="0.01" min="0" class="additionals-price-input" value="{{ $serviceFee }}" required>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium" @click="serviceFeeOpen = false">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md">Update Fee</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No menus selected for this reservation.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
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

                @if($r->status === 'approved')
                    <form method="POST" action="{{ route('admin.reservations.additionals.store', $r) }}" class="grid grid-cols-1 sm:grid-cols-6 gap-3 mb-6" data-action-loading>
                        @csrf
                        <div class="sm:col-span-3">
                            <label for="additional_name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Additional Name</label>
                            <input id="additional_name" name="name" type="text" placeholder="e.g. Extra chairs" class="additionals-input" required>
                        </div>
                        <div class="sm:col-span-3">
                            <label for="additional_price" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Price</label>
                            <div class="additionals-price-group">
                                <span class="additionals-currency">₱</span>
                                <input id="additional_price" name="price" type="number" step="0.01" min="0" class="additionals-price-input" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="sm:col-span-6 flex flex-wrap justify-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 text-sm font-semibold w-full sm:w-auto">Add</button>
                        </div>
                    </form>
                @endif

                @if($r->additionals && $r->additionals->count() > 0)
                    <div class="space-y-3">
                        @foreach($r->additionals as $additional)
                            <div class="rounded-xl border border-gray-200 bg-white p-3 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $additional->name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">&#8369;{{ number_format((float) $additional->price, 2) }}</p>
                                </div>
                                @if($r->status === 'approved')
                                    <form method="POST" action="{{ route('admin.reservations.additionals.destroy', [$r, $additional]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Delete">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200">
                        <span class="additionals-pill">Additionals Total</span>
                        <span class="text-sm font-semibold text-green-700">&#8369;{{ number_format($additionalsTotal, 2) }}</span>
                    </div>
                @else
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        No additionals recorded.
                    </div>
                @endif
            </div>

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
                </dl>
            </div>

            {{-- 2. ISOLATED COMPONENT: Reservation & Payment --}}
            <div class="info-card" x-data="{ markPaidOpen: false }">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation & Payment</h2>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Created:</dt>
                        <dd class="text-gray-900">{{ $r->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    
                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-3">
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
                            <div class="flex justify-between items-center mb-4">
                                <dt class="text-gray-500 font-medium">OR Number:</dt>
                                <dd class="text-gray-900 font-bold bg-gray-100 px-3 py-1 rounded border border-gray-200">
                                    {{ $r->or_number }}
                                </dd>
                            </div>
                        @endif

                        @if($r->status === 'approved' && ($r->payment_status ?? 'unpaid') !== 'paid')
                            <button type="button" @click="markPaidOpen = true" class="w-full action-btn action-btn-approve justify-center mt-2 focus:outline-none">
                                <i class="fas fa-receipt text-lg mr-2"></i> Mark as Paid (Enter OR)
                            </button>
                        @endif
                    </div>
                </dl>

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
            </div>

            @if($r->status !== 'approved' && $r->status !== 'declined')
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
            <div class="info-card">
                <div class="info-card-header">
                    <h2 class="info-card-title text-red-600">Decline Reason</h2>
                </div>
                <p class="text-sm text-gray-700">{{ $r->decline_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Main Page Confirmation Modals (Using reservationShow scope) --}}
    <div x-cloak x-show="approveConfirmationOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="approveConfirmationOpen=false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10">
            <h3 class="text-lg font-semibold mb-2">Confirm Approval</h3>
            <div class="flex justify-center gap-3 mt-4">
                <button @click="approveConfirmationOpen=false" class="px-6 py-2 bg-gray-200 rounded-lg">Cancel</button>
                <button @click="handleApprove($event)" class="px-6 py-2 bg-green-600 text-white rounded-lg">Approve</button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="declineConfirmationOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="declineConfirmationOpen=false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10">
            <h3 class="text-lg font-semibold mb-2">Confirm Decline</h3>
            <div class="flex justify-center gap-3 mt-4">
                <button @click="declineConfirmationOpen=false" class="px-6 py-2 bg-gray-200 rounded-lg">Cancel</button>
                <button @click="openDeclineForm()" class="px-6 py-2 bg-red-600 text-white rounded-lg">Decline</button>
            </div>
        </div>
    </div>
    
    <div x-cloak x-show="declineOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="declineOpen = false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-6 w-full max-w-lg relative z-10">
            <h3 class="text-lg font-semibold mb-3">Decline Reason</h3>
            <form method="POST" action="{{ route('admin.reservations.decline', $r) }}">
                @csrf @method('PATCH')
                <textarea name="reason" rows="4" required class="w-full border rounded-lg p-2"></textarea>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="declineOpen = false" class="px-4 py-2 bg-gray-200 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div x-cloak x-show="inventoryWarningOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="inventoryWarningOpen=false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-6 w-full max-w-lg relative z-10 border-l-4 border-yellow-500">
            <h3 class="text-lg font-bold text-yellow-700 mb-2">Inventory Warning</h3>
            <p class="text-sm text-gray-700 mb-4">Approving this reservation exceeds current inventory for the following items:</p>
            <ul class="list-disc list-inside text-sm text-gray-600 mb-6 pl-4">
                <template x-for="item in insufficientItems" :key="item.name">
                    <li>
                        <span class="font-semibold" x-text="item.name"></span>: 
                        Need <span x-text="item.needed"></span>, 
                        Available <span x-text="item.available" class="text-red-500 font-bold"></span>
                    </li>
                </template>
            </ul>
            <div class="flex justify-end gap-3">
                <button @click="inventoryWarningOpen=false" class="px-5 py-2 bg-gray-200 rounded-lg text-sm font-medium">Cancel Approval</button>
                <button @click="forceApprove()" class="px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium">Approve Anyway</button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="overlapWarningOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="overlapWarningOpen=false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-6 w-full max-w-lg relative z-10 border-l-4 border-orange-500">
            <h3 class="text-lg font-bold text-orange-700 mb-2">Schedule Overlap Detected</h3>
            <p class="text-sm text-gray-700 mb-4">
                There is already an approved reservation (ID: <span class="font-bold" x-text="overlapReservationId"></span>) 
                scheduled on <span class="font-bold" x-text="overlapDate"></span>.
            </p>
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to approve this reservation on the same date?</p>
            <div class="flex justify-end gap-3">
                <button @click="overlapWarningOpen=false" class="px-5 py-2 bg-gray-200 rounded-lg text-sm font-medium">Cancel Approval</button>
                <button @click="forceApprove()" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium">Approve Anyway</button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="acceptedOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="acceptedOpen=false" class="absolute inset-0 bg-black/40"></div>
        <div class="modern-modal p-8 w-full max-w-sm text-center relative z-10">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Reservation Approved!</h3>
            <p class="text-gray-500 mb-6">The customer will be notified.</p>
            <button @click="acceptedOpen=false" class="w-full py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg font-medium">Close</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reservationShow', (config) => ({
            approveConfirmationOpen: false,
            declineConfirmationOpen: false,
            acceptedOpen: config.accepted,
            inventoryWarningOpen: config.inventoryWarning,
            declineOpen: false,
            overlapWarningOpen: config.overlapWarning,
            insufficientItems: config.insufficientItems || [],
            overlapReservationId: config.overlapReservationId,
            overlapDate: config.overlapDate,

            openApproveConfirmation() {
                this.approveConfirmationOpen = true;
            },
            handleApprove(event) {
                document.getElementById('approveForm').submit();
            },
            openDeclineConfirmation() {
                this.declineConfirmationOpen = true;
            },
            openDeclineForm() {
                this.declineConfirmationOpen = false;
                this.declineOpen = true;
            },
            forceApprove() {
                document.getElementById('forceApproveInput').value = '1';
                document.getElementById('approveForm').submit();
            }
        }));
    });
</script>
@endsection