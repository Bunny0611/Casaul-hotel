@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'CASAUL HOTEL',
    'subtitle' => 'Experience comfort, elegance, and unforgettable hospitality.',
    'cta' => ['href' => route('accommodation'), 'label' => 'Book Now'],
'backgroundImage' => 'image/HM.jpg',
])

<section class="offers">

    <h2>Special Offers</h2>

    <div class="cards">

        <div class="card">

            <img src="{{ asset('images/Royal-Suite-room.jpg') }}">

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


<section class="recommendation animate-on-scroll">

    <h2>Recommended Rooms</h2>

    <div class="cards">

        @forelse($rooms as $room)
        <div class="card">

            <img src="{{ $room->image ? asset('images/' . $room->image) : asset('images/room1.jpg') }}" alt="{{ $room->room_type }}">

            <h3>{{ $room->room_type }}</h3>

            <p>₱{{ number_format($room->price, 2) }}/night</p>

        </div>
        @empty
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
        @endforelse

    </div>

</section>

@endsection

