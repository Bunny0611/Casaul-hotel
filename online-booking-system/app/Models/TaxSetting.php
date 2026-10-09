<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    public const CATEGORIES = [
        'rooms' => 'Room charges',
        'extra_person' => 'Extra-person charges',
        'parking' => 'Parking fees',
        'dining' => 'Dining orders',
        'events' => 'Event bookings',
        'services_addons' => 'Additional services and add-ons',
    ];

    protected $fillable = [
        'name',
        'rate',
        'enabled',
        'inclusive',
        'categories',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'enabled' => 'boolean',
        'inclusive' => 'boolean',
        'categories' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'name' => 'Sample VAT',
            'rate' => 12,
            'enabled' => false,
            'inclusive' => false,
            'categories' => [],
        ]);
    }

    public function pricingSettings(): array
    {
        return [
            'name' => $this->name,
            'rate' => (float) $this->rate,
            'enabled' => $this->enabled,
            'inclusive' => $this->inclusive,
            'categories' => $this->categories ?? [],
        ];
    }
}
