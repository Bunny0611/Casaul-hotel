<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningReservation extends Model
{
    protected $table = 'dining_reservations';

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    protected $fillable = [
        'guest_name',
        'guest_email',
        'guest_phone',
        'dining_area',
        'dining_schedule',
        'check_in',
        'check_out',
        'quantity',
        'dining_id',
        'status',
        'total_amount',
        'payment_method',
        'payment_details',
        'amount_paid',
        'special_requests',
    ];

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function diningItems()
    {
        return $this->hasMany(DiningReservationItem::class);
    }

    public function scopeActiveForTableAndSchedule($query, string $tableNumber, $date, string $schedule)
    {
        $tableNumbers = collect(explode(',', $tableNumber))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values();
        $schedules = collect(explode(',', $schedule))
            ->map(fn ($value) => trim($value))
            ->filter()
            ->unique()
            ->values();

        return $query
            ->where(function ($tableQuery) use ($tableNumbers) {
                $tableNumbers->each(function ($number) use ($tableQuery) {
                    $tableQuery->orWhere(function ($numberQuery) use ($number) {
                        $numberQuery->where('dining_area', $number)
                            ->orWhere('dining_area', 'like', $number . ',%')
                            ->orWhere('dining_area', 'like', '%,' . $number)
                            ->orWhere('dining_area', 'like', '%,' . $number . ',%');
                    });
                });
            })
            ->whereDate('check_in', $date)
            ->where(function ($scheduleQuery) use ($schedules) {
                $schedules->each(function ($period) use ($scheduleQuery) {
                    $scheduleQuery->orWhere(function ($periodQuery) use ($period) {
                        $periodQuery->where('dining_schedule', $period)
                            ->orWhere('dining_schedule', 'like', $period . ',%')
                            ->orWhere('dining_schedule', 'like', '%,' . $period)
                            ->orWhere('dining_schedule', 'like', '%,' . $period . ',%');
                    });
                });
            })
            ->whereNotIn('status', ['cancelled', 'completed']);
    }
}
