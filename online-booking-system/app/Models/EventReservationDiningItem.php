<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReservationDiningItem extends Model
{
    protected $fillable = [
        'event_reservation_id',
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

    public function eventReservation(): BelongsTo
    {
        return $this->belongsTo(EventReservation::class);
    }

    public function diningMenu(): BelongsTo
    {
        return $this->belongsTo(DiningMenu::class, 'dining_id');
    }
}