<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dining_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->string('dining_area');
            $table->string('dining_schedule');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('quantity')->nullable();
            $table->string('dining_id')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'checked-in', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->text('payment_details')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dining_reservations');
    }
};
