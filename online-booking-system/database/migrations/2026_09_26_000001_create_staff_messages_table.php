<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_messages')) {
            return;
        }

        Schema::create('staff_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('staff_users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('staff_users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['sender_id', 'recipient_id', 'created_at']);
            $table->index(['recipient_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_messages');
    }
};