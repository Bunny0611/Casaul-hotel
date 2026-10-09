<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\GuestRequest;
use App\Models\Room;
use App\Models\RoomReservation;
use App\Models\Staff;
use App\Models\TaxSetting;
use App\Models\User;
use App\Support\ReservationTax;
use Tests\TestCase;

class ReservationTaxSnapshotTest extends TestCase
{
    public function test_default_tax_is_a_disabled_twelve_percent_sample(): void
    {
        $setting = TaxSetting::current();

        $this->assertSame(12.0, (float) $setting->rate);
        $this->assertFalse($setting->enabled);
        $this->assertSame([], $setting->categories);
    }

    public function test_changing_tax_settings_does_not_change_a_saved_reservation_snapshot(): void
    {
        $reservation = $this->makeRoomReservation();
        $setting = TaxSetting::current();
        $setting->update([
            'name' => 'Sample VAT',
            'rate' => 12,
            'enabled' => true,
            'inclusive' => false,
            'categories' => ['rooms'],
        ]);
        $breakdown = ReservationTax::calculateAndStore($reservation, [
            ['category' => 'rooms', 'amount' => 5000],
        ], $setting->fresh()->pricingSettings());
        $reservation->update(['total_amount' => $breakdown['total']]);

        $setting->update(['rate' => 20, 'name' => 'Changed Sample']);
        $saved = $reservation->fresh('taxSnapshot');

        $this->assertSame(5600.0, (float) $saved->total_amount);
        $this->assertSame(600.0, (float) $saved->taxSnapshot->tax_amount);
        $this->assertSame(12.0, (float) $saved->taxSnapshot->tax_rate);
        $this->assertSame('Sample VAT', $saved->taxSnapshot->tax_name);
    }

    public function test_legacy_reservation_without_snapshot_keeps_its_existing_total(): void
    {
        $reservation = $this->makeRoomReservation();
        TaxSetting::current()->update([
            'enabled' => true,
            'rate' => 12,
            'categories' => ['rooms'],
        ]);

        $saved = $reservation->fresh('taxSnapshot');

        $this->assertSame(5000.0, (float) $saved->total_amount);
        $this->assertNull($saved->taxSnapshot);
    }

    public function test_admin_can_configure_the_educational_tax_simulation(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.taxes'))
            ->assertOk()
            ->assertSee('Educational simulation only')
            ->assertSee('12.00');

        $this->actingAs($admin)->put(route('admin.taxes.update'), [
            'name' => 'Practice VAT',
            'rate' => 12,
            'enabled' => '1',
            'inclusive' => '0',
            'categories' => ['rooms', 'parking'],
        ])->assertRedirect();

        $setting = TaxSetting::current()->fresh();
        $this->assertSame('Practice VAT', $setting->name);
        $this->assertSame(12.0, (float) $setting->rate);
        $this->assertTrue($setting->enabled);
        $this->assertFalse($setting->inclusive);
        $this->assertEqualsCanonicalizing(['rooms', 'parking'], $setting->categories);
    }

    public function test_guest_booking_uses_server_tax_even_when_submitted_total_is_untaxed(): void
    {
        $guest = Guest::factory()->create([
            'name' => 'Tax Test Guest',
            'email' => 'tax-guest@example.test',
        ]);
        $room = Room::create([
            'room_number' => '102',
            'room_type' => 'Standard',
            'price' => 5000,
            'floor' => '1',
            'status' => 'available',
            'capacity' => 2,
        ]);
        TaxSetting::current()->update([
            'enabled' => true,
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['rooms'],
        ]);
        $checkIn = today()->addDay()->toDateString();

        $this->actingAs($guest, 'guest')->post(route('reservation.store'), [
            'room_id' => $room->id,
            'guest_name' => $guest->name,
            'guest_email' => $guest->email,
            'guest_phone' => '09123456789',
            'check_in' => $checkIn,
            'check_out' => today()->addDays(2)->toDateString(),
            'number_of_guests' => 2,
            'room_number_of_guests' => 2,
            'total_amount' => 5000,
            'payment_method' => 'Cash / Pay at Hotel',
        ])->assertRedirect(route('reservation'));

        $reservation = RoomReservation::where('guest_email', $guest->email)->firstOrFail()->load('taxSnapshot');
        $this->assertSame(5600.0, (float) $reservation->total_amount);
        $this->assertSame(5000.0, (float) $reservation->taxSnapshot->subtotal);
        $this->assertSame(5000.0, (float) $reservation->taxSnapshot->taxable_base);
        $this->assertSame(600.0, (float) $reservation->taxSnapshot->tax_amount);
        $this->assertSame(12.0, (float) $reservation->taxSnapshot->tax_rate);

        $reservation->update(['status' => 'confirmed']);
        $this->actingAs($guest, 'guest')->get(route('guest.receipts'))
            ->assertOk()
            ->assertSee('Sample VAT')
            ->assertSee('5,600.00');
    }

    public function test_staff_add_on_tax_is_snapshotted_and_included_in_payment_balance(): void
    {
        $employee = User::factory()->create(['role' => 'employee', 'email' => 'tax-employee@example.test']);
        $reservation = $this->makeRoomReservation();
        TaxSetting::current()->update([
            'enabled' => true,
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['services_addons'],
        ]);

        $chargeResponse = $this->actingAs($employee)->postJson(route('employee.reservations.charges.store', $reservation->id), [
            'charge_type' => 'custom',
            'name' => 'Late-night service',
            'quantity' => 2,
            'unit_price' => 100,
            'source' => 'Front Desk',
        ])->assertCreated()->assertJsonPath('charge.total', 224);
        $chargeId = $chargeResponse->json('charge.id');

        $this->actingAs($employee)->postJson(route('employee.reservations.payments.store', $reservation->id), [
            'amount' => 5224,
            'payment_method' => 'Cash',
            'payment_date' => today()->toDateString(),
        ])->assertOk()->assertJsonPath('total', 5224);

        $this->assertDatabaseHas('guest_requests', [
            'id' => $chargeId,
            'billing_status' => 'posted',
        ]);
        $this->assertDatabaseHas('reservation_tax_snapshots', [
            'reservationable_type' => GuestRequest::class,
            'reservationable_id' => $chargeId,
            'tax_amount' => 24,
            'total_amount' => 224,
        ]);

        $reservation->update(['status' => 'confirmed']);
        $guest = Guest::factory()->create(['email' => $reservation->guest_email]);
        $this->actingAs($guest, 'guest')->get(route('guest.receipts'))
            ->assertOk()
            ->assertSee('Late-night service')
            ->assertSee('5,224.00');
    }

    private function makeRoomReservation(): RoomReservation
    {
        $room = Room::create([
            'room_number' => '101',
            'room_type' => 'Standard',
            'price' => 5000,
            'floor' => '1',
            'status' => 'available',
            'capacity' => 2,
        ]);

        return RoomReservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.test',
            'guest_phone' => '09123456789',
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-02',
            'number_of_guests' => 2,
            'status' => 'pending',
            'total_amount' => 5000,
        ]);
    }
}
