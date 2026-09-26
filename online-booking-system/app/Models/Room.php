<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'bed_type',
        'price',
        'adult_guest_price',
        'kid_guest_price',
        'floor',
        'status',
        'cleaning_status',
        'description',
        'image',
        'capacity',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function roomReservations()
    {
        return $this->hasMany(RoomReservation::class);
    }

    public function scopeAvailableForDates(Builder $query, $checkIn, $checkOut): Builder
    {
        $withoutOverlap = function ($reservations) use ($checkIn, $checkOut) {
            $reservations
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->whereDate('check_in', '<', $checkOut)
                ->whereDate('check_out', '>', $checkIn);
        };

        return $query
            ->whereDoesntHave('roomReservations', $withoutOverlap)
            ->whereDoesntHave('reservations', $withoutOverlap);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class);
    }
}
