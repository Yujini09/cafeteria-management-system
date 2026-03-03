<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StandardAppMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'unread':
                $query->unreadFirst()->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $messages = $query->paginate(10)->withQueryString();
        $unreadCount = ContactMessage::unreadCount();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);

        if ($message->isUnread()) {
            $message->markAsRead();
        }

        return response()->json([
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'message' => $message->message,
            'status' => $message->status,
            'created_at' => $message->created_at,
            'updated_at' => $message->updated_at,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::findOrFail($id);
        $replySubject = trim((string) $request->subject);

        try {
            Mail::to($contact->email, $contact->name)->send(
                new StandardAppMail(
                    topic: 'Message Reply - '.$replySubject,
                    title: 'We replied to your message',
                    recipientName: $contact->name,
                    introLines: [
                        'A member of our team has responded to your recent message.',
                    ],
                    details: [
                        'Reply Subject' => $replySubject,
                        'Replied By' => $request->user()?->name ?? config('app.name'),
                        'Replied At' => now()->format('M d, Y h:i A'),
                    ],
                    sections: [
                        [
                            'title' => 'Response',
                            'content' => $request->message,
                        ],
                        [
                            'title' => 'Your Original Message',
                            'content' => $contact->message,
                        ],
                    ],
                    outroLines: [
                        'If you need more help, please send us another message through the website.',
                    ],
                    headerLabel: 'Message Reply',
                )
            );

            $contact->markAsReplied();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        ContactMessage::destroy($id);

        return back()->with('message_success', 'Message deleted successfully.');
    }
}
