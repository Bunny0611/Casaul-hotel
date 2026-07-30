<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_reservation(): void
    {
        $room = Room::create([
            'room_number' => '101',
            'room_type' => 'Deluxe',
            'price' => 1500.00,
            'floor' => '1st',
            'capacity' => 2,
            'description' => 'Comfortable room',
            'status' => 'available',
        ]);

        $response = $this->post(route('admin.reservations.store'), [
            'room_id' => $room->id,
            'guest_name' => 'Jane Doe',
            'guest_email' => 'jane@example.com',
            'guest_phone' => '09171234567',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'status' => 'pending',
            'total_amount' => 3000.00,
            'special_requests' => 'Late check-in',
        ]);

        $response->assertRedirect(route('admin.reservations'));
        $response->assertSessionHas('success', 'Reservation created successfully!');
        $this->assertDatabaseHas('reservations', [
            'guest_email' => 'jane@example.com',
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $reservation = Reservation::latest()->first();
        $this->assertInstanceOf(Carbon::class, $reservation->check_in);
        $this->assertInstanceOf(Carbon::class, $reservation->check_out);
        $this->assertCount(1, Reservation::all());
    }
}
