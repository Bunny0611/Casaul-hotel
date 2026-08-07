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
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create housekeeping user
        User::create([
            'name' => 'Housekeeping',
            'email' => 'housekeeping@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'housekeeping',
        ]);

        // Create employee user
        User::create([
            'name' => 'Employee',
            'email' => 'employee@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        // Run room seeder
        $this->call(RoomSeeder::class);
    }
}
