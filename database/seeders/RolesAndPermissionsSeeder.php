<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

          $permissions = [
            // Manager permissions
            'manage receptionists',
            'manage floors',
            'manage rooms',
            'manage clients',
            
            // Receptionist permissions
            'approve clients',
            'view approved clients',
            'view client reservations',
            
            // Client permissions
            'make reservation',
            'view own reservations',
            
            // Admin-only permissions
            'manage managers',
            'view statistics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
         $client = Role::firstOrCreate(['name' => 'client']);
        $client->syncPermissions([
            'make reservation',
            'view own reservations',
        ]);

        // Receptionist role
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionist->syncPermissions([
            'approve clients',
            'view approved clients',
            'view client reservations',
        ]);

        // Manager role
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'manage receptionists',
            'manage floors',
            'manage rooms',
            'manage clients',
            'approve clients',
            'view approved clients',
            'view client reservations',
            'view statistics',
        ]);

        // Admin role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());
    }
}