<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Guest;
use App\Support\StaffNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StaffNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_notifications_are_stored_for_staff_users(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        $this->actingAs($employee);

        StaffNotificationService::notifyEmployees(
            'New Reservation',
            'Juan Dela Cruz booked Deluxe Room 101.',
            [
                'reference' => 'reservation:101',
                'url' => '/employee/reservation',
            ]
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $employee->id,
            'notifiable_type' => Staff::class,
        ]);
    }

    public function test_database_notifications_are_written_without_a_queue_worker(): void
    {
        Config::set('queue.default', 'database');
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        StaffNotificationService::notifyEmployees(
            'Immediate Notification',
            'This should be available to the bell immediately.',
            ['reference' => 'immediate-notification:1']
        );

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $employee->id,
            'notifiable_type' => Staff::class,
        ]);
    }

    public function test_employee_notifications_endpoint_returns_unread_items(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        StaffNotificationService::notifyEmployees(
            'New Reservation',
            'Test reservation created.',
            [
                'reference' => 'reservation:202',
                'url' => '/employee/reservation',
            ]
        );

        $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'New Reservation');
    }

    public function test_employee_messages_endpoint_refreshes_conversations_json(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        \App\Models\Message::create([
            'customer_name' => 'Maria Guest',
            'customer_email' => 'maria@example.com',
            'message' => 'Need extra towels.',
            'is_replied' => false,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $this->actingAs($employee)
            ->getJson(route('employee.messages', ['filter' => 'all']))
            ->assertOk()
            ->assertJsonPath('stats.unread', 1)
            ->assertJsonPath('conversations.0.email', 'maria@example.com');

        \App\Models\Message::create([
            'customer_name' => 'Maria Guest',
            'customer_email' => 'maria@example.com',
            'message' => 'Could I also get a pillow?',
            'is_replied' => false,
        ]);

        $this->getJson(route('employee.messages', ['filter' => 'all']))
            ->assertOk()
            ->assertJsonPath('stats.unread', 2)
            ->assertJsonPath('conversations.0.latest_message.message', 'Could I also get a pillow?');
    }

    public function test_opening_guest_conversation_marks_messages_read_without_replying(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);
        $message = \App\Models\Message::create([
            'customer_name' => 'Maria Guest',
            'customer_email' => 'maria@example.com',
            'message' => 'Please bring extra towels.',
            'is_replied' => false,
            'is_read' => false,
        ]);

        $this->actingAs($employee)
            ->postJson(route('employee.messages.mark-read'), [
                'customer_email' => 'maria@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('updated', 1);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
            'is_replied' => false,
        ]);
    }

    public function test_employee_unread_filter_uses_read_state_not_reply_state(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        \App\Models\Message::create([
            'customer_name' => 'Maria Guest',
            'customer_email' => 'maria@example.com',
            'message' => 'I have seen this, but still need to reply.',
            'is_replied' => false,
            'is_read' => true,
        ]);

        $this->actingAs($employee)
            ->getJson(route('employee.messages', ['filter' => 'unread']))
            ->assertOk()
            ->assertJsonPath('stats.unread', 0)
            ->assertJsonCount(0, 'conversations');
    }

    public function test_chatbot_front_desk_message_notifies_active_employees(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);
        $guest = Guest::factory()->create();

        $this->actingAs($guest, 'guest')
            ->postJson(route('chatbot.message'), [
                'message' => 'Can someone help me with my room?',
                'action' => 'contact_front_desk',
            ])
            ->assertOk()
            ->assertJsonPath('mode', 'contact_front_desk');

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $employee->id,
            'notifiable_type' => Staff::class,
        ]);
    }
}
