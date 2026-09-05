<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningReservationItem extends Model
{
    protected $fillable = [
        'dining_reservation_id',
        'dining_id',
        'quantity',
        'dining_area',
        'dining_schedule',
        'dining_date',
    ];

    protected $casts = [
        'dining_date' => 'date',
        'quantity' => 'integer',
    ];

    public function diningReservation(): BelongsTo
    {
        return $this->belongsTo(DiningReservation::class);
    }

    public function diningMenu(): BelongsTo
    {
        return $this->belongsTo(DiningMenu::class, 'dining_id');
    }
}