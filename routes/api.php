<?php

use App\Http\Controllers\Api\LicenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware([App\Http\Middleware\ForceJsonMiddleware::class])->group(function () {
    Route::post('/license/verify', [LicenseController::class, 'verify']);
    Route::get('/update/check', [App\Http\Controllers\Api\UpdateController::class, 'check']);
});
