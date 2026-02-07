<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public string $status // approved|rejected
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $r = $this->payment->reservation;
        $statusLabel = strtoupper($this->status);
        $subject = "Payment {$statusLabel} - Reservation #{$r->id}";

        $actionUrl = $this->status === 'approved'
            ? route('reservation.view', $r->id)
            : route('payments.show', $r->id);

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.(optional($r->user)->name ?? 'there').',')
            ->line("Your payment for reservation #{$r->id} has been {$statusLabel}.");

        if ($this->status === 'rejected' && $this->payment->notes) {
            $mail->line('Notes: '.$this->payment->notes);
        }

        $mail->action('View Details', $actionUrl)
             ->line('Thank you!');

        return $mail;
    }
}
