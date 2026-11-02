<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierPanelController;
use App\Http\Controllers\PlanetBanController;
use App\Http\Controllers\ServiceQueueDisplayController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/account', [AuthController::class, 'dashboard'])->name('account.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================================
// AREA USER (Publik: Beranda, Katalog, Booking)
// ==========================================
Route::get('/', [PlanetBanController::class, 'index']);
Route::get('/home', [PlanetBanController::class, 'index'])->name('home');
Route::get('/product/{id}', [PlanetBanController::class, 'showProduct'])->name('product.show');

Route::post('/booking', [PlanetBanController::class, 'storeBooking'])
    ->middleware('auth')
    ->name('booking.store');
Route::get('/booking', function () {
    return redirect('/home');
});
Route::get('/booking/status', [PlanetBanController::class, 'bookingStatus'])->name('booking.status');
Route::get('/antrean', ServiceQueueDisplayController::class)->name('queues.display');

// Tambahkan rute ini di Area User (routes/web.php)
Route::get('/stores', [PlanetBanController::class, 'storeLocations'])->name('stores.index');

// Halaman Informasi Footer
Route::get('/tentang-kami', [PlanetBanController::class, 'aboutUs'])->name('about');
Route::get('/faq', [PlanetBanController::class, 'faq'])->name('faq');
Route::get('/blog', [PlanetBanController::class, 'blog'])->name('blog');
Route::get('/karir', [PlanetBanController::class, 'career'])->name('career');
Route::get('/kebijakan-privasi', [PlanetBanController::class, 'privacyPolicy'])->name('privacy');
Route::get('/kebijakan-hak-cipta', [PlanetBanController::class, 'copyrightPolicy'])->name('copyright');
Route::get('/syarat-ketentuan', [PlanetBanController::class, 'terms'])->name('terms');

// ==========================================
// AREA KHUSUS ADMIN (Manajemen & Auth)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginAdmin'])
        ->middleware('throttle:6,1')
        ->name('login.submit');
    Route::redirect('/register', '/admin/login')->name('register');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
        Route::post('/bookings/{booking}/update', [AdminDashboardController::class, 'updateBookingStatus'])
            ->name('bookings.update');
    });
});

Route::prefix('cashir')->name('cashir.')->middleware(['auth', 'cashier'])->group(function () {
    Route::get('/', fn () => redirect()->route('cashir.dashboard'))->name('home');
    Route::get('/dashboard', [CashierPanelController::class, 'index'])->name('dashboard');
    Route::post('/pos', [CashierPanelController::class, 'storePointOfSale'])
        ->name('pos.store');
    Route::post('/bookings/{booking}/process', [CashierPanelController::class, 'updateBooking'])
        ->name('bookings.process');
    Route::post('/queues/{serviceQueue}/status', [CashierPanelController::class, 'updateQueueStatus'])
        ->name('queues.status');
    Route::get('/bookings/{booking}/receipt', [CashierPanelController::class, 'receipt'])
        ->name('bookings.receipt');
});
