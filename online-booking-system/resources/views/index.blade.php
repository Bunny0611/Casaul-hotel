    @extends('app')

@section('content')

<main class="home-page">
    <section class="home-hero">
        <img src="{{ asset('image/HM.jpg') }}" alt="Comfortable CASAUL Hotel room">
        <div class="home-hero-overlay"></div>
        <div class="home-hero-copy">
            <p class="home-eyebrow">Welcome to CASAUL Hotel</p>
            <h1>Experience Comfort<br>Like Never Before</h1>
            <p>Where elegance meets hospitality. Discover a relaxing stay with world-class amenities and exceptional service.</p>
            <a href="{{ auth('guest')->check() ? route('reservation') : '#guest-auth-modal' }}" class="home-primary-btn{{ auth('guest')->check() ? '' : ' js-auth-trigger' }}"{{ auth('guest')->check() ? '' : ' data-auth-trigger' }}>Book Your Stay <span aria-hidden="true">-&gt;</span></a>
        </div>
    </section>

    <section class="home-rooms">
        <header class="home-section-heading">
            <h2>Featured Rooms</h2>
            <div class="home-heading-rule"><span></span><i class="fas fa-sun"></i><span></span></div>
            <p>Discover our handpicked rooms for your perfect stay.</p>
        </header>

        <div class="home-room-grid">
            @foreach(array_slice($rooms, 0, 3) as $roomIndex => $room)
                <a href="{{ route('accommodation.room', ['slug' => $room['slug']]) }}" class="home-room-card">
                    <div class="home-room-image">
                        <img src="{{ asset($room['image']) }}" alt="{{ $room['name'] }}">
                        <span>{{ ['BEST SELLER', 'TOP RATED', 'LUXURY'][$roomIndex] }}</span>
                    </div>
                    <div class="home-room-content">
                        <h3>{{ $room['name'] }}</h3>
                        <strong>{{ $room['price'] }} <small>/ night</small></strong>
                        <p>{{ $room['tagline'] }}</p>
                        <span class="home-room-arrow" aria-hidden="true">-&gt;</span>
                    </div>
                </a>
            @endforeach
        </div>

        <a href="{{ route('accommodation') }}" class="home-secondary-btn">View All Rooms</a>
    </section>

    <section class="home-trust-section">
        <div class="home-trust-copy">
            <h2>Why Guests Love Us</h2>
            <div class="home-accent-line"></div>
            <div class="home-trust-grid">
                <article><i class="fas fa-gem"></i><div><h3>Premium Amenities</h3><p>Fast Wi-Fi, cozy rooms, and everything you need.</p></div></article>
                <article><i class="fas fa-users"></i><div><h3>Friendly Hospitality</h3><p>Our team is here to make your stay memorable.</p></div></article>
                <article><i class="fas fa-map-marker-alt"></i><div><h3>Prime Location</h3><p>Close to top spots, dining, and transport.</p></div></article>
            </div>
        </div>

        <div class="home-booking-card">
            <p>Plan Your Perfect Stay</p>
            <h2>Ready to lock in<br>your ideal room?</h2>
            <span>Choose your room, amenities, and dining plans in one seamless booking experience.</span>
            <a href="{{ auth('guest')->check() ? route('reservation') : '#guest-auth-modal' }}" class="home-light-btn{{ auth('guest')->check() ? '' : ' js-auth-trigger' }}"{{ auth('guest')->check() ? '' : ' data-auth-trigger' }}>Make Reservation <span aria-hidden="true">-&gt;</span></a>
            <i class="far fa-calendar-alt home-booking-icon" aria-hidden="true"></i>
        </div>
    </section>

    <section class="home-highlights" aria-label="Hotel benefits">
        <article><i class="far fa-thumbs-up"></i><div><strong>Best Rate Guarantee</strong><span>Get the best rates only here.</span></div></article>
        <article><i class="far fa-calendar-check"></i><div><strong>Secure Booking</strong><span>Your data is safe with us.</span></div></article>
        <article><i class="far fa-comments"></i><div><strong>24/7 Support</strong><span>We're here to help anytime.</span></div></article>
        <article><i class="far fa-envelope"></i><div><strong>Flexible Cancellation</strong><span>Plans change, we understand.</span></div></article>
    </section>
</main>

@endsection

