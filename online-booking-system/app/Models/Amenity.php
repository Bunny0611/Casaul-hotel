<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    protected $fillable = ['name', 'description', 'price', 'pricing_basis', 'capacity', 'scheduling_requirement', 'status', 'image'];

    protected $casts = ['price' => 'decimal:2'];
}