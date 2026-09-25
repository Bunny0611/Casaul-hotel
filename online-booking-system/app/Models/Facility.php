<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = ['name', 'description', 'price', 'pricing_basis', 'capacity', 'location', 'scheduling_requirement', 'status', 'image'];

    protected $casts = ['price' => 'decimal:2'];
}
