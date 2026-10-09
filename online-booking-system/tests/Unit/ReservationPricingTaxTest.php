<?php

namespace Tests\Unit;

use App\Models\Facility;
use App\Support\ReservationPricing;
use PHPUnit\Framework\TestCase;

class ReservationPricingTaxTest extends TestCase
{
    public function test_tax_exclusive_total_adds_tax_only_to_selected_categories(): void
    {
        $result = ReservationPricing::taxBreakdown([
            ['category' => 'rooms', 'amount' => 4000],
            ['category' => 'dining', 'amount' => 1000],
        ], [
            'enabled' => true,
            'name' => 'Sample VAT',
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['rooms'],
        ]);

        $this->assertSame(5000.0, $result['subtotal']);
        $this->assertSame(4000.0, $result['taxable_base']);
        $this->assertSame(480.0, $result['tax_amount']);
        $this->assertSame(5480.0, $result['total']);
    }

    public function test_tax_inclusive_total_extracts_tax_without_changing_price(): void
    {
        $result = ReservationPricing::taxBreakdown([
            ['category' => 'rooms', 'amount' => 5600],
        ], [
            'enabled' => true,
            'name' => 'Sample VAT',
            'rate' => 12,
            'inclusive' => true,
            'categories' => ['rooms'],
        ]);

        $this->assertSame(5600.0, $result['subtotal']);
        $this->assertSame(5000.0, $result['taxable_base']);
        $this->assertSame(600.0, $result['tax_amount']);
        $this->assertSame(5600.0, $result['total']);
    }

    public function test_sample_exclusive_example_adds_six_hundred_to_five_thousand(): void
    {
        $result = ReservationPricing::taxBreakdown([
            ['category' => 'rooms', 'amount' => 5000],
        ], [
            'enabled' => true,
            'name' => 'Sample VAT',
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['rooms'],
        ]);

        $this->assertSame(5000.0, $result['subtotal']);
        $this->assertSame(600.0, $result['tax_amount']);
        $this->assertSame(5600.0, $result['total']);
    }

    public function test_parking_tax_does_not_apply_to_other_facilities(): void
    {
        $parking = new Facility(['price' => 100, 'pricing_basis' => 'Per Vehicle']);
        $parking->id = 1;
        $service = new Facility(['price' => 50, 'pricing_basis' => 'Per Stay']);
        $service->id = 2;
        $charges = ReservationPricing::facilitySelectionComponents(collect([$parking, $service]), [
            ['facility_id' => 1, 'quantity' => 2],
            ['facility_id' => 2, 'quantity' => 2],
        ], '2026-11-01', '2026-11-02');

        $result = ReservationPricing::taxBreakdown($charges, [
            'enabled' => true,
            'name' => 'Sample VAT',
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['parking'],
        ]);

        $this->assertSame(250.0, $result['subtotal']);
        $this->assertSame(200.0, $result['taxable_base']);
        $this->assertSame(24.0, $result['tax_amount']);
        $this->assertSame(274.0, $result['total']);
    }

    public function test_disabled_tax_leaves_the_total_unchanged(): void
    {
        $result = ReservationPricing::taxBreakdown([
            ['category' => 'rooms', 'amount' => 5000],
        ], [
            'enabled' => false,
            'name' => 'Sample VAT',
            'rate' => 12,
            'inclusive' => false,
            'categories' => ['rooms'],
        ]);

        $this->assertSame(0.0, $result['taxable_base']);
        $this->assertSame(0.0, $result['tax_amount']);
        $this->assertSame(5000.0, $result['total']);
    }
}
