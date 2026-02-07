<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentRequested extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public bool $isReminder = false,
        public int $reminderCount = 0
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $r = $this->reservation;
        $subject = $this->isReminder
            ? "Payment Reminder #{$this->reminderCount} - Reservation #{$r->id}"
            : "Payment Required - Reservation #{$r->id}";

        $payUrl = route('login', ['redirect' => route('payments.show', $r->id)]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.(optional($r->user)->name ?? 'there').',')
            ->line("Your reservation #{$r->id} is approved and payment is now required.")
            ->line('Please submit your payment reference number using the button below.')
            ->action('Pay Now', $payUrl)
            ->line('Thank you!');
    }
}
