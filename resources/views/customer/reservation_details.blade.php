@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
    .reservation-hero-bg {
        background-image: url('/images/banner1.jpg');
        background-size: cover;
        background-position: top;
    }
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        background-color: #f7f7f7;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .status-label {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .status-approved { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .status-declined { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-cancelled { background-color: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
    
    .payment-label {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .payment-unpaid { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .payment-paid { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .payment-na { background-color: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
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
        @php
            // Changed from ->get() to ->paginate(10) to show 10 items per page
            $reservations = App\Models\Reservation::with(['items.menu.items'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        @endphp
        
        @if($reservations->isEmpty())
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">No Reservations Yet</h3>
                <p class="text-gray-600 mb-6">You haven't made any reservations yet.</p>
                <a href="{{ route('reservation_form') }}" class="inline-flex items-center px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition duration-150 font-semibold">
                    Make Your First Reservation
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Your Reservations</h2>
                    {{-- Changed from count() to total() to show the full amount across all pages --}}
                    <p class="text-sm text-gray-600 mt-1">You have {{ $reservations->total() }} reservation(s)</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date &amp; Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meal / Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persons</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="py-6 pl-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-bold text-gray-800 text-base">{{ \Carbon\Carbon::parse($reservation->event_date)->format('M d') }}</div>
                                        <div class="text-xs text-gray-500 mt-1 font-medium">{{ $reservation->event_time }}</div>
                                    </td>
                                    <td class="py-6 text-sm text-gray-700 capitalize">
                                        @if($reservation->items->count() > 1) Multiple Meals
                                        @elseif($reservation->items->first()) {{ str_replace('_', ' ', $reservation->items->first()->meal_time) }}
                                        @else N/A @endif
                                    </td>
                                    <td class="py-6 text-center text-sm text-gray-700 font-medium">{{ $reservation->number_of_persons }} pax</td>
                                    
                                    <td class="py-6 whitespace-nowrap text-sm font-bold text-gray-900">
                                        @php
                                            $finalTotal = $reservation->total_amount;
                                            if(!$finalTotal || $finalTotal == 0) {
                                                $finalTotal = 0;
                                                foreach($reservation->items as $item) {
                                                    $p = $item->menu->price ?? (($item->menu->type ?? 'standard') === 'special' ? 200 : 150);
                                                    $finalTotal += ($item->quantity * $p);
                                                }
                                            }
                                        @endphp
                                        &#8369;{{ number_format($finalTotal, 2) }}
                                    </td>

                                    <td class="py-6 whitespace-nowrap text-center">
                                        <span class="status-label status-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(in_array($reservation->status, ['declined', 'cancelled']))                                            <span class="status-badge payment-na inline-flex items-center px-2.5 py-1 text-[11px] font-bold">N/A</span>
                                        @elseif(($reservation->payment_status ?? 'unpaid') === 'paid')
                                            <span class="status-badge payment-paid inline-flex items-center px-2.5 py-1 text-[11px] font-bold">Paid</span>
                                            @if(!empty($reservation->or_number))
                                            @endif
                                        @else
                                            <span class="status-badge payment-unpaid inline-flex items-center px-2.5 py-1 text-[11px] font-bold">Unpaid</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('reservation.view', $reservation->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 text-xs font-semibold">
                                                View
                                            </a>
                                            
                                            @if($reservation->status == 'declined' && $reservation->decline_reason)
                                                <button onclick="showDeclineDetails('{{ addslashes($reservation->decline_reason) }}')" class="inline-flex items-center px-3 py-1 bg-red-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                                    Reason
                                                </button>
                                            @endif
                                            
                                            @if(in_array($reservation->status, ['pending']))
                                                <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 text-xs font-semibold">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Links Container --}}
                @if($reservations->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-white">
                        {{ $reservations->links() }}
                    </div>
                @endif
                
            </div>
        @endif
    </div>
</section>

{{-- MODAL STRUCTURE for Decline Reason --}}
<div id="declineModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full">
        <div class="bg-red-500 px-6 py-4 text-white">
            <h3 class="text-xl font-bold">Reservation Declined</h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-700 font-medium">Reason for decline:</p>
            <p id="decline-reason" class="mt-2 text-gray-900"></p>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end">
            <button type="button" onclick="closeDeclineDetails()" class="px-4 py-2 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
    function showDeclineDetails(reason) {
        document.getElementById('decline-reason').textContent = reason;
        document.getElementById('declineModal').classList.remove('hidden');
        document.getElementById('declineModal').classList.add('flex');
    }
    function closeDeclineDetails() {
        document.getElementById('declineModal').classList.add('hidden');
        document.getElementById('declineModal').classList.remove('flex');
    }
</script>
@endsection