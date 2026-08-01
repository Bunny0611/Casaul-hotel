<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reports_page_loads(): void
    {
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

        $response = $this->get(route('admin.reports'));

        $response->assertOk();
        $response->assertSee('Comprehensive Reporting System');
        $response->assertSee('Total Revenue');
    }
}
