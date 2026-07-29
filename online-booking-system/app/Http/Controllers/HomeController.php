<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;

class HomeController extends Controller
{
    public function index()
    {
        $rooms = Room::where('status', 'available')->take(3)->get();
        return view('index', compact('rooms'));
    }
    
    public function accommodation()
    {
        $rooms = Room::where('status', 'available')->get();
        return view('accommodation', compact('rooms'));
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
