@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'ACCOMMODATION',
    'subtitle' => 'Comfortable stays with premium rooms and unforgettable views.',
    'cta' => ['href' => '#rooms', 'label' => 'Explore Rooms'],
'backgroundImage' => 'image/Royal-Suite-room.jpg',
])

<section class="offers animate-on-scroll" id="rooms">
    <h2>Featured Rooms</h2>

    <div class="cards">
        @forelse($rooms as $room)
        <div class="card">
            <img src="{{ $room->image ? asset('images/' . $room->image) : asset('images/room1.jpg') }}" alt="{{ $room->room_type }}">
            <h3>{{ $room->room_type }}</h3>
            <p>₱{{ number_format($room->price, 2) }}/night • {{ $room->description ?? 'Comfortable and spacious room.' }}</p>
        </div>
        @empty
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
        @endforelse
    </div>
</section>

<section class="recommendation animate-on-scroll">
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

<section class="reservation-highlight animate-on-scroll">
    <div class="reservation-highlight-card" style="background: rgba(255,255,255,0.96); border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 24px 45px rgba(0,0,0,0.12); border-radius: 28px; padding: 2rem; max-width: 960px; margin: 0 auto;">
        <div class="reservation-highlight-copy" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <span class="eyebrow">Plan the Perfect Stay</span>
            <h2 style="margin: 0; font-size: clamp(1.75rem, 2.4vw, 2.6rem);">Ready to lock in your ideal room?</h2>
            <p style="margin: 0; color: #4f4f4f; line-height: 1.7;">Choose your room, amenities, event package, or dining plan in one seamless booking experience.</p>
        </div>
        <div style="margin-top: 1.75rem; display: flex; justify-content: flex-start;">
            <a href="{{ auth('guest')->check() ? route('reservation') : '#guest-auth-modal' }}" class="btn{{ auth('guest')->check() ? '' : ' js-auth-trigger' }}"{{ auth('guest')->check() ? '' : ' data-auth-trigger' }} style="padding: 0.95rem 1.8rem; border-radius: 999px; background: #3f51b5; color: #fff; text-decoration: none; box-shadow: 0 14px 30px rgba(63,81,181,0.18); transition: transform 0.2s ease;">
                <span>Make Reservation</span>
            </a>
        </div>
    </div>
</section>
 
@endsection

