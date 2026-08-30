<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\GuestRequest;
use App\Models\Staff;
use Tests\TestCase;

class EmployeeGuestRequestsTest extends TestCase
{
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
            'submitted_at' => $submittedAt,
        ]);

        $response = $this->actingAs($housekeepingStaff)
            ->get(route('housekeeping.guest-requests'));

        $response->assertOk();
        $this->assertCount(1, $response->original->getData()['requests']);
        $this->assertStringContainsString('Extra Towels', $response->original->getData()['requests']->first()->request_type);
    }
}
