<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiningMenu extends Model
{
    protected $fillable = [
        'dining_schedule_id',
        'name',
        'category',
        'description',
        'price',
        'status',
        'available_from',
        'available_to',
        'quantity',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function diningSchedule(): BelongsTo
    {
        return $this->belongsTo(DiningSchedule::class);
    }
}