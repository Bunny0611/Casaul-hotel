@extends('app')

@section('content')

<main class="accommodation-reference-page">
    <section class="accommodation-reference-hero">
        <div class="accommodation-reference-hero-copy">
            <p class="accommodation-eyebrow">Comfort. Elegance. Memories. <span></span></p>
            <h1>Rooms Designed<br>for Your <strong>Comfort</strong></h1>
            <p class="accommodation-hero-text">Relax in our thoughtfully designed rooms<br class="accommodation-desktop-break"> where comfort meets elegance.</p>
            <a class="accommodation-check-button" href="#rooms"><i class="far fa-calendar-alt"></i> Check Availability</a>
        </div>
        <div class="accommodation-reference-hero-image">
            <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="Elegant CASAUL Hotel guest room">
        </div>
    </section>

    <section class="accommodation-rooms-section" id="rooms">
        <header class="accommodation-section-heading">
            <h2>Featured Rooms</h2>
            <div class="accommodation-heading-rule"><span></span><i class="fas fa-bed"></i><span></span></div>
        </header>

        <div class="accommodation-room-grid">
            @forelse($rooms->take(5) as $index => $room)
                <article class="accommodation-room-card">
                    @if($index === 0)<span class="accommodation-room-badge">Best Seller</span>@endif
                    <div class="accommodation-room-image"><img src="{{ $room->image ? asset('images/' . $room->image) : asset('image/Royal-Suite-room.jpg') }}" alt="{{ $room->room_type }}"></div>
                    <div class="accommodation-room-content">
                        <h3>{{ $room->room_type }}</h3>
                        <p class="accommodation-room-price">₱{{ number_format($room->price, 2) }} <span>/ night</span></p>
                        <p class="accommodation-room-description">{{ $room->description ?? 'Comfortable and spacious room.' }}</p>
                        <div class="accommodation-room-meta"><span><i class="fas fa-users"></i> 2 Guests</span><span><i class="fas fa-bed"></i> 1 Bed</span><span><i class="fas fa-wifi"></i> Wi-Fi</span><span><i class="fas fa-snowflake"></i> AC</span></div>
                    </div>
                    <button class="accommodation-room-action accommodation-details-trigger" type="button" data-room-name="{{ $room->room_type }}" data-room-price="₱{{ number_format($room->price, 2) }}" data-room-description="{{ $room->description ?? 'Comfortable and spacious room.' }}" data-room-image="{{ $room->image ? asset('images/' . $room->image) : asset('image/Royal-Suite-room.jpg') }}">View Details</button>
                </article>
            @empty
                @foreach([
                    ['slug' => 'deluxe-room', 'name' => 'Deluxe Room', 'price' => '3,000.00', 'description' => 'Comfortable and spacious room.', 'image' => 'image/Royal-Suite-room.jpg'],
                    ['slug' => 'executive-room', 'name' => 'Deluxe Room', 'price' => '3,500.00', 'description' => 'Comfortable and spacious room.', 'image' => 'image/HM.jpg'],
                    ['slug' => 'presidential-room', 'name' => 'Deluxe Room', 'price' => '4,200.00', 'description' => 'Elegant and relaxing atmosphere.', 'image' => 'image/Royal-Suite-room.jpg'],
                    ['slug' => 'standard-room', 'name' => 'Deluxe Room', 'price' => '4,000.00', 'description' => 'Bright and relaxing atmosphere.', 'image' => 'image/HM.jpg'],
                    ['slug' => 'deluxe-room', 'name' => 'Deluxe Room', 'price' => '3,800.00', 'description' => 'Bright and cozy room.', 'image' => 'image/Royal-Suite-room.jpg'],
                ] as $index => $room)
                    <article class="accommodation-room-card">
                        @if($index === 0)<span class="accommodation-room-badge">Best Seller</span>@endif
                        <div class="accommodation-room-image"><img src="{{ asset($room['image']) }}" alt="{{ $room['name'] }}"></div>
                        <div class="accommodation-room-content">
                            <h3>{{ $room['name'] }}</h3>
                            <p class="accommodation-room-price">₱{{ $room['price'] }} <span>/ night</span></p>
                            <p class="accommodation-room-description">{{ $room['description'] }}</p>
                            <div class="accommodation-room-meta"><span><i class="fas fa-users"></i> 2 Guests</span><span><i class="fas fa-bed"></i> 1 Bed</span><span><i class="fas fa-wifi"></i> Wi-Fi</span><span><i class="fas fa-snowflake"></i> AC</span></div>
                        </div>
                        <button class="accommodation-room-action accommodation-details-trigger" type="button" data-room-name="{{ $room['name'] }}" data-room-price="₱{{ $room['price'] }}" data-room-description="{{ $room['description'] }}" data-room-image="{{ asset($room['image']) }}">View Details</button>
                    </article>
                @endforeach
            @endforelse
        </div>
    </section>

    <section class="accommodation-benefits" aria-label="Why guests love CASAUL Hotel">
        <article><i class="fas fa-crown"></i><div><h3>Premium Amenities</h3><p>Enjoy world-class facilities and thoughtful in-room essentials.</p></div></article>
        <article><i class="fas fa-users"></i><div><h3>Friendly Hospitality</h3><p>We take care of the details so your stay feels effortless.</p></div></article>
        <article><i class="fas fa-map-marker-alt"></i><div><h3>Prime Location</h3><p>Close to dining, attractions, and easy transport access.</p></div></article>
    </section>

    <div class="accommodation-details-modal" id="accommodation-details-modal" aria-hidden="true">
        <div class="accommodation-details-dialog" role="dialog" aria-modal="true" aria-labelledby="accommodation-details-title">
            <button class="accommodation-details-close" type="button" aria-label="Close room details"><i class="fas fa-times"></i></button>
            <img id="accommodation-details-image" src="" alt="">
            <div class="accommodation-details-copy">
                <p class="accommodation-eyebrow">Room Details</p>
                <h2 id="accommodation-details-title"></h2>
                <p class="accommodation-details-price" id="accommodation-details-price"></p>
                <p id="accommodation-details-description"></p>
                <div class="accommodation-room-meta"><span><i class="fas fa-users"></i> 2 Guests</span><span><i class="fas fa-bed"></i> 1 Bed</span><span><i class="fas fa-wifi"></i> Wi-Fi</span><span><i class="fas fa-snowflake"></i> AC</span></div>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.accommodation-details-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('accommodation-details-title').textContent = button.dataset.roomName;
            document.getElementById('accommodation-details-price').textContent = button.dataset.roomPrice + ' / night';
            document.getElementById('accommodation-details-description').textContent = button.dataset.roomDescription;
            document.getElementById('accommodation-details-image').src = button.dataset.roomImage;
            document.getElementById('accommodation-details-image').alt = button.dataset.roomName;
            document.getElementById('accommodation-details-modal').classList.add('is-open');
            document.getElementById('accommodation-details-modal').setAttribute('aria-hidden', 'false');
        });
    });

    function closeAccommodationDetails() {
        document.getElementById('accommodation-details-modal').classList.remove('is-open');
        document.getElementById('accommodation-details-modal').setAttribute('aria-hidden', 'true');
    }

    document.querySelector('.accommodation-details-close').addEventListener('click', closeAccommodationDetails);
    document.getElementById('accommodation-details-modal').addEventListener('click', function (event) {
        if (event.target === this) closeAccommodationDetails();
    });
</script>

@endsection
