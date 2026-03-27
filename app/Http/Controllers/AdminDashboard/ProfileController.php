<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        abort_unless($user->hasAnyRole(['admin', 'manager', 'receptionist']), 403);

        return Inertia::render('AdminDashboard/Profile/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_image' => $user->avatar_image,
                'national_id' => $user->national_id,
                'primary_role' => $user->getRoleNames()->first(),
                'can_manage_avatar' => $user->hasAnyRole(['manager', 'receptionist']),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasAnyRole(['admin', 'manager', 'receptionist']), 403);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ];

        if ($user->hasAnyRole(['manager', 'receptionist'])) {
            $rules['national_id'] = [
                'nullable',
                'numeric',
                'digits:14',
                'starts_with:2,3',
                Rule::unique('users', 'national_id')->ignore($user->id),
            ];
            $rules['avatar_image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        $data = $request->validate($rules);

        if ($user->hasRole('admin')) {
            unset($data['national_id'], $data['avatar_image']);
        }

        if ($user->hasAnyRole(['manager', 'receptionist'])) {
            if ($request->hasFile('avatar_image')) {
                if (
                    filled($user->avatar_image) &&
                    $user->avatar_image !== 'default.png' &&
                    Storage::disk('public')->exists($user->avatar_image)
                ) {
                    Storage::disk('public')->delete($user->avatar_image);
                }

                $data['avatar_image'] = $request->file('avatar_image')->store('staff/avatars', 'public');
            } else {
                unset($data['avatar_image']);
            }
        }

        $user->update($data);

        return redirect()
            ->route('admins.profile.index')
            ->with('success', 'Profile updated successfully.');
    }
}
