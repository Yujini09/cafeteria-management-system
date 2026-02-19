<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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

        // Share sidebar badge counts for admin/superadmin.
        View::composer('layouts.sidebar', function ($view) {
            $unreadMessagesCount = 0;
            $inventoryWarningCount = 0;
            $user = auth()->user();
            $isAdminAreaUser = $user && in_array($user->role, ['admin', 'superadmin'], true);

            if ($isAdminAreaUser && Schema::hasTable('contact_messages')) {
                $unreadMessagesCount = ContactMessage::where('is_read', false)->count();
            }

            if ($isAdminAreaUser && Schema::hasTable('inventory_items')) {
                $today = Carbon::today();
                $expiryWindowEnd = Carbon::today()->addDays(7);

                $inventoryWarningCount = InventoryItem::query()
                    ->where(function ($query) use ($today, $expiryWindowEnd) {
                        $query
                            ->where('qty', 0)
                            ->orWhere(function ($lowStockQuery) {
                                $lowStockQuery->where('qty', '>', 0)->where('qty', '<=', 5);
                            })
                            ->orWhere(function ($expiringQuery) use ($today, $expiryWindowEnd) {
                                $expiringQuery
                                    ->whereNotNull('expiry_date')
                                    ->whereDate('expiry_date', '>=', $today)
                                    ->whereDate('expiry_date', '<=', $expiryWindowEnd);
                            });
                    })
                    ->count();
            }

            $view->with('sidebarUnreadMessagesCount', $unreadMessagesCount);
            $view->with('sidebarInventoryWarningCount', $inventoryWarningCount);
        });

        // Password rules: min 8, at least one number, one letter, one symbol (uppercase optional)
        Password::defaults(function () {
            return Password::min(8)->letters()->numbers()->symbols();
        });
    }
}
