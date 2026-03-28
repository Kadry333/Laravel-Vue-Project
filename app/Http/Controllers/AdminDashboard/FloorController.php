<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Floor\StoreFloorRequest;
use App\Http\Requests\Admin\Floor\UpdateFloorRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Floor;
use Inertia\Inertia;

class FloorController extends Controller
{
    public function index() {
        $filters = request()->only(['search', 'sort', 'direction', 'min_floor', 'max_floor']);
        $user = auth()->user();
        $isAdmin = $user->getRoleNames()->contains('admin');
        $isManager = $user->getRoleNames()->contains('manager');


        $floors = Floor::query()->with('manager')->when($filters['search'] ?? null, function ($query, $search) use ($user, $isAdmin, $isManager) {
            $query->where(function ($q) use ($search, $user, $isAdmin, $isManager) {

                if ($isAdmin) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%")
                      ->orWhereHas('manager', function ($mq) use ($search) {
                          $mq->where('name', 'like', "%{$search}%");
                      });
                }

                if ($isManager) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                }
                });
            })
            ->when($filters['min_floor'] ?? null, function ($query, $min) {
                $query->where('id', '>=', $min);
            })
            ->when($filters['max_floor'] ?? null, function ($query, $max) {
                $query->where('id', '<=', $max);
            })
            ->when($filters['sort'] ?? null, function ($query, $sort) use ($filters) {
                $direction = $filters['direction'] ?? 'asc';
                $query->orderBy($sort, $direction);
            })
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('AdminDashboard/Floors/Index', [
            'floors' => $floors,
            'filters' => $filters,
        ]);
    }

    public function create() {
        $managers = User::role('manager')->get();
        return Inertia::render('AdminDashboard/Floors/Create', [
            'managers' => $managers
        ]);
    }

    public function store(StoreFloorRequest $request)
    {
        try {
            $data = $request->validated();
            $floor = Floor::create($data);

            return redirect()->route('admins.floors.index')
                ->with('success', 'Floor created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admins.floors.create')
                ->with('error', 'Something went wrong while creating the floor.');
        }
    }

    public function edit($id)
    {
        $floor = Floor::findOrFail($id);
        $managers = User::role('manager')->get();
        return Inertia::render('AdminDashboard/Floors/Edit', [
            'floor' => $floor,
            'managers' => $managers
        ]);
    }

    public function update(UpdateFloorRequest $request, $id)
    {
        $floor = Floor::findOrFail($id);
        $data = $request->validated();

        $floor->update($data);

        return redirect()->route('admins.floors.index')
            ->with('success', 'Floor updated successfully.');
    }

    public function show($id)
    {
        $floor = Floor::with('manager')->findOrFail($id);
        return Inertia::render('AdminDashboard/Floors/Show', [
            'floor' => $floor,
        ]);
    }

    public function destroy($id)
    {
        try {
            $floor = Floor::findOrFail($id);

            if ($floor->rooms()->exists()) {
                return back()->with('error', 'Cannot delete floor because it has rooms.');
            }

            $floor->delete();

            return redirect()->route('admins.floors.index')
                ->with('success', 'Floor deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while deleting the floor.');
        }
    }


}
