<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Show the guest login form.
     */
    public function showGuestLoginForm()
    {
        return view('guest.login');
    }

    /**
     * Handle an admin login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:admin,housekeeping,employee'],
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('role', $credentials['role'])
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect based on role
            if ($credentials['role'] === 'housekeeping') {
                return redirect()->intended(route('housekeeping.dashboard'));
            }

            if ($credentials['role'] === 'employee') {
                return redirect()->intended(route('employee.dashboard'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records for the selected role.',
        ])->onlyInput('email');
    }

    /**
     * Handle a guest login attempt.
     */
    public function guestLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('profile'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle a guest registration attempt.
     */
    public function guestRegister(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['required', 'string', 'max:3'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'contact_no' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_initial' => $validated['middle_initial'],
            'email' => $validated['email'],
            'contact_no' => $validated['contact_no'],
            'name' => trim($validated['first_name'].' '.$validated['middle_initial'].' '.$validated['last_name']),
            'password' => Hash::make($validated['password']),
            'role' => 'guest',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('profile'));
    }

    /**
     * Log the admin out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

