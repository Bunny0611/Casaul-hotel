<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Facility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomImageRenderingTest extends TestCase
{
    public function test_admin_room_upload_is_stored_in_the_rooms_directory(): void
    {
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'room-image-admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->post(route('admin.rooms.store'), [
            'room_number' => '301',
            'room_type' => 'Standard Room',
            'bed_type' => '1 Queen Bed',
            'price' => 3000,
            'adult_guest_price' => 0,
            'kid_guest_price' => 0,
            'floor' => '3rd',
            'capacity' => 2,
            'image' => UploadedFile::fake()->create('room.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('admin.rooms', ['tab' => 'rooms']));

        $room = Room::where('room_number', '301')->firstOrFail();
        $this->assertStringStartsWith('rooms/', $room->image);
        Storage::disk('public')->assertExists($room->image);
    }

    public function test_admin_facility_upload_is_stored_in_the_catalog_directory(): void
    {
        Storage::fake('public');
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'facility-image-admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->post(route('admin.inventory.store'), [
            'category' => 'facilities',
            'name' => 'Pool',
            'price' => 100,
            'pricing_basis' => 'Per Stay',
            'scheduling_requirement' => 'No Additional Schedule',
            'status' => 'available',
            'image' => UploadedFile::fake()->create('pool.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('admin.rooms', ['tab' => 'facilities']));

        $facility = Facility::where('name', 'Pool')->firstOrFail();
        $this->assertStringStartsWith('catalog/', $facility->image);
        Storage::disk('public')->assertExists($facility->image);

        $previousImage = $facility->image;
        $this->actingAs($admin)->put(route('admin.inventory.update', $facility->id), [
            'category' => 'facilities',
            'name' => 'Pool',
            'price' => 100,
            'pricing_basis' => 'Per Stay',
            'scheduling_requirement' => 'No Additional Schedule',
            'status' => 'available',
            'image' => UploadedFile::fake()->create('updated-pool.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('admin.rooms', ['tab' => 'facilities']));

        $facility->refresh();
        $this->assertStringStartsWith('catalog/', $facility->image);
        $this->assertNotSame($previousImage, $facility->image);
        Storage::disk('public')->assertMissing($previousImage);
        Storage::disk('public')->assertExists($facility->image);
    }

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
