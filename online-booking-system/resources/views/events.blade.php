@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'EVENTS',
    'subtitle' => 'Celebrate birthdays, gatherings, and special moments—made effortless.',
    'cta' => ['href' => '#event-packages', 'label' => 'Explore Packages'],
'backgroundImage' => 'images/Royal-Suite-room.jpg',
])


<section class="offers" id="event-packages">
    <h2>Event Packages</h2>

    <div class="cards">
        <div class="card">
            <img src="{{ asset('images/offer1.jpg') }}" alt="Weddings">
            <h3>Weddings & Renewals</h3>
            <p>Premium venue planning with flexible options for your day.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer2.jpg') }}" alt="Corporate">
            <h3>Corporate Events</h3>
            <p>Work-friendly setups for conferences, meetings, and team activities.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer3.jpg') }}" alt="Private Parties">
            <h3>Private Parties</h3>
            <p>Celebrate in style with curated food and smooth coordination.</p>
        </div>
    </div>
</section>

<section class="recommendation">
    <h2>Plan with Ease</h2>

    <div class="cards">
        <div class="card">
            <h3>Simple Consultation</h3>
            <p>Tell us your vision—our team helps shape the perfect plan.</p>
        </div>
        <div class="card">
            <h3>Friendly Coordination</h3>
            <p>We handle the details so you can enjoy the moment.</p>
        </div>
        <div class="card">
            <h3>Comfort for Guests</h3>
            <p>A space designed to keep everyone relaxed and happy.</p>
        </div>
    </div>
</section>

@endsection

