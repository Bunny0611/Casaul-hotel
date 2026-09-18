<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guest_requests')) {
            return;
        }

        if (! Schema::hasColumn('guest_requests', 'quantity')) {
            Schema::table('guest_requests', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1)->after('status');
            });
        }

        foreach ([
            'unit_price' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'default' => 0],
            'subtotal' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'default' => 0],
            'is_billable' => ['type' => 'boolean', 'default' => false],
            'billing_status' => ['type' => 'string', 'default' => 'pending'],
            'reservation_type' => ['type' => 'string', 'nullable' => true],
            'reservation_key' => ['type' => 'unsignedBigInteger', 'nullable' => true],
            'billing_posted_at' => ['type' => 'timestamp', 'nullable' => true],
        ] as $column => $definition) {
            if (Schema::hasColumn('guest_requests', $column)) {
                continue;
            }

            Schema::table('guest_requests', function (Blueprint $table) use ($column, $definition) {
                $type = $definition['type'];

                if ($type === 'decimal') {
                    $table->decimal($column, $definition['precision'], $definition['scale'])->default($definition['default']);
                    return;
                }

                if ($type === 'boolean') {
                    $table->boolean($column)->default($definition['default']);
                    return;
                }

                if ($type === 'unsignedBigInteger') {
                    $table->unsignedBigInteger($column)->nullable();
                    return;
                }

                if ($type === 'timestamp') {
                    $table->timestamp($column)->nullable();
                    return;
                }

                $table->string($column, 255)->default($definition['default']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('guest_requests')) {
            return;
        }

        $columns = [
            'billing_posted_at',
            'reservation_key',
            'reservation_type',
            'billing_status',
            'is_billable',
            'subtotal',
            'unit_price',
            'quantity',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('guest_requests', $column)) {
                Schema::table('guest_requests', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
