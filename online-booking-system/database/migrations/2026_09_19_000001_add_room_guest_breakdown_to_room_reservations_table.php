<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->unsignedInteger('adult_guests')->nullable()->after('number_of_guests');
            $table->unsignedInteger('kid_guests')->nullable()->after('adult_guests');
        });
    }

    public function down(): void
    {
        Schema::table('room_reservations', function (Blueprint $table) {
            $table->dropColumn(['adult_guests', 'kid_guests']);
        });
    }
};
