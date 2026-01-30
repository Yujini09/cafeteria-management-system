<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $userRole = Auth::user()->role;

        // Allow superadmin to access admin routes
        if ($userRole !== $role && !($userRole === 'superadmin' && $role === 'admin')) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

