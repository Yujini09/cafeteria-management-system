<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $routeName = $request->route()?->getName();
            $allowedRoutes = [
                'profile.edit',
                'profile.update',
                'profile.destroy',
                'password.update',
                'password.check-current',
                'password.confirm',
                'logout',
            ];

            if ($routeName && in_array($routeName, $allowedRoutes, true)) {
                return $next($request);
            }

            if ($request->is('profile') || $request->is('password') || $request->is('password/*')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Password change required.',
                ], 403);
            }

            return redirect()
                ->route('profile.edit')
                ->with('warning', 'Please change your password to continue using the system.');
        }

        return $next($request);
    }
}
