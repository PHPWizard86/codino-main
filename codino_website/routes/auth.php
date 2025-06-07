<?php

use App\Http\Controllers\Auth\ApiAuthController; // Changed to new controller
use Illuminate\Support\Facades\Route;

// JWT Authentication Routes

Route::post('/register', [ApiAuthController::class, 'register'])->name('api.register');
Route::post('/login', [ApiAuthController::class, 'login'])->name('api.login');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout'])->name('api.logout');
    Route::post('/refresh', [ApiAuthController::class, 'refresh'])->name('api.refresh');
    Route::get('/me', [ApiAuthController::class, 'me'])->name('api.me');
    // Email verification routes might need adjustment or different controller if not handled by ApiAuthController
    // For now, keeping them out as Breeze ones were for web/Sanctum primarily.
    // Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    //                ->middleware(['throttle:6,1']) // Removed auth:sanctum as main middleware is auth:api
    //                ->name('verification.send');
});
