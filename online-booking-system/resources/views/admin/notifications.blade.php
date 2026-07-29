@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">System Notifications</h2>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option>All Notifications</option>
                    <option>Unread</option>
                    <option>Read</option>
                </select>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-check-double mr-2"></i>MARK ALL READ
                </button>
                <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash mr-2"></i>CLEAR ALL
                </button>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($messages as $message)
            <div class="p-6 hover:bg-gray-50 transition-colors {{ !$message->is_replied ? 'bg-orange-50' : '' }}">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ substr($message->customer_name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $message->customer_name }}</h4>
                            <div class="flex items-center space-x-2">
                                @if(!$message->is_replied)
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                @endif
                                <span class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-2">{{ $message->message }}</p>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $message->customer_email }}</span>
                        </div>
                        @if($message->admin_reply)
                        <div class="mt-3 p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500">
                            <p class="text-sm text-gray-600 mb-1">Your reply:</p>
                            <p class="text-gray-800">{{ $message->admin_reply }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-bell-slash text-4xl mb-4 text-gray-300"></i>
                <p>No notifications yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
