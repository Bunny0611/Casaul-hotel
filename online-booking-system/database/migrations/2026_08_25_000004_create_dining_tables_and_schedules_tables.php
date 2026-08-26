<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dining_tables')) {
            Schema::create('dining_tables', function (Blueprint $table) {
                $table->id();
                $table->string('table_no')->unique();
                $table->string('type');
                $table->unsignedInteger('capacity');
                $table->string('location')->nullable();
                $table->string('status')->default('Available');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('dining_schedules')) {
            Schema::create('dining_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('period')->unique();
                $table->time('available_from');
                $table->time('available_to');
                $table->unsignedInteger('max_guests')->nullable();
                $table->string('status')->default('Active');
                $table->timestamps();
            });
        }

        if (DB::table('dining_tables')->count() === 0) {
            DB::table('dining_tables')->insert([
                ['table_no' => 'T01', 'type' => 'Indoor', 'capacity' => 2, 'location' => 'Window', 'status' => 'Available', 'created_at' => now(), 'updated_at' => now()],
                ['table_no' => 'T02', 'type' => 'Indoor', 'capacity' => 4, 'location' => 'Main Area', 'status' => 'Reserved', 'created_at' => now(), 'updated_at' => now()],
                ['table_no' => 'T03', 'type' => 'Outdoor', 'capacity' => 6, 'location' => 'Garden', 'status' => 'Available', 'created_at' => now(), 'updated_at' => now()],
                ['table_no' => 'T04', 'type' => 'Private', 'capacity' => 8, 'location' => 'Private Room', 'status' => 'Unavailable', 'created_at' => now(), 'updated_at' => now()],
                ['table_no' => 'T05', 'type' => 'Indoor', 'capacity' => 4, 'location' => 'Main Area', 'status' => 'Available', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (DB::table('dining_schedules')->count() === 0) {
            DB::table('dining_schedules')->insert([
                ['period' => 'Breakfast', 'available_from' => '07:00', 'available_to' => '10:00', 'max_guests' => 30, 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
                ['period' => 'Lunch', 'available_from' => '11:00', 'available_to' => '14:00', 'max_guests' => 40, 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
                ['period' => 'Afternoon Snacks', 'available_from' => '14:00', 'available_to' => '17:00', 'max_guests' => 20, 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
                ['period' => 'Dinner', 'available_from' => '17:00', 'available_to' => '21:00', 'max_guests' => 40, 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_schedules');
        Schema::dropIfExists('dining_tables');
    }
};
