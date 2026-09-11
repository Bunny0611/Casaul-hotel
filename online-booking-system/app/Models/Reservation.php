<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    public function diningItems()
    {
        return $this->hasMany(ReservationDiningItem::class);
    }

    protected $fillable = [
        'category',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'event_type',
        'number_of_guests',
        'dining_area',
        'dining_schedule',
        'quantity',
        'check_in',
        'check_in_time',
        'room_check_in_time',
        'room_check_out_time',
        'event_start_time',
        'event_end_time',
        'facility_start_time',
        'facility_end_time',
        'dining_time',
        'check_out',
        'check_out_time',
        'status',
        'total_amount',
        'amount_paid',
        'facility_id',
        'event_id',
        'dining_id',
        'payment_method',
        'payment_details',
        'special_requests',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function amenity()
    {
        return $this->facility();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventPlace()
    {
        return $this->event();
    }

    public function getAmenityIdAttribute()
    {
        return $this->getAttribute('facility_id');
    }

    public function getEventPlaceIdAttribute()
    {
        return $this->getAttribute('event_id');
    }

    public function diningMenu()
    {
        return $this->belongsTo(DiningMenu::class, 'dining_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(HousekeepingTask::class);
    }
}
