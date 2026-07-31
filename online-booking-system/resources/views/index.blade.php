    @extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'CASAUL HOTEL',
    'subtitle' => 'Experience comfort, elegance, and unforgettable hospitality.',
    'cta' => ['href' => route('accommodation'), 'label' => 'Book Now'],
'backgroundImage' => 'image/HM.jpg',
])

<section class="offers animate-on-scroll">



    <h2>Special Offers</h2>

    <div class="cards">

        <div class="card">

            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Weekend Escape">

            <h3>Weekend Escape</h3>

            <p>Save up to 30% on weekends.</p>

        </div>

        <div class="card">

            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Family Package">

            <h3>Family Package</h3>

            <p>Kids stay free with complimentary breakfast.</p>

        </div>

        <div class="card">

            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Romantic Getaway">

            <h3>Romantic Getaway</h3>

            <p>Perfect for couples.</p>

        </div>

    </div>

</section>


<section class="recommendation accommodation animate-on-scroll">

    <h2>Premier Room with Private Garden</h2>

    <div class="accommodation-card">
        <div class="accommodation-image">
            <span class="accommodation-image-badge">Featured</span>
            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Premier Room with Private Garden">
        </div>
        <div class="accommodation-content">
            <div class="accommodation-content-inner">
                <p class="accommodation-meta">
                    <i class="fas fa-ruler-combined"></i>
                    81 square metres &nbsp;|&nbsp; 1 King Bed or 2 Twin Beds
                </p>
                <p>Discover the spacious comfort of this light-filled guest room. Relax on the <strong>plush sofa</strong> or unwind in your <strong>sculptural bathtub</strong>. Floor-to-ceiling windows lead out to a large private terrace and landscaped semiprivate garden with sun loungers.</p>
                <div class="accommodation-amenities">
                    <span><i class="fas fa-wifi"></i> Free Wi-Fi</span>
                    <span><i class="fas fa-tv"></i> Smart TV</span>
                    <span><i class="fas fa-snowflake"></i> A/C</span>
                    <span><i class="fas fa-coffee"></i> Breakfast</span>
                </div>
                <div class="accommodation-price">
                    <span class="amount">₱5,999</span>
                    <span class="per-night">/ night</span>
                </div>
            </div>
            <a href="{{ route('accommodation') }}" class="btn">Book Now <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

</section>


<section class="offers accommodation animate-on-scroll">

    <h2>Available Rooms</h2>

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

