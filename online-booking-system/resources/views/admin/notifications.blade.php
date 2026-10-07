@extends('admin.layout')

@section('content')
<div class="admin-notifications animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">System Notifications</h2>

    <div class="notification-toolbar p-4 border-b bg-gray-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <select id="notificationFilter" aria-label="Filter notifications" class="notification-filter w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:w-auto">
                <option value="all">All Notifications</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>

        <div class="notification-actions flex items-center gap-2">
            <button id="markAllReadButton" type="submit" form="markAllReadForm" class="inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors sm:px-4">
                <i class="fas fa-check-circle mr-2"></i> Mark all as read
            </button>
            <button id="clearAllNotificationsButton" type="button" class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors sm:px-4" data-confirm-target="clearAllNotificationsForm">
                <i class="fas fa-trash-alt mr-2"></i> Clear all
            </button>
        </div>
    </div>

    <div class="notification-list divide-y divide-gray-200">
        @forelse($messages as $message)
            <div data-notification-state="{{ $message->is_read ? 'read' : 'unread' }}" class="notification-entry p-4 hover:bg-gray-50 transition-colors sm:p-6 {{ !$message->is_read ? 'is-unread bg-orange-50' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1 text-orange-500">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="notification-copy flex-1">
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-lg font-semibold text-gray-800">{{ $message->customer_name ?? 'Guest' }}</p>
                                <p class="text-sm text-gray-600">{{ $message->customer_email }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $message->is_read ? 'bg-gray-100 text-gray-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $message->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-gray-700">{{ $message->message }}</p>

                        <div class="mt-3 text-xs text-gray-500">
                            {{ $message->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">No notifications found.</div>
        @endforelse
    </div>
</div>

<div id="notificationConfirmDialog" class="hidden" aria-live="polite"></div>

<form id="markAllReadForm" class="hidden" method="POST" action="{{ route('admin.notifications.read-all') }}">
    @csrf
</form>

<form id="clearAllNotificationsForm" class="hidden" method="POST" action="{{ route('admin.notifications.clear-all') }}">
    @csrf
    @method('DELETE')
</form>
