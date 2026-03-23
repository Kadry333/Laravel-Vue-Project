<?php

namespace App\Http\Controllers\ClientDashboard;

use App\Cores\General\Enums\ReservationStatus;
use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;


class ReservationController extends Controller
{

    private ReservationRepositoryInterface $reservationRepository;
    private RoomRepositoryInterface $roomRepository;

    public function __construct(
        ReservationRepositoryInterface  $reservationRepository,
        RoomRepositoryInterface $roomRepository
    ) {

        $this->reservationRepository = $reservationRepository;
        $this->roomRepository = $roomRepository;
    }

    public function create(CreateReservationRequest $request)
    {
        $clientId = env("CLIENT_ID");
        $room = $this->roomRepository->find($request->room_id);

        // $reservation = $this->reservationRepository->store([
        //     'client_id'        => $clientId,
        //     'room_id'          => $request->room_id,
        //     'accompany_number' => $request->accompany_number,
        //     'paid_price'       => $room->price,
        //     'status'           => ReservationStatus::PENDING,
        // ]);


    }
}
