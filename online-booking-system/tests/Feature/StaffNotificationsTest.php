<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomReservation;
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

    public function test_sidebar_unread_counts_and_read_state_are_isolated_per_staff_user(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Staff::factory()->create(['role' => 'employee', 'is_active' => true]);

        StaffNotificationService::notifyUsers([$admin, $employee], 'New reservation', 'A reservation needs review.', [
            'reference' => 'shared-reservation:1',
            'url' => '/employee/reservation',
            'type' => 'reservation',
            'module' => 'reservations',
        ]);

        $this->assertSame(1, StaffNotificationService::unreadSidebarCounts($admin)['reservations']);
        $this->assertSame(1, StaffNotificationService::unreadSidebarCounts($employee)['reservations']);

        StaffNotificationService::markModuleRead($employee, 'reservations');

        $this->assertSame(0, StaffNotificationService::unreadSidebarCounts($employee)['reservations']);
        $this->assertSame(1, StaffNotificationService::unreadSidebarCounts($admin)['reservations']);
    }

    public function test_opening_employee_reservations_marks_only_the_employees_module_notifications_read(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Staff::factory()->create(['role' => 'employee', 'is_active' => true]);
        StaffNotificationService::notifyUsers([$admin, $employee], 'New reservation', 'A booking needs review.', [
            'reference' => 'route-read-reservation:1',
            'url' => route('employee.reservation'),
            'type' => 'reservation',
            'module' => 'reservations',
        ]);
        $employeeNotification = $employee->notifications()->firstOrFail();
        $adminNotification = $admin->notifications()->firstOrFail();

        $this->actingAs($employee)
            ->get(route('employee.reservation'))
            ->assertOk();

        $this->assertNotNull($employeeNotification->fresh()->read_at);
        $this->assertNull($adminNotification->fresh()->read_at);
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
            ->assertJsonPath('data.0.title', 'New Reservation')
            ->assertJsonPath('module_counts.reservations', 1);
    }

    public function test_employee_reservation_notifications_are_upgraded_to_the_specific_record_target(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);
        $room = Room::create([
            'room_number' => '105',
            'room_type' => 'Deluxe',
            'price' => 3500,
            'floor' => '1',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Ana Reyes',
            'guest_email' => 'ana@example.com',
            'guest_phone' => '09171234567',
            'check_in' => now()->addDay()->toDateString(),
            'room_check_in_time' => '14:00',
            'check_out' => now()->addDays(3)->toDateString(),
            'room_check_out_time' => '12:00',
            'number_of_guests' => 2,
            'status' => 'pending',
            'total_amount' => 7200,
            'amount_paid' => 0,
        ]);

        StaffNotificationService::notifyEmployees(
            'New reservation',
            'Ana Reyes booked Deluxe Room 105.',
            [
                'reference' => 'reservation:' . $reservation->getKey(),
                'url' => '/employee/reservation',
                'type' => 'reservation',
                'related_id' => $reservation->getKey(),
                'related_type' => RoomReservation::class,
            ]
        );

        $notificationUrl = $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->json('data.0.url');

        $this->assertSame(
            '/employee/reservation?tab=rooms&reservation_id=' . $reservation->id . '&reservation_type=room_reservations',
            $notificationUrl
        );
    }

    public function test_legacy_cancellation_notification_uses_its_reference_to_target_the_reservation(): void
    {
        $employee = Staff::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);
        $room = Room::create([
            'room_number' => '102',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '1',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Darlene Rane F. Bongon',
            'guest_email' => 'darlene@example.com',
            'guest_phone' => '09171234567',
            'check_in' => now()->addDay()->toDateString(),
            'room_check_in_time' => '15:00',
            'check_out' => now()->addDays(3)->toDateString(),
            'room_check_out_time' => '15:00',
            'number_of_guests' => 2,
            'status' => 'cancelled',
            'total_amount' => 7200,
            'amount_paid' => 0,
        ]);

        StaffNotificationService::notifyEmployees(
            'Reservation cancelled',
            'Darlene Rane F. Bongon cancelled a reservation for 102.',
            [
                'reference' => 'reservation-cancelled:' . $reservation->getKey(),
                'url' => '/employee/reservation',
                'type' => 'reservation',
            ]
        );

        $notificationUrl = $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->json('data.0.url');

        $this->assertSame(
            '/employee/reservation?tab=rooms&reservation_id=' . $reservation->id . '&reservation_type=room_reservations',
            $notificationUrl
        );
    }

    public function test_employee_room_notification_opens_and_highlights_the_target_room(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee', 'is_active' => true]);
        for ($roomNumber = 101; $roomNumber <= 111; $roomNumber++) {
            Room::create([
                'room_number' => (string) $roomNumber,
                'room_type' => 'Standard',
                'price' => 2400,
                'floor' => '1',
                'status' => 'available',
                'cleaning_status' => 'clean',
                'capacity' => 2,
            ]);
        }
        $targetRoom = Room::create([
            'room_number' => '201',
            'room_type' => 'Deluxe',
            'price' => 3200,
            'floor' => '2',
            'status' => 'available',
            'cleaning_status' => 'dirty',
            'capacity' => 2,
        ]);

        StaffNotificationService::notifyEmployees(
            'Room status updated',
            'Room 201 was updated by housekeeping.',
            [
                'reference' => 'legacy-room-status:' . $targetRoom->id,
                'url' => '/employee/room-status',
                'type' => 'housekeeping',
                'related_id' => $targetRoom->id,
                'related_type' => Room::class,
            ]
        );

        $notificationUrl = $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->json('data.0.url');

        $this->assertStringContainsString('room_id=' . $targetRoom->id, $notificationUrl);
        $this->assertSame('/employee/room-status?room_id=' . $targetRoom->id, $notificationUrl);
        $this->get($notificationUrl)
            ->assertOk()
            ->assertSee('data-notification-room-id="' . $targetRoom->id . '"', false)
            ->assertSee('data-room-id="' . $targetRoom->id . '"', false)
            ->assertSee('data-room-number="111"', false)
            ->assertSee('notification-room-highlight', false)
            ->assertSee('page=2', false);
    }

    public function test_legacy_housekeeping_room_alert_is_upgraded_to_current_room_target(): void
    {
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping', 'is_active' => true]);
        $room = Room::create([
            'room_number' => '301',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '3',
            'status' => 'available',
            'cleaning_status' => 'dirty',
            'capacity' => 2,
        ]);

        StaffNotificationService::notifyHousekeeping(
            'Legacy room alert',
            'Room 301 needs attention.',
            [
                'reference' => 'legacy-housekeeping-room:' . $room->id,
                'url' => 'http://127.0.0.1:8000/housekeeping/room-status-update',
                'related_id' => $room->id,
                'related_type' => Room::class,
            ]
        );

        $this->actingAs($housekeeper)
            ->getJson(route('housekeeping.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.url', '/housekeeping/room-status-update?room_id=' . $room->id);
    }

    public function test_legacy_room_status_alert_resolves_room_id_from_message(): void
    {
        $employee = Staff::factory()->create(['role' => 'employee', 'is_active' => true]);
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping', 'is_active' => true]);
        $room = Room::create([
            'room_number' => '102',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '1',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);

        StaffNotificationService::notifyEmployees(
            'Room status updated',
            'Housekeeping updated Room 102 to clean.',
            [
                'reference' => 'legacy-employee-room-status:' . $room->id,
                'url' => 'http://127.0.0.1:8000/employee/room-status',
            ]
        );
        StaffNotificationService::notifyHousekeeping(
            'Room status updated',
            'Room 102 has been updated to clean.',
            [
                'reference' => 'legacy-housekeeping-room-status:' . $room->id,
                'url' => 'http://127.0.0.1:8000/housekeeping/room-status-update',
            ]
        );

        $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.url', '/employee/room-status?room_id=' . $room->id);

        $this->actingAs($housekeeper)
            ->getJson(route('housekeeping.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.url', '/housekeeping/room-status-update?room_id=' . $room->id);
    }

    public function test_housekeeping_can_mark_an_older_notification_read_before_navigating(): void
    {
        $housekeeper = Staff::factory()->create(['role' => 'housekeeping', 'is_active' => true]);
        StaffNotificationService::notifyHousekeeping(
            'Older alert',
            'First notification.',
            ['reference' => 'older-alert:1']
        );
        $olderNotification = $housekeeper->notifications()->firstOrFail();

        StaffNotificationService::notifyHousekeeping(
            'Newer alert',
            'Second notification.',
            ['reference' => 'newer-alert:1']
        );

        $this->actingAs($housekeeper)
            ->postJson(route('housekeeping.notifications.mark-read', $olderNotification->id))
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->assertNotNull($olderNotification->fresh()->read_at);
        $this->assertNull($housekeeper->notifications()->where('data->reference', 'newer-alert:1')->firstOrFail()->read_at);
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
        $admin = Staff::factory()->create([
            'role' => 'admin',
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

        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $employee->id,
            'notifiable_type' => Staff::class,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Staff::class,
        ]);
    }
}
