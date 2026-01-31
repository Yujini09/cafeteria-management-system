@extends('layouts.app')

@section('title', 'Billing Receipt - CLSU RET Cafeteria')

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
    
    .receipt-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 120px;
        font-weight: 900;
        opacity: 0.1;
        color: #00462E;
        white-space: nowrap;
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
    
    .status-paid {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
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

    .total-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 15px;
        padding: 8px 0;
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
    
    .amount-section {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .total-amount {
        font-size: 1.5rem;
        font-weight: 800;
        color: #00462E;
    }
    
    .print-only {
        display: none;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        .print-only {
            display: block;
        }
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .receipt-container {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            margin: 0 !important;
            max-width: none !important;
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
@endsection

@section('content')

@php
    // Get reservation ID from route parameter or session
    $reservationId = request()->route('id') ?? session('receipt_reservation_id');
    
    if (!$reservationId) {
        // Try to get the latest approved reservation for the user
        $reservation = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->latest()
            ->first();
            
        if (!$reservation) {
            // Redirect if no approved reservations
            echo '<div class="text-center py-16">
                    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-700 mb-2">No Receipt Available</h3>
                        <p class="text-gray-600 mb-6">You don\'t have any approved reservations with a receipt yet.</p>
                        <a href="' . route('reservation_details') . '" class="inline-flex items-center px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-semibold">
                            View Reservations
                        </a>
                    </div>
                  </div>';
            return;
        }
    } else {
        // Get specific reservation
        $reservation = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->where('id', $reservationId)
            ->firstOrFail();
    }
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($reservation->items as $item) {
        if ($item->menu) {
            // Get menu price - adjust this based on your actual price field
            $price = $item->menu->price ?? 150; // Default price if not set
            $totalAmount += $item->quantity * $price;
        }
    }
@endphp

<!-- Billing Receipt Section -->
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
                <button onclick="window.print()" 
                        class="download-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
                
                <button onclick="downloadAsImage()" 
                        class="download-btn bg-blue-600 hover:bg-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download
                </button>
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
                        <span class="status-badge status-paid">PAID</span>
                    </div>
                    <div class="header-right">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">OFFICIAL RECEIPT</h1>
                        <div class="text-sm text-gray-600">Date: {{ now()->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-600">Receipt No: {{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-600">Reservation ID: #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer & Payment Information -->
            <div class="receipt-section">
                <div class="info-grid">
                    <!-- Bill To -->
                    <div>
                        <div class="info-label">BILL TO</div>
                        <div class="space-y-2 mt-2">
                            <div><span class="font-medium">Name:</span> {{ $reservation->contact_person ?? $reservation->user->name ?? 'N/A' }}</div>
                            <div><span class="font-medium">Department/Office:</span> {{ $reservation->department ?? 'N/A' }}</div>
                            <div><span class="font-medium">Email:</span> {{ $reservation->email ?? 'N/A' }}</div>
                            <div><span class="font-medium">Phone:</span> {{ $reservation->contact_number ?? 'N/A' }}</div>
                            <div><span class="font-medium">Address:</span> {{ $reservation->address ?? 'N/A' }}</div>
                            <div><span class="font-medium">Event:</span> {{ $reservation->event_name }}</div> 
                            <div><span class="font-medium">Venue:</span> {{ $reservation->venue ?? 'N/A' }}</div>
                            <div><span class="font-medium">Date:</span> 
                                {{ \Carbon\Carbon::parse($reservation->event_date)->format('M d, Y') }}
                            </div>
                            @if($reservation->event_time)
                                <div><span class="font-medium">Time:</span> {{ $reservation->event_time }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Payment Reference -->
                    <div>
                        <div class="info-label">PAYMENT REFERENCE</div>
                        <div class="payment-ref-box">
                            <div class="font-semibold text-gray-900">Payment to RET Cafeteria</div>
                            <div class="text-gray-600 mt-1">Account Code:</div>
                            <div class="text-gray-900 font-bold text-lg">{{ $reservation->account_code ?? ('ACNT-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT) . '-RETCAF') }}</div>
                            @if($reservation->project_name)
                                <div class="text-gray-600 mt-2">Project:</div>
                                <div class="text-gray-900 font-medium">{{ $reservation->project_name }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="receipt-section">
                <h3 class="section-title">ORDER DETAILS</h3>
                
                <div class="item-row item-header">
                    <div>DESCRIPTION</div>
                    <div class="text-right">QTY</div>
                    <div class="text-right">UNIT PRICE</div>
                    <div class="text-right">AMOUNT</div>
                </div>
                
                @foreach($reservation->items as $item)
                    @if($item->menu)
                        @php
                            $price = $item->menu->price ?? 150;
                            $itemAmount = $item->quantity * $price;
                        @endphp
                        
                        <div class="item-row">
                            <div>
                                <div class="font-medium text-gray-900">{{ $item->menu->name }}</div>
                                @if($item->meal_time)
                                    <div class="meal-badge">{{ str_replace('_', ' ', $item->meal_time) }}</div>
                                @endif
                                <div class="text-sm text-gray-500 mt-1">
                                    @if($item->menu->type)
                                        Category: {{ ucfirst($item->menu->type) }}
                                    @endif
                                    @if($item->menu->items && count($item->menu->items) > 0)
                                        <br>Includes: 
                                        @foreach($item->menu->items as $menuItem)
                                            {{ $menuItem->name }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-gray-900">{{ $item->quantity }} pax</div>
                            <div class="text-right text-gray-900">₱{{ number_format($price, 2) }}</div>
                            <div class="text-right font-semibold text-gray-900">₱{{ number_format($itemAmount, 2) }}</div>
                        </div>
                    @endif
                @endforeach
                
                @if($reservation->special_requests)
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <div class="font-medium text-gray-900 mb-1">Special Instructions:</div>
                        <div class="text-sm text-gray-700">{{ $reservation->special_requests }}</div>
                    </div>
                @endif
            </div>

            <!-- Totals Section -->
            <div class="receipt-section">
                <div class="flex justify-end">
                    <div class="w-80">
                        <div class="amount-section">
                            <div class="total-row">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-right font-semibold">₱{{ number_format($totalAmount, 2) }}</span>
                            </div>
                            <div class="total-row">
                                <span class="text-gray-600">Tax (0%):</span>
                                <span class="text-right font-semibold">₱0.00</span>
                            </div>
                            <div class="total-row">
                                <span class="text-gray-600">Service Fee:</span>
                                <span class="text-right font-semibold">₱0.00</span>
                            </div>
                            <div class="total-row border-t border-gray-300 pt-3 mt-2">
                                <span class="text-gray-900 font-bold text-lg">TOTAL:</span>
                                <span class="text-right total-amount">₱{{ number_format($totalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="receipt-section bg-gray-50 text-center">
                <p class="text-gray-600 mb-2">Thank you for choosing CLSU RET Cafeteria!</p>
                <p class="text-xs text-gray-500">For inquiries: retcafeteria@clsu.edu.ph | (044) 456-7890</p>
                <p class="text-xs text-gray-400 mt-2">Receipt generated on {{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</section>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
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