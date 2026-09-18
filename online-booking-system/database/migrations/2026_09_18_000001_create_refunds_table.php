<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('refunds')) {
            return;
        }

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('reservationable_type');
            $table->unsignedBigInteger('reservationable_id');
            $table->string('guest_name');
            $table->decimal('original_total', 10, 2);
            $table->decimal('final_total', 10, 2);
            $table->decimal('total_paid', 10, 2);
            $table->decimal('refund_amount', 10, 2);
            $table->enum('reason', ['Early Check-out', 'Reservation Change', 'Cancellation']);
            $table->date('refund_date');
            $table->enum('status', ['Pending', 'Refunded'])->default('Pending');
            $table->foreignId('processed_by')->nullable()->constrained('staff_users')->nullOnDelete();
            $table->timestamps();

            $table->index(['reservationable_type', 'reservationable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};