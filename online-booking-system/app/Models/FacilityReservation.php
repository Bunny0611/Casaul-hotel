<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityReservation extends Model
{
    protected $table = 'facility_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'facility_quantity' => 'integer',
    ];

    protected $fillable = [
        'facility_id', 'facility_quantity', 'guest_name', 'guest_email', 'guest_phone',
        'check_in', 'facility_start_time', 'check_out', 'facility_end_time',
        'number_of_guests', 'status', 'total_amount', 'payment_method',
        'payment_details', 'amount_paid', 'special_requests',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function refunds()
    {
        return $this->morphMany(Refund::class, 'reservationable');
    }
}