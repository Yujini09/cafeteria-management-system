@extends('layouts.app')

@section('title', 'Reservation Details - CLSU RET Cafeteria')

@section('styles')
    .reservation-hero-bg {
    background-image: url('/images/banner1.jpg');
    background-size: cover;
    background-position: top;
    }
    /* Custom styles for the reservation details page */
    .details-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        background-color: #f7f7f7;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .reservation-table-card {
        background-color: white;
        border-radius: 6px;
        overflow-x: auto;
        border: 1px solid #e0e0e0;
    }
    .status-label {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px; /* Fully rounded */
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .status-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-declined {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .status-cancelled {
        background-color: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .action-link {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s;
    }
    .action-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .payment-label {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .payment-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .payment-under_review {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }
    .payment-paid {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
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
            // Get user's reservations
                $reservations = App\Models\Reservation::with(['items.menu.items'])
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
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
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Make Your First Reservation
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Your Reservations</h2>
                    <p class="text-sm text-gray-600 mt-1">You have {{ $reservations->count() }} reservation(s)</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persons</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
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

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($reservation->status)
                                            @case('approved')
                                                <span class="status-label status-approved">Approved</span>
                                                @break
                                            @case('declined')
                                                <span class="status-label status-declined">Declined</span>
                                                @break
                                            @case('cancelled')
                                                <span class="status-label status-cancelled">Cancelled</span>
                                                @break
                                            @default
                                                <span class="status-label status-pending">Pending</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $paymentStatus = $reservation->payment_status ?? 'pending';
                                        @endphp
                                        @if($paymentStatus === 'paid')
                                            <span class="payment-label payment-paid">Paid</span>
                                        @elseif($paymentStatus === 'under_review')
                                            <span class="payment-label payment-under_review">Under Review</span>
                                        @else
                                            <span class="payment-label payment-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('reservation.view', $reservation->id) }}" 
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 text-xs font-semibold">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>

                                            @if($reservation->status === 'approved' && $reservation->payment_status === 'pending')
                                                <a href="{{ route('payments.show', $reservation->id) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 text-xs font-semibold">
                                                    Pay Now
                                                </a>
                                            @elseif($reservation->payment_status === 'under_review')
                                                <a href="{{ route('payments.show', $reservation->id) }}"
                                                   class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-150 text-xs font-semibold">
                                                    View Payment
                                                </a>
                                            @endif
                                            
                                            {{-- @if($reservation->status == 'approved')
                                                <a href="{{ route('billing_info', $reservation->id) }}" 
                                                   class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 text-xs font-semibold">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Receipt
                                                </a>
                                            @endif --}}
                                            
                                            @if($reservation->status == 'declined' && $reservation->decline_reason)
                                                <button onclick="showDeclineDetails('{{ addslashes($reservation->decline_reason) }}')" 
                                                        class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition duration-150 text-xs font-semibold">
                                                    Reason
                                                </button>
                                            @endif
                                            
                                            @if(in_array($reservation->status, ['pending']))
                                                <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-150 text-xs font-semibold">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
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
        </div>
    </div>
@endif

</section>

{{-- MODAL STRUCTURE for Decline Reason --}}
<div id="declineModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full">
        <div class="bg-red-500 px-6 py-4 text-white">
            <h3 class="text-xl font-bold">Reservation Declined</h3>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-700 font-medium">Reason for decline:</p>
                    <p id="decline-reason" class="mt-2 text-gray-900"></p>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 flex justify-end">
            <button type="button" onclick="closeDeclineDetails()" 
                    class="px-4 py-2 bg-clsu-green text-white rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-clsu-green transition duration-150">
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
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeDeclineDetails() {
        document.getElementById('declineModal').classList.add('hidden');
        document.getElementById('declineModal').classList.remove('flex');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
    
    // Close modal when clicking outside the modal content
    document.getElementById('declineModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeclineDetails();
        }
    });

        // Helper function to format time (remove seconds)
    function formatTimeDisplay($timeString) {
        if (empty($timeString)) {
            return '';
        }
        
        // If it's already in HH:MM format, return as-is
        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString)) {
            return $timeString;
        }
        
        // If it has seconds, remove them
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $timeString)) {
            return substr($timeString, 0, 5);
        }
        
        // If it's a time range like "08:00-10:00", handle it
        if (strpos($timeString, '-') !== false) {
            $parts = explode('-', $timeString);
            $formattedParts = array_map(function($part) {
                $trimmed = trim($part);
                return preg_replace('/:\d{2}$/', '', $trimmed);
            }, $parts);
            return implode(' - ', $formattedParts);
        }
        
        // Default: just remove seconds if they exist
        return preg_replace('/:\d{2}$/', '', $timeString);
    }

    </script>

@endsection
