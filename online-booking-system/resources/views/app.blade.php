<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casaul Hotel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">

    @vite('resources/js/app.js')

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

<!-- ============ CHATBOT ============ -->

<!-- Chat Toggle Button -->
<button id="chat-toggle" class="chat-toggle" aria-label="Open chat">
    <svg id="chat-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <svg id="chat-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
</button>

<!-- Chat Widget -->
<div id="chat-widget" class="chat-widget">
    <!-- Header -->
    <div class="chat-header">
        <div class="chat-header-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8z"/>
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            </svg>
        </div>
        <div class="chat-header-text">
            <span class="chat-header-title">Virtual Assistant</span>
            <span class="chat-header-status">Online</span>
        </div>
        <button id="chat-close-btn" class="chat-close-btn" aria-label="Close chat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- Messages -->
    <div id="chat-messages" class="chat-messages">
        <div class="chat-msg bot">
            <div class="chat-msg-content">
                <p>Hi! I am a virtual assistant and I can help you book your upcoming stay.</p>
                <p>Please let us know how we can help you today by tapping on the options or by sending us a message.</p>
            </div>
            <span class="chat-msg-time">Just now</span>
        </div>
    </div>

    <!-- Quick Replies -->
    <div id="chat-quick-replies" class="chat-quick-replies">
        <button class="quick-reply" data-action="book">Book a Room</button>
        <button class="quick-reply" data-action="inquiries">Inquiries</button>
        <button class="quick-reply" data-action="availability">Check Availability</button>
        <button class="quick-reply" data-action="offers">Special Offers</button>
        <button class="quick-reply" data-action="contact">Contact Us</button>
    </div>

    <!-- Input Area -->
    <div class="chat-input-area">
        <input type="text" id="chat-input" class="chat-input" placeholder="Type your message..." autocomplete="off">
        <button id="chat-send-btn" class="chat-send-btn" aria-label="Send message">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </div>
</div>

<!-- ============ END CHATBOT ============ -->

</body>
</html>
