<?php

namespace Database\Seeders;

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
            ['room_number' => '101', 'room_type' => 'Standard Room', 'bed_type' => '1 Queen Bed', 'price' => 2800.00, 'adult_guest_price' => 500.00, 'kid_guest_price' => 250.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Comfortable standard room for up to two guests.', 'image' => null, 'capacity' => 2],
            ['room_number' => '102', 'room_type' => 'Standard Room', 'bed_type' => '1 Queen Bed', 'price' => 2800.00, 'adult_guest_price' => 500.00, 'kid_guest_price' => 250.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Comfortable standard room for up to two guests.', 'image' => null, 'capacity' => 2],
            ['room_number' => '103', 'room_type' => 'Standard Room', 'bed_type' => '2 Twin Beds', 'price' => 3000.00, 'adult_guest_price' => 500.00, 'kid_guest_price' => 250.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Twin-bed standard room for business or leisure stays.', 'image' => null, 'capacity' => 2],
            ['room_number' => '104', 'room_type' => 'Standard Room', 'bed_type' => '2 Twin Beds', 'price' => 3000.00, 'adult_guest_price' => 500.00, 'kid_guest_price' => 250.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Twin-bed standard room for business or leisure stays.', 'image' => null, 'capacity' => 2],
            ['room_number' => '105', 'room_type' => 'Standard Room', 'bed_type' => '2 Single Beds', 'price' => 1900.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Twin-bed room suitable for friends or two guests.', 'image' => 'rooms/1790145484_6ab373ccb8359.png', 'capacity' => 2],
            ['room_number' => '106', 'room_type' => 'Standard Room', 'bed_type' => '2 Single Beds', 'price' => 1900.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Twin room for guests who prefer separate beds', 'image' => 'rooms/1790145539_6ab3740328ce3.png', 'capacity' => 2],
            ['room_number' => '107', 'room_type' => 'Standard Room', 'bed_type' => '1 Double + 1 Single', 'price' => 2200.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Family-friendly room suitable for up to three guests.', 'image' => 'rooms/1790145626_6ab3745a53348.png', 'capacity' => 3],
            ['room_number' => '108', 'room_type' => 'Standard Room', 'bed_type' => '1 Double + 1 Single', 'price' => 2200.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious standard room ideal for a small family or group', 'image' => 'rooms/1790145701_6ab374a5061a4.png', 'capacity' => 3],
            ['room_number' => '109', 'room_type' => 'Standard Room', 'bed_type' => '2 Double Beds', 'price' => 2500.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious room suitable for families or groups of up to four', 'image' => 'rooms/1790145792_6ab375005b5ef.png', 'capacity' => 4],
            ['room_number' => '110', 'room_type' => 'Standard Room', 'bed_type' => '2 Double Beds', 'price' => 2500.00, 'adult_guest_price' => 600.00, 'kid_guest_price' => 300.00, 'floor' => '1st', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Comfortable family room with ample sleeping space for four.', 'image' => 'rooms/1790145844_6ab3745b53348.png', 'capacity' => 4],
            ['room_number' => '201', 'room_type' => 'Deluxe Room', 'bed_type' => '1 King Bed', 'price' => 4500.00, 'adult_guest_price' => 650.00, 'kid_guest_price' => 325.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious deluxe room with a king bed and upgraded amenities.', 'image' => null, 'capacity' => 2],
            ['room_number' => '202', 'room_type' => 'Deluxe Room', 'bed_type' => '1 King Bed', 'price' => 4500.00, 'adult_guest_price' => 650.00, 'kid_guest_price' => 325.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious deluxe room with a king bed and upgraded amenities.', 'image' => null, 'capacity' => 2],
            ['room_number' => '203', 'room_type' => 'Deluxe Room', 'bed_type' => '2 Queen Beds', 'price' => 5200.00, 'adult_guest_price' => 650.00, 'kid_guest_price' => 325.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Deluxe room with two queen beds for families or small groups.', 'image' => null, 'capacity' => 4],
            ['room_number' => '204', 'room_type' => 'Deluxe Room', 'bed_type' => '2 Queen Beds', 'price' => 5200.00, 'adult_guest_price' => 650.00, 'kid_guest_price' => 325.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Deluxe room with two queen beds for families or small groups.', 'image' => null, 'capacity' => 4],
            ['room_number' => '205', 'room_type' => 'Deluxe Room', 'bed_type' => '2 Queen Beds', 'price' => 3300.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'dirty', 'description' => 'Comfortable deluxe family room with additional guest space.', 'image' => 'rooms/1790144166_6ab36ea628cff.png', 'capacity' => 4],
            ['room_number' => '206', 'room_type' => 'Deluxe Room', 'bed_type' => '1 King + 1 Single', 'price' => 3200.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious room ideal for a small family or group.', 'image' => 'rooms/1790144405_6ab36f95569e9.png', 'capacity' => 3],
            ['room_number' => '207', 'room_type' => 'Deluxe Room', 'bed_type' => '1 King + 1 Single', 'price' => 3200.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Premium family-friendly room with king bed, single bed, and balcony.', 'image' => 'rooms/1790144469_6ab36fd55ed65.png', 'capacity' => 3],
            ['room_number' => '208', 'room_type' => 'Deluxe Room', 'bed_type' => '2 King Beds', 'price' => 3800.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Large room with two king beds, two bathrooms, and balcony.', 'image' => 'rooms/1790144541_6ab3701d44e15.png', 'capacity' => 4],
            ['room_number' => '209', 'room_type' => 'Deluxe Room', 'bed_type' => '2 Queen Beds', 'price' => 3700.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Spacious group room with two bathrooms and private balcony.', 'image' => 'rooms/1790144638_6ab3707e87148.png', 'capacity' => 4],
            ['room_number' => '210', 'room_type' => 'Deluxe Room', 'bed_type' => '2 King Beds', 'price' => 4000.00, 'adult_guest_price' => 1000.00, 'kid_guest_price' => 500.00, 'floor' => '2nd', 'status' => 'available', 'cleaning_status' => 'clean', 'description' => 'Largest deluxe room with two king beds, two bathrooms, and balcony.', 'image' => 'rooms/1790144689_6ab370b199cab.png', 'capacity' => 4],
        ];

        $roomNumbers = array_column($rooms, 'room_number');

        Room::whereNotIn('room_number', $roomNumbers)
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
