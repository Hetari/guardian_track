<?php

use App\Http\Controllers\TrackingController;
use Illuminate\Routing\Route;

Route::post('/save-tracking', [TrackingController::class, 'store']);

Route::get('/reports/live-tracking/{trackingCode}/location', [TrackingController::class, 'latestLocation']);
