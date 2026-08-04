<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'price',
        'floor',
        'status',
        'cleaning_status',
        'description',
        'image',
        'capacity',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
