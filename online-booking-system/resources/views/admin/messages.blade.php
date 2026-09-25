@extends('admin.layout')

@section('content')
<style>
    .admin-conversation { width: 100%; border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #fff; padding: 1rem; text-align: left; transition: 0.2s; }
    .admin-conversation:hover, .admin-conversation.is-selected { border-color: #ff6b35; background: #fff8f4; }
    .admin-chat-thread { display: flex; min-height: 260px; max-height: 360px; flex-direction: column; gap: 0.75rem; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.75rem; background: #f8fafc; padding: 1rem; }
    .admin-chat-message { max-width: 78%; }
    .admin-chat-message.guest { align-self: flex-start; }
    .admin-chat-message.admin { align-self: flex-end; text-align: right; }
    .admin-chat-bubble { display: inline-block; border-radius: 1rem; background: #fff; padding: 0.75rem 1rem; color: #374151; text-align: left; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08); }
    .admin-chat-message.admin .admin-chat-bubble { background: #ff6b35; color: #fff; }
</style>

@php
    $messages = collect();
    $stats = [
        'unread' => $messages->where('is_replied', false)->count(),
        'replied' => $messages->where('is_replied', true)->count(),
        'total' => $messages->count(),
    ];
    $conversations = $messages->groupBy('customer_email')->map(function ($conversationMessages) {
        $orderedMessages = $conversationMessages->sortBy('created_at')->values();
        return (object) [
            'key' => sha1($orderedMessages->last()->customer_email),
            'name' => $orderedMessages->last()->customer_name,
            'latest_message' => $orderedMessages->last(),
            'messages' => $orderedMessages,
            'unread' => $orderedMessages->where('is_replied', false)->count(),
        ];
    })->sortByDesc(fn ($conversation) => $conversation->latest_message->created_at)->values();
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-3 gap-4">
        @foreach([
            ['label' => 'Unread Messages', 'value' => $stats['unread'] ?? 0, 'description' => 'Messages awaiting your response', 'icon' => 'fa-envelope', 'background' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
            ['label' => 'Replied Messages', 'value' => $stats['replied'] ?? 0, 'description' => "Messages you've already responded to", 'icon' => 'fa-check-circle', 'background' => 'bg-green-100', 'text' => 'text-green-600'],
            ['label' => 'Total Messages', 'value' => $stats['total'] ?? 0, 'description' => 'All guest messages received', 'icon' => 'fa-comment-dots', 'background' => 'bg-blue-100', 'text' => 'text-blue-600'],
        ] as $stat)
            <div class="rounded-2xl bg-white p-6 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $stat['label'] }}</p>
                        <p class="mt-2 text-4xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-xs text-gray-500">{{ $stat['description'] }}</p>
                    </div>
                    <div class="rounded-lg {{ $stat['background'] }} p-3 {{ $stat['text'] }}"><i class="fas {{ $stat['icon'] }} text-lg"></i></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="flex gap-2">
        @foreach(['today' => ['fa-calendar-day', 'Today'], 'week' => ['fa-calendar-week', 'This Week'], 'all' => ['fa-envelope-open', 'All Messages'], 'unread' => ['fa-star', 'Unread']] as $value => [$icon, $label])
            <button type="button" data-admin-filter="{{ $value }}" onclick="applyAdminFilter('{{ $value }}')" class="admin-filter-button flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition hover:bg-gray-50">
                <i class="fas {{ $icon }}"></i> {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="mb-6 flex items-center gap-3"><div class="rounded-lg bg-orange-100 p-2 text-orange-600"><i class="fas fa-comments text-lg"></i></div><h3 class="text-lg font-semibold text-gray-800">Guest Message Box</h3></div>
            @forelse($conversations as $conversation)
                <button type="button" class="admin-conversation mb-3 {{ $loop->first ? 'is-selected' : '' }}" data-conversation-key="{{ $conversation->key }}" data-message-dates="{{ $conversation->messages->map(fn ($message) => $message->created_at?->toIso8601String())->implode('|') }}" data-unread="{{ $conversation->unread }}" onclick="selectAdminConversation('{{ $conversation->key }}')">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600"><i class="fas fa-user text-sm"></i></div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2"><h6 class="truncate font-semibold text-gray-800">{{ $conversation->name }}</h6><span class="flex-shrink-0 text-xs text-gray-500">{{ $conversation->latest_message->created_at?->diffForHumans() }}</span></div>
                            <p class="text-xs text-gray-500">Room {{ $conversation->room_number ?? '—' }}</p>
                            <p class="mt-1 truncate text-sm text-gray-500">Latest message: &quot;{{ $conversation->latest_message->message }}&quot;</p>
                        </div>
                        @if($conversation->unread > 0)<span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-orange-500 px-2 text-xs font-semibold text-white">{{ $conversation->unread }}</span>@endif
                    </div>
                </button>
            @empty
                <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 py-12"><div class="mb-2 text-4xl text-gray-400"><i class="fas fa-inbox"></i></div><p class="font-medium text-gray-600">No messages</p><p class="text-sm text-gray-500">There are no messages for this filter.</p></div>
            @endforelse
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="mb-6 flex items-center gap-3"><div class="rounded-lg bg-orange-100 p-2 text-orange-600"><i class="fas fa-pen-square text-lg"></i></div><h3 id="admin-chat-title" class="text-lg font-semibold text-gray-800">Select a conversation</h3></div>
            <div id="admin-chat-thread" class="admin-chat-thread"><div class="flex min-h-[260px] items-center justify-center text-center text-gray-500">Select a guest conversation to view the full chat.</div></div>
            <form id="admin-reply-form" method="POST" class="mt-3 space-y-2 {{ $conversations->isEmpty() ? 'pointer-events-none opacity-55' : '' }}">
                @csrf
                <label class="mb-1 block text-xs font-semibold uppercase text-gray-600">Reply</label>
                <textarea name="admin_reply" rows="3" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Type your reply here..."></textarea>
                <div class="flex gap-2"><button type="reset" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Clear</button><button type="submit" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600"><i class="fas fa-paper-plane"></i> Send</button></div>
            </form>
        </div>
    </div>
</div>

@php
    $adminConversationData = $conversations->mapWithKeys(function ($conversation) {
        return [$conversation->key => ['name' => $conversation->name, 'recipient' => $conversation->latest_message->id, 'messages' => $conversation->messages->map(function ($message) {
            return ['guest' => $message->message, 'replies' => $message->replies->map(fn ($reply) => ['reply' => $reply->reply, 'replied_at' => $reply->replied_at?->format('M j, Y g:i A')])->values(), 'sent_at' => $message->created_at?->format('M j, Y g:i A')];
        })->values()]];
    });
@endphp
<script>
    const adminConversations = @json($adminConversationData);
    function selectAdminConversation(key) {
        const conversation = adminConversations[key];
        if (!conversation) return;
        document.querySelectorAll('.admin-conversation').forEach((item) => item.classList.toggle('is-selected', item.dataset.conversationKey === key));
        document.getElementById('admin-chat-title').textContent = conversation.name;
        document.getElementById('admin-reply-form').action = "{{ route('admin.messages.reply', ['id' => '__ID__']) }}".replace('__ID__', conversation.recipient);
        document.getElementById('admin-chat-thread').innerHTML = conversation.messages.map((message) => `<div class="admin-chat-message guest"><div class="admin-chat-bubble">${escapeAdminMessage(message.guest)}</div><span class="mt-1 block text-xs text-gray-400">Guest · ${message.sent_at || ''}</span></div>${(message.replies || []).map((reply) => `<div class="admin-chat-message admin"><div class="admin-chat-bubble">${escapeAdminMessage(reply.reply)}</div><span class="mt-1 block text-xs text-gray-400">Admin · ${reply.replied_at || ''}</span></div>`).join('')}`).join('');
        const thread = document.getElementById('admin-chat-thread');
        thread.scrollTop = thread.scrollHeight;
    }
    function escapeAdminMessage(value) { return String(value || '').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[character])); }
    function applyAdminFilter(filter) {
        const now = new Date();
        const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const startOfWeek = new Date(startOfToday);
        startOfWeek.setDate(startOfToday.getDate() - ((startOfToday.getDay() + 6) % 7));

        document.querySelectorAll('.admin-filter-button').forEach((button) => {
            const active = button.dataset.adminFilter === filter;
            button.className = `admin-filter-button flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition hover:bg-gray-50 ${active ? 'border-2 border-orange-500 bg-orange-50 text-orange-600' : 'border border-gray-300 bg-white text-gray-700'}`;
        });

        document.querySelectorAll('.admin-conversation').forEach((conversation) => {
            const dates = conversation.dataset.messageDates.split('|').filter(Boolean).map((date) => new Date(date));
            const visible = filter === 'all'
                || (filter === 'unread' && Number(conversation.dataset.unread) > 0)
                || (filter === 'today' && dates.some((date) => date >= startOfToday))
                || (filter === 'week' && dates.some((date) => date >= startOfWeek));
            conversation.hidden = !visible;
        });
    }

    if (Object.keys(adminConversations).length) selectAdminConversation(Object.keys(adminConversations)[0]);
    applyAdminFilter('all');
</script>
@endsection
