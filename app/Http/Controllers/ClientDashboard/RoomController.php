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
        $rooms = $this->roomRepository->getRoomsWithReservations(['floor']);

        $rooms->transform(function ($room) {
            $room->blocked_ranges = $room->reservations
                ->map(fn($r) => [
                    'start' => $r->check_in_date,
                    'end'   => $r->check_out_date,
                ]);
            unset($room->reservations);
            return $room;
        });

        return Inertia::render('ClientDashboard/MakeReservation/Rooms', [
            'rooms' => $rooms
        ]);
    }
}
