<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->time('room_check_in_time')->nullable()->after('check_in_time');
            $table->time('room_check_out_time')->nullable()->after('check_out_time');
            $table->time('event_start_time')->nullable()->after('room_check_out_time');
            $table->time('event_end_time')->nullable()->after('event_start_time');
            $table->time('amenity_start_time')->nullable()->after('event_end_time');
            $table->time('amenity_end_time')->nullable()->after('amenity_start_time');
            $table->time('dining_time')->nullable()->after('amenity_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'room_check_in_time',
                'room_check_out_time',
                'event_start_time',
                'event_end_time',
                'amenity_start_time',
                'amenity_end_time',
                'dining_time',
            ]);
        });
    }
};
