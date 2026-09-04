@extends('employee.layout')

@section('pageTitle', 'Message Management')
@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4">
        <!-- Unread Messages -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Unread Messages</p>
                    <p class="mt-2 text-4xl font-bold text-gray-800">0</p>
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
                    <p class="mt-2 text-4xl font-bold text-gray-800">0</p>
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
                    <p class="mt-2 text-4xl font-bold text-gray-800">0</p>
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
        <button class="flex items-center gap-2 rounded-lg border-2 border-orange-500 bg-orange-50 px-4 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-100">
            <i class="fas fa-calendar-day"></i> Today
        </button>
        <button class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            <i class="fas fa-calendar-week"></i> This Week
        </button>
        <button class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            <i class="fas fa-envelope-open"></i> All Messages
        </button>
        <button class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
            <i class="fas fa-star"></i> Unread
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

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

            @forelse($messages as $message)
                <div class="space-y-4">
                    <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gray-200 text-gray-600">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h6 class="font-semibold text-gray-800">{{ $message->customer_name }}</h6>
                                <span class="flex-shrink-0 text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit($message->message, 100) }}</p>
                        </div>
                    </div>
                </div>
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

        <!-- Quick Reply -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <div class="mb-6 flex items-center gap-3">
                <div class="rounded-lg bg-orange-100 p-2 text-orange-600">
                    <i class="fas fa-pen-square text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Quick Reply</h3>
            </div>

            <form action="{{ route('employee.messages.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Compose Message</label>
                    <div>
                        <label class="mb-2 block text-xs font-semibold text-gray-600 uppercase">Send To</label>
                        <select name="recipient" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select a guest...</option>
                            @forelse($messages as $message)
                                <option value="{{ $message->id }}">{{ $message->customer_name }}</option>
                            @empty
                                <option disabled>No guests available</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold text-gray-600 uppercase">Quick Templates</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" onclick="document.querySelector('textarea[name=message]').value = 'Assisting Soon'" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">Assisting Soon</button>
                        <button type="button" onclick="document.querySelector('textarea[name=message]').value = 'Completed'" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">Completed</button>
                        <button type="button" onclick="document.querySelector('textarea[name=message]').value = 'Front Desk'" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">Front Desk</button>
                        <button type="button" onclick="document.querySelector('textarea[name=message]').value = 'Thank You'" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50">Thank You</button>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold text-gray-600 uppercase">Message</label>
                    <textarea name="message" rows="6" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Type your message here..."></textarea>
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
@endsection
