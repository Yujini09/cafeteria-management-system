@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
    .reservation-hero-bg { background-image: url('/images/banner1.jpg'); background-size: cover; background-position: top; }
    .status-label { display: inline-block; padding: 6px 12px; border-radius: 9999px; font-weight: 700; font-size: 0.70rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .status-approved { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-declined { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-cancelled { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
    .table-header { font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; padding-bottom: 1rem; }
@endsection

@section('content')
<section class="reservation-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 tracking-wide">Your Reservations</h1>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left table-header pl-4">Date and Time</th>
                            <th class="text-left table-header">Meal</th>
                            <th class="text-left table-header">Category</th>
                            <th class="text-center table-header">Qty</th>
                            <th class="text-left table-header">Total Price</th>
                            <th class="text-center table-header">Status</th>
                            <th class="text-center table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(isset($reservations) && count($reservations) > 0)
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 group">
                                    <td class="py-6 pl-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-bold text-gray-800 text-base">{{ \Carbon\Carbon::parse($reservation->event_date)->format('M d') }}</div>
                                        <div class="text-xs text-gray-500 mt-1 font-medium">{{ $reservation->event_time }}</div>
                                    </td>
                                    <td class="py-6 text-sm text-gray-700 capitalize">
                                        @if($reservation->items->count() > 1) Multiple Meals
                                        @elseif($reservation->items->first()) {{ str_replace('_', ' ', $reservation->items->first()->meal_time) }}
                                        @else N/A @endif
                                    </td>
                                    <td class="py-6 text-sm text-gray-600">
                                        @if($reservation->items->count() > 1) Mixed
                                        @elseif($reservation->items->first()) {{ ucfirst($reservation->items->first()->menu->type ?? 'Standard') }}
                                        @else - @endif
                                    </td>
                                    <td class="py-6 text-center text-sm text-gray-700 font-medium">{{ $reservation->number_of_persons }} pax</td>
                                    
                                    {{-- DYNAMIC TOTAL CALCULATION FIX --}}
                                    <td class="py-6 whitespace-nowrap text-sm font-bold text-gray-900">
                                        @php
                                            $finalTotal = $reservation->total_amount;
                                            if(!$finalTotal || $finalTotal == 0) {
                                                $finalTotal = 0;
                                                foreach($reservation->items as $item) {
                                                    $p = $item->menu->price ?? 0;
                                                    if($p == 0) {
                                                        // Fallback logic
                                                        $p = ($item->menu->type ?? 'standard') === 'special' ? 200 : 150;
                                                    }
                                                    $finalTotal += ($item->quantity * $p);
                                                }
                                            }
                                        @endphp
                                        ₱{{ number_format($finalTotal, 2) }}
                                    </td>

                                    <td class="py-6 whitespace-nowrap text-center">
                                        <span class="status-label status-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
                                    </td>
                                    <td class="py-6 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('reservation.view', $reservation->id) }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">See Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No reservations found.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection