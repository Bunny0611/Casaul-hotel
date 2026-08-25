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
            'role' => 'guest',
        ]);

        $response = $this->get(route('guest.login'));
        $response->assertOk();
        $response->assertSee('Guest Sign In');

        $loginResponse = $this->post(route('guest.login.submit'), [
            'email' => 'guest@example.com',
            'password' => 'guest-password',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_signup_logs_in_user_and_shows_profile_menu(): void
    {
        $response = $this->post(route('guest.register.submit'), [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_initial' => 'L.',
            'email' => 'maria@example.com',
            'contact_no' => '09123456789',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertAuthenticatedAs(User::where('email', 'maria@example.com')->first());

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertSee('profile-dropdown');
        $homeResponse->assertSee('My Profile');
        $homeResponse->assertSee('Logout');
    }

    public function test_authenticated_user_sees_profile_icon_even_without_guest_role(): void
    {
        $user = User::factory()->create([
            'name' => 'Staff User',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertSee('profile-dropdown');
        $response->assertSee('Staff User');
        $response->assertDontSee('id="guest-signin-trigger"');
    }

    public function test_invalid_guest_signup_returns_validation_errors_to_homepage(): void
    {
        $response = $this->from(route('home'))->post(route('guest.register.submit'), [
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'middle_initial' => 'L.',
            'email' => 'not-an-email',
            'contact_no' => '09123456789',
            'password' => 'secret123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors(['email', 'password']);
        $response->assertSessionHasInput('first_name', 'Maria');
    }
}
