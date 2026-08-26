<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Staff;
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

            'role' => ['required', 'in:admin,employee,housekeeping'],
        ]);

        $user = Staff::where('email', $credentials['email'])
            ->where('role', $credentials['role'])
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if (! $user->is_active) {
                return back()->withErrors([
                    'email' => 'This account has been deactivated. Please contact the administrator.',
                ])->onlyInput('email');
            }

            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

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

        if (Auth::guard('guest')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->route('home');
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
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_initial' => 'required|string|max:3',
            'email' => ['required', 'email', 'unique:staff_users,email', 'unique:guest_users,email'],
            'contact_no' => 'required|string|max:25',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = Guest::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_initial' => $data['middle_initial'],
            'name' => trim($data['first_name'].' '.$data['middle_initial'].' '.$data['last_name']),
            'email' => $data['email'],
            'contact_no' => $data['contact_no'],
            'password' => Hash::make($data['password']),
        ]);

        // auto-login and secure session
        Auth::guard('guest')->login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard($request->user('guest') ? 'guest' : 'web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

