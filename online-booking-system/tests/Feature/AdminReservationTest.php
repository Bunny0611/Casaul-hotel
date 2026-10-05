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
use Illuminate\Support\Facades\Storage;
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

    public function test_facility_pricing_uses_the_selected_basis_and_duration(): void
    {
        $hourlyFacility = new \App\Models\Facility(['price' => 100, 'pricing_basis' => 'Per Hour']);
        $dailyFacility = new \App\Models\Facility(['price' => 100, 'pricing_basis' => 'Per Day']);
        $personFacility = new \App\Models\Facility(['price' => 100, 'pricing_basis' => 'Per Person']);
        $combinedFacility = new \App\Models\Facility(['price' => 100, 'pricing_basis' => 'Per Stay + Per Vehicle']);

        $this->assertSame(300.0, \App\Support\ReservationPricing::facilities(collect([$hourlyFacility]), 1, '2026-09-29', '2026-09-29', 3));
        $this->assertSame(200.0, \App\Support\ReservationPricing::facilities(collect([$dailyFacility]), 1, '2026-09-29', '2026-09-29', 25));
        $this->assertSame(400.0, \App\Support\ReservationPricing::facilities(collect([$personFacility]), 4, '2026-09-29', '2026-09-29', 1));
        $this->assertSame(500.0, \App\Support\ReservationPricing::facilities(collect([$combinedFacility]), 3, '2026-09-29', '2026-10-01', 1));
    }

    public function test_employee_facility_table_displays_facility_time_and_quantity(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee']);
        $facility = \App\Models\Facility::create([
            'name' => 'Conference Room Test',
            'price' => 1500,
            'pricing_basis' => 'Per Stay',
            'status' => 'available',
        ]);
        \App\Models\FacilityReservation::create([
            'facility_id' => $facility->id,
            'facility_quantity' => 3,
            'guest_name' => 'Facility Table Guest',
            'guest_email' => 'facility-table@example.com',
            'guest_phone' => '09191234589',
            'check_in' => '2026-10-01',
            'facility_start_time' => '15:00',
            'check_out' => '2026-10-01',
            'facility_end_time' => '17:00',
            'number_of_guests' => 3,
            'status' => 'confirmed',
            'total_amount' => 1500,
        ]);

        $this->actingAs($employee)
            ->get(route('employee.reservation'))
            ->assertOk()
            ->assertSeeText('3:00 PM - 5:00 PM')
            ->assertSeeText('Quantity: 3');
    }

    public function test_reservation_confirmation_email_uses_the_booked_room_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('rooms/102.jpg', 'room image');

        $reservation = new Reservation(['guest_name' => 'Jane Doe']);
        $reservation->setRelation('room', new Room([
            'room_type' => 'Deluxe Room',
            'image' => 'rooms/102.jpg',
        ]));

        $html = (string) app(\Illuminate\Mail\Markdown::class)
            ->render('emails.reservation-confirmed', compact('reservation'));

        $this->assertStringContainsString(asset('storage/rooms/102.jpg'), $html);
        $this->assertStringContainsString('alt="Deluxe Room"', $html);
        $this->assertStringNotContainsString(asset('image/Royal-Suite-room.jpg'), $html);
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

    public function test_public_facility_booking_keeps_the_room_checkout_and_guest_breakdown(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'room-facility@example.com',
            'name' => 'Room and Facility Guest',
        ]);
        $room = Room::create([
            'room_number' => '207',
            'room_type' => 'Deluxe Room',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'available',
        ]);
        $facility = \App\Models\Facility::create([
            'name' => 'Pool Access Test',
            'price' => 500,
            'pricing_basis' => 'Per Hour',
            'status' => 'available',
        ]);
        $checkIn = today()->addDay()->toDateString();
        $roomCheckOut = today()->addDays(3)->toDateString();
        $facilityDate = today()->addDays(2)->toDateString();

        $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'room_id' => $room->id,
            'facility_id' => (string) $facility->id,
            'facility_date' => $facilityDate,
            'facility_quantity' => 1,
            'duration_hours' => 2,
            'guest_name' => 'Room and Facility Guest',
            'guest_email' => 'room-facility@example.com',
            'guest_phone' => '09191234589',
            'check_in' => $checkIn,
            'check_in_time' => '14:00',
            'facility_start_time' => '15:00',
            'check_out' => $roomCheckOut,
            'check_out_time' => '15:00',
            'facility_duration_hours' => 2,
            'number_of_guests' => 6,
            'room_number_of_guests' => 6,
            'adult_guests' => 1,
            'kid_guests' => 1,
            'total_amount' => 10000,
            'payment_method' => 'Cash / Pay at Hotel',
        ])->assertRedirect(route('reservation'));

        $roomReservation = RoomReservation::where('guest_email', 'room-facility@example.com')->firstOrFail();
        $this->assertSame($checkIn, $roomReservation->check_in->toDateString());
        $this->assertSame($roomCheckOut, $roomReservation->check_out->toDateString());
        $this->assertSame(6, $roomReservation->number_of_guests);
        $this->assertSame(1, $roomReservation->adult_guests);
        $this->assertSame(1, $roomReservation->kid_guests);

        $facilityReservation = \App\Models\FacilityReservation::where('guest_email', 'room-facility@example.com')->firstOrFail();
        $this->assertSame($facilityDate, $facilityReservation->check_in->toDateString());
        $this->assertSame($facilityDate, $facilityReservation->check_out->toDateString());
        $this->assertSame('15:00', $facilityReservation->facility_start_time);
        $this->assertSame(1000.0, (float) $facilityReservation->total_amount);
        $this->assertSame('17:00', $facilityReservation->facility_end_time);
    }

    public function test_public_booking_keeps_each_selected_service_on_its_own_date(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'multi-service@example.com',
            'name' => 'Multi Service Guest',
        ]);
        $room = Room::create([
            'room_number' => '208',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2nd',
            'capacity' => 2,
            'status' => 'available',
        ]);
        $facility = \App\Models\Facility::create([
            'name' => 'Meeting Room',
            'price' => 1000,
            'pricing_basis' => 'Per Hour',
            'status' => 'available',
        ]);
        $event = \App\Models\Event::create([
            'name' => 'Birthday Package',
            'event_type' => 'Birthday',
            'price' => 5000,
            'pricing_basis' => 'Per Event',
            'duration_hours' => 4,
            'capacity' => 40,
            'available_from' => '08:00',
            'available_to' => '22:00',
            'status' => 'available',
        ]);
        $diningItem = \App\Models\DiningMenu::create([
            'name' => 'Dinner Set',
            'category' => 'Main Course',
            'price' => 500,
            'status' => 'available',
        ]);

        $roomCheckIn = now()->addDays(2)->toDateString();
        $roomCheckOut = now()->addDays(4)->toDateString();
        $eventDate = now()->addDays(5)->toDateString();
        $facilityDate = now()->addDays(6)->toDateString();
        $diningDate = now()->addDays(7)->toDateString();

        $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'room_id' => $room->id,
            'facility_id' => (string) $facility->id,
            'facility_date' => $facilityDate,
            'facility_start_time' => '10:00',
            'facility_duration_hours' => 2,
            'event_id' => (string) $event->id,
            'event_date' => $eventDate,
            'event_start_time' => '09:00',
            'event_addons' => '[]',
            'event_type' => 'Birthday',
            'dining_date' => $diningDate,
            'dining_items' => json_encode([[
                'dining_id' => $diningItem->id,
                'quantity' => 1,
                'dining_area' => 'T01',
                'dining_schedule' => 'Dinner',
                'dining_date' => $diningDate,
            ]]),
            'guest_name' => 'Multi Service Guest',
            'guest_email' => 'multi-service@example.com',
            'guest_phone' => '09191234569',
            'check_in' => $roomCheckIn,
            'check_out' => $roomCheckOut,
            'check_in_time' => '14:00',
            'number_of_guests' => 2,
            'room_number_of_guests' => 2,
            'total_amount' => 10000,
            'payment_method' => 'Cash / Pay at Hotel',
        ])->assertRedirect(route('reservation'));

        $roomReservation = RoomReservation::where('guest_email', 'multi-service@example.com')->firstOrFail();
        $this->assertSame($roomCheckIn, $roomReservation->check_in->toDateString());
        $this->assertSame($roomCheckOut, $roomReservation->check_out->toDateString());

        $eventReservation = \App\Models\EventReservation::where('guest_email', 'multi-service@example.com')->firstOrFail();
        $this->assertSame($eventDate, $eventReservation->check_in->toDateString());
        $this->assertSame($eventDate, $eventReservation->check_out->toDateString());

        $facilityReservation = \App\Models\FacilityReservation::where('guest_email', 'multi-service@example.com')->firstOrFail();
        $this->assertSame($facilityDate, $facilityReservation->check_in->toDateString());
        $this->assertSame($facilityDate, $facilityReservation->check_out->toDateString());

        $diningReservation = \App\Models\DiningReservation::where('guest_email', 'multi-service@example.com')->firstOrFail();
        $this->assertSame($diningDate, $diningReservation->check_in->toDateString());
        $this->assertSame($diningDate, $diningReservation->check_out->toDateString());
        $this->assertSame($diningDate, $diningReservation->diningItems()->firstOrFail()->dining_date->toDateString());
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

    public function test_admin_can_save_event_package_inclusions_and_clear_them_on_edit(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'web');

        $response = $this->post(route('admin.inventory.store'), [
            'category' => 'event',
            'event_type' => 'Birthday',
            'name' => 'Birthday Premium Test',
            'description' => 'A test event package.',
            'price' => 8000,
            'pricing_basis' => 'Per Event',
            'duration_hours' => 4,
            'capacity' => 30,
            'location' => 'Garden',
            'available_from' => '08:00',
            'available_to' => '22:00',
            'status' => 'available',
            'inclusions' => ['Themed decoration', 'Birthday cake', '', ' birthday cake '],
            'optional_addons' => [
                ['name' => 'Birthday Cake', 'description' => 'Custom birthday cake', 'price' => 1000, 'available' => '1'],
                ['name' => 'Live Music', 'description' => 'Acoustic set', 'price' => 2500, 'available' => '0'],
            ],
        ]);

        $response->assertRedirect(route('admin.rooms'));
        $event = \App\Models\Event::where('name', 'Birthday Premium Test')->firstOrFail();
        $this->assertSame(['Themed decoration', 'Birthday cake'], $event->inclusions);
        $this->assertSame([
                ['name' => 'Birthday Cake', 'description' => 'Custom birthday cake', 'price' => 1000, 'available' => true],
                ['name' => 'Live Music', 'description' => 'Acoustic set', 'price' => 2500, 'available' => false],
        ], $event->optional_addons);

        $this->put(route('admin.inventory.update', $event->id), [
            'category' => 'event',
            'event_type' => 'Birthday',
            'name' => 'Birthday Premium Test',
            'description' => 'A test event package.',
            'price' => 8000,
            'pricing_basis' => 'Per Event',
            'duration_hours' => 4,
            'capacity' => 30,
            'location' => 'Garden',
            'available_from' => '08:00',
            'available_to' => '22:00',
            'status' => 'available',
            'inclusions_present' => 1,
            'optional_addons_present' => 1,
            'optional_addons' => [
                ['name' => 'Premium Birthday Cake', 'description' => 'Two-tier cake', 'price' => 1800, 'available' => '1'],
            ],
        ])->assertRedirect(route('admin.rooms'));

        $this->assertSame([], $event->fresh()->inclusions);
        $this->assertSame([
            ['name' => 'Premium Birthday Cake', 'description' => 'Two-tier cake', 'price' => 1800, 'available' => true],
        ], $event->fresh()->optional_addons);
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

    public function test_event_end_time_rolls_over_midnight_using_package_duration(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'overnight-event@example.com',
            'name' => 'Overnight Event Guest',
            'contact_no' => '09191234567',
        ]);
        $event = \App\Models\Event::create([
            'name' => 'Overnight Test Package',
            'event_type' => 'Wedding',
            'description' => 'Overnight test package',
            'price' => 1000,
            'pricing_basis' => 'Per Hour',
            'duration_hours' => 4,
            'capacity' => 40,
            'location' => 'Garden',
            'available_from' => '08:00',
            'available_to' => '22:00',
            'status' => 'available',
        ]);
        $eventDate = now()->addDay()->toDateString();
        $nextDate = now()->addDays(2)->toDateString();

        $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'category' => 'event',
            'event_id' => $event->id,
            'event_addons' => '[]',
            'event_type' => 'Wedding',
            'guest_name' => 'Overnight Event Guest',
            'guest_email' => 'overnight-event@example.com',
            'guest_phone' => '09191234567',
            'check_in' => $eventDate,
            'check_out' => $nextDate,
            'event_start_time' => '22:00',
            'event_end_time' => '02:00',
            'duration_hours' => 1,
            'number_of_guests' => 20,
            'total_amount' => 1000,
            'payment_method' => 'Cash / Pay at Hotel',
        ])->assertRedirect(route('reservation'));

        $reservation = \App\Models\EventReservation::where('guest_email', 'overnight-event@example.com')->firstOrFail();
        $this->assertSame($nextDate, $reservation->check_out->toDateString());
        $this->assertSame('02:00', substr((string) $reservation->event_end_time, 0, 5));
        $this->assertSame(4, $reservation->duration_hours);
        $this->assertSame(4000.0, (float) $reservation->total_amount);
    }

    public function test_public_event_booking_saves_and_charges_selected_active_addons(): void
    {
        $guest = Guest::factory()->create([
            'email' => 'addons@example.com',
            'name' => 'Add-on Guest',
            'contact_no' => '09191234568',
        ]);
        $event = \App\Models\Event::create([
            'name' => 'Add-on Test Package',
            'event_type' => 'Birthday',
            'description' => 'Test package',
            'price' => 5000,
            'pricing_basis' => 'Per Event',
            'capacity' => 40,
            'location' => 'Garden',
            'status' => 'available',
            'optional_addons' => [
                ['name' => 'Cake', 'description' => 'Custom cake', 'price' => 1000, 'available' => true],
                ['name' => 'Live Music', 'description' => null, 'price' => 2500, 'available' => true],
                ['name' => 'Unavailable Extra', 'description' => null, 'price' => 9000, 'available' => false],
            ],
        ]);
        $eventDate = now()->addDay()->format('Y-m-d');

        $response = $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'category' => 'event',
            'event_id' => $event->id,
            'event_addons' => json_encode([['event_id' => (string) $event->id, 'addon_indexes' => [0, 1]]]),
            'event_type' => 'Birthday',
            'guest_name' => 'Add-on Guest',
            'guest_email' => 'addons@example.com',
            'guest_phone' => '09191234568',
            'check_in' => $eventDate,
            'check_out' => $eventDate,
            'event_start_time' => '10:00',
            'event_end_time' => '14:00',
            'number_of_guests' => 20,
            'total_amount' => 5000,
            'payment_method' => 'Cash / Pay at Hotel',
        ]);

        $response->assertRedirect(route('reservation'));
        $reservation = \App\Models\EventReservation::where('guest_email', 'addons@example.com')->firstOrFail();
        $this->assertSame(8500.0, (float) $reservation->total_amount);
        $this->assertSame(['Cake', 'Live Music'], array_column($reservation->selected_addons, 'name'));

        $this->post(route('reservation.store'), [
            'category' => 'event',
            'event_id' => $event->id,
            'event_addons' => json_encode([['event_id' => (string) $event->id, 'addon_indexes' => [2]]]),
            'event_type' => 'Birthday',
            'guest_name' => 'Add-on Guest',
            'guest_email' => 'addons@example.com',
            'guest_phone' => '09191234568',
            'check_in' => $eventDate,
            'check_out' => $eventDate,
            'event_start_time' => '10:00',
            'event_end_time' => '14:00',
            'number_of_guests' => 20,
            'total_amount' => 5000,
            'payment_method' => 'Cash / Pay at Hotel',
        ])->assertUnprocessable();
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
