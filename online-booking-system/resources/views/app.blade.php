<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casaul Hotel</title>

<link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">

</head>
<body>


<div class="page-loader" id="page-loader">
    <div class="loader-content">
        <div class="loader-logo">CASAUL HOTEL</div>
        <div class="loader-spinner"></div>
    </div>
</div>


<div class="floating-element floating-element-1"></div>
<div class="floating-element floating-element-2"></div>
<div class="floating-element floating-element-3"></div>

<nav>

    <div class="logo">
        <img src="{{ asset('image/LOGO.png') }}" alt="Casaul Hotel Logo" class="logo-img">
        CASAUL HOTEL
    </div>

    <ul>

        <li><a href="{{ route('home') }}">HOME</a></li>

        <li><a href="{{ route('accommodation') }}">ACCOMMODATION</a></li>

        <li><a href="{{ route('dining') }}">DINING</a></li>

        <li><a href="{{ route('aboutus') }}">ABOUT US</a></li>

        @auth('guest')
            <li><a href="{{ route('guest.records') }}#guest-request-form">GUEST REQUEST</a></li>
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

@php($signupHasErrors = $errors->any() && old('first_name') !== null)
<div class="auth-modal-backdrop{{ $errors->any() ? ' open' : '' }}" id="guest-auth-modal" aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
    <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="guest-auth-title">
        <button type="button" class="auth-close-btn" id="guest-auth-close" aria-label="Close">×</button>

        <div class="auth-brand">
            <img src="{{ asset('image/LOGO.png') }}" alt="Casaul Hotel" class="auth-brand-logo">
            <h2 id="guest-auth-title">Welcome to CASAUL</h2>
            <p>Sign in to continue or create a guest account.</p>
        </div>

        @if($errors->any())
            <div id="auth-message" class="auth-message" role="alert">
                {{ $errors->first() }}
            </div>
        @else
            <div id="auth-message" class="auth-message" style="display:none;"></div>
        @endif

        <div class="auth-content">
            <div id="auth-signin-view" class="auth-panel{{ $signupHasErrors ? ' auth-hidden' : '' }}">
                <button type="button" class="auth-social-btn google-btn" id="google-signin-btn">
                    <i class="fab fa-google"></i>
                    Continue with Google
                </button>

                <div class="auth-divider"><span>or sign in with email</span></div>

                <form method="POST" action="{{ route('guest.login.submit') }}" class="auth-form">
                    @csrf
                    <input type="email" name="email" class="auth-input" placeholder="Email address" required>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                    <button type="submit" class="auth-submit-btn">Sign In</button>
                </form>
            </div>

            <div id="auth-signup-view" class="auth-panel{{ $signupHasErrors ? '' : ' auth-hidden' }}">
                <form method="POST" action="{{ route('guest.register.submit') }}" class="auth-form" id="guest-signup-form">
                    @csrf
                    <input type="text" name="first_name" class="auth-input" placeholder="First Name" value="{{ old('first_name') }}" required>
                    <input type="text" name="last_name" class="auth-input" placeholder="Last Name" value="{{ old('last_name') }}" required>
                    <input type="text" name="middle_initial" class="auth-input" placeholder="M.I" maxlength="3" value="{{ old('middle_initial') }}" required>
                    <input type="email" name="email" class="auth-input" placeholder="Gmail Address" value="{{ old('email') }}" required>
                    <input type="text" name="contact_no" class="auth-input" placeholder="Contact No." value="{{ old('contact_no') }}" required>
                    <input type="password" name="password" class="auth-input" placeholder="Password" required>
                    <input type="password" name="password_confirmation" class="auth-input" placeholder="Re-Type Password" required>
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
    </div>
</footer>




<button id="chat-toggle" class="chat-toggle" aria-label="Open chat">
    <svg id="chat-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <svg id="chat-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
</button>


<div id="chat-widget" class="chat-widget">
   
    <div class="chat-header">
        <div class="chat-header-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            </svg>
        </div>
        <div class="chat-header-text">
            <span class="chat-header-title">Virtual Assistant</span>
            <span class="chat-header-status">Online</span>
        </div>
        <button id="chat-close-btn" class="chat-close-btn" aria-label="Close chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

   
    <div id="chat-messages" class="chat-messages">
        <div class="chat-msg bot">
            <div class="chat-msg-content">
                <p>Hi! I am a virtual assistant and I can help you book your upcoming stay.</p>
                <p>Please let us know how we can help you today by tapping on the options or by sending us a message.</p>
            </div>
            <span class="chat-msg-time">Just now</span>
        </div>
    </div>

  
    <div id="chat-quick-replies" class="chat-quick-replies">
        <button class="quick-reply" data-action="book">Book a Room</button>
        <button class="quick-reply" data-action="inquiries">Inquiries</button>
        <button class="quick-reply" data-action="availability">Check Availability</button>
        <button class="quick-reply" data-action="offers">Special Offers</button>
        <button class="quick-reply" data-action="contact">Contact Us</button>
    </div>

  
    <div class="chat-input-area">
        <form id="message-form" action="{{ route('send.message') }}" method="POST">
            @csrf
            <input type="hidden" name="name" id="customer-name" value="Guest">
            <input type="hidden" name="email" id="customer-email" value="guest@example.com">
            <input type="text" id="chat-input" name="message" class="chat-input" placeholder="Type your message..." autocomplete="off" required>
            <button type="submit" id="chat-send-btn" class="chat-send-btn" aria-label="Send message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>

</body>
</html>
