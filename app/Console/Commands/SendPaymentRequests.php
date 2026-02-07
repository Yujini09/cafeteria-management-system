<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\PaymentRequested;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class SendPaymentRequests extends Command
{
    protected $signature = 'payments:send-requests';
    protected $description = 'Send payment request notifications after approved reservation end date';

    public function handle(): int
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        $initialRequests = Reservation::query()
            ->where('status', 'approved')
            ->where('payment_status', 'pending')
            ->whereNull('payment_requested_at')
            ->whereDate(DB::raw('COALESCE(end_date, event_date)'), '<=', $yesterday->toDateString())
            ->get();

        foreach ($initialRequests as $reservation) {
            $this->sendPaymentRequest($reservation, false, 0);
            $reservation->update([
                'payment_requested_at' => now(),
                'payment_last_reminder_at' => now(),
                'payment_reminder_count' => 0,
            ]);
        }

        $reminders = Reservation::query()
            ->where('status', 'approved')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_requested_at')
            ->where('payment_reminder_count', '<', 3)
            ->where(function ($q) use ($yesterday) {
                $q->whereNull('payment_last_reminder_at')
                    ->orWhereDate('payment_last_reminder_at', '<=', $yesterday->toDateString());
            })
            ->get();

        foreach ($reminders as $reservation) {
            $nextCount = (int) $reservation->payment_reminder_count + 1;
            $this->sendPaymentRequest($reservation, true, $nextCount);
            $reservation->update([
                'payment_last_reminder_at' => now(),
                'payment_reminder_count' => $nextCount,
            ]);
        }

        return self::SUCCESS;
    }

    private function sendPaymentRequest(Reservation $reservation, bool $isReminder, int $reminderCount): void
    {
        $notification = new PaymentRequested($reservation, $isReminder, $reminderCount);
        $user = $reservation->user;

        if ($user) {
            $user->notify($notification);

            (new NotificationService())->createUserNotification(
                $user->id,
                $isReminder ? 'payment_reminder' : 'payment_requested',
                'payments',
                $isReminder
                    ? "Payment reminder #{$reminderCount} for reservation #{$reservation->id}"
                    : "Payment required for reservation #{$reservation->id}",
                [
                    'reservation_id' => $reservation->id,
                    'reminder_count' => $reminderCount,
                    'url' => route('payments.show', $reservation->id),
                    'link_label' => 'Pay Now',
                ]
            );

            return;
        }

        if (!empty($reservation->email)) {
            NotificationFacade::route('mail', $reservation->email)->notify($notification);
        }
    }
}
