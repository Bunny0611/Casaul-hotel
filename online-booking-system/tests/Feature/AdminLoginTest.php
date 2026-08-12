<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_login_uses_the_selected_role_from_the_login_as_dropdown(): void
    {
        $user = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'employee@example.com',
            'password' => 'password',
            'role' => 'employee',
        ]);

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
