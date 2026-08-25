<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('request_type');
            $table->text('description');
            $table->string('department');
            $table->string('priority')->default('Medium');
            $table->string('preferred_time')->nullable();
            $table->string('status')->default('New');
            $table->foreignId('assigned_employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('employee_notes')->nullable();
            $table->dateTime('submitted_at')->useCurrent();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->index(['department', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_requests');
    }
};
