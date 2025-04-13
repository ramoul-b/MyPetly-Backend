<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crée les rôles de base
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'provider']);
        Role::firstOrCreate(['name' => 'user']);
    }
}
