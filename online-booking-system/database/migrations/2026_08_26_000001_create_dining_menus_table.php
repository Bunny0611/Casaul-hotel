<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dining_schedule_id')
                ->nullable()
                ->constrained('dining_schedules')
                ->nullOnDelete();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status')->default('available');
            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_menus');
    }
};