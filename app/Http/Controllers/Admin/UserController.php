<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('reports')->get();

        return Inertia::render('Dashboard/Users/Index', [
            'users' => $users,
        ]);
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'ownership_number' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'national_id_number' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
        ]);

        $user = User::find($validated['id']);
        if (!$user) {
            return back()->withErrors(['user' => 'User not found']);
        }

        $user->update($validated);

        return Inertia::location(route('dashboard.users.index'));
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return back()->withErrors(['user' => 'User not found']);
        }

        $user->delete();

        return Inertia::location(route('dashboard.users.index'));
    }
}
