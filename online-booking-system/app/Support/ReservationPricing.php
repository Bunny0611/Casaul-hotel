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
        $extraGuestPrice = str_contains(strtolower((string) $room->room_type), 'standard') ? 500 : 650;
        $kidGuestPrice = $extraGuestPrice / 2;

        if (($adultGuests ?? 0) + ($kidGuests ?? 0) > 0) {
            $extraGuestTotal = (max(0, (int) $adultGuests) * $extraGuestPrice)
                + (max(0, (int) $kidGuests) * $kidGuestPrice);
        } else {
            $extraGuests = max(0, $numberOfGuests - $capacity);
            $extraGuestTotal = $extraGuests * $extraGuestPrice;
        }

        return round(((float) $room->price + $extraGuestTotal) * $nights, 2);
    }

    public static function facilities($facilities, int $quantity, $checkIn, $checkOut): float
    {
        $stayDays = max(1, Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
        $quantity = max(1, $quantity);

        return round(collect($facilities)->sum(function (Facility $facility) use ($quantity, $stayDays) {
            $price = (float) $facility->price;
            $pricingBasis = strtolower(trim((string) $facility->pricing_basis));

            return match ($pricingBasis) {
                'per stay + per vehicle' => $price * ($stayDays + $quantity),
                'per vehicle' => $price * $quantity,
                default => $price,
            };
        }), 2);
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
}