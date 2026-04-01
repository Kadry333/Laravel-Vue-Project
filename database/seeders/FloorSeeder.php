<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Floor;
use App\Models\User;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ramadan  = User::where('email', 'ramadan@hotel.com')->first();
        $amr = User::where('email', 'amr@hotel.com')->first();
        $ahmed = User::where('email', 'ahmed.manager@hotel.com')->first();

        $floors = [
            ['name' => 'Floor A', 'manager_id' => $ramadan->id],
            ['name' => 'Floor B', 'manager_id' => $ramadan->id],
            ['name' => 'Floor C', 'manager_id' => $amr->id],
            ['name' => 'Floor D', 'manager_id' => $amr->id],
            ['name' => 'Floor E', 'manager_id' => $ahmed->id],
        ];

        foreach ($floors as $floor) {
            Floor::firstOrCreate(
                ['name' => $floor['name']],
                $floor
            );
        }
    }
}
