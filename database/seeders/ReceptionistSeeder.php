<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ramadan  = User::where('email', 'ramadan@hotel.com')->first();
        $amr = User::where('email', 'amr@hotel.com')->first();
        $ahmed = User::where('email', 'ahmed.manager@hotel.com')->first();

        $egyptId = DB::table('lc_countries')->where('iso_alpha_2', 'EG')->value('id');
        $saudiId = DB::table('lc_countries')->where('iso_alpha_2', 'SA')->value('id');
        $uaeId   = DB::table('lc_countries')->where('iso_alpha_2', 'AE')->value('id');

        $receptionists = [
            // Ramadan's receptionists
            [
                'name'        => 'Omar Nabil',
                'email'       => 'omar@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010010891',
                'gender'      => 'male',
                'mobile'      => '+201000000010',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'is_banned'   => false,
                'created_by'  => $ramadan->id,
            ],
            [
                'name'        => 'Hana Samir',
                'email'       => 'hana@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010011094',
                'gender'      => 'female',
                'mobile'      => '+201000000011',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'is_banned'   => false,
                'created_by'  => $ramadan->id,
            ],
            // Amr's receptionists
            [
                'name'        => 'Youssef Ali',
                'email'       => 'youssef@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010012820',
                'gender'      => 'male',
                'mobile'      => '+201000000012',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'is_banned'   => false,
                'created_by'  => $amr->id,
            ],
            // Ahmed's receptionists
            [
                'name'        => 'Nora Khalid',
                'email'       => 'nora@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010013291',
                'gender'      => 'female',
                'mobile'      => '+966500000010',
                'country_id'  => $saudiId,
                'is_approved' => true,
                'is_banned'   => false,
                'created_by'  => $ahmed->id,
            ],
        ];

        foreach ($receptionists as $receptionist) {
            $user = User::firstOrCreate(
                ['email' => $receptionist['email']],
                $receptionist
            );
            $user->assignRole('receptionist');
        }
    }
}
