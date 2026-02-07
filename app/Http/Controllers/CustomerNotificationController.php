<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    public function recent()
    {
        $user = Auth::user();
        $notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function markAllRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    public function setRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'read' => 'required|boolean',
        ]);

        $notification->update(['read' => $data['read']]);

        return response()->json(['success' => true]);
    }
}
