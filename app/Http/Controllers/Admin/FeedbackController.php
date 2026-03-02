<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $visibility = $request->string('visibility')->toString();

        $feedbackQuery = Feedback::query();

        if ($visibility === 'visible') {
            $feedbackQuery->where('is_visible', true);
        } elseif ($visibility === 'hidden') {
            $feedbackQuery->where('is_visible', false);
        } else {
            $visibility = '';
        }

        $feedbacks = $feedbackQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $visibleCount = Feedback::where('is_visible', true)->count();
        $hiddenCount = Feedback::where('is_visible', false)->count();

        return view('admin.feedbacks.index', compact('feedbacks', 'visibility', 'visibleCount', 'hiddenCount'));
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
