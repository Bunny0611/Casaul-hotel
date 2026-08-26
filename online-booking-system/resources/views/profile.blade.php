@extends('app')

@section('content')

<div class="profile-page">
    <section class="profile-hero">
        <p class="eyebrow">My Account</p>
        <h1>Your Profile</h1>
        <p>Manage your personal details and keep track of your reservations.</p>
    </section>

    @if(session('success'))
        <div class="reservation-alert reservation-alert--success">
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-shell">
        <div class="profile-card">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info">
                <h2>{{ auth('guest')->user()->name }}</h2>
                <p class="profile-meta"><i class="fas fa-envelope"></i> {{ auth('guest')->user()->email }}</p>
                @if(auth('guest')->user()->contact_no)
                    <p class="profile-meta"><i class="fas fa-phone"></i> {{ auth('guest')->user()->contact_no }}</p>
                @endif
                <p class="profile-meta"><i class="fas fa-id-badge"></i> Guest Account</p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="{{ route('guest.records') }}" class="profile-action-btn">
                <i class="fas fa-list-alt"></i>
                <span>View Records</span>
                <small>See your reservation history</small>
            </a>
            <a href="{{ route('reservation') }}" class="profile-action-btn">
                <i class="fas fa-calendar-plus"></i>
                <span>Make a Reservation</span>
                <small>Book a new stay</small>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="profile-logout-form">
                @csrf
                <button type="submit" class="profile-action-btn profile-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                    <small>Sign out of your account</small>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
