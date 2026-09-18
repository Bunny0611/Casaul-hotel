<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestRequest extends Model
{
    protected $fillable = [
        'guest_id',
        'reservation_id',
        'room_id',
        'request_type',
        'description',
        'department',
        'priority',
        'preferred_time',
        'status',
        'quantity',
        'unit_price',
        'subtotal',
        'is_billable',
        'billing_status',
        'reservation_type',
        'reservation_key',
        'billing_posted_at',
        'assigned_employee_id',
        'employee_notes',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_billable' => 'boolean',
        'billing_posted_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Staff::class, 'assigned_employee_id');
    }
}
