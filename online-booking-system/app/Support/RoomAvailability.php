<?php

namespace App\Support;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomReservation;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability
{
    public static function conflict(Room $room, $checkIn, $checkOut, ?Model $ignore = null): ?Model
    {
        $specialized = RoomReservation::query()
            ->where('room_id', $room->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn);

        if ($ignore instanceof RoomReservation) {
            $specialized->whereKeyNot($ignore->getKey());
        }

        $conflict = $specialized->first();
        if ($conflict) {
            return $conflict;
        }

        $legacy = Reservation::query()
            ->where('room_id', $room->id)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn);

        if ($ignore instanceof Reservation) {
            $legacy->whereKeyNot($ignore->getKey());
        }

        return $legacy->first();
    }
}