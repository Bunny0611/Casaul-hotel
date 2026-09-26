@extends('employee.layout')

@section('pageTitle', 'Message Management')
@section('content')
<style>
    .employee-template-buttons {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
    }

    .employee-template-btn {
        overflow: hidden;
        border: 1px solid #e5eaf0;
        border-radius: 0.375rem;
        background: #f8fafc;
        padding: 0.5rem 0.625rem;
        color: #475569;
        font-size: 0.6875rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: 0.2s;
    }

    .employee-template-btn:hover {
        border-color: #ff6b35;
        background: #fff1eb;
        color: #ff6b35;
    }

    .employee-conversation-list {
        max-height: 560px;
        overflow-y: auto;
    }

    .employee-conversation {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #fff;
        padding: 1rem;
        text-align: left;
        transition: 0.2s;
    }

    .employee-conversation:hover,
    .employee-conversation.is-selected {
        border-color: #ff6b35;
        background: #fff8f4;
    }

    .employee-conversation-preview {
        overflow: hidden;
        color: #6b7280;
        font-size: 0.875rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .employee-chat-thread {
        display: flex;
        min-height: 260px;
        max-height: 360px;
        flex-direction: column;
        gap: 0.75rem;
        overflow-y: auto;
        scrollbar-gutter: stable;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #f8fafc;
        padding: 1rem;
    }

    .employee-chat-empty {
        display: flex;
        min-height: 260px;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        text-align: center;
    }

    .employee-chat-message {
        max-width: 78%;
    }

    .employee-chat-message.guest {
        align-self: flex-start;
    }

    .employee-chat-message.front-desk {
        align-self: flex-end;
        text-align: right;
    }

    .employee-chat-bubble {
        display: inline-block;
        border-radius: 1rem;
        background: #fff;
        padding: 0.75rem 1rem;
        color: #374151;
        text-align: left;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .front-desk .employee-chat-bubble {
        background: #ff6b35;
        color: #fff;
    }

    .employee-chat-time {
        display: block;
        margin-top: 0.25rem;
        color: #9ca3af;
        font-size: 0.6875rem;
    }

    .employee-forward-button {
        margin-top: 0.35rem;
        border: 0;
        background: transparent;
        padding: 0;
        color: #c2410c;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
    }

    .employee-forward-button:hover {
        color: #9a3412;
        text-decoration: underline;
    }

    .employee-forward-form[hidden] {
        display: none !important;
    }

    .employee-forward-form {
        display: grid;
        gap: 0.65rem;
        margin-top: 0.75rem;
        border: 1px solid #fed7aa;
        border-radius: 0.75rem;
        background: #fffaf5;
        padding: 0.9rem;
    }

    .employee-forward-form select,
    .employee-forward-form textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        background: #fff;
        padding: 0.6rem 0.7rem;
        font-size: 0.875rem;
    }

    .employee-forward-form textarea {
        min-height: 68px;
        resize: vertical;
    }

    .employee-reply-form.is-disabled {
        opacity: 0.55;
        pointer-events: none;
    }

    .employee-channel-switcher {
        display: flex;
        width: fit-content;
        max-width: 100%;
        gap: 0.35rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #f3f4f6;
        padding: 0.3rem;
    }

    .employee-channel-tab {
        display: inline-flex;
        min-height: 2.75rem;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        border: 1px solid transparent;
        border-radius: 0.55rem;
        background: transparent;
        padding: 0.55rem 0.9rem;
        color: #4b5563;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
    }

    .employee-channel-tab[aria-selected="true"] {
        border-color: #e5e7eb;
        background: #fff;
        color: #c2410c;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .employee-channel-count {
        display: inline-flex;
        min-width: 1.25rem;
        height: 1.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #ffedd5;
        padding: 0 0.35rem;
        color: #9a3412;
        font-size: 0.7rem;
    }

    .employee-channel-panel[hidden] {
        display: none !important;
    }

    @media (max-width: 480px) {
        .employee-channel-switcher {
            width: 100%;
        }

        .employee-channel-tab {
            flex: 1;
            padding-inline: 0.45rem;
        }
    }
</style>
<div class="space-y-6">
    <div class="employee-channel-switcher" role="tablist" aria-label="Message channels">
        <button type="button" id="employee-guest-tab" class="employee-channel-tab" role="tab" aria-selected="true" aria-controls="employee-guest-channel" onclick="switchEmployeeMessageChannel('guest')">
            <i class="fas fa-user"></i> Guest Inbox
            @if(($stats['unread'] ?? 0) > 0)<span class="employee-channel-count">{{ $stats['unread'] }}</span>@endif
        </button>
        <button type="button" id="employee-staff-tab" class="employee-channel-tab" role="tab" aria-selected="false" aria-controls="employee-staff-channel" onclick="switchEmployeeMessageChannel('staff')">
            <i class="fas fa-users"></i> Staff Chat
        </button>
    </div>

    <section id="employee-guest-channel" class="employee-channel-panel space-y-6" role="tabpanel" aria-labelledby="employee-guest-tab">
        <p class="text-sm text-gray-500">Private conversations between guests and employees.</p>
    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <!-- Unread Messages -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unread Messages</p>
                    <p class="mt-2 text-4xl font-bold text-gray-800">{{ $stats['unread'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-gray-500">Messages awaiting your response</p>
                </div>
                <div class="rounded-lg bg-yellow-100 p-3 text-yellow-600">
                    <i class="fas fa-envelope text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Replied Messages -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Replied Messages</p>
                    <p class="mt-2 text-4xl font-bold text-gray-800">{{ $stats['replied'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-gray-500">Messages you've already responded to</p>
                </div>
                <div class="rounded-lg bg-green-100 p-3 text-green-600">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Total Messages -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Messages</p>
                    <p class="mt-2 text-4xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                    <p class="mt-2 text-xs text-gray-500">All guest messages received</p>
                </div>
                <div class="rounded-lg bg-blue-100 p-3 text-blue-600">
                    <i class="fas fa-comment-dots text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex gap-2">
        <button type="button" onclick="window.location.href='{{ route('employee.messages', ['filter' => 'today']) }}'" class="flex items-center gap-2 rounded-lg {{ $filter === 'today' ? 'border-2 border-orange-500 bg-orange-50 text-orange-600' : 'border border-gray-300 bg-white text-gray-700' }} px-4 py-2 text-sm font-medium transition hover:bg-gray-50">
            <i class="fas fa-calendar-day"></i> Today
        </button>
        <button type="button" onclick="window.location.href='{{ route('employee.messages', ['filter' => 'week']) }}'" class="flex items-center gap-2 rounded-lg {{ $filter === 'week' ? 'border-2 border-orange-500 bg-orange-50 text-orange-600' : 'border border-gray-300 bg-white text-gray-700' }} px-4 py-2 text-sm font-medium transition hover:bg-gray-50">
            <i class="fas fa-calendar-week"></i> This Week
        </button>
        <button type="button" onclick="window.location.href='{{ route('employee.messages', ['filter' => 'all']) }}'" class="flex items-center gap-2 rounded-lg {{ $filter === 'all' ? 'border-2 border-orange-500 bg-orange-50 text-orange-600' : 'border border-gray-300 bg-white text-gray-700' }} px-4 py-2 text-sm font-medium transition hover:bg-gray-50">
            <i class="fas fa-envelope-open"></i> All Messages
        </button>
        <button type="button" onclick="window.location.href='{{ route('employee.messages', ['filter' => 'unread']) }}'" class="flex items-center gap-2 rounded-lg {{ $filter === 'unread' ? 'border-2 border-orange-500 bg-orange-50 text-orange-600' : 'border border-gray-300 bg-white text-gray-700' }} px-4 py-2 text-sm font-medium transition hover:bg-gray-50">
            <i class="fas fa-star"></i> Unread
        </button>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-2 gap-6">
        <!-- Guest Message Box -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="mb-6 flex items-center gap-3">
                <div class="rounded-lg bg-orange-100 p-2 text-orange-600">
                    <i class="fas fa-comments text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Guest Conversations</h3>
            </div>

            @forelse($conversations as $conversation)
                <button type="button" class="employee-conversation mb-3 {{ $loop->first ? 'is-selected' : '' }}" data-conversation-key="{{ $conversation->key }}" onclick="selectEmployeeConversation('{{ $conversation->key }}')">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <h6 class="truncate font-semibold text-gray-800">{{ $conversation->name }}</h6>
                                <span class="flex-shrink-0 text-xs text-gray-500">{{ $conversation->latest_message->created_at?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-gray-500">Room {{ $conversation->room_number ?? '—' }}</p>
                            <p class="employee-conversation-preview mt-1">Latest message: &quot;{{ $conversation->latest_message->message }}&quot;</p>
                        </div>
                        @if($conversation->unread > 0)
                            <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-orange-500 px-2 text-xs font-semibold text-white">{{ $conversation->unread }}</span>
                        @endif
                    </div>
                </button>
            @empty
                <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-12">
                    <div class="text-4xl text-gray-400 mb-2">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p class="text-center text-gray-600 font-medium">No new messages</p>
                    <p class="text-center text-sm text-gray-500">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        <!-- Conversation and Quick Reply -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="mb-6 flex items-center gap-3">
                <div class="rounded-lg bg-orange-100 p-2 text-orange-600">
                    <i class="fas fa-pen-square text-lg"></i>
                </div>
                <h3 id="employee-chat-title" class="text-lg font-semibold text-gray-800">Select a conversation</h3>
            </div>

            <div id="employee-chat-thread" class="employee-chat-thread">
                @if($conversations->isNotEmpty())
                    @foreach($conversations->first()->messages as $threadMessage)
                        <div class="employee-chat-message guest">
                            <div class="employee-chat-bubble">{{ $threadMessage->message }}</div>
                            <span class="employee-chat-time">Guest · {{ $threadMessage->created_at?->format('M j, Y g:i A') }}</span>
                        </div>
                        @if($threadMessage->admin_reply)
                            <div class="employee-chat-message front-desk">
                                <div class="employee-chat-bubble">{{ $threadMessage->admin_reply }}</div>
                                <span class="employee-chat-time">Front Desk · {{ $threadMessage->replied_at?->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="employee-chat-empty">Select a guest conversation to view the full chat.</div>
                @endif
            </div>

            <form id="employee-forward-form" class="employee-forward-form" method="POST" hidden>
                @csrf
                <div>
                    <h4 class="font-semibold text-gray-800">Write a staff handoff</h4>
                    <p id="employee-forward-preview" class="mt-1 text-xs text-gray-500">Include the item or issue, room, approximate time, and next step. Only your summary is sent; the guest's name, email, and original message stay private.</p>
                </div>
                <label class="text-xs font-semibold text-gray-600" for="employee-forward-recipient">Send to staff</label>
                <select id="employee-forward-recipient" name="recipient_id" required>
                    <option value="">Select a staff member...</option>
                    @foreach($staffConversations as $staffConversation)
                        <option value="{{ $staffConversation->contact->id }}">{{ $staffConversation->contact->name }} ({{ ucfirst($staffConversation->contact->role) }})</option>
                    @endforeach
                </select>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 uppercase">Quick handoff templates</label>
                    <div class="employee-template-buttons">
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Room [room]: Please deliver [item] around [time].')">Item delivery</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Room [room]: Please complete [cleaning request] around [time].')">Room cleaning</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Room [room]: Please provide or replace [linen or amenity] around [time].')">Linens or amenities</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Room [room]: Please inspect [issue] and update the front desk.')">Room issue</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Please follow up on [request] for Room [room] by [time].')">Guest request</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeHandoffTemplate('Please contact the guest in Room [room] about [issue] and update the front desk.')">Staff follow-up</button>
                    </div>
                </div>
                <label class="text-xs font-semibold text-gray-600" for="employee-forward-note">Staff message</label>
                <textarea id="employee-forward-note" name="note" maxlength="1000" required placeholder="Room 204 needs extra towels around 2 PM. Please deliver when available."></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" id="employee-forward-cancel" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700">Cancel</button>
                    <button type="submit" class="rounded-lg bg-orange-500 px-3 py-2 text-sm font-semibold text-white"><i class="fas fa-paper-plane"></i> Send handoff</button>
                </div>
            </form>

            <form id="employee-reply-form" action="{{ route('employee.messages.store') }}" method="POST" class="employee-reply-form mt-3 space-y-2 {{ $conversations->isEmpty() ? 'is-disabled' : '' }}">
                @csrf
                <input type="hidden" id="employee-recipient" name="recipient" value="{{ $conversations->first()?->latest_message?->id }}">

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 uppercase">Quick Templates</label>
                    <div class="employee-template-buttons">
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeTemplate('We will assist you shortly.')">Assisting Soon</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeTemplate('Your request has been completed.')">Completed</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeTemplate('Please contact front desk for assistance.')">Front Desk</button>
                        <button type="button" class="employee-template-btn" onclick="insertEmployeeTemplate('Thank you for your message.')">Thank You</button>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-600 uppercase">Reply</label>
                    <textarea id="employeeReplyMessage" name="message" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Type your message here..."></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="reset" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Clear</button>
                    <button type="submit" class="flex-1 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
    </section>

    <section id="employee-staff-channel" class="employee-channel-panel" role="tabpanel" aria-labelledby="employee-staff-tab" hidden>
        <p class="mb-4 text-sm text-gray-500">Internal conversations visible only to staff.</p>
        @include('shared.staff-messages')
    </section>
</div>
@php
    $employeeConversationData = $conversations->mapWithKeys(function ($conversation) {
        return [$conversation->key => [
            'name' => $conversation->name,
            'recipient' => $conversation->latest_message->id,
            'messages' => $conversation->messages->map(function ($message) {
                $replies = $message->replies->isNotEmpty()
                    ? $message->replies
                    : ($message->admin_reply ? collect([(object) ['reply' => $message->admin_reply, 'replied_at' => $message->replied_at]]) : collect());

                return [
                    'id' => $message->id,
                    'guest' => $message->message,
                    'replies' => $replies->map(fn ($reply) => [
                        'reply' => $reply->reply,
                        'replied_at' => $reply->replied_at?->format('M j, Y g:i A'),
                    ])->values(),
                    'sent_at' => $message->created_at?->format('M j, Y g:i A'),
                ];
            })->values(),
        ]];
    });
@endphp
<script>
    const employeeConversations = @json($employeeConversationData);

    function selectEmployeeConversation(key) {
        const conversation = employeeConversations[key];
        if (!conversation) return;

        document.querySelectorAll('.employee-conversation').forEach((item) => {
            item.classList.toggle('is-selected', item.dataset.conversationKey === key);
        });

        document.getElementById('employee-chat-title').textContent = conversation.name;
        document.getElementById('employee-recipient').value = conversation.recipient;
        document.getElementById('employee-reply-form').classList.remove('is-disabled');
        document.getElementById('employee-forward-form').hidden = true;

        const thread = document.getElementById('employee-chat-thread');
        thread.innerHTML = conversation.messages.map((message) => `
            <div class="employee-chat-message guest">
                <div class="employee-chat-bubble">${escapeEmployeeMessage(message.guest)}</div>
                <span class="employee-chat-time">Guest · ${message.sent_at || ''}</span>
                <button type="button" class="employee-forward-button" data-forward-message="${message.id}"><i class="fas fa-share"></i> Forward to staff</button>
            </div>
            ${(message.replies || []).map((reply) => `<div class="employee-chat-message front-desk"><div class="employee-chat-bubble">${escapeEmployeeMessage(reply.reply)}</div><span class="employee-chat-time">Front Desk · ${reply.replied_at || ''}</span></div>`).join('')}
        `).join('');
        thread.scrollTop = thread.scrollHeight;
    }

    document.getElementById('employee-chat-thread').addEventListener('click', (event) => {
        const button = event.target.closest('[data-forward-message]');
        if (!button) return;
        const messageId = button.dataset.forwardMessage;
        const form = document.getElementById('employee-forward-form');
        form.action = "{{ route('employee.messages.forward', ['id' => '__MESSAGE_ID__']) }}".replace('__MESSAGE_ID__', messageId);
        form.hidden = false;
        document.getElementById('employee-forward-recipient').focus();
    });

    document.getElementById('employee-forward-cancel').addEventListener('click', () => {
        const form = document.getElementById('employee-forward-form');
        form.reset();
        form.hidden = true;
    });

    function escapeEmployeeMessage(value) {
        return String(value || '').replace(/[&<>'"]/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
        }[character]));
    }

    function insertEmployeeTemplate(text) {
        const textarea = document.getElementById('employeeReplyMessage');
        textarea.value = textarea.value ? textarea.value + '\n\n' + text : text;
        textarea.focus();
    }

    function insertEmployeeHandoffTemplate(text) {
        const textarea = document.getElementById('employee-forward-note');
        textarea.value = textarea.value ? textarea.value + '\n\n' + text : text;
        textarea.focus();
    }

    function switchEmployeeMessageChannel(channel) {
        const isStaff = channel === 'staff';
        document.getElementById('employee-guest-tab').setAttribute('aria-selected', String(!isStaff));
        document.getElementById('employee-staff-tab').setAttribute('aria-selected', String(isStaff));
        document.getElementById('employee-guest-channel').hidden = isStaff;
        document.getElementById('employee-staff-channel').hidden = !isStaff;
    }

    const initialConversationKey = @json($selectedConversationKey);
    if (initialConversationKey && employeeConversations[initialConversationKey]) {
        selectEmployeeConversation(initialConversationKey);
    } else if (Object.keys(employeeConversations).length) {
        selectEmployeeConversation(Object.keys(employeeConversations)[0]);
    }
    switchEmployeeMessageChannel(new URLSearchParams(window.location.search).has('staff_id') ? 'staff' : 'guest');
</script>
@endsection
