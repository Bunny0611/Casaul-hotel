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

    .employee-reply-form.is-disabled {
        opacity: 0.55;
        pointer-events: none;
    }
</style>
<div class="space-y-6">
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
                <h3 class="text-lg font-semibold text-gray-800">Guest Message Box</h3>
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

        const thread = document.getElementById('employee-chat-thread');
        thread.innerHTML = conversation.messages.map((message) => `
            <div class="employee-chat-message guest">
                <div class="employee-chat-bubble">${escapeEmployeeMessage(message.guest)}</div>
                <span class="employee-chat-time">Guest · ${message.sent_at || ''}</span>
            </div>
            ${(message.replies || []).map((reply) => `<div class="employee-chat-message front-desk"><div class="employee-chat-bubble">${escapeEmployeeMessage(reply.reply)}</div><span class="employee-chat-time">Front Desk · ${reply.replied_at || ''}</span></div>`).join('')}
        `).join('');
        thread.scrollTop = thread.scrollHeight;
    }

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

    const initialConversationKey = @json($selectedConversationKey);
    if (initialConversationKey && employeeConversations[initialConversationKey]) {
        selectEmployeeConversation(initialConversationKey);
    } else if (Object.keys(employeeConversations).length) {
        selectEmployeeConversation(Object.keys(employeeConversations)[0]);
    }
</script>
@endsection
