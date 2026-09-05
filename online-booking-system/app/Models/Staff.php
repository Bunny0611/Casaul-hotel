<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    /** @use HasFactory<StaffFactory> */
    use HasFactory, Notifiable;

    protected $table = 'staff_users';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_initial',
        'name',
        'email',
        'contact_no',
        'password',
        'role',
        'is_active',
        'created_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function creator()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class, 'assigned_staff_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
