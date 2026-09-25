<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Staff;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return response()
            ->view('admin.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
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

    public function redirectToGoogle()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('home', ['auth' => 'signin'])
                ->withErrors(['email' => 'Google sign-in is not configured yet. Please use email sign-in or contact the hotel.']);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('home', ['auth' => 'signin'])
                ->withErrors(['email' => 'Google sign-in is not configured yet. Please use email sign-in or contact the hotel.']);
        }

        $googleUser = Socialite::driver('google')->user();

        if (! $googleUser->getEmail()) {
            return redirect()->route('home', ['auth' => 'signin'])
                ->withErrors(['email' => 'Google did not provide an email address.']);
        }

        $guest = Guest::where('email', $googleUser->getEmail())->first();

        if (! $guest) {
            $name = trim((string) $googleUser->getName());
            $nameParts = preg_split('/\s+/', $name, 2);
            $firstName = $nameParts[0] ?? 'Google';
            $lastName = $nameParts[1] ?? 'Guest';

            $guest = Guest::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_initial' => null,
                'name' => $name !== '' ? $name : $googleUser->getEmail(),
                'email' => $googleUser->getEmail(),
                'contact_no' => null,
                'password' => Hash::make(Str::random(40)),
            ]);
            $guest->markEmailAsVerified();
        } elseif (! $guest->hasVerifiedEmail()) {
            $guest->markEmailAsVerified();
        }

        Auth::guard('guest')->login($guest, true);
        $request->session()->regenerate();

        return redirect()->route('profile.edit');
    }

    public function showForgotPasswordForm()
    {
        return view('guest.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email']]);

        $status = Password::broker('guests')->sendResetLink($credentials);

        return back()->with('status', __($status));
    }

    public function showResetPasswordForm(string $token)
    {
        return view('guest.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $status = Password::broker('guests')->reset(
            $data,
            function (Guest $guest, string $password): void {
                $guest->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('home', ['auth' => 'signin'])
                ->with('status', 'Your password has been reset. You may now sign in.');
        }

        return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
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

        $user->sendEmailVerificationNotification();

        $request->session()->put([
            'verification_email' => $user->email,
            'verification_name' => $user->first_name,
        ]);

        return redirect()->route('guest.verification.notice');
    }

    /**
     * Show the email verification instructions after guest registration.
     */
    public function showVerificationNotice(Request $request)
    {
        abort_unless($request->session()->has('verification_email'), 404);

        return view('guest.verify-email', [
            'email' => $request->session()->get('verification_email'),
            'name' => $request->session()->get('verification_name'),
        ]);
    }

    /**
     * Resend Laravel's built-in verification notification.
     */
    public function resendVerification(Request $request)
    {
        $email = $request->session()->get('verification_email');
        $guest = $email ? Guest::where('email', $email)->first() : null;

        if ($guest && ! $guest->hasVerifiedEmail()) {
            $guest->sendEmailVerificationNotification();
        }

        return back()->with('status', 'A new verification link has been sent.');
    }

    /**
     * Verify a guest through Laravel's signed verification URL without logging in.
     */
    public function verifyGuestEmail(Request $request, string $id, string $hash)
    {
        abort_unless($request->hasValidSignature(), 403);

        $guest = Guest::findOrFail($id);
        abort_unless(hash_equals(sha1($guest->getEmailForVerification()), $hash), 403);

        if (! $guest->hasVerifiedEmail() && $guest->markEmailAsVerified()) {
            event(new Verified($guest));
        }

        $request->session()->forget(['verification_email', 'verification_name']);

        return redirect()->route('home', ['auth' => 'signin'])
            ->with('status', 'Your email has been verified. You may now sign in.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $isStaff = Auth::guard('web')->check();
        $isGuest = Auth::guard('guest')->check();

        if ($isStaff) {
            Auth::guard('web')->logout();
        }

        if ($isGuest) {
            Auth::guard('guest')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($isStaff ? 'login' : 'home');
    }
}

