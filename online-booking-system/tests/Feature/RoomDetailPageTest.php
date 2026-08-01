<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoomDetailPageTest extends TestCase
{
    public function test_deluxe_room_detail_page_loads(): void
    {
        $response = $this->get('/accommodation/deluxe-room');

        $response->assertStatus(200);
        $response->assertSee('Deluxe Room');
        $response->assertSee('Book This Room');
    }

    public function test_homepage_has_four_clickable_room_links(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Deluxe Room');
        $response->assertSee('Executive Room');
        $response->assertSee('Presidential Room');
        $response->assertSee('Standard Room');
        $response->assertSee(route('accommodation.room', ['slug' => 'deluxe-room']));
    }
}
