<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('amenities') && !Schema::hasTable('facilities')) {
            Schema::rename('amenities', 'facilities');
        }
        if (Schema::hasTable('event_places') && !Schema::hasTable('events')) {
            Schema::rename('event_places', 'events');
        }
        if (Schema::hasTable('amenity_reservations') && !Schema::hasTable('facility_reservations')) {
            Schema::rename('amenity_reservations', 'facility_reservations');
        }

        if (Schema::hasTable('event_reservations')) {
            Schema::table('event_reservations', function (Blueprint $table) {
                if (Schema::hasColumn('event_reservations', 'event_place_id')) {
                    if (Schema::getConnection()->getDriverName() === 'sqlite') {
                        $table->dropForeign(['event_place_id']);
                    } else {
                        $table->dropForeign('event_reservations_event_place_id_foreign');
                    }
                    $table->renameColumn('event_place_id', 'event_id');
                }
            });
            Schema::table('event_reservations', function (Blueprint $table) {
                if (Schema::hasColumn('event_reservations', 'event_id')) {
                    $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('facility_reservations')) {
            Schema::table('facility_reservations', function (Blueprint $table) {
                if (Schema::hasColumn('facility_reservations', 'amenity_id')) {
                    if (Schema::getConnection()->getDriverName() === 'sqlite') {
                        $table->dropForeign(['amenity_id']);
                    } else {
                        $table->dropForeign('amenity_reservations_amenity_id_foreign');
                    }
                    $table->renameColumn('amenity_id', 'facility_id');
                }
                if (Schema::hasColumn('facility_reservations', 'amenity_quantity')) {
                    $table->renameColumn('amenity_quantity', 'facility_quantity');
                }
                if (Schema::hasColumn('facility_reservations', 'amenity_start_time')) {
                    $table->renameColumn('amenity_start_time', 'facility_start_time');
                }
                if (Schema::hasColumn('facility_reservations', 'amenity_end_time')) {
                    $table->renameColumn('amenity_end_time', 'facility_end_time');
                }
            });
            Schema::table('facility_reservations', function (Blueprint $table) {
                if (Schema::hasColumn('facility_reservations', 'facility_id')) {
                    $table->foreign('facility_id')->references('id')->on('facilities')->cascadeOnDelete();
                }
            });
        }

        if (Schema::hasTable('reservations')) {
            Schema::table('reservations', function (Blueprint $table) {
                if (Schema::hasColumn('reservations', 'amenity_id')) {
                    $table->renameColumn('amenity_id', 'facility_id');
                }
                if (Schema::hasColumn('reservations', 'event_place_id')) {
                    $table->renameColumn('event_place_id', 'event_id');
                }
                if (Schema::hasColumn('reservations', 'amenity_start_time')) {
                    $table->renameColumn('amenity_start_time', 'facility_start_time');
                }
                if (Schema::hasColumn('reservations', 'amenity_end_time')) {
                    $table->renameColumn('amenity_end_time', 'facility_end_time');
                }
            });
        }

        if (Schema::hasTable('inventory_items')) {
            $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';
            if (!$isSqlite) {
                DB::statement("ALTER TABLE inventory_items MODIFY category VARCHAR(50) NOT NULL");
            }
            DB::table('inventory_items')->where('category', 'amenities')->update(['category' => 'facilities']);
            DB::table('inventory_items')->where('category', 'event_place')->update(['category' => 'event']);
            if (!$isSqlite) {
                DB::statement("ALTER TABLE inventory_items MODIFY category ENUM('facilities', 'event', 'dining') NOT NULL");
            }
        }
        if (Schema::hasTable('reservations')) {
            DB::table('reservations')->where('category', 'amenities')->update(['category' => 'facilities']);
            DB::table('reservations')->where('category', 'event_place')->update(['category' => 'event']);
        }
    }

    public function down(): void
    {
        throw new \RuntimeException('The Facilities and Events rename cannot be automatically reversed.');
    }
};