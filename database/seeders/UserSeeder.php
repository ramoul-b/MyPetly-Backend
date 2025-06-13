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
            'email' => 'badr.ramoul@gmail.com',
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('111111'),
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
