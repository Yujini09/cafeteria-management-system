<?php

namespace App\Http\Controllers;

use App\Mail\StandardAppMail;
use App\Models\AuditTrail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Support\AuditDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $expectsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:20',
        ]);

        $contactMessage = ContactMessage::create($validated);

        AuditTrail::record(
            Auth::id(),
            AuditDictionary::SUBMITTED_MESSAGE,
            AuditDictionary::MODULE_MESSAGES,
            "submitted message #{$contactMessage->id}"
        );

        $recipients = User::whereIn('role', ['admin', 'superadmin'])
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            $errorMessage = 'No admin recipients configured. Your message was saved, but we could not notify the team.';

            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()->route('contact')->with('contact_error', $errorMessage);
        }

        $primaryRecipient = $recipients->first();
        $bccRecipients = $recipients->slice(1)->values();

        $emailSent = false;

        try {
            $mail = (new StandardAppMail(
                topic: 'New Contact Message',
                title: 'A new contact message was submitted',
                introLines: [
                    'A new message was submitted through the website contact form.',
                ],
                details: [
                    'Sender' => $validated['name'],
                    'Sender Email' => $validated['email'],
                    'Received At' => now()->format('M d, Y h:i A'),
                ],
                sections: [[
                    'title' => 'Message',
                    'content' => $validated['message'],
                ]],
                outroLines: [
                    'Reply directly to this email to respond to the sender.',
                ],
                headerLabel: 'New Message',
            ))->replyTo($validated['email'], $validated['name']);

            Mail::to($primaryRecipient)
                ->bcc($bccRecipients->all())
                ->send($mail);

            $emailSent = true;
        } catch (\Throwable $e) {
            Log::warning('Contact message email failed to send.', [
                'error' => $e->getMessage(),
                'sender_email' => $validated['email'],
            ]);
        }

        if (! $emailSent) {
            $errorMessage = 'Your message was saved, but we could not send the notification email. Please try again later.';

            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()->route('contact')->with('contact_error', $errorMessage);
        }

        $successMessage = 'Your message has been sent and the admins have been notified.';

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return redirect()->route('contact')->with('contact_success', $successMessage);
    }
}
