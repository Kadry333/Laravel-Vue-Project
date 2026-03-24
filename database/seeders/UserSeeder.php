<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manager
        $manager = User::firstOrCreate([
            'name' => 'Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('123456'),
        ]);
        $manager->assignRole('manager');

        // Receptionist
        $receptionist = User::firstOrCreate([
            'name' => 'Receptionist',
            'email' => 'reception@test.com',
            'password' => Hash::make('123456'),
        ]);
        $receptionist->assignRole('receptionist');

        // Client
        $client = User::firstOrCreate([
            'name' => 'Client',
            'email' => 'client@test.com',
            'password' => Hash::make('123456'),
        ]);
        $client->assignRole('client');
    }
}
