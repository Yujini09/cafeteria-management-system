<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminInvitationController extends Controller
{
    private const ROLE_ACTIVE_ADMIN = 'admin';
    private const ROLE_PENDING_ADMIN = 'admin_pending';

    public function activate(Request $request): RedirectResponse
    {
        $user = User::find($request->query('id'));
        $hash = (string) $request->query('hash', '');

        if (! $user) {
            return $this->redirectToLoginWithError('Admin account not found.');
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return $this->redirectToLoginWithError('Invalid admin sign-in link.');
        }

        if (! in_array($user->role, [self::ROLE_PENDING_ADMIN, self::ROLE_ACTIVE_ADMIN], true)) {
            return $this->redirectToLoginWithError('This sign-in link is only valid for admin invitations.');
        }

        if ($user->role === self::ROLE_PENDING_ADMIN) {
            $user->forceFill([
                'role' => self::ROLE_ACTIVE_ADMIN,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            return redirect()
                ->route('login', ['email' => $user->email])
                ->with('status', 'Admin account activated. Sign in using the temporary password sent by email.');
        }

        return redirect()
            ->route('login', ['email' => $user->email])
            ->with('status', 'Admin account is already active. You can sign in.');
    }

    private function redirectToLoginWithError(string $message): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors([
                'email' => $message,
            ]);
    }
}
