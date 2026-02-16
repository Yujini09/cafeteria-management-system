<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('login', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            AuditTrail::record(
                $request->user()->id,
                AuditDictionary::VERIFIED_EMAIL,
                AuditDictionary::MODULE_AUTH,
                'verified email address'
            );
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('login', absolute: false).'?verified=1');
    }
}
