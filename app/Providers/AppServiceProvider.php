<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Validation\Rules\Password;
use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register 'role' middleware alias so routes/controllers can use 'role:admin' etc.
        if (class_exists(Router::class)) {
            $router = $this->app->make(Router::class);
            $router->aliasMiddleware('role', RoleMiddleware::class);
        }

        // Password rules: min 8, at least one number, one letter, one symbol (uppercase optional)
        Password::defaults(function () {
            return Password::min(8)->letters()->numbers()->symbols();
        });
    }
}
