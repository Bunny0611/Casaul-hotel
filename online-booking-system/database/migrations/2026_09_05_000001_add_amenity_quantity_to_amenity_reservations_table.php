<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amenity_reservations', function (Blueprint $table) {
            $table->unsignedInteger('amenity_quantity')->default(1)->after('amenity_id');
        });
    }

    public function down(): void
    {
        Schema::table('amenity_reservations', function (Blueprint $table) {
            $table->dropColumn('amenity_quantity');
        });
    }
};
