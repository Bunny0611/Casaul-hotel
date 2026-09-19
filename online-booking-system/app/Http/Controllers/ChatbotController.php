<?php

namespace App\Http\Controllers;

use App\Models\DiningMenu;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(Request $request)
    {
        $message = trim((string) $request->input('message', ''));

        if ($message === '') {
            return response()->json([
                'reply' => 'Please type your question so I can help you.',
            ], 422);
        }

        return response()->json([
            'reply' => $this->generateReply($message),
        ]);
    }

    protected function generateReply(string $message): string
    {
        $normalized = strtolower($message);

        if (Str::contains($normalized, ['check-in', 'check in', 'arrival', 'arrive'])) {
            return 'Check-in usually starts at 2:00 PM. Check-out is at 12:00 PM. If you need an early arrival or late departure, please let our front desk know and we will do our best to assist.';
        }

        if (Str::contains($normalized, ['check-out', 'check out', 'departure', 'checkout'])) {
            return 'Check-out is at 12:00 PM. You may request a late check-out, subject to room availability and front desk approval.';
        }

        if (Str::contains($normalized, ['room', 'rooms', 'accommodation', 'stay'])) {
            return $this->roomReply($normalized);
        }

        if (Str::contains($normalized, ['facility', 'facilities', 'amenity', 'amenities'])) {
            return $this->facilityReply($normalized);
        }

        if (Str::contains($normalized, ['dining', 'restaurant', 'meal', 'breakfast', 'food'])) {
            return $this->diningReply($normalized);
        }

        if (Str::contains($normalized, ['event', 'events', 'wedding', 'party', 'venue'])) {
            return $this->eventReply($normalized);
        }

        if (Str::contains($normalized, ['reservation', 'book', 'booking', 'reserve'])) {
            return $this->reservationReply($normalized);
        }

        if (Str::contains($normalized, ['hotel', 'contact', 'location', 'address', 'about'])) {
            return $this->hotelInfoReply($normalized);
        }

        if (Str::contains($normalized, ['hello', 'hi', 'hey'])) {
            return 'Hello! I can help with rooms, dining, facilities, events, and reservations. What would you like to know?';
        }

        return 'I can help with rooms, facilities, dining, events, reservations, check-in and check-out, and general hotel information. Please ask a question about one of those topics.';
    }

    protected function roomReply(string $normalized): string
    {
        $roomCount = Room::count();
        $roomTypes = Room::query()->select('room_type')->distinct()->orderBy('room_type')->pluck('room_type')->take(5)->all();
        $lowestPrice = Room::query()->whereNotNull('price')->min('price');

        $roomList = $roomTypes ? implode(', ', $roomTypes) : 'standard room options';
        $priceText = $lowestPrice ? ' from ₱' . number_format((float) $lowestPrice, 2) : 'from our current available rates';

        if (Str::contains($normalized, ['available', 'availability'])) {
            $availableRooms = Room::where('status', 'available')->count();
            return 'We currently have ' . $availableRooms . ' available room' . ($availableRooms === 1 ? '' : 's') . ' in our system. Room types include ' . $roomList . '.';
        }

        if (Str::contains($normalized, ['price', 'rate', 'cost'])) {
            return 'Our rooms start at ' . $priceText . '. Popular room types include ' . $roomList . '.';
        }

        return 'Casaul Hotel currently has ' . $roomCount . ' room' . ($roomCount === 1 ? '' : 's') . ' in our inventory. We offer room types such as ' . $roomList . '. If you want, I can help you check available dates or guide you to the best room for your stay.';
    }

    protected function facilityReply(string $normalized): string
    {
        $facilityCount = Facility::count();
        $facilityNames = Facility::query()->select('name')->orderBy('name')->pluck('name')->take(5)->all();

        if (Str::contains($normalized, ['available', 'availability'])) {
            $availableFacilities = Facility::whereIn('status', ['available', 'limited'])->count();
            return 'We currently have ' . $availableFacilities . ' available facility option' . ($availableFacilities === 1 ? '' : 's') . ' for guests.';
        }

        $listText = $facilityNames ? implode(', ', $facilityNames) : 'our hotel amenities';

        return 'We offer ' . $facilityCount . ' facility option' . ($facilityCount === 1 ? '' : 's') . ' for guests, including ' . $listText . '. These may include amenities designed for comfort, leisure, and convenience.';
    }

    protected function diningReply(string $normalized): string
    {
        $diningCount = DiningMenu::count();
        $menuItems = DiningMenu::query()->select('name')->orderBy('name')->pluck('name')->take(5)->all();
        $listText = $menuItems ? implode(', ', $menuItems) : 'signature dishes and refreshments';

        if (Str::contains($normalized, ['menu', 'food', 'meal'])) {
            return 'Our dining menu includes ' . $diningCount . ' item' . ($diningCount === 1 ? '' : 's') . ' such as ' . $listText . '. You can also ask about breakfast, main courses, desserts, or beverages.';
        }

        return 'Casaul Hotel offers dining options for guests, including breakfast, meals, and beverages. We currently have ' . $diningCount . ' menu item' . ($diningCount === 1 ? '' : 's') . ' available in our system.';
    }

    protected function eventReply(string $normalized): string
    {
        $eventCount = Event::count();
        $eventNames = Event::query()->select('name')->orderBy('name')->pluck('name')->take(5)->all();
        $eventList = $eventNames ? implode(', ', $eventNames) : 'our event packages';

        if (Str::contains($normalized, ['price', 'cost'])) {
            $lowestPrice = Event::query()->whereNotNull('price')->min('price');
            $priceText = $lowestPrice ? ' starting at ₱' . number_format((float) $lowestPrice, 2) : 'based on the package you choose';
            return 'Event packages are available' . $priceText . '. We currently have ' . $eventCount . ' package' . ($eventCount === 1 ? '' : 's') . ' in the system, including ' . $eventList . '.';
        }

        return 'We offer ' . $eventCount . ' event package' . ($eventCount === 1 ? '' : 's') . ' in our system, including ' . $eventList . '. These packages are suitable for celebrations and gatherings.';
    }

    protected function reservationReply(string $normalized): string
    {
        $roomReservationCount = RoomReservation::count();
        $totalReservations = Reservation::count();

        if (Str::contains($normalized, ['status', 'pending', 'confirmed'])) {
            return 'You can check your reservation status from the guest profile or by contacting the front desk. We also support room, facility, event, and dining reservations.';
        }

        if (Str::contains($normalized, ['how do i book', 'book room', 'book a room', 'make reservation'])) {
            return 'You can make a reservation through the hotel reservation page. Select your preferred room, dates, and any extras such as facilities or dining before confirming.';
        }

        return 'We support room, facility, dining, and event reservations. Our system currently shows ' . $totalReservations . ' reservation record' . ($totalReservations === 1 ? '' : 's') . ' and ' . $roomReservationCount . ' room reservation' . ($roomReservationCount === 1 ? '' : 's') . ' in the database.';
    }

    protected function hotelInfoReply(string $normalized): string
    {
        $hotelName = config('app.name', 'Casaul Hotel');
        $roomCount = Room::count();
        $facilityCount = Facility::count();
        $eventCount = Event::count();
        $diningCount = DiningMenu::count();

        return $hotelName . ' offers ' . $roomCount . ' room' . ($roomCount === 1 ? '' : 's') . ', ' . $facilityCount . ' facility option' . ($facilityCount === 1 ? '' : 's') . ', ' . $eventCount . ' event package' . ($eventCount === 1 ? '' : 's') . ', and ' . $diningCount . ' dining item' . ($diningCount === 1 ? '' : 's') . '. We are here to help with reservations, room selection, dining, and guest requests.';
    }
}
