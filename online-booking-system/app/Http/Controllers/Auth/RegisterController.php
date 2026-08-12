<?php

use Illuminate\Support\Facades\Auth;

public function store(Request $request)
{
    // validate & create user...
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        // other fields...
    ]);

    // automatically log in the new user
    Auth::login($user);

    return redirect()->route('home');
}