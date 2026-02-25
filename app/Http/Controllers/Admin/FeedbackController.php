<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::latest()->paginate(10);
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

    public function toggleVisibility(Feedback $feedback)
    {
        $feedback->update(['is_visible' => !$feedback->is_visible]);
        $status = $feedback->is_visible ? 'visible on' : 'hidden from';
        return back()->with('success', "Feedback is now {$status} the homepage.");
    }
    
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return back()->with('success', 'Feedback deleted successfully.');
    }
}