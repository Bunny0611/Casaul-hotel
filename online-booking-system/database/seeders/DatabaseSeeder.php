<?php

namespace Database\Seeders;

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
        // Create default housekeeping user
        // Create default employee user
            $this->call(AccountSeeder::class);

        // Run room seeder
        $this->call(RoomSeeder::class);
    }
}

