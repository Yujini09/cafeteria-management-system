<?php

namespace App\Http\Controllers;

use App\Mail\StandardAppMail;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::where('is_visible', true)
            ->latest()
            ->get();

        return view('customer.homepage', compact('feedbacks'));
    }

    public function storeFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'name' => $request->name,
            'message' => $request->message,
            'rating' => $request->rating,
            'is_visible' => false,
        ]);

        $recipients = User::whereIn('role', ['admin', 'superadmin'])
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        if ($recipients->isNotEmpty()) {
            $primaryRecipient = $recipients->first();
            $bccRecipients = $recipients->slice(1)->values();

            try {
                Mail::to($primaryRecipient)
                    ->bcc($bccRecipients->all())
                    ->send(new StandardAppMail(
                        topic: 'New Customer Feedback',
                        title: 'A new feedback entry was submitted',
                        introLines: [
                            'A new customer feedback entry has been submitted and is waiting for review.',
                        ],
                        details: [
                            'Customer Name' => $feedback->name,
                            'Rating' => $feedback->rating.'/5',
                            'Submitted At' => optional($feedback->created_at)->format('M d, Y h:i A') ?? now()->format('M d, Y h:i A'),
                            'Visibility' => 'Hidden until approved',
                        ],
                        sections: [[
                            'title' => 'Feedback',
                            'content' => $feedback->message,
                        ]],
                        outroLines: [
                            'Review this feedback in the admin panel when convenient.',
                        ],
                        headerLabel: 'New Feedback',
                    ));
            } catch (\Throwable $e) {
                Log::warning('Feedback notification email failed to send.', [
                    'error' => $e->getMessage(),
                    'feedback_id' => $feedback->id,
                ]);
            }
        }

        // UPDATED: Redirects straight to the testimonials section
        return redirect(url()->previous() . '#testimonials')->with('success', 'Thank you! Your feedback has been submitted and is waiting for review.');
    }
}