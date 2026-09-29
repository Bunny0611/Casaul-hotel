<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->json('optional_addons')->nullable();
        });

        Schema::table('event_reservations', function (Blueprint $table) {
            $table->json('selected_addons')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('event_reservations', function (Blueprint $table) {
            $table->dropColumn('selected_addons');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('optional_addons');
        });
    }
};