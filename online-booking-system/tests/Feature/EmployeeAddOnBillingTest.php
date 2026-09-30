<?php

namespace Tests\Feature;

use App\Models\GuestRequest;
use App\Models\DiningMenu;
use App\Models\Facility;
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

    public function test_employee_can_manage_catalog_and_custom_charges_for_only_the_selected_room_reservation(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $room = Room::create([
            'room_number' => '118',
            'room_type' => 'Standard',
            'price' => 2500,
            'floor' => '1',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Charge Guest',
            'guest_email' => 'charges@example.com',
            'guest_phone' => '09123456789',
            'check_in' => today(),
            'check_out' => today(),
            'number_of_guests' => 1,
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);
        $otherReservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Other Guest',
            'guest_email' => 'other@example.com',
            'guest_phone' => '09123456780',
            'check_in' => today()->addDay(),
            'check_out' => today()->addDay(),
            'number_of_guests' => 1,
            'status' => 'confirmed',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);
        $existingCharge = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Pillows',
            'quantity' => 1,
            'unit_price' => 50,
            'subtotal' => 50,
            'status' => 'Completed',
            'is_billable' => true,
            'charge_type' => 'custom',
            'source' => 'Phone Call',
        ]);
        $pendingRequest = $this->makeGuestRequest($reservation, [
            'request_type' => 'Pending Extra Bed',
            'quantity' => 1,
            'unit_price' => 500,
            'subtotal' => 500,
            'status' => 'New',
            'is_billable' => true,
        ]);
        $catalogRequest = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Towels',
            'quantity' => 1,
            'unit_price' => 80,
            'subtotal' => 80,
            'status' => 'New',
            'is_billable' => true,
        ]);
        $parking = Facility::create([
            'name' => 'Parking',
            'price' => 50,
            'pricing_basis' => 'Per Stay',
            'status' => 'available',
        ]);
        $menu = DiningMenu::create([
            'name' => 'Chicken Adobo',
            'category' => 'Main Course',
            'price' => 250,
            'status' => 'available',
        ]);
        $secondMenu = DiningMenu::create([
            'name' => 'Fresh Lemonade',
            'category' => 'Beverage',
            'price' => 100,
            'status' => 'available',
        ]);

        $this->actingAs($employee)->get(route('employee.reservation') . '?tab=rooms')
            ->assertOk()
            ->assertSeeText('Reservation Details')
            ->assertSeeText('Add-ons & Charges')
            ->assertSeeText('Charged Services & Additional Charges')
            ->assertSeeText('Update the reservation details below.');

        $this->actingAs($employee)->getJson(route('employee.reservations.charges.index', $reservation->id))
            ->assertOk()
            ->assertJsonPath('total', 50)
            ->assertJsonPath('charges.0.id', $existingCharge->id);

        $this->actingAs($employee)->postJson(route('employee.reservations.charges.store', $reservation->id), [
            'charge_type' => 'guest_addon',
            'name' => 'Parking',
            'facility_id' => $parking->id,
            'quantity' => 1,
            'unit_price' => 999,
            'source' => 'Front Desk',
        ])->assertCreated()->assertJsonPath('charge.unit_price', 50);

        $diningResponse = $this->actingAs($employee)->postJson(route('employee.reservations.charges.store', $reservation->id), [
            'charge_type' => 'dining',
            'items' => [
                ['dining_menu_id' => $menu->id, 'quantity' => 2],
                ['dining_menu_id' => $secondMenu->id, 'quantity' => 1],
            ],
        ])->assertCreated()->assertJsonCount(2, 'charges')->assertJsonPath('charges.0.unit_price', 250);
        $diningChargeId = $diningResponse->json('charges.0.id');
        $secondDiningChargeId = $diningResponse->json('charges.1.id');

        $customBatchResponse = $this->actingAs($employee)->postJson(route('employee.reservations.charges.store', $reservation->id), [
            'charge_type' => 'custom',
            'items' => [
                ['guest_request_id' => $pendingRequest->id, 'quantity' => 2],
                ['guest_request_id' => $catalogRequest->id, 'quantity' => 1, 'unit_price' => 90],
            ],
            'source' => 'Front Desk',
        ])->assertCreated()->assertJsonCount(2, 'charges')->assertJsonPath('charges.0.unit_price', 500);
        $customSourceChargeId = $customBatchResponse->json('charges.0.id');
        $customOtherChargeId = $customBatchResponse->json('charges.1.id');

        $customResponse = $this->actingAs($employee)->postJson(route('employee.reservations.charges.store', $reservation->id), [
            'charge_type' => 'custom',
            'name' => 'Late-night service',
            'quantity' => 3,
            'unit_price' => 50,
            'source' => 'Phone Call',
            'notes' => 'Guest requested delivery to room.',
        ])->assertCreated()->assertJsonPath('charge.total', 150);
        $customChargeId = $customResponse->json('charge.id');

        $this->actingAs($employee)->getJson(route('employee.reservations.charges.index', $reservation->id))
            ->assertJsonPath('total', 1940);

        $this->actingAs($employee)->putJson(route('employee.reservations.charges.update', [$reservation->id, $customChargeId]), [
            'charge_type' => 'custom',
            'name' => 'Late-night service',
            'quantity' => 2,
            'unit_price' => 100,
            'source' => 'Phone Call',
            'notes' => 'Updated quantity and price.',
        ])->assertOk()->assertJsonPath('charge.total', 200);

        $this->actingAs($employee)->putJson(route('employee.reservations.charges.update', [$otherReservation->id, $customChargeId]), [
            'charge_type' => 'custom',
            'name' => 'Wrong reservation',
            'quantity' => 1,
            'unit_price' => 1,
        ])->assertNotFound();

        $this->actingAs($employee)->deleteJson(route('employee.reservations.charges.destroy', [$reservation->id, $customChargeId]))
            ->assertOk()->assertJson(['deleted' => true]);
        $this->actingAs($employee)->getJson(route('employee.reservations.charges.index', $reservation->id))
            ->assertJsonPath('total', 1790);

        $this->assertDatabaseHas('room_reservations', ['id' => $reservation->id, 'guest_name' => 'Charge Guest']);
        $this->assertDatabaseHas('guest_requests', ['id' => $diningChargeId, 'reservation_key' => $reservation->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $secondDiningChargeId, 'dining_menu_id' => $secondMenu->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $customSourceChargeId, 'source_guest_request_id' => $pendingRequest->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $customOtherChargeId, 'source_guest_request_id' => $catalogRequest->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $pendingRequest->id, 'status' => 'New', 'reservation_key' => $reservation->id]);
        $this->assertDatabaseHas('guest_requests', ['id' => $catalogRequest->id, 'status' => 'New', 'reservation_key' => $reservation->id]);
        $this->assertDatabaseMissing('guest_requests', ['id' => $customChargeId]);
    }

    public function test_delivered_guest_request_moves_into_existing_employee_charged_add_on_section_once(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $housekeeping = User::factory()->create(['role' => 'housekeeping']);
        $room = Room::create([
            'room_number' => '110',
            'room_type' => 'Standard',
            'price' => 2500,
            'floor' => '1',
            'status' => 'occupied',
            'cleaning_status' => 'clean',
            'capacity' => 2,
        ]);
        $reservation = RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Delivered Guest',
            'guest_email' => 'delivered@example.com',
            'guest_phone' => '09123456789',
            'check_in' => today(),
            'check_out' => today(),
            'number_of_guests' => 1,
            'status' => 'checked-in',
            'total_amount' => 2500,
            'amount_paid' => 0,
        ]);
        $guestRequest = $this->makeGuestRequest($reservation, [
            'request_type' => 'Extra Towels',
            'unit_price' => 80,
            'subtotal' => 80,
            'is_billable' => true,
            'status' => 'New',
        ]);

        $this->actingAs($employee)->get(route('employee.checkin'))
            ->assertDontSee('Extra Towels');

        $this->actingAs($housekeeping)->postJson(
            route('housekeeping.guest-requests.mark-delivered', $guestRequest->id)
        )->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('guest_requests', [
            'id' => $guestRequest->id,
            'status' => 'Delivered',
            'billing_status' => 'posted',
        ]);
        $this->assertDatabaseHas('room_reservations', [
            'id' => $reservation->id,
            'total_amount' => 2580,
        ]);

        $this->actingAs($housekeeping)->postJson(
            route('housekeeping.guest-requests.mark-delivered', $guestRequest->id)
        )->assertOk();

        $this->assertDatabaseCount('guest_requests', 1);
        $this->actingAs($employee)->get(route('employee.reservation'))
            ->assertOk()
            ->assertSee('Charged Add-On Services')
            ->assertSee('Extra Towels')
            ->assertSee('"total":80', false);
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
