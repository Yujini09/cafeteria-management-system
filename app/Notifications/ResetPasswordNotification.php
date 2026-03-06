<?php

namespace App\Notifications;

use App\Mail\StandardAppMail;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        $resetUrl = $this->resetUrl($notifiable);
        $recipient = $notifiable->routeNotificationFor('mail', $this) ?: $notifiable->email;
        $expiryMinutes = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new StandardAppMail(
            topic: 'Password Reset Request',
            title: 'Reset your password',
            recipientName: $notifiable->name ?? null,
            introLines: [
                'We received a request to reset the password for your account.',
                'Use the button below to choose a new password.',
            ],
            details: [
                'Email Address' => $notifiable->getEmailForPasswordReset(),
                'Link Expires In' => $expiryMinutes.' minutes',
            ],
            action: [
                'text' => 'Reset Password',
                'url' => $resetUrl,
            ],
            outroLines: [
                'If you did not request a password reset, you can ignore this email and no changes will be made.',
            ],
            headerLabel: 'Password Reset',
        ))->to($recipient, $notifiable->name ?? null);
    }
}
