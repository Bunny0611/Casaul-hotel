<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Sample VAT');
            $table->decimal('rate', 5, 2)->default(12.00);
            $table->boolean('enabled')->default(false);
            $table->boolean('inclusive')->default(false);
            $table->json('categories');
            $table->timestamps();
        });

        Schema::create('reservation_tax_snapshots', function (Blueprint $table) {
            $table->id();
            $table->morphs('reservationable', 'reservation_tax_reservationable_index');
            $table->string('tax_name');
            $table->decimal('tax_rate', 5, 2);
            $table->boolean('tax_enabled')->default(false);
            $table->boolean('tax_inclusive')->default(false);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('taxable_base', 12, 2);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->json('categories');
            $table->timestamps();
            $table->unique(['reservationable_type', 'reservationable_id'], 'reservation_tax_reservationable_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_tax_snapshots');
        Schema::dropIfExists('tax_settings');
    }
};
