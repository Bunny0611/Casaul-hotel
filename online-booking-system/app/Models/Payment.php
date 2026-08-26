<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id', 'amount', 'payment_method', 'payment_date',
        'reference_number', 'notes', 'recorded_by',
    ];

    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function recorder()
    {
        return $this->belongsTo(Staff::class, 'recorded_by');
    }
}