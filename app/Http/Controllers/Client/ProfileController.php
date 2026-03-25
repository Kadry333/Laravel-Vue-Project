<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
{
    $user = auth()->user();

    $countries = DB::table('lc_countries')
        ->orderBy('official_name')
        ->pluck('official_name')
        ->map(fn ($country) => (string) $country)
        ->values()
        ->all();

    return Inertia::render('Client/Profile/Edit', [
        'user' => $user,
        'countries' => $countries,
    ]);
}

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validated();

        if ($request->hasFile('avatar_image')) {
            if ($user->avatar_image && $user->avatar_image !== 'default.png') {
                Storage::disk('public')->delete($user->avatar_image);
            }

            $data['avatar_image'] = $request->file('avatar_image')->store('users', 'public');
        } else {
            unset($data['avatar_image']);
        }

        $user->update($data);

        return redirect()
            ->route('client.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}