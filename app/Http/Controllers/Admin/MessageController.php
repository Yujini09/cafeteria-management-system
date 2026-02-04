<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(10);
        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        return view('admin.messages.show', compact('message'));
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'reply_message' => 'required|string|min:2|max:5000',
        ]);

        $admin = Auth::user();
        $mailData = [
            'app_name' => config('app.name', 'Smart Cafeteria'),
            'recipient_name' => $message->name,
            'recipient_email' => $message->email,
            'reply_message' => $data['reply_message'],
            'original_message' => $message->message,
            'replied_at' => now()->format('M d, Y h:i A'),
            'admin_name' => $admin?->name ?? 'Admin',
        ];

        try {
            Mail::send(
                ['html' => 'emails.contact_reply', 'text' => 'emails.contact_reply_plain'],
                $mailData,
                function ($mail) use ($message, $admin, $mailData) {
                    $mail->to($message->email, $message->name)
                        ->subject('Reply to your message - ' . ($mailData['app_name'] ?? config('app.name')));

                    if (!empty($admin?->email)) {
                        $mail->replyTo($admin->email, $admin->name ?? 'Admin');
                    }
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Message reply email failed to send.', [
                'error' => $e->getMessage(),
                'message_id' => $message->id ?? null,
                'recipient_email' => $message->email ?? null,
            ]);
            return back()->withInput()->with('message_error', 'Failed to send reply. Please try again.');
        }

        return back()->with('message_success', 'Reply sent to ' . $message->email . '.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return back()->with('message_success', 'Message deleted successfully.');
    }
}
