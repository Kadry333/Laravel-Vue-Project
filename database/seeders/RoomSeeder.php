<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;


class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ramadan  = User::where('email', 'ramadan@hotel.com')->first();
        $amr = User::where('email', 'amr@hotel.com')->first();
        $ahmed = User::where('email', 'ahmed.manager@hotel.com')->first();

        $floorA = Floor::where('name', 'Floor A')->first();
        $floorB = Floor::where('name', 'Floor B')->first();
        $floorC = Floor::where('name', 'Floor C')->first();
        $floorD = Floor::where('name', 'Floor D')->first();
        $floorE = Floor::where('name', 'Floor E')->first();

        $rooms = [
            // Floor A — ramadan's rooms
            ['number' => '1001', 'capacity' => 2, 'price' => 12000,  'floor_id' => $floorA->id, 'manager_id' => $ramadan->id],
            ['number' => '1002', 'capacity' => 4, 'price' => 20000,  'floor_id' => $floorA->id, 'manager_id' => $ramadan->id],
            ['number' => '1003', 'capacity' => 2, 'price' => 9500,   'floor_id' => $floorA->id, 'manager_id' => $ramadan->id],
            ['number' => '1004', 'capacity' => 3, 'price' => 15000,  'floor_id' => $floorA->id, 'manager_id' => $ramadan->id],

            // Floor B — ramadan's rooms
            ['number' => '2001', 'capacity' => 2, 'price' => 11000,  'floor_id' => $floorB->id, 'manager_id' => $ramadan->id],
            ['number' => '2002', 'capacity' => 6, 'price' => 35000,  'floor_id' => $floorB->id, 'manager_id' => $ramadan->id],
            ['number' => '2003', 'capacity' => 2, 'price' => 10000,  'floor_id' => $floorB->id, 'manager_id' => $ramadan->id],

            // Floor C — amr's rooms
            ['number' => '3001', 'capacity' => 2, 'price' => 13000,  'floor_id' => $floorC->id, 'manager_id' => $amr->id],
            ['number' => '3002', 'capacity' => 4, 'price' => 22000,  'floor_id' => $floorC->id, 'manager_id' => $amr->id],
            ['number' => '3003', 'capacity' => 2, 'price' => 9000,   'floor_id' => $floorC->id, 'manager_id' => $amr->id],

            // Floor D — amr's rooms
            ['number' => '4001', 'capacity' => 2, 'price' => 14000,  'floor_id' => $floorD->id, 'manager_id' => $amr->id],
            ['number' => '4002', 'capacity' => 3, 'price' => 18000,  'floor_id' => $floorD->id, 'manager_id' => $amr->id],

            // Floor E — ahmed's rooms
            ['number' => '5001', 'capacity' => 2, 'price' => 16000,  'floor_id' => $floorE->id, 'manager_id' => $ahmed->id],
            ['number' => '5002', 'capacity' => 4, 'price' => 25000,  'floor_id' => $floorE->id, 'manager_id' => $ahmed->id],
            ['number' => '5003', 'capacity' => 2, 'price' => 12000,  'floor_id' => $floorE->id, 'manager_id' => $ahmed->id],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['number' => $room['number']],
                $room
            );
        }
    }
}
