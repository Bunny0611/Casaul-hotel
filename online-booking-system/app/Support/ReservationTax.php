<?php

namespace App\Support;

use App\Models\TaxSetting;

class ReservationTax
{
    public static function calculateAndStore($reservation, array $charges, ?array $settings = null): array
    {
        $settings ??= TaxSetting::current()->pricingSettings();
        $breakdown = ReservationPricing::taxBreakdown($charges, $settings);
        $taxableCategories = ! empty($settings['enabled'])
            ? array_values($settings['categories'] ?? [])
            : [];

        $reservation->taxSnapshot()->updateOrCreate([], [
            ...$breakdown,
            'total_amount' => $breakdown['total'],
            'categories' => $taxableCategories,
        ]);

        return $breakdown;
    }
}
