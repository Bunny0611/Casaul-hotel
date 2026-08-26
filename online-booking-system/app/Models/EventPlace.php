<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPlace extends Model
{
    protected $fillable = ['name', 'description', 'price', 'capacity', 'location', 'status', 'image'];

    protected $casts = ['price' => 'decimal:2'];
}