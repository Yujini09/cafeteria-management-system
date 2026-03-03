<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): StandardAppMail
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;

        return (new StandardAppMail(
            topic: 'Verify Your Email Address',
            title: 'Verify your email address',
            recipientName: $notifiable->name ?? null,
            introLines: [
                'Thanks for creating your account.',
                'Please confirm your email address to finish setting up access.',
            ],
            details: [
                'Email Address' => $notifiable->getEmailForVerification(),
            ],
            action: [
                'text' => 'Verify Email Address',
                'url' => $verificationUrl,
            ],
            outroLines: [
                'If you did not create an account, you can ignore this email.',
            ],
            headerLabel: 'Account Verification',
        ))->to($recipient, $notifiable->name ?? null);
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::signedRoute(
            'verification.link',
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
