<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\AuditTrail;
use App\Support\AuditDictionary;

class AuthenticatedSessionController extends Controller
{
    private const ROLE_ACTIVE_ADMIN = 'admin';
    private const ROLE_ACTIVE_CUSTOMER = 'customer';
    private const ROLE_PENDING_ADMIN = 'admin_pending';
    private const ROLE_PENDING_CUSTOMER = 'customer_pending';

    // Show login (or redirect if already logged in)
    public function create(Request $request): View|RedirectResponse
    {
        $redirect = $this->normalizeRedirect($request->query('redirect'));
        if ($redirect) {
            $request->session()->put('login_redirect', $redirect);
        }

        if (Auth::check()) {
            $this->activatePendingAccountIfEligible(Auth::user());

            $redirect = $this->normalizeRedirect($request->query('redirect') ?? $request->session()->pull('login_redirect'));
            if ($redirect) {
                return redirect($redirect);
            }

            if (in_array(Auth::user()->role, [self::ROLE_ACTIVE_ADMIN, 'superadmin'], true)) return redirect()->route('admin.dashboard');
            if (Auth::user()->role === self::ROLE_ACTIVE_CUSTOMER) return redirect()->route('customer.homepage');

            // No valid role? Force logout to avoid 403 loop
            Auth::logout();
            return redirect()->route('login');
        }

        return view('auth.login');
    }

    // Login
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $this->activatePendingAccountIfEligible(Auth::user());

        Session::regenerate();

        // ✅ Log login action
        AuditTrail::record(
            Auth::id(),
            AuditDictionary::LOGGED_IN,
            AuditDictionary::MODULE_AUTH,
            'logged in with email/password'
        );

        $redirect = $this->normalizeRedirect($request->input('redirect') ?? $request->session()->pull('login_redirect'));
        if ($redirect) {
            return redirect($redirect);
        }

        if (in_array(Auth::user()->role, [self::ROLE_ACTIVE_ADMIN, 'superadmin'], true)) return redirect()->route('admin.dashboard');

        return redirect()->route('customer.homepage');
    }

    // Logout
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Log logout action before session ends
        AuditTrail::record(
            Auth::id(),
            AuditDictionary::LOGGED_OUT,
            AuditDictionary::MODULE_AUTH,
            'logged out'
        );

        Auth::guard('web')->logout();

    Session::invalidate();
    Session::regenerateToken();

        return redirect()->route('login');
    }

    private function normalizeRedirect(?string $redirect): ?string
    {
        if (!$redirect) {
            return null;
        }

        $parts = parse_url($redirect);
        if ($parts === false) {
            return null;
        }

        $path = $parts['path'] ?? '';
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) {
            return null;
        }

        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $path.$query.$fragment;
    }

    private function activatePendingAccountIfEligible(?User $user): void
    {
        if (!$user || !$user->hasVerifiedEmail()) {
            return;
        }

        $activeRole = match ($user->role) {
            self::ROLE_PENDING_ADMIN => self::ROLE_ACTIVE_ADMIN,
            self::ROLE_PENDING_CUSTOMER => self::ROLE_ACTIVE_CUSTOMER,
            default => null,
        };

        if ($activeRole === null) {
            return;
        }

        $user->forceFill(['role' => $activeRole])->save();
        $user->refresh();
    }
}
