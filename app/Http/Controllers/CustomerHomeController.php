<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback; // Make sure this is imported!
use Illuminate\Support\Facades\Auth;

class CustomerHomeController extends Controller
{
    // The method to load the homepage
    public function index()
    {
        // Fetch visible feedbacks
        $feedbacks = Feedback::where('is_visible', true)
                        ->latest()
                        ->get();

        return view('customer.homepage', compact('feedbacks'));
    } // <-- Make sure this closing brace is here!

    // The method to store new feedback
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Feedback::create([
            'name' => $request->name,
            'message' => $request->message,
            'rating' => $request->rating,
            'is_visible' => false,
        ]);

        return back()->with('success', 'Thank you! Your feedback has been submitted and is waiting for review.');
    }
}