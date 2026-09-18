<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'refunds'
                 AND COLUMN_NAME = 'processed_by' AND REFERENCED_TABLE_NAME IS NOT NULL"
            );

            foreach ($foreignKeys as $foreignKey) {
                DB::statement('ALTER TABLE `refunds` DROP FOREIGN KEY `' . $foreignKey->CONSTRAINT_NAME . '`');
            }
        }

        Schema::table('refunds', function (Blueprint $table) {
            $table->foreign('processed_by')->references('id')->on('staff_users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
        });
    }
};