<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manager\StoreManagerRequest;
use App\Http\Requests\Admin\Manager\UpdateManagerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ManagerController extends Controller
{

    public function index() {
        $filters = request()->only(['search', 'sort', 'direction']);
        $managers = User::role('manager')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->when($filters['sort'] ?? null, function ($query, $sort) use ($filters) {
                $direction = $filters['direction'] ?? 'asc';
                $query->orderBy($sort, $direction);
            })
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('AdminDashboard/Managers/Index', [
            'managers' => $managers,
            'filters' => $filters,
        ]);
    }

    public function edit($id)
    {
        $manager = User::role('manager')->findOrFail($id);
        return Inertia::render('AdminDashboard/Managers/Edit', [
            'manager' => $manager,
        ]);
    }

    public function update(UpdateManagerRequest $request, $id)
    {
        $manager = User::role('manager')->findOrFail($id);
        $data = $request->validated();

        if (!$request->filled('password')) {
            unset($data['password']);
        }
        if (!$request->filled('avatar_image')) {
            unset($data['avatar_image']);
        }

        $manager->update($data);

        if ($request->hasFile('avatar_image')) {
            if ($manager->avatar_image && Storage::disk('public')->exists($manager->avatar_image)) {
                Storage::disk('public')->delete($manager->avatar_image);
            }

            $file = $request->file('avatar_image');
            $filename = 'manager_' . $manager->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $manager->avatar_image = $path;
        }

        return redirect()->route('admins.managers.index')
            ->with('success', 'Manager updated successfully.');
    }




    public function destroy($id)
    {
        try {
            throw new \Exception("Error Processing Request", 1);

            $manager = User::role('manager')->findOrFail($id);

            if ($manager->avatar_image) {
                Storage::disk('public')->delete($manager->avatar_image);
            }

            $manager->delete();

            return back()->with('success', 'Manager deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while deleting the manager.');
        }
    }

    public function create()
    {
        return Inertia::render('AdminDashboard/Managers/Create');
    }

    public function show($id)
    {
        $manager = User::role('manager')->findOrFail($id);
        return Inertia::render('AdminDashboard/Managers/Show', [
            'manager' => $manager,
        ]);
    }

    public function store(StoreManagerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar_image')) {
            $file = $request->file('avatar_image');
            $filename = 'manager_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');
            $data['avatar_image'] = $path;
        }

        $manager = User::create($data);
        $manager->assignRole('manager');

        return redirect()->route('admins.managers.index')
            ->with('success', 'Manager created successfully.');
    }

}
