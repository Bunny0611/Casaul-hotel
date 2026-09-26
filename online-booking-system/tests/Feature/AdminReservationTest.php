<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Staff;
use App\Models\RoomReservation;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_formula_uses_paid_amount_and_reduction_amount(): void
    {
        $controller = new \App\Http\Controllers\AdminController();
        $method = new \ReflectionMethod($controller, 'calculateRefundAmount');
        $method->setAccessible(true);

        $this->assertSame(500.0, $method->invoke($controller, 3000.0, 2500.0, 3000.0));
        $this->assertSame(0.0, $method->invoke($controller, 3000.0, 2500.0, 2000.0));
        $this->assertSame(700.0, $method->invoke($controller, 4000.0, 2500.0, 3200.0));
        $this->assertSame(0.0, $method->invoke($controller, 3000.0, 3500.0, 3000.0));
        $this->assertSame(0.0, $method->invoke($controller, 3000.0, 2500.0, 0.0));
    }

    public function test_room_pricing_uses_separate_adult_and_kid_rates(): void
    {
        $room = Room::create([
            'room_number' => '104',
            'room_type' => 'Deluxe Room',
            'price' => 1000,
            'floor' => '1st',
            'status' => 'available',
            'capacity' => 2,
        ]);

        $this->assertSame(
            5925.0,
            \App\Support\ReservationPricing::room($room, '2026-09-19', '2026-09-22', 4, 1, 1)
        );
        $this->assertSame(
            1975.0,
            \App\Support\ReservationPricing::room($room, '2026-09-19', '2026-09-20', 4, 1, 1)
        );
    }

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

    public function test_public_booking_rejects_overlapping_room_reservation(): void
    {
        $guest = Guest::factory()->create();
        $this->actingAs($guest, 'guest');
        $room = Room::create([
            'room_number' => '204',
            'room_type' => 'Deluxe',
            'price' => 2500.00,
            'floor' => '2nd',
            'capacity' => 2,
            'description' => 'Deluxe room',
            'status' => 'available',
        ]);

        $booking = [
            'room_id' => $room->id,
            'guest_name' => 'First Guest',
            'guest_email' => 'first@example.com',
            'guest_phone' => '09191234570',
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
            'total_amount' => 5000.00,
            'payment_method' => 'Cash / Pay at Hotel',
        ];

        $this->post(route('reservation.store'), $booking)->assertRedirect(route('reservation'));

        $response = $this->post(route('reservation.store'), [
            ...$booking,
            'guest_name' => 'Second Guest',
            'guest_email' => 'second@example.com',
            'guest_phone' => '09191234571',
        ]);

        $response->assertSessionHasErrors([
            'room_id' => 'Sorry, this room is no longer available for your selected dates. Please choose another room.',
        ]);
        $this->assertDatabaseCount('room_reservations', 1);
        $this->assertDatabaseHas('room_reservations', [
            'guest_email' => $guest->email,
            'room_id' => $room->id,
        ]);
    }

    public function test_extension_options_report_future_reservation_and_available_transfer_room(): void
    {
        $this->actingAs(Staff::factory()->create(['role' => 'employee']));
        $room = Room::create([
            'room_number' => '203',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'occupied',
        ]);
        $availableRoom = Room::create([
            'room_number' => '204',
            'room_type' => 'Deluxe',
            'price' => 1800,
            'floor' => '2nd',
            'capacity' => 4,
            'bed_type' => '2 Queen Beds',
            'status' => 'available',
        ]);
        $current = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Guest A',
            'guest_email' => 'guest-a@example.com',
            'guest_phone' => '09190000001',
            'check_in' => today()->subDays(1),
            'check_out' => today()->addDay(),
            'number_of_guests' => 2,
            'status' => 'checked-in',
            'total_amount' => 5000,
        ]);
        RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Guest B',
            'guest_email' => 'guest-b@example.com',
            'guest_phone' => '09190000002',
            'check_in' => today()->addDay(),
            'check_out' => today()->addDays(3),
            'number_of_guests' => 2,
            'status' => 'confirmed',
            'total_amount' => 5000,
        ]);

        $this->get(route('employee.reservation'))
            ->assertOk()
            ->assertSee('Extend Stay');

        $response = $this->getJson(route('employee.reservations.extension-options', $current->id) . '?check_out=' . today()->addDays(3)->toDateString());

        $response->assertOk()
            ->assertJsonPath('current_room_available', false)
            ->assertJsonPath('conflict.guest_name', 'Guest B')
            ->assertJsonPath('conflict.check_in', today()->addDay()->toDateString());
        $this->assertContains(
            (int) $availableRoom->id,
            array_map('intval', collect($response->json('available_rooms'))->pluck('id')->all())
        );
    }

    public function test_extension_cannot_overwrite_future_reservation(): void
    {
        $this->actingAs(Staff::factory()->create(['role' => 'employee']));
        $room = Room::create([
            'room_number' => '203',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'occupied',
        ]);
        $current = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Guest A',
            'guest_email' => 'guest-a@example.com',
            'guest_phone' => '09190000001',
            'check_in' => today()->subDays(1),
            'check_out' => today()->addDay(),
            'number_of_guests' => 2,
            'status' => 'checked-in',
            'total_amount' => 5000,
        ]);
        $future = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Guest B',
            'guest_email' => 'guest-b@example.com',
            'guest_phone' => '09190000002',
            'check_in' => today()->addDay(),
            'check_out' => today()->addDays(3),
            'number_of_guests' => 2,
            'status' => 'confirmed',
            'total_amount' => 5000,
        ]);

        $this->from(route('employee.reservation'))
            ->post(route('employee.reservations.extend', $current->id), [
                'check_out' => today()->addDays(3)->toDateString(),
                'room_id' => $room->id,
            ])
            ->assertRedirect(route('employee.reservation'))
            ->assertSessionHasErrors('room_id');

        $this->assertSame(today()->addDay()->toDateString(), $current->fresh()->check_out->toDateString());
        $this->assertSame(today()->addDays(3)->toDateString(), $future->fresh()->check_out->toDateString());
        $this->assertSame('confirmed', $future->fresh()->status);
        $this->assertDatabaseCount('room_reservations', 2);
    }

    public function test_extension_transfer_creates_linked_room_segment_and_adds_only_extension_charge(): void
    {
        $this->actingAs(Staff::factory()->create(['role' => 'employee']));
        $currentRoom = Room::create([
            'room_number' => '203',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'occupied',
        ]);
        $nextRoom = Room::create([
            'room_number' => '204',
            'room_type' => 'Deluxe',
            'price' => 1800,
            'floor' => '2nd',
            'capacity' => 4,
            'status' => 'available',
        ]);
        $current = RoomReservation::create([
            'room_id' => $currentRoom->id,
            'guest_name' => 'Guest A',
            'guest_email' => 'guest-a@example.com',
            'guest_phone' => '09190000001',
            'check_in' => today()->subDay(),
            'check_out' => today()->addDay(),
            'number_of_guests' => 2,
            'status' => 'checked-in',
            'total_amount' => 5000,
            'amount_paid' => 700,
        ]);

        $this->post(route('employee.reservations.extend', $current->id), [
            'check_out' => today()->addDays(3)->toDateString(),
            'room_id' => $nextRoom->id,
        ])->assertRedirect(route('employee.reservation'));

        $current->refresh();
        $extension = RoomReservation::where('stay_group_id', $current->stay_group_id)
            ->whereKeyNot($current->id)
            ->firstOrFail();
        $this->assertSame(today()->addDay()->toDateString(), $current->check_out->toDateString());
        $this->assertSame(5000.0, (float) $current->total_amount);
        $this->assertSame(700.0, (float) $current->amount_paid);
        $this->assertSame($nextRoom->id, $extension->room_id);
        $this->assertSame(today()->addDay()->toDateString(), $extension->check_in->toDateString());
        $this->assertSame(today()->addDays(3)->toDateString(), $extension->check_out->toDateString());
        $this->assertSame(3600.0, (float) $extension->total_amount);
        $this->assertSame(0.0, (float) $extension->amount_paid);
        $this->assertSame($current->stay_group_id, $extension->stay_group_id);
    }

    public function test_room_availability_uses_stay_dates_and_keeps_occupied_rooms_listed(): void
    {
        $room = Room::create([
            'room_number' => '205',
            'room_type' => 'Standard',
            'price' => 1800,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'occupied',
        ]);
        RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Future Guest',
            'guest_email' => 'future@example.com',
            'guest_phone' => '09190000003',
            'check_in' => today()->addDays(10),
            'check_out' => today()->addDays(13),
            'number_of_guests' => 2,
            'status' => 'confirmed',
            'total_amount' => 5400,
        ]);

        $page = app(HomeController::class)->reservation();
        $listedRooms = $page->getData()['rooms'];
        $this->assertTrue($listedRooms->contains('id', $room->id));

        $available = app(HomeController::class)->roomAvailability(Request::create('/reservation/availability', 'GET', [
            'check_in' => today()->addDays(3)->toDateString(),
            'check_out' => today()->addDays(6)->toDateString(),
        ]))->getData(true);
        $overlapping = app(HomeController::class)->roomAvailability(Request::create('/reservation/availability', 'GET', [
            'check_in' => today()->addDays(11)->toDateString(),
            'check_out' => today()->addDays(12)->toDateString(),
        ]))->getData(true);

        $this->assertTrue(collect($available['rooms'])->firstWhere('id', $room->id)['available']);
        $this->assertFalse(collect($overlapping['rooms'])->firstWhere('id', $room->id)['available']);
    }

    public function test_same_room_extension_adds_room_charge_without_changing_amount_paid(): void
    {
        $this->actingAs(Staff::factory()->create(['role' => 'employee']));
        $room = Room::create([
            'room_number' => '206',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'occupied',
        ]);
        $current = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Guest A',
            'guest_email' => 'guest-a@example.com',
            'guest_phone' => '09190000001',
            'check_in' => today()->subDay(),
            'check_out' => today()->addDay(),
            'number_of_guests' => 2,
            'status' => 'checked-in',
            'total_amount' => 5000,
            'amount_paid' => 700,
        ]);

        $this->post(route('employee.reservations.extend', $current->id), [
            'check_out' => today()->addDays(3)->toDateString(),
            'room_id' => $room->id,
        ])->assertRedirect(route('employee.reservation'));

        $this->assertSame(today()->addDays(3)->toDateString(), $current->fresh()->check_out->toDateString());
        $this->assertSame(10000.0, (float) $current->fresh()->total_amount);
        $this->assertSame(700.0, (float) $current->fresh()->amount_paid);
        $this->assertDatabaseCount('room_reservations', 1);
    }

    public function test_public_booking_can_create_an_event_reservation(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'event@example.com',
            'name' => 'Event Guest',
            'contact_no' => '09191234569',
        ]);

        $event = \App\Models\Event::create([
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
        $response = $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'category' => 'event',
            'event_id' => $event->id,
            'event_type' => 'Wedding',
            'guest_name' => 'Event Guest',
            'guest_email' => 'event@example.com',
            'guest_phone' => '09191234569',
            'check_in' => $eventDate,
            'check_out' => $eventDate,
            'check_in_time' => '10:00',
            'check_out_time' => '18:00',
            'event_start_time' => '10:00',
            'event_end_time' => '18:00',
            'number_of_guests' => 50,
            'total_amount' => 25000,
            'payment_method' => 'Cash / Pay at Hotel',
        ]);

        $response->assertRedirect(route('reservation'));
        $this->assertDatabaseHas('event_reservations', [
            'event_id' => $event->id,
            'guest_email' => 'event@example.com',
            'number_of_guests' => 50,
        ]);
    }

    public function test_public_booking_rejects_same_day_event_reservation(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'late@example.com',
            'name' => 'Late Guest',
            'contact_no' => '09191234568',
        ]);

        $event = \App\Models\Event::create([
            'name' => 'Same Day Event',
            'event_type' => 'Birthday',
            'description' => 'No same-day bookings',
            'price' => 15000,
            'pricing_basis' => 'Per Event',
            'capacity' => 40,
            'location' => 'Pool Deck',
            'status' => 'available',
        ]);

        $response = $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'category' => 'event',
            'event_id' => $event->id,
            'event_type' => 'Birthday',
            'guest_name' => 'Late Guest',
            'guest_email' => 'late@example.com',
            'guest_phone' => '09191234568',
            'check_in' => now()->format('Y-m-d'),
            'check_out' => now()->format('Y-m-d'),
            'check_in_time' => '09:00',
            'check_out_time' => '15:00',
            'event_start_time' => '09:00',
            'event_end_time' => '15:00',
            'number_of_guests' => 20,
            'total_amount' => 15000,
            'payment_method' => 'Cash / Pay at Hotel',
        ]);

        $response->assertSessionHasErrors('check_in');
        $this->assertDatabaseMissing('event_reservations', [
            'guest_email' => 'late@example.com',
        ]);
    }
}
