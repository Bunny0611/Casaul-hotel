<button id="chat-toggle" class="chat-toggle" aria-label="Open chat">
    <svg id="chat-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
    <svg id="chat-icon-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
</button>


<div id="chat-widget" class="chat-widget">
   
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

   
    <div id="chat-messages" class="chat-messages">
        <div class="chat-msg bot">
            <div class="chat-msg-content">
                <p>Let me check room availability for you! 📅</p>
                <p>Share your desired dates, number of guests, and preferred room type, or tap below to view our available rooms.</p>
            </div>
            <span class="chat-msg-time">Just now</span>
        </div>
    </div>

  
    <div id="chat-quick-replies" class="chat-quick-replies">
        <button class="quick-reply" data-action="book">Book a Room</button>
        <button class="quick-reply" data-action="inquiries">Inquiries</button>
        <button class="quick-reply" data-action="availability">Show Available Rooms</button>
        <button class="quick-reply" data-action="offers">Special Offers</button>
        <button class="quick-reply" data-action="contact">Contact Us</button>
    </div>

  
    <div class="chat-input-area">
        <form id="message-form" action="{{ route('chatbot.message') }}" method="POST" data-chatbot-endpoint="{{ route('chatbot.message') }}">
            @csrf
            <input type="hidden" name="name" id="customer-name" value="Guest">
            <input type="hidden" name="email" id="customer-email" value="guest@example.com">
            <input type="text" id="chat-input" name="message" class="chat-input" placeholder="Type your message..." autocomplete="off" required>
            <button type="submit" id="chat-send-btn" class="chat-send-btn" aria-label="Send message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </form>
    </div>
</div>
