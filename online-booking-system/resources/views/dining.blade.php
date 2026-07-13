@extends('app')

@section('content')

@include('partials.section-hero', [
    'title' => 'DINING',
    'subtitle' => 'Delicious cuisine served with a warm, welcoming atmosphere.',
    'cta' => ['href' => '#menu', 'label' => 'View Menu'],
'backgroundImage' => 'images/Royal-Suite-room.jpg',
])


<section class="offers" id="menu">
    <h2>Chef’s Favorites</h2>

    <div class="cards">
        <div class="card">
            <img src="{{ asset('images/offer1.jpg') }}" alt="Signature Dish">
            <h3>Signature Platter</h3>
            <p>Chef’s special mix of seasonal favorites.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer2.jpg') }}" alt="Healthy Choice">
            <h3>Fresh & Bright</h3>
            <p>Light, flavorful options made with fresh ingredients.</p>
        </div>
        <div class="card">
            <img src="{{ asset('images/offer3.jpg') }}" alt="Dessert">
            <h3>Sweet Finale</h3>
            <p>End your day with delightful desserts.</p>
        </div>
    </div>
</section>

<section class="recommendation">
    <h2>Dining Experience</h2>

    <div class="cards">
        <div class="card">
            <h3>Comfortable Seating</h3>
            <p>Relax with a friendly ambiance designed for easy conversation.</p>
        </div>
        <div class="card">
            <h3>Family-Friendly</h3>
            <p>Options for all tastes—perfect for group dining.</p>
        </div>
        <div class="card">
            <h3>Evening Atmosphere</h3>
            <p>Enjoy a calm, welcoming vibe after a day of exploring.</p>
        </div>
    </div>
</section>

@endsection

