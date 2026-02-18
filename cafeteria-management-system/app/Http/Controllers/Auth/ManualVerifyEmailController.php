<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ManualVerifyEmailController extends Controller
{
    /**
     * Manually verify the user's email address.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $userId = session('verification_user_id');

        if (!$userId) {
            return redirect()->route('verification.notice')->withErrors(['message' => 'No user found for verification.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('verification.notice')->withErrors(['message' => 'User not found.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        // Clear the session
        session()->forget('verification_user_id');

        return redirect()->route('login')->with('status', 'Email verified successfully. You can now log in.');
    }

    /**
     * Verify email via link (for email notifications).
     */
    public function verifyViaLink(Request $request): RedirectResponse
    {
        // Get the user from the signed URL parameters
        $userId = $request->query('id');
        $hash = $request->query('hash');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'User not found.']);
        }

        // Validate the hash to ensure the link is valid
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->withErrors(['message' => 'Invalid verification link.']);
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('login')->with('status', 'Email verified successfully. You can now log in.');
    }
}