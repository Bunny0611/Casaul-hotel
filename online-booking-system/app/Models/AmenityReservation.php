<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenityReservation extends Model
{
    protected $table = 'amenity_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'amenity_quantity' => 'integer',
    ];

    protected $fillable = [
        'amenity_id',
        'amenity_quantity',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'amenity_start_time',
        'check_out',
        'amenity_end_time',
        'number_of_guests',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
