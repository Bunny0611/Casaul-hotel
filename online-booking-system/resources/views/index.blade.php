@extends('app')

@section('content')

<section class="home-page-shell">
    <section class="home-hero-panel">
        <div class="home-hero-layout">
            <div class="home-hero-copy">
                <h1>Embrace Luxury</h1>
                <p>Discover a refined stay where comfort meets elegance. Your perfect getaway begins here.</p>

                <div class="home-hero-actions">
                    <a href="{{ route('reservation') }}" class="home-primary-btn">Book your stay</a>
                    <a href="{{ route('accommodation') }}" class="home-secondary-btn">Explore rooms</a>
                </div>
            </div>

            <div class="home-hero-visual">
                <img src="{{ asset('image/HM.jpg') }}" alt="Casaul Hotel building and guest photo">
            </div>
        </div>

        <div class="booking-strip">
            <div class="booking-field">
                <span class="booking-label"><i class="fa-regular fa-calendar"></i> Check-In</span>
                <span class="booking-value">Select Date</span>
            </div>
            <div class="booking-field">
                <span class="booking-label"><i class="fa-regular fa-calendar"></i> Check-Out</span>
                <span class="booking-value">Select Date</span>
            </div>
            <div class="booking-field">
                <span class="booking-label"><i class="fa-solid fa-user-group"></i> Guests &amp; Rooms</span>
                <span class="booking-value">2 Guests, 1 Room</span>
            </div>
            <button type="button" class="booking-button">Check Availability</button>
        </div>
    </section>

    <section class="home-rooms-section">
        <div class="section-heading-row">
            <span class="section-tag">Featured Rooms</span>
            <h2>Find Your Perfect Room</h2>
            <p>Elegant spaces designed for your comfort. Choose the perfect room for your stay.</p>
        </div>

        <div class="room-grid">
            <article class="room-card">
                <div class="room-card-image">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Deluxe Room">
                    <span class="room-price">₱3,500/night</span>
                </div>
                <div class="room-card-info">
                    <div class="room-specs">
                        <span><i class="fa-solid fa-user-group"></i> 2 Guests</span>
                        <span><i class="fa-solid fa-ruler-combined"></i> 32 sqm</span>
                        <span><i class="fa-solid fa-wifi"></i> Wi‑Fi</span>
                    </div>
                    <h3>Deluxe Room</h3>
                    <p>Elegant comfort for a restful getaway.</p>
                </div>
            </article>

            <article class="room-card">
                <div class="room-card-image">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Executive Room">
                    <span class="room-price">₱6,500/night</span>
                </div>
                <div class="room-card-info">
                    <div class="room-specs">
                        <span><i class="fa-solid fa-user-group"></i> 2 Guests</span>
                        <span><i class="fa-solid fa-ruler-combined"></i> 40 sqm</span>
                        <span><i class="fa-solid fa-wifi"></i> Wi‑Fi</span>
                    </div>
                    <h3>Executive Room</h3>
                    <p>Sophisticated luxury for work and leisure.</p>
                </div>
            </article>

            <article class="room-card">
                <div class="room-card-image">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Presidential Room">
                    <span class="room-price">₱12,000/night</span>
                </div>
                <div class="room-card-info">
                    <div class="room-specs">
                        <span><i class="fa-solid fa-user-group"></i> 2 Guests</span>
                        <span><i class="fa-solid fa-ruler-combined"></i> 60 sqm</span>
                        <span><i class="fa-solid fa-wifi"></i> Wi‑Fi</span>
                    </div>
                    <h3>Presidential Room</h3>
                    <p>A grand stay with a sense of occasion.</p>
                </div>
            </article>

            <article class="room-card">
                <div class="room-card-image">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Standard Room">
                    <span class="room-price">₱2,500/night</span>
                </div>
                <div class="room-card-info">
                    <div class="room-specs">
                        <span><i class="fa-solid fa-user-group"></i> 2 Guests</span>
                        <span><i class="fa-solid fa-ruler-combined"></i> 20 sqm</span>
                        <span><i class="fa-solid fa-wifi"></i> Wi‑Fi</span>
                    </div>
                    <h3>Standard Room</h3>
                    <p>Simple comfort with a polished finish.</p>
                </div>
            </article>
        </div>

        <div class="section-link-row">
            <a href="{{ route('accommodation') }}" class="section-link-btn">View all rooms</a>
        </div>
    </section>

    <section class="offers-section">
        <div class="section-heading-row left-offset">
            <span class="section-tag">Special Offers</span>
            <h2>Exclusive Offers for an Unforgettable Stay</h2>
            <p>Enjoy limited-time offers and packages made for every occasion.</p>
        </div>

        <div class="offer-grid">
            <article class="offer-card offer-card-one">
                <div class="offer-card-content">
                    <span>Weekend Escape</span>
                    <h3>Save up to 30% on weekends</h3>
                    <a href="{{ route('offers') }}" class="offer-button">Book now</a>
                </div>
            </article>

            <article class="offer-card offer-card-two">
                <div class="offer-card-content">
                    <span>Family Package</span>
                    <h3>Kids stay free with breakfast</h3>
                    <a href="{{ route('offers') }}" class="offer-button">Book now</a>
                </div>
            </article>

            <article class="offer-card offer-card-three">
                <div class="offer-card-content">
                    <span>Romantic Getaway</span>
                    <h3>Perfect for couples</h3>
                    <a href="{{ route('offers') }}" class="offer-button">Book now</a>
                </div>
            </article>
        </div>
    </section>

    <section class="benefits-row">
        <div class="benefit-item">
            <div class="benefit-icon"><i class="fa-solid fa-shield-heart"></i></div>
            <div>
                <h4>Best Rate Guarantee</h4>
                <p>Get the best rates when you book direct.</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div>
                <h4>Flexible Booking</h4>
                <p>Easy changes and cancellations.</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon"><i class="fa-solid fa-gift"></i></div>
            <div>
                <h4>Exclusive Deals</h4>
                <p>Access special offers and perks.</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon"><i class="fa-solid fa-headset"></i></div>
            <div>
                <h4>24/7 Support</h4>
                <p>We are here to help anytime.</p>
            </div>
        </div>
    </section>
</section>

@endsection

