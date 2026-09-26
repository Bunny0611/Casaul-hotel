<style>
    .staff-messages { display: grid; grid-template-columns: minmax(220px, 0.8fr) minmax(0, 1.4fr); gap: 1rem; margin: 1.5rem 0; color: #1f2937; }
    .staff-messages-panel { min-width: 0; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; padding: 1rem; }
    .staff-messages-heading { margin: 0 0 0.25rem; font-size: 1.125rem; font-weight: 700; }
    .staff-messages-subheading { margin: 0 0 1rem; color: #6b7280; font-size: 0.875rem; }
    .staff-messages-contacts { display: grid; max-height: 440px; gap: 0.5rem; overflow-y: auto; }
    .staff-messages-contact { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; padding: 0.75rem; text-align: left; cursor: pointer; }
    .staff-messages-contact:hover, .staff-messages-contact.is-selected { border-color: #ff6b35; background: #fff8f4; }
    .staff-messages-contact-name { display: block; font-weight: 600; }
    .staff-messages-contact-meta, .staff-messages-preview { display: block; overflow: hidden; color: #6b7280; font-size: 0.75rem; text-overflow: ellipsis; white-space: nowrap; }
    .staff-messages-thread { display: flex; min-height: 250px; max-height: 360px; flex-direction: column; gap: 0.75rem; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; background: #f8fafc; padding: 1rem; }
    .staff-messages-empty { display: grid; min-height: 250px; place-items: center; color: #6b7280; text-align: center; }
    .staff-message { max-width: 82%; align-self: flex-start; }
    .staff-message.mine { align-self: flex-end; text-align: right; }
    .staff-message-bubble { display: inline-block; border-radius: 12px; background: #fff; padding: 0.65rem 0.85rem; text-align: left; overflow-wrap: anywhere; box-shadow: 0 1px 4px #0f172a14; }
    .staff-message.mine .staff-message-bubble { background: #ff6b35; color: #fff; }
    .staff-message-meta { display: block; margin-top: 0.2rem; color: #6b7280; font-size: 0.7rem; }
    .staff-message-form { display: grid; gap: 0.6rem; margin-top: 0.75rem; }
    .staff-message-form textarea { width: 100%; min-height: 80px; resize: vertical; border: 1px solid #d1d5db; border-radius: 8px; padding: 0.7rem; }
    .staff-message-form select { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; padding: 0.6rem; }
    .staff-message-form button { justify-self: end; border: 0; border-radius: 8px; background: #ff6b35; padding: 0.65rem 1rem; color: #fff; font-weight: 600; cursor: pointer; }
    .staff-message-form button:disabled { cursor: not-allowed; opacity: 0.5; }
    @media (max-width: 760px) { .staff-messages { grid-template-columns: 1fr; } .staff-messages-contacts { max-height: 220px; } }
</style>

<section class="staff-messages" aria-label="Staff messages">
    <div class="staff-messages-panel">
        <h2 class="staff-messages-heading">Staff Messages</h2>
        <p class="staff-messages-subheading">Message employees, housekeeping, and administration.</p>
        <div class="staff-messages-contacts" id="staff-messages-contacts">
            @forelse($staffConversations as $contactId => $conversation)
                <button type="button" class="staff-messages-contact" data-staff-contact="{{ $contactId }}">
                    <span class="staff-messages-contact-name">{{ $conversation->contact->name }}</span>
                    <span class="staff-messages-contact-meta">{{ ucfirst($conversation->contact->role) }}</span>
                    <span class="staff-messages-preview">{{ $conversation->latest_message?->body ?? 'Start a conversation' }}</span>
                </button>
            @empty
                <p class="staff-messages-subheading">No other staff accounts are available.</p>
            @endforelse
        </div>
    </div>

    <div class="staff-messages-panel">
        <h2 class="staff-messages-heading" id="staff-messages-title">Select a staff member</h2>
        <p class="staff-messages-subheading" id="staff-messages-role"></p>
        <div class="staff-messages-thread" id="staff-messages-thread" aria-live="polite">
            <div class="staff-messages-empty">Choose a staff member to view or start a conversation.</div>
        </div>
        <form class="staff-message-form" method="POST" action="{{ route(auth()->user()->role . '.staff-messages.store') }}">
            @csrf
            <input type="hidden" name="recipient_id" id="staff-message-recipient">
            <textarea name="message" maxlength="5000" required placeholder="Write a staff message..."></textarea>
            @error('message')<p role="alert">{{ $message }}</p>@enderror
            <button type="submit" id="staff-message-send" disabled><i class="fas fa-paper-plane"></i> Send</button>
        </form>
    </div>
</section>

<script>
    (() => {
        const conversations = @json($staffConversationData);
        const buttons = document.querySelectorAll('[data-staff-contact]');
        const thread = document.getElementById('staff-messages-thread');
        const recipient = document.getElementById('staff-message-recipient');
        const sendButton = document.getElementById('staff-message-send');

        function selectContact(id) {
            const conversation = conversations[id];
            if (!conversation) return;
            recipient.value = id;
            sendButton.disabled = false;
            document.getElementById('staff-messages-title').textContent = conversation.name;
            document.getElementById('staff-messages-role').textContent = conversation.role.charAt(0).toUpperCase() + conversation.role.slice(1);
            buttons.forEach((button) => button.classList.toggle('is-selected', button.dataset.staffContact === String(id)));
            thread.replaceChildren();

            if (!conversation.messages.length) {
                const empty = document.createElement('div');
                empty.className = 'staff-messages-empty';
                empty.textContent = 'No messages yet. Start the conversation below.';
                thread.append(empty);
                return;
            }

            conversation.messages.forEach((message) => {
                const item = document.createElement('div');
                item.className = `staff-message${message.mine ? ' mine' : ''}`;
                const bubble = document.createElement('div');
                bubble.className = 'staff-message-bubble';
                bubble.textContent = message.body;
                const meta = document.createElement('span');
                meta.className = 'staff-message-meta';
                meta.textContent = `${message.mine ? 'You' : message.sender} · ${message.sent_at || ''}`;
                item.append(bubble, meta);
                thread.append(item);
            });
            thread.scrollTop = thread.scrollHeight;
        }

        buttons.forEach((button) => button.addEventListener('click', () => selectContact(button.dataset.staffContact)));
        const requestedContact = new URLSearchParams(window.location.search).get('staff_id');
        const initialContact = Array.from(buttons).find((button) => button.dataset.staffContact === requestedContact) || buttons[0];
        if (initialContact) selectContact(initialContact.dataset.staffContact);
    })();
</script>