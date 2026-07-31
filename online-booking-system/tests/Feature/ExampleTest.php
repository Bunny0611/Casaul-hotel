<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_accommodation_page_shows_the_requested_room_types(): void
    {
        $response = $this->get('/accommodation');

        $response->assertStatus(200)
            ->assertSee('Deluxe Room')
            ->assertSee('Standard Room')
            ->assertSee('Executive Suite')
            ->assertSee('Presidential Suite');
    }
}
