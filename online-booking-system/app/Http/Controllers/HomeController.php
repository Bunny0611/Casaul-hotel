<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;
use App\Models\Reservation;

class HomeController extends Controller
{
    protected function featuredRooms(): array
    {
        return [
            [
                'slug' => 'deluxe-room',
                'name' => 'Deluxe Room',
                'price' => '₱3,500',
                'tagline' => 'Elegant comfort for a restful getaway.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Our Deluxe Room pairs a warm, modern aesthetic with airy interiors, plush bedding, and a convenient layout designed for both relaxation and productivity.',
                'features' => ['King or twin beds', 'Private bath', 'High-speed Wi‑Fi', 'Room service'],
            ],
            [
                'slug' => 'executive-room',
                'name' => 'Executive Room',
                'price' => '₱6,500',
                'tagline' => 'Sophisticated luxury for work and leisure.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'The Executive Room is crafted for guests who want a more elevated experience, with generous space, refined finishes, and a tranquil atmosphere throughout the stay.',
                'features' => ['Executive lounge access', 'Large workspace', 'Premium amenities', 'City view'],
            ],
            [
                'slug' => 'presidential-room',
                'name' => 'Presidential Room',
                'price' => '₱12,000',
                'tagline' => 'A grand stay with a sense of occasion.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'Designed for memorable stays, the Presidential Room offers a luxurious ambiance, refined details, and spacious comfort that effortlessly balances elegance and practicality.',
                'features' => ['VIP service', 'Luxury furnishings', 'Premium toiletries', 'Private seating area'],
            ],
            [
                'slug' => 'standard-room',
                'name' => 'Standard Room',
                'price' => '₱2,800',
                'tagline' => 'Simple comfort with a polished finish.',
                'image' => 'image/Royal-Suite-room.jpg',
                'description' => 'A well-appointed Standard Room brings together comfort and clarity, making it ideal for guests seeking a fresh, restful base in the heart of the city.',
                'features' => ['Complimentary breakfast', 'Air-conditioned', 'Smart TV', 'Daily housekeeping'],
            ],
        ];
    }

    public function index()
    {
        $rooms = $this->featuredRooms();
        return view('index', compact('rooms'));
    }

    public function reservation()
    {
        $rooms = Room::where('status', 'available')->get();

        $amenities = [
            ['id' => 'swimming_pool', 'name' => 'Swimming Pool', 'description' => 'Access to the pool for up to 4 guests.', 'price' => 1000, 'details' => 'Includes towels and pool lounge seating.'],
            ['id' => 'breakfast', 'name' => 'Breakfast Buffet', 'description' => 'Daily buffet breakfast for 2 guests.', 'price' => 800, 'details' => 'Includes hot and cold selections, coffee, and juice.'],
            ['id' => 'spa', 'name' => 'Spa Access', 'description' => 'Full spa access for one day.', 'price' => 1500, 'details' => 'Enjoy sauna, steam room, and private relaxation lounge.'],
        ];

        $events = [
            ['id' => 'wedding', 'name' => 'Wedding Package', 'description' => 'Elegant wedding package for 100 guests.', 'price' => 25000, 'details' => 'Venue, catering, decorations, and coordination included.'],
            ['id' => 'corporate', 'name' => 'Corporate Event', 'description' => 'Conference package for 50 guests.', 'price' => 18000, 'details' => 'Meeting room, AV support, and refreshments provided.'],
            ['id' => 'birthday', 'name' => 'Birthday Celebration', 'description' => 'Celebration package for up to 40 guests.', 'price' => 12000, 'details' => 'Includes decorations, cake, and buffet options.' ],
        ];

        $dining = [
            ['id' => 'romantic_dinner', 'name' => 'Romantic Dinner', 'description' => 'Candlelit dinner for 2 guests.', 'price' => 1500, 'details' => 'Two-course meal with wine pairing.'],
            ['id' => 'family_feast', 'name' => 'Family Feast', 'description' => 'Dinner package for 4 guests.', 'price' => 2200, 'details' => 'Shared set menu with appetizers, mains, and dessert.'],
            ['id' => 'seafood_banquet', 'name' => 'Seafood Banquet', 'description' => 'Premium seafood experience for 2 guests.', 'price' => 1800, 'details' => 'Includes seafood platter and dessert.'],
        ];

        return view('reservation', compact('rooms', 'amenities', 'events', 'dining'));
    }

    public function storeReservation(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'total_amount' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string',
        ]);

        Reservation::create(array_merge($validated, ['status' => 'pending']));

        return redirect()->route('reservation')->with('success', 'Your reservation request has been submitted. We will contact you soon.');
    }
    
    public function accommodation()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('accommodation', compact('rooms'));
    }

    public function profile()
    {
        abort_unless(auth()->user()->role === 'guest', 403);

        return view('profile');
    }

    public function records()
    {
        abort_unless(auth()->user()->role === 'guest', 403);

        $reservations = Reservation::with('room')
            ->where('guest_email', auth()->user()->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile-records', compact('reservations'));
    }

    public function roomDetail($slug)
    {
        $room = collect($this->featuredRooms())->firstWhere('slug', $slug);

        if (!$room) {
            abort(404);
        }

        return view('room-detail', compact('room'));
    }
    
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        
        Message::create([
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'message' => $validated['message'],
        ]);
        
        return redirect()->back()->with('success', 'Message sent successfully! We will get back to you soon.');
    }
}
