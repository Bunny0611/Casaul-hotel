<?php

namespace Tests\Feature;

use App\Models\Room;
use Tests\TestCase;

class ChatbotAvailabilityTest extends TestCase
{
    public function test_chatbot_lists_each_available_room_when_user_asks_for_room_availability(): void
    {
        Room::query()->delete();

        Room::create([
            'room_number' => '101',
            'room_type' => 'Deluxe',
            'status' => 'available',
            'price' => 2500,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Deluxe room',
        ]);

        Room::create([
            'room_number' => '102',
            'room_type' => 'Standard',
            'status' => 'available',
            'price' => 1800,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Standard room',
        ]);

        Room::create([
            'room_number' => '103',
            'room_type' => 'Suite',
            'status' => 'occupied',
            'price' => 3500,
            'floor' => 2,
            'capacity' => 3,
            'cleaning_status' => 'clean',
            'description' => 'Occupied room',
        ]);

        $response = $this->postJson('/chatbot/message', ['message' => 'room availability']);

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Currently available rooms', $content);
        $this->assertStringContainsString('Room 101', $content);
        $this->assertStringContainsString('Price: ', $content);
        $this->assertStringContainsString('2,500.00', $content);
        $this->assertStringContainsString('Room 102', $content);
        $this->assertStringContainsString('1,800.00', $content);
        $this->assertStringNotContainsString('Room 103', $content);
    }

    public function test_chatbot_lists_available_deluxe_rooms_with_prices_when_user_types_deluxe(): void
    {
        Room::query()->delete();

        Room::create([
            'room_number' => '201',
            'room_type' => 'Deluxe Room',
            'status' => 'available',
            'price' => 2800,
            'floor' => 2,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Deluxe room 1',
        ]);

        Room::create([
            'room_number' => '208',
            'room_type' => 'Deluxe Room',
            'status' => 'available',
            'price' => 2900,
            'floor' => 2,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Deluxe room 2',
        ]);

        Room::create([
            'room_number' => '301',
            'room_type' => 'Executive Room',
            'status' => 'available',
            'price' => 4200,
            'floor' => 3,
            'capacity' => 3,
            'cleaning_status' => 'clean',
            'description' => 'Executive room',
        ]);

        $response = $this->postJson('/chatbot/message', ['message' => 'deluxe']);

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Available Deluxe rooms', $content);
        $this->assertStringContainsString('Room 201', $content);
        $this->assertStringContainsString('Room 208', $content);
        $this->assertStringContainsString('2,800.00', $content);
        $this->assertStringContainsString('2,900.00', $content);
        $this->assertStringNotContainsString('Executive', $content);
    }

    public function test_chatbot_lists_available_standard_rooms_with_prices_when_user_types_standard(): void
    {
        Room::query()->delete();

        Room::create([
            'room_number' => '101',
            'room_type' => 'Standard Room',
            'status' => 'available',
            'price' => 1800,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Standard room 1',
        ]);

        Room::create([
            'room_number' => '110',
            'room_type' => 'Standard Room',
            'status' => 'available',
            'price' => 2200,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Standard room 2',
        ]);

        Room::create([
            'room_number' => '205',
            'room_type' => 'Deluxe Room',
            'status' => 'available',
            'price' => 2800,
            'floor' => 2,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Deluxe room',
        ]);

        $response = $this->postJson('/chatbot/message', ['message' => 'standard room']);

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Available Standard rooms', $content);
        $this->assertStringContainsString('Room 101', $content);
        $this->assertStringContainsString('Room 110', $content);
        $this->assertStringContainsString('1,800.00', $content);
        $this->assertStringContainsString('2,200.00', $content);
        $this->assertStringNotContainsString('Room 205', $content);
    }

    public function test_chatbot_includes_standard_rooms_when_user_requests_deluxe_and_standard_is_available(): void
    {
        Room::query()->delete();

        Room::create([
            'room_number' => '201',
            'room_type' => 'Deluxe Room',
            'status' => 'available',
            'price' => 2800,
            'floor' => 2,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Deluxe room 1',
        ]);

        Room::create([
            'room_number' => '101',
            'room_type' => 'Standard Room',
            'status' => 'available',
            'price' => 1800,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Standard room 1',
        ]);

        $response = $this->postJson('/chatbot/message', ['message' => 'deluxe']);

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Available Deluxe rooms', $content);
        $this->assertStringContainsString('Room 201', $content);
        $this->assertStringContainsString('Available Standard rooms', $content);
        $this->assertStringContainsString('Room 101', $content);
    }

    public function test_chatbot_reads_vacant_ready_standard_rooms_as_available(): void
    {
        Room::query()->delete();

        Room::create([
            'room_number' => '110',
            'room_type' => 'Standard Room',
            'status' => 'vacant_ready',
            'price' => 2200,
            'floor' => 1,
            'capacity' => 2,
            'cleaning_status' => 'clean',
            'description' => 'Vacant-ready standard room',
        ]);

        $response = $this->postJson('/chatbot/message', ['message' => 'standard room']);

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringContainsString('Available Standard rooms', $content);
        $this->assertStringContainsString('Room 110', $content);
        $this->assertStringContainsString('2,200.00', $content);
    }
}
