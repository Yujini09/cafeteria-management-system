@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
    .reservation-hero-bg {
        background-image: url('/images/banner1.jpg');
        background-size: cover;
        background-position: top;
    }
    .status-label {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.70rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-approved { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .status-declined { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .status-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-cancelled { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
    
    .table-header {
        font-size: 0.75rem;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding-bottom: 1rem;
    }
@endsection

@section('content')

<section class="reservation-hero-bg py-20 lg:py-20 bg-gray-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-10"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl lg:text-5xl font-extrabold mb-3 tracking-wide">
            Your Reservations
        </h1>
        <p class="text-lg lg:text-xl font-poppins opacity-90">
            Track the status of your catering requests.
        </p>
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
                            {{-- Menu Column Removed --}}
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
                                    
                                    {{-- 1. DATE AND TIME --}}
                                    <td class="py-6 pl-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-bold text-gray-800 text-base">
                                            {{ \Carbon\Carbon::parse($reservation->event_date)->format('M d') }}
                                            @if($reservation->end_date && $reservation->event_date != $reservation->end_date)
                                                - {{ \Carbon\Carbon::parse($reservation->end_date)->format('M d') }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 font-medium">
                                            {{-- Safe Time Display --}}
                                            @php
                                                $timeDisplay = $reservation->event_time;
                                                $dayTimes = $reservation->day_times; 
                                                
                                                if(is_array($dayTimes) && !empty($dayTimes)) {
                                                    $firstDay = reset($dayTimes);
                                                    if(isset($firstDay['start_time']) && isset($firstDay['end_time'])) {
                                                        $timeDisplay = \Carbon\Carbon::parse($firstDay['start_time'])->format('g:i A') . ' - ' . \Carbon\Carbon::parse($firstDay['end_time'])->format('g:i A');
                                                    }
                                                }
                                            @endphp
                                            {{ $timeDisplay }}
                                        </div>
                                    </td>

                                    {{-- 2. MEAL (Summarized) --}}
                                    <td class="py-6 text-sm text-gray-700 capitalize">
                                        @if($reservation->items->count() > 1)
                                            <span class="font-medium text-gray-800">Multiple Meals</span>
                                            <div class="text-xs text-gray-500 mt-0.5">({{ $reservation->items->count() }} items)</div>
                                        @elseif($reservation->items->first())
                                            {{ str_replace('_', ' ', $reservation->items->first()->meal_time) }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>

                                    {{-- 3. CATEGORY (Summarized) --}}
                                    <td class="py-6 text-sm text-gray-600">
                                        @if($reservation->items->count() > 1)
                                            <span>Mixed</span>
                                        @elseif($reservation->items->first())
                                            {{ ucfirst($reservation->items->first()->menu->type ?? 'Standard') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- Menu Column Removed --}}

                                    {{-- 4. QTY (Main Count) --}}
                                    <td class="py-6 text-center text-sm text-gray-700 font-medium">
                                        {{ $reservation->number_of_persons }} pax
                                    </td>

                                    {{-- 5. TOTAL PRICE --}}
                                    <td class="py-6 whitespace-nowrap text-sm font-bold text-gray-900">
                                        ₱{{ number_format($reservation->total_amount ?? 0, 2) }}
                                    </td>

                                    {{-- 6. STATUS --}}
                                    <td class="py-6 whitespace-nowrap text-center">
                                        @php
                                            $statusClass = 'status-pending';
                                            if($reservation->status == 'approved') $statusClass = 'status-approved';
                                            elseif($reservation->status == 'declined') $statusClass = 'status-declined';
                                            elseif($reservation->status == 'cancelled') $statusClass = 'status-cancelled';
                                        @endphp
                                        <span class="status-label {{ $statusClass }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>

                                    {{-- 7. ACTIONS --}}
                                    <td class="py-6 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex flex-col gap-2 items-center justify-center">
                                            @if($reservation->status == 'declined')
                                                <button onclick="showDeclineDetails('{{ addslashes($reservation->decline_reason ?? 'No reason provided') }}')" 
                                                        class="px-4 py-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition text-xs font-bold border border-red-200">
                                                    View Reason
                                                </button>
                                            @else
                                                <a href="{{ route('reservation.view', $reservation->id) }}" 
                                                   class="px-5 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition text-xs font-bold">
                                                    See Details
                                                </a>
                                            @endif
                                            
                                            {{-- Cancel Request Button Removed --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                {{-- Adjusted colspan from 8 to 7 since we removed one column --}}
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-lg font-semibold text-gray-600">No reservations found.</p>
                                        <a href="{{ route('reservation_form') }}" class="mt-2 text-clsu-green hover:underline font-medium text-sm">Make a new reservation</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- Decline Reason Modal --}}
<div id="declineModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center">
            <div class="bg-red-100 rounded-full p-2 mr-3">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-red-900">Reservation Declined</h3>
        </div>
        <div class="px-6 py-6">
            <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-2">Reason provided by admin</p>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p id="decline-reason" class="text-gray-800 text-sm leading-relaxed"></p>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end">
            <button type="button" onclick="closeDeclineDetails()" 
                    class="px-5 py-2.5 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700 transition duration-150 shadow-sm text-sm">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function showDeclineDetails(reason) {
        document.getElementById('decline-reason').textContent = reason;
        document.getElementById('declineModal').classList.remove('hidden');
        document.getElementById('declineModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDeclineDetails() {
        document.getElementById('declineModal').classList.add('hidden');
        document.getElementById('declineModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    document.getElementById('declineModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeclineDetails();
        }
    });
</script>

@endsection