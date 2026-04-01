<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'        => 'Admin',
                'password'    => Hash::make('123456'),
                'is_approved' => true,
                'is_banned'   => false,
            ]
        );

        $admin->assignRole('admin');
    }
    
    

}
