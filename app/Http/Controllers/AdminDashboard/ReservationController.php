<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Cores\General\Enums\ReservationStatus;
use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    private ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    /**
     * Display a listing of all reservations.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'status' => $request->status,
        ];

        // Fetching all reservations with relationships (client and room)
        $reservations = $this->reservationRepository->paginate(15, ['client', 'room'], $filters);

        return Inertia::render('AdminDashboard/Reservations/Index', [
            'reservations' => $reservations,
            'filters' => $filters,
        ]);
    }

    /**
     * Update the status of a reservation.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|in:approved,cancelled,pending',
        ]);

        $this->reservationRepository->update($id, [
            'status' => $request->status,
        ]);

        return back()->with('success', 'Reservation status updated successfully.');
    }
}
