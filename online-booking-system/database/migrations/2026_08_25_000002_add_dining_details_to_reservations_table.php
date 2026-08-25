<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'dining_area')) {
                $table->string('dining_area')->nullable()->after('dining_id');
            }
            if (!Schema::hasColumn('reservations', 'quantity')) {
                $table->unsignedInteger('quantity')->nullable()->after('dining_area');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['dining_area', 'quantity']);
        });
    }
};
