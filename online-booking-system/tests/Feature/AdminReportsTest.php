<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\MaintenanceReport;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reports_page_loads(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $room = Room::create([
            'room_number' => '201',
            'room_type' => 'Deluxe',
            'price' => 1800.00,
            'floor' => '2nd',
            'capacity' => 2,
            'description' => 'Comfort room',
            'status' => 'available',
        ]);

        Reservation::create([
            'room_id' => $room->id,
            'guest_name' => 'Jane Doe',
            'guest_email' => 'jane@example.com',
            'guest_phone' => '09171234567',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'status' => 'completed',
            'total_amount' => 3600.00,
            'special_requests' => 'Late check-in',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports'));

        $response->assertOk();
        $response->assertSee('Comprehensive Reporting System');
        $response->assertSee('Total Revenue');
    }

    public function test_maintenance_report_is_shared_between_admin_and_housekeeping(): void
    {
        $admin = Staff::factory()->create(['role' => 'admin']);
        $housekeeping = Staff::factory()->create(['role' => 'housekeeping']);
        $room = Room::create([
            'room_number' => '301',
            'room_type' => 'Suite',
            'price' => 2500.00,
            'floor' => '3rd',
            'capacity' => 2,
            'description' => 'Suite room',
            'status' => 'available',
        ]);

        $report = MaintenanceReport::create([
            'room_number' => $room->room_number,
            'room_type' => $room->room_type,
            'reported_by' => 'Housekeeping User',
            'category' => 'Plumbing',
            'priority' => 'High',
            'problem' => 'Leaking faucet',
            'description' => 'The bathroom faucet is leaking.',
            'date_reported' => now(),
            'technician' => 'Unassigned',
            'status' => 'Pending',
        ]);

        $this->actingAs($admin)->get(route('admin.reports'))
            ->assertOk()
            ->assertSee($report->description);

        $this->actingAs($housekeeping)->get(route('housekeeping.maintenance-report'))
            ->assertOk()
            ->assertSee($report->description);

        $this->actingAs($admin)->patch(route('admin.maintenance-reports.status', $report), [
            'status' => 'Completed',
        ])->assertRedirect();

        $this->assertDatabaseHas('maintenance_reports', [
            'id' => $report->id,
            'status' => 'Completed',
        ]);
    }
}
