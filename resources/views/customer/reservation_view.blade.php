@extends('layouts.app')

@section('title', 'View Reservation - CLSU RET Cafeteria')

@section('styles')
    .receipt-hero-bg { background-image: url('/images/banner1.jpg'); background-size: cover; background-position: top; }
    .receipt-container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; border: 1px solid #e0e0e0; }
    .receipt-header { background: #f8fafc; color: #1f2937; padding: 30px; border-bottom: 2px solid #e5e7eb; }
    
    .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-approved { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-declined { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-cancelled { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }

    .payment-unpaid { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .payment-paid { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .payment-na { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
    
    .receipt-section { padding: 25px 30px; border-bottom: 1px solid #f0f0f0; }
    .receipt-section:last-child { border-bottom: none; }
    .section-title { font-size: 1rem; font-weight: 700; color: #00462E; margin-bottom: 15px; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
    .info-label { font-size: 0.8rem; color: #6b7280; font-weight: 500; text-transform: uppercase; margin-bottom: 4px; }
    
    .download-btn { background: linear-gradient(135deg, #00462E 0%, #057C3C 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
    .download-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 70, 46, 0.3); }
    .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }

    .header-content { display: flex; justify-content: space-between; align-items: flex-start; }
    .header-left { flex: 1; }
    .header-right { text-align: right; }
    
    .day-group { background: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 15px; border: 1px solid #e5e7eb; }
    .day-header { font-size: 1.1rem; font-weight: 700; color: #00462E; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; }
    
    .menu-items-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .menu-items-table th { background: #f8fafc; padding: 8px; text-align: left; font-weight: 600; color: #00462E; border-bottom: 2px solid #e5e7eb; }
    .menu-items-table td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
    .days-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; align-items: start; }

    @media (max-width: 768px) { .days-grid { grid-template-columns: 1fr; } }
    @media print {
        body { background: white !important; margin: 0; padding: 0; }
        .no-print, footer, .footer { display: none !important; }
        .receipt-container { box-shadow: none !important; border: none !important; max-width: 100% !important; width: 100% !important; margin: 0 !important; }
        .days-grid { display: block !important; }
        .day-group { page-break-inside: avoid; height: auto !important; margin-bottom: 20px !important; border: 1px solid #eee !important; }
        .receipt-section { padding: 15px 0 !important; }
        .receipt-header { padding: 10px 0 !important; border-bottom: 1px solid #333 !important; }
    }
@endsection

@section('content')
<section class="py-10 bg-gray-50 no-print">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('reservation_details') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                &larr; Back to Reservations
            </a>

            <div class="action-buttons">
            @if($reservation->status == 'approved')
                <button onclick="printReceipt()" class="download-btn">Print</button>
                <button onclick="downloadAsPDF(this)" class="download-btn bg-blue-600 hover:bg-blue-700">Download PDF</button>
            @endif
            </div>
        </div>

        <div class="receipt-container" id="receipt-content">
            <div class="receipt-header">
                <div class="header-content">
                    <div class="header-left">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">CLSU RET Cafeteria</h1>
                            <p class="text-gray-600 mt-1">Central Luzon State University</p>
                        </div>
                        <div class="mt-4 flex flex-col items-start gap-2">
                            @switch($reservation->status)
                                @case('approved') <span class="status-badge status-approved">Approved</span> @break
                                @case('declined') <span class="status-badge status-declined">Declined</span> @break
                                @case('cancelled') <span class="status-badge status-cancelled">Cancelled</span> @break
                                @default <span class="status-badge status-pending">Pending Approval</span>
                            @endswitch

                            @if($reservation->status === 'approved')
                                @if(($reservation->payment_status ?? 'unpaid') === 'paid')
                                    <div class="flex items-center gap-2">
                                        <span class="status-badge payment-paid">Payment: PAID</span>
                                        <span class="text-sm font-bold text-gray-700 bg-gray-200 px-3 py-1 rounded">OR#: {{ $reservation->or_number }}</span>
                                    </div>
                                @else
                                    <span class="status-badge payment-unpaid">Payment: UNPAID</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="header-right">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">RESERVATION</h1>
                        <div class="text-sm text-gray-600">ID: #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-sm text-gray-600">Date: {{ $reservation->created_at->format('M d, Y') }}</div>
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
                                <div class="font-bold text-sm">
                                    @if($days > 1) {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }} ({{ $days }} days)
                                    @else {{ $startDate->format('M d, Y') }} @endif
                                </div>
                            </div>
                            <div><span class="font-medium">Venue:</span> <span class="font-semibold text-gray-900">{{ $reservation->venue ?? 'Not specified' }}</span></div>
                            <div><span class="font-medium">Persons:</span> <span class="font-bold text-xl text-clsu-green">{{ $reservation->number_of_persons }}</span></div>
                            <div><span class="font-medium">Special Request:</span> <span class="text-gray-900">{{ $reservation->special_requests ?? 'None' }}</span></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="info-label">CONTACT INFORMATION</div>
                        <div class="space-y-3 mt-3">
                            <div><span class="font-medium">Contact:</span> <span class="font-semibold text-gray-900">{{ $reservation->contact_person ?? $reservation->user->name ?? 'N/A' }}</span></div>
                            <div><span class="font-medium">Department:</span> <span class="font-semibold text-gray-900">{{ $reservation->department ?? 'N/A' }}</span></div>
                            <div><span class="font-medium">Email:</span> <span class="font-semibold text-gray-900">{{ $reservation->email ?? 'N/A' }}</span></div>
                            <div><span class="font-medium">Phone:</span> <span class="font-semibold text-gray-900">{{ $reservation->contact_number ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <h3 class="text-xl font-bold text-gray-900 mb-6">SELECTED MENU ITEMS</h3>
                @php $reservation->load(['items.menu.items']); $totalAmount = 0; @endphp
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
                                    <thead><tr><th>Meal</th><th>Item</th><th>Total</th></tr></thead>
                                    <tbody>
                                        @foreach($dayItems as $item)
                                            @if($item->menu)
                                                @php
                                                    $price = $item->menu->price > 0 ? $item->menu->price : ($item->menu->type == 'special' ? 200 : 150);
                                                    $itemTotal = $item->quantity * $price;
                                                    $totalAmount += $itemTotal;
                                                @endphp
                                                <tr>
                                                    <td class="text-xs uppercase font-bold">{{ str_replace('_', ' ', $item->meal_time ?? 'lunch') }}</td>
                                                    <td>
                                                        <div class="font-semibold">{{ $item->menu->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->quantity }} pax × ₱{{ number_format($price, 2) }}</div>
                                                    </td>
                                                    <td class="font-bold text-clsu-green">₱{{ number_format($itemTotal, 2) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $additionalsTotal = $reservation->additionals ? $reservation->additionals->sum('price') : 0;
                        $serviceFee = $reservation->service_fee ?? 0;
                        $grandTotal = $totalAmount + $additionalsTotal + $serviceFee;
                    @endphp

                    <div class="mt-6 border-t border-gray-200 pt-4 flex flex-col items-end">
                        <div class="w-full sm:w-1/2 md:w-1/3 text-gray-700 mb-2">
                            <div class="flex justify-between py-1">
                                <span>Subtotal:</span>
                                <span class="font-medium">&#8369;{{ number_format($totalAmount, 2) }}</span>
                            </div>
                            @if($additionalsTotal > 0)
                            <div class="flex justify-between py-1">
                                <span>Additionals:</span>
                                <span class="font-medium">&#8369;{{ number_format($additionalsTotal, 2) }}</span>
                            </div>
                            @endif
                            @if($serviceFee > 0)
                            <div class="flex justify-between py-1">
                                <span>Service Fee:</span>
                                <span class="font-medium">&#8369;{{ number_format($serviceFee, 2) }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="w-full sm:w-1/2 md:w-1/3 flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <span class="text-gray-600 font-semibold uppercase tracking-wider text-sm">Total Amount</span>
                            <span class="text-2xl font-bold text-clsu-green">&#8369;{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="receipt-section bg-gray-50 text-center">
                @if($reservation->status === 'approved')
                    @if(($reservation->payment_status ?? 'unpaid') !== 'paid')
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg font-medium">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Please proceed to the cashier to pay for your reservation.
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg font-bold text-lg">
                            <i class="fas fa-check-circle mr-2"></i> Payment Completed! OR Number: {{ $reservation->or_number }}
                        </div>
                    @endif
                @endif

                <p class="text-gray-600 mb-2">For inquiries: retcafeteria@clsu.edu.ph</p>
                <p class="text-xs text-gray-400">Generated on {{ now()->format('M d, Y h:i A') }}</p>
                
                @if($reservation->status == 'pending')
                    <div class="mt-6 pt-6 border-t border-gray-200 flex justify-center gap-4">
                        <a href="{{ route('reservation.edit', $reservation->id) }}" class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-semibold shadow-sm">
                            Edit Reservation
                        </a>
                        <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow-sm">
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
<script>
    function printReceipt() { window.print(); }
    function downloadAsPDF() {
        const element = document.getElementById('receipt-content');
        html2pdf().set({
            margin: [0.3, 0.3, 0.3, 0.3],
            filename: 'Reservation-{{ $reservation->id }}.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        }).from(element).save();
    }
</script>
@endsection