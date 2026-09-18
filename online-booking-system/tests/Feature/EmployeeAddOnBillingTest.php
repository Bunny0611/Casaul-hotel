<?php

namespace Tests\Feature;

use App\Models\GuestRequest;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeAddOnBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_billing_charges_only_completed_billable_add_ons_once_and_preserves_history(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
            'email' => 'front-desk@example.com',
        ]);
        $room = Room::create([
            'room_number' => '109',
            'room_type' => 'Standard',
            'price' => 2500,
            'floor' => '1',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'haha h ahaha',
            'guest_email' => 'haha@example.com',
            'guest_phone' => '09123456789',
            'check_in' => today(),
            'room_check_in_time' => '14:00',
            'check_out' => today(),
            'room_check_out_time' => '12:00',
            'number_of_guests' => 1,
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);

        $billable = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Towels',
            'quantity' => 2,
            'unit_price' => 50,
            'subtotal' => 100,
            'status' => 'Completed',
            'is_billable' => true,
        ]);
        $nonBillable = $this->makeGuestRequest($reservation, [
            'request_type' => 'Broken Aircon',
            'status' => 'Completed',
            'is_billable' => false,
            'unit_price' => 500,
            'subtotal' => 500,
        ]);
        $pending = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Pillows',
            'status' => 'Pending',
            'is_billable' => true,
            'unit_price' => 80,
            'subtotal' => 80,
        ]);
        $rejected = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Towels',
            'status' => 'Rejected',
            'is_billable' => true,
            'unit_price' => 50,
            'subtotal' => 50,
        ]);
        $alreadyCharged = $this->makeGuestRequest($reservation, [
            'request_type' => 'Toiletries',
            'status' => 'Completed',
            'is_billable' => true,
            'billing_status' => 'posted',
            'unit_price' => 100,
            'subtotal' => 100,
        ]);

        $checkout = $this->actingAs($employee)->get(route('employee.checkin'));
        $checkout->assertOk();
        $checkout->assertSee('Extra Towels');
        $checkout->assertSee('data-addon-total="100.00"', false);
        $checkout->assertSee('data-total="2600.00"', false);
        $checkout->assertDontSee('Broken Aircon');
        $checkout->assertDontSee('Extra Pillows');
        $checkout->assertDontSee('Toiletries');

        $payment = $this->actingAs($employee)->postJson(
            route('employee.reservations.payments.store', $reservation->id),
            [
                'amount' => 2600,
                'payment_method' => 'Cash',
                'payment_date' => today()->toDateString(),
            ]
        );
        $payment->assertOk()->assertJson([
            'total' => 2600,
            'paid' => 2600,
            'balance' => 0,
            'status' => 'Paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'paymentable_type' => RoomReservation::class,
            'paymentable_id' => $reservation->id,
            'amount' => 2600,
            'recorded_by' => $employee->id,
        ]);
        $this->assertDatabaseHas('guest_requests', [
            'id' => $billable->id,
            'billing_status' => 'posted',
        ]);
        $this->assertDatabaseHas('guest_requests', [
            'id' => $nonBillable->id,
            'billing_status' => 'pending',
        ]);
        $this->assertDatabaseHas('guest_requests', [
            'id' => $pending->id,
            'billing_status' => 'pending',
        ]);
        $this->assertDatabaseHas('guest_requests', [
            'id' => $rejected->id,
            'billing_status' => 'pending',
        ]);
        $this->assertDatabaseHas('guest_requests', [
            'id' => $alreadyCharged->id,
            'billing_status' => 'posted',
        ]);

        $this->actingAs($employee)
            ->postJson(route('employee.reservations.payments.store', $reservation->id), [
                'amount' => 100,
                'payment_method' => 'Cash',
                'payment_date' => today()->toDateString(),
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('guest_requests', ['id' => $billable->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $alreadyCharged->id]);

        $receipt = $this->actingAs($employee)->get(route('employee.reservation'));
        $receipt->assertOk();
        $receipt->assertSee('Charged Add-On Services');
        $receipt->assertSee('Extra Towels');
        $receipt->assertSee('Add-On Total');
        $receipt->assertDontSee('Broken Aircon');
        $receipt->assertDontSee('Extra Pillows');
    }

    public function test_editing_a_paid_reservation_does_not_overwrite_payment_records(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $room = Room::create([
            'room_number' => '109',
            'room_type' => 'Standard',
            'price' => 2500,
            'floor' => '1',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Paid Guest',
            'guest_email' => 'paid@example.com',
            'guest_phone' => '09123456789',
            'check_in' => today(),
            'check_out' => today(),
            'number_of_guests' => 1,
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);

        $this->actingAs($employee)->postJson(route('employee.reservations.payments.store', $reservation->id), [
            'amount' => 2500,
            'payment_method' => 'Cash',
            'payment_date' => today()->toDateString(),
        ])->assertOk();

        $this->actingAs($employee)->put(route('employee.reservations.update', $reservation->id), [
            'category' => 'rooms',
            'room_id' => $room->id,
            'guest_name' => 'Paid Guest Updated',
            'guest_email' => 'paid@example.com',
            'guest_phone' => '09123456789',
            'check_in' => today()->toDateString(),
            'check_out' => today()->toDateString(),
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ])->assertRedirect(route('employee.reservation'));

        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'paymentable_type' => RoomReservation::class,
            'paymentable_id' => $reservation->id,
            'amount' => 2500,
        ]);
        $this->assertDatabaseHas('room_reservations', [
            'id' => $reservation->id,
            'amount_paid' => 2500,
            'guest_name' => 'Paid Guest Updated',
        ]);
    }

    private function makeGuestRequest(RoomReservation $reservation, array $attributes): GuestRequest
    {
        return GuestRequest::create(array_merge([
            'reservation_id' => null,
            'room_id' => $reservation->room_id,
            'request_type' => 'Add-on',
            'description' => 'Test request',
            'department' => 'Employee',
            'priority' => 'Normal',
            'status' => 'New',
            'quantity' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
            'is_billable' => false,
            'billing_status' => 'pending',
            'reservation_type' => RoomReservation::class,
            'reservation_key' => $reservation->id,
            'submitted_at' => now(),
        ], $attributes));
    }
}
