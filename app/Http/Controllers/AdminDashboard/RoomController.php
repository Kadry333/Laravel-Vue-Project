<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Cores\General\RepositoryInterfaces\FloorRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
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
        return Inertia::render('AdminDashboard/Rooms/Index', [
            'rooms'  => $this->roomRepository->get(['floor']),
            'floors' => $this->floorRepository->get(),
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        $managerId = 1;
        $this->roomRepository->store([
            'number'     => $request->number,
            'capacity'   => $request->capacity,
            'price'      => $request->price * 100,
            'floor_id'   => $request->floor_id,
            'manager_id' => $managerId,
        ]);

        return back();
    }

    public function update(UpdateRoomRequest $request, $id)
    {
        $this->roomRepository->update($id, [
            'number'   => $request->number,
            'capacity' => $request->capacity,
            'price'    => $request->price * 100,
            'floor_id' => $request->floor_id,
        ]);

        return back();
    }

    public function destroy($id)
    {
        $this->roomRepository->delete($id);
        return back();
    }
}
