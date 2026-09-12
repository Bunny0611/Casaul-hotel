<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('status')->default('available');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('available');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('inventory_items')->where('category', 'facilities')->get() as $item) {
            DB::table('facilities')->insert([
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'status' => $item->status,
                'image' => $item->image,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        foreach (DB::table('inventory_items')->where('category', 'event')->get() as $item) {
            DB::table('events')->insert([
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'capacity' => $item->capacity,
                'location' => $item->location,
                'status' => $item->status,
                'image' => $item->image,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        DB::table('inventory_items')->whereIn('category', ['facilities', 'event'])->delete();
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilities');
    }
};