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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'middle_initial')) {
                $table->string('middle_initial', 3)->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('users', 'contact_no')) {
                $table->string('contact_no', 25)->nullable()->after('email');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'housekeeping', 'employee', 'guest'])->default('admin')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'housekeeping'])->default('admin')->change();
            }

            $table->dropColumn(['first_name', 'last_name', 'middle_initial', 'contact_no']);
        });
    }
};
