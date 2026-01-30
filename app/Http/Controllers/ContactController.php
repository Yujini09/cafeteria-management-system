<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage; // <--- This import is critical
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Save to Database
        ContactMessage::create($validated);

        // Optional: Send Email (Uncomment if .env is set up)
        /*
        Mail::raw($validated['message'], function ($message) use ($validated) {
            $message->to('RETCafeteria@clsu2.edu.ph')
                    ->subject('New Message from ' . $validated['name']);
        });
        */

        return response()->json(['message' => 'Message saved successfully!']);
    }
}