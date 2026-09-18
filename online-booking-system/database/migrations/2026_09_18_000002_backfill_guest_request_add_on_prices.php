<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guest_requests')) {
            return;
        }

        $prices = [
            'Extra Towels' => 80.00,
            'Extra Pillows' => 50.00,
            'Extra Blanket' => 120.00,
            'Toiletries' => 80.00,
            'Room Cleaning' => 200.00,
            'Change Bedsheets' => 150.00,
            'Other Housekeeping Request' => 100.00,
            'Dining/Food Request' => 250.00,
        ];

        foreach ($prices as $requestType => $unitPrice) {
            DB::table('guest_requests')
                ->where('request_type', $requestType)
                ->where(function ($query) {
                    $query->whereNull('unit_price')->orWhere('unit_price', 0);
                })
                ->update([
                    'unit_price' => $unitPrice,
                    'subtotal' => DB::raw('(' . $unitPrice . ' * COALESCE(quantity, 1))'),
                    'is_billable' => true,
                    'billing_status' => 'pending',
                ]);
        }
    }

    public function down(): void
    {
        // Existing request prices are preserved when this data correction is rolled back.
    }
};
