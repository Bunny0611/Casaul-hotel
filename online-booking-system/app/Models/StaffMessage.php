<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'body',
    ];

    public function sender()
    {
        return $this->belongsTo(Staff::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Staff::class, 'recipient_id');
    }
}