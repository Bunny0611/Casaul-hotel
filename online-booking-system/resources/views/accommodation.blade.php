@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'ACCOMMODATION',
    'subtitle' => 'Comfortable stays with premium rooms and unforgettable views.',
    'cta' => ['href' => '#rooms', 'label' => 'Explore Rooms'],
'backgroundImage' => 'images/Royal-Suite-room.jpg',
])


<section class="offers" id="rooms">
    <h2>Featured Rooms</h2>

    <div class="cards">
        <div class="card">
            <img src="{{ asset('images/room1.jpg') }}" alt="Deluxe Room">
            <h3>Deluxe Room</h3>
            <p>₱3,500/night • Cozy, modern, and perfect for couples.</p>
        </div>

        <div class="card">
            <img src="{{ asset('images/room2.jpg') }}" alt="Executive Suite">
            <h3>Executive Suite</h3>
            <p>₱6,500/night • Spacious suite with lounge area.</p>
        </div>

        <div class="card">
            <img src="{{ asset('images/room3.jpg') }}" alt="Presidential Suite">
            <h3>Presidential Suite</h3>
            <p>₱12,000/night • Luxury experience for special occasions.</p>
        </div>
    </div>
</section>

<section class="recommendation">
    <h2>Why Guests Love Us</h2>

    <div class="cards">
        <div class="card">
            <h3>Premium Amenities</h3>
            <p>Fast Wi‑Fi, comfortable bedding, and thoughtful in-room essentials.</p>
        </div>
        <div class="card">
            <h3>Friendly Hospitality</h3>
            <p>We take care of the details so your stay feels effortless.</p>
        </div>
        <div class="card">
            <h3>Prime Location</h3>
            <p>Close to dining, attractions, and easy transport access.</p>
        </div>
    </div>
</section>

@endsection

