<?php

namespace App\Http\Controllers\Auth;

use App\Models\Guest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:guest_users,email', 'unique:staff_users,email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        $user = Guest::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('guest')->login($user);

        return redirect()->route('home');
    }
}