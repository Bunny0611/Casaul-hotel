<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_dashboard_does_not_show_dummy_activity_data(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee@example.com',
            'name' => 'Employee User',
        ]);

        $response = $this->actingAs($user)->get(route('employee.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Guest John Doe checked in.');
        $response->assertSee('No recent activity yet.');
    }

    public function test_employee_can_confirm_a_reservation(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee2@example.com',
            'name' => 'Employee User 2',
        ]);

        $room = Room::create([
            'room_number' => '205',
            'room_type' => 'Deluxe',
            'price' => 2500,
            'floor' => '2',
            'status' => 'available',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Maria Cruz',
            'guest_email' => 'maria@example.com',
            'guest_phone' => '09123456789',
            'check_in' => '2026-08-26',
            'check_out' => '2026-08-29',
            'status' => 'pending',
            'total_amount' => 7500,
            'special_requests' => 'Late check-in',
        ]);

        $response = $this->actingAs($user)->patch(route('employee.reservations.status', $reservation->id), [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'confirmed',
        ]);
    }
}
