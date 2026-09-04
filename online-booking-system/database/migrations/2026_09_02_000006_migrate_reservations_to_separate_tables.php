<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate room reservations
        DB::statement("
            INSERT INTO room_reservations (
                room_id, guest_name, guest_email, guest_phone,
                check_in, room_check_in_time, check_out, room_check_out_time,
                number_of_guests, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            )
            SELECT
                room_id, guest_name, guest_email, guest_phone,
                check_in, room_check_in_time, check_out, room_check_out_time,
                COALESCE(number_of_guests, 1), status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            FROM reservations
            WHERE category = 'rooms' OR (category IS NULL AND room_id IS NOT NULL)
        ");

        // Migrate event reservations
        DB::statement("
            INSERT INTO event_reservations (
                event_place_id, guest_name, guest_email, guest_phone,
                event_type, check_in, event_start_time, check_out, event_end_time,
                number_of_guests, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            )
            SELECT
                event_place_id, guest_name, guest_email, guest_phone,
                event_type, check_in, event_start_time, check_out, event_end_time,
                number_of_guests, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            FROM reservations
            WHERE category = 'event_place' AND event_place_id IS NOT NULL
        ");

        // Migrate amenity reservations
        DB::statement("
            INSERT INTO amenity_reservations (
                amenity_id, guest_name, guest_email, guest_phone,
                check_in, amenity_start_time, check_out, amenity_end_time,
                number_of_guests, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            )
            SELECT
                amenity_id, guest_name, guest_email, guest_phone,
                check_in, amenity_start_time, check_out, amenity_end_time,
                number_of_guests, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            FROM reservations
            WHERE category = 'amenities' AND amenity_id IS NOT NULL
        ");

        // Migrate dining reservations
        DB::statement("
            INSERT INTO dining_reservations (
                guest_name, guest_email, guest_phone,
                dining_area, dining_schedule, check_in, check_out,
                quantity, dining_id, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            )
            SELECT
                guest_name, guest_email, guest_phone,
                dining_area, dining_schedule, check_in, check_out,
                quantity, dining_id, status, total_amount, payment_method,
                payment_details, amount_paid, special_requests, created_at, updated_at
            FROM reservations
            WHERE category = 'dining' AND dining_area IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the new tables
        DB::table('room_reservations')->truncate();
        DB::table('event_reservations')->truncate();
        DB::table('amenity_reservations')->truncate();
        DB::table('dining_reservations')->truncate();
    }
};
