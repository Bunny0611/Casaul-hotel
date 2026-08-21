<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationInventorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_page_uses_admin_inventory_items_for_all_tabs(): void
    {
        Room::create([
            'room_number' => '101',
            'room_type' => 'Deluxe',
            'price' => 2500.00,
            'floor' => '1st',
            'capacity' => 2,
            'description' => 'Comfortable room',
            'status' => 'available',
        ]);

        InventoryItem::create([
            'category' => 'amenities',
            'name' => 'Pool Pass',
            'type' => 'wellness',
            'description' => 'Pool access for two guests.',
            'price' => 1000,
            'status' => 'available',
            'location' => 'Pool Deck',
            'capacity' => 2,
        ]);

        InventoryItem::create([
            'category' => 'event_place',
            'name' => 'Garden Hall',
            'type' => 'wedding',
            'description' => 'Outdoor venue with garden view.',
            'price' => 25000,
            'status' => 'available',
            'location' => 'Garden',
            'capacity' => 80,
        ]);

        InventoryItem::create([
            'category' => 'dining',
            'name' => 'Sunset Dinner',
            'type' => 'dinner',
            'description' => 'Chef-inspired dinner course.',
            'price' => 1800,
            'status' => 'available',
            'location' => 'Rooftop',
            'capacity' => 4,
        ]);

        $response = $this->get(route('reservation'));

        $response->assertOk();
        $response->assertSee('Pool Pass');
        $response->assertSee('Garden Hall');
        $response->assertSee('Sunset Dinner');
    }

    public function test_reservation_page_displays_inventory_images_when_available(): void
    {
        Room::create([
            'room_number' => '202',
            'room_type' => 'Suite',
            'price' => 3000.00,
            'floor' => '2nd',
            'capacity' => 3,
            'description' => 'Luxury suite',
            'status' => 'available',
        ]);

        InventoryItem::create([
            'category' => 'amenities',
            'name' => 'Spa Access',
            'type' => 'wellness',
            'description' => 'Spa access for guests.',
            'price' => 1200,
            'status' => 'available',
            'image' => 'inventory/spa.jpg',
        ]);

        $response = $this->get(route('reservation'));

        $response->assertOk();
        $response->assertSee('inventory/spa.jpg');
    }
}
