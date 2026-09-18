<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('refund_payment_method')->nullable()->after('processed_by');
            $table->string('refund_reference_number')->nullable()->after('refund_payment_method');
            $table->string('refund_receipt')->nullable()->after('refund_reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['refund_payment_method', 'refund_reference_number', 'refund_receipt']);
        });
    }
};