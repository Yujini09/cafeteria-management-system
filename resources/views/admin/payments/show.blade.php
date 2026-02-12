@extends('layouts.sidebar')
@section('page-title', 'Payment Review')
@php
    $successMessage = session('success');
    $normalizedSuccess = is_string($successMessage) ? strtolower($successMessage) : '';
    $showPaymentApproved = $normalizedSuccess !== ''
        && \Illuminate\Support\Str::contains($normalizedSuccess, 'payment approved');
    $showPaymentRejected = $normalizedSuccess !== ''
        && \Illuminate\Support\Str::contains($normalizedSuccess, 'payment rejected');
    if ($showPaymentApproved || $showPaymentRejected) {
        $disableAdminSuccessToast = true;
    }
@endphp

@section('content')
<style>
.modern-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--neutral-200);
    overflow: hidden;
    transition: all 0.25s ease;
    position: relative;
}

.action-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid transparent;
    white-space: nowrap;
}

.action-btn-approve {
    color: #16a34a;
    border-color: rgba(34, 197, 94, 0.2);
}

.action-btn-approve:hover {
    transform: translateY(-1px);
}

.action-btn-decline {
    color: #dc2626;
    border-color: rgba(239, 68, 68, 0.2);
}

.action-btn-decline:hover {
    transform: translateY(-1px);
}

.back-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    border: 1px solid var(--neutral-300);
    background: white;
    color: var(--neutral-700);
}

.back-btn:hover {
    background: var(--neutral-50);
    border-color: var(--neutral-400);
    transform: translateY(-1px);
}

.status-badge {
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    gap: 0.375rem;
    border: 1px solid transparent;
}

.status-submitted {
    background: #fef3c7;
    color: #92400e;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.receipt-preview {
    border: 1px solid var(--neutral-200);
    border-radius: 14px;
    background: var(--neutral-50);
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.receipt-preview img {
    display: block;
    width: 100%;
    height: auto;
    max-height: 460px;
    object-fit: contain;
    background: #ffffff;
}

.receipt-preview-body {
    padding: 1rem;
}

.receipt-preview-meta {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--neutral-500);
}

.receipt-preview-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--neutral-200);
    background: #ffffff;
}

.receipt-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: rgba(0, 70, 46, 0.1);
    color: var(--primary);
}

.icon-sm { width: 14px; height: 14px; }
.icon-md { width: 16px; height: 16px; }
.icon-lg { width: 20px; height: 20px; }
[x-cloak] { display: none !important; }
</style>

@php
    $statusValue = strtolower($payment->status ?? 'submitted');
    $statusLabel = match($statusValue) {
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        default => 'Submitted'
    };
    $statusClass = match($statusValue) {
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
        default => 'status-submitted'
    };
@endphp

<div class="admin-page-shell modern-card p-6 mx-auto max-w-full md:max-w-none md:ml-0 md:mr-0"
     x-data="{ approvedOpen: @js($showPaymentApproved), rejectedOpen: @js($showPaymentRejected) }"
     x-effect="document.body.classList.toggle('overflow-hidden', approvedOpen || rejectedOpen)"
     @keydown.escape.window="approvedOpen = false; rejectedOpen = false">
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.payments.index') }}" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div class="header-icon">
                <x-admin.ui.icon name="fa-file-invoice-dollar" style="fas" class="text-white w-6 h-6" />
            </div>
            <div class="header-text">
                <h1 class="header-title">Payment Review</h1>
                <p class="header-subtitle">Reservation #{{ $payment->reservation_id }}</p>
            </div>
        </div>
        <span class="status-badge {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3-1.343-3-3S10.343 2 12 2s3 1.343 3 3-1.343 3-3 3zm0 4c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z"></path>
                    </svg>
                    <h2 class="info-card-title">Payment Details</h2>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reference:</dt>
                        <dd class="text-gray-900 font-semibold">{{ $payment->reference_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Department:</dt>
                        <dd class="text-gray-900">{{ $payment->department_office ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Account Code:</dt>
                        <dd class="text-gray-900">{{ $payment->account_code ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Payer Name:</dt>
                        <dd class="text-gray-900">{{ $payment->payer_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Amount:</dt>
                        <dd class="text-gray-900 font-semibold">PHP {{ number_format($payment->amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Submitted:</dt>
                        <dd class="text-gray-900">{{ $payment->created_at?->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Receipt:</dt>
                        <dd class="text-gray-900">
                            @if($payment->receipt_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}"
                                   target="_blank"
                                   class="text-admin-primary hover:text-admin-primary-hover text-sm font-medium">View</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    @if($payment->notes)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Notes:</dt>
                            <dd class="text-gray-900">{{ $payment->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h2 class="info-card-title">Reservation Details</h2>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Customer:</dt>
                        <dd class="text-gray-900">{{ $payment->reservation?->user?->name ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Event:</dt>
                        <dd class="text-gray-900">{{ $payment->reservation?->event_name ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Date:</dt>
                        <dd class="text-gray-900">{{ optional($payment->reservation?->event_date)->format('M d, Y') ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reservation Status:</dt>
                        <dd class="text-gray-900">{{ ucfirst($payment->reservation?->status ?? 'pending') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Payment Status:</dt>
                        <dd class="text-gray-900">{{ $statusLabel }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            @php
                $receiptPath = $payment->receipt_path;
                $receiptUrl = $receiptPath ? \Illuminate\Support\Facades\Storage::url($receiptPath) : null;
                $receiptExt = $receiptPath ? strtolower(pathinfo($receiptPath, PATHINFO_EXTENSION)) : '';
                $receiptIsImage = in_array($receiptExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                $receiptIsPdf = $receiptExt === 'pdf';
            @endphp

            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h2 class="info-card-title">Receipt Preview</h2>
                </div>

                @if($receiptUrl)
                    <div class="receipt-preview">
                        @if($receiptIsImage)
                            <a href="{{ $receiptUrl }}" target="_blank" rel="noopener">
                                <img src="{{ $receiptUrl }}" alt="Payment receipt preview">
                            </a>
                        @elseif($receiptIsPdf)
                            <div class="receipt-preview-body">
                                <div class="receipt-preview-meta">PDF Receipt</div>
                                <p class="text-sm text-gray-700 mt-2">
                                    PDF receipts can’t be previewed inline. Use the button below to open it.
                                </p>
                            </div>
                        @else
                            <div class="receipt-preview-body">
                                <div class="receipt-preview-meta">Receipt File</div>
                                <p class="text-sm text-gray-700 mt-2">
                                    File type not supported for preview. Use the button below to open it.
                                </p>
                            </div>
                        @endif

                        <div class="receipt-preview-actions">
                            <span class="receipt-pill">
                                {{ strtoupper($receiptExt ?: 'FILE') }}
                            </span>
                            <a href="{{ $receiptUrl }}" target="_blank" rel="noopener"
                               class="text-admin-primary hover:text-admin-primary-hover text-sm font-semibold">
                                Open Receipt
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-600">No receipt uploaded for this payment.</p>
                @endif
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="info-card-title">Review Actions</h2>
                </div>
                @if($payment->status === 'submitted')
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" data-action-loading>
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="action-btn action-btn-approve w-full justify-center" data-loading-text="Approving Payment...">
                                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Payment
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="space-y-3" data-action-loading>
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="notes" class="block text-sm font-semibold text-gray-700">Rejection Notes (optional)</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500"
                                          placeholder="Add a short reason"></textarea>
                            </div>
                            <button type="submit" class="action-btn action-btn-decline w-full justify-center" data-loading-text="Rejecting Payment...">
                                <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Payment
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-gray-700">This payment has already been reviewed.</p>
                @endif
            </div>

            <div class="info-card">
                <div class="info-card-header">
                    <svg class="icon-md text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"></path>
                    </svg>
                    <h2 class="info-card-title">Review Info</h2>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reviewed By:</dt>
                        <dd class="text-gray-900">{{ $payment->reviewer?->name ?? 'â€”' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Reviewed At:</dt>
                        <dd class="text-gray-900">{{ $payment->reviewed_at?->format('M d, Y h:i A') ?? 'â€”' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
    {{-- Payment Approved popup (unified modal style) --}}
    <div x-cloak x-show="approvedOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="approvedOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Payment Approved</h3>
            <p class="text-sm text-gray-600">The customer has been notified and the reservation is marked as paid.</p>
            <button class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium" @click="approvedOpen = false">OK</button>
        </div>
    </div>

    {{-- Payment Rejected popup (unified modal style) --}}
    <div x-cloak x-show="rejectedOpen" x-transition.opacity class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div @click="rejectedOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="modern-modal p-6 w-full max-w-sm text-center relative z-10"
             x-transition.scale.90
             @click.stop>
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Payment Rejected</h3>
            <p class="text-sm text-gray-600">The customer has been notified and can resubmit a payment reference.</p>
            <button class="mt-4 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 font-medium" @click="rejectedOpen = false">OK</button>
        </div>
    </div>
</div>
@endsection
