@extends('app')

@section('content')

<main class="wedding-events">
    <section class="wedding-hero">
        <div class="wedding-hero-copy">
            <p class="wedding-eyebrow">Let's plan your</p>
            <h1>Beautiful<br><em>Event Day</em></h1>
            <p class="wedding-hero-text">Thoughtfully planned. Warmly hosted. An unforgettable celebration made around you.</p>
            <div class="wedding-hero-actions">
                <a href="#event-packages" class="wedding-button">Explore packages</a>
                <a href="{{ route('reservation') }}" class="wedding-button wedding-button--light">Start planning <span aria-hidden="true"></span></a>
            </div>
        </div>
        <div class="wedding-hero-image">
            <img src="{{ asset('image/HM.jpg') }}" alt="Warmly lit event setting at CASAUL Hotel">
        </div>
    </section>

    <section class="wedding-services" aria-labelledby="services-title">
        <div class="wedding-section-intro">
            <p class="wedding-eyebrow">What we do</p>
            <h2 id="services-title">Our Event Planning Services</h2>
            <p>From the first idea to the final send-off, we handle every detail with care.</p>
        </div>
        <div class="wedding-service-grid">
            <article><i class="fas fa-calendar-check" aria-hidden="true"></i><h3>Full Event Planning</h3><p>End-to-end planning for a stress-free, memorable celebration.</p></article>
            <article><i class="fas fa-spa" aria-hidden="true"></i><h3>Theme &amp; Design</h3><p>Unique themes, beautiful decor, and breathtaking setups.</p></article>
            <article><i class="fas fa-camera" aria-hidden="true"></i><h3>Vendor Management</h3><p>Best vendors, curated for quality and trust.</p></article>
            <article><i class="fas fa-people-roof" aria-hidden="true"></i><h3>Destination Events</h3><p>Magical locations and memorable experiences, wherever you gather.</p></article>
            <article><i class="far fa-calendar-check" aria-hidden="true"></i><h3>On-Day Coordination</h3><p>Relax and enjoy - we'll take care of the rest.</p></article>
        </div>
    </section>

    <section class="wedding-story">
        <div class="wedding-story-image"><img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Elegant CASAUL Hotel space ready for a celebration"></div>
        <div class="wedding-story-copy">
            <p class="wedding-eyebrow">About CASAUL</p>
            <h2>More Than Planners,<br><em>We're Your Event Partners</em></h2>
            <p>Every occasion is unique and deserves to be celebrated. At CASAUL, we blend creativity, thoughtful design, and flawless execution to create celebrations that feel like you.</p>
            <div class="wedding-feature-list"><span><i class="fas fa-heart"></i>Personalized<br>Approach</span><span><i class="fas fa-sun"></i>Creative<br>Designs</span><span><i class="fas fa-handshake"></i>Trusted<br>Vendors</span><span><i class="fas fa-spa"></i>Stress-Free<br>Experience</span></div>
            <a href="{{ route('aboutus') }}" class="wedding-button">Our story</a>
        </div>
    </section>

    <section class="wedding-themes" id="event-packages" aria-labelledby="themes-title">
        <div class="wedding-section-heading"><div><p class="wedding-eyebrow">Event themes</p><h2 id="themes-title">Themes That Speak Your Style</h2><p class="wedding-section-subtitle">From timeless celebrations to modern gatherings, discover a theme that feels like you.</p></div><a href="{{ route('reservation') }}" class="wedding-text-link">View All Themes</a></div>
        <div class="wedding-theme-grid">
            <a href="{{ route('reservation') }}" class="wedding-theme-card"><img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Royal celebration theme"><span>Royal Celebration<small>Grand · Traditional · Timeless</small></span></a>
            <a href="{{ route('reservation') }}" class="wedding-theme-card"><img src="{{ asset('image/HM.jpg') }}" alt="Beach event theme"><span>Beach Gathering<small>Breezy · Relaxed · Serene</small></span></a>
            <a href="{{ route('reservation') }}" class="wedding-theme-card"><img src="{{ asset('image/HM.jpg') }}" alt="Garden party theme"><span>Garden Party<small>Fresh · Elegant · Dreamy</small></span></a>
            <a href="{{ route('reservation') }}" class="wedding-theme-card"><img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Pastel event theme"><span>Pastel Modern<small>Soft · Chic · Modern</small></span></a>
            <a href="{{ route('reservation') }}" class="wedding-theme-card"><img src="{{ asset('image/HM.jpg') }}" alt="Boho event theme"><span>Boho Chic<small>Free · Earthy · Beautiful</small></span></a>
        </div>
    </section>

    <section class="wedding-testimonial">
        <div><p class="wedding-eyebrow">Real stories</p><h2>Beautiful<br><em>Memories</em></h2><p>Nothing makes us happier than seeing guests enjoy their celebrations.</p><a href="{{ route('reservation') }}" class="wedding-button">Read More Stories</a></div>
        <blockquote><span class="wedding-quote-mark">“</span><p>CASAUL made our celebration come true! Everything was so thoughtfully planned, from the decor to the smallest details. We simply enjoyed every moment.</p><cite>- CASAUL Guest<br><small>Celebration Event</small></cite></blockquote>
        <div class="wedding-testimonial-image"><img src="{{ asset('image/HM.jpg') }}" alt="Guests enjoying an event celebration"></div>
    </section>

    <section class="wedding-cta">
        <div><p class="wedding-eyebrow">Your next event</p><h2>Starts Here</h2><p>Let's create a celebration you'll cherish forever.</p></div>
        <a href="{{ route('reservation') }}" class="wedding-button wedding-button--gold">Plan Your Event</a>
    </section>
</main>

@endsection

