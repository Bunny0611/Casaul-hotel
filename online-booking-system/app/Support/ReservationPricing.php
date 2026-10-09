<?php

namespace App\Support;

use App\Models\DiningMenu;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Room;
use Carbon\Carbon;

class ReservationPricing
{
    public static function room(Room $room, $checkIn, $checkOut, int $numberOfGuests, ?int $adultGuests = null, ?int $kidGuests = null): float
    {
        $nights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        $capacity = max(1, (int) ($room->capacity ?? 2));
        $extraGuestPrice = (float) ($room->adult_guest_price ?? (str_contains(strtolower((string) $room->room_type), 'standard') ? 500 : 650));
        $kidGuestPrice = (float) ($room->kid_guest_price ?? ($extraGuestPrice / 2));

        if (($adultGuests ?? 0) + ($kidGuests ?? 0) > 0) {
            $extraGuestTotal = (max(0, (int) $adultGuests) * $extraGuestPrice)
                + (max(0, (int) $kidGuests) * $kidGuestPrice);
        } else {
            $extraGuests = max(0, $numberOfGuests - $capacity);
            $extraGuestTotal = $extraGuests * $extraGuestPrice;
        }

        return round(((float) $room->price + $extraGuestTotal) * $nights, 2);
    }

    public static function roomChargeComponents(Room $room, $checkIn, $checkOut, int $numberOfGuests, ?int $adultGuests = null, ?int $kidGuests = null): array
    {
        $nights = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        $roomCharge = round((float) $room->price * $nights, 2);
        $extraGuestCharge = round(max(0, self::room($room, $checkIn, $checkOut, $numberOfGuests, $adultGuests, $kidGuests) - $roomCharge), 2);

        return array_values(array_filter([
            ['category' => 'rooms', 'amount' => $roomCharge],
            ['category' => 'extra_person', 'amount' => $extraGuestCharge],
        ], fn (array $charge) => $charge['amount'] > 0));
    }

    public static function facilities($facilities, int $quantity, $checkIn, $checkOut, ?int $durationHours = null): float
    {
        $stayDays = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        $quantity = max(1, $quantity);
        $durationHours = max(1, $durationHours ?? 1);
        $billableDays = max($stayDays, (int) ceil($durationHours / 24));

        return round(collect($facilities)->sum(function (Facility $facility) use ($quantity, $stayDays, $durationHours, $billableDays) {
            $price = (float) $facility->price;
            $pricingBasis = strtolower(trim((string) $facility->pricing_basis));

            return match ($pricingBasis) {
                'per stay + per vehicle' => $price * ($stayDays + $quantity),
                'per vehicle', 'per person' => $price * $quantity,
                'per hour' => $price * $durationHours,
                'per day' => $price * $billableDays,
                default => $price,
            };
        }), 2);
    }

    public static function facilityChargeComponents($facilities, int $quantity, $checkIn, $checkOut, ?int $durationHours = null): array
    {
        return collect($facilities)->map(function (Facility $facility) use ($quantity, $checkIn, $checkOut, $durationHours) {
            $category = str_contains(strtolower(trim((string) $facility->pricing_basis)), 'vehicle')
                ? 'parking'
                : 'services_addons';

            return [
                'category' => $category,
                'amount' => self::facilities(collect([$facility]), $quantity, $checkIn, $checkOut, $durationHours),
            ];
        })->filter(fn (array $charge) => $charge['amount'] > 0)->values()->all();
    }

    public static function facilitySelectionComponents($facilities, array $selections, $checkIn, $checkOut, int $defaultQuantity = 1, ?int $defaultDurationHours = null): array
    {
        $selectionById = collect($selections)->keyBy(fn (array $selection) => (int) ($selection['facility_id'] ?? 0));

        return collect($facilities)->map(function (Facility $facility) use ($selectionById, $checkIn, $checkOut, $defaultQuantity, $defaultDurationHours) {
            $selection = $selectionById->get((int) $facility->id, []);
            $quantity = max(1, (int) ($selection['quantity'] ?? $defaultQuantity));
            $durationHours = max(1, (int) ($selection['duration_hours'] ?? $defaultDurationHours ?? 1));
            $category = str_contains(strtolower(trim((string) $facility->pricing_basis)), 'vehicle')
                ? 'parking'
                : 'services_addons';

            return [
                'category' => $category,
                'amount' => self::facilities(collect([$facility]), $quantity, $checkIn, $checkOut, $durationHours),
            ];
        })->filter(fn (array $charge) => $charge['amount'] > 0)->values()->all();
    }

    public static function facilityDurationHours($checkIn, $startTime, $checkOut, $endTime): int
    {
        $start = Carbon::parse(Carbon::parse($checkIn)->toDateString() . ' ' . ($startTime ?: '00:00'));
        $end = Carbon::parse(Carbon::parse($checkOut)->toDateString() . ' ' . ($endTime ?: '00:00'));

        return max(1, (int) ceil(abs($start->diffInMinutes($end)) / 60));
    }

    public static function events($events, int $numberOfGuests = 1, int $durationHours = 1): float
    {
        $numberOfGuests = max(1, $numberOfGuests);
        $durationHours = max(1, $durationHours);

        return round(collect($events)->sum(function (Event $event) use ($numberOfGuests, $durationHours) {
            $price = (float) $event->price;

            return match (strtolower(trim((string) $event->pricing_basis))) {
                'per person' => $price * $numberOfGuests,
                'per hour' => $price * $durationHours,
                default => $price,
            };
        }), 2);
    }

    public static function eventChargeComponents($events, int $numberOfGuests = 1, int $durationHours = 1): array
    {
        return collect($events)->map(fn (Event $event) => [
            'category' => 'events',
            'amount' => self::events(collect([$event]), $numberOfGuests, $durationHours),
        ])->filter(fn (array $charge) => $charge['amount'] > 0)->values()->all();
    }

    public static function dining(array $selections): float
    {
        if ($selections === []) {
            return 0.0;
        }

        $menus = DiningMenu::whereIn('id', collect($selections)->pluck('dining_id')->unique())->get()->keyBy('id');

        return round(collect($selections)->sum(function (array $selection) use ($menus) {
            $menu = $menus->get((int) $selection['dining_id']);
            return $menu ? (float) $menu->price * max(1, (int) ($selection['quantity'] ?? 1)) : 0;
        }), 2);
    }

    public static function diningChargeComponents(array $selections): array
    {
        if ($selections === []) {
            return [];
        }

        $menus = DiningMenu::whereIn('id', collect($selections)->pluck('dining_id')->unique())->get()->keyBy('id');

        return collect($selections)->map(function (array $selection) use ($menus) {
            $menu = $menus->get((int) $selection['dining_id']);

            return [
                'category' => 'dining',
                'amount' => $menu ? round((float) $menu->price * max(1, (int) ($selection['quantity'] ?? 1)), 2) : 0,
            ];
        })->filter(fn (array $charge) => $charge['amount'] > 0)->values()->all();
    }

    public static function taxBreakdown(array $charges, array $settings): array
    {
        $charges = collect($charges)->map(function (array $charge) {
            return [
                'category' => (string) ($charge['category'] ?? ''),
                'amount' => round(max(0, (float) ($charge['amount'] ?? 0)), 2),
            ];
        });
        $subtotal = round((float) $charges->sum('amount'), 2);
        $taxableCharges = !empty($settings['enabled'])
            ? $charges->whereIn('category', $settings['categories'] ?? [])
            : collect();
        $taxableSubtotal = round((float) $taxableCharges->sum('amount'), 2);
        $rate = round(max(0, (float) ($settings['rate'] ?? 0)), 2);
        $inclusive = (bool) ($settings['inclusive'] ?? false);
        $taxableBase = $inclusive && $rate > 0
            ? round($taxableSubtotal / (1 + ($rate / 100)), 2)
            : $taxableSubtotal;
        $taxAmount = $inclusive
            ? round($taxableSubtotal - $taxableBase, 2)
            : round($taxableBase * $rate / 100, 2);

        return [
            'tax_name' => (string) ($settings['name'] ?? 'Simulated VAT'),
            'tax_rate' => $rate,
            'tax_enabled' => !empty($settings['enabled']),
            'tax_inclusive' => $inclusive,
            'subtotal' => $subtotal,
            'taxable_base' => $taxableBase,
            'tax_amount' => $taxAmount,
            'total' => $inclusive ? $subtotal : round($subtotal + $taxAmount, 2),
        ];
    }
}