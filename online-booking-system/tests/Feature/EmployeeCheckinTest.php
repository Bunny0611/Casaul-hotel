<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
