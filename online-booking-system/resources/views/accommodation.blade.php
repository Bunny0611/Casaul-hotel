@extends('app')

@section('content')

@php
    $displayRooms = [
        ['name' => 'Deluxe Room', 'slug' => 'deluxe-room', 'active' => true],
        ['name' => 'Standard Room', 'slug' => 'standard-room'],
        ['name' => 'Executive Suite', 'slug' => 'executive-suite'],
        ['name' => 'Presidential Suite', 'slug' => 'presidential-suite'],
    ];
@endphp

<section class="accommodation-page animate-on-scroll">
    <div class="accommodation-shell">
        <div class="accommodation-feature">
            <div class="feature-image-wrap">
                <img src="{{ $featuredRooms[0]['image'] ?? asset('image/Royal-Suite-room.jpg') }}" alt="{{ $featuredRooms[0]['name'] ?? 'Deluxe Room' }}">
            </div>
            <div class="feature-caption">
                <div>
                    <span class="feature-tag">{{ $featuredRooms[0]['tag'] ?? 'Featured Stay' }}</span>
                    <h3>{{ $featuredRooms[0]['name'] ?? 'Deluxe Room' }}</h3>
                </div>
                <div class="feature-price">{{ $featuredRooms[0]['price'] ?? '₱3,500' }}<span>/night</span></div>
            </div>
            <p class="feature-description">{{ $featuredRooms[0]['description'] ?? 'A refined stay with thoughtful details and timeless comfort.' }}</p>
        </div>

        <div class="accommodation-listing single-listing">
            <div class="listing-group full-width-listing">
                <h4>Rooms</h4>
                <ul>
                    @foreach($displayRooms as $room)
                        <li class="{{ $room['name'] === 'Deluxe Room' ? 'active' : '' }}">
                            <a href="{{ route('accommodation.room', $room['slug']) }}">{{ $room['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="booking-bar">
        <div class="booking-field">
            <span class="booking-label">Date</span>
            <div class="field-value">Select Date Range</div>
        </div>
        <div class="booking-field">
            <span class="booking-label">Guests</span>
            <div class="field-value">1 Room, 1 Adult, 0 Child</div>
        </div>
        <button type="button" class="booking-btn">BOOK NOW</button>
    </div>
</section>

@endsection

