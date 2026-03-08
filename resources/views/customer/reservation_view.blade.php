@extends('layouts.app')

@section('title', 'View Reservation - CLSU RET Cafeteria')

@section('styles')
    .reservation-view-page .reservation-hero-bg { background-image: url('/images/banner1.jpg'); background-size: cover; background-position: top; }
    .reservation-view-page .receipt-hero-bg { background-image: url('/images/banner1.jpg'); background-size: cover; background-position: top; }
    .reservation-view-page .receipt-container { max-width: 1000px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; border: 1px solid #e0e0e0; }
    .reservation-view-page .receipt-header { background: #f8fafc; color: #1f2937; padding: 30px; border-bottom: 2px solid #e5e7eb; }

    .reservation-view-page .status-badge { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .reservation-view-page .status-approved { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .reservation-view-page .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .reservation-view-page .status-declined { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .reservation-view-page .status-cancelled { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }

    .reservation-view-page .payment-unpaid { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .reservation-view-page .payment-paid { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .reservation-view-page .payment-na { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }

    .reservation-view-page .receipt-section { padding: 25px 30px; border-bottom: 1px solid #f0f0f0; }
    .reservation-view-page .receipt-section:last-child { border-bottom: none; }

    .reservation-view-page .download-btn { background: linear-gradient(135deg, #00462E 0%, #057C3C 100%); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; }
    .reservation-view-page .download-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 70, 46, 0.3); }
    .reservation-view-page .action-buttons { display: flex; gap: 12px; flex-wrap: wrap; }

    .reservation-view-page .header-content { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
    .reservation-view-page .header-left { flex: 1; }
    .reservation-view-page .header-right { text-align: right; }

    .reservation-view-page .customer-detail-card { border: 1px solid #e5e7eb; border-radius: 14px; background: #ffffff; overflow: hidden; }
    .reservation-view-page .customer-detail-card-header { display: flex; align-items: center; gap: 10px; padding: 18px 22px 0; }
    .reservation-view-page .customer-detail-card-title { font-size: 1rem; font-weight: 700; color: #111827; }
    .reservation-view-page .customer-detail-card-body { padding: 18px 22px 22px; }

    .reservation-view-page .customer-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 40px; }
    .reservation-view-page .customer-detail-row { min-width: 0; color: #111827; font-size: 0.95rem; line-height: 1.6; }
    .reservation-view-page .customer-detail-label { font-weight: 700; color: #111827; }
    .reservation-view-page .customer-detail-time-list { margin-top: 4px; display: grid; gap: 4px; }
    .reservation-view-page .customer-detail-divider { margin: 18px 0; border-top: 1px solid #e5e7eb; }

    .reservation-view-page .customer-menu-day-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
    .reservation-view-page .customer-menu-day-title { font-size: 1.05rem; font-weight: 700; color: #111827; margin-bottom: 14px; }
    .reservation-view-page .customer-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .reservation-view-page .customer-menu-table { width: 100%; min-width: 760px; border-collapse: collapse; font-size: 0.9rem; }
    .reservation-view-page .customer-menu-table th { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; color: #6b7280; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; text-align: left; white-space: nowrap; }
    .reservation-view-page .customer-menu-table td { padding: 14px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; color: #111827; }
    .reservation-view-page .customer-menu-table tbody tr:last-child td { border-bottom: none; }
    .reservation-view-page .customer-menu-name { font-weight: 700; color: #111827; margin-bottom: 6px; }
    .reservation-view-page .customer-menu-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .reservation-view-page .customer-menu-tag { display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 999px; background: #f3f4f6; color: #4b5563; font-size: 0.72rem; line-height: 1.2; }
    .reservation-view-page .customer-menu-total { text-align: right; font-weight: 700; color: #15803d; white-space: nowrap; }

    .reservation-view-page .customer-total-card { margin-top: 8px; border: 1px solid #bbf7d0; border-radius: 10px; background: #f0fdf4; padding: 16px 18px; }
    .reservation-view-page .customer-total-row { display: flex; justify-content: space-between; gap: 16px; font-size: 0.95rem; color: #14532d; }
    .reservation-view-page .customer-total-row + .customer-total-row { margin-top: 4px; }
    .reservation-view-page .customer-total-row-grand { margin-top: 10px; padding-top: 10px; border-top: 1px solid #86efac; font-weight: 700; font-size: 1.05rem; }

    @media (max-width: 768px) {
        .reservation-view-page .header-content { flex-direction: column; }
        .reservation-view-page .header-right { text-align: left; }
        .reservation-view-page .customer-detail-card-header { padding: 16px 18px 0; }
        .reservation-view-page .customer-detail-card-body { padding: 16px 18px 18px; }
        .reservation-view-page .customer-detail-grid { grid-template-columns: 1fr; gap: 12px; }
        .reservation-view-page .customer-menu-day-card { padding: 14px; }
        .reservation-view-page .customer-menu-table { min-width: 680px; }
    }

    @media print {
        body { background: white !important; margin: 0; padding: 0; }
        .reservation-view-page .no-print, footer, .footer { display: none !important; }
        .reservation-view-page .receipt-container { box-shadow: none !important; border: none !important; max-width: 100% !important; width: 100% !important; margin: 0 !important; }
        .reservation-view-page .customer-menu-day-card { page-break-inside: avoid; }
        .reservation-view-page .receipt-section { padding: 15px 0 !important; }
        .reservation-view-page .receipt-header { padding: 10px 0 !important; border-bottom: 1px solid #333 !important; }
    }
@endsection

@section('content')
<div class="reservation-view-page">
<section class="reservation-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 tracking-wide">Reservation Details</h1>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6 no-print">
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

            @php
                $startDate = \Carbon\Carbon::parse($reservation->event_date);
                $endDate = $reservation->end_date ? \Carbon\Carbon::parse($reservation->end_date) : $startDate;
                $dayTimes = $reservation->day_times ?? [];
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

                $reservation->load(['items.menu.items']);
                $groupedItems = [];
                foreach ($reservation->items ?? [] as $item) {
                    $dayNumber = $item->day_number ?? 1;
                    $groupedItems[$dayNumber][] = $item;
                }
                ksort($groupedItems);
            @endphp

            <div class="receipt-section">
                <div class="customer-detail-card">
                    <div class="customer-detail-card-header">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="customer-detail-card-title">Event Details</h3>
                    </div>
                    <div class="customer-detail-card-body">
                        <div class="customer-detail-grid">
                            <div class="customer-detail-row"><span class="customer-detail-label">Event Name:</span> {{ $reservation->event_name ?: 'N/A' }}</div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Number of Persons:</span> {{ $reservation->number_of_persons ?: 'N/A' }}</div>
                            <div class="customer-detail-row">
                                <div class="customer-detail-label">Date &amp; Time:</div>
                                <div class="customer-detail-time-list">
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

                                            if (empty($startTime) && $days === 1 && !empty($reservation->event_time)) {
                                                $parts = explode(' - ', $reservation->event_time);
                                                $startTime = trim($parts[0] ?? '');
                                                $endTime = trim($parts[1] ?? '');
                                            }

                                            $formattedStartTime = $formatTimeForDisplay($startTime);
                                            $formattedEndTime = $formatTimeForDisplay($endTime);
                                        @endphp
                                        <div>
                                            {{ $formattedDate }}:
                                            @if($formattedStartTime !== '')
                                                {{ $formattedStartTime }}{{ $formattedEndTime !== '' ? ' - ' . $formattedEndTime : '' }}
                                            @else
                                                No time specified
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Special Requests:</span> {{ $reservation->special_requests ?: 'None' }}</div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Venue:</span> {{ $reservation->venue ?: 'Not specified' }}</div>
                        </div>

                        <div class="customer-detail-divider"></div>

                        <div class="customer-detail-grid">
                            <div class="customer-detail-row"><span class="customer-detail-label">Contact Person:</span> {{ $reservation->contact_person ?? optional($reservation->user)->name ?? 'N/A' }}</div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Email:</span> {{ $reservation->email ?? optional($reservation->user)->email ?? 'N/A' }}</div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Department:</span> {{ $reservation->department ?? 'N/A' }}</div>
                            <div class="customer-detail-row"><span class="customer-detail-label">Phone:</span> {{ $reservation->contact_number ?? optional($reservation->user)->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <div class="customer-detail-card">
                    <div class="customer-detail-card-header">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="customer-detail-card-title">Selected Menus</h3>
                    </div>
                    <div class="customer-detail-card-body">
                        @php
                            $totalAmount = 0;
                            $additionalsTotal = $reservation->additionals ? $reservation->additionals->sum('price') : 0;
                        @endphp

                        @if($reservation->items && $reservation->items->count() > 0)
                            @foreach($groupedItems as $dayNumber => $dayItems)
                                @php $dayDate = $startDate->copy()->addDays($dayNumber - 1); @endphp
                                <div class="customer-menu-day-card">
                                    <div class="customer-menu-day-title">Day {{ $dayNumber }}: {{ $dayDate->format('M d, Y') }}</div>
                                    <div class="customer-table-wrap">
                                        <table class="customer-menu-table">
                                            <thead>
                                                <tr>
                                                    <th>Menu Item</th>
                                                    <th>Meal</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th class="text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dayItems as $item)
                                                    @if($item->menu)
                                                        @php
                                                            $price = $item->price ?? $item->menu->price ?? 150;
                                                            if ($price <= 0) {
                                                                $price = ($item->menu->type == 'special' ? 200 : 150);
                                                            }
                                                            $itemTotal = $item->quantity * $price;
                                                            $totalAmount += $itemTotal;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="customer-menu-name">{{ $item->menu->name }}</div>
                                                                @if($item->menu->items && $item->menu->items->count() > 0)
                                                                    <div class="customer-menu-tags">
                                                                        @foreach($item->menu->items as $menuItem)
                                                                            <span class="customer-menu-tag">{{ $menuItem->name }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="capitalize">{{ str_replace('_', ' ', $item->meal_time) }}</td>
                                                            <td class="font-semibold">{{ $item->quantity }}</td>
                                                            <td>&#8369;{{ number_format($price, 2) }}</td>
                                                            <td class="customer-menu-total">&#8369;{{ number_format($itemTotal, 2) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            @php $grandTotal = $totalAmount + $additionalsTotal; @endphp

                            <div class="customer-total-card">
                                <div class="customer-total-row">
                                    <span>Subtotal:</span>
                                    <span>&#8369;{{ number_format($totalAmount, 2) }}</span>
                                </div>
                                <div class="customer-total-row">
                                    <span>Additionals:</span>
                                    <span>&#8369;{{ number_format($additionalsTotal, 2) }}</span>
                                </div>
                                <div class="customer-total-row customer-total-row-grand">
                                    <span>Total:</span>
                                    <span>&#8369;{{ number_format($grandTotal, 2) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">No menus selected for this reservation.</div>
                        @endif
                    </div>
                </div>
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

                <p class="text-gray-600 mb-2">For inquiries: food.lodgingservices@clsu.edu.ph</p>
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
</div>

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
