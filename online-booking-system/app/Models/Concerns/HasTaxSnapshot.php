<?php

namespace App\Models\Concerns;

use App\Models\ReservationTaxSnapshot;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasTaxSnapshot
{
    public function taxSnapshot(): MorphOne
    {
        return $this->morphOne(ReservationTaxSnapshot::class, 'reservationable');
    }
}
