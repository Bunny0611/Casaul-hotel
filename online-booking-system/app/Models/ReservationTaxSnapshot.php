<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationTaxSnapshot extends Model
{
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'tax_enabled',
        'tax_inclusive',
        'subtotal',
        'taxable_base',
        'tax_amount',
        'total_amount',
        'categories',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'tax_enabled' => 'boolean',
        'tax_inclusive' => 'boolean',
        'subtotal' => 'decimal:2',
        'taxable_base' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'categories' => 'array',
    ];

    public function reservationable()
    {
        return $this->morphTo();
    }
}
