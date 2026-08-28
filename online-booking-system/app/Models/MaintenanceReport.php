<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceReport extends Model
{
    protected $table = 'maintenance_reports';

    protected $fillable = [
        'room_number',
        'room_type',
        'reported_by',
        'category',
        'priority',
        'problem',
        'description',
        'date_reported',
        'expected_date',
        'technician',
        'status',
        'photo_path',
    ];

    protected $casts = [
        'date_reported' => 'datetime',
        'expected_date' => 'date',
    ];
}
