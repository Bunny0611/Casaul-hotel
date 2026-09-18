<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_hours')->default(4)->after('available_to');
        });

        Schema::table('event_reservations', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_hours')->nullable()->after('event_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('event_reservations', function (Blueprint $table) {
            $table->dropColumn('duration_hours');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('duration_hours');
        });
    }
};
