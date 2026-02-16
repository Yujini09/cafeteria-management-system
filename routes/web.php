<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\{
    MenuController, RecipeController, ReservationController, CalendarController, InventoryItemController, ReportsController, PaymentController, CustomerNotificationController
};
use App\Http\Controllers\Admin\MessageController;

// 1. PUBLIC MARKETING PAGES
Route::get('/', function () {
    return view('customer.homepage');
})->name('marketing.home');

// 2. EXPLICIT LOGIN ROUTE
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// ---------- Breeze auth routes ----------
require __DIR__ . '/auth.php';

// Google OAuth Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
});
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ---------- Dashboard redirect helper ----------
Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user) return redirect()->route('login');

    return match ($user->role) {
        'superadmin' => redirect()->route('admin.dashboard'),
        'admin'      => redirect()->route('admin.dashboard'),
        'customer'   => redirect()->route('customer.homepage'),
        default      => redirect()->route('login'),
    };
})->middleware(['auth'])->name('dashboard');

// ---------- Profile ----------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Superadmin ----------
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get   ('/users',             [SuperAdminController::class, 'index'])->name('users');
        Route::post  ('/users',             [SuperAdminController::class, 'store'])->name('users.store');
        Route::post  ('/users/check-email', [SuperAdminController::class, 'checkEmailRealtime'])->name('users.check-email');
        Route::put   ('/users/{user}',      [SuperAdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',      [SuperAdminController::class, 'destroy'])->name('users.destroy');
        Route::get   ('/users/{user}/audit',[SuperAdminController::class, 'audit'])->name('users.audit');
        Route::get   ('/recent-audits',     [SuperAdminController::class, 'recentAudits'])->name('recent-audits');
    });

// ---------- Admin/Superadmin Shared ----------
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/recent-notifications', [SuperAdminController::class, 'recentNotifications'])->name('recent-notifications');
        Route::post('/notifications/mark-all-read', [SuperAdminController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        Route::patch('/notifications/{notification}/read', [SuperAdminController::class, 'setNotificationRead'])->name('notifications.set-read');
    });

// ---------- Admin Only ----------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

        Route::resource('inventory', InventoryItemController::class);
        Route::get('/menus/prices', [MenuController::class,'prices'])->name('menus.prices');
        Route::post('/menus/prices', [MenuController::class,'updatePrices'])->name('menus.prices.update');
        Route::resource('menus', MenuController::class);
        Route::post('/menus/{menu}/items', [MenuController::class,'addItem'])->name('menus.items.store');

        Route::get   ('/menu-items/{menuItem}/recipes', [RecipeController::class,'index'])->name('recipes.index');
        Route::post  ('/menu-items/{menuItem}/recipes', [RecipeController::class,'store'])->name('recipes.store');
        Route::delete('/menu-items/{menuItem}/recipes/{recipe}', [RecipeController::class,'destroy'])->name('recipes.destroy');

        Route::get  ('/reservations',                       [ReservationController::class,'index'])->name('reservations');
        Route::get  ('/reservations/{reservation}',         [ReservationController::class,'show'])->name('reservations.show');
        Route::post ('/reservations/{reservation}/check-inventory', [ReservationController::class,'checkInventory'])->name('reservations.check-inventory');
        Route::patch('/reservations/{reservation}/approve', [ReservationController::class,'approve'])->name('reservations.approve');
        Route::patch('/reservations/{reservation}/decline', [ReservationController::class,'decline'])->name('reservations.decline');
        Route::post('/reservations/{reservation}/additionals', [ReservationController::class, 'storeAdditional'])->name('reservations.additionals.store');
        Route::patch('/reservations/{reservation}/additionals/{additional}', [ReservationController::class, 'updateAdditional'])->name('reservations.additionals.update');
        Route::delete('/reservations/{reservation}/additionals/{additional}', [ReservationController::class, 'deleteAdditional'])->name('reservations.additionals.destroy');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'showAdmin'])->name('payments.show');
        Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::patch('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export/pdf', [ReportsController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::post('/reports/export/excel', [ReportsController::class, 'exportExcel'])->name('reports.export.excel');

        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.delete');
    });

// ---------- Customer ----------
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/homepage', [CustomerHomeController::class, 'index'])->name('customer.homepage');

    Route::get('/customer/recent-notifications', [CustomerNotificationController::class, 'recent'])->name('customer.notifications.recent');
    Route::post('/customer/notifications/mark-all-read', [CustomerNotificationController::class, 'markAllRead'])->name('customer.notifications.mark-all-read');
    Route::patch('/customer/notifications/{notification}/read', [CustomerNotificationController::class, 'setRead'])->name('customer.notifications.set-read');

    Route::get('/customer/payment-due', [PaymentController::class, 'due'])->name('customer.payment-due');
    Route::get('/payments/{reservation}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{reservation}', [PaymentController::class, 'store'])->name('payments.store');
});

Route::get('/menu', [MenuController::class, 'customerIndex'])->name('menu');

Route::get('/about', function () {
    return view('customer.about');
})->name('about');

Route::get('/contact', function () {
    return view('customer.contact');
})->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Group routes that require the user to be logged in (authenticated)
Route::middleware(['auth'])->group(function () {
    // 1. Initial reservation form (GET)
    Route::get('/reservation_form', function () {
        return view('customer.reservation_form');
    })->name('reservation_form');

    Route::post('/reservation/step1', [ReservationController::class, 'postDetails'])->name('reservation.post_details');

    // 2. Menu selection
    Route::get('/reservation_form_menu', [ReservationController::class, 'create'])->name('reservation.create');

    // 3. Store reservation (POST)
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservation.store');

    // 4. View ALL reservations
    Route::get('/reservation_details', function () {
        $reservations = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('customer.reservation_details', compact('reservations'));
    })->name('reservation_details');

    // 5. View SINGLE reservation
    Route::get('/reservations/{id}', function ($id) {
        $reservation = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        return view('customer.reservation_view', compact('reservation'));
    })->name('reservation.view');

    // 5b. Edit reservation (pending only)
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservation.edit');

    // 6. Billing info
    Route::get('/billing_info/{id?}', function ($id = null) {
        return view('customer.billing_info', ['id' => $id]);
    })->name('billing_info');

    Route::post('/reservations/{reservation}/upload-receipt', [ReservationController::class, 'uploadReceipt'])->name('reservation.upload-receipt');

    // 7. Route for cancelling a reservation
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');
}); // THIS LINE CLOSES THE auth() GROUP

