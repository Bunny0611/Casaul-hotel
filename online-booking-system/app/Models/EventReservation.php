<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventReservation extends Model
{
    protected $table = 'event_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    protected $fillable = [
        'event_place_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'event_type',
        'check_in',
        'event_start_time',
        'check_out',
        'event_end_time',
        'number_of_guests',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function eventPlace()
    {
        return $this->belongsTo(EventPlace::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function diningItems()
    {
        return $this->hasMany(EventReservationDiningItem::class);
    }
}
