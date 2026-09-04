<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningReservation extends Model
{
    protected $table = 'dining_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    protected $fillable = [
        'guest_name',
        'guest_email',
        'guest_phone',
        'dining_area',
        'dining_schedule',
        'check_in',
        'check_out',
        'quantity',
        'dining_id',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function diningItems()
    {
        return $this->hasMany(DiningReservationItem::class);
    }
}
