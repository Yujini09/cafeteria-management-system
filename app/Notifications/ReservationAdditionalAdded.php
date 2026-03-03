<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use App\Models\Reservation;
use App\Models\ReservationAdditional;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationAdditionalAdded extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public ReservationAdditional $additional,
        public float $updatedGrandTotal
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): StandardAppMail
    {
        $reservation = $this->reservation;
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;
        $additionalAmount = (float) ($this->additional->price ?? 0);

        return (new StandardAppMail(
            topic: 'Reservation Updated (#'.$reservation->id.')',
            title: 'A new additional charge was added',
            recipientName: optional($reservation->user)->name,
            introLines: [
                'Your reservation #'.$reservation->id.' was updated with an additional charge.',
            ],
            details: [
                'Reservation ID' => '#'.$reservation->id,
                'Date(s)' => $reservation->emailScheduleSummary(),
                'Attendees' => (string) ($reservation->number_of_persons ?? 'Not provided'),
                'Venue' => (string) ($reservation->venue ?? $reservation->address ?? 'Not provided'),
                'Updated Total' => 'PHP '.number_format($this->updatedGrandTotal, 2),
            ],
            sections: [[
                'title' => 'Additional Charge',
                'content' => ($this->additional->name ?: 'Additional Charge').' (PHP '.number_format($additionalAmount, 2).')',
            ]],
            action: [
                'text' => 'View Reservation',
                'url' => route('reservation.view', $reservation),
            ],
            outroLines: [
                'Please review the updated total in your reservation details.',
            ],
            headerLabel: 'Reservation Update',
        ))->to($recipient, optional($reservation->user)->name);
    }
}
