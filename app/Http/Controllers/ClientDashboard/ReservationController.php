<?php

namespace App\Http\Controllers\ClientDashboard;

use App\Cores\General\Enums\ReservationStatus;
use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Cores\General\Service\Contract\StripePaymentContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\SuccessReservationRequest;
use Inertia\Inertia;

class ReservationController extends Controller
{

    private ReservationRepositoryInterface $reservationRepository;
    private RoomRepositoryInterface $roomRepository;
    private StripePaymentContract $stripePayment;

    public function __construct(
        ReservationRepositoryInterface  $reservationRepository,
        RoomRepositoryInterface $roomRepository,
        StripePaymentContract $stripePayment
    ) {

        $this->reservationRepository = $reservationRepository;
        $this->roomRepository = $roomRepository;
        $this->stripePayment = $stripePayment;
    }

    public function create(CreateReservationRequest $request)
    {
        $clientId = env("CLIENT_ID");
        $room = $this->roomRepository->find($request->room_id);

        $nights = \Carbon\Carbon::parse($request->check_in_date)
            ->diffInDays(\Carbon\Carbon::parse($request->check_out_date));

        $reservation = $this->reservationRepository->store([
            'client_id'        => $clientId,
            'room_id'          => $request->room_id,
            'accompany_number' => $request->accompany_number,
            'paid_price'       => $room->price * $nights,
            'status'           => ReservationStatus::PENDING,
            'check_in_date'    => $request->check_in_date,
            'check_out_date'   => $request->check_out_date,
        ]);

        $session = $this->stripePayment->createCheckoutSession($reservation->id, $reservation->paid_price);

        $reservation->update(['payment_session_id' => $session->id]);

        return Inertia::location($session->url);
    }


    public function success(SuccessReservationRequest $request)
    {
        $sessionId = $request->session_id;
        $reservation = $this->reservationRepository->first(['payment_session_id' => $sessionId]);
        $reservation->update([
            'status' => ReservationStatus::APPROVED,
        ]);

        return redirect()->route('client.rooms.index')->with('payment_success', [
            'order_id' => $reservation->id,
            'amount' => $reservation->paid_price,
        ]);
    }

    public function cancel()
    {
        return redirect()->route('client.rooms.index')->with('payment_cancelled', true);
    }
}
