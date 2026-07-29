@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'OFFERS',
    'subtitle' => 'Save more with limited-time packages and special guest perks.',
    'cta' => ['href' => '#offers', 'label' => 'See Deals'],
'backgroundImage' => 'image/Royal-Suite-room.jpg',
])


<section class="offers" id="offers">
    <h2>Current Promotions</h2>

    <div class="cards">
        <div class="card">
            <img src="{{ asset('images/offer1.jpg') }}" alt="Weekend Escape">
            <h3>Weekend Escape</h3>
            <p>Save up to 30% on weekend bookings. Perfect for a quick getaway.</p>
        </div>

        <div class="card">
            <img src="{{ asset('images/offer2.jpg') }}" alt="Family Package">
            <h3>Family Package</h3>
            <p>Kids stay free with complimentary breakfast. Family-friendly comfort.</p>
        </div>

        <div class="card">
            <img src="{{ asset('images/offer3.jpg') }}" alt="Romantic Getaway">
            <h3>Romantic Getaway</h3>
            <p>Couples package with special touches for a memorable stay.</p>
        </div>
    </div>
</section>

<section class="recommendation">
    <h2>How to Book</h2>

    <div class="cards">
        <div class="card">
            <h3>1) Choose your offer</h3>
            <p>Pick the promotion that matches your travel plan.</p>
        </div>
        <div class="card">
            <h3>2) Select your room</h3>
            <p>Enjoy your selected room with offer benefits applied.</p>
        </div>
        <div class="card">
            <h3>3) Confirm & relax</h3>
            <p>We’ll take care of the details—so you can focus on your trip.</p>
        </div>
    </div>
</section>

@endsection

