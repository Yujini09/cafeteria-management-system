<?php

namespace App\Providers;

use App\Http\Middleware\RoleMiddleware;
use App\Models\ContactMessage;
use App\Models\Reservation;
use App\Services\InventoryAlertService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\Password;

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

        // Share sidebar badge counts for admin/superadmin.
        View::composer('layouts.sidebar', function ($view) {
            $unreadMessagesCount = 0;
            $pendingReservationsCount = 0;
            $inventoryWarningCount = 0;
            $user = auth()->user();
            $isAdminAreaUser = $user && in_array($user->role, ['admin', 'superadmin'], true);

            if ($isAdminAreaUser) {
                $unreadMessagesCount = ContactMessage::unreadCount();
            }

            if ($isAdminAreaUser && Schema::hasTable('reservations')) {
                $pendingReservationsCount = Reservation::query()
                    ->where('status', 'pending')
                    ->count();
            }

            if ($isAdminAreaUser) {
                $inventoryWarningCount = app(InventoryAlertService::class)->getWarningCount();
            }

            $view->with('sidebarUnreadMessagesCount', $unreadMessagesCount);
            $view->with('sidebarPendingReservationsCount', $pendingReservationsCount);
            $view->with('sidebarInventoryWarningCount', $inventoryWarningCount);
        });

        // Password rules: min 8, at least one number, one letter, one symbol (uppercase optional)
        Password::defaults(function () {
            return Password::min(8)->letters()->numbers()->symbols();
        });
    }
}
