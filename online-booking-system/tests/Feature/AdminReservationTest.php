<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_reservation(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'web');

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
            'category' => 'rooms',
            'payment_method' => 'Cash / Pay at Hotel',
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

    public function test_admin_can_create_a_dining_reservation_with_upon_arriving_menu_option(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'web');

        $response = $this->post(route('admin.reservations.store'), [
            'category' => 'dining',
            'guest_name' => 'John Smith',
            'guest_email' => 'john@example.com',
            'guest_phone' => '09181234567',
            'dining_area' => 'Table 1',
            'dining_schedule' => 'Dinner',
            'dining_id' => 'upon_arriving',
            'check_in' => '2026-08-05',
            'check_out' => '2026-08-05',
            'payment_method' => 'Cash / Pay at Hotel',
            'total_amount' => 450.00,
            'special_requests' => 'No preference',
        ]);

        $response->assertRedirect(route('admin.reservations'));
        $this->assertDatabaseHas('reservations', [
            'guest_email' => 'john@example.com',
            'dining_area' => 'Table 1',
            'dining_schedule' => 'Dinner',
            'dining_id' => null,
        ]);
    }

    public function test_public_booking_saves_the_selected_payment_method_and_details(): void
    {
        $room = Room::create([
            'room_number' => '202',
            'room_type' => 'Suite',
            'price' => 3200.00,
            'floor' => '2nd',
            'capacity' => 4,
            'description' => 'Suite room',
            'status' => 'available',
        ]);

        $tomorrow = now()->addDay()->format('Y-m-d');
        $dayAfter = now()->addDays(2)->format('Y-m-d');

        $response = $this->post(route('reservation.store'), [
            'room_id' => $room->id,
            'guest_name' => 'Guest User',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '09191234567',
            'check_in' => $tomorrow,
            'check_out' => $dayAfter,
            'total_amount' => 6400.00,
            'payment_method' => 'GCash',
            'amount_paid' => 123.00,
            'payment_details' => 'Account: Guest User • Number: 09191234567 • Amount: ₱123.00 • Reference: G-REF-1001',
            'special_requests' => 'Late arrival',
        ]);

        $response->assertRedirect(route('reservation'));
        $this->assertDatabaseHas('reservations', [
            'guest_email' => 'guest@example.com',
            'room_id' => $room->id,
            'payment_method' => 'GCash',
            'payment_details' => 'Account: Guest User • Number: 09191234567 • Amount: ₱123.00 • Reference: G-REF-1001',
            'amount_paid' => 123.00,
        ]);

        $reservation = Reservation::where('guest_email', 'guest@example.com')->first();
        $this->assertNotNull($reservation);
        $this->assertSame(123.0, (float) $reservation->amount_paid);
    }

    public function test_public_booking_marks_pay_at_hotel_as_unpaid(): void
    {
        $room = Room::create([
            'room_number' => '203',
            'room_type' => 'Deluxe',
            'price' => 2500.00,
            'floor' => '2nd',
            'capacity' => 2,
            'description' => 'Deluxe room',
            'status' => 'available',
        ]);

        $response = $this->post(route('reservation.store'), [
            'room_id' => $room->id,
            'guest_name' => 'Pay Later Guest',
            'guest_email' => 'paylater@example.com',
            'guest_phone' => '09191234568',
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
            'total_amount' => 5000.00,
            'payment_method' => 'Cash / Pay at Hotel',
            'amount_paid' => 5000.00,
        ]);

        $response->assertRedirect(route('reservation'));
        $this->assertDatabaseHas('room_reservations', [
            'guest_email' => 'paylater@example.com',
            'payment_method' => 'Cash / Pay at Hotel',
            'amount_paid' => 0,
        ]);
    }

    public function test_public_booking_can_create_an_event_place_reservation(): void
    {
        $eventPlace = \App\Models\EventPlace::create([
            'name' => 'Garden Hall',
            'event_type' => 'Wedding',
            'description' => 'Outdoor venue',
            'price' => 25000,
            'pricing_basis' => 'Per Event',
            'capacity' => 80,
            'location' => 'Garden',
            'status' => 'available',
        ]);

        $eventDate = now()->addDay()->format('Y-m-d');
        $response = $this->post(route('reservation.store'), [
            'category' => 'event_place',
            'event_place_id' => $eventPlace->id,
            'event_type' => 'Wedding',
            'guest_name' => 'Event Guest',
            'guest_email' => 'event@example.com',
            'guest_phone' => '09191234569',
            'check_in' => $eventDate,
            'check_out' => $eventDate,
            'check_in_time' => '10:00',
            'check_out_time' => '18:00',
            'number_of_guests' => 50,
            'total_amount' => 25000,
            'payment_method' => 'Cash / Pay at Hotel',
        ]);

        $response->assertRedirect(route('reservation'));
        $this->assertDatabaseHas('event_reservations', [
            'event_place_id' => $eventPlace->id,
            'guest_email' => 'event@example.com',
            'number_of_guests' => 50,
        ]);
    }
}
