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
    
    .item-row {
        display: grid;
        grid-template-columns: 3fr 1fr 1fr 1fr;
        gap: 15px;
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .item-row:last-child {
        border-bottom: none;
    }
    
    .item-header {
        font-weight: 700;
        color: #1f2937;
        border-bottom: 2px solid #00462E;
        padding-bottom: 10px;
        font-size: 0.85rem;
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
    
    .info-value {
        font-weight: 600;
        color: #1f2937;
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


    
    .print-only {
        display: none;
    }
    
    @media print {
        /* Hide user greeting and logout button */
        header .flex.items-center.space-x-4.text-sm.text-gray-600.font-poppins,
        header .text-clsu-green,
        header .whitespace-nowrap,
        header button[type="submit"],
        header form {
            display: none !important;
        }
        
        /* Keep the logo */
        header .flex.items-center.space-x-4:first-child {
            display: flex !important;
        }
        
        /* Hide other elements but keep logo image */
        header .flex.items-center.space-x-4:first-child > *:not(img) {
            display: none !important;
        }
        
        /* Hide navigation links but keep header structure */
        header nav {
            display: none !important;
        }
        
        /* Hide action buttons */
        .no-print {
            display: none !important;
        }
    }
    
    /* Download button styles */
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

    .payment-ref-box {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .header-left {
        flex: 1;
    }

    .header-right {
        text-align: right;
    }

    .company-info {
        margin-bottom: 10px;
    }
    
    /* Meal badge styles */
    .meal-badge {
        display: inline-block;
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 5px;
        text-transform: capitalize;
    }
    
    /* Menu item card styles */
    .menu-item-card {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #00462E;
        transition: all 0.3s ease;
    }
    
    .menu-item-card:hover {
        background: #f0f9ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 70, 46, 0.1);
    }
    
    .menu-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .menu-item-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00462E;
    }
    
    .menu-item-details {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .menu-item-qty {
        background: #00462E;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .menu-item-price {
        font-size: 0.9rem;
        color: #059669;
        font-weight: 600;
    }
    
    .menu-items-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    
    .menu-food-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 0.85rem;
        color: #4b5563;
    }
    
    /* Section styles */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .section-title-main {
        font-size: 1.25rem;
        font-weight: 700;
        color: #00462E;
    }
    
    .section-actions {
        display: flex;
        gap: 10px;
    }
    
    /* Date range badge */
    .date-range-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f9ff;
        color: #0369a1;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 8px;
    }
    
    /* Day grouping styles */
    .day-group {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #00462E;
    }
    
    .day-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: #00462E;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .day-info {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    
    .day-date {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 600;
    }
    
    .day-time {
        background: #dcfce7;
        color: #059669;
        padding: 4px 10px;
        border-radius: 4px;
        font-weight: 600;
    }
    
    /* Table styles for menu items */
    .menu-items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .menu-items-table th {
        background: #f8fafc;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #00462E;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .menu-items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .menu-items-table tr:hover {
        background: #f0f9ff;
    }
@endsection

@section('content')
<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Action Buttons -->
        <div class="no-print flex justify-between items-center mb-6">
            <a href="{{ route('reservation_details') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-150 font-semibold">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Reservations
            </a>

            <div class="action-buttons">
            @if($reservation->status == 'approved')
                <button onclick="window.print()" 
                        class="download-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                
                <button onclick="downloadAsPDF(this)" 
                    class="download-btn bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </button>
            @endif
            </div>
            

        </div>

        <!-- Receipt Container -->
        <div class="receipt-container" id="receipt-content">
            
            <!-- Header -->
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
                        </div>
                    </div>
                    <div class="header-right">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">RESERVATION DETAILS</h1>
                        <div class="text-sm text-gray-600">Reservation ID: #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-600">Date Created: {{ $reservation->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer & Event Information -->
            <div class="receipt-section">
                <div class="info-grid">
                    <!-- Event Information -->
                    <div>
                        <div class="info-label">EVENT INFORMATION</div>
                        <div class="space-y-4 mt-3">
                            <div>
                                <span class="font-medium">Event Name:</span> 
                                <span class="font-semibold text-gray-900">{{ $reservation->event_name }}</span>
                            </div>
                            
                            <!-- Fixed Date-Time Format Display -->
                            <div>
                                <span class="font-medium">Date & Time:</span>
                                @php
                                    $startDate = \Carbon\Carbon::parse($reservation->event_date);
                                    $endDate = $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date) : $startDate;
                                    
                                    // Get day_times - already cast to array by Laravel
                                    $dayTimes = $reservation->day_times ?? [];
                                    
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
                                    <div class="period-summary">
                                        @if($days > 1)
                                            {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                                        @else
                                            {{ $startDate->format('M d, Y') }}
                                        @endif
                                    </div>
                                    
                                    <div class="datetime-display">
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
                                                if (empty($startTime) && $days == 1 && !empty($reservation->event_time)) {
                                                    $parts = explode(' - ', $reservation->event_time);
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
                                            <div class="datetime-item">
                                                <span class="date">{{ $formattedDate }}</span>
                                                @if($hasStartTime && $hasEndTime)
                                                    <span class="time">- {{ $formattedStartTime }} - {{ $formattedEndTime }}</span>
                                                @elseif($hasStartTime)
                                                    <span class="time">- {{ $formattedStartTime }}</span>
                                                @else
                                                    <span class="time text-gray-400">- No time specified</span>
                                                @endif
                                            </div>
                                        @endforeach
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
                    
                    <!-- Contact Information -->
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

            <!-- Menu Items -->
            <div class="receipt-section">
                <h3 class="text-xl font-bold text-gray-900 mb-6">SELECTED MENU ITEMS</h3>
                
                @php
                    // Load items with menu relationship
                    $reservation->load(['items.menu.items']);
                @endphp
                
                @if($reservation->items && $reservation->items->count() > 0)
                    @php
                        $totalAmount = 0;
                        $groupedItems = [];
                        
                        // Group items by day_number
                        foreach ($reservation->items as $item) {
                            $dayNumber = $item->day_number ?? 1;
                            if (!isset($groupedItems[$dayNumber])) {
                                $groupedItems[$dayNumber] = [];
                            }
                            $groupedItems[$dayNumber][] = $item;
                        }
                        
                        ksort($groupedItems);
                    @endphp
                    
                    @foreach($groupedItems as $dayNumber => $dayItems)
                        @php
                            $currentDate = $startDate->copy()->addDays($dayNumber - 1);
                            $formattedDate = $currentDate->format('M d, Y');
                        @endphp
                        
                        <div class="day-group">
                            <div class="day-header">
                                Day {{ $dayNumber }}: {{ $formattedDate }}
                            </div>
                            
                            <table class="menu-items-table">
                                <thead>
                                    <tr>
                                        <th>Menu Item</th>
                                        <th>Meal</th>
                                        <th>Qty</th>
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
                                                    <div class="font-semibold">{{ $item->menu->name }}</div>
                                                    @if($item->menu->items && $item->menu->items->count() > 0)
                                                        <div class="menu-items-list mt-2">
                                                            @foreach($item->menu->items as $menuItem)
                                                                <span class="menu-food-item">{{ $menuItem->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-gray-400 italic">No specific items listed</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="category-badge bg-blue-100 text-blue-800 capitalize">
                                                        {{ str_replace('_', ' ', $item->meal_time ?? 'lunch') }}
                                                    </span>
                                                </td>
                                                <td class="font-bold text-center">{{ $item->quantity }}</td>
                                                <td class="font-semibold">₱{{ number_format($price, 2) }}</td>
                                                <td class="font-bold text-clsu-green">₱{{ number_format($itemTotal, 2) }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center text-gray-500">
                                                    Menu item not found (ID: {{ $item->menu_id }})
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    
                    <!-- Total Amount -->
                    <div class="total-amount-box mt-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold">₱{{ number_format($totalAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600">Service Fee:</span>
                            <span class="font-semibold">₱0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-300">
                            <span class="text-lg font-bold text-gray-900">TOTAL:</span>
                            <span class="text-xl font-bold text-clsu-green">₱{{ number_format($totalAmount, 2) }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">No menu items selected for this reservation.</p>
                    </div>
                @endif
            </div>

            <!-- Additional Information -->
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

            <!-- Footer -->
            <div class="receipt-section bg-gray-50 text-center">
                <p class="text-gray-600 mb-2">For inquiries: retcafeteria@clsu.edu.ph</p>
                <p class="text-xs text-gray-400">Generated on {{ now()->format('M d, Y h:i A') }}</p>
                
                @if($reservation->status == 'pending')
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Cancel Reservation
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    // Print function with better formatting
    function printReceipt() {
        window.print();
    }
    
// Download as PDF function
function downloadAsPDF(button) {
    const element = document.getElementById('receipt-content');
    const opt = {
        margin:       0.5,
        filename:     'Reservation-{{ $reservation->id }}-{{ now()->format("Y-m-d") }}.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false
        },
        jsPDF:        { 
            unit: 'in', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };
    
    // Store original button state
    const originalHTML = button.innerHTML;
    const originalDisabled = button.disabled;
    
    // Show loading state
    button.innerHTML = '<span class="animate-spin mr-2">⟳</span> Generating PDF...';
    button.disabled = true;
    
    // Generate and download PDF
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            // Restore button state on success
            button.innerHTML = originalHTML;
            button.disabled = originalDisabled;
        })
        .catch((error) => {
            console.error('PDF generation error:', error);
            // Restore button state on error
            button.innerHTML = originalHTML;
            button.disabled = originalDisabled;
            
            // Show error message
            alert('Error generating PDF. Please try again.');
        });
}

        function downloadAsImage() {
        const element = document.getElementById('receipt-content');
        const name = 'Receipt-' + {{ $reservation->id }} + '-' + new Date().toISOString().slice(0,10);
        
        html2canvas(element, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = name + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }).catch(error => {
            console.error('Error generating image:', error);
            alert('Error generating image. Please try printing instead.');
        });
    }
</script>
@endsection