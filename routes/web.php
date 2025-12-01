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

// ---------- Dashboard redirect helper ----------
Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if (!$user) return redirect()->route('login');

    return match ($user->role) {
        'superadmin' => redirect()->route('admin.dashboard'), // Superadmin goes to admin dashboard
        'admin'      => redirect()->route('admin.dashboard'),
        default      => redirect()->route('customer.home'),
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
    Route::get('/homepage', [CustomerHomeController::class, 'index'])->name('customer.home');
});

Route::get('/menu', [MenuController::class, 'customerIndex'])->name('menu');

Route::get('/about', function () {
    return view('customer.about');
})->name('about');

Route::get('/contact', function () {
    return view('customer.contact');
})->name('contact');

// Group routes that require the user to be logged in (authenticated)
// Route::middleware(['auth'])->group(function () {
    // 1. Route for displaying the initial reservation form (GET)
    Route::get('/reservation_form', function () {
        // This renders the view that contains the missing route link
        return view('customer.reservation_form');
    })->name('reservation_form');

    // 2. Route for transitioning to the menu selection after basic reservation details are entered (GET/POST)
    // NOTE: This route is temporarily allowing GET for debugging, should ideally be POST.
    Route::match(['GET', 'POST'], '/reservation_form_menu', [ReservationController::class, 'create'])->name('reservation_form_menu');

    // 3. NEW FIX: Route for handling the final submission and storing the reservation (POST)
    Route::post('/reservation/store', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservation.store');

    // 4. Route for viewing reservation details (Assuming this view is correct)
    Route::get('/reservation/details', function () {
        return view('customer.reservation_details');
    })->name('reservation_details');

    // 5. Route for cancelling a reservation
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');
// });

    Route::get('/reservation_form_menu', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservation.store');
