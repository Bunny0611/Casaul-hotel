<?php

namespace Tests\Feature;

use App\Models\Room;
use Tests\TestCase;

class RoomDetailPageTest extends TestCase
{
    public function test_deluxe_room_detail_page_loads(): void
    {
        $response = $this->get('/accommodation/deluxe-room');

        $response->assertStatus(200);
        $response->assertSee('Deluxe Room');
        $response->assertSee('Book This Room');
    }

    public function test_homepage_uses_real_room_data_for_featured_rooms(): void
    {
        Room::query()->delete();

        Room::query()->create([
            'room_number' => '999',
            'room_type' => 'Ocean View Suite',
            'price' => 4200.00,
            'floor' => '9',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'description' => 'A scenic room with a bright, calming interior.',
            'image' => 'ocean-view-suite.jpg',
            'capacity' => 2,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ocean View Suite');
        $response->assertSee('images/ocean-view-suite.jpg');
        $response->assertSee(route('accommodation.room', ['slug' => 'ocean-view-suite']));
    }

    public function test_homepage_has_five_clickable_room_links(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Deluxe Room');
        $response->assertSee('Executive Room');
        $response->assertSee('Presidential Room');
        $response->assertSee('Standard Room');
        $response->assertSee('Garden Suite');
        $response->assertSee(route('accommodation.room', ['slug' => 'deluxe-room']));
    }
}
