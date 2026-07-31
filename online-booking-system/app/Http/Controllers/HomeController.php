<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;

class HomeController extends Controller
{
    private function roomCatalog(): array
    {
        return [
            [
                'slug' => 'deluxe-room',
                'name' => 'Deluxe Room',
                'tag' => 'Featured Stay',
                'price' => '₱3,500',
                'image' => asset('image/Royal-Suite-room.jpg'),
                'intro' => 'A refined retreat for guests who value elegance, comfort, and a restorative stay.',
                'description' => 'Our Deluxe Room blends contemporary styling with warm hospitality, creating a restful place to recharge. Each room includes plush bedding, a spa-inspired bath, generous storage space, and a calming palette that feels both modern and inviting. Perfect for couples, family weekends, and short city escapes.',
                'amenities' => ['King-sized bed', 'Rain shower', 'Complimentary Wi‑Fi', 'Smart TV', 'In-room coffee setup'],
                'rooms' => array_map(fn($i) => 'Deluxe Room ' . $i, range(101, 110)),
            ],
            [
                'slug' => 'standard-room',
                'name' => 'Standard Room',
                'tag' => 'Essential Comfort',
                'price' => '₱2,200',
                'image' => asset('image/HM.jpg'),
                'intro' => 'A clean, welcoming stay designed for practical comfort and easy convenience.',
                'description' => 'The Standard Room is a versatile choice for guests seeking a relaxed and efficient accommodation. It offers a cozy sleeping arrangement, thoughtfully placed lighting, and a streamlined design that keeps everything comfortable and uncluttered. Ideal for business trips, family stays, or easy weekend getaways.',
                'amenities' => ['Queen bed', 'Workspace area', 'Air conditioning', 'Desk lamp', 'Complimentary toiletries'],
                'rooms' => array_map(fn($i) => 'Standard Room ' . $i, range(201, 210)),
            ],
            [
                'slug' => 'executive-suite',
                'name' => 'Executive Suite',
                'tag' => 'Executive Escape',
                'price' => '₱6,800',
                'image' => asset('image/Royal-Suite-room.jpg'),
                'intro' => 'An elevated suite for business travelers and guests who prefer extra space and polish.',
                'description' => 'The Executive Suite offers a more expansive experience with a refined atmosphere and generous room to unwind. Designed with both work and leisure in mind, it includes a comfortable lounge arrangement, elegant finishes, and a premium in-room experience for guests expecting a more elevated stay.',
                'amenities' => ['Lounge corner', 'Premium linens', 'Work desk', 'Mini bar', 'Priority housekeeping'],
                'rooms' => array_map(fn($i) => 'Executive Suite ' . $i, range(301, 310)),
            ],
            [
                'slug' => 'presidential-suite',
                'name' => 'Presidential Suite',
                'tag' => 'Signature Luxury',
                'price' => '₱12,500',
                'image' => asset('image/HM.jpg'),
                'intro' => 'The grandest stay, crafted for signature occasions, dignified comfort, and unforgettable luxury.',
                'description' => 'The Presidential Suite delivers an indulgent hospitality experience with layered textures, grand proportions, and curated details that make every moment feel special. Ideal for milestone occasions, executive stays, or guests seeking a truly exceptional retreat with a flawless balance of privacy and elegance.',
                'amenities' => ['King suite layout', 'Luxury bath amenities', 'Private lounge', 'Butler assistance', 'Dining area'],
                'rooms' => array_map(fn($i) => 'Presidential Suite ' . $i, range(401, 410)),
            ],
        ];
    }

    public function index()
    {
        $rooms = Room::where('status', 'available')->take(3)->get();
        return view('index', compact('rooms'));
    }
    
    public function accommodation()
    {
        try {
            $rooms = Room::where('status', 'available')->get();
        } catch (\Throwable $e) {
            $rooms = collect([]);
        }

        $featuredRooms = $this->roomCatalog();

        return view('accommodation', compact('rooms', 'featuredRooms'));
    }

    public function roomDetail($slug)
    {
        $catalog = $this->roomCatalog();
        $room = collect($catalog)->firstWhere('slug', $slug);

        abort_if(!$room, 404);

        return view('accommodation-room', compact('room'));
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
