<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/email/verify', function () {
    return inertia('auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

// email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

// Reports
Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/lost', [ReportController::class, 'lost'])->name('lost');
    Route::get('/stolen', [ReportController::class, 'stolen'])->name('stolen');

    Route::group(['prefix' => 'items'], function () {
        Route::post('/stolen', [ReportController::class, 'store'])->name('stolen.store');
        Route::post('/lost', [ReportController::class, 'store'])->name('lost.store');
    });

    Route::get('/status', [ReportController::class, 'status'])->name('status');
});

// Admin Dashboard
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'dashboard/users', 'as' => 'users.'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/edit', [UserController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'dashboard/reports', 'as' => 'reports.'], function () {
        Route::get('/', [ReportManagementController::class, 'index'])->name('index');
        Route::post('/edit', [ReportManagementController::class, 'edit'])->name('edit');
        Route::delete('/delete/{id}', [ReportManagementController::class, 'delete'])->name('delete');
    });
});

Auth::routes(['verify' => true]);
// Other includes
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
