<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Models\User;
use App\Support\AuditDictionary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PasswordResetLinkController extends Controller
{
    private const RESET_LINK_MAX_ATTEMPTS = 1;
    private const RESET_LINK_THROTTLE_SECONDS = 180;

    /**
     * Display the password reset link request view.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')->with('forgot', true);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validateWithBag('passwordReset', [
            'email' => ['required', 'email'],
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, self::RESET_LINK_MAX_ATTEMPTS)) {
            $retryAfter = max(1, RateLimiter::availableIn($throttleKey));

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('passwords.throttled')], 'passwordReset')
                ->with('forgot', true)
                ->with('password_reset_retry_after', $retryAfter);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $user = User::where('email', (string) $request->input('email'))->first();
            AuditTrail::record(
                $user?->id,
                AuditDictionary::REQUESTED_PASSWORD_RESET,
                AuditDictionary::MODULE_AUTH,
                'requested password reset link'
            );

            RateLimiter::hit($throttleKey, self::RESET_LINK_THROTTLE_SECONDS);

            return back()->with('status', __($status))->with('forgot', true);
        }

        if ($status === Password::RESET_THROTTLED) {
            $retryAfter = max(1, RateLimiter::availableIn($throttleKey));

            if ($retryAfter <= 0) {
                $retryAfter = self::RESET_LINK_THROTTLE_SECONDS;
            }

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('passwords.throttled')], 'passwordReset')
                ->with('forgot', true)
                ->with('password_reset_retry_after', $retryAfter);
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)], 'passwordReset')
            ->with('forgot', true);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email').'|'.$request->ip()));
    }
}
