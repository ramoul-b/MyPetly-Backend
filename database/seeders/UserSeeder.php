<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Compte Super Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@mypetly.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('super_admin');

        // Compte Provider
        $provider = User::firstOrCreate([
            'email' => 'badrramoul@gmail.com.com',
        ], [
            'name' => 'Badr Ramoul',
            'password' => bcrypt('111111'),
        ]);

        $provider->assignRole('provider');
    }
}
