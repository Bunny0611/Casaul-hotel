@extends('app')

@section('content')

<main class="dining-page">
    <div class="dining-container">
        <section class="dining-hero">
            <div class="dining-hero-copy">
                <p class="dining-eyebrow">Good food, great memories <span aria-hidden="true">*</span></p>
                <h1>Dining at<br><strong>CASAUL</strong> Hotel</h1>
                <div class="dining-rule"></div>
                <p class="dining-hero-text">We bring people together with  warm hospitality, and memorable dining experiences.</p>
            </div>
            <div class="dining-hero-image">
                <img src="{{ asset('image/HM.jpg') }}" alt="Warm dining atmosphere at CASAUL Hotel">
            </div>
        </section>

        <section class="dining-menu" id="menu">
            <header class="dining-section-heading">
                <div class="dining-heading-mark"><span></span><i class="fas fa-utensils"></i><span></span></div>
                <h2>Chef's Favorites</h2>
                <div class="dining-rule"></div>
            </header>

            <div class="dining-dishes">
                <article class="dining-dish-card">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Signature platter">
                    <div class="dining-dish-copy"><h3>Signature Platter</h3><p>Chef's special mix of seasonal favorites.</p><a href="#dining-experience">Explore Menu</a></div>
                </article>
                <article class="dining-dish-card">
                    <img src="{{ asset('image/HM.jpg') }}" alt="Fresh and bright dish">
                    <div class="dining-dish-copy"><h3>Fresh &amp; Bright</h3><p>Light, flavorful options made with fresh ingredients.</p><a href="#dining-experience">Explore Menu</a></div>
                </article>
                <article class="dining-dish-card">
                    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Sweet dessert finale">
                    <div class="dining-dish-copy"><h3>Sweet Finale</h3><p>End your day with delightful desserts.</p><a href="#dining-experience">Explore Menu</a></div>
                </article>
            </div>
        </section>

        <section class="dining-experience" id="dining-experience">
            <div class="dining-experience-copy">
                <header class="dining-section-heading"><h2>Dining Experience</h2><div class="dining-rule"></div></header>
                <div class="dining-benefits">
                    <article><i class="fas fa-chair"></i><div><h3>Comfortable Seating</h3><p>Relax with a friendly ambiance designed for easy conversation.</p></div></article>
                    <article><i class="fas fa-users"></i><div><h3>Family-Friendly</h3><p>Options for all tastes, perfect for group dining.</p></div></article>
                    <article><i class="fas fa-concierge-bell"></i><div><h3>Evening Atmosphere</h3><p>Enjoy a calm, welcoming vibe after a day of exploring.</p></div></article>
                </div>
                <div class="dining-booking"><i class="far fa-calendar-check"></i><div><strong>Planning a special occasion or group dining?</strong><span>We're here to make it memorable.</span></div><a href="{{ route('reservation') }}">Book a Table</a></div>
            </div>
            <img class="dining-experience-image" src="{{ asset('image/HM.jpg') }}" alt="CASAUL Hotel dining room">
        </section>
    </div>
</main>

@endsection

