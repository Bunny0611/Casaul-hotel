<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPlace extends Model
{
    protected $fillable = ['event_type', 'name', 'description', 'price', 'pricing_basis', 'capacity', 'location', 'available_from', 'available_to', 'status', 'image'];

    protected $casts = ['price' => 'decimal:2'];
}