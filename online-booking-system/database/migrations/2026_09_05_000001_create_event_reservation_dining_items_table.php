<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_reservation_dining_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_reservation_id')->constrained('event_reservations')->cascadeOnDelete();
            $table->foreignId('dining_id')->constrained('dining_menus')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('dining_area')->nullable();
            $table->string('dining_schedule')->nullable();
            $table->date('dining_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_reservation_dining_items');
    }
};