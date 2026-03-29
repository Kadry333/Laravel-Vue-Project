<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Cores\General\RepositoryInterfaces\FloorRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RoomController extends Controller
{
    private RoomRepositoryInterface $roomRepository;
    private FloorRepositoryInterface $floorRepository;

    public function __construct(
        RoomRepositoryInterface $roomRepository,
        FloorRepositoryInterface $floorRepository,
    ) {
        $this->roomRepository  = $roomRepository;
        $this->floorRepository = $floorRepository;
    }

    public function index()
    {
        $filters = request()->only(['search', 'sort', 'direction']);

        return Inertia::render('AdminDashboard/Rooms/Index', [
            'rooms'   => $this->roomRepository->paginate(
                5,
                ['floor', 'manager'],
                [],
                $filters
            ),
            'filters' => $filters,
            'floors'  => $this->floorRepository->get(),
            'authUser' => [
                'id'   => auth()->id(),
                'role' => auth()->user()->getRoleNames()->first(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('AdminDashboard/Rooms/Create', [
            'floors' => $this->floorRepository->get(),
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('AdminDashboard/Rooms/Edit', [
            'room'   => $this->roomRepository->find($id, ['floor']),
            'floors' => $this->floorRepository->get(),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        $this->roomRepository->store([
            'number'     => $request->number,
            'capacity'   => $request->capacity,
            'price'      => $request->price, // store in cents
            'floor_id'   => $request->floor_id,
            'manager_id' => auth()->id(),
        ]);

        return redirect()->route('admins.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $this->roomRepository->update($id, [
            'number'   => $request->number,
            'capacity' => $request->capacity,
            'price'    => $request->price, // store in cents
            'floor_id' => $request->floor_id,
        ]);

        return redirect()->route('admins.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy($id)
    {
        $room = $this->roomRepository->find($id, ['reservations']);

        
        if ($room->reservations && $room->reservations->isNotEmpty()) {
            return back()->with('error', 'Cannot delete a room that is currently reserved.');
        }

        $this->roomRepository->delete(['id' => $id]);

        return back()->with('success', 'Room deleted successfully.');
    }
}
