@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'GALLERY',
    'subtitle' => 'Explore the ambiance, rooms, and moments that make CASAUL HOTEL special.',
    'cta' => ['href' => '#photo-grid', 'label' => 'View Photos'],
'backgroundImage' => 'image/Royal-Suite-room.jpg',
])


<section class="offers" id="photo-grid">
    <h2>Photo Highlights</h2>

    <div class="cards">
        <div class="card">
            <img src="{{ asset('images/room1.jpg') }}" alt="Room">
            <h3>Deluxe Comfort</h3>
            <p>Warm lighting and cozy design details.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/room2.jpg') }}" alt="Suite">
            <h3>Executive Living</h3>
            <p>Space to relax, work, and unwind.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/room3.jpg') }}" alt="Presidential">
            <h3>Presidential Luxury</h3>
            <p>Premium experience for your best moments.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer1.jpg') }}" alt="Offer">
            <h3>Weekend Escapes</h3>
            <p>Small getaways, big memories.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer2.jpg') }}" alt="Family">
            <h3>Family Time</h3>
            <p>Comfort for everyone.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer3.jpg') }}" alt="Romantic">
            <h3>Romantic Getaways</h3>
            <p>Cherish the moments that matter.</p>
        </div>
    </div>
</section>

@endsection

