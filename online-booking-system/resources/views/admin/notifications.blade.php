@extends('admin.layout')

@section('content')
<style>
    .admin-notifications .notification-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .admin-notifications .notification-copy {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    body.dark .admin-notifications .text-gray-700,
    body.dark .admin-notifications .text-gray-600 {
        color: #cbd5e1 !important;
    }

    body.dark .admin-notifications select {
        background-color: #071023;
        border-color: #374151;
        color: #e6eef8;
    }

    body.dark .admin-notifications .notification-entry:hover {
        background-color: #1f2937 !important;
    }

    body.dark .admin-notifications .notification-entry.is-unread {
        background-color: #31251a !important;
    }

    body.dark .admin-notifications .notification-reply {
        background-color: #30251b;
        border-color: #f97316;
    }

    body.dark .admin-notifications .notification-confirm-cancel {
        background-color: #334155;
        color: #e6eef8;
    }

    @media (max-width: 640px) {
        .admin-notifications .notification-actions button {
            flex: 1 1 9rem;
            justify-content: center;
        }
    }
</style>

<div class="admin-notifications animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">System Notifications</h2>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="notification-toolbar p-4 border-b bg-gray-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <select id="notificationFilter" aria-label="Filter notifications" class="notification-filter w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent sm:w-auto">
                    <option value="all">All Notifications</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>
            <div class="notification-actions">
                <button id="markAllReadButton" type="button" class="inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors sm:px-4">
                    <i class="fas fa-check-double mr-2"></i>MARK ALL READ
                </button>
                <button id="clearAllNotificationsButton" type="button" class="inline-flex items-center px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors sm:px-4">
                    <i class="fas fa-trash mr-2"></i>CLEAR ALL
                </button>
            </div>
        </div>
        
        <div class="notification-list divide-y divide-gray-200">
            @forelse($messages as $message)
            <div data-notification-state="{{ $message->is_read ? 'read' : 'unread' }}" class="notification-entry p-4 hover:bg-gray-50 transition-colors sm:p-6 {{ !$message->is_read ? 'is-unread bg-orange-50' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ substr($message->customer_name, 0, 1) }}
                    </div>
                    <div class="notification-copy flex-1">
                        <div class="mb-2 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                            <h4 class="break-words font-semibold text-gray-900">{{ $message->customer_name }}</h4>
                            <div class="flex flex-wrap items-center gap-2">
                                @if(!$message->is_read)
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                @endif
                                <span class="text-sm text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="mb-2 break-words text-gray-700">{{ $message->message }}</p>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <i class="fas fa-envelope"></i>
                            <span class="notification-copy">{{ $message->customer_email }}</span>
                        </div>
                        @if($message->admin_reply)
                        <div class="notification-reply mt-3 p-3 bg-orange-50 rounded-lg border-l-4 border-orange-500">
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
<form id="markAllReadForm" class="hidden" method="POST" action="{{ route('admin.notifications.read-all') }}">
    @csrf
</form>
<form id="clearAllNotificationsForm" class="hidden" method="POST" action="{{ route('admin.notifications.clear-all') }}">
    @csrf
    @method('DELETE')
</form>
<div id="notificationConfirmDialog" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 p-4" aria-hidden="true">
    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="notificationConfirmTitle" aria-describedby="notificationConfirmMessage">
        <h3 id="notificationConfirmTitle" class="text-lg font-semibold text-gray-900"></h3>
        <p id="notificationConfirmMessage" class="mt-2 text-sm text-gray-600"></p>
        <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <button id="cancelNotificationAction" type="button" class="notification-confirm-cancel rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">Cancel</button>
            <button id="confirmNotificationAction" type="button" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">Yes</button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filter = document.getElementById('notificationFilter');
        const list = document.querySelector('.notification-list');
        if (filter && list) {
            const entries = Array.from(list.querySelectorAll('[data-notification-state]'));
            const emptyState = document.createElement('p');
            emptyState.className = 'hidden p-6 text-center text-gray-500';
            emptyState.setAttribute('role', 'status');
            emptyState.textContent = 'No notifications match this filter.';
            list.appendChild(emptyState);

            function applyNotificationFilter() {
                let visibleCount = 0;
                entries.forEach(entry => {
                    const matches = filter.value === 'all' || entry.dataset.notificationState === filter.value;
                    entry.classList.toggle('hidden', !matches);
                    if (matches) visibleCount += 1;
                });
                emptyState.classList.toggle('hidden', visibleCount > 0 || filter.value === 'all');
            }

            filter.addEventListener('change', applyNotificationFilter);
            applyNotificationFilter();
        }

        const markAllReadButton = document.getElementById('markAllReadButton');
        const clearAllButton = document.getElementById('clearAllNotificationsButton');
        const dialog = document.getElementById('notificationConfirmDialog');
        const dialogTitle = document.getElementById('notificationConfirmTitle');
        const dialogMessage = document.getElementById('notificationConfirmMessage');
        const confirmButton = document.getElementById('confirmNotificationAction');
        const cancelButton = document.getElementById('cancelNotificationAction');
        const markAllReadForm = document.getElementById('markAllReadForm');
        const clearAllForm = document.getElementById('clearAllNotificationsForm');
        let pendingForm = null;
        let lastTrigger = null;

        function closeConfirmation() {
            dialog.classList.add('hidden');
            dialog.classList.remove('flex');
            dialog.setAttribute('aria-hidden', 'true');
            pendingForm = null;
            lastTrigger?.focus();
        }

        function openConfirmation(action, trigger) {
            lastTrigger = trigger;
            const isDelete = action === 'delete';
            pendingForm = isDelete ? clearAllForm : markAllReadForm;
            dialogTitle.textContent = isDelete ? 'Delete all notifications?' : 'Mark all notifications as read?';
            dialogMessage.textContent = isDelete
                ? 'This permanently deletes all guest messages and their replies. This action cannot be undone.'
                : 'This will mark every notification as read. Reply status will not change.';
            confirmButton.textContent = isDelete ? 'Yes, delete all' : 'Yes, mark all read';
            confirmButton.classList.toggle('bg-red-600', isDelete);
            confirmButton.classList.toggle('hover:bg-red-700', isDelete);
            confirmButton.classList.toggle('bg-orange-600', !isDelete);
            confirmButton.classList.toggle('hover:bg-orange-700', !isDelete);
            dialog.classList.remove('hidden');
            dialog.classList.add('flex');
            dialog.setAttribute('aria-hidden', 'false');
            cancelButton.focus();
        }

        markAllReadButton.addEventListener('click', () => openConfirmation('read', markAllReadButton));
        clearAllButton.addEventListener('click', () => openConfirmation('delete', clearAllButton));
        cancelButton.addEventListener('click', closeConfirmation);
        dialog.addEventListener('click', event => {
            if (event.target === dialog) closeConfirmation();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !dialog.classList.contains('hidden')) closeConfirmation();
        });
        confirmButton.addEventListener('click', () => pendingForm?.submit());
    });
</script>
@endsection
