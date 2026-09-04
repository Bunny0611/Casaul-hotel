    @extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'CASAUL HOTEL',
    'subtitle' => 'Experience comfort, elegance, and unforgettable hospitality.',
    'cta' => ['href' => route('reservation'), 'label' => 'Book Now', 'requiresGuestAuth' => true],
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
            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Premier Room with Private Garden">
        </div>
        <div class="accommodation-content">
            <p class="accommodation-meta">81 square metres | 1 King Bed or 2 Twin Beds</p>
            <p>Discover the spacious comfort of this light-filled guest room. Relax on the plush sofa or unwind in your sculptural bathtub. Floor-to-ceiling windows lead out to a large private terrace and landscaped semiprivate garden with sun loungers.</p>
            <a href="{{ route('accommodation') }}" class="btn">Book Now</a>
        </div>
    </div>

</section>


</div>

</section>

<section class="offers accommodation animate-on-scroll">

    <h2>Featured Rooms</h2>

    <div class="featured-room-grid">
        @foreach($rooms as $room)
            <a href="{{ route('accommodation.room', ['slug' => $room['slug']]) }}" class="featured-room-card">
                <div class="featured-room-image-wrap">
                    <img src="{{ asset($room['image']) }}" alt="{{ $room['name'] }}">
                    <span class="featured-room-price">{{ $room['price'] }}/night</span>
                </div>
                <div class="featured-room-body">
                    <h3>{{ $room['name'] }}</h3>
                    <p>{{ $room['tagline'] }}</p>
                </div>
            </a>
        @endforeach
    </div>

</section>

@endsection

