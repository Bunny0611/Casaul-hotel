@extends('app')

@section('content')
<main class="home-reference-page">
    <section class="home-reference-hero">
        <img src="{{ asset('image/HM.jpg') }}" alt="Warm CASAUL Hotel guest room">
        <div class="home-reference-hero-overlay"></div>
        <div class="home-reference-hero-copy">
            <p class="home-reference-eyebrow">Comfort. Elegance. Memories.</p>
            <h1>A Stay to<br>Remember</h1>
            <p>Experience exceptional hospitality, luxurious<br class="home-desktop-break"> comfort, and unforgettable moments at CASAUL Hotel.</p>
            <a class="home-gold-button{{ auth('guest')->check() ? '' : ' js-auth-trigger' }}" href="{{ auth('guest')->check() ? route('reservation') : '#guest-auth-modal' }}"{{ auth('guest')->check() ? '' : ' data-auth-trigger' }}>Book Your Stay <span>-></span></a>
        </div>
    </section>

    <section class="home-welcome-section">
        <div class="home-welcome-copy">
            <p class="home-section-eyebrow">Welcome to</p>
            <h2><strong>CASAUL</strong> Hotel</h2>
            <div class="home-gold-rule"></div>
            <p>Where comfort meets elegance. Whether you're here for business, leisure, or a special celebration, we provide the perfect setting for a truly memorable stay.</p>
            <a class="home-outline-button" href="{{ route('aboutus') }}">Discover More <span>-></span></a>
        </div>
        <img src="{{ asset('image/HM.jpg') }}" alt="CASAUL Hotel exterior at night">
    </section>

    <section class="home-experience-section">
        <header class="home-centered-heading"><h2>Experience CASAUL</h2><div class="home-heading-ornament"><span></span><i class="fas fa-star"></i><span></span></div></header>
        <div class="home-experience-grid">
            <a href="{{ route('accommodation') }}" class="home-experience-card"><img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="CASAUL accommodation"><span class="home-experience-icon"><i class="fas fa-bed"></i></span><h3>Accommodation</h3><p>Relax in our thoughtfully designed rooms and enjoy a restful stay.</p><b>Explore Rooms <span>-></span></b></a>
            <a href="{{ route('dining') }}" class="home-experience-card"><img src="{{ asset('image/HM.jpg') }}" alt="CASAUL dining experience"><span class="home-experience-icon"><i class="fas fa-utensils"></i></span><h3>Dining</h3><p>Savor delicious cuisine crafted from the finest ingredients.</p><b>View Dining Options <span>-></span></b></a>
            <a href="{{ route('events') }}" class="home-experience-card"><img src="{{ asset('image/HM.jpg') }}" alt="CASAUL event space"><span class="home-experience-icon"><i class="fas fa-calendar-alt"></i></span><h3>Events</h3><p>Host unforgettable events with our elegant venues and services.</p><b>Plan Your Event <span>-></span></b></a>
            <a href="{{ route('aboutus') }}" class="home-experience-card"><img src="{{ asset('image/HM.jpg') }}" alt="CASAUL Hotel amenities"><span class="home-experience-icon"><i class="fas fa-spa"></i></span><h3>Amenities</h3><p>Enjoy premium facilities designed for your comfort and relaxation.</p><b>Learn More <span>-></span></b></a>
        </div>
    </section>

    <section class="home-featured-section">
        <header class="home-featured-heading"><h2>Featured Rooms</h2><a href="{{ route('accommodation') }}">View All Rooms <span>-></span></a></header>
        <div class="home-room-grid">
            @foreach($rooms as $room)
                <a href="{{ route('accommodation.room', ['slug' => $room['slug']]) }}" class="home-room-card">
                    <img src="{{ asset($room['image']) }}" alt="{{ $room['name'] }}">
                    <div><h3>{{ $room['name'] }}</h3><p>{{ $room['price'] }} <span>/ night</span></p><div class="home-room-meta"><span><i class="fas fa-users"></i> Guests</span><span><i class="fas fa-bed"></i> 1 Bed</span><span><i class="fas fa-wifi"></i> Wi-Fi</span><span><i class="fas fa-snowflake"></i> AC</span></div></div>
                    <b>View Room <span>-></span></b>
                </a>
            @endforeach
        </div>
    </section>

    <section class="home-benefits-section">
        <header class="home-centered-heading"><h2>Why Stay With Us</h2><div class="home-heading-ornament"><span></span><i class="fas fa-star"></i><span></span></div></header>
        <div class="home-benefits-grid">
            <article><i class="fas fa-crown"></i><div><h3>Premium Comfort</h3><p>Enjoy well-appointed rooms, modern amenities, and unmatched comfort.</p></div></article>
            <article><i class="far fa-heart"></i><div><h3>Exceptional Service</h3><p>Our dedicated team is here to ensure a warm, personalized, and memorable stay.</p></div></article>
            <article><i class="fas fa-map-marker-alt"></i><div><h3>Prime Location</h3><p>Conveniently located near top attractions, dining, and business establishments.</p></div></article>
        </div>
    </section>
</main>
@endsection
