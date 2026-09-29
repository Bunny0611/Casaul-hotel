@extends('app')

@section('content')

<main class="room-detail-page">
    <section class="room-detail-hero" aria-labelledby="room-detail-title">
        <div class="room-detail-hero-inner">
            <div class="room-detail-copy">
                <a href="{{ route('accommodation') }}" class="room-detail-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> BACK TO ROOMS</a>
                <p class="room-detail-kicker">STANDARD ROOM</p>
                <h1 id="room-detail-title">Standard Room</h1>
                <p class="room-detail-price">₱2,800.00 <span>/ night</span></p>
                <p class="room-detail-description">Comfortable standard room for up to two guests, designed for a relaxing and convenient stay.</p>
                <div class="room-detail-features" aria-label="Room amenities">
                    <span><i class="fas fa-users" aria-hidden="true"></i>2 Guests</span>
                    <span><i class="fas fa-bed" aria-hidden="true"></i>1 Bed</span>
                    <span><i class="fas fa-wifi" aria-hidden="true"></i>Wi-Fi</span>
                    <span><i class="fas fa-snowflake" aria-hidden="true"></i>Air conditioning</span>
                </div>
                <a href="{{ auth('guest')->check() ? route('reservation') : '#guest-auth-modal' }}" class="room-detail-primary{{ auth('guest')->check() ? '' : ' js-auth-trigger' }}"{{ auth('guest')->check() ? '' : ' data-auth-trigger' }}>BOOK THIS ROOM <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="room-detail-visual">
                <img src="{{ asset($room['image']) }}" alt="Standard Room">
            </div>
        </div>
    </section>

    <section class="room-stay" aria-labelledby="room-stay-title">
        <svg class="room-stay-botanical room-stay-botanical-left" viewBox="0 0 90 180" aria-hidden="true">
            <g fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 174C27 145 25 111 12 77 5 59 5 38 8 11M9 116C29 106 43 89 49 63M13 83C29 78 41 64 48 43M22 142C39 131 53 115 61 95M9 116C4 96 3 78 7 61M13 83C5 69 3 53 7 38M22 142C14 127 9 112 8 96"/>
                <path d="M11 78C20 82 27 89 30 99 20 97 14 90 11 78ZM7 61C17 64 24 70 28 79 18 77 11 70 7 61ZM20 117C30 120 37 127 40 136 30 134 23 127 20 117ZM49 64C47 52 50 40 57 31 61 43 58 55 49 64ZM29 105C40 102 51 104 60 111 49 116 38 114 29 105ZM35 88C45 84 55 84 65 89 56 96 45 96 35 88ZM45 128C54 122 64 120 75 123 67 132 56 135 45 128Z"/>
            </g>
        </svg>
        <div class="room-stay-inner">
            <div class="room-stay-intro">
                <p class="room-stay-kicker">YOUR STAY</p>
                <h2 id="room-stay-title">More Than a Room,<br>A Better Stay</h2>
                <div class="room-stay-rule"><span></span></div>
                <p class="room-stay-description">Thoughtful comforts, elegant details, and a relaxing atmosphere — everything you need for a memorable stay.</p>
            </div>
            <div class="room-stay-features">
                <article class="room-stay-feature">
                    <span class="room-stay-icon"><i class="fas fa-spa" aria-hidden="true"></i></span>
                    <h3>Relax &amp; Unwind</h3>
                    <p>Step into a calm and cozy space where you can rest, recharge, and feel at home.</p>
                </article>
                <article class="room-stay-feature">
                    <span class="room-stay-icon"><i class="fas fa-bed" aria-hidden="true"></i></span>
                    <h3>Thoughtfully Designed</h3>
                    <p>Every detail is crafted to create a warm, inviting atmosphere for your comfort.</p>
                </article>
                <article class="room-stay-feature">
                    <span class="room-stay-icon"><i class="fas fa-bell" aria-hidden="true"></i></span>
                    <h3>Easy, Comfortable Stay</h3>
                    <p>From check-in to check-out, we make your stay simple, seamless, and stress-free.</p>
                </article>
            </div>
        </div>
        <svg class="room-stay-botanical room-stay-botanical-right" viewBox="0 0 90 180" aria-hidden="true">
            <g fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M85 174C63 145 65 111 78 77 85 59 85 38 82 11M81 116C61 106 47 89 41 63M77 83C61 78 49 64 42 43M68 142C51 131 37 115 29 95M81 116C86 96 87 78 83 61M77 83C85 69 87 53 83 38M68 142C76 127 81 112 82 96"/>
                <path d="M79 78C70 82 63 89 60 99 70 97 76 90 79 78ZM83 61C73 64 66 70 62 79 72 77 79 70 83 61ZM70 117C60 120 53 127 50 136 60 134 67 127 70 117ZM41 64C43 52 40 40 33 31 29 43 32 55 41 64ZM61 105C50 102 39 104 30 111 41 116 52 114 61 105ZM55 88C45 84 35 84 25 89 34 96 45 96 55 88ZM45 128C36 122 26 120 15 123 23 132 34 135 45 128Z"/>
            </g>
        </svg>
    </section>
</main>

@endsection
