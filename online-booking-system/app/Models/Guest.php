<?php

namespace App\Models;

use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $table = 'guest_users';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_initial',
        'name',
        'email',
        'contact_no',
        'country_code',
        'country_name',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
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
