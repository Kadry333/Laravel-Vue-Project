<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'national_id' => 'required|string|unique:users,national_id',
            'avatar_image' => 'nullable|image|mimes:jpg,jpeg|max:2048',
            'country' => 'required|string',
            'gender' => 'required|string|in:male,female',
        ]);

        // Handle Avatar Upload
        $avatarName = 'default.png';
        if ($request->hasFile('avatar_image')) {
            $avatarName = time() . '.' . $request->avatar_image->extension();
            $request->avatar_image->move(public_path('avatars'), $avatarName);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'national_id' => $request->national_id,
            'avatar_image' => $avatarName,
            'country' => $request->country,
            'gender' => $request->gender,
            'is_approved' => false,
        ]);

        // Assign 'client' role
        $user->assignRole('client');

        event(new Registered($user));

        // Disable automatic login for pending approval
        // Auth::login($user);

        return redirect(route('login'))->with('status', 'Your account has been registered. Please wait for an administrator to approve your account before logging in.');
    }
}
