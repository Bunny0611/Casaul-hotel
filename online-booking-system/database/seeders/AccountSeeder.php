<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Seed the default staff accounts.
     */
    public function run(): void
    {
        Staff::updateOrCreate([
            'email' => 'admin@casaul.com',
        ], [
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Staff::updateOrCreate([
            'email' => 'housekeeping@casaul.com',
        ], [
            'name' => 'Housekeeping',
            'first_name' => 'Housekeeping',
            'last_name' => 'Staff',
            'password' => bcrypt('password'),
            'role' => 'housekeeping',
            'is_active' => true,
        ]);

        Staff::updateOrCreate([
            'email' => 'employee@casaul.com',
        ], [
            'name' => 'Employee',
            'first_name' => 'Employee',
            'last_name' => 'Staff',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
        ]);
    }
}
