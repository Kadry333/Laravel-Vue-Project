<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Cache;
use App\Models\Country;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
            'user' => fn () => $request->user()
                ? $request->user()->load('roles')->toArray()
                : null,
                'user' => $user,
                'primary_role' => $user ? $this->resolvePrimaryRole($user) : null,
                'role_label' => $user ? ucfirst($this->resolvePrimaryRole($user)) : null,
                'profile_route' => $user ? $this->resolveProfileRoute($user) : null,
            ],
            // laravel cache to cache the drop down list of countries
            'countries' => Cache::remember('all_countries', 86400, function () {
                return Country::orderBy('official_name')->pluck('official_name')->toArray();
            }),
            'flash' => [
                'payment_success' => fn() => $request->session()->get('payment_success'),
                'payment_cancelled' => fn() => $request->session()->get('payment_cancelled'),
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
        ];
    }

    private function resolvePrimaryRole(User $user): string
    {
        foreach (['admin', 'manager', 'receptionist', 'client'] as $role) {
            if ($user->hasRole($role)) {
                return $role;
            }
        }

        return 'user';
    }

    private function resolveProfileRoute(User $user): string
    {
        return $user->hasRole('client')
            ? route('client.profile.edit', absolute: false)
            : route('admins.profile.index', absolute: false);
    }
}
