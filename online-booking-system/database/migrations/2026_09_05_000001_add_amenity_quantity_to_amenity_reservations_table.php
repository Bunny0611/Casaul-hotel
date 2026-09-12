<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->unsignedInteger('facility_quantity')->default(1)->after('facility_id');
        });
    }

    public function down(): void
    {
        Schema::table('facility_reservations', function (Blueprint $table) {
            $table->dropColumn('facility_quantity');
        });
    }
};
