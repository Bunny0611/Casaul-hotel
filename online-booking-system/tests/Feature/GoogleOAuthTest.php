<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    public function test_invalid_google_oauth_state_redirects_to_sign_in_with_a_retry_message(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');

        $provider = Mockery::mock();
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new InvalidStateException());

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $this->get(route('guest.google.callback'))
            ->assertRedirect(route('home', ['auth' => 'signin']))
            ->assertSessionHasErrors([
                'email' => 'Google sign-in could not be verified. Please try again.',
            ]);
    }
}