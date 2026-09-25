<?php

use Database\Seeders\RoomSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $roomNumbers = [
            '101', '102', '103', '104', '105', '106', '107', '108', '109', '110',
            '201', '202', '203', '204', '205', '206', '207', '208', '209', '210',
        ];

        $obsoleteRoomIds = DB::table('rooms')
            ->whereNotIn('room_number', $roomNumbers)
            ->pluck('id');

        if ($obsoleteRoomIds->isNotEmpty()) {
            foreach (['reservations', 'room_reservations', 'housekeeping_tasks'] as $table) {
                if (!Schema::hasTable($table)) {
                    continue;
                }

                if ($table === 'reservations' && Schema::hasColumn('reservations', 'room_id')) {
                    DB::table($table)
                        ->whereIn('room_id', $obsoleteRoomIds)
                        ->update(['room_id' => null]);

                    continue;
                }

                DB::table($table)
                    ->whereIn('room_id', $obsoleteRoomIds)
                    ->delete();
            }
        }

        app(RoomSeeder::class)->run();
    }

    public function down(): void
    {
        throw new RuntimeException('The room catalog replacement migration cannot be reversed safely.');
    }
};