<?php

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
        return Inertia::render('Reports');
    })->name('index');
});

// Admin routes
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
