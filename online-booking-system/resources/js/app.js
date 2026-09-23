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
    const modalTriggers = document.querySelectorAll('#guest-signin-trigger, [data-auth-trigger]');
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

        profileTrigger.addEventListener('click', () => {
            const isOpen = profileMenu.classList.toggle('open');
            profileTrigger.classList.toggle('open', isOpen);
            profileTrigger.setAttribute('aria-expanded', String(isOpen));
        });

        document.addEventListener('click', (event) => {
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

    if (modalTriggers.length && authModal) {
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

        modalTriggers.forEach((trigger) => trigger.addEventListener('click', openModal));
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
    const faqToggle   = document.getElementById('chat-faq-toggle');
    const quickBtns   = document.querySelectorAll('.quick-reply');
    const iconOpen    = document.getElementById('chat-icon-open');
    const iconClose   = document.getElementById('chat-icon-close');
    const formEl      = document.getElementById('message-form');
    const chatbotUrl  = formEl ? formEl.dataset.chatbotEndpoint : null;
    const guestMessagesUrl = formEl ? formEl.dataset.guestMessagesEndpoint : null;

    let isOpen = false;
    let pendingAction = null;
    let frontDeskHistoryLoaded = false;
    let lastFrontDeskAction = null;
    let lastFrontDeskPrompt = false;
    let frontDeskPollingTimer = null;
    const frontDeskServerState = new Map();

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
        }
    }

    function addFrontDeskBubble(text, type, timestamp, key = '') {
        const typing = messagesEl.querySelector('.typing-indicator');
        if (typing) typing.remove();

        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${type} front-desk-msg`;
        if (key) msgDiv.dataset.frontDeskMessageKey = key;

        const contentDiv = document.createElement('div');
        contentDiv.className = 'chat-msg-content';
        contentDiv.textContent = text;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'chat-msg-time';
        timeSpan.textContent = timestamp || formatTime();

        msgDiv.appendChild(contentDiv);
        msgDiv.appendChild(timeSpan);
        messagesEl.appendChild(msgDiv);
    }

    function parseFrontDeskHistory(replyText) {
        const historyStart = replyText.indexOf('\n\nYou (');
        if (historyStart === -1) return null;

        const history = replyText.slice(historyStart).trim();
        const entries = history.split('\n\nYou (').filter(Boolean);

        return entries.map((entry) => {
            const guestEnd = entry.indexOf('): ');
            if (guestEnd === -1) return null;

            const guestTimestamp = entry.slice(0, guestEnd);
            const conversation = entry.slice(guestEnd + 3);
            const frontDeskMarker = conversation.indexOf('\nFront Desk');
            const guestMessage = frontDeskMarker === -1
                ? conversation
                : conversation.slice(0, frontDeskMarker);
            const frontDeskConversation = frontDeskMarker === -1
                ? ''
                : conversation.slice(frontDeskMarker).replace(/^\nFront Desk(?: \((.*?)\))?: ?/, '');
            const frontDeskTimestampMatch = conversation.match(/\nFront Desk \((.*?)\):/);

            return {
                guestMessage,
                guestTimestamp,
                frontDeskMessage: frontDeskConversation === 'Reply pending' ? '' : frontDeskConversation,
                frontDeskTimestamp: frontDeskTimestampMatch?.[1] || '',
            };
        }).filter(Boolean);
    }

    function renderFrontDeskHistory(replyText, includeGuestMessages = true) {
        const entries = parseFrontDeskHistory(replyText);
        if (!entries) return false;

        entries.forEach((entry) => {
            const key = frontDeskMessageKey(entry.guestMessage, entry.guestTimestamp);
            frontDeskServerState.set(key, entry.frontDeskMessage || '');
            if (includeGuestMessages) {
                addFrontDeskBubble(entry.guestMessage, 'user', entry.guestTimestamp, key);
            }
            if (entry.frontDeskMessage) {
                addFrontDeskBubble(entry.frontDeskMessage, 'bot', entry.frontDeskTimestamp, `${key}:reply`);
            }
        });

        scrollToBottom();
        return true;
    }

    function renderLatestFrontDeskReply(replyText) {
        const entries = parseFrontDeskHistory(replyText);
        const latestEntry = entries?.[entries.length - 1];

        if (latestEntry?.frontDeskMessage) {
            const key = frontDeskMessageKey(latestEntry.guestMessage, latestEntry.guestTimestamp);
            frontDeskServerState.set(key, latestEntry.frontDeskMessage);
            addFrontDeskBubble(latestEntry.frontDeskMessage, 'bot', latestEntry.frontDeskTimestamp, `${key}:reply`);
            scrollToBottom();
        }
    }

    function frontDeskMessageKey(message, timestamp) {
        const parsedTimestamp = Date.parse(timestamp || '');
        return `${parsedTimestamp || timestamp || ''}|${message}`;
    }

    async function syncFrontDeskConversation() {
        if (!guestMessagesUrl || pendingAction !== 'contact_front_desk') return;

        try {
            const response = await fetch(guestMessagesUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            const messages = Array.isArray(data.messages) ? [...data.messages].reverse() : [];

            messages.forEach((message) => {
                const key = frontDeskMessageKey(message.message, message.sent_at);
                const knownReply = frontDeskServerState.get(key);

                if (knownReply === undefined) {
                    frontDeskServerState.set(key, message.reply || '');
                    addFrontDeskBubble(message.message, 'user', formatConversationTime(message.sent_at), key);
                    if (message.reply) {
                        addFrontDeskBubble(message.reply, 'bot', formatConversationTime(message.replied_at), `${key}:reply`);
                    }
                } else if (message.reply && knownReply !== message.reply) {
                    frontDeskServerState.set(key, message.reply);
                    addFrontDeskBubble(message.reply, 'bot', formatConversationTime(message.replied_at), `${key}:reply`);
                }
            });

            scrollToBottom();
        } catch (error) {
            console.error('Front desk conversation sync error:', error);
        }
    }

    function formatConversationTime(timestamp) {
        if (!timestamp) return '';
        const date = new Date(timestamp);
        return Number.isNaN(date.getTime()) ? '' : date.toLocaleString([], {
            month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
        });
    }

    function startFrontDeskPolling() {
        if (frontDeskPollingTimer || pendingAction !== 'contact_front_desk') return;
        syncFrontDeskConversation();
        frontDeskPollingTimer = window.setInterval(syncFrontDeskConversation, 4000);
    }

    function stopFrontDeskPolling() {
        if (!frontDeskPollingTimer) return;
        window.clearInterval(frontDeskPollingTimer);
        frontDeskPollingTimer = null;
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
        container.style.display = 'none';
        faqToggle.style.display = 'flex';
        faqToggle.setAttribute('aria-expanded', 'false');

        if (!buttons || buttons.length === 0) {
            container.style.display = 'none';
            faqToggle.style.display = 'none';
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

    const defaultQuickReplies = ['Reservations', 'Rooms', 'Check-in / Check-out', 'Payment Information', 'Dining & Menu', 'Hotel Services', 'Hotel Policies', 'Contact Front Desk', 'Request Housekeeping', 'Book a Room', 'Inquiries', 'Show Available Rooms', 'Special Offers', 'Contact Us'];

    async function sendChatbotMessage(message, action = null, requestType = null) {
        if (!chatbotUrl) {
            return { reply: 'Thank you for your message. Please contact our front desk for assistance.' };
        }

        const csrfToken = formEl ? formEl.querySelector('input[name="_token"]')?.value || '' : '';

        try {
            const response = await fetch(chatbotUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    message: message,
                    action: action,
                    request_type: requestType,
                    name: 'Guest',
                    email: 'guest@example.com'
                })
            });

            if (!response.ok) {
                throw new Error('Chatbot request failed');
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Chatbot request error:', error);
            return { reply: 'Thank you for your message. Please contact our front desk for assistance.' };
        }
    }

    function botReplyFromServer(replyData) {
        const typingEl = showTypingIndicator();
        const delay = 500 + Math.random() * 400;

        setTimeout(() => {
            typingEl.remove();
            const isFrontDeskReply = lastFrontDeskAction === 'contact_front_desk' || replyData.mode === 'contact_front_desk';
            let renderedHistory = false;

            if (isFrontDeskReply && !frontDeskHistoryLoaded) {
                renderedHistory = renderFrontDeskHistory(replyData.reply, lastFrontDeskPrompt);
                if (renderedHistory) frontDeskHistoryLoaded = true;
            } else if (isFrontDeskReply) {
                renderLatestFrontDeskReply(replyData.reply);
            } else if (!isFrontDeskReply) {
                addMessage(replyData.reply, 'bot', true);
            }
            updateQuickReplies(replyData.quick_replies || defaultQuickReplies);
        }, delay);
    }

    function sendUserMessage(text) {
        if (!text || text.trim() === '') return;
        const message = text.trim();
        const action = pendingAction;
        const isFrontDeskPrompt = action === 'contact_front_desk' && message === 'Contact Front Desk';

        if (!isFrontDeskPrompt) {
            addMessage(message, 'user');
        }

        inputEl.value = '';
        lastFrontDeskAction = action;
        lastFrontDeskPrompt = isFrontDeskPrompt;
        const requestType = action === 'request_housekeeping' ? message : null;
        pendingAction = null;

        if (action !== 'contact_front_desk') {
            frontDeskHistoryLoaded = false;
        }

        sendChatbotMessage(message, action, requestType).then((reply) => {
            pendingAction = reply.mode || null;
            if (pendingAction === 'contact_front_desk') startFrontDeskPolling();
            botReplyFromServer(reply);
        });
    }

   
    function handleQuickReply(label, action) {
        if (action === 'contact-front-desk') {
            pendingAction = 'contact_front_desk';
        } else if (action === 'request-housekeeping') {
            pendingAction = 'request_housekeeping';
        } else if (pendingAction === 'request_housekeeping') {
            sendUserMessage(label);
            return;
        }

        sendUserMessage(label);
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
            if (pendingAction === 'contact_front_desk') startFrontDeskPolling();
        } else {
            stopFrontDeskPolling();
        }
    }

    toggleBtn.addEventListener('click', () => toggleChat(!isOpen));
    closeBtn.addEventListener('click', () => toggleChat(false));
    faqToggle.addEventListener('click', () => {
        const isExpanded = faqToggle.getAttribute('aria-expanded') === 'true';
        faqToggle.setAttribute('aria-expanded', String(!isExpanded));
        faqToggle.textContent = isExpanded ? 'Show Quick Questions' : 'Hide Quick Questions';
        document.getElementById('chat-quick-replies').style.display = isExpanded ? 'none' : 'flex';
    });

  
    function handleSend() {
        const text = inputEl.value.trim();
        if (text) sendUserMessage(text);
    }

    formEl?.addEventListener('submit', (event) => {
        event.preventDefault();
        handleSend();
    });

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

