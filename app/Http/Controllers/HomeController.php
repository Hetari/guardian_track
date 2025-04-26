<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Welcome', [
            'is_authenticated' => $user ? true : false,
        ]);
    }
}
