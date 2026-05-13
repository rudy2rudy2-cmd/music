<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/download/{license_key}', [App\Http\Controllers\DownloadController::class, 'download'])->name('platform.download');

Route::middleware([
    'auth',
    \App\Http\Middleware\TwoFactorMiddleware::class,
])->group(function () {
    Route::get('/checkout/{platform_id}', [App\Http\Controllers\CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/{platform_id}/process', [App\Http\Controllers\PaymentController::class, 'process'])->name('checkout.process');
    Route::get('/payment/{platform_id}/success', [App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{platform_id}/success-paypal', [App\Http\Controllers\PaymentController::class, 'successPayPal'])->name('payment.success.paypal');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function() {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/2fa', [App\Http\Controllers\TwoFactorController::class, 'index'])->name('2fa.index');
    Route::post('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'verify'])->name('2fa.verify');
});
