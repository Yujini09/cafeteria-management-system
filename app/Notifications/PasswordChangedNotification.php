<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected ?string $changeSource = null,
        protected ?string $changedAt = null,
    ) {
        $this->changedAt ??= now()->format('M d, Y h:i A');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): StandardAppMail
    {
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;
        $sourceLine = match ($this->changeSource) {
            'password-reset' => 'Your password was updated through the password reset flow.',
            'force-change' => 'Your temporary password was replaced with a new password.',
            default => 'Your account password was updated successfully.',
        };

        return (new StandardAppMail(
            topic: 'Password Changed',
            title: 'Your password was changed',
            recipientName: $notifiable->name ?? null,
            introLines: [
                $sourceLine,
                'If you made this change, no further action is required.',
            ],
            details: [
                'Email Address' => $notifiable->email ?? '',
                'Changed At' => $this->changedAt ?? '',
            ],
            action: [
                'text' => 'Sign In',
                'url' => route('login'),
            ],
            outroLines: [
                'If you did not make this change, reset your password immediately and contact support.',
            ],
            headerLabel: 'Security Notice',
        ))->to($recipient, $notifiable->name ?? null);
    }
}
