@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'CASA HOTEL',
    'subtitle' => 'Experience comfort, elegance, and unforgettable hospitality.',
    'cta' => ['href' => route('accommodation'), 'label' => 'Book Now'],
    'backgroundImage' => 'images/Royal-Suite-room.jpg',
])

<section class="offers">

    <h2>Special Offers</h2>

    <div class="cards">

        <div class="card">

            <img src="{{ asset('images/offer1.jpg') }}">

            <h3>Weekend Escape</h3>

            <p>Save up to 30% on weekends.</p>

        </div>

        <div class="card">

            <img src="{{ asset('images/offer2.jpg') }}">

            <h3>Family Package</h3>

            <p>Kids stay free with complimentary breakfast.</p>

        </div>

        <div class="card">

            <img src="{{ asset('images/offer3.jpg') }}">

            <h3>Romantic Getaway</h3>

            <p>Perfect for couples.</p>

        </div>

    </div>

</section>


<section class="recommendation">

    <h2>Recommended Rooms</h2>

    <div class="cards">

        <div class="card">

            <img src="{{ asset('images/room1.jpg') }}">

            <h3>Deluxe Room</h3>

            <p>₱3,500/night</p>

        </div>

        <div class="card">

            <img src="{{ asset('images/room2.jpg') }}">

            <h3>Executive Suite</h3>

            <p>₱6,500/night</p>

        </div>

        <div class="card">

            <img src="{{ asset('images/room3.jpg') }}">

            <h3>Presidential Suite</h3>

            <p>₱12,000/night</p>

        </div>

    </div>

</section>

@endsection

