<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = ['event_type', 'name', 'description', 'price', 'pricing_basis', 'capacity', 'location', 'available_from', 'available_to', 'status', 'image'];

    protected $casts = ['price' => 'decimal:2'];
}
