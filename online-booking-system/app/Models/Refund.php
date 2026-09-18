<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'guest_name', 'original_total', 'final_total', 'total_paid',
        'refund_amount', 'reason', 'refund_date', 'status', 'processed_by',
        'refund_payment_method', 'refund_reference_number', 'refund_receipt',
    ];

    protected $casts = [
        'original_total' => 'decimal:2',
        'final_total' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refund_date' => 'date',
    ];

    public function reservationable()
    {
        return $this->morphTo();
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}