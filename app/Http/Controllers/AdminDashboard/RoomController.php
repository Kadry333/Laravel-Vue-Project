<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Cores\General\RepositoryInterfaces\FloorRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\User;
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

    private function isAdmin(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    private function managers()
    {
        return User::role('manager')->select('id', 'name')->get();
    }

    public function index()
    {
        $filters = request()->only(['search', 'sort', 'direction']);

        return Inertia::render('AdminDashboard/Rooms/Index', [
            'rooms'   => $this->roomRepository->paginate(10, ['floor', 'manager'], [], $filters),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $data = ['floors' => $this->floorRepository->get()];

        if ($this->isAdmin()) {
            $data['managers'] = $this->managers();
        }

        return Inertia::render('AdminDashboard/Rooms/Create', $data);
    }

    public function store(StoreRoomRequest $request)
    {
        $managerId = $this->isAdmin() ? $request->manager_id : auth()->id();

        $this->roomRepository->store([
            'number'     => $request->number,
            'capacity'   => $request->capacity,
            'price'      => $request->price,
            'floor_id'   => $request->floor_id,
            'manager_id' => $managerId,
        ]);

        return redirect()->route('admins.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function edit($id)
    {
        $room = $this->findAndAuthorizeRoom($id, ['floor']);

        $data = [
            'room'   => $room,
            'floors' => $this->floorRepository->get(),
        ];

        if ($this->isAdmin()) {
            $data['managers'] = $this->managers();
        }

        return Inertia::render('AdminDashboard/Rooms/Edit', $data);
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $room = $this->findAndAuthorizeRoom($id);

        $data = [
            'number'   => $request->number,
            'capacity' => $request->capacity,
            'price'    => $request->price,
            'floor_id' => $request->floor_id,
        ];

        if ($this->isAdmin() && $request->manager_id) {
            $data['manager_id'] = $request->manager_id;
        }

        $this->roomRepository->update($room->id, $data);

        return redirect()->route('admins.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy($id)
    {
        $room = $this->findAndAuthorizeRoom($id, ['reservations']);

        if ($room->reservations && $room->reservations->isNotEmpty()) {
            return back()->with('error', 'Cannot delete a room that is currently reserved.');
        }

        $this->roomRepository->delete(['id' => $room->id]);

        return back()->with('success', 'Room deleted successfully.');
    }


    private function findAndAuthorizeRoom($id, $relations = [])
    {
        $room = $this->roomRepository->find($id, $relations);


        if (!$room) {
            abort(404, 'Room not found');
        }


        if (!$this->isAdmin() && $room->manager_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return $room;
    }
}
