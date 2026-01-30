<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SuperAdminController extends Controller
{
    public function index(): View
    {
        // Show everyone except superadmin
        $users = User::where('role', '!=', 'superadmin')->orderBy('name')->get();

        return view('superadmin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => 'admin', // always admin when created by superadmin
            'email_verified_at' => now(), // no verification needed for admins
        ]);

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Created Admin',
            'module'      => 'users',
            'description' => 'created an admin',
        ]);

        return redirect()->route('superadmin.users')->with('success', 'Admin created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'admin') {
            return back()->with('error', 'Only admin accounts can be edited.');
        }

        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email,' . $user->id],
        ]);

        $user->update($data);

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Updated Admin',
            'module'      => 'users',
            'description' => 'updated an admin',
        ]);

        return redirect()->route('superadmin.users')->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $role  = $user->role;
        $email = $user->email;

        $user->delete();

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Deleted User',
            'module'      => 'users',
            'description' => 'deleted a user',
        ]);

        return back()->with('success', 'User deleted successfully.');
    }

    public function audit(User $user): View
    {
        $audits = AuditTrail::where('user_id', $user->id)->latest()->get();
        return view('superadmin.audit', compact('user','audits'));
    }

    public function recentAudits()
    {
        $audits = AuditTrail::with('user')->latest()->take(50)->get();
        return response()->json($audits);
    }

    public function clearRecentAudits(Request $request): RedirectResponse
    {
        AuditTrail::truncate();

        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'Cleared Audit Trails',
            'module'      => 'audit',
            'description' => 'cleared all recent activities',
        ]);

        return back()->with('success', 'All audit trails have been cleared.');
    }

    public function recentNotifications()
    {
        $notificationService = new NotificationService();
        $user = Auth::user();

        // Get unique notifications for the user
        $notifications = $notificationService->getNotificationsForUser($user, 20);

        return response()->json($notifications);
    }

    public function markAllNotificationsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        Notification::query()->update(['read' => true]);

        return response()->json(['success' => true]);
    }

    public function setNotificationRead(Request $request, Notification $notification)
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403);
        }

        $data = $request->validate([
            'read' => ['required', 'boolean'],
        ]);

        $notification->update(['read' => $data['read']]);

        return response()->json(['success' => true, 'read' => $notification->read]);
    }
}