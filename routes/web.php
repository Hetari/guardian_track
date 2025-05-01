
<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportManagementController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Reports
Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/lost', [ReportController::class, 'lost'])->name('lost');
    Route::get('/stolen', [ReportController::class, 'stolen'])->name('stolen');
    Route::get('/submitted/{tracking_code}', [ReportController::class, 'submitted'])->name('submitted');

    Route::group(['prefix' => 'items'], function () {
        Route::post('/stolen', [ReportController::class, 'store'])->name('stolen.store');
        Route::post('/lost', [ReportController::class, 'store'])->name('lost.store');
    });

    Route::get('/status', [ReportController::class, 'status'])->name('status');
});

// Admin Dashboard
Route::group(['middleware' => ['auth', 'verified', 'admin'], 'as' => 'dashboard.'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('index');

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

// Other includes
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
