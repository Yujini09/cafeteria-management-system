<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SuperAdminController extends Controller
{
    public function index(): View
    {
        // Show everyone except superadmin
        $users = User::where('role', '!=', 'superadmin')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.users', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','unique:users,email'],
        ]);

        if (!Schema::hasColumn('users', 'must_change_password')) {
            return back()
                ->withInput()
                ->with('error', 'Admin account could not be created because the database is missing the must_change_password column. Please run migrations and try again.');
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $user = null;

        try {
            DB::transaction(function () use (&$user, $data, $temporaryPassword) {
                $user = User::create([
                    'name'                 => $data['name'],
                    'email'                => $data['email'],
                    'password'             => Hash::make($temporaryPassword),
                    'role'                 => 'admin', // always admin when created by superadmin
                    'email_verified_at'    => now(), // no verification needed for admins
                    'must_change_password' => true,
                ]);

                $this->sendAdminCredentialsEmail($user, $temporaryPassword);

                AuditTrail::create([
                    'user_id'     => Auth::id(),
                    'action'      => 'Created Admin',
                    'module'      => 'users',
                    'description' => 'created an admin',
                ]);
            });
        } catch (TransportExceptionInterface $e) {
            Log::warning('Admin account email failed to send.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Admin account could not be created because the email failed to send. Please check mail configuration and try again.');
        } catch (QueryException $e) {
            Log::warning('Admin account creation failed due to database error.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Admin account could not be created due to a database error. Please run migrations and try again.');
        } catch (\Throwable $e) {
            Log::warning('Admin account creation failed.', [
                'error' => $e->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Admin account could not be created. Please try again.');
        }

        if (!$user) {
            return redirect()->route('superadmin.users')
                ->with('success', 'Admin created successfully. A temporary password has been emailed.');
        }

        $perPage = 10;
        $position = User::where('role', '!=', 'superadmin')
            ->where(function ($query) use ($user) {
                $query->where('name', '<', $user->name)
                    ->orWhere(function ($subQuery) use ($user) {
                        $subQuery->where('name', '=', $user->name)
                            ->where('id', '<', $user->id);
                    });
            })
            ->count();
        $page = intdiv($position, $perPage) + 1;

        return redirect()
            ->route('superadmin.users', ['page' => $page])
            ->with('success', 'Admin created successfully. A temporary password has been emailed.');
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
        if (Auth::check()) {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => 'Viewed Audit Trail',
                'module' => 'audit',
                'description' => "viewed audit trail for {$user->name} ({$user->email})",
            ]);
        }

        $audits = AuditTrail::where('user_id', $user->id)->latest()->get();
        return view('superadmin.audit', compact('user','audits'));
    }

    public function recentAudits()
    {
        $audits = AuditTrail::with('user')->latest()->take(50)->get();
        return response()->json($audits);
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

        Notification::where('user_id', $user->id)->update(['read' => true]);

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

    private function generateTemporaryPassword(int $length = 12): string
    {
        $length = max(8, $length);
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $numbers = '23456789';
        $special = '!@#$%&*?';
        $all = $upper . $lower . $numbers . $special;

        $password = $upper[random_int(0, strlen($upper) - 1)]
            . $lower[random_int(0, strlen($lower) - 1)]
            . $numbers[random_int(0, strlen($numbers) - 1)]
            . $special[random_int(0, strlen($special) - 1)];

        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    private function sendAdminCredentialsEmail(User $user, string $temporaryPassword): void
    {
        $mailData = [
            'app_name' => config('app.name', 'Smart Cafeteria'),
            'user_name' => $user->name,
            'user_email' => $user->email,
            'temporary_password' => $temporaryPassword,
            'login_url' => route('login'),
            'created_at' => now()->format('M d, Y h:i A'),
        ];

        Mail::send(
            ['html' => 'emails.admin_account_created', 'text' => 'emails.admin_account_created_plain'],
            $mailData,
            function ($message) use ($user, $mailData) {
                $message->to($user->email, $user->name)
                    ->subject('Your admin account for ' . ($mailData['app_name'] ?? config('app.name')));
            }
        );
    }
}
