<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomReservation;
use Tests\TestCase;

class AccommodationRoomSearchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Room::query()->delete();
    }

    public function test_deluxe_search_excludes_other_types_and_rooms_reserved_for_the_dates(): void
    {
        $availableDeluxe = $this->createRoom('201', 'Deluxe Room', 2);
        $reservedDeluxe = $this->createRoom('202', 'Deluxe Room', 4);
        $standard = $this->createRoom('101', 'Standard Room', 2);
        [$checkIn, $checkOut] = $this->stayDates();

        RoomReservation::create([
            'room_id' => $reservedDeluxe->id,
            'guest_name' => 'Existing Guest',
            'guest_email' => 'existing@example.com',
            'guest_phone' => '09123456789',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_guests' => 2,
            'status' => 'confirmed',
            'total_amount' => 9000,
        ]);

        $response = $this->get(route('accommodation', [
            'search' => 1,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
            'room_type' => 'Deluxe Room',
        ]));

        $response->assertOk();
        $response->assertSee('Room ' . $availableDeluxe->room_number);
        $response->assertDontSee('Room ' . $reservedDeluxe->room_number);
        $response->assertDontSee('Room ' . $standard->room_number);
    }

    public function test_standard_search_filters_by_capacity_and_shows_empty_state_when_none_match(): void
    {
        $tooSmall = $this->createRoom('101', 'Standard Room', 2);
        $matchingStandard = $this->createRoom('102', 'Standard Room', 4);
        $wrongType = $this->createRoom('201', 'Deluxe Room', 4);
        [$checkIn, $checkOut] = $this->stayDates();

        $response = $this->get(route('accommodation', [
            'search' => 1,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 3,
            'room_type' => 'Standard Room',
        ]));

        $response->assertOk();
        $response->assertSee('Room ' . $matchingStandard->room_number);
        $response->assertDontSee('Room ' . $tooSmall->room_number);
        $response->assertDontSee('Room ' . $wrongType->room_number);

        $emptyResponse = $this->get(route('accommodation', [
            'search' => 1,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 5,
            'room_type' => 'Standard Room',
        ]));

        $emptyResponse->assertOk();
        $emptyResponse->assertSee('No rooms available');
        $emptyResponse->assertSee('No rooms match your selected dates, guest count, and room type.');
        $emptyResponse->assertDontSee('Room ' . $tooSmall->room_number);
        $emptyResponse->assertDontSee('Room ' . $matchingStandard->room_number);
    }

    private function createRoom(string $number, string $type, int $capacity): Room
    {
        return Room::create([
            'room_number' => $number,
            'room_type' => $type,
            'bed_type' => '1 Queen Bed',
            'price' => 3000,
            'floor' => '1st',
            'capacity' => $capacity,
            'status' => 'available',
        ]);
    }

    private function stayDates(): array
    {
        return [now()->addDays(2)->toDateString(), now()->addDays(4)->toDateString()];
    }
}