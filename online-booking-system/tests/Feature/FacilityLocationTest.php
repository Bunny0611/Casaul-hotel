<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FacilityLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_facilities_store_and_retrieve_location_data(): void
    {
        $this->assertTrue(Schema::hasColumn('facilities', 'location'));

        $facility = Facility::create([
            'name' => 'Swimming Pool',
            'description' => 'Pool for guests',
            'price' => 1200,
            'status' => 'available',
            'location' => 'Ground Floor',
            'capacity' => 30,
            'pricing_basis' => 'Per Day',
            'scheduling_requirement' => 'Date Required',
        ]);

        $this->assertSame('Ground Floor', $facility->fresh()->location);
        $this->assertSame(30, $facility->fresh()->capacity);
    }
}
