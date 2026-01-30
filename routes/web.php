<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerHomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\{
    MenuController, RecipeController, ReservationController, CalendarController, InventoryItemController, ReportsController
};
use App\Models\Menu;

// 1. PUBLIC MARKETING PAGES (No authentication required)
Route::get('/', function () {
    return view('customer.homepage');
})->name('marketing.home');

// 2. EXPLICIT LOGIN ROUTE (WAS '/' BEFORE)
// Users click the 'Reserve' button, which points here.
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// ---------- Breeze auth routes (login, register, logout, password, etc.) ----------
require __DIR__ . '/auth.php';

// Google OAuth Routes
// Note: Only the redirect needs 'guest' middleware. Callback must NOT be in guest middleware
// because Socialite needs session access to validate the state parameter
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
});

// Callback route WITHOUT guest middleware - allows Socialite to access session for state validation
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');


// ---------- Dashboard redirect helper ----------
Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user) return redirect()->route('login');

    return match ($user->role) {
        'superadmin' => redirect()->route('admin.dashboard'), // Superadmin goes to admin dashboard
        'admin'      => redirect()->route('admin.dashboard'),
        default      => redirect()->route('customer.homepage'),
    };
})->middleware(['auth'])->name('dashboard');

// ---------- Profile (Account Settings) ----------
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
        Route::get   ('/users',            [SuperAdminController::class, 'index'])->name('users');
        Route::post  ('/users',            [SuperAdminController::class, 'store'])->name('users.store');
        Route::put   ('/users/{user}',     [SuperAdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',     [SuperAdminController::class, 'destroy'])->name('users.destroy');
        Route::get   ('/users/{user}/audit',[SuperAdminController::class, 'audit'])->name('users.audit');
        Route::get   ('/recent-audits',    [SuperAdminController::class, 'recentAudits'])->name('recent-audits');
    });

// ---------- Admin and Superadmin shared routes ----------
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/recent-notifications', [SuperAdminController::class, 'recentNotifications'])->name('recent-notifications');
        Route::post('/notifications/mark-all-read', [SuperAdminController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
        Route::patch('/notifications/{notification}/read', [SuperAdminController::class, 'setNotificationRead'])->name('notifications.set-read');
    });

// ---------- Admin only routes ----------
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

        // Recipes
        Route::get   ('/menu-items/{menuItem}/recipes', [RecipeController::class,'index'])->name('recipes.index');
        Route::post  ('/menu-items/{menuItem}/recipes', [RecipeController::class,'store'])->name('recipes.store');
        Route::delete('/menu-items/{menuItem}/recipes/{recipe}', [RecipeController::class,'destroy'])->name('recipes.destroy');

        // Reservations (names align with your Blade: admin.reservations, admin.reservations.show, etc.)
        Route::get  ('/reservations',                       [ReservationController::class,'index'])->name('reservations');
        Route::get  ('/reservations/{reservation}',         [ReservationController::class,'show'])->name('reservations.show');
        Route::post ('/reservations/{reservation}/check-inventory', [ReservationController::class,'checkInventory'])->name('reservations.check-inventory');
        Route::patch('/reservations/{reservation}/approve', [ReservationController::class,'approve'])->name('reservations.approve');
        Route::patch('/reservations/{reservation}/decline', [ReservationController::class,'decline'])->name('reservations.decline');

        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/export/pdf', [ReportsController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::post('/reports/export/excel', [ReportsController::class, 'exportExcel'])->name('reports.export.excel');
    });



// ---------- Customer ----------
// Customer

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/homepage', [CustomerHomeController::class, 'index'])->name('customer.homepage');
});

Route::get('/menu', [MenuController::class, 'customerIndex'])->name('menu');

Route::get('/about', function () {
    return view('customer.about');
})->name('about');

Route::get('/contact', function () {
    return view('customer.contact');
})->name('contact');

// Group routes that require the user to be logged in (authenticated)
Route::middleware(['auth'])->group(function () {
    // 1. Route for displaying the initial reservation form (GET)
    Route::get('/reservation_form', function () {
        return view('customer.reservation_form');
    })->name('reservation_form');

    // 2. Route for transitioning to the menu selection after basic reservation details are entered
    Route::get('/reservation_form_menu', [ReservationController::class, 'create'])->name('reservation.create');

    // 3. Route for handling the final submission and storing the reservation (POST)
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservation.store');

    // 4. Route for viewing ALL reservation details (list of all reservations)
    Route::get('/reservation_details', function () {
        $reservations = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('customer.reservation_details', compact('reservations'));
    })->name('reservation_details');

    // 5. Route for viewing a SINGLE reservation detail
    Route::get('/reservations/{id}', function ($id) {
        $reservation = \App\Models\Reservation::with(['items.menu.items'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);
        
        return view('customer.reservation_view', compact('reservation'));
    })->name('reservation.view');

    // 6. Route for viewing billing info/receipt
    Route::get('/billing_info/{id?}', function ($id = null) {
        return view('customer.billing_info', ['id' => $id]);
    })->name('billing_info');

    Route::post('/reservations/{reservation}/upload-receipt', [ReservationController::class, 'uploadReceipt'])->name('reservation.upload-receipt');
    
    // 7. Route for cancelling a reservation
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');
}); // THIS LINE CLOSES THE auth() GROUP

// In routes/web.php (temporary) - OUTSIDE the auth group since it's for debugging
Route::get('/debug-reservation/{id}', function($id) {
    $reservation = \App\Models\Reservation::with(['items.menu'])->find($id);
    
    if (!$reservation) {
        return "Reservation not found";
    }
    
    echo "<h1>Reservation #{$reservation->id}</h1>";
    echo "<h2>Items:</h2>";
    
    if ($reservation->items->count() > 0) {
        foreach ($reservation->items as $item) {
            echo "Item ID: {$item->id}<br>";
            echo "Menu ID: {$item->menu_id}<br>";
            echo "Menu Name: " . ($item->menu ? $item->menu->name : 'NOT FOUND') . "<br>";
            echo "Quantity: {$item->quantity}<br>";
            echo "Meal Time: {$item->meal_time}<br>";
            echo "Day Number: {$item->day_number}<br>";
            echo "<hr>";
        }
    } else {
        echo "No items found";
    }
    
    echo "<h2>Raw Data:</h2>";
    dd($reservation->toArray());
});