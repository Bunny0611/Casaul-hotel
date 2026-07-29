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
            [
                'room_number' => '101',
                'room_type' => 'Deluxe Room',
                'price' => 3500.00,
                'floor' => '1st Floor',
                'status' => 'available',
                'description' => 'Cozy, modern, and perfect for couples with city view.',
                'image' => 'room1.jpg',
                'capacity' => 2,
            ],
            [
                'room_number' => '102',
                'room_type' => 'Deluxe Room',
                'price' => 3500.00,
                'floor' => '1st Floor',
                'status' => 'available',
                'description' => 'Comfortable room with modern amenities.',
                'image' => 'room1.jpg',
                'capacity' => 2,
            ],
            [
                'room_number' => '201',
                'room_type' => 'Executive Suite',
                'price' => 6500.00,
                'floor' => '2nd Floor',
                'status' => 'available',
                'description' => 'Spacious suite with lounge area and premium amenities.',
                'image' => 'room2.jpg',
                'capacity' => 3,
            ],
            [
                'room_number' => '202',
                'room_type' => 'Executive Suite',
                'price' => 6500.00,
                'floor' => '2nd Floor',
                'status' => 'available',
                'description' => 'Luxury suite with separate living area.',
                'image' => 'room2.jpg',
                'capacity' => 3,
            ],
            [
                'room_number' => '301',
                'room_type' => 'Presidential Suite',
                'price' => 12000.00,
                'floor' => '3rd Floor',
                'status' => 'available',
                'description' => 'Ultimate luxury experience for special occasions with panoramic views.',
                'image' => 'room3.jpg',
                'capacity' => 4,
            ],
            [
                'room_number' => '302',
                'room_type' => 'Standard Room',
                'price' => 2500.00,
                'floor' => '3rd Floor',
                'status' => 'available',
                'description' => 'Budget-friendly comfortable room.',
                'image' => 'room1.jpg',
                'capacity' => 2,
            ],
            [
                'room_number' => '303',
                'room_type' => 'Standard Room',
                'price' => 2500.00,
                'floor' => '3rd Floor',
                'status' => 'occupied',
                'description' => 'Comfortable standard room.',
                'image' => 'room1.jpg',
                'capacity' => 2,
            ],
            [
                'room_number' => '401',
                'room_type' => 'Deluxe Room',
                'price' => 3500.00,
                'floor' => '4th Floor',
                'status' => 'maintenance',
                'description' => 'Deluxe room under maintenance.',
                'image' => 'room1.jpg',
                'capacity' => 2,
            ],
            [
                'room_number' => '402',
                'room_type' => 'Executive Suite',
                'price' => 6500.00,
                'floor' => '4th Floor',
                'status' => 'available',
                'description' => 'Executive suite with ocean view.',
                'image' => 'room2.jpg',
                'capacity' => 3,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
