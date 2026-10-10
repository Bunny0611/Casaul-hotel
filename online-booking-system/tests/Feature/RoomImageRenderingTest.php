<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomImageRenderingTest extends TestCase
{
    public function test_reservation_shows_the_uploaded_room_category_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('rooms/standard-room.jpg', 'room image');

        Room::create([
            'room_number' => '101',
            'room_type' => 'Standard Room',
            'bed_type' => '1 Queen Bed',
            'price' => 3000,
            'floor' => '1st',
            'status' => 'available',
            'capacity' => 2,
            'image' => 'storage/rooms/standard-room.jpg',
        ]);

        $response = $this->actingAs(Guest::factory()->create(), 'guest')
            ->get(route('reservation'));

        $response->assertOk();
        $response->assertSee(asset('storage/rooms/standard-room.jpg'), false);
    }
}
