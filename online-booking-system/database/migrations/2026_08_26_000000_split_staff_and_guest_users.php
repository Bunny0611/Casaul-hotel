<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createUserTable('staff_users', true);
        $this->createUserTable('guest_users', false);

        DB::table('users')->whereIn('role', ['admin', 'employee', 'housekeeping'])->orderBy('id')->each(function ($user) {
            DB::table('staff_users')->insert((array) $user);
        });

        DB::table('users')->where('role', 'guest')->orderBy('id')->each(function ($user) {
            $data = (array) $user;
            unset($data['role'], $data['created_by']);
            DB::table('guest_users')->insert($data);
        });

        Schema::table('guest_requests', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['assigned_employee_id']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
        });

        Schema::table('guest_requests', function (Blueprint $table) {
            $table->foreign('guest_id')->references('id')->on('guest_users')->nullOnDelete();
            $table->foreign('assigned_employee_id')->references('id')->on('staff_users')->nullOnDelete();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('recorded_by')->references('id')->on('staff_users')->nullOnDelete();
        });

        Schema::drop('users');
    }

    public function down(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_initial')->nullable();
            $table->string('contact_no')->nullable();
            $table->enum('role', ['admin', 'housekeeping', 'employee', 'guest'])->default('admin');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });

        DB::table('staff_users')->orderBy('id')->each(function ($user) {
            DB::table('users')->insert((array) $user);
        });
        DB::table('guest_users')->orderBy('id')->each(function ($user) {
            $data = (array) $user;
            $data['role'] = 'guest';
            $data['created_by'] = null;
            DB::table('users')->insert($data);
        });

        Schema::table('guest_requests', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['assigned_employee_id']);
            $table->foreign('guest_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_employee_id')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::dropIfExists('staff_users');
        Schema::dropIfExists('guest_users');
    }

    private function createUserTable(string $tableName, bool $staff): void
    {
        Schema::create($tableName, function (Blueprint $table) use ($staff) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('middle_initial')->nullable();
            $table->string('email')->unique();
            $table->string('contact_no')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            if ($staff) {
                $table->enum('role', ['admin', 'housekeeping', 'employee']);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable();
            } else {
                $table->boolean('is_active')->default(true);
            }
            $table->timestamps();
        });
    }
};
