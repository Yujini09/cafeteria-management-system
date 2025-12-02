<?php
// app/Notifications/ReservationStatusChanged.php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;

class ReservationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public string $status,          // approved|declined
        public ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        $channels = ['mail'];
        if (config('services.vonage.key') && config('services.vonage.secret')) {
            $channels[] = 'vonage';
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $r = $this->reservation;

        $mail = (new MailMessage)
            ->subject('Reservation ' . ucfirst($this->status) . ' - #'.$r->id)
            ->greeting('Hello '.(optional($r->user)->name ?? 'there').',')
            ->line("Your reservation #{$r->id} has been ".strtoupper($this->status).'.')
            ->line('Summary:')
            ->line('• Date(s): '.($r->start_date && $r->end_date ? "{$r->start_date} to {$r->end_date}" : ($r->event_date ?? '—')))
            ->line('• Days: '.($r->days ?? '—'))
            ->line('• Attendees: '.($r->guests ?? $r->attendees ?? '—'))
            ->line('• Location: '.($r->location ?? '—'));

        if ($this->status === 'declined' && $this->reason) {
            $mail->line('Reason for decline: '.$this->reason);
        }

        $mail->action('View Details', url(route('admin.reservations.show', $r)))
             ->line('Thank you!');

        return $mail;
    }

    public function toVonage($notifiable): VonageMessage
    {
        $r = $this->reservation;
        $txt = "Reservation #{$r->id} ".strtoupper($this->status).". ".
               "Attendees: ".($r->guests ?? $r->attendees ?? 'n/a').". ".
               ($this->status==='declined' && $this->reason ? "Reason: {$this->reason}" : 'See email for details.');

        return (new VonageMessage)->content($txt);
    }
}
