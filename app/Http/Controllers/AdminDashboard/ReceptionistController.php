<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreReceptionistRequest;
use App\Http\Requests\Admin\UpdateReceptionistRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ReceptionistController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $filters = [
            'search' => trim((string) $request->string('search')),
            'sort' => $request->string('sort')->toString() ?: 'created_at',
            'direction' => $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc',
        ];

        $allowedSorts = [
            'name' => 'name',
            'email' => 'email',
            'created_at' => 'created_at',
            'banned_at' => 'banned_at',
        ];

        $sortColumn = $allowedSorts[$filters['sort']] ?? 'created_at';

        $baseQuery = $this->receptionistsQuery()
            ->when($filters['search'] !== '', function (Builder $query) use ($filters) {
                $query->where(function (Builder $nestedQuery) use ($filters) {
                    $nestedQuery
                        ->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                });
            });

        $statsQuery = clone $baseQuery;

        $receptionists = $baseQuery
            ->orderBy($sortColumn, $filters['direction'])
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $receptionist) => $this->serializeReceptionist($receptionist, $user));

        return Inertia::render('AdminDashboard/ManageReceptionists/Index', [
            'receptionists' => $receptionists,
            'filters' => $filters,
            'permissions' => [
                'show_manager_column' => $user->hasRole('admin'),
                'can_create' => $user->hasAnyRole(['admin', 'manager']),
            ],
            'stats' => [
                'total' => (clone $statsQuery)->count(),
                'active' => (clone $statsQuery)->whereNull('banned_at')->count(),
                'banned' => (clone $statsQuery)->whereNotNull('banned_at')->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorizeCreate($request->user());

        return Inertia::render('AdminDashboard/ManageReceptionists/Create');
    }

    public function store(StoreReceptionistRequest $request): RedirectResponse
    {
        $this->authorizeCreate($request->user());

        $data = $request->validated();
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'national_id' => $data['national_id'] ?? null,
            'avatar_image' => null,
            'is_approved' => true,
        ];

        if (Schema::hasColumn('users', 'created_by')) {
            $payload['created_by'] = $request->user()->id;
        }

        if ($request->hasFile('avatar_image')) {
            $payload['avatar_image'] = $request->file('avatar_image')->store('staff/avatars', 'public');
        }

        $receptionist = User::create($payload);
        $receptionist->assignRole('receptionist');

        return redirect()
            ->route('admins.receptionists.index')
            ->with('success', 'Receptionist created successfully.');
    }

    public function edit(Request $request, User $receptionist): Response
    {
        $receptionist = $this->resolveReceptionist($receptionist);
        $this->authorizeManage($request->user(), $receptionist);

        return Inertia::render('AdminDashboard/ManageReceptionists/Edit', [
            'receptionist' => $this->serializeReceptionistForm($receptionist),
        ]);
    }

    public function update(UpdateReceptionistRequest $request, User $receptionist): RedirectResponse
    {
        $receptionist = $this->resolveReceptionist($receptionist);
        $this->authorizeManage($request->user(), $receptionist);

        $data = $request->validated();
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'national_id' => $data['national_id'] ?? null,
        ];

        if ($request->hasFile('avatar_image')) {
            $this->deleteAvatarIfExists($receptionist);
            $payload['avatar_image'] = $request->file('avatar_image')->store('staff/avatars', 'public');
        }

        $receptionist->update($payload);

        return redirect()
            ->route('admins.receptionists.index')
            ->with('success', 'Receptionist updated successfully.');
    }

    public function destroy(Request $request, User $receptionist): RedirectResponse
    {
        $receptionist = $this->resolveReceptionist($receptionist);
        $this->authorizeManage($request->user(), $receptionist);

        $this->deleteAvatarIfExists($receptionist);
        $receptionist->delete();

        return redirect()
            ->route('admins.receptionists.index')
            ->with('success', 'Receptionist deleted successfully.');
    }

    public function ban(Request $request, User $receptionist): RedirectResponse
    {
        $receptionist = $this->resolveReceptionist($receptionist);
        $this->authorizeManage($request->user(), $receptionist);

        if (! $receptionist->isBanned()) {
            $receptionist->ban();
        }

        $this->syncLegacyBanState($receptionist, true);

        return back()->with('success', 'Receptionist banned successfully.');
    }

    public function unban(Request $request, User $receptionist): RedirectResponse
    {
        $receptionist = $this->resolveReceptionist($receptionist);
        $this->authorizeManage($request->user(), $receptionist);

        if ($receptionist->isBanned()) {
            $receptionist->unban();
        }

        $this->syncLegacyBanState($receptionist, false);

        return back()->with('success', 'Receptionist unbanned successfully.');
    }

    private function receptionistsQuery(): Builder
    {
        return User::query()
            ->withBanned()
            ->role('receptionist')
            ->with(['creator:id,name']);
    }

    private function resolveReceptionist(User $receptionist): User
    {
        abort_unless($receptionist->hasRole('receptionist'), 404);

        return $receptionist->loadMissing('creator:id,name');
    }

    private function authorizeCreate(User $user): void
    {
        abort_unless($user->hasAnyRole(['admin', 'manager']), 403);
    }

    private function authorizeManage(User $user, User $receptionist): void
    {
        if ($user->hasRole('admin')) {
            return;
        }

        abort_unless(
            $user->hasRole('manager') && (int) $receptionist->created_by === (int) $user->id,
            403
        );
    }

    private function serializeReceptionist(User $receptionist, User $authUser): array
    {
        return [
            'id' => $receptionist->id,
            'name' => $receptionist->name,
            'email' => $receptionist->email,
            'national_id' => $receptionist->national_id,
            'avatar_image' => $receptionist->avatar_image,
            'manager_name' => $receptionist->creator?->name,
            'created_at' => optional($receptionist->created_at)->toDateString(),
            'is_banned' => $receptionist->isCurrentlyBanned(),
            'status_label' => $receptionist->isCurrentlyBanned() ? 'Banned' : 'Active',
            'can_manage' => $authUser->hasRole('admin') || (int) $receptionist->created_by === (int) $authUser->id,
        ];
    }

    private function serializeReceptionistForm(User $receptionist): array
    {
        return [
            'id' => $receptionist->id,
            'name' => $receptionist->name,
            'email' => $receptionist->email,
            'national_id' => $receptionist->national_id,
            'avatar_image' => $receptionist->avatar_image,
        ];
    }

    private function deleteAvatarIfExists(User $receptionist): void
    {
        if (
            filled($receptionist->avatar_image) &&
            $receptionist->avatar_image !== 'default.png' &&
            Storage::disk('public')->exists($receptionist->avatar_image)
        ) {
            Storage::disk('public')->delete($receptionist->avatar_image);
        }
    }

    private function syncLegacyBanState(User $receptionist, bool $isBanned): void
    {
        $receptionist->forceFill([
            'is_banned' => $isBanned,
            'banned_at' => $isBanned ? now() : null,
        ])->saveQuietly();
    }
}
