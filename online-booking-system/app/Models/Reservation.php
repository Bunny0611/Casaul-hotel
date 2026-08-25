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
        'category',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'event_type',
        'number_of_guests',
        'dining_area',
        'dining_schedule',
        'quantity',
        'check_in',
        'check_in_time',
        'check_out',
        'check_out_time',
        'status',
        'total_amount',
        'amount_paid',
        'amenity_id',
        'event_place_id',
        'dining_id',
        'payment_method',
        'special_requests',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
