<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
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

        // Create default housekeeping user
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

        // Create default employee user
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

        // Create default guest user for local SQLite testing
        User::updateOrCreate([
            'email' => 'ruzzelarmuela@gmail.com',
        ], [
            'name' => 'Ruzzel Armuela',
            'first_name' => 'Ruzzel',
            'last_name' => 'Armuela',
            'middle_initial' => '',
            'contact_no' => '09171234567',
            'password' => bcrypt('123456'),
            'role' => 'guest',
            'is_active' => true,
        ]);

        // Run room seeder
        $this->call(RoomSeeder::class);
    }
}

