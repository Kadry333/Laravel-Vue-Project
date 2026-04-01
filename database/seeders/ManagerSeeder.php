<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $egyptId = DB::table('lc_countries')
            ->where('iso_alpha_2', 'EG')
            ->value('id');

        $saudiId = DB::table('lc_countries')
            ->where('iso_alpha_2', 'SA')
            ->value('id');
            $managers = [
            [
                'name'        => 'Ramadan Mohamed',
                'email'       => 'ramadan@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010001652',
                'gender'      => 'male',
                'mobile'      => '+201000000001',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'is_banned'   => false,
            ],
            [
                'name'        => 'Amr Shoukry',
                'email'       => 'amr@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010002251',
                'gender'      => 'male',
                'mobile'      => '+201000000002',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'is_banned'   => false,
            ],
            [
                'name'        => 'Ahmed Al-Emam',
                'email'       => 'ahmed.manager@hotel.com',
                'password'    => Hash::make('123456'),
                'national_id' => '32001010003092',
                'gender'      => 'female',
                'mobile'      => '+966500000001',
                'country_id'  => $saudiId,
                'is_approved' => true,
                'is_banned'   => false,
            ],
        ];

        foreach ($managers as $manager) {
            $user = User::firstOrCreate(
                ['email' => $manager['email']],
                $manager
            );
            $user->assignRole('manager');
        }
    }
}
