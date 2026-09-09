<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        DB::statement("ALTER TABLE rooms MODIFY status ENUM('OC', 'OD', 'VR', 'VC', 'VD', 'HSD', 'HSUC', 'OOO', 'BLO', 'NS', 'SO', 'HU', 'DND', 'available', 'occupied', 'reserved', 'maintenance', 'blocked', 'out_of_order') NOT NULL DEFAULT 'VR'");
        DB::statement("ALTER TABLE rooms MODIFY cleaning_status ENUM('clean', 'dirty', 'in_progress', 'ready', 'blocked', 'out_of_order') NOT NULL DEFAULT 'clean'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('rooms')) {
            return;
        }

        DB::statement("ALTER TABLE rooms MODIFY status ENUM('available', 'occupied', 'reserved', 'maintenance', 'blocked', 'out_of_order', 'vacant_ready', 'vacant_dirty', 'occupied_clean', 'occupied_dirty') NOT NULL DEFAULT 'available'");
        DB::statement("ALTER TABLE rooms MODIFY cleaning_status ENUM('clean', 'dirty', 'in_progress', 'ready', 'blocked', 'out_of_order') NOT NULL DEFAULT 'clean'");
    }
};
