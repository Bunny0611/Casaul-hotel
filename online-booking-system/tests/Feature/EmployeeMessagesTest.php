<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\MessageReply;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_housekeeping_and_admin_can_message_each_other(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee']);
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping']);
        $admin = Staff::factory()->create(['role' => 'admin']);

        $this->actingAs($employee)->post(route('employee.staff-messages.store'), [
            'recipient_id' => $housekeeper->id,
            'message' => 'Please clean room 204 before check-in.',
        ])->assertRedirect(route('employee.messages', ['staff_id' => $housekeeper->id]));

        $this->actingAs($housekeeper)->post(route('housekeeping.staff-messages.store'), [
            'recipient_id' => $admin->id,
            'message' => 'Room 204 is ready.',
        ])->assertRedirect(route('housekeeping.messages', ['staff_id' => $admin->id]));

        $this->actingAs($admin)->post(route('admin.staff-messages.store'), [
            'recipient_id' => $employee->id,
            'message' => 'Thanks for coordinating the room.',
        ])->assertRedirect(route('admin.messages', ['staff_id' => $employee->id]));

        $this->assertDatabaseCount('staff_messages', 3);
        $this->actingAs($housekeeper)->get(route('housekeeping.messages'))
            ->assertOk()
            ->assertSee('Please clean room 204 before check-in.')
            ->assertDontSee('Guest Message Box');
        $this->actingAs($employee)->get(route('employee.messages'))
            ->assertOk()
            ->assertSee('Guest Inbox')
            ->assertSee('Staff Chat')
            ->assertSee('Private conversations between guests and employees.')
            ->assertSee('Internal conversations visible only to staff.')
            ->assertSee('Quick handoff templates')
            ->assertSee('Item delivery')
            ->assertSee('Room cleaning')
            ->assertSee('Linens or amenities')
            ->assertSee('Room issue')
            ->assertSee('Guest request')
            ->assertSee('Staff follow-up');
        $this->actingAs($admin)->get(route('admin.messages'))->assertOk()->assertSee('Staff Messages');
    }

    public function test_employee_cannot_start_a_guest_conversation(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee']);

        $this->actingAs($employee)->post(route('employee.messages.store'), [
            'recipient' => 'Housekeeping Team',
            'message' => 'This must use staff messaging.',
        ])->assertSessionHasErrors('recipient');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_employee_can_forward_a_guest_message_to_housekeeping(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee']);
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping']);
        $guestMessage = Message::create([
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'message' => 'I am missing a towel in my room.',
        ]);

        $this->actingAs($employee)->post(route('employee.messages.forward', $guestMessage->id), [
            'recipient_id' => $housekeeper->id,
            'note' => 'Lost item reported in Room 204 around 2 PM. Please check with housekeeping.',
        ])->assertRedirect(route('employee.messages', ['staff_id' => $housekeeper->id]));

        $handoff = "Guest issue handoff:\n\nLost item reported in Room 204 around 2 PM. Please check with housekeeping.";
        $this->assertDatabaseHas('staff_messages', [
            'sender_id' => $employee->id,
            'recipient_id' => $housekeeper->id,
            'body' => $handoff,
        ]);

        $this->actingAs($housekeeper)->get(route('housekeeping.messages'))
            ->assertOk()
            ->assertSee('Lost item reported in Room 204 around 2 PM.')
            ->assertDontSee('guest@example.com')
            ->assertDontSee('I am missing a towel in my room.');
    }

    public function test_admin_cannot_reply_to_guest_messages(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $guestMessage = Message::create([
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
            'message' => 'I need help with my reservation.',
        ]);

        $this->actingAs($admin)->post(route('admin.messages.reply', $guestMessage->id), [
            'admin_reply' => 'Please contact the front desk.',
        ])->assertForbidden();

        $this->assertDatabaseCount('message_replies', 0);
        $this->assertDatabaseHas('messages', ['id' => $guestMessage->id, 'is_replied' => false]);
    }
}
