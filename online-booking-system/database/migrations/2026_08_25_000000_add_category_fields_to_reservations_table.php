<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'category')) {
                $table->string('category')->default('rooms')->after('id');
            }
            if (!Schema::hasColumn('reservations', 'amenity_id')) {
                $table->unsignedBigInteger('amenity_id')->nullable()->after('category');
            }
            if (!Schema::hasColumn('reservations', 'event_place_id')) {
                $table->unsignedBigInteger('event_place_id')->nullable()->after('amenity_id');
            }
            if (!Schema::hasColumn('reservations', 'dining_id')) {
                $table->unsignedBigInteger('dining_id')->nullable()->after('event_place_id');
            }
        });

        if (Schema::hasColumn('reservations', 'room_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->unsignedBigInteger('room_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->nullable(false)->change();
            $table->dropColumn([
                'category',
                'amenity_id',
                'event_place_id',
                'dining_id',
            ]);
        });
    }
};
