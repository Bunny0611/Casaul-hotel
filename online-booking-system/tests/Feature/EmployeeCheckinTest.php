<?php

namespace Tests\Feature;

use App\Mail\ReservationCancelled;
use App\Models\Reservation;
use App\Models\RoomReservation;
use App\Models\Room;
use App\Models\Staff;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeCheckinTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_checkin_page_uses_live_reservation_data_instead_of_dummy_entries(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee-checkin@example.com',
            'name' => 'Front Desk Employee',
        ]);

        $room = Room::create([
            'room_number' => '201',
            'room_type' => 'Deluxe',
            'price' => 3200,
            'floor' => '2',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);

        Reservation::create([
            'room_id' => $room->id,
            'guest_name' => 'John Smith',
            'guest_email' => 'john@example.com',
            'guest_phone' => '09123456789',
            'check_in' => now()->toDateString(),
            'check_in_time' => '14:00',
            'check_out' => now()->addDay()->toDateString(),
            'check_out_time' => '12:00',
            'status' => 'confirmed',
            'total_amount' => 6400,
            'special_requests' => 'Late arrival',
        ]);

        $response = $this->actingAs($user)->get(route('employee.checkin'));

        $response->assertOk();
        $response->assertSee('John Smith');
        $response->assertDontSee('BK1001');
    }

    public function test_check_in_and_checkout_synchronize_room_status_and_cleaning_state(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $room = Room::create([
            'room_number' => '202',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '2',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Jane Doe',
            'guest_email' => 'jane@example.com',
            'guest_phone' => '09123456789',
            'check_in' => now()->toDateString(),
            'check_out' => now()->addDay()->toDateString(),
            'status' => 'confirmed',
            'total_amount' => 4800,
        ]);

        $this->actingAs($user)
            ->patch(route('employee.reservations.status', $reservation->id), ['status' => 'checked-in'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'checked-in',
        ]);
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'occupied',
            'cleaning_status' => 'clean',
        ]);

        $this->actingAs($user)
            ->postJson(route('employee.reservations.payments.store', $reservation->id), [
                'amount' => 4800,
                'payment_method' => 'Cash',
                'payment_date' => now()->toDateString(),
            ])
            ->assertOk();

        $this->assertDatabaseHas('payments', [
            'reservation_id' => $reservation->id,
            'amount' => 4800,
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->patch(route('employee.reservations.status', $reservation->id), ['status' => 'completed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'available',
            'cleaning_status' => 'dirty',
        ]);
    }

    public function test_employee_cancellation_emails_guest_once_with_reservation_details(): void
    {
        Mail::fake();
        Storage::fake('public');
        Storage::disk('public')->put('rooms/305.jpg', 'sample room image');

        $employee = User::factory()->create(['role' => 'employee']);
        $room = Room::create([
            'room_number' => '305',
            'room_type' => 'Deluxe',
            'image' => 'rooms/305.jpg',
            'price' => 3200,
            'floor' => '3',
            'status' => 'reserved',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Jordan Lee',
            'guest_email' => 'jordan@example.com',
            'guest_phone' => '09123456789',
            'check_in' => now()->addDay()->toDateString(),
            'room_check_in_time' => '14:00',
            'check_out' => now()->addDays(3)->toDateString(),
            'room_check_out_time' => '12:00',
            'status' => 'pending',
            'total_amount' => 6400,
        ]);

        $this->actingAs($employee)
            ->patch(route('employee.reservations.status', $reservation->id), [
                'status' => 'cancelled',
                'category' => 'rooms',
            ])
            ->assertSessionHas('success');

        Mail::assertSent(ReservationCancelled::class, fn (ReservationCancelled $mail) =>
            $mail->hasTo('jordan@example.com')
                && $mail->reservation->id === $reservation->id
                && $mail->reservation->status === 'cancelled'
                && str_contains($mail->render(), asset('storage/rooms/305.jpg'))
        );

        $this->patch(route('employee.reservations.status', $reservation->id), [
            'status' => 'cancelled',
            'category' => 'rooms',
        ]);

        Mail::assertSent(ReservationCancelled::class, 1);
    }

    public function test_guest_checkout_notifies_housekeeping_when_room_becomes_vacant_and_dirty(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        Room::create([
            'room_number' => '205',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '2',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $room = Room::create([
            'room_number' => '204',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '2',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = Reservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Checkout Guest',
            'guest_email' => 'checkout@example.com',
            'guest_phone' => '09123456789',
            'check_in' => now()->toDateString(),
            'check_out' => now()->addDay()->toDateString(),
            'status' => 'checked-in',
            'total_amount' => 0,
        ]);

        $this->actingAs($employee)
            ->patch(route('employee.reservations.status', $reservation->id), ['status' => 'completed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'status' => 'available',
            'cleaning_status' => 'dirty',
        ]);
        $notification = $housekeepingStaff->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('Vacant dirty room needs cleaning', $notification->data['title']);
        $this->assertStringContainsString('Room 204 is vacant and dirty', $notification->data['message']);
        $this->assertSame(
            '/housekeeping/room-status-update?room_id=' . $room->id,
            $notification->data['url']
        );

        $response = $this->actingAs($housekeepingStaff)->get($notification->data['url']);

        $response->assertOk()
            ->assertSee('id="room-row-' . $room->id . '" class="notification-room-target notification-room-highlight"', false)
            ->assertSee('class="table-pagination"', false)
            ->assertSee('const notificationRoomId = ' . $room->id . ';', false)
            ->assertSee('background-color:#ffe3ad !important', false)
            ->assertSee('setTimeout(() => notificationRoomRow.classList.remove(\'notification-room-highlight\'), 3000)', false);

        $this->assertGreaterThan(1, substr_count($response->getContent(), 'id="room-row-'));
    }

    public function test_housekeeping_room_status_update_notifies_employees_and_housekeeping(): void
    {
        $housekeepingStaff = Staff::factory()->create(['role' => 'housekeeping']);
        $employee = Staff::factory()->create(['role' => 'employee']);
        $room = Room::create([
            'room_number' => '203',
            'room_type' => 'Standard',
            'price' => 2400,
            'floor' => '2',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);

        $this->actingAs($housekeepingStaff)
            ->patch(route('housekeeping.rooms.cleaning', $room->id), ['cleaning_status' => 'dirty'])
            ->assertSessionHas('success', 'Room 203 status updated.');

        $employeeNotification = $employee->notifications()->first();
        $housekeepingNotification = $housekeepingStaff->notifications()->first();

        $this->assertNotNull($employeeNotification);
        $this->assertSame('Room status updated', $employeeNotification->data['title']);
        $this->assertSame('/employee/room-status?room_id=' . $room->id, $employeeNotification->data['url']);

        $this->assertNotNull($housekeepingNotification);
        $this->assertSame('Room status updated', $housekeepingNotification->data['title']);
        $this->assertSame('/housekeeping/room-status-update?room_id=' . $room->id, $housekeepingNotification->data['url']);

        $this->actingAs($employee)
            ->getJson(route('employee.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Room status updated')
            ->assertJsonPath('data.0.url', '/employee/room-status?room_id=' . $room->id);

        $this->actingAs($housekeepingStaff)
            ->getJson(route('housekeeping.notifications.index'))
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Room status updated')
            ->assertJsonPath('data.0.url', '/housekeeping/room-status-update?room_id=' . $room->id);
    }

    public function test_unpaid_reservation_cannot_be_checked_out(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $reservation = Reservation::create([
            'guest_name' => 'Unpaid Guest',
            'guest_email' => 'unpaid@example.com',
            'guest_phone' => '09123456789',
            'check_in' => now()->toDateString(),
            'check_out' => now()->addDay()->toDateString(),
            'status' => 'checked-in',
            'total_amount' => 9000,
        ]);

        $this->actingAs($user)
            ->patch(route('employee.reservations.status', $reservation->id), ['status' => 'completed'])
            ->assertStatus(422);

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'checked-in']);
    }
}
