<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'country' => 'required|string|max:255',
            'ownership_number' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'national_id_number' => 'required|string|max:255',
            'product_type' => 'required|string|max:255',
        ]);

        $user = User::create($data);

        // Generate the signed URL for email verification
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );
        $user->notify(new VerifyEmail($verificationUrl));

        // event(new Registered($user));
        return redirect()->back()->with('success', 'User created and verification email sent.');
    }
}
