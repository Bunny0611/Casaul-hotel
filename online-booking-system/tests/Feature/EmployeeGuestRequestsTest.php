<?php

namespace Tests\Feature;

use Illuminate\Auth\GenericUser;
use Tests\TestCase;

class EmployeeGuestRequestsTest extends TestCase
{
    public function test_employee_can_resolve_a_guest_request(): void
    {
        $user = new GenericUser([
            'id' => 1,
            'name' => 'Employee',
            'email' => 'employee@example.com',
        ]);

        $this->actingAs($user)
            ->withSession([
                'employee_guest_requests' => [[
                    'id' => 1,
                    'title' => 'Room 305 - Extra Towels',
                    'requested_at' => '10:15 AM',
                    'status' => 'Pending',
                ]],
            ])
            ->post(route('employee.guest-requests.resolve', ['id' => 1]))
            ->assertRedirect(route('employee.guest-requests'))
            ->assertSessionHas('success', 'Guest request resolved.');
    }
}
