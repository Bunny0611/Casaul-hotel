<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestSignupDiagTest extends TestCase
{
    public function test_guest_register_redirects_to_profile()
    {
        $response = $this->post('/guest/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_initial' => 'A',
            'email' => 'diag'.uniqid().'@example.com',
            'contact_no' => '09123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->dump();
        $response->assertRedirect('/profile');
        $this->assertAuthenticated();
    }
}
