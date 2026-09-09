<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schedules = DB::table('dining_schedules')->pluck('id', 'period');
        $menus = [
            ['Filipino Breakfast', 'Breakfast', 250, '07:00', '10:00', 'Traditional Filipino breakfast with garlic rice, egg, and savory meat.', 'dining/filipino-breakfast.jpg'],
            ['Continental Breakfast', 'Breakfast', 280, '07:00', '10:00', 'A light breakfast of eggs, toast, fruit, and freshly brewed coffee.', 'dining/continental-breakfast.jpg'],
            ['Tocino Silog', 'Breakfast', 260, '07:00', '10:00', 'Sweet cured pork served with garlic rice and a sunny-side egg.', 'dining/tocino-silog.jpg'],
            ['Longganisa Breakfast Set', 'Breakfast', 270, '07:00', '10:00', 'House longganisa with garlic rice, egg, and fresh fruit.', 'dining/longganisa-breakfast.jpg'],
            ['Crispy Calamari', 'Appetizer', 320, '12:00', '21:00', 'Tender calamari lightly breaded and served with citrus aioli.', 'dining/crispy-calamari.jpg'],
            ['Buffalo Wings', 'Appetizer', 280, '12:00', '21:00', 'Crispy chicken wings tossed in a bright house buffalo sauce.', 'dining/buffalo-wings.jpg'],
            ['Cheese Sticks', 'Appetizer', 240, '12:00', '21:00', 'Golden fried cheese sticks served with tomato dipping sauce.', 'dining/cheese-sticks.jpg'],
            ['Nacho Platter', 'Appetizer', 340, '12:00', '21:00', 'Crisp tortilla chips layered with cheese, salsa, and savory toppings.', 'dining/nacho-platter.jpg'],
            ['Club Sandwich', 'Main Course', 320, '11:00', '22:00', 'A toasted triple-decker sandwich with chicken, egg, lettuce, and tomato.', 'dining/club-sandwich.jpg'],
            ['Chicken Adobo', 'Main Course', 350, '11:00', '22:00', 'Classic savory Filipino chicken adobo served with steamed rice.', 'dining/chicken-adobo.jpg'],
            ['Grilled Chicken', 'Main Course', 420, '11:00', '22:00', 'Herb-marinated chicken breast grilled and served with seasonal vegetables.', 'dining/grilled-chicken.jpg'],
            ['Beef Steak', 'Main Course', 650, '17:00', '21:00', 'Tender grilled beef steak with pepper sauce and roasted vegetables.', 'dining/beef-steak.jpg'],
            ['Pasta Carbonara', 'Main Course', 380, '11:00', '22:00', 'Creamy pasta with smoked bacon, parmesan, and cracked black pepper.', 'dining/pasta-carbonara.jpg'],
            ['Seafood Rice Bowl', 'Main Course', 460, '12:00', '21:00', 'A hearty rice bowl with fresh seafood, vegetables, and house sauce.', 'dining/seafood-rice-bowl.jpg'],
            ['Grilled Salmon', 'Main Course', 560, '12:00', '21:00', 'Grilled salmon fillet with lemon butter and garden greens.', 'dining/grilled-salmon.jpg'],
            ['Chicken Noodle Soup', 'Soup', 220, '11:00', '21:00', 'Comforting chicken noodle soup with vegetables and fresh herbs.', 'dining/chicken-noodle-soup.jpg'],
            ['Vegetable Soup', 'Soup', 200, '11:00', '21:00', 'A warming seasonal vegetable soup finished with garden herbs.', 'dining/vegetable-soup.jpg'],
            ['Tomato Basil Soup', 'Soup', 210, '11:00', '21:00', 'Slow-simmered tomato soup with basil and a touch of cream.', 'dining/tomato-basil-soup.jpg'],
            ['Garden Salad', 'Salad', 220, '11:00', '21:00', 'Crisp garden greens, cucumber, tomato, and house vinaigrette.', 'dining/garden-salad.jpg'],
            ['Caesar Salad', 'Salad', 260, '11:00', '21:00', 'Crisp romaine, parmesan, croutons, and classic Caesar dressing.', 'dining/caesar-salad.jpg'],
            ['Fruit Salad', 'Salad', 230, '11:00', '21:00', 'Fresh seasonal fruit lightly dressed with citrus and mint.', 'dining/fruit-salad.jpg'],
            ['Chocolate Lava Cake', 'Dessert', 260, '12:00', '21:00', 'Warm chocolate cake with a rich molten center and vanilla cream.', 'dining/chocolate-lava-cake.jpg'],
            ['Mango Float', 'Dessert', 240, '12:00', '21:00', 'Chilled layers of ripe mango, cream, and crisp biscuit.', 'dining/mango-float.jpg'],
            ['Leche Flan', 'Dessert', 250, '12:00', '21:00', 'Silky Filipino caramel custard with a delicate vanilla finish.', 'dining/leche-flan.jpg'],
            ['Cheesecake Slice', 'Dessert', 270, '12:00', '21:00', 'Classic creamy cheesecake served with seasonal fruit.', 'dining/cheesecake-slice.jpg'],
            ['Fresh Lemonade', 'Beverage', 140, '07:00', '21:00', 'Freshly squeezed lemonade served chilled with mint.', 'dining/fresh-lemonade.jpg'],
            ['Iced Tea', 'Beverage', 120, '07:00', '21:00', 'Refreshing house-brewed iced tea with citrus.', 'dining/iced-tea.jpg'],
            ['Cafe Latte', 'Beverage', 180, '07:00', '21:00', 'Smooth espresso with steamed milk and a delicate foam finish.', 'dining/cafe-latte.jpg'],
            ['Fruit Shake', 'Beverage', 170, '07:00', '21:00', 'A chilled blend of fresh seasonal fruit.', 'dining/fruit-shake.jpg'],
            ['Sparkling Water', 'Beverage', 100, '07:00', '21:00', 'Chilled sparkling water served with lemon.', 'dining/sparkling-water.jpg'],
        ];

        foreach ($menus as [$name, $category, $price, $availableFrom, $availableTo, $description, $image]) {
            $schedulePeriod = match ($category) {
                'Breakfast' => 'Breakfast',
                'Main Course' => 'Lunch',
                default => null,
            };

            DB::table('dining_menus')->updateOrInsert(
                ['name' => $name],
                [
                    'category' => $category,
                    'description' => $description,
                    'price' => $price,
                    'status' => 'available',
                    'available_from' => $availableFrom,
                    'available_to' => $availableTo,
                    'image' => $image,
                    'dining_schedule_id' => $schedulePeriod ? $schedules->get($schedulePeriod) : null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('dining_menus')->whereIn('name', [
            'Continental Breakfast', 'Tocino Silog', 'Longganisa Breakfast Set',
            'Crispy Calamari', 'Buffalo Wings', 'Cheese Sticks', 'Nacho Platter',
            'Seafood Rice Bowl', 'Grilled Salmon', 'Chicken Noodle Soup', 'Vegetable Soup',
            'Tomato Basil Soup', 'Garden Salad', 'Caesar Salad', 'Fruit Salad',
            'Chocolate Lava Cake', 'Mango Float', 'Leche Flan', 'Cheesecake Slice',
            'Fresh Lemonade', 'Iced Tea', 'Cafe Latte', 'Fruit Shake', 'Sparkling Water',
        ])->delete();
    }
};
