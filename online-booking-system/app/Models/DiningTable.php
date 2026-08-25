<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    protected $fillable = [
        'table_no',
        'type',
        'capacity',
        'location',
        'status',
    ];
}
