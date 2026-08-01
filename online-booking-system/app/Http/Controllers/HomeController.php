<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;

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
    
    public function accommodation()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('accommodation', compact('rooms'));
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
