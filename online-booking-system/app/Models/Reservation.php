<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    protected $fillable = [
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'check_in_time',
        'check_out',
        'check_out_time',
        'status',
        'total_amount',
        'payment_method',
        'special_requests',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
