<?php

namespace App\Support;

use App\Models\Guest;

class GuestOrigin
{
    public const HOTEL_COUNTRY_CODE = 'PH';
    public const HOTEL_REGION_CODE = '050000000';
    public const HOTEL_PROVINCE_CODE = '050500000';
    public const HOTEL_CITY_CODE = '050517000';

    public static function classify(Guest $guest): string
    {
        if ($guest->country_code !== self::HOTEL_COUNTRY_CODE) {
            return 'International';
        }

        if ($guest->city_code === self::HOTEL_CITY_CODE) {
            return 'Local';
        }

        if ($guest->province_code === self::HOTEL_PROVINCE_CODE) {
            return 'Provincial';
        }

        if ($guest->region_code === self::HOTEL_REGION_CODE) {
            return 'Regional';
        }

        return 'National';
    }
}