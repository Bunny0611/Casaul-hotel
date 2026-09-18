<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_calendar_page_loads(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin-calendar@example.com',
            'name' => 'Admin Calendar User',
        ]);

        $response = $this->actingAs($user)->get(route('admin.calendar'));

        $response->assertOk();
        $response->assertSee('Calendar');
    }

    public function test_employee_calendar_page_loads(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee-calendar@example.com',
            'name' => 'Employee Calendar User',
        ]);

        $response = $this->actingAs($user)->get(route('employee.calendar'));

        $response->assertOk();
        $response->assertSee('Calendar');
    }
}
