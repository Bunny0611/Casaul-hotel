<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('amenities', function (Blueprint $table) {
            $table->string('pricing_basis')->default('Per Stay')->after('price');
            $table->unsignedInteger('capacity')->nullable()->after('pricing_basis');
            $table->string('scheduling_requirement')->default('No Additional Schedule')->after('capacity');
        });
        Schema::table('event_places', function (Blueprint $table) {
            $table->string('event_type')->default('Birthday')->after('id');
            $table->string('pricing_basis')->default('Per Event')->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('amenities', fn (Blueprint $table) => $table->dropColumn(['pricing_basis', 'capacity', 'scheduling_requirement']));
        Schema::table('event_places', fn (Blueprint $table) => $table->dropColumn(['event_type', 'pricing_basis']));
    }
};