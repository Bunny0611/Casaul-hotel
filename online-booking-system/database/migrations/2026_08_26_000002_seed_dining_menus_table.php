<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schedules = DB::table('dining_schedules')->pluck('id', 'period');

        $menus = [
            // Breakfast
            ['name' => 'Filipino Breakfast', 'category' => 'Breakfast', 'price' => 250, 'available_from' => '07:00', 'available_to' => '10:00', 'status' => 'available', 'image' => 'dining/filipino-breakfast.jpg'],
            ['name' => 'Continental Breakfast', 'category' => 'Breakfast', 'price' => 280, 'available_from' => '07:00', 'available_to' => '10:00', 'status' => 'available', 'image' => 'dining/continental-breakfast.jpg'],
            ['name' => 'Tocino Silog', 'category' => 'Breakfast', 'price' => 260, 'available_from' => '07:00', 'available_to' => '10:00', 'status' => 'available', 'image' => 'dining/tocino-silog.jpg'],
            ['name' => 'Longganisa Breakfast Set', 'category' => 'Breakfast', 'price' => 270, 'available_from' => '07:00', 'available_to' => '10:00', 'status' => 'available', 'image' => 'dining/longganisa-breakfast.jpg'],

            // Appetizers
            ['name' => 'Crispy Calamari', 'category' => 'Appetizer', 'price' => 320, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/crispy-calamari.jpg'],
            ['name' => 'Buffalo Wings', 'category' => 'Appetizer', 'price' => 280, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/buffalo-wings.jpg'],
            ['name' => 'Cheese Sticks', 'category' => 'Appetizer', 'price' => 240, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/cheese-sticks.jpg'],
            ['name' => 'Nacho Platter', 'category' => 'Appetizer', 'price' => 340, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/nacho-platter.jpg'],

            // Main Course
            ['name' => 'Chicken Adobo', 'category' => 'Main Course', 'price' => 350, 'available_from' => '11:00', 'available_to' => '22:00', 'status' => 'available', 'image' => 'dining/chicken-adobo.jpg'],
            ['name' => 'Grilled Chicken', 'category' => 'Main Course', 'price' => 420, 'available_from' => '11:00', 'available_to' => '22:00', 'status' => 'available', 'image' => 'dining/grilled-chicken.jpg'],
            ['name' => 'Beef Steak', 'category' => 'Main Course', 'price' => 650, 'available_from' => '17:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/beef-steak.jpg'],
            ['name' => 'Pasta Carbonara', 'category' => 'Main Course', 'price' => 380, 'available_from' => '11:00', 'available_to' => '22:00', 'status' => 'available', 'image' => 'dining/pasta-carbonara.jpg'],
            ['name' => 'Seafood Rice Bowl', 'category' => 'Main Course', 'price' => 460, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/seafood-rice-bowl.jpg'],
            ['name' => 'Grilled Salmon', 'category' => 'Main Course', 'price' => 560, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/grilled-salmon.jpg'],

            // Soup
            ['name' => 'Chicken Noodle Soup', 'category' => 'Soup', 'price' => 220, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/chicken-noodle-soup.jpg'],
            ['name' => 'Vegetable Soup', 'category' => 'Soup', 'price' => 200, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/vegetable-soup.jpg'],
            ['name' => 'Tomato Basil Soup', 'category' => 'Soup', 'price' => 210, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/tomato-basil-soup.jpg'],

            // Salad
            ['name' => 'Garden Salad', 'category' => 'Salad', 'price' => 220, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/garden-salad.jpg'],
            ['name' => 'Caesar Salad', 'category' => 'Salad', 'price' => 260, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/caesar-salad.jpg'],
            ['name' => 'Fruit Salad', 'category' => 'Salad', 'price' => 230, 'available_from' => '11:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/fruit-salad.jpg'],

            // Dessert
            ['name' => 'Chocolate Lava Cake', 'category' => 'Dessert', 'price' => 260, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/chocolate-lava-cake.jpg'],
            ['name' => 'Mango Float', 'category' => 'Dessert', 'price' => 240, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/mango-float.jpg'],
            ['name' => 'Leche Flan', 'category' => 'Dessert', 'price' => 250, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/leche-flan.jpg'],
            ['name' => 'Cheesecake Slice', 'category' => 'Dessert', 'price' => 270, 'available_from' => '12:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/cheesecake-slice.jpg'],

            // Beverage
            ['name' => 'Fresh Lemonade', 'category' => 'Beverage', 'price' => 140, 'available_from' => '07:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/fresh-lemonade.jpg'],
            ['name' => 'Iced Tea', 'category' => 'Beverage', 'price' => 120, 'available_from' => '07:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/iced-tea.jpg'],
            ['name' => 'Café Latte', 'category' => 'Beverage', 'price' => 180, 'available_from' => '07:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/cafe-latte.jpg'],
            ['name' => 'Fruit Shake', 'category' => 'Beverage', 'price' => 170, 'available_from' => '07:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/fruit-shake.jpg'],
            ['name' => 'Sparkling Water', 'category' => 'Beverage', 'price' => 100, 'available_from' => '07:00', 'available_to' => '21:00', 'status' => 'available', 'image' => 'dining/sparkling-water.jpg'],
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
            'Continental Breakfast',
            'Tocino Silog',
            'Longganisa Breakfast Set',
            'Crispy Calamari',
            'Buffalo Wings',
            'Cheese Sticks',
            'Nacho Platter',
            'Chicken Adobo',
            'Grilled Chicken',
            'Beef Steak',
            'Pasta Carbonara',
            'Seafood Rice Bowl',
            'Grilled Salmon',
            'Chicken Noodle Soup',
            'Vegetable Soup',
            'Tomato Basil Soup',
            'Garden Salad',
            'Caesar Salad',
            'Fruit Salad',
            'Chocolate Lava Cake',
            'Mango Float',
            'Leche Flan',
            'Cheesecake Slice',
            'Fresh Lemonade',
            'Iced Tea',
            'Café Latte',
            'Fruit Shake',
            'Sparkling Water',
        ])->delete();
    }
};