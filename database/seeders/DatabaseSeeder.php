<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class, 
            AdminSeeder::class,               
            ManagerSeeder::class,             
            ReceptionistSeeder::class,        
            ClientSeeder::class,              
            FloorSeeder::class,               
            RoomSeeder::class,                
            ReservationSeeder::class, 
            
        ]);
    }
}
