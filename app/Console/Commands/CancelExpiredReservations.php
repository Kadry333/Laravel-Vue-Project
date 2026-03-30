<?php

namespace App\Console\Commands;

use App\Cores\General\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Console\Command;

class CancelExpiredReservations extends Command
{
    protected $signature   = 'reservations:cancel-expired';
    protected $description = 'Cancel pending reservations that exceeded 30 minutes';

    public function handle()
    {
        $count = Reservation::where('status', ReservationStatus::PENDING)
            ->where('created_at', '<', now()->subMinutes(30))
            ->update(['status' => ReservationStatus::CANCELLED]);

        $this->info("Cancelled {$count} expired reservations.");
    }
}
