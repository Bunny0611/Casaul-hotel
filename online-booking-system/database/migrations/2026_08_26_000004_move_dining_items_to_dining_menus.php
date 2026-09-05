<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('dining_menus', 'quantity')) {
            Schema::table('dining_menus', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->nullable()->after('available_to');
            });
        }

        $items = DB::table('inventory_items')->where('category', 'dining')->get();

        foreach ($items as $item) {
            DB::table('dining_menus')->updateOrInsert(
                ['id' => $item->id],
                [
                    'name' => $item->name,
                    'category' => $item->type ?: 'Menu / Meal',
                    'description' => $item->description,
                    'price' => $item->price,
                    'status' => $item->status,
                    'available_from' => $item->available_from,
                    'available_to' => $item->available_to,
                    'quantity' => $item->quantity,
                    'image' => $item->image,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ]
            );
        }

        DB::table('inventory_items')->where('category', 'dining')->delete();
    }

    public function down(): void
    {
        // Dining menu records remain in their dedicated table when rolled back.
    }
};