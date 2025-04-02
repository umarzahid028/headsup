<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(['name' => 'admin']);
    }
} 