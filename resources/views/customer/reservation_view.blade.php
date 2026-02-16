@extends('layouts.app')

@section('title', 'View Reservation - CLSU RET Cafeteria')

@section('styles')
    .receipt-hero-bg {
        background-image: url('/images/banner1.jpg');
        background-size: cover;
        background-position: top;
    }
    
    .receipt-container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e0e0e0;
    }
    
    .receipt-header {
        background: #f8fafc;
        color: #1f2937;
        padding: 30px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-approved {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .status-declined {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    .status-cancelled {
        background: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .payment-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .payment-under_review {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .payment-paid {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .payment-na {
        background: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .receipt-section {
        padding: 25px 30px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .receipt-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #00462E;
        margin-bottom: 15px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .download-btn {
        background: linear-gradient(135deg, #00462E 0%, #057C3C 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 70, 46, 0.3);
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .header-left { flex: 1; }
    .header-right { text-align: right; }
    .company-info { margin-bottom: 10px; }
    
    .day-group {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #e5e7eb; 
    }
    
    .day-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00462E;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .menu-items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    
    .menu-items-table th {
        background: #f8fafc;
        padding: 8px;
        text-align: left;
        font-weight: 600;
        color: #00462E;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .menu-items-table td {
        padding: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 768px) {
        .days-grid {
            grid-template-columns: 1fr;
        }
    }

    .menu-food-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 0.75rem;
        color: #4b5563;
        display: inline-block;
        margin-right: 4px;
        margin-bottom: 4px;
    }

    /* --- CRITICAL PRINT FIXES --- */
    @media print {
        body { background: white !important; margin: 0; padding: 0; }
        .no-print, footer, .footer { display: none !important; } /* Added footer here */
        
        .receipt-container { 
            box-shadow: none !important; 
            border: none !important; 
            max-width: 100% !important; 
            width: 100% !important; 
            margin: 0 !important;
        }
        
        .days-grid { 
            display: block !important; 
        }
        
        .day-group { 
            page-break-inside: avoid; 
            height: auto !important; 
            margin-bottom: 20px !important;
            border: 1px solid #eee !important;
        }
        
        .receipt-section { padding: 15px 0 !important; }
        .receipt-header { padding: 10px 0 !important; border-bottom: 1px solid #333 !important; }
    }
@endsection

@section('content')
<section class="py-10 bg-gray-50 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('reservation_details') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-150 font-semibold">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Reservations
            </a>

            <div class="action-buttons">
            @if($reservation->status == 'approved')
                <button onclick="printReceipt()" class="download-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                
                <button onclick="downloadAsPDF(this)" class="download-btn bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </button>
            @endif
            </div>
        </div>

        <div class="receipt-container" id="receipt-content">
            
            <div class="receipt-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="company-info">
                            <h1 class="text-3xl font-bold text-gray-900">CLSU RET Cafeteria</h1>
                            <p class="text-gray-600 mt-1">Central Luzon State University</p>
                        </div>
                        <div class="mt-3 flex items-center gap-4">
                            @switch($reservation->status)
                                @case('approved')
                                    <span class="status-badge status-approved">Approved</span>
                                    @break
                                @case('declined')
                                    <span class="status-badge status-declined">Declined</span>
                                    @break
                                @case('cancelled')
                                    <span class="status-badge status-cancelled">Cancelled</span>
                                    @break
                                @default
                                    <span class="status-badge status-pending">Pending Approval</span>
                            @endswitch

                            @php
                                $paymentStatus = $reservation->payment_status;
                                $isPaymentApplicable = $reservation->status === 'approved';
                            @endphp
                            @if(!$isPaymentApplicable)
                                <span class="status-badge payment-na">Payment: N/A</span>
                            @elseif($paymentStatus === 'paid')
                                <span class="status-badge payment-paid">Payment: Paid</span>
                            @elseif($paymentStatus === 'under_review')
                                <span class="status-badge payment-under_review">Payment: Under Review</span>
                            @else
                                <span class="status-badge payment-pending">Payment: Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="header-right">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">RESERVATION DETAILS</h1>
                        <div class="text-sm text-gray-600">Reservation ID: #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-600">Date Created: {{ $reservation->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <div class="info-grid">
                    <div>
                        <div class="info-label">EVENT INFORMATION</div>
                        <div class="space-y-4 mt-3">
                            <div>
                                <span class="font-medium">Event Name:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->event_name }}</span>
                            </div>
                            
                            <div>
                                <span class="font-medium">Date & Time:</span>
                                @php
                                    $startDate = \Carbon\Carbon::parse($reservation->event_date);
                                    $endDate = $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date) : $startDate;
                                    $dayTimes = $reservation->day_times ?? [];
                                    $days = $startDate->diffInDays($endDate) + 1;
                                    
                                    function formatTimeForDisplay($timeString) {
                                        if (empty($timeString) || trim($timeString) === '') return '';
                                        $timeString = trim($timeString);
                                        if (preg_match('/(AM|PM|am|pm)/', $timeString)) return strtoupper($timeString);
                                        try {
                                            return \Carbon\Carbon::parse($timeString)->format('g:i A');
                                        } catch (\Exception $e) { return $timeString; }
                                    }
                                    
                                    $dateRange = [];
                                    for ($i = 0; $i < $days; $i++) {
                                        $currentDate = $startDate->copy()->addDays($i);
                                        $dateKey = $currentDate->format('Y-m-d');
                                        $dateRange[$dateKey] = $currentDate;
                                    }
                                @endphp
                                
                                <div class="reservation-period text-sm">
                                    <div class="font-bold mb-1">
                                        @if($days > 1)
                                            {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                                        @else
                                            {{ $startDate->format('M d, Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <span class="font-medium">Venue:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->venue ?? 'Not specified' }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Number of Persons:</span> 
                                <span class="font-bold text-xl text-clsu-green">{{ $reservation->number_of_persons }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="info-label">CONTACT INFORMATION</div>
                        <div class="space-y-3 mt-3">
                            <div>
                                <span class="font-medium">Contact Person:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->contact_person ?? $reservation->user->name ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Department:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->department ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Email:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->email ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Phone:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->contact_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <h3 class="text-xl font-bold text-gray-900 mb-6">SELECTED MENU ITEMS</h3>
                
                @php
                    $reservation->load(['items.menu.items']);
                    $grandTotal = 0; // Initialize a grand total variable
                @endphp
                
                @if($reservation->items && $reservation->items->count() > 0)
                    @php
                        $groupedItems = [];
                        
                        foreach ($reservation->items as $item) {
                            $dayNumber = $item->day_number ?? 1;
                            if (!isset($groupedItems[$dayNumber])) {
                                $groupedItems[$dayNumber] = [];
                            }
                            $groupedItems[$dayNumber][] = $item;
                        }
                        ksort($groupedItems);
                    @endphp
                    
                    <div class="days-grid">
                        @foreach($groupedItems as $dayNumber => $dayItems)
                            @php
                                $currentDate = $startDate->copy()->addDays($dayNumber - 1);
                                $formattedDate = $currentDate->format('M d, Y');
                            @endphp
                            
                            <div class="day-group">
                                <div class="day-header">
                                    Day {{ $dayNumber }} <span class="text-sm font-normal text-gray-500">({{ $formattedDate }})</span>
                                </div>
                                
                                <table class="menu-items-table">
                                    <thead>
                                        <tr>
                                            <th>Meal</th>
                                            <th>Menu Item</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dayItems as $item)
                                            @if($item->menu)
                                                @php
                                                    $price = $item->menu->price ?? 0;
                                                    // Fallback price logic if database is 0
                                                    if($price == 0) {
                                                        $price = ($item->menu->type == 'special' ? 200 : 150);
                                                    }
                                                    
                                                    $itemTotal = $item->quantity * $price;
                                                    $grandTotal += $itemTotal; // Add to grand total
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs uppercase font-bold">
                                                            {{ str_replace('_', ' ', $item->meal_time ?? 'lunch') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="font-semibold">{{ $item->menu->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->quantity }} pax × ₱{{ number_format($price, 2) }}</div>
                                                        
                                                        @if($item->menu->items && $item->menu->items->count() > 0)
                                                            <div class="mt-1">
                                                                @foreach($item->menu->items as $menuItem)
                                                                    <span class="menu-food-item">{{ $menuItem->name }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="font-bold text-clsu-green align-top">
                                                        ₱{{ number_format($itemTotal, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    {{-- TOTAL SECTION (Now uses the calculated $grandTotal) --}}
                    <div class="total-amount-box mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">₱{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600">Service Fee:</span>
                            <span class="font-semibold">₱0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                            <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                            <span class="text-xl font-bold text-clsu-green">₱{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">No menu items selected for this reservation.</p>
                    </div>
                @endif
            </div>

            <div class="receipt-section">
                @if($reservation->special_requests)
                    <div class="mb-6">
                        <h3 class="section-title">SPECIAL REQUESTS</h3>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p class="text-gray-700">{{ $reservation->special_requests }}</p>
                        </div>
                    </div>
                @endif

                @if($reservation->status == 'declined' && $reservation->decline_reason)
                    <div>
                        <h3 class="section-title">REASON FOR DECLINE</h3>
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <p class="text-red-700">{{ $reservation->decline_reason }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="receipt-section bg-gray-50 text-center">
                <p class="text-gray-600 mb-2">For inquiries: retcafeteria@clsu.edu.ph</p>
                <p class="text-xs text-gray-400">Generated on {{ now()->format('M d, Y h:i A') }}</p>
                
                @if($reservation->status == 'pending')
                    <div class="mt-6 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-center gap-4">
                        
                        {{-- EDIT RESERVATION BUTTON --}}
                        <a href="{{ route('reservation.edit', $reservation->id) }}"
                           class="inline-flex items-center justify-center px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-150 font-semibold shadow-sm w-full sm:w-auto">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Reservation
                        </a>

                        {{-- CANCEL RESERVATION BUTTON --}}
                        <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" data-action-loading
                              onsubmit="return confirm('Are you sure you want to cancel this reservation?')" class="w-full sm:w-auto">
                            @csrf
                            @method('PATCH')
                            <button type="submit" data-loading-text="Cancelling Reservation..." class="inline-flex items-center justify-center px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 font-semibold shadow-sm w-full sm:w-auto">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Reservation
                            </button>
                        </form>
                    </div>
                @endif

                @if($reservation->status === 'approved' && $reservation->payment_status === 'pending')
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('payments.show', $reservation->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Submit Payment Reference
                        </a>
                    </div>
                @elseif($reservation->payment_status === 'under_review')
                    <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-blue-700">
                        Your payment is under review.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<div class="receipt-container mb-20" id="receipt-content">
    <div class="receipt-header">
        <div class="header-content">
            <div class="header-left">
                <div class="company-info">
                    <h1 class="text-3xl font-bold text-gray-900">CLSU RET Cafeteria</h1>
                    <p class="text-gray-600 mt-1">Central Luzon State University</p>
                </div>
                <div class="mt-3 flex items-center gap-4">
                    @switch($reservation->status)
                        @case('approved') <span class="status-badge status-approved">Approved</span> @break
                        @case('declined') <span class="status-badge status-declined">Declined</span> @break
                        @case('cancelled') <span class="status-badge status-cancelled">Cancelled</span> @break
                        @default <span class="status-badge status-pending">Pending Approval</span>
                    @endswitch
                </div>
            </div>
            <div class="header-right">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">RESERVATION DETAILS</h1>
                <div class="text-sm text-gray-600">Reservation ID: #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="text-sm text-gray-600">Date Created: {{ $reservation->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="receipt-section">
        <div class="info-grid">
            <div>
                <div class="info-label">EVENT INFORMATION</div>
                <div class="space-y-4 mt-3">
                    <div><span class="font-medium">Event Name:</span> <span class="font-semibold text-gray-900">{{ $reservation->event_name }}</span></div>
                    <div>
                        <span class="font-medium">Date & Time:</span>
                        @php
                            $startDate = \Carbon\Carbon::parse($reservation->event_date);
                            $endDate = $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date) : $startDate;
                            $days = $startDate->diffInDays($endDate) + 1;
                        @endphp
                        <div class="text-sm font-bold">
                            @if($days > 1) {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                            @else {{ $startDate->format('M d, Y') }} @endif
                        </div>
                    </div>
                    <div><span class="font-medium">Venue:</span> <span class="font-semibold text-gray-900">{{ $reservation->venue ?? 'Not specified' }}</span></div>
                    <div><span class="font-medium">Number of Persons:</span> <span class="font-bold text-xl text-green-700">{{ $reservation->number_of_persons }}</span></div>
                </div>
            </div>
            <div>
                <div class="info-label">CONTACT INFORMATION</div>
                <div class="space-y-3 mt-3">
                    <div><span class="font-medium">Contact Person:</span> <span class="font-semibold text-gray-900">{{ $reservation->contact_person ?? $reservation->user->name ?? 'N/A' }}</span></div>
                    <div><span class="font-medium">Department:</span> <span class="font-semibold text-gray-900">{{ $reservation->department ?? 'N/A' }}</span></div>
                    <div><span class="font-medium">Email:</span> <span class="font-semibold text-gray-900">{{ $reservation->email ?? 'N/A' }}</span></div>
                    <div><span class="font-medium">Phone:</span> <span class="font-semibold text-gray-900">{{ $reservation->contact_number ?? 'N/A' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="receipt-section">
        <h3 class="text-xl font-bold text-gray-900 mb-6">SELECTED MENU ITEMS</h3>
        @php $reservation->load(['items.menu.items']); $grandTotal = 0; @endphp
        @if($reservation->items && $reservation->items->count() > 0)
            @php
                $groupedItems = [];
                foreach ($reservation->items as $item) {
                    $dayNumber = $item->day_number ?? 1;
                    $groupedItems[$dayNumber][] = $item;
                }
                ksort($groupedItems);
            @endphp
            <div class="days-grid">
                @foreach($groupedItems as $dayNumber => $dayItems)
                    <div class="day-group">
                        <div class="day-header">
                            Day {{ $dayNumber }} <span class="text-sm font-normal text-gray-500">({{ $startDate->copy()->addDays($dayNumber - 1)->format('M d, Y') }})</span>
                        </div>
                        <table class="menu-items-table">
                            <thead>
                                <tr><th>Meal</th><th>Menu Item</th><th>Total</th></tr>
                            </thead>
                            <tbody>
                                @foreach($dayItems as $item)
                                    @if($item->menu)
                                        @php
                                            $price = $item->menu->price > 0 ? $item->menu->price : ($item->menu->type == 'special' ? 200 : 150);
                                            $itemTotal = $item->quantity * $price;
                                            $grandTotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td class="capitalize">{{ str_replace('_', ' ', $item->meal_time ?? 'lunch') }}</td>
                                            <td>
                                                <div class="font-semibold">{{ $item->menu->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->quantity }} pax × ₱{{ number_format($price, 2) }}</div>
                                                @if($item->menu->items)
                                                    <div class="mt-1">
                                                        @foreach($item->menu->items as $mi)
                                                            <span class="menu-food-item">{{ $mi->name }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="font-bold text-green-800">₱{{ number_format($itemTotal, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex justify-between items-center mb-2"><span class="text-gray-600">Subtotal:</span><span class="font-semibold">₱{{ number_format($grandTotal, 2) }}</span></div>
                <div class="flex justify-between items-center mb-4"><span class="text-gray-600">Service Fee:</span><span class="font-semibold">₱0.00</span></div>
                <div class="flex justify-between items-center pt-4 border-t border-gray-400"><span class="text-lg font-bold text-gray-900">TOTAL:</span><span class="text-xl font-bold text-green-700">₱{{ number_format($grandTotal, 2) }}</span></div>
            </div>
        @endif
    </div>

    <div class="receipt-section text-center">
        <p class="text-gray-600 mb-2">For inquiries: retcafeteria@clsu.edu.ph</p>
        <p class="text-xs text-gray-400">Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function printReceipt() { window.print(); }
    function downloadAsPDF(button) {
        const element = document.getElementById('receipt-content');
        const opt = {
            margin: [0.3, 0.3, 0.3, 0.3],
            filename: 'Reservation-{{ $reservation->id }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
@endsection
