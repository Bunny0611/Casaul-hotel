<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casaul Hotel</title>

<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">

</head>
<body>


<div class="page-loader" id="page-loader">
    <div class="loader-content">
        <div class="loader-logo"></div>
        <div class="loader-spinner"></div>
    </div>
</div>


<div class="floating-element floating-element-1"></div>
<div class="floating-element floating-element-2"></div>
<div class="floating-element floating-element-3"></div>

<nav class="site-header" aria-label="Main navigation">

    <div class="logo" aria-label="CASAUL Hotel">
        <div class="logo-mark">
            <img src="{{ asset('image/LOGO.png') }}" alt="Casaul Hotel Logo" class="logo-img">
        </div>
        <div class="logo-text">
            <span class="logo-name">CASAUL HOTEL</span>
            <span class="logo-tag">LUXURY &amp; COMFORT</span>
        </div>
    </div>

    <button type="button" class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <ul class="nav-menu" id="nav-menu">

        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif>HOME</a></li>

        <li><a href="{{ route('accommodation') }}" class="{{ request()->routeIs('accommodation*') ? 'active' : '' }}" @if(request()->routeIs('accommodation*')) aria-current="page" @endif>ACCOMMODATION</a></li>

        <li><a href="{{ route('dining') }}" class="{{ request()->routeIs('dining') ? 'active' : '' }}" @if(request()->routeIs('dining')) aria-current="page" @endif>DINING</a></li>

        <li><a href="{{ route('events') }}" class="{{ request()->routeIs('events') ? 'active' : '' }}" @if(request()->routeIs('events')) aria-current="page" @endif>EVENTS</a></li>

        <li><a href="{{ route('aboutus') }}" class="{{ request()->routeIs('aboutus') ? 'active' : '' }}" @if(request()->routeIs('aboutus')) aria-current="page" @endif>ABOUT US</a></li>

        @auth('guest')
            <li><a href="{{ route('guest.records') }}#guest-request-form" class="{{ request()->routeIs('guest.records') ? 'active' : '' }}" @if(request()->routeIs('guest.records')) aria-current="page" @endif>GUEST REQUEST</a></li>
        @endauth

    </ul>

    <div class="nav-actions">
        <div class="nav-search-wrapper">
            <button type="button" class="nav-search-toggle" id="nav-search-toggle" aria-label="Open search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <form class="nav-search-form" id="nav-search-form" role="search">
                <input type="search" id="nav-search-input" placeholder="Search this page..." aria-label="Search this page">
                <button type="submit" class="nav-search-submit">GO</button>
            </form>
        </div>

@auth('guest')
            <div class="profile-dropdown" id="profile-dropdown">
                <button type="button" class="profile-trigger" id="profile-trigger" aria-label="My Account" aria-expanded="false" aria-controls="profile-menu">
                    <span class="profile-trigger-avatar">
                        <i class="fas fa-user-circle"></i>
                    </span>
                </button>
                <div class="profile-menu" id="profile-menu" role="menu" aria-labelledby="profile-trigger">
                    <div class="profile-menu-header">
                        <span class="profile-menu-avatar"><i class="fas fa-user-circle"></i></span>
                        <div>
                            <p class="profile-menu-name">{{ auth('guest')->user()->name }}</p>
                            <p class="profile-menu-email">{{ auth('guest')->user()->email }}</p>
                        </div>
                    </div>
                    @if(auth('guest')->check())
                        <a href="{{ route('guest.records') }}" class="profile-menu-item" role="menuitem">
                            <i class="fas fa-list-alt"></i> My Reservations
                        </a>
                        @php
                            $guestHasReceiptAccess = \App\Models\Reservation::where('guest_email', auth('guest')->user()->email)
                                ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                                ->exists()
                                || \App\Models\RoomReservation::where('guest_email', auth('guest')->user()->email)
                                    ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                                    ->exists()
                                || \App\Models\EventReservation::where('guest_email', auth('guest')->user()->email)
                                    ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                                    ->exists()
                                || \App\Models\FacilityReservation::where('guest_email', auth('guest')->user()->email)
                                    ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                                    ->exists()
                                || \App\Models\DiningReservation::where('guest_email', auth('guest')->user()->email)
                                    ->whereIn('status', ['confirmed', 'checked-in', 'completed'])
                                    ->exists();
                        @endphp
                        @if($guestHasReceiptAccess)
                            <a href="{{ route('guest.receipts') }}" class="profile-menu-item" role="menuitem">
                                <i class="fas fa-receipt"></i> My Receipts
                            </a>
                        @endif
                        <a href="{{ route('guest.profile') }}" class="profile-menu-item" role="menuitem">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                    @endif
                    <div class="profile-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="profile-menu-form">
                        @csrf
                        <button type="submit" class="profile-menu-item profile-menu-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <button type="button" class="nav-signin-btn" id="guest-signin-trigger">SIGN IN</button>
        @endif
    </div>

</nav>

@php($signupHasErrors = old('auth_form') === 'signup' || $errors->hasAny(['first_name', 'last_name', 'middle_initial', 'contact_no', 'country_code', 'region_code', 'province_code', 'city_code', 'password_confirmation']))
@php($authHasErrors = $errors->has('email') || $signupHasErrors)
@php($authShouldOpen = $authHasErrors || request()->query('auth') === 'signin' || session()->has('status'))
<div class="auth-modal-backdrop{{ $authShouldOpen ? ' open' : '' }}" id="guest-auth-modal" aria-hidden="{{ $authShouldOpen ? 'false' : 'true' }}">
    <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="guest-auth-title">
        <button type="button" class="auth-close-btn" id="guest-auth-close" aria-label="Close">×</button>

        <div class="auth-brand">
            <img src="{{ asset('image/LOGO.png') }}" alt="Casaul Hotel" class="auth-brand-logo">
            <h2 id="guest-auth-title">CASAUL</h2>
        </div>

        @if($authHasErrors || session('status'))
            <div id="auth-message" class="auth-message" role="alert">
                {{ $errors->first() ?: session('status') }}
            </div>
        @else
            <div id="auth-message" class="auth-message" style="display:none;"></div>
        @endif

        <div class="auth-content">
            <div id="auth-signin-view" class="auth-panel{{ $signupHasErrors ? ' auth-hidden' : '' }}">
                <div class="auth-panel-heading">
                    <h3>Welcome Back</h3>
                    <p>Sign in to continue to your account.</p>
                </div>
                <a href="{{ route('guest.google.redirect') }}" class="auth-social-btn google-btn" id="google-signin-btn">
                    <i class="fab fa-google"></i>
                    Continue with Google
                </a>

                <div class="auth-divider"><span>OR</span></div>

                <form method="POST" action="{{ route('guest.login.submit') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="auth_form" value="signin">
                    <label class="auth-input-wrap"><i class="far fa-envelope" aria-hidden="true"></i><input type="email" name="email" class="auth-input" placeholder="Email address" required></label>
                    <label class="auth-input-wrap"><i class="fas fa-lock" aria-hidden="true"></i><input id="signin-password" type="password" name="password" class="auth-input" placeholder="Password" required><button type="button" class="auth-password-toggle" data-password-target="signin-password" aria-label="Show password"><i class="far fa-eye" aria-hidden="true"></i></button></label>
                    <a href="{{ route('guest.password.request') }}" class="auth-forgot-link">Forgot password?</a>
                    <button type="submit" class="auth-submit-btn">Sign In</button>
                </form>
            </div>

            <div id="auth-signup-view" class="auth-panel{{ $signupHasErrors ? '' : ' auth-hidden' }}">
                <div class="auth-panel-heading">
                    <h3>Create Your Account</h3>
                    <p>Join CASAUL and create your guest account.</p>
                </div>
                <form method="POST" action="{{ route('guest.register.submit') }}" class="auth-form" id="guest-signup-form">
                    @csrf
                    <input type="hidden" name="auth_form" value="signup">
                    <label class="auth-input-wrap"><i class="far fa-user" aria-hidden="true"></i><input type="text" name="first_name" class="auth-input" placeholder="First Name" value="{{ old('first_name') }}" required></label>
                    <label class="auth-input-wrap"><i class="far fa-user" aria-hidden="true"></i><input type="text" name="last_name" class="auth-input" placeholder="Last Name" value="{{ old('last_name') }}" required></label>
                    <label class="auth-input-wrap"><i class="far fa-id-card" aria-hidden="true"></i><input type="text" name="middle_initial" class="auth-input" placeholder="M.I." maxlength="3" value="{{ old('middle_initial') }}" required></label>
                    <label class="auth-input-wrap"><i class="far fa-envelope" aria-hidden="true"></i><input type="email" name="email" class="auth-input" placeholder="Gmail Address" value="{{ old('email') }}" required></label>
                    <label class="auth-input-wrap"><i class="fas fa-phone" aria-hidden="true"></i><input type="text" name="contact_no" class="auth-input" placeholder="Contact No." value="{{ old('contact_no') }}" required></label>
                    <div class="auth-location-grid">
                        <label class="auth-select-wrap"><span>Country <strong>*</strong></span><select name="country_code" id="guest-country" required data-old="{{ old('country_code') }}"><option value="">Select country</option></select></label>
                        <label class="auth-select-wrap"><span>Region/State</span><select name="region_code" id="guest-region" data-old="{{ old('region_code') }}" disabled><option value="">Select country first</option></select></label>
                        <label class="auth-select-wrap"><span>Province/Area</span><select name="province_code" id="guest-province" data-old="{{ old('province_code') }}" disabled><option value="">Select region/state first</option></select></label>
                        <label class="auth-select-wrap"><span>City</span><select name="city_code" id="guest-city" data-old="{{ old('city_code') }}" disabled><option value="">Select province/area first</option></select></label>
                    </div>
                    <input type="hidden" name="country_name" id="guest-country-name" value="{{ old('country_name') }}">
                    <input type="hidden" name="region_name" id="guest-region-name" value="{{ old('region_name') }}">
                    <input type="hidden" name="province_name" id="guest-province-name" value="{{ old('province_name') }}">
                    <input type="hidden" name="city_name" id="guest-city-name" value="{{ old('city_name') }}">
                    <label class="auth-input-wrap"><i class="fas fa-lock" aria-hidden="true"></i><input id="signup-password" type="password" name="password" class="auth-input" placeholder="Password" required><button type="button" class="auth-password-toggle" data-password-target="signup-password" aria-label="Show password"><i class="far fa-eye" aria-hidden="true"></i></button></label>
                    <label class="auth-input-wrap"><i class="fas fa-lock" aria-hidden="true"></i><input id="signup-password-confirmation" type="password" name="password_confirmation" class="auth-input" placeholder="Re-type Password" required><button type="button" class="auth-password-toggle" data-password-target="signup-password-confirmation" aria-label="Show password"><i class="far fa-eye" aria-hidden="true"></i></button></label>
                    <button type="submit" class="auth-submit-btn">Create Account</button>
                </form>
            </div>
        </div>

        <p class="auth-switch-text">
            <span id="auth-switch-label">Don’t have an account?</span>
            <button type="button" class="auth-switch-link" id="auth-switch-btn">Sign up</button>
        </p>
    </div>
</div>

@yield('content')

<footer>
    <div class="footer-container">
       
        <div class="footer-col footer-col-brand">
            <div class="footer-logo">
                <img src="{{ asset('image/LOGO.png') }}" alt="CASAUL Hotel Logo">
                <span>CASAUL Hotel</span>
            </div>
            <h4>TABACO CITY, ALBAY</h4>
            <p class="footer-hotel-name">CASAUL Hotel Tabaco</p>
            <p class="footer-contact"><strong>Mobile:</strong> (+63) 935 017 7564</p>
            <p class="footer-contact"><strong>Email:</strong> taba-roomsreservation@casahotels.com</p>
            <p class="footer-address">Tomas Cabiles St., Tabaco City</p>
        </div>

       
        <div class="footer-col footer-col-office">
            <h4>Corporate Office</h4>
            <p class="footer-contact"><strong>Tel. No.:</strong> (052) 203-0244 / (052) 203-0243</p>
            <p class="footer-contact"><strong>Email:</strong> inquiry@casaulhotels.com</p>
        </div>

        
        <div class="footer-col footer-col-brands">
            <h4>Our Brand</h4>
            <ul class="footer-brand-list">
                <li>CASAUL Hotel Tabaco</li>
                <li>CASAUL Hotel Bataan</li>
                <li>CASAUL Hotel Luxury Suites Tagaytay</li>
                <li>PROXY by CASAUL Hotel Albay</li>
                <li>The Inns by CASAUL Hotel Bacolod</li>
                <li>PROXY Plus by CASAUL Hotel Pangasinan</li>
            </ul>
        </div>

        <div class="footer-col footer-col-inspiration">
            <h4>Stay Inspired</h4>
            <p class="footer-contact">Receive thoughtful ideas and updates from CASAUL Hotel.</p>
            <form class="footer-newsletter-form" action="#" method="POST" onsubmit="return false;">
                <label class="sr-only" for="footer-newsletter-email">Email address</label>
                <input id="footer-newsletter-email" type="email" placeholder="Enter your email" aria-label="Enter your email">
                <button type="submit" aria-label="Subscribe">-&gt;</button>
            </form>
        </div>
    </div>
    <div class="footer-bottom"><span>&copy; {{ date('Y') }} CASAUL Hotel. All Rights Reserved.</span><span>Privacy Policy&nbsp;&nbsp; | &nbsp;&nbsp;Terms &amp; Conditions</span></div>
</footer>


@include('chatbot.widget')

@vite('resources/js/app.js')

<script>
    (function () {
        const siteHeader = document.querySelector('nav.site-header');
        const applyHeaderScrollState = function () {
            if (!siteHeader) return;
            siteHeader.classList.toggle('scrolled', window.scrollY > 50);
        };

        applyHeaderScrollState();
        window.addEventListener('scroll', applyHeaderScrollState, { passive: true });

        const navToggle = document.getElementById('nav-toggle');
        const navMenu = document.getElementById('nav-menu');

        if (navToggle && navMenu) {
            navToggle.addEventListener('click', function () {
                const isOpen = navMenu.classList.toggle('open');
                navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            navMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    navMenu.classList.remove('open');
                    navToggle.setAttribute('aria-expanded', 'false');
                });
            });
        }
    })();
</script>

</body>
</html>
