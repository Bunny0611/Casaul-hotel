<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        abort_unless(Auth::guard('guest')->check(), 403);

        return view('profile');
    }

    public function update(Request $request)
    {
        $user = Auth::guard('guest')->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'unique:guest_users,email,'.$user->id, 'unique:staff_users,email'],
            'contact_no' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->contact_no = $data['contact_no'] ?? null;

        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();
        Auth::guard('guest')->login($user, true);

        return view('profile')->with('status', 'Profile updated.');
    }
}