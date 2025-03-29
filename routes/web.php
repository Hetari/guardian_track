<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $user = auth()->user();
    return Inertia::render('Welcome', [
        'is_authenticated' => $user ? true : false,
    ]);
})->name('home');

Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'reports', 'as' => 'reports.'], function () {
    Route::get('/', function () {
        return Inertia::render('Reports/Index');
    })->name('index');

    Route::get('/lost', function () {
        return Inertia::render("Reports/Lost");
    })->name('lost');
    Route::get('/stolen', function () {
        return Inertia::render("Reports/Stolen");
    })->name('stolen');


    Route::group(['prefix' => 'items'], function () {
        Route::post('/stolen', [ReportController::class, 'store'])->name('stolen.store');
        Route::post('/lost', [ReportController::class, 'store'])->name('lost.store');
    });

    Route::get('/status', function () {
        return Inertia::render('Reports/Status');
    })->name('status');
});

// Admin routes
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
