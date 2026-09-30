<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_requests', function (Blueprint $table) {
            $table->string('charge_type')->nullable();
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('source_guest_request_id')->nullable()->constrained('guest_requests')->nullOnDelete();
            $table->foreignId('dining_menu_id')->nullable()->constrained('dining_menus')->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('guest_requests', function (Blueprint $table) {
            $table->dropForeign(['source_guest_request_id']);
            $table->dropForeign(['dining_menu_id']);
            $table->dropForeign(['facility_id']);
            $table->dropColumn(['charge_type', 'source', 'notes', 'source_guest_request_id', 'dining_menu_id', 'facility_id']);
        });
    }
};