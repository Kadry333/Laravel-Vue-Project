<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mobile' => 'required|string|regex:/^[+]?[0-9]{10,15}$/|unique:users,mobile',
            'avatar_image' => 'nullable|image|mimes:jpg,jpeg|max:2048',
            'country' => 'required|string|exists:lc_countries,official_name',
            'gender' => 'required|string|in:male,female',
        ]);

        // Find country by name to get ID
        $country = Country::where('official_name', $request->country)->first();
        $countryId = $country ? $country->id : null;

        // Store avatar image in storage/app/public/clients/avatars
        $avatarName = 'default.png';
        if ($request->hasFile('avatar_image')) {
            $file = $request->file('avatar_image');

            $filename = time() . '_' . $file->getClientOriginalName();
            $avatarName = $file->storeAs('clients/avatars', $filename, 'public');
            $data['avatar_image'] = $avatarName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile' => $request->mobile,
            'avatar_image' => $avatarName,
            'country_id' => $countryId,
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
