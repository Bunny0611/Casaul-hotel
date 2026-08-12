<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_send_a_new_message(): void
    {
        $user = User::factory()->create([
            'role' => 'employee',
            'email' => 'employee@example.com',
            'name' => 'Employee User',
        ]);

        $response = $this->actingAs($user)->post(route('employee.messages.store'), [
            'recipient' => 'Housekeeping Team',
            'subject' => 'Room Cleaning',
            'message' => 'Please clean room 204 before check-in.',
        ]);

        $response->assertRedirect(route('employee.messages'));
        $this->assertDatabaseHas('messages', [
            'customer_name' => 'Employee User',
            'customer_email' => 'employee@example.com',
        ]);
    }
}
