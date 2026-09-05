<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationDiningItem extends Model
{
    protected $fillable = [
        'reservation_id',
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

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function diningMenu(): BelongsTo
    {
        return $this->belongsTo(DiningMenu::class, 'dining_id');
    }
}
