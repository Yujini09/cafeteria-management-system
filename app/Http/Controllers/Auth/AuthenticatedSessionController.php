<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use App\Models\AuditTrail;
use App\Models\Payment;
use App\Support\AuditDictionary;
use App\Services\NotificationService;

class AuthenticatedSessionController extends Controller
{
    // Show login (or redirect if already logged in)
    public function create(Request $request): View|RedirectResponse
    {
        $redirect = $this->normalizeRedirect($request->query('redirect'));
        if ($redirect) {
            $request->session()->put('login_redirect', $redirect);
        }

        if (Auth::check()) {
            $redirect = $this->normalizeRedirect($request->query('redirect') ?? $request->session()->pull('login_redirect'));
            if ($redirect) {
                return redirect($redirect);
            }

            if (in_array(Auth::user()->role, ['admin', 'superadmin'], true)) return redirect()->route('admin.dashboard');
            if (Auth::user()->role === 'customer') return redirect()->route('customer.homepage');

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

        if (in_array(Auth::user()->role, ['admin', 'superadmin'], true)) {
            $pendingPayments = Payment::where('status', 'submitted')->count();
            if ($pendingPayments > 0) {
                $notificationService = new NotificationService();
                $description = 'Payments awaiting review.';
                if (! $notificationService->notificationExists('payments_pending', 'payments', $description)) {
                    $notificationService->createAdminNotification(
                        'payments_pending',
                        'payments',
                        $description,
                        [
                            'pending_count' => $pendingPayments,
                            'url' => route('admin.payments.index'),
                        ]
                    );
                }
            }
        }

        if (in_array(Auth::user()->role, ['admin', 'superadmin'], true)) return redirect()->route('admin.dashboard');

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
}
