<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // 1. Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // 2. Sorting Logic (Matches SariSariPH)
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'unread':
                $query->unreadFirst()->orderBy('created_at', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $messages = $query->paginate(10)->withQueryString();
        
        // Count for the badge
        $unreadCount = ContactMessage::unreadCount();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    // AJAX Handler: View Message (Marks as READ)
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Only update status if it is currently UNREAD
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

    // AJAX Handler: Reply (Marks as REPLIED)
    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::findOrFail($id);

        try {
            // Send Email
            Mail::html($request->message, function ($mail) use ($contact, $request) {
                $mail->to($contact->email)
                     ->subject($request->subject)
                     ->from(config('mail.from.address'), config('app.name'));
            });

            // Update status to REPLIED
            $contact->markAsReplied();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        ContactMessage::destroy($id);
        return back()->with('message_success', 'Message deleted successfully.');
    }
}
