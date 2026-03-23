<?php

namespace App\Http\Controllers\ClientDashboard;


use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RoomController extends Controller
{
    private RoomRepositoryInterface $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function index()
    {
        $availableRooms = $this->roomRepository->getCurrentlyAvailableRooms(['floor']);

        return Inertia::render('ClientDashboard/MakeReservation/Rooms', ['rooms' => $availableRooms]);
    }
}
