import './bootstrap';



document.addEventListener('DOMContentLoaded', function () {
    
   
    const pageLoader = document.getElementById('page-loader');
    if (pageLoader) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                pageLoader.classList.add('hidden');
            }, 800);
        });
    }
    
 
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

   
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

  
    document.querySelectorAll('.offers h2, .recommendation h2').forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });

  
    const heroMedia = document.querySelector('.hero .media');
    const heroImage = document.querySelector('.hero .bg-image, .hero .bg-video');
    
    if (heroMedia && heroImage) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * 0.3;
            
            if (scrolled < window.innerHeight) {
                heroImage.style.transform = `scale(1.1) translateY(${rate}px)`;
            }
        });
    }

    
    document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });


    const nav = document.querySelector('nav');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            nav.style.boxShadow = '0 8px 32px rgba(30, 58, 95, 0.25)';
        } else {
            nav.style.boxShadow = '0 4px 20px rgba(30, 58, 95, 0.15)';
        }

        lastScroll = currentScroll;
    });

   
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            btn.style.setProperty('--x', `${x}px`);
            btn.style.setProperty('--y', `${y}px`);
        });
    });

    const searchToggle = document.getElementById('nav-search-toggle');
    const searchForm = document.getElementById('nav-search-form');
    const searchInput = document.getElementById('nav-search-input');
    const modalTrigger = document.getElementById('guest-signin-trigger');
    const authModal = document.getElementById('guest-auth-modal');
    const closeModalBtn = document.getElementById('guest-auth-close');
    const signInView = document.getElementById('auth-signin-view');
    const signUpView = document.getElementById('auth-signup-view');
    const switchBtn = document.getElementById('auth-switch-btn');
    const switchLabel = document.getElementById('auth-switch-label');
    const googleBtn = document.getElementById('google-signin-btn');
    const authMessage = document.getElementById('auth-message');
    const signupForm = document.getElementById('guest-signup-form');
    const profileDropdown = document.getElementById('profile-dropdown');
    const profileTrigger = document.getElementById('profile-trigger');
    const profileMenu = document.getElementById('profile-menu');

    if (profileDropdown && profileTrigger && profileMenu) {
        const closeProfileMenu = () => {
            profileMenu.classList.remove('open');
            profileTrigger.classList.remove('open');
            profileTrigger.setAttribute('aria-expanded', 'false');
        };

        const openProfileMenu = (event) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            if (!profileMenu.classList.contains('open')) {
                profileMenu.classList.add('open');
                profileTrigger.classList.add('open');
                profileTrigger.setAttribute('aria-expanded', 'true');
            }
        };

        profileTrigger.addEventListener('click', openProfileMenu);
        profileTrigger.addEventListener('pointerdown', (event) => {
            event.preventDefault();
            event.stopPropagation();
        });

        profileMenu.addEventListener('pointerdown', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('pointerdown', (event) => {
            if (!profileDropdown.contains(event.target)) {
                closeProfileMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeProfileMenu();
            }
        });
    }

    if (searchToggle && searchForm && searchInput) {
        searchToggle.addEventListener('click', () => {
            searchForm.classList.toggle('active');
            if (searchForm.classList.contains('active')) {
                searchInput.focus();
            }
        });

        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const query = searchInput.value.trim().toLowerCase();

            if (!query) {
                searchInput.focus();
                return;
            }

            const candidates = Array.from(document.querySelectorAll('main h1, main h2, main h3, main h4, main p, main li, main a, main span, main button, .card, .section, .room-card'));
            const match = candidates.find((element) => {
                const text = element.textContent?.trim() || '';
                return text.length > 0 && text.toLowerCase().includes(query) && !element.closest('nav') && !element.closest('footer') && !element.closest('.chat-widget') && !element.closest('.page-loader');
            });

            if (match) {
                match.scrollIntoView({ behavior: 'smooth', block: 'center' });
                match.classList.add('search-highlight');
                setTimeout(() => match.classList.remove('search-highlight'), 2200);
                searchForm.classList.remove('active');
            } else {
                alert('No matching content found on this page.');
            }
        });

        document.addEventListener('click', (event) => {
            if (!searchForm.contains(event.target) && !searchToggle.contains(event.target)) {
                searchForm.classList.remove('active');
            }
        });
    }

    if (modalTrigger && authModal) {
        const openModal = () => {
            authModal.classList.add('open');
            authModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            authModal.classList.remove('open');
            authModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        modalTrigger.addEventListener('click', openModal);
        closeModalBtn?.addEventListener('click', closeModal);
        authModal.addEventListener('click', (event) => {
            if (event.target === authModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        let isSignUp = signUpView?.classList.contains('auth-hidden') === false;
        switchBtn?.addEventListener('click', () => {
            isSignUp = !isSignUp;
            signInView?.classList.toggle('auth-hidden', isSignUp);
            signUpView?.classList.toggle('auth-hidden', !isSignUp);
            switchLabel.textContent = isSignUp ? 'Already have an account?' : 'Don’t have an account?';
            switchBtn.textContent = isSignUp ? 'Sign in' : 'Sign up';
        });

        googleBtn?.addEventListener('click', () => {
            if (authMessage) {
                authMessage.style.display = 'block';
                authMessage.textContent = 'Google sign-in is coming soon. Please use email sign-in for now.';
            }
        });

        signupForm?.addEventListener('submit', () => {
            if (authMessage) {
                authMessage.style.display = 'none';
                authMessage.textContent = '';
            }
        });
    }

    console.log('✨ Dynamic animations loaded!');
});



document.addEventListener('DOMContentLoaded', function () {

   
    const toggleBtn   = document.getElementById('chat-toggle');
    const closeBtn    = document.getElementById('chat-close-btn');
    const widget      = document.getElementById('chat-widget');
    const messagesEl  = document.getElementById('chat-messages');
    const inputEl     = document.getElementById('chat-input');
    const sendBtn     = document.getElementById('chat-send-btn');
    const quickBtns   = document.querySelectorAll('.quick-reply');
    const iconOpen    = document.getElementById('chat-icon-open');
    const iconClose   = document.getElementById('chat-icon-close');

    let isOpen = false;

 
    const botResponses = {
        book: {
            text: 'I\'d be happy to help you book a room! 🏨\n\nCould you please tell me:\n• Your preferred check-in and check-out dates\n• Number of guests\n• Room type preference (Deluxe, Executive, or Presidential Suite)',
            quick: ['Check Availability', 'View Rooms', 'Talk to Agent']
        },
        inquiries: {
            text: 'I\'m here to answer any questions! 💬\n\nCommon topics:\n• 🕐 Check-in / Check-out times\n• 🅿️ Parking & transport\n• 🐾 Pet policy\n• 🍳 Breakfast & dining hours\n• 🧺 Room amenities\n\nWhat would you like to know more about?',
            quick: ['Check-in/out Times', 'Amenities', 'Pet Policy', 'Dining']
        },
        availability: {
            text: 'Let me check room availability for you! 📅\n\nTo get started, please share:\n• Your desired dates\n• Number of guests\n• Preferred room type\n\nOr browse our available rooms directly below.',
            quick: ['View All Rooms', 'Special Offers', 'Call Us']
        },
        offers: {
            text: 'Check out our current special offers! 🎉\n\n• 🌙 <b>Weekend Escape</b> — Save up to 30% on weekend bookings.\n• 👨‍👩‍👧‍👦 <b>Family Package</b> — Kids stay free with complimentary breakfast.\n• ❤️ <b>Romantic Getaway</b> — Special touches for couples.\n\nTap an offer below to learn more or book now!',
            quick: ['Weekend Escape', 'Family Package', 'Romantic Getaway', 'Book Now']
        },
        contact: {
            text: 'You can reach us through any of these channels: 📞\n\n📍 <b>Address:</b> Casaul Hotel, Main Street\n📞 <b>Phone:</b> +63 (2) 1234 5678\n📧 <b>Email:</b> reservations@casaulhotel.com\n🌐 <b>Website:</b> www.casaulhotel.com\n\nOur front desk is available 24/7 for any urgent assistance!',
            quick: ['Call Now', 'Send Email', 'View Location', 'Book a Room']
        },
        fallback: {
            text: 'Thank you for your message! 🙏\n\nFor quick assistance, please choose one of the options below, or give us a call at <b>+63 (2) 1234 5678</b>.\n\nHow else can I help you today?',
            quick: ['Book a Room', 'Inquiries', 'Check Availability', 'Special Offers', 'Contact Us']
        }
    };

   
    function scrollToBottom() {
        requestAnimationFrame(() => {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        });
    }

    function formatTime() {
        const now = new Date();
        const h = now.getHours().toString().padStart(2, '0');
        const m = now.getMinutes().toString().padStart(2, '0');
        return `${h}:${m}`;
    }

    function addMessage(text, type, keepQuick = false) {
    
        const typing = messagesEl.querySelector('.typing-indicator');
        if (typing) typing.remove();

        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${type}`;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'chat-msg-content';
        contentDiv.innerHTML = text.replace(/\n/g, '<br>');

        const timeSpan = document.createElement('span');
        timeSpan.className = 'chat-msg-time';
        timeSpan.textContent = formatTime();

        msgDiv.appendChild(contentDiv);
        msgDiv.appendChild(timeSpan);
        messagesEl.appendChild(msgDiv);
        scrollToBottom();

      
        const quickRepliesEl = document.getElementById('chat-quick-replies');
        if (type === 'user') {
            quickRepliesEl.style.display = 'none';
        } else if (type === 'bot' && keepQuick) {
            quickRepliesEl.style.display = 'flex';
        } else {
            quickRepliesEl.style.display = 'none';
        }
    }

    function showTypingIndicator() {
        const typing = document.createElement('div');
        typing.className = 'typing-indicator';
        typing.innerHTML = '<span></span><span></span><span></span>';
        messagesEl.appendChild(typing);
        scrollToBottom();
        return typing;
    }

    function updateQuickReplies(buttons) {
        const container = document.getElementById('chat-quick-replies');
        container.innerHTML = '';
        container.style.display = 'flex';

        if (!buttons || buttons.length === 0) {
            container.style.display = 'none';
            return;
        }

        buttons.forEach(label => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply';
            btn.textContent = label;
            btn.dataset.action = label.toLowerCase().replace(/[^a-z0-9]+/g, '-');
            btn.addEventListener('click', () => handleQuickReply(label, btn.dataset.action));
            container.appendChild(btn);
        });
    }

    function botReply(responseKey) {
        const response = botResponses[responseKey] || botResponses.fallback;
        const typingEl = showTypingIndicator();

       
        const delay = 800 + Math.random() * 700;

        setTimeout(() => {
            typingEl.remove();
            addMessage(response.text, 'bot', true);
            updateQuickReplies(response.quick || botResponses.fallback.quick);
        }, delay);
    }

    function sendUserMessage(text) {
        if (!text || text.trim() === '') return;
        const message = text.trim();
        addMessage(message, 'user');
        inputEl.value = '';

        const lower = message.toLowerCase();
        let action = 'fallback';

        if (lower.includes('book') || lower.includes('room') || lower.includes('reservation')) {
            action = 'book';
        } else if (lower.includes('inquiry') || lower.includes('question') || lower.includes('info')) {
            action = 'inquiries';
        } else if (lower.includes('available') || lower.includes('availability') || lower.includes('date')) {
            action = 'availability';
        } else if (lower.includes('offer') || lower.includes('deal') || lower.includes('promo') || lower.includes('special') || lower.includes('discount')) {
            action = 'offers';
        } else if (lower.includes('contact') || lower.includes('phone') || lower.includes('email') || lower.includes('call') || lower.includes('address') || lower.includes('location')) {
            action = 'contact';
        }

        botReply(action);
    }

   
    function handleQuickReply(label, action) {
        addMessage(label, 'user');

        
        const lower = label.toLowerCase();
        let responseKey = 'fallback';

        if (lower.includes('book') || lower.includes('room')) {
            responseKey = 'book';
        } else if (lower.includes('inquir')) {
            responseKey = 'inquiries';
        } else if (lower.includes('available') || lower.includes('availability')) {
            responseKey = 'availability';
        } else if (lower.includes('offer') || lower.includes('deal') || lower.includes('weekend') || lower.includes('family') || lower.includes('romantic') || lower.includes('special')) {
            responseKey = 'offers';
        } else if (lower.includes('contact') || lower.includes('call') || lower.includes('email') || lower.includes('address') || lower.includes('location') || lower.includes('agent')) {
            responseKey = 'contact';
        } else if (lower.includes('view') || lower.includes('explore') || lower.includes('all')) {
           
            responseKey = 'offers';
        } else if (lower.includes('check') || lower.includes('time') || lower.includes('pet') || lower.includes('amenit') || lower.includes('dining') || lower.includes('breakfast')) {
            responseKey = 'inquiries';
        } else {
            
            const actionMap = {
                'book': 'book',
                'book-a-room': 'book',
                'inquiries': 'inquiries',
                'availability': 'availability',
                'check-availability': 'availability',
                'offers': 'offers',
                'special-offers': 'offers',
                'contact': 'contact',
                'contact-us': 'contact',
                'view-rooms': 'book',
                'view-all-rooms': 'book',
                'talk-to-agent': 'contact',
                'call-now': 'contact',
                'send-email': 'contact',
                'view-location': 'contact',
                'check-in-out-times': 'inquiries',
                'amenities': 'inquiries',
                'pet-policy': 'inquiries',
                'dining': 'inquiries',
                'weekend-escape': 'offers',
                'family-package': 'offers',
                'romantic-getaway': 'offers',
                'book-now': 'book'
            };
            responseKey = actionMap[action] || 'fallback';
        }

        botReply(responseKey);
    }

   
    function toggleChat(open) {
        isOpen = open;
        widget.classList.toggle('open', open);
        toggleBtn.classList.toggle('active', open);
        iconOpen.style.display = open ? 'none' : 'block';
        iconClose.style.display = open ? 'block' : 'none';
        toggleBtn.setAttribute('aria-label', open ? 'Close chat' : 'Open chat');

        if (open) {
            scrollToBottom();
            inputEl.focus();
        }
    }

    toggleBtn.addEventListener('click', () => toggleChat(!isOpen));
    closeBtn.addEventListener('click', () => toggleChat(false));

  
    function handleSend() {
        const text = inputEl.value.trim();
        if (text) sendUserMessage(text);
    }

    sendBtn.addEventListener('click', handleSend);
    inputEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSend();
        }
    });

 
    quickBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            handleQuickReply(btn.textContent, btn.dataset.action);
        });
    });



    console.log('🤖 Casaul Hotel Virtual Assistant loaded!');
});

