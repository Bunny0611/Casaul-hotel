<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('adult_guest_price', 10, 2)->default(0)->after('price');
            $table->decimal('kid_guest_price', 10, 2)->default(0)->after('adult_guest_price');
        });

        DB::table('rooms')->get()->each(function ($room) {
            $adultPrice = str_contains(strtolower((string) $room->room_type), 'standard') ? 500 : 650;

            DB::table('rooms')->where('id', $room->id)->update([
                'adult_guest_price' => $adultPrice,
                'kid_guest_price' => $adultPrice / 2,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['adult_guest_price', 'kid_guest_price']);
        });
    }
};
