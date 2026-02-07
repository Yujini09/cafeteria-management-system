@extends('layouts.app')

@section('title', 'Payment - CLSU RET Cafeteria')

@section('styles')
    .status-pill { display: inline-block; padding: 6px 12px; border-radius: 9999px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-under_review { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .status-paid { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
@endsection

@section('content')
<section class="py-10 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Payment Confirmation</h1>
                    <p class="text-sm text-gray-600">Reservation #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    @php
                        $paymentStatus = $reservation->payment_status ?? 'pending';
                    @endphp
                    <span class="status-pill status-{{ $paymentStatus }}">
                        {{ $paymentStatus === 'under_review' ? 'Under Review' : ucfirst($paymentStatus) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <h2 class="font-semibold text-gray-900 mb-3">Reservation Info</h2>
                    <div class="text-sm text-gray-700 space-y-2">
                        <div><span class="font-medium">Event:</span> {{ $reservation->event_name }}</div>
                        <div><span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($reservation->event_date)->format('M d, Y') }}</div>
                        @if($reservation->end_date)
                            <div><span class="font-medium">End Date:</span> {{ \Carbon\Carbon::parse($reservation->end_date)->format('M d, Y') }}</div>
                        @endif
                        <div><span class="font-medium">Venue:</span> {{ $reservation->venue ?? 'N/A' }}</div>
                        <div><span class="font-medium">Contact:</span> {{ $reservation->contact_person ?? $reservation->user->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <h2 class="font-semibold text-gray-900 mb-3">Amount Due</h2>
                    <div class="text-3xl font-bold text-clsu-green">PHP {{ number_format($totalAmount, 2) }}</div>
                    <p class="text-xs text-gray-500 mt-2">Please submit your payment reference number for verification.</p>
                </div>
            </div>

            @if($reservation->payment_status === 'paid')
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 mb-6">
                    Payment has been approved. Thank you!
                </div>
            @elseif($reservation->payment_status === 'under_review')
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 mb-6">
                    Your payment is under review. We will notify you once approved.
                </div>
            @elseif(!$canSubmit)
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 mb-6">
                    Payment can be submitted after the reservation is approved.
                </div>
            @else
                <form method="POST" action="{{ route('payments.store', $reservation->id) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="reference_number" class="block text-sm font-semibold text-gray-700">Reference Number</label>
                        <input id="reference_number" name="reference_number" type="text" required
                               class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600"
                               placeholder="Enter payment reference number" value="{{ old('reference_number') }}">
                        @error('reference_number')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department_office" class="block text-sm font-semibold text-gray-700">Department/Office</label>
                        <input id="department_office" name="department_office" type="text"
                               class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600"
                               placeholder="Department or office" value="{{ old('department_office', $reservation->department) }}">
                        @error('department_office')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payer_name" class="block text-sm font-semibold text-gray-700">Name</label>
                        <input id="payer_name" name="payer_name" type="text" required
                               class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-green-600 focus:ring-green-600"
                               placeholder="Name of payer" value="{{ old('payer_name', $reservation->contact_person ?? $reservation->user->name) }}">
                        @error('payer_name')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ route('reservation.view', $reservation->id) }}" class="text-sm text-gray-600 hover:text-gray-800">Back to reservation</a>
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-clsu-green text-white rounded-lg hover:bg-green-700 transition font-semibold">
                            Submit for Review
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
@endsection
