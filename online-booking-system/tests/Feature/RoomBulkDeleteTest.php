<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'bulkadmin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    private function seedRooms(): void
    {
        Room::create(['room_number' => '101', 'room_type' => 'Deluxe Room', 'price' => 5000, 'floor' => '1', 'capacity' => 2, 'status' => 'available']);
        Room::create(['room_number' => '102', 'room_type' => 'Deluxe Room', 'price' => 5500, 'floor' => '1', 'capacity' => 2, 'status' => 'available']);
        Room::create(['room_number' => '201', 'room_type' => 'Standard Room', 'price' => 3000, 'floor' => '2', 'capacity' => 2, 'status' => 'available']);
    }

    public function test_bulk_delete_multiple_rooms(): void
    {
        $this->seedRooms();

        $response = $this->actingAs($this->admin())->delete(route('admin.rooms.bulkDestroy'), [
            'room_ids' => '1,2',
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $this->assertEquals(1, Room::count());
        $this->assertDatabaseMissing('rooms', ['id' => 1]);
        $this->assertDatabaseMissing('rooms', ['id' => 2]);
        $this->assertDatabaseHas('rooms', ['id' => 3]);
    }

    public function test_bulk_delete_all_rooms_via_select_all(): void
    {
        $this->seedRooms();

        $allIds = Room::pluck('id')->join(',');

        $response = $this->actingAs($this->admin())->delete(route('admin.rooms.bulkDestroy'), [
            'room_ids' => $allIds,
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $this->assertEquals(0, Room::count());
    }

    public function test_bulk_delete_accepts_array_of_ids(): void
    {
        $this->seedRooms();

        $response = $this->actingAs($this->admin())->delete(route('admin.rooms.bulkDestroy'), [
            'room_ids' => ['1', '3'],
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $this->assertEquals(1, Room::count());
        $this->assertDatabaseHas('rooms', ['id' => 2]);
    }

    public function test_bulk_delete_with_no_selection_does_not_error(): void
    {
        $this->seedRooms();

        $response = $this->actingAs($this->admin())->delete(route('admin.rooms.bulkDestroy'), [
            'room_ids' => '',
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $this->assertEquals(3, Room::count());
    }

    public function test_bulk_delete_ignores_invalid_ids(): void
    {
        $this->seedRooms();

        $response = $this->actingAs($this->admin())->delete(route('admin.rooms.bulkDestroy'), [
            'room_ids' => '1,not-a-number,,9999',
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $this->assertEquals(2, Room::count());
    }
}
