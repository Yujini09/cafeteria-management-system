<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage; // <--- This import is critical
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|min:20',
        ]);

        // Save to Database
        ContactMessage::create($validated);

        // Send Email to all admins/superadmins (if mail is configured)
        $recipients = User::whereIn('role', ['admin', 'superadmin'])
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No admin recipients configured. Your message was saved, but we could not notify the team.'
            ], 500);
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
                    ->subject('New Contact Message from ' . $validated['name'])
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
            return response()->json([
                'success' => false,
                'message' => 'Your message was saved, but we could not send the notification email. Please try again later.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email has been sent.'
        ]);
    }
}
