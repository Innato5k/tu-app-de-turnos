<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $proRole   = Role::firstOrCreate(['name' => 'professional']);
        Role::firstOrCreate(['name' => 'patient']);

        User::create([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'Admin@Admin.com', // Poné el que vos quieras
            'password' => Hash::make('123456789'), // Poné una clave segura
            'cuil' => '00000000000',
            'national_md_lic' => '0', // Al ser admin, no necesita licencia médica real
            'provincial_md_lic' => '0',
        ]);

        $admin = User::where('email', 'Admin@Admin.com')->first();
        $admin->assignRole($adminRole);

        
    }
}