<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\GuestRequest;
use App\Models\Room;
use App\Models\Staff;
use Tests\TestCase;

class EmployeeGuestRequestsTest extends TestCase
{
    public function test_employee_page_only_shows_employee_requests(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee']);
        $guest = Guest::factory()->create();

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Late Checkout',
            'description' => 'Need a late checkout request.',
            'department' => 'Employee',
            'priority' => 'Normal',
            'preferred_time' => '17:00',
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Extra Towels',
            'description' => 'Need extra towels for the room.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        $this->actingAs($employee)
            ->get(route('employee.guest-requests'))
            ->assertOk()
            ->assertSee('Late Checkout')
            ->assertDontSee('Extra Towels');
    }

    public function test_housekeeping_page_only_shows_housekeeping_requests(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $guest = Guest::factory()->create();

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Extra Towels',
            'description' => 'Need extra towels for the room.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Late Checkout',
            'description' => 'Need a late checkout request.',
            'department' => 'Employee',
            'priority' => 'Normal',
            'preferred_time' => '17:00',
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'))
            ->assertOk()
            ->assertSee('Extra Towels')
            ->assertDontSee('Late Checkout');
    }

    public function test_housekeeping_request_details_page_shows_selected_request(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $guest = Guest::factory()->create();

        $request = GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Extra Towels',
            'description' => 'Need extra towels for the room.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'Completed',
            'submitted_at' => now(),
        ]);

        $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests.show', $request))
            ->assertOk()
            ->assertSee('Housekeeping Add-On Request')
            ->assertSee('Extra Towels')
            ->assertSee('Need extra towels for the room.');
    }

    public function test_guest_can_delete_their_own_request(): void
    {
        $guest = Guest::factory()->create();
        $guestRequest = GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Extra Towels',
            'description' => 'Need extra towels for the room.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'New',
            'submitted_at' => now(),
        ]);

        $this->actingAs($guest, 'guest')
            ->delete(route('guest.requests.destroy', $guestRequest))
            ->assertRedirect(route('guest.records'))
            ->assertSessionHas('success', 'Your request has been deleted.');

        $this->assertDatabaseMissing('guest_requests', ['id' => $guestRequest->id]);
    }

    public function test_guest_records_group_requests_by_room_and_exact_submission_time(): void
    {
        $guest = Guest::factory()->create();
        $room = \App\Models\Room::create([
            'room_number' => '107',
            'room_type' => 'Deluxe',
            'price' => 2000,
            'floor' => '1',
            'status' => 'occupied',
            'capacity' => 2,
        ]);
        $submittedAt = now()->setMicrosecond(0);

        foreach ([
            ['request_type' => 'Extra Towels', 'status' => 'Delivered'],
            ['request_type' => 'Extra Pillows', 'status' => 'Delivered'],
        ] as $requestData) {
            GuestRequest::create(array_merge($requestData, [
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'department' => 'Housekeeping',
                'description' => 'Please prepare this request.',
                'priority' => 'Normal',
                'submitted_at' => $submittedAt,
            ]));
        }

        GuestRequest::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'request_type' => 'Extra Blanket',
            'department' => 'Housekeeping',
            'description' => 'Please prepare this request.',
            'priority' => 'Normal',
            'status' => 'New',
            'submitted_at' => $submittedAt->copy()->addSecond(),
        ]);

        $response = $this->actingAs($guest, 'guest')->get(route('guest.records'));

        $response->assertOk()
            ->assertSee('2 Guest Requests')
            ->assertSee('Extra Towels')
            ->assertSee('Extra Pillows')
            ->assertSee('Extra Blanket');
        $this->assertSame(2, substr_count($response->getContent(), 'class="view-request guest-request-view"'));
    }

    public function test_guest_can_submit_multiple_housekeeping_requests_with_quantity(): void
    {
        $guest = Guest::factory()->create();
        $room = \App\Models\Room::create([
            'room_number' => '101',
            'room_type' => 'Deluxe',
            'price' => 2000,
            'floor' => '1',
            'status' => 'occupied',
            'capacity' => 2,
        ]);

        $reservation = \App\Models\Reservation::create([
            'category' => 'room',
            'room_id' => $room->id,
            'guest_name' => $guest->name,
            'guest_email' => $guest->email,
            'guest_phone' => '09123456789',
            'number_of_guests' => 2,
            'check_in' => now()->toDateString(),
            'check_out' => now()->addDay()->toDateString(),
            'status' => 'checked-in',
            'total_amount' => 2000,
            'amount_paid' => 2000,
        ]);

        $this->actingAs($guest, 'guest')
            ->post(route('guest.requests.store'), [
                'request_items' => json_encode([
                    ['type' => 'Extra Towels', 'quantity' => 2],
                    ['type' => 'Room Cleaning', 'quantity' => 1],
                ]),
                'description' => 'Need extra towels and room cleaning.',
                'priority' => 'Normal',
                'preferred_time' => '15:30',
            ])
            ->assertRedirect(route('guest.records'));

        $this->assertDatabaseHas('guest_requests', [
            'guest_id' => $guest->id,
            'request_type' => 'Extra Towels',
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('guest_requests', [
            'guest_id' => $guest->id,
            'request_type' => 'Room Cleaning',
            'quantity' => 1,
        ]);
    }

    public function test_housekeeping_table_groups_same_guest_submission_into_one_request_row(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $guest = Guest::factory()->create();
        $submittedAt = now();

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Extra Towels',
            'description' => 'Need extra towels and room cleaning.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'New',
            'quantity' => 2,
            'unit_price' => 80,
            'subtotal' => 160,
            'submitted_at' => $submittedAt,
        ]);

        GuestRequest::create([
            'guest_id' => $guest->id,
            'reservation_id' => null,
            'room_id' => null,
            'request_type' => 'Room Cleaning',
            'description' => 'Need extra towels and room cleaning.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'preferred_time' => '15:00',
            'status' => 'New',
            'quantity' => 1,
            'unit_price' => 200,
            'subtotal' => 200,
            'submitted_at' => $submittedAt,
        ]);

        $response = $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'));

        $response->assertOk();
        $this->assertCount(1, $response->original->getData()['requests']);
        $this->assertStringContainsString('Extra Towels', $response->original->getData()['requests']->first()->request_type);
        $requestData = $response->original->getData()['requestData']->first();
        $this->assertSame('Varies', $requestData['unitPriceFormatted']);
        $this->assertSame(360.0, (float) $requestData['subtotal']);
    }

    public function test_housekeeping_group_stays_together_when_delivered(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $guest = Guest::factory()->create();
        $room = Room::create([
            'room_number' => '107',
            'room_type' => 'Standard',
            'price' => 2200,
            'floor' => '1',
            'status' => 'occupied',
            'capacity' => 2,
        ]);
        $submittedAt = now();

        $firstRequest = GuestRequest::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'request_type' => 'Extra Towels',
            'description' => 'Towels please.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'status' => 'New',
            'submitted_at' => $submittedAt,
        ]);

        GuestRequest::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'request_type' => 'Extra Pillows',
            'description' => 'Pillows please.',
            'department' => 'Housekeeping',
            'priority' => 'High',
            'status' => 'In Progress',
            'submitted_at' => $submittedAt,
        ]);

        $response = $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'));

        $response->assertOk();
        $this->assertCount(1, $response->original->getData()['requests']);
        $this->assertCount(2, $response->original->getData()['requestData']->first()['items']);

        $this->actingAs($housekeepingStaff)
            ->postJson(route('housekeeping.guest-requests.mark-delivered', $firstRequest->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('Delivered', $firstRequest->fresh()->status);
        $this->assertSame('Delivered', GuestRequest::where('request_type', 'Extra Pillows')->first()->status);

        $response = $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'));

        $this->assertCount(1, $response->original->getData()['requests']);
        $this->assertSame('Delivered', $response->original->getData()['requestData']->first()['status']);
    }

    public function test_billable_add_ons_are_price_tagged_and_added_to_the_room_reservation_total(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $guest = Guest::factory()->create();
        $room = \App\Models\Room::create([
            'room_number' => '205',
            'room_type' => 'Deluxe',
            'price' => 2200,
            'floor' => '2',
            'status' => 'occupied',
            'capacity' => 2,
        ]);

        $reservation = \App\Models\RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => $guest->name,
            'guest_email' => $guest->email,
            'guest_phone' => '09123456789',
            'check_in' => now()->toDateString(),
            'check_out' => now()->addDay()->toDateString(),
            'number_of_guests' => 2,
            'status' => 'checked-in',
            'total_amount' => 2200,
            'amount_paid' => 0,
        ]);

        $this->actingAs($guest, 'guest')
            ->post(route('guest.requests.store'), [
                'request_items' => json_encode([
                    ['type' => 'Extra Towels', 'quantity' => 2],
                ]),
                'description' => 'Need extra towels.',
                'priority' => 'Normal',
                'preferred_time' => '15:30',
            ])
            ->assertRedirect(route('guest.records'));

        $guestRequest = GuestRequest::query()->where('guest_id', $guest->id)->firstOrFail();
        $this->assertSame(80.0, (float) $guestRequest->unit_price);
        $this->assertSame(160.0, (float) $guestRequest->subtotal);
        $this->assertTrue((bool) $guestRequest->is_billable);
        $this->assertSame('App\\Models\\RoomReservation', $guestRequest->reservation_type);
        $this->assertSame($reservation->id, $guestRequest->reservation_key);

        $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'))
            ->assertOk()
            ->assertSee('80.00')
            ->assertSee('160.00');

        $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests.show', $guestRequest))
            ->assertOk()
            ->assertSee('80.00')
            ->assertSee('160.00');

        $this->actingAs($housekeepingStaff)
            ->patch(route('housekeeping.guest-requests.update', $guestRequest->id), [
                'status' => 'Completed',
                'notes' => 'Added towels to the room.',
            ])
            ->assertOk();

        $reservation->refresh();
        $this->assertSame(2360.0, (float) $reservation->total_amount);
        $this->assertSame('posted', $guestRequest->fresh()->billing_status);
    }
}
