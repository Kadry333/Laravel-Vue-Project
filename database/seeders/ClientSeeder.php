<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $omar  = User::where('email', 'omar@hotel.com')->first();
        $hana  = User::where('email', 'hana@hotel.com')->first();
        $nora  = User::where('email', 'nora@hotel.com')->first();

        $egyptId  = DB::table('lc_countries')->where('iso_alpha_2', 'EG')->value('id');
        $saudiId  = DB::table('lc_countries')->where('iso_alpha_2', 'SA')->value('id');
        $uaeId    = DB::table('lc_countries')->where('iso_alpha_2', 'AE')->value('id');
        $jordanId = DB::table('lc_countries')->where('iso_alpha_2', 'JO')->value('id');

        $clients = [
            // Approved clients (approved by omar)
            [
                'name'        => 'Ahmed Mostafa',
                'email'       => 'ahmed@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'male',
                'mobile'      => '+201100000001',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'approved_by' => $omar->id,
            ],
            [
                'name'        => 'Fatma Hassan',
                'email'       => 'fatma@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'female',
                'mobile'      => '+201100000002',
                'country_id'  => $egyptId,
                'is_approved' => true,
                'approved_by' => $omar->id,
            ],
            [
                'name'        => 'Ali Ibrahim',
                'email'       => 'ali@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'male',
                'mobile'      => '+966500000020',
                'country_id'  => $saudiId,
                'is_approved' => true,
                'approved_by' => $hana->id,
            ],
            [
                'name'        => 'Mona Khaled',
                'email'       => 'mona@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'female',
                'mobile'      => '+971500000001',
                'country_id'  => $uaeId,
                'is_approved' => true,
                'approved_by' => $hana->id,
            ],
            [
                'name'        => 'Khaled Nasser',
                'email'       => 'khaled@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'male',
                'mobile'      => '+962700000001',
                'country_id'  => $jordanId,
                'is_approved' => true,
                'approved_by' => $nora->id,
            ],
            // Pending clients (not yet approved)
            [
                'name'        => 'Nour Tarek',
                'email'       => 'nour@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'female',
                'mobile'      => '+201100000010',
                'country_id'  => $egyptId,
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name'        => 'Sameh Adel',
                'email'       => 'sameh@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'male',
                'mobile'      => '+201100000011',
                'country_id'  => $egyptId,
                'is_approved' => false,
                'approved_by' => null,
            ],
            [
                'name'        => 'Rania Mahmoud',
                'email'       => 'rania@client.com',
                'password'    => Hash::make('123456'),
                'gender'      => 'female',
                'mobile'      => '+966500000030',
                'country_id'  => $saudiId,
                'is_approved' => false,
                'approved_by' => null,
            ],
        ];

        foreach ($clients as $client) {
            $user = User::firstOrCreate(
                ['email' => $client['email']],
                $client
            );
            $user->assignRole('client');
        }
    
    }
}
