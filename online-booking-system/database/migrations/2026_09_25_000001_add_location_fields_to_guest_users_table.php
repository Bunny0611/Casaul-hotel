<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_users', function (Blueprint $table): void {
            $table->string('country_code', 10)->nullable()->after('contact_no');
            $table->string('country_name')->nullable()->after('country_code');
            $table->string('region_code', 30)->nullable()->after('country_name');
            $table->string('region_name')->nullable()->after('region_code');
            $table->string('province_code', 30)->nullable()->after('region_name');
            $table->string('province_name')->nullable()->after('province_code');
            $table->string('city_code', 30)->nullable()->after('province_name');
            $table->string('city_name')->nullable()->after('city_code');
        });
    }

    public function down(): void
    {
        Schema::table('guest_users', function (Blueprint $table): void {
            $table->dropColumn([
                'country_code', 'country_name', 'region_code', 'region_name',
                'province_code', 'province_name', 'city_code', 'city_name',
            ]);
        });
    }
};