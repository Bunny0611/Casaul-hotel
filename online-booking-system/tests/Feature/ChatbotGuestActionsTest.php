<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\GuestRequest;
use App\Models\Message;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotGuestActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_send_a_front_desk_message_and_view_reply_history(): void
    {
        $guest = Guest::factory()->create();

        $this->actingAs($guest, 'guest')
            ->postJson(route('chatbot.message'), [
                'message' => 'Contact Front Desk',
                'action' => 'contact_front_desk',
            ])
            ->assertOk()
            ->assertJsonPath('mode', 'contact_front_desk');

        $this->actingAs($guest, 'guest')
            ->postJson(route('chatbot.message'), [
                'message' => 'Please confirm my airport transfer.',
                'action' => 'contact_front_desk',
            ])
            ->assertOk()
            ->assertSee('Please confirm my airport transfer.');

        $message = Message::query()->where('customer_email', $guest->email)->firstOrFail();
        $employee = Staff::factory()->create(['role' => 'employee']);

        $this->actingAs($employee)
            ->post(route('employee.messages.store'), [
                'recipient' => (string) $message->id,
                'message' => 'The front desk will confirm that shortly.',
            ])
            ->assertRedirect(route('employee.messages'));

        $this->actingAs($guest, 'guest')
            ->getJson(route('guest.messages'))
            ->assertOk()
            ->assertJsonPath('messages.0.reply', 'The front desk will confirm that shortly.')
            ->assertJsonPath('messages.0.is_replied', true);
    }

    public function test_guest_can_submit_a_housekeeping_request_from_the_chatbot(): void
    {
        $guest = Guest::factory()->create();
        $room = Room::create([
            'room_number' => '410',
            'room_type' => 'Deluxe',
            'status' => 'occupied',
            'price' => 2500,
            'floor' => 4,
            'capacity' => 2,
        ]);
        RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => $guest->name,
            'guest_email' => $guest->email,
            'guest_phone' => '09123456789',
            'check_in' => today(),
            'check_out' => today(),
            'number_of_guests' => 1,
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);

        $this->actingAs($guest, 'guest')
            ->postJson(route('chatbot.message'), [
                'message' => 'Room Cleaning',
                'action' => 'request_housekeeping',
                'request_type' => 'Room Cleaning',
            ])
            ->assertOk()
            ->assertSee('room cleaning request has been submitted');

        $this->assertDatabaseHas('guest_requests', [
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'request_type' => 'Room Cleaning',
            'department' => 'Housekeeping',
            'status' => 'Pending',
        ]);
    }

    public function test_housekeeping_can_update_chatbot_request_statuses(): void
    {
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping']);
        $request = GuestRequest::create([
            'request_type' => 'Towels',
            'description' => 'Fresh towels requested.',
            'department' => 'Housekeeping',
            'priority' => 'Normal',
            'status' => 'Pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($housekeeper)
            ->patchJson(route('housekeeping.guest-requests.update', $request), [
                'status' => 'Assigned',
                'notes' => 'Assigned to the afternoon team.',
            ])
            ->assertOk();

        $this->assertDatabaseHas('guest_requests', [
            'id' => $request->id,
            'status' => 'Assigned',
            'employee_notes' => 'Assigned to the afternoon team.',
        ]);
    }
}
