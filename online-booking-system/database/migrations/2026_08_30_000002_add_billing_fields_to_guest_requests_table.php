<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_requests', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->default(0)->after('quantity');
            $table->decimal('subtotal', 12, 2)->default(0)->after('unit_price');
            $table->boolean('is_billable')->default(false)->after('subtotal');
            $table->string('billing_status')->default('pending')->after('is_billable');
            $table->string('reservation_type')->nullable()->after('billing_status');
            $table->unsignedBigInteger('reservation_key')->nullable()->after('reservation_type');
            $table->timestamp('billing_posted_at')->nullable()->after('reservation_key');
        });
    }

    public function down(): void
    {
        Schema::table('guest_requests', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price',
                'subtotal',
                'is_billable',
                'billing_status',
                'reservation_type',
                'reservation_key',
                'billing_posted_at',
            ]);
        });
    }
};
