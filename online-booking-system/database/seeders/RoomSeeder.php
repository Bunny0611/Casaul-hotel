<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            ['room_number' => '102', 'room_type' => 'Deluxe Room', 'price' => 90000.00, 'floor' => '1st', 'status' => 'available', 'description' => 'sgdfhyduhsjkmgresa', 'image' => null, 'capacity' => 2],
            ['room_number' => '104', 'room_type' => 'Deluxe Room', 'price' => 1000.00, 'floor' => '1st', 'status' => 'available', 'description' => 'darwftfhiwa', 'image' => null, 'capacity' => 2],
            ['room_number' => '106', 'room_type' => 'Deluxe Room', 'price' => 66666.00, 'floor' => '6', 'status' => 'available', 'description' => null, 'image' => null, 'capacity' => 6],
            ['room_number' => '109', 'room_type' => 'Deluxe Room', 'price' => 123.00, 'floor' => '1st', 'status' => 'available', 'description' => 'adsf', 'image' => null, 'capacity' => 10],
            ['room_number' => '177', 'room_type' => 'Deluxe Room', 'price' => 1.00, 'floor' => '1', 'status' => 'available', 'description' => null, 'image' => null, 'capacity' => 1],
        ];

        Room::whereNotIn('room_number', array_column($rooms, 'room_number'))
            ->whereDoesntHave('reservations')
            ->delete();

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['room_number' => $room['room_number']],
                $room
            );
        }
    }
}
