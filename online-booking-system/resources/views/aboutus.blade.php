@extends('app')

@section('content')

<section class="about-page" id="about-us">
    <div class="about-container">
        <header class="about-heading">
            <div class="about-ornament" aria-hidden="true"><span></span><i class="fas fa-spa"></i><span></span></div>
            <p class="about-kicker">Welcome to CASAUL</p>
            <h1>About <strong>CASAUL</strong> Hotel</h1>
            <p class="about-lead">Where comfort meets genuine hospitality.</p>
        </header>

        <div class="about-feature-image">
            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="A refined guest room at CASAUL Hotel">
        </div>

        <div class="about-purpose-grid">
            <article class="about-card about-story-card">
                <div class="about-card-icon"><i class="fas fa-book-open"></i></div>
                <div>
                    <h2>Our Story</h2>
                    <div class="about-rule"></div>
                    <p>CASAUL Hotel is a proud hospitality destination rooted in Filipino warmth and service excellence. From our first welcome to your last goodbye, we aim to make every stay memorable and meaningful.</p>
                    <p>We continually evolve to meet the needs of modern travelers while staying true to our values of comfort, care, and community.</p>
                </div>
            </article>

            <article class="about-card about-purpose-card mission-card">
                <div class="about-card-icon"><i class="fas fa-bullseye"></i></div>
                <div>
                    <h2>Mission</h2>
                    <div class="about-rule"></div>
                    <p>To provide exceptional hospitality through comfort, sincere service, and thoughtful experiences that make every guest feel at home.</p>
                </div>
            </article>

            <article class="about-card about-purpose-card vision-card">
                <div class="about-card-icon"><i class="fas fa-eye"></i></div>
                <div>
                    <h2>Vision</h2>
                    <div class="about-rule"></div>
                    <p>To become one of the most trusted and admired hotel brands in the region, known for quality, hospitality, and lasting guest relationships.</p>
                </div>
            </article>
        </div>

        <div class="about-values-grid">
            <article class="about-value-card"><div class="about-value-icon"><i class="fas fa-bed"></i></div><div><h3>Comfort</h3><div class="about-rule"></div><p>Thoughtfully designed rooms and modern amenities for a relaxing stay.</p></div></article>
            <article class="about-value-card"><div class="about-value-icon"><i class="fas fa-concierge-bell"></i></div><div><h3>Hospitality</h3><div class="about-rule"></div><p>A team committed to being attentive, warm, and genuinely helpful.</p></div></article>
            <article class="about-value-card"><div class="about-value-icon"><i class="fas fa-users"></i></div><div><h3>Community</h3><div class="about-rule"></div><p>We celebrate local culture and support the community we proudly serve.</p></div></article>
        </div>
    </div>
</section>

@endsection
