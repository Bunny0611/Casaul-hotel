<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'category',
        'name',
        'type',
        'description',
        'price',
        'status',
        'location',
        'capacity',
        'available_from',
        'available_to',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}