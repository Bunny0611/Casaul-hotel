<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventReservation extends Model
{
    protected $table = 'event_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'duration_hours' => 'integer',
    ];

    protected $fillable = [
        'event_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'event_type',
        'check_in',
        'event_start_time',
        'check_out',
        'event_end_time',
        'duration_hours',
        'number_of_guests',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function refunds()
    {
        return $this->morphMany(Refund::class, 'reservationable');
    }

    public function diningItems()
    {
        return $this->hasMany(EventReservationDiningItem::class);
    }
}
