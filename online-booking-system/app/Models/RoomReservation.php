<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReservation extends Model
{
    protected $table = 'room_reservations';

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
        'room_check_in_time',
        'check_out',
        'room_check_out_time',
        'number_of_guests',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function getCheckInTimeAttribute($value)
    {
        return $this->attributes['room_check_in_time'] ?? $value;
    }

    public function getCheckOutTimeAttribute($value)
    {
        return $this->attributes['room_check_out_time'] ?? $value;
    }
}
