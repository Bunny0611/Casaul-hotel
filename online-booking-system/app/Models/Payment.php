<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id', 'amount', 'payment_method', 'payment_date',
        'reference_number', 'notes', 'recorded_by', 'paymentable_type', 'paymentable_id',
    ];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Polymorphic relationship to handle payments for all reservation types
    public function paymentable()
    {
        return $this->morphTo();
    }

    public function recorder()
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}