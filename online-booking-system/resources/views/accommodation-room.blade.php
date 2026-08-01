@extends('app')

@section('content')

<section class="room-detail-page animate-on-scroll">
    @php
        $reverseLayout = in_array($room['slug'], ['standard-room', 'presidential-suite'], true);
        $roomLinks = [
            ['slug' => 'deluxe-room', 'name' => 'Deluxe Room'],
            ['slug' => 'standard-room', 'name' => 'Standard Room'],
            ['slug' => 'executive-suite', 'name' => 'Executive Suite'],
            ['slug' => 'presidential-suite', 'name' => 'Presidential Suite'],
        ];
    @endphp

    <div class="room-detail-card {{ $reverseLayout ? 'reverse' : '' }}">
        <div class="room-detail-image">
            <img src="{{ $room['image'] }}" alt="{{ $room['name'] }}">
        </div>

        <div class="room-detail-content">
            <span class="room-detail-tag">{{ $room['tag'] }}</span>
            <h1>{{ $room['name'] }}</h1>
            <p class="room-detail-intro">{{ $room['intro'] }}</p>
            <p class="room-detail-description">{{ $room['description'] }}</p>

            <div class="room-detail-meta">
                <div class="room-detail-price">
                    <span class="amount">{{ $room['price'] }}</span>
                    <span class="per-night">/night</span>
                </div>
            </div>

            <div class="room-detail-amenities">
                @foreach($room['amenities'] as $amenity)
                    <span>{{ $amenity }}</span>
                @endforeach
            </div>

            <div class="room-detail-actions">
                <a href="{{ route('accommodation') }}" class="btn btn-secondary">Back to Rooms</a>
                <button type="button" class="btn">BOOK NOW</button>
            </div>
        </div>
    </div>

    <div class="room-detail-more">
        <p class="room-detail-more-title">See more rooms</p>
        <div class="room-detail-more-actions">
            @foreach($roomLinks as $roomLink)
                <a href="{{ route('accommodation.room', $roomLink['slug']) }}"
                   class="room-detail-more-btn {{ $room['slug'] === $roomLink['slug'] ? 'active' : '' }}">
                    {{ $roomLink['name'] }}
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
