<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rooms_are_paginated_five_per_page(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'pagination-admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Room::create([
                'room_number' => '10' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'room_type' => 'Deluxe Room',
                'price' => 5000,
                'floor' => '1',
                'capacity' => 2,
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.rooms'));

        $response->assertOk();
        $response->assertSee('Next');
        $this->assertStringContainsString('page=2', $response->getContent());

        $rows = substr_count($response->getContent(), 'room-checkbox h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500');
        $this->assertEquals(5, $rows);
    }

    public function test_rooms_pagination_page_two_shows_remaining_rooms(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'pagination-admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 7; $i++) {
            Room::create([
                'room_number' => '10' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'room_type' => 'Deluxe Room',
                'price' => 5000,
                'floor' => '1',
                'capacity' => 2,
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.rooms', ['page' => 2]));

        $response->assertOk();
        $response->assertSee('Previous');
        $rows = substr_count($response->getContent(), 'room-checkbox h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500');
        $this->assertEquals(2, $rows);
    }

    public function test_rooms_no_pagination_when_five_or_fewer(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'pagination-admin@casaul.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Room::create([
                'room_number' => '10' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'room_type' => 'Deluxe Room',
                'price' => 5000,
                'floor' => '1',
                'capacity' => 2,
                'status' => 'available',
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.rooms'));

        $response->assertOk();
        $this->assertStringNotContainsString('page=2', $response->getContent());
    }
}
