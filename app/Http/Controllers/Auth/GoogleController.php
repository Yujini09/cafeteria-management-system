<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use App\Support\AuditDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    private const PENDING_ACCOUNT_MESSAGE = 'This account already exists and is awaiting verification. Please check your email for the verification link or contact the admin.';

    /** Create notification for admins/superadmin */
    protected function createAdminNotification(string $action, string $module, string $description, array $metadata = []): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

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
            $email = trim((string) $googleUser->getEmail());
            $name = trim((string) $googleUser->getName());
            $googleId = trim((string) $googleUser->getId());

            if ($email === '') {
                \Log::warning('Google OAuth callback missing email address');

                return redirect()->route('login')->withErrors([
                    'google' => 'Google did not return an email address for this account. Please choose a Google account with email access and try again.',
                ]);
            }

            if ($name === '') {
                $name = Str::before($email, '@');
            }

            \Log::info('Google user retrieved', ['email' => $email]);

            // Check if user exists with this email
            $user = User::where('email', $email)->first();

            if ($user) {
                if ($user->isPendingAccount()) {
                    \Log::info('Pending user blocked from Google OAuth sign-in', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);

                    return redirect()->route('login')->withErrors([
                        'google' => self::PENDING_ACCOUNT_MESSAGE,
                    ]);
                }

                $this->attachGoogleIdIfPossible($user, $googleId);

                // User exists, log them in
                \Log::info('Existing user logging in via Google', ['user_id' => $user->id, 'email' => $user->email]);
                Auth::login($user);
                Session::regenerate();
                $this->recordGoogleAudit($user->id, 'logged in via Google OAuth');
                return redirect()->route('dashboard');
            } else {
                // User doesn't exist, create new user
                \Log::info('Creating new user from Google OAuth', ['email' => $email]);
                
                $userPayload = [
                    'name' => $name,
                    'email' => $email,
                    'password' => User::makeOauthOnlyPassword(),
                    'role' => 'customer',
                ];

                if ($googleId !== '' && Schema::hasColumn('users', 'google_id')) {
                    $userPayload['google_id'] = $googleId;
                }

                $user = User::create($userPayload);

                if (Schema::hasColumn('users', 'email_verified_at')) {
                    $user->forceFill([
                        'email_verified_at' => now(),
                    ])->save();
                }

                // Create notification for admins/superadmin about new Google registration
                $this->runNonCriticalGoogleSideEffect('create admin notification for Google registration', function () use ($user): void {
                    $this->createAdminNotification('user_registered_google', 'users', "New customer {$user->name} registered via Google", [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'provider' => 'google',
                    ]);
                });

                \Log::info('New user created via Google OAuth', ['user_id' => $user->id, 'email' => $user->email]);
                Auth::login($user);
                Session::regenerate();
                $this->recordGoogleAudit($user->id, 'logged in via Google OAuth (new account)');
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

    private function attachGoogleIdIfPossible(User $user, string $googleId): void
    {
        if ($googleId === '' || ! Schema::hasColumn('users', 'google_id') || ! blank($user->google_id)) {
            return;
        }

        $this->runNonCriticalGoogleSideEffect('attach google_id to existing account', function () use ($user, $googleId): void {
            $user->forceFill([
                'google_id' => $googleId,
            ])->save();
        });
    }

    private function recordGoogleAudit(int $userId, string $description): void
    {
        if (! Schema::hasTable('audit_trails')) {
            return;
        }

        $this->runNonCriticalGoogleSideEffect('record Google OAuth audit trail', function () use ($userId, $description): void {
            AuditTrail::record(
                $userId,
                AuditDictionary::LOGGED_IN_VIA_GOOGLE,
                AuditDictionary::MODULE_AUTH,
                $description
            );
        });
    }

    private function runNonCriticalGoogleSideEffect(string $operation, callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            \Log::warning('Google OAuth non-critical operation failed', [
                'operation' => $operation,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
