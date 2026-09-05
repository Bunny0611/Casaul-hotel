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
        Schema::table('payments', function (Blueprint $table) {
            // Add polymorphic columns for the new reservation tables
            $table->string('paymentable_type')->nullable()->after('reservation_id');
            $table->unsignedBigInteger('paymentable_id')->nullable()->after('paymentable_type');
            
            // Index for polymorphic relationship
            $table->index(['paymentable_type', 'paymentable_id']);
            
            // Keep reservation_id for backward compatibility but make it nullable
            $table->dropForeign(['reservation_id']);
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the new columns
            $table->dropIndex(['paymentable_type', 'paymentable_id']);
            $table->dropColumn(['paymentable_type', 'paymentable_id']);
            
            // Restore reservation_id as not nullable with foreign key
            $table->unsignedBigInteger('reservation_id')->change();
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        });
    }
};
