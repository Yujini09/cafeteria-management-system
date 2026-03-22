<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationPlaced extends Notification
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public ?string $submittedBy = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): StandardAppMail
    {
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;
        $recipientName = $notifiable->name ?? null;
        $isAdminRecipient = in_array((string) ($notifiable->role ?? ''), ['admin', 'superadmin'], true);

        $mail = $isAdminRecipient
            ? $this->buildAdminMail($recipientName)
            : $this->buildCustomerMail($recipientName);

        return $mail->to($recipient, $recipientName);
    }

    protected function buildAdminMail(?string $recipientName): StandardAppMail
    {
        $reservation = $this->reservation;
        $submittedBy = $this->submittedBy
            ?? optional($reservation->user)->name
            ?? $reservation->contact_person
            ?? 'Customer';

        $sections = [];
        if (is_string($reservation->special_requests) && trim($reservation->special_requests) !== '') {
            $sections[] = [
                'title' => 'Special Requests',
                'content' => $reservation->special_requests,
            ];
        }

        return new StandardAppMail(
            topic: 'New Reservation Placed (#'.$reservation->id.')',
            title: 'A new reservation needs review',
            recipientName: $recipientName,
            introLines: [
                $submittedBy.' submitted a new reservation request.',
                'Please review the request in the admin dashboard.',
            ],
            details: [
                'Reservation ID' => '#'.$reservation->id,
                'Customer Name' => $submittedBy,
                'Customer Email' => (string) (optional($reservation->user)->email ?? $reservation->email ?? ''),
                'Date(s)' => $reservation->emailScheduleSummary(),
                'Attendees' => (string) ($reservation->number_of_persons ?? 'Not provided'),
                'Department' => (string) ($reservation->department ?? optional($reservation->user)->department ?? ''),
                'Venue' => (string) ($reservation->venue ?? $reservation->address ?? 'Not provided'),
            ],
            sections: $sections,
            action: [
                'text' => 'Review Reservation',
                'url' => route('admin.reservations.show', $reservation),
            ],
            outroLines: [
                'Approve or decline this reservation after review.',
            ],
            headerLabel: 'New Reservation',
        );
    }

    protected function buildCustomerMail(?string $recipientName): StandardAppMail
    {
        $reservation = $this->reservation;
        $sections = [];

        if (is_string($reservation->special_requests) && trim($reservation->special_requests) !== '') {
            $sections[] = [
                'title' => 'Your Special Requests',
                'content' => $reservation->special_requests,
            ];
        }

        return new StandardAppMail(
            topic: 'Reservation Received (#'.$reservation->id.')',
            title: 'Your reservation has been received',
            recipientName: $recipientName,
            introLines: [
                'We received your reservation request and it is now pending review.',
                'You will receive another email once the request status changes.',
            ],
            details: [
                'Reservation ID' => '#'.$reservation->id,
                'Date(s)' => $reservation->emailScheduleSummary(),
                'Attendees' => (string) ($reservation->number_of_persons ?? 'Not provided'),
                'Venue' => (string) ($reservation->venue ?? $reservation->address ?? 'Not provided'),
                'Status' => ucfirst((string) ($reservation->status ?? 'pending')),
            ],
            sections: $sections,
            action: [
                'text' => 'View Reservation',
                'url' => route('reservation.view', $reservation),
            ],
            outroLines: [
                'If you need to update details while pending, edit the reservation from your dashboard.',
            ],
            headerLabel: 'Reservation Received',
        );
    }
}
