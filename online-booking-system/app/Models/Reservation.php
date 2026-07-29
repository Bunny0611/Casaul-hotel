<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'check_out',
        'status',
        'total_amount',
        'special_requests',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
