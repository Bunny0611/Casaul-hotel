<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'event_type')) {
                $table->string('event_type')->nullable()->after('guest_phone');
            }
            if (!Schema::hasColumn('reservations', 'number_of_guests')) {
                $table->unsignedInteger('number_of_guests')->nullable()->after('event_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'number_of_guests']);
        });
    }
};
