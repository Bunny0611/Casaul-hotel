<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schedules = DB::table('dining_schedules')->pluck('id', 'period');

        $menus = [
            ['name' => 'Filipino Breakfast', 'category' => 'Breakfast', 'price' => 250, 'available_from' => '07:00', 'available_to' => '10:00', 'status' => 'available'],
            ['name' => 'Club Sandwich', 'category' => 'Lunch', 'price' => 320, 'available_from' => '11:00', 'available_to' => '14:00', 'status' => 'available'],
            ['name' => 'Grilled Chicken', 'category' => 'Lunch', 'price' => 350, 'available_from' => '11:00', 'available_to' => '14:00', 'status' => 'available'],
            ['name' => 'Steak', 'category' => 'Dinner', 'price' => 650, 'available_from' => '17:00', 'available_to' => '21:00', 'status' => 'unavailable'],
            ['name' => 'Pasta Carbonara', 'category' => 'Dinner', 'price' => 380, 'available_from' => '17:00', 'available_to' => '21:00', 'status' => 'available'],
        ];

        foreach ($menus as $menu) {
            DB::table('dining_menus')->updateOrInsert(
                ['name' => $menu['name']],
                [
                    ...$menu,
                    'dining_schedule_id' => $schedules->get($menu['category']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('dining_menus')->whereIn('name', [
            'Filipino Breakfast',
            'Club Sandwich',
            'Grilled Chicken',
            'Steak',
            'Pasta Carbonara',
        ])->delete();
    }
};