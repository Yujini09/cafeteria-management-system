<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification as NotificationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
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
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists with this email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended(route('customer.homepage'));
            } else {
                // User doesn't exist, create new user
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

                Auth::login($user);
                return redirect()->intended(route('customer.homepage'));
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['google' => 'Unable to login with Google. Please try again.']);
        }
    }
}
