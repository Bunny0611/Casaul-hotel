<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>Casaul Hotel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">

</head>
<body>

<nav>

    <div class="logo">
        CASAUL HOTEL
    </div>

    <ul>

        <li><a href="{{ route('home') }}">HOME</a></li>

        <li><a href="{{ route('accommodation') }}">ACCOMMODATION</a></li>

        <li><a href="{{ route('offers') }}">OFFERS</a></li>

        <li><a href="{{ route('gallery') }}">GALLERY</a></li>

        <li><a href="{{ route('dining') }}">DINING</a></li>

        <li><a href="{{ route('events') }}">EVENTS</a></li>

    </ul>

</nav>

@yield('content')

<footer>

    <h3>Casaul Hotel</h3>

    <p>Luxury • Comfort • Hospitality</p>

</footer>

</body>
</html>