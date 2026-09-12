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
            if (!Schema::hasColumn('reservations', 'facility_id')) {
                $table->unsignedBigInteger('facility_id')->nullable()->after('category');
            }
            if (!Schema::hasColumn('reservations', 'event_id')) {
                $table->unsignedBigInteger('event_id')->nullable()->after('facility_id');
            }
            if (!Schema::hasColumn('reservations', 'dining_id')) {
                $table->unsignedBigInteger('dining_id')->nullable()->after('event_id');
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
                'facility_id',
                'event_id',
                'dining_id',
            ]);
        });
    }
};
