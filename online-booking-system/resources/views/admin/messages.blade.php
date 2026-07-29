@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-3xl font-bold text-gray-800 mb-6">Customer Messages</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Messages List -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Inbox</h3>
                </div>
                <div class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                    @forelse($messages as $message)
                    <div class="p-4 hover:bg-gray-50 cursor-pointer transition-colors {{ !$message->is_replied ? 'bg-orange-50' : '' }}" onclick="selectMessage({{ $message->id }}, @json($message->customer_name), @json($message->customer_email), @json($message->message), @json($message->admin_reply ?? ''), {{ $message->is_replied ? 'true' : 'false' }})">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center text-white font-semibold">
                                {{ substr($message->customer_name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-gray-900 truncate">{{ $message->customer_name }}</p>
                                    @if(!$message->is_replied)
                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 truncate">{{ substr($message->message, 0, 50) }}...</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-comments text-4xl mb-4 text-gray-300"></i>
                        <p>No messages yet</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Message Detail & Reply -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b bg-gray-50">
                    <h3 class="font-semibold text-gray-800">Message Details</h3>
                </div>
                <div id="messageDetail" class="p-6">
                    <div class="text-center text-gray-500 py-12">
                        <i class="fas fa-envelope-open text-4xl mb-4 text-gray-300"></i>
                        <p>Select a message to view details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 animate-fade-in">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Reply to Customer</h3>
        </div>
        <form id="replyForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="id" id="replyMessageId">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-2"><strong>From:</strong> <span id="replyCustomerName"></span></p>
                <p class="text-sm text-gray-600 mb-2"><strong>Email:</strong> <span id="replyCustomerEmail"></span></p>
                <p class="text-sm text-gray-600"><strong>Message:</strong></p>
                <p id="replyOriginalMessage" class="text-gray-800 mt-2"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Reply</label>
                <textarea name="admin_reply" id="replyText" rows="5" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Type your reply here..."></textarea>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('replyModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300">
                    <i class="fas fa-paper-plane mr-2"></i>Send Reply
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentMessageId = null;
    
    function selectMessage(id, name, email, message, reply, isReplied) {
        currentMessageId = id;
        const replyText = reply ? reply : 'Not replied yet';
        
        document.getElementById('messageDetail').innerHTML = `
            <div class="space-y-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center text-white font-semibold text-xl">
                        ${name.charAt(0)}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900">${name}</h4>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${isReplied ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                                ${isReplied ? 'Replied' : 'Pending'}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">${email}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Customer Message:</p>
                    <p class="text-gray-800">${message}</p>
                </div>
                
                ${reply ? `
                <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                    <p class="text-sm text-gray-600 mb-2">Your Reply:</p>
                    <p class="text-gray-800">${reply}</p>
                    <p class="text-xs text-gray-500 mt-2">Replied at: ${new Date().toLocaleString()}</p>
                </div>
                ` : ''}
                
                ${!isReplied ? `
                <button onclick="openReplyModal(${id}, '${name}', '${email}', '${message.replace(/'/g, "\\'")}')" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-reply mr-2"></i>Reply to Customer
                </button>
                ` : ''}
            </div>
        `;
    }
    
    function openReplyModal(id, name, email, message) {
        document.getElementById('replyMessageId').value = id;
        document.getElementById('replyCustomerName').textContent = name;
        document.getElementById('replyCustomerEmail').textContent = email;
        document.getElementById('replyOriginalMessage').textContent = message;
        document.getElementById('replyText').value = '';
        var replyRoute = "{{ route('admin.messages.reply', ['id' => '__ID__']) }}";
        document.getElementById('replyForm').action = replyRoute.replace('__ID__', id);
        document.getElementById('replyModal').classList.remove('hidden');
    }
</script>
@endsection
