<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::updateOrCreate([
            'email' => 'admin@casaul.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create default housekeeping user
        User::updateOrCreate([
            'email' => 'housekeeping@casaul.com',
        ], [
            'name' => 'Housekeeping',
            'password' => bcrypt('password'),
            'role' => 'housekeeping',
        ]);

        // Create default employee user
        User::updateOrCreate([
            'email' => 'employee@casaul.com',
        ], [
            'name' => 'Employee',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        // Run room seeder
        $this->call(RoomSeeder::class);
    }
}
