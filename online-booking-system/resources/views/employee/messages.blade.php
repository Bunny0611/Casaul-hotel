@extends('employee.layout')

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-md">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Inbox</h3>
            <p class="text-sm text-gray-500">Team and guest communication updates</p>
        </div>
        <button type="button" onclick="document.getElementById('composeMessageModal').classList.remove('hidden')" class="rounded-lg bg-orange-500 px-4 py-2 text-white transition hover:bg-orange-600">New Message</button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($messages as $message)
            <div class="flex items-start gap-3 border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600"><i class="fas fa-user"></i></div>
                <div class="flex-1">
                    <div class="flex justify-between gap-3">
                        <h6 class="font-semibold text-gray-800">{{ $message->customer_name }}</h6>
                        <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ Str::limit($message->message, 120) }}</p>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">
                No messages yet.
            </div>
        @endforelse
    </div>
</div>

<div id="composeMessageModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Compose Message</h3>
                <p class="text-sm text-gray-500">Send a new update to the team.</p>
            </div>
            <button type="button" onclick="document.getElementById('composeMessageModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('employee.messages.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Recipient</label>
                <select name="recipient" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Select a recipient</option>
                    <option value="Housekeeping Team">Housekeeping Team</option>
                    <option value="Front Desk">Front Desk</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Guest Services">Guest Services</option>
                    <option value="Administration">Administration</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Subject</label>
                <input type="text" name="subject" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Room cleaning request">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" rows="5" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Write your message here..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('composeMessageModal').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 text-white hover:bg-orange-600">Send Message</button>
            </div>
        </form>
    </div>
</div>
@endsection
