<?php

namespace App\Models;

use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory, Notifiable;

    protected $table = 'guest_users';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_initial',
        'name',
        'email',
        'contact_no',
        'password',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getRoleAttribute(): string
    {
        return 'guest';
    }
}
