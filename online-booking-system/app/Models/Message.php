<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_email',
        'message',
        'admin_reply',
        'is_replied',
        'replied_at',
    ];

    protected $casts = [
        'is_replied' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function replies()
    {
        return $this->hasMany(MessageReply::class)->orderBy('replied_at');
    }
}
