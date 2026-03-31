<?php

namespace App\Http\Controllers\ClientDashboard;

use App\Cores\General\Enums\ReservationStatus;
use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use App\Cores\General\Service\Contract\StripePaymentContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class ReservationController extends Controller
{
    private ReservationRepositoryInterface $reservationRepository;
    private RoomRepositoryInterface $roomRepository;
    private StripePaymentContract $stripePayment;

    public function __construct(
        ReservationRepositoryInterface $reservationRepository,
        RoomRepositoryInterface $roomRepository,
        StripePaymentContract $stripePayment
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->roomRepository        = $roomRepository;
        $this->stripePayment         = $stripePayment;
    }

    public function index()
    {
        $reservations = $this->reservationRepository->paginate(10, ['room'], ['client_id' => Auth::id()]);

        return Inertia::render('ClientDashboard/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(CreateReservationRequest $request)
    {
        $room = $this->roomRepository->find($request->room_id);

        $nights = \Carbon\Carbon::parse($request->check_in_date)
            ->diffInDays(\Carbon\Carbon::parse($request->check_out_date));

        $reservation = $this->reservationRepository->store([
            'client_id'        => auth()->id(),
            'room_id'          => $request->room_id,
            'accompany_number' => $request->accompany_number,
            'paid_price'       => $room->price * $nights,
            'status'           => ReservationStatus::PENDING,
            'check_in_date'    => $request->check_in_date,
            'check_out_date'   => $request->check_out_date,
        ]);

        $session = $this->stripePayment->createCheckoutSession(
            $reservation->id,
            $reservation->paid_price
        );

        $reservation->update(['payment_session_id' => $session->id]);

        return Inertia::location($session->url);
    }

    public function success(Request $request)
    {
        $reservation = $this->reservationRepository->first([
            'payment_session_id' => $request->session_id,
        ]);

        $reservation->update(['status' => ReservationStatus::APPROVED]);

        return inertia('ClientDashboard/MakeReservation/Success', [
            'order_id' => $reservation->id,
            'amount'   => number_format($reservation->paid_price / 100, 2),
        ]);
    }

    public function cancel(Request $request)
    {
        if ($request->session_id) {
            $reservation = $this->reservationRepository->first([
                'payment_session_id' => $request->session_id,
            ]);

            if ($reservation && $reservation->status === ReservationStatus::PENDING->value) {
                $this->reservationRepository->update($reservation->id, [
                    'status' => ReservationStatus::CANCELLED,
                ]);
            }
        }

        return inertia('ClientDashboard/MakeReservation/Cancel');
    }

    public function handle(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException | \UnexpectedValueException $e) {
            return response('Invalid request', 400);
        }

        $session = $event->data->object;

        $reservation = $this->reservationRepository->first([
            'payment_session_id' => $session->id,
        ]);

        if (!$reservation) {
            return response('Not found', 404);
        }

        $status = match ($event->type) {
            'checkout.session.completed' => ReservationStatus::APPROVED,
            'checkout.session.expired'   => ReservationStatus::CANCELLED,
            default                      => ReservationStatus::CANCELLED,
        };

        if ($status === ReservationStatus::APPROVED && $reservation->status !== ReservationStatus::CANCELLED) {
            $this->reservationRepository->update($reservation->id, ['status' => ReservationStatus::APPROVED]);
        }

        if ($status === ReservationStatus::CANCELLED && $reservation->status !== ReservationStatus::APPROVED) {
            $this->reservationRepository->update($reservation->id, ['status' => ReservationStatus::CANCELLED]);
        }

        return response('OK', 200);
    }
}
