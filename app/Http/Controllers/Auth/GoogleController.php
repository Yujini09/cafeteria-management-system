<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use App\Support\AuditDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        // Get all admin and superadmin users
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        
        // Create a notification for each admin/superadmin
        foreach ($admins as $admin) {
            NotificationModel::create([
                'user_id' => $admin->id,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        }
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            \Log::info('Google OAuth callback initiated');
            
            $googleUser = Socialite::driver('google')->user();
            $request->session()->forget('google_oauth_state_retry');
            \Log::info('Google user retrieved', ['email' => $googleUser->getEmail()]);

            // Check if user exists with this email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User exists, log them in
                \Log::info('Existing user logging in via Google', ['user_id' => $user->id, 'email' => $user->email]);
                Auth::login($user);
                Session::regenerate();
                AuditTrail::record(
                    $user->id,
                    AuditDictionary::LOGGED_IN_VIA_GOOGLE,
                    AuditDictionary::MODULE_AUTH,
                    'logged in via Google OAuth'
                );
                return redirect()->route('dashboard');
            } else {
                // User doesn't exist, create new user
                \Log::info('Creating new user from Google OAuth', ['email' => $googleUser->getEmail()]);
                
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)), // Random password since Google auth
                    'email_verified_at' => now(), // Google accounts are verified
                    'role' => 'customer', // default role
                    'google_id' => $googleUser->getId(),
                ]);

                // Create notification for admins/superadmin about new Google registration
                $this->createAdminNotification('user_registered_google', 'users', "New customer {$user->name} registered via Google", [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'provider' => 'google',
                ]);

                \Log::info('New user created via Google OAuth', ['user_id' => $user->id, 'email' => $user->email]);
                Auth::login($user);
                Session::regenerate();
                AuditTrail::record(
                    $user->id,
                    AuditDictionary::LOGGED_IN_VIA_GOOGLE,
                    AuditDictionary::MODULE_AUTH,
                    'logged in via Google OAuth (new account)'
                );
                return redirect()->route('dashboard');
            }
        } catch (InvalidStateException $e) {
            $alreadyRetried = (bool) $request->session()->pull('google_oauth_state_retry', false);

            \Log::warning('Google OAuth invalid state encountered', [
                'already_retried' => $alreadyRetried,
            ]);

            // One automatic retry keeps UX smooth without risking redirect loops.
            if (!$alreadyRetried) {
                $request->session()->put('google_oauth_state_retry', true);
                return redirect()->route('auth.google');
            }

            return redirect()->route('login')->withErrors([
                'google' => 'Google sign-in session expired. Please try again.',
            ]);
        } catch (\Throwable $e) {
            $request->session()->forget('google_oauth_state_retry');
            \Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')->withErrors([
                'google' => 'Unable to login with Google. Please try again.',
            ]);
        }
    }
}
