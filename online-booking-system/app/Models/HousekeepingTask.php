<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousekeepingTask extends Model
{
    protected $fillable = [
        'room_id',
        'reservation_id',
        'assigned_staff_id',
        'task',
        'priority',
        'scheduled_date',
        'scheduled_time',
        'estimated_duration',
        'started_at',
        'finished_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}