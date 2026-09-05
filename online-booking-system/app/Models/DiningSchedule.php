<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningSchedule extends Model
{
    protected $fillable = [
        'period',
        'available_from',
        'available_to',
        'max_guests',
        'status',
    ];
}
