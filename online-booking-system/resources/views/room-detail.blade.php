@extends('app')

@section('content')

<section class="room-detail-hero">
    <div class="room-detail-hero-inner">
        <div class="room-detail-copy">
            <p class="room-detail-kicker">Luxury Stay</p>
            <h1>{{ $room['name'] }}</h1>
            <p class="room-detail-price">{{ $room['price'] }} <span>/ night</span></p>
            <p class="room-detail-description">{{ $room['description'] }}</p>
            <div class="room-detail-features">
                @foreach($room['features'] as $feature)
                    <span>{{ $feature }}</span>
                @endforeach
            </div>
            <div class="room-detail-actions">
                <a href="{{ route('accommodation') }}" class="btn room-detail-secondary">Back to Rooms</a>
                <a href="{{ auth()->check() ? route('reservation') : '#guest-auth-modal' }}" class="btn room-detail-primary{{ auth()->check() ? '' : ' js-auth-trigger' }}"{{ auth()->check() ? '' : ' data-auth-trigger' }}>Book This Room</a>
            </div>
        </div>

        <div class="room-detail-visual">
            <img src="{{ asset($room['image']) }}" alt="{{ $room['name'] }}">
        </div>
    </div>
</section>

@endsection
