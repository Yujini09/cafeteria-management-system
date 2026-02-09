<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentReviewed;

class PaymentController extends Controller
{
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $reservation->loadMissing(['items.menu', 'user']);
        $latestPayment = $reservation->payments()->latest()->first();
        $totalAmount = $this->calculateReservationTotal($reservation);
        $canSubmit = $reservation->status === 'approved' && $reservation->payment_status === 'pending';

        return view('customer.payment', compact('reservation', 'latestPayment', 'totalAmount', 'canSubmit'));
    }

    public function store(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        if ($reservation->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved reservations can submit payment.');
        }

        if ($reservation->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Payment has already been submitted.');
        }

        $validated = $request->validate([
            'reference_number' => 'required|string|max:120',
            'department_office' => 'nullable|string|max:120',
            'payer_name' => 'required|string|max:120',
            'account_code' => 'nullable|string|max:120',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $reservation->loadMissing(['items.menu', 'user']);
        $amount = $this->calculateReservationTotal($reservation);
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('payment-receipts', 'public');
        }

        DB::transaction(function () use ($reservation, $validated, $amount, $receiptPath) {
            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'reference_number' => $validated['reference_number'],
                'department_office' => $validated['department_office'] ?? null,
                'payer_name' => $validated['payer_name'],
                'account_code' => $validated['account_code'] ?? $reservation->account_code ?? null,
                'amount' => $amount,
                'status' => 'submitted',
                'receipt_path' => $receiptPath,
                'receipt_uploaded_at' => $receiptPath ? now() : null,
            ]);

            $reservation->update([
                'payment_status' => 'under_review',
            ]);

            (new NotificationService())->createAdminNotification(
                'payment_submitted',
                'payments',
                "Payment submitted for reservation #{$reservation->id}",
                [
                    'reservation_id' => $reservation->id,
                    'payment_id' => $payment->id,
                    'customer_name' => optional($reservation->user)->name ?? 'Unknown',
                    'url' => route('admin.payments.show', $payment),
                ]
            );
        });

        return redirect()->route('payments.show', $reservation)->with('success', 'Payment submitted for review.');
    }

    public function due()
    {
        $userId = Auth::id();
        if (!$userId) {
            abort(403, 'Unauthorized.');
        }

        $yesterday = now()->subDay()->toDateString();

        $reservation = Reservation::query()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('payment_status', 'pending')
            ->whereDate(DB::raw('COALESCE(end_date, event_date)'), '<=', $yesterday)
            ->with(['items.menu', 'user'])
            ->orderByRaw('COALESCE(end_date, event_date) desc')
            ->first();

        if (! $reservation) {
            return response()->json(['reservation' => null]);
        }

        $amount = $this->calculateReservationTotal($reservation);

        return response()->json([
            'reservation' => [
                'id' => $reservation->id,
                'event_name' => $reservation->event_name,
                'event_date' => optional($reservation->event_date)->format('M d, Y'),
                'end_date' => optional($reservation->end_date)->format('M d, Y'),
                'venue' => $reservation->venue,
                'contact_person' => $reservation->contact_person ?? optional($reservation->user)->name,
                'department' => $reservation->department ?? optional($reservation->user)->department,
                'account_code' => $reservation->account_code ?? null,
            ],
            'total_amount' => $amount,
        ]);
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Payment::with(['reservation', 'user'])->latest();
        if (in_array($status, ['submitted', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $payments = $query->paginate(12)->withQueryString();

        return view('admin.payments.index', compact('payments', 'status'));
    }

    public function showAdmin(Payment $payment)
    {
        $payment->loadMissing(['reservation.user']);
        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted payments can be approved.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $payment->reservation->update([
                'payment_status' => 'paid',
            ]);
        });

        $reservation = $payment->reservation;
        $user = $reservation->user;
        if ($user) {
            $user->notify(new PaymentReviewed($payment, 'approved'));
        }

        (new NotificationService())->createUserNotification(
            $reservation->user_id,
            'payment_approved',
            'payments',
            "Payment approved for reservation #{$reservation->id}",
            [
                'reservation_id' => $reservation->id,
                'payment_id' => $payment->id,
                'url' => route('reservation.view', $reservation->id),
                'link_label' => 'View Details',
            ]
        );

        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment approved.');
    }

    public function reject(Request $request, Payment $payment)
    {
        if ($payment->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted payments can be rejected.');
        }

        $data = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            $payment->reservation->update([
                'payment_status' => 'pending',
                'payment_reminder_count' => 0,
                'payment_last_reminder_at' => now(),
                'payment_requested_at' => $payment->reservation->payment_requested_at ?? now(),
            ]);
        });

        $reservation = $payment->reservation;
        $user = $reservation->user;
        if ($user) {
            $user->notify(new PaymentReviewed($payment, 'rejected'));
        }

        (new NotificationService())->createUserNotification(
            $reservation->user_id,
            'payment_rejected',
            'payments',
            "Payment rejected for reservation #{$reservation->id}",
            [
                'reservation_id' => $reservation->id,
                'payment_id' => $payment->id,
                'url' => route('payments.show', $reservation->id),
                'link_label' => 'Pay Now',
            ]
        );

        return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment rejected.');
    }

    private function calculateReservationTotal(Reservation $reservation): float
    {
        $total = 0;
        foreach ($reservation->items as $item) {
            $menu = $item->menu;
            if (!$menu) {
                continue;
            }

            $price = $menu->price ?? 0;
            if ($price == 0) {
                $price = ($menu->type ?? 'standard') === 'special' ? 200 : 150;
            }

            $total += ($item->quantity ?? 0) * $price;
        }

        return (float) $total;
    }
}
