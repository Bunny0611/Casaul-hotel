<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('dining_menus')
            ->whereIn('category', ['Dinner', 'Menu / Meal'])
            ->update([
                'category' => 'Main Course',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('dining_menus')
            ->where('category', 'Main Course')
            ->whereIn('name', ['Steak', 'Sunset Dinner'])
            ->update([
                'category' => 'Dinner',
                'updated_at' => now(),
            ]);
    }
};
