<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;
use App\Models\ContactMessage; 
use App\Models\User;
use App\Support\AuditDictionary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        // Save to Database
        $contactMessage = ContactMessage::create($validated);

        // Contact form can be submitted by guests; only log when user is authenticated.
        AuditTrail::record(
            Auth::id(),
            AuditDictionary::SUBMITTED_MESSAGE,
            AuditDictionary::MODULE_MESSAGES,
            "submitted message #{$contactMessage->id}"
        );

        // Send Email to all admins/superadmins (if mail is configured)
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
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->route('contact')->with('contact_error', $errorMessage);
        }

        $primaryRecipient = $recipients->first();
        $bccRecipients = $recipients->slice(1)->values();

        $mailData = [
            'app_name' => config('app.name', 'Smart Cafeteria'),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_message' => $validated['message'],
            'sent_at' => now()->format('M d, Y h:i A'),
        ];

        $emailSent = false;
        try {
            Mail::send(
                ['html' => 'emails.contact_message', 'text' => 'emails.contact_message_plain'],
                $mailData,
                function ($message) use ($validated, $primaryRecipient, $bccRecipients) {
                $message->to($primaryRecipient)
                    ->subject('New Message from ' . $validated['name'])
                    ->replyTo($validated['email'], $validated['name']);

                if ($bccRecipients->isNotEmpty()) {
                    $message->bcc($bccRecipients->all());
                }
            });
            $emailSent = true;
        } catch (\Throwable $e) {
            Log::warning('Contact message email failed to send.', [
                'error' => $e->getMessage(),
                'sender_email' => $validated['email'],
            ]);
        }

        if (!$emailSent) {
            $errorMessage = 'Your message was saved, but we could not send the notification email. Please try again later.';

            if ($expectsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->route('contact')->with('contact_error', $errorMessage);
        }

        $successMessage = 'Your message has been sent and the admins have been notified.';

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->route('contact')->with('contact_success', $successMessage);
    }
}
