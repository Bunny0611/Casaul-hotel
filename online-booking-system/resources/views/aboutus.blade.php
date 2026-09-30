@extends('app')

@section('content')

<section class="about-page" id="about-us">
    <section class="about-hero" aria-label="About Casaul Hotel hero section">
        <div class="about-hero-copy">
            <p class="about-kicker">ABOUT CASAUL HOTEL</p>
            <h1>More Than a Stay,<br><em>It’s a Feeling</em></h1>
            <p>At Casaul Hotel, we believe true hospitality isn’t just about a place to stay — it’s about feeling at home, wherever you are.</p>
        </div>
        <div class="about-hero-image">
            <img src="{{ asset('image/HM.jpg') }}" alt="Luxury hotel bedroom at Casaul Hotel">
        </div>
    </section>

    <div class="about-shell">
        <section class="about-story" aria-label="Our story">
            <div class="about-story-image-wrap">
                <img src="{{ asset('image/HM.jpg') }}" alt="Casaul Hotel exterior at dusk">
            </div>

            <div class="about-story-copy">
                <p class="about-section-kicker">OUR STORY</p>
                <h2>Rooted in Hospitality,<br>Built for You</h2>
                <p>
                    Casaul Hotel was founded with a simple vision — to create a place where comfort, warmth, and genuine Filipino hospitality come together. What started as a dream to provide a better stay for travelers has grown into a hotel that welcomes guests from all walks of life, with the same care and sincerity we would give our own family.
                </p>
                <p>
                    Today, Casaul Hotel continues to be a home away from home, where every stay is more than just a visit — it’s a memorable experience.
                </p>
                <div class="about-story-footer">
                    <span class="about-story-line" aria-hidden="true"></span>
                    <span class="about-story-tag">FILIPINO HOSPITALITY. ALWAYS.</span>
                </div>
            </div>
        </section>

        <section class="about-mission-vision" aria-label="Mission and vision">
            <article class="about-mission">
                <div class="about-icon-wrap" aria-hidden="true">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>OUR MISSION</h3>
                <p>To provide exceptional hospitality through comfort, sincere service, and thoughtful experiences that make every guest feel at home.</p>
            </article>

            <article class="about-vision">
                <div class="about-icon-wrap" aria-hidden="true">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>OUR VISION</h3>
                <p>To become one of the most trusted and admired hotel brands in the region, known for quality, hospitality, and lasting guest relationships.</p>
            </article>
        </section>

        <section class="about-values" aria-label="Our values">
            <p class="about-values-kicker">OUR VALUES</p>
            <h2>What We Stand For</h2>

            <div class="about-values-grid">
                <article class="about-value-item">
                    <div class="about-value-icon" aria-hidden="true"><i class="fas fa-heart"></i></div>
                    <h3>Comfort</h3>
                    <p>Thoughtfully designed spaces<br>for a restful and relaxing stay.</p>
                </article>

                <article class="about-value-item">
                    <div class="about-value-icon" aria-hidden="true"><i class="fas fa-user-friends"></i></div>
                    <h3>Warm Hospitality</h3>
                    <p>A team that treats you<br>like family.</p>
                </article>

                <article class="about-value-item">
                    <div class="about-value-icon" aria-hidden="true"><i class="fas fa-utensils"></i></div>
                    <h3>Quality Dining</h3>
                    <p>Authentic flavors,<br>crafted with care.</p>
                </article>

                <article class="about-value-item">
                    <div class="about-value-icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></div>
                    <h3>Meaningful Events</h3>
                    <p>Beautiful spaces for life’s<br>special moments.</p>
                </article>
            </div>
        </section>
    </div>

        <section class="about-experience" aria-label="Casaul Experience">
            <p class="about-experience-kicker">THE CASAUL EXPERIENCE</p>
            <h2>A Place Made for Every Moment</h2>

            <div class="about-experience-grid">
                <article class="about-experience-item">
                    <div class="about-experience-image-wrap">
                        <img src="{{ asset('image/HM.jpg') }}" alt="Hotel bedroom experience">
                    </div>
                    <div class="about-experience-meta">
                        <span class="about-experience-number">01</span>
                        <strong>STAY</strong>
                    </div>
                    <p>Rest, recharge, and feel at home in thoughtfully designed spaces.</p>
                </article>

                <article class="about-experience-item">
                    <div class="about-experience-image-wrap">
                        <img src="{{ asset('image/HM.jpg') }}" alt="Dining experience at Casaul Hotel">
                    </div>
                    <div class="about-experience-meta">
                        <span class="about-experience-number">02</span>
                        <strong>DINE</strong>
                    </div>
                    <p>Discover comforting flavors and memorable dining experiences.</p>
                </article>

                <article class="about-experience-item">
                    <div class="about-experience-image-wrap">
                        <img src="{{ asset('image/HM.jpg') }}" alt="Celebration or event setup at Casaul Hotel">
                    </div>
                    <div class="about-experience-meta">
                        <span class="about-experience-number">03</span>
                        <strong>CELEBRATE</strong>
                    </div>
                    <p>Make life’s special moments even more meaningful at Casaul.</p>
                </article>
            </div>

            <a class="about-explore-btn" href="{{ route('reservation') }}">EXPLORE CASAUL <span aria-hidden="true">→</span></a>
        </section>

    <section class="about-cta" aria-label="Booking call to action">
        <div class="about-cta-overlay">
            <p class="about-cta-kicker">YOUR HOME AWAY FROM HOME</p>
            <h3>Experience the True Meaning of Hospitality</h3>
            <p>At Casaul Hotel, every stay is a story — and we can’t wait to be part of yours.</p>
            <a href="{{ route('reservation') }}" class="about-cta-button">BOOK YOUR STAY <span aria-hidden="true">→</span></a>
        </div>
    </section>
</section>

@endsection
