<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahmed  = User::where('email', 'ahmed@client.com')->first();
        $fatma  = User::where('email', 'fatma@client.com')->first();
        $ali    = User::where('email', 'ali@client.com')->first();
        $mona   = User::where('email', 'mona@client.com')->first();
        $khaled = User::where('email', 'khaled@client.com')->first();
        $omar   = User::where('email', 'omar@hotel.com')->first();
        $hana   = User::where('email', 'hana@hotel.com')->first();
        $nora   = User::where('email', 'nora@hotel.com')->first();

        $room1001 = Room::where('number', '1001')->first();
        $room1002 = Room::where('number', '1002')->first();
        $room2001 = Room::where('number', '2001')->first();
        $room3001 = Room::where('number', '3001')->first();
        $room5001 = Room::where('number', '5001')->first();

           $reservations = [
            // Ahmed — completed past reservation
            [
                'client_id'        => $ahmed->id,
                'room_id'          => $room1001->id,
                'receptionist_id'  => $omar->id,
                'accompany_number' => 1,
                'paid_price'       => $room1001->price,
                'check_in_date'    => now()->subMonths(2)->toDateString(),
                'check_out_date'   => now()->subMonths(2)->addDays(3)->toDateString(),
                'status'           => 'approved',
                'payment_session_id' => 'cs_test_seed_001',
            ],
            // Fatma — completed past reservation
            [
                'client_id'        => $fatma->id,
                'room_id'          => $room2001->id,
                'receptionist_id'  => $hana->id,
                'accompany_number' => 1,
                'paid_price'       => $room2001->price,
                'check_in_date'    => now()->subMonth()->toDateString(),
                'check_out_date'   => now()->subMonth()->addDays(2)->toDateString(),
                'status'           => 'approved',
                'payment_session_id' => 'cs_test_seed_002',
            ],
            // Ali — active current reservation
            [
                'client_id'        => $ali->id,
                'room_id'          => $room1002->id,
                'receptionist_id'  => $omar->id,
                'accompany_number' => 2,
                'paid_price'       => $room1002->price,
                'check_in_date'    => now()->toDateString(),
                'check_out_date'   => now()->addDays(3)->toDateString(),
                'status'           => 'approved',
                'payment_session_id' => 'cs_test_seed_003',
            ],
            // Mona — future reservation
            [
                'client_id'        => $mona->id,
                'room_id'          => $room3001->id,
                'receptionist_id'  => $hana->id,
                'accompany_number' => 1,
                'paid_price'       => $room3001->price,
                'check_in_date'    => now()->addDays(5)->toDateString(),
                'check_out_date'   => now()->addDays(8)->toDateString(),
                'status'           => 'pending',
                'payment_session_id' => 'cs_test_seed_004',
            ],
            // Khaled — cancelled reservation
            [
                'client_id'        => $khaled->id,
                'room_id'          => $room5001->id,
                'receptionist_id'  => $nora->id,
                'accompany_number' => 1,
                'paid_price'       => $room5001->price,
                'check_in_date'    => now()->subWeeks(2)->toDateString(),
                'check_out_date'   => now()->subWeeks(2)->addDays(2)->toDateString(),
                'status'           => 'cancelled',
                'payment_session_id' => 'cs_test_seed_005',
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::firstOrCreate(
                [
                    'client_id' => $reservation['client_id'],
                    'room_id'   => $reservation['room_id'],
                ],
                $reservation
            );
        }
    }
}
