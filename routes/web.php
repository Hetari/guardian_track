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
        $status = [
            'received' => 'received',
            'checking_brand' => 'checking brand',
            'checking_company' => 'checking company',
            'transferred_to_police' => 'transferred to police',
            'done' => 'done',
        ];
        $reports = auth()->user()
            ->reports()
            ->select(
                'id',
                'type',
                'product_name',
                'serial_code',
                'date_time',
                'country',
                'city',
                'street_address',
                'purchase_location',
                'item_type',
                'status'
            )
            ->get();
        return Inertia::render('Reports/Status', [
            'reports' => $reports,
            'statuses' => $status,
        ]);
    })->name('status');
});

// Admin routes
Route::group(['middleware' => ['auth', 'verified', 'admin']], function () {
    Route::get('dashboard', function () {
        $users_count = \App\Models\User::count();
        $reports_count = \App\Models\Report::count();
        return Inertia::render('Dashboard/Index', [
            'users_count' => $users_count,
            'reports_count' => $reports_count,
        ]);
    })->name('dashboard');

    Route::group(['prefix' => 'dashboard/users', 'as' => 'users'], function () {
        Route::get('/', function () {
            $users = \App\Models\User::withCount('reports')->get();
            return Inertia::render('Dashboard/Users', [
                'users' => $users,
            ]);
        })->name('');

        Route::post('/edit', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:255',
            ]);

            $user = \App\Models\User::find($validated['id']);
            if (!$user) {
                return redirect()->back()->withErrors(['user' => 'User not found']);
            }
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->save();

            return Inertia::location(route('users'));
        })->name('users.edit');

        Route::delete('/delete/{id}', function ($id) {
            $user = \App\Models\User::find($id);
            if (!$user) {
                return redirect()->back()->withErrors(['user' => 'User not found']);
            }
            $user->delete();
            return Inertia::location(route('users'));
        })->name('users.delete');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
