<?php

namespace App\Http\Controllers\ClientDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user()->load('country:id,official_name');

        $countries = Country::query()
            ->select(['id', 'official_name'])
            ->orderBy('official_name')
            ->get()
            ->values()
            ->all();

        return Inertia::render('ClientDashboard/Profile/Edit', [
            'user' => $user,
            'countries' => $countries,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validated();
        $data['country_id'] = $user->hasRole('client') ? ($data['country_id'] ?? null) : null;

        if ($request->hasFile('avatar_image')) {
            if (
                $user->avatar_image &&
                $user->avatar_image !== 'default.png' &&
                Storage::disk('public')->exists($user->avatar_image)
            ) {
                Storage::disk('public')->delete($user->avatar_image);
            }

            $data['avatar_image'] = $request->file('avatar_image')->store('clients/avatars', 'public');
        } else {
            unset($data['avatar_image']);
        }

        $user->update($data);

        return redirect()
            ->route('client.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
