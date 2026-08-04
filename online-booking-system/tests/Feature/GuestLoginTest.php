<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GuestLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_login_page_is_accessible_and_logs_in_a_user(): void
    {
        $user = User::factory()->create([
            'email' => 'guest@example.com',
            'password' => Hash::make('guest-password'),
        ]);

        $response = $this->get(route('guest.login'));
        $response->assertOk();
        $response->assertSee('Guest Access');

        $loginResponse = $this->post(route('guest.login.submit'), [
            'email' => 'guest@example.com',
            'password' => 'guest-password',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }
}
