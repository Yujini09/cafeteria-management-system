<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public string $status,
        public ?string $reason = null
    ) {
    }

    public function via($notifiable): array
    {
        $channels = ['mail'];

        if (config('services.vonage.key') && config('services.vonage.secret')) {
            $channels[] = 'vonage';
        }

        return $channels;
    }

    public function toMail($notifiable): StandardAppMail
    {
        $reservation = $this->reservation;
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;
        $sections = [];

        if ($this->status === 'declined' && $this->reason) {
            $sections[] = [
                'title' => 'Reason for Decline',
                'content' => $this->reason,
            ];
        }

        return (new StandardAppMail(
            topic: 'Reservation '.$this->displayStatus().' (#'.$reservation->id.')',
            title: 'Your reservation was '.$this->displayStatus(),
            recipientName: optional($reservation->user)->name,
            introLines: [
                'Your reservation request #'.$reservation->id.' has been '.$this->displayStatusText().'.',
            ],
            details: $this->reservationDetails($reservation),
            sections: $sections,
            action: [
                'text' => 'View Reservation',
                'url' => route('reservation.view', $reservation),
            ],
            outroLines: [
                'If you need help with this reservation, please contact the cafeteria team.',
            ],
            headerLabel: 'Reservation Update',
        ))->to($recipient, optional($reservation->user)->name);
    }

    public function toVonage($notifiable): VonageMessage
    {
        $reservation = $this->reservation;
        $txt = 'Reservation #'.$reservation->id.' '.strtoupper($this->status).'. '
            .'Attendees: '.($reservation->number_of_persons ?? 'n/a').'. '
            .($this->status === 'declined' && $this->reason ? 'Reason: '.$this->reason : 'See email for details.');

        return (new VonageMessage)->content($txt);
    }

    protected function displayStatus(): string
    {
        return match ($this->status) {
            'approved' => 'Accepted',
            'declined' => 'Declined',
            default => ucfirst($this->status),
        };
    }

    protected function displayStatusText(): string
    {
        return strtolower($this->displayStatus());
    }

    protected function reservationDetails(Reservation $reservation): array
    {
        return [
            'Reservation ID' => '#'.$reservation->id,
            'Date(s)' => $reservation->emailScheduleSummary(),
            'Attendees' => (string) ($reservation->number_of_persons ?? 'Not provided'),
            'Venue' => (string) ($reservation->venue ?? $reservation->address ?? 'Not provided'),
        ];
    }
}
