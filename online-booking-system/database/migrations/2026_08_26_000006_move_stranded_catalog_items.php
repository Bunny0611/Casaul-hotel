<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_items')) {
            return;
        }

        DB::transaction(function () {
            DB::table('inventory_items')->where('category', 'amenities')->orderBy('id')->each(function ($item) {
                DB::table('amenities')->updateOrInsert(
                    ['name' => $item->name],
                    [
                        'description' => $item->description,
                        'price' => $item->price,
                        'status' => $item->status,
                        'image' => $item->image,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]
                );
            });

            DB::table('inventory_items')->where('category', 'event_place')->orderBy('id')->each(function ($item) {
                DB::table('event_places')->updateOrInsert(
                    ['name' => $item->name],
                    [
                        'description' => $item->description,
                        'price' => $item->price,
                        'capacity' => $item->capacity,
                        'location' => $item->location,
                        'status' => $item->status,
                        'image' => $item->image,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]
                );
            });

            DB::table('inventory_items')->whereIn('category', ['amenities', 'event_place'])->delete();
        });
    }

    public function down(): void
    {
        // Dedicated catalog tables remain the source of truth after migration.
    }
};