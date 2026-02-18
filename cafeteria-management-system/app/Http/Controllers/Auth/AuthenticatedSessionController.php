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

class AuthenticatedSessionController extends Controller
{
    // Show login (or redirect if already logged in)
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'superadmin') return redirect()->route('superadmin.users');
            if (Auth::user()->role === 'admin')      return redirect()->route('admin.dashboard');
            if (Auth::user()->role === 'customer')   return redirect()->route('customer.home');

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
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action'  => 'Logged in',
            'module'  => 'auth',
            'description' => 'logged in',
        ]);

        if (Auth::user()->role === 'superadmin') return redirect()->route('superadmin.users');
        if (Auth::user()->role === 'admin')      return redirect()->route('admin.dashboard');

        return redirect()->route('customer.home');
    }

    // Logout
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Log logout action before session ends
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action'  => 'Logged out',
            'module'  => 'auth',
            'description' => 'logged out',
        ]);

        Auth::guard('web')->logout();

    Session::invalidate();
    Session::regenerateToken();

        return redirect('/');
    }
}
