@extends('housekeeping.layout')

@section('content')

@php
    $messages = $messages ?? collect();
    $dateFilter = $dateFilter ?? 'today';
@endphp

<style>
    .messages-page {
        width: 100%;
        min-height: 100%;
        padding: 28px;
        background: #f5f6f8;
        font-family: 'Poppins', sans-serif;
    }

    .messages-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .messages-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #800000 0%, #650000 55%, #4d0000 100%);
        color: #fff;
        border-radius: 22px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 12px 30px rgba(80, 0, 0, 0.15);
    }

    .messages-hero::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        top: -110px;
        right: 80px;
    }

    .messages-hero::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255,107,53,0.12);
        bottom: -80px;
        right: -30px;
    }

    .messages-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 25px;
    }

    .hero-label {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #f7c8b6;
        margin-bottom: 8px;
    }

    .messages-hero h2 {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.2;
    }

    .messages-hero-description {
        margin: 9px 0 0;
        font-size: 14px;
        color: #eadede;
        max-width: 650px;
    }

    .message-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .message-stat-card {
        position: relative;
        overflow: hidden;
        background: #fff;
        border-radius: 18px;
        padding: 23px;
        border: 1px solid #e8eaed;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.05);
        transition: 0.25s ease;
    }

    .message-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.09);
    }

    .message-stat-card::after {
        content: "";
        position: absolute;
        width: 85px;
        height: 85px;
        border-radius: 50%;
        right: -35px;
        top: -35px;
        opacity: 0.08;
    }

    .stat-unread {
        border-top: 4px solid #f59e0b;
    }

    .stat-unread::after {
        background: #f59e0b;
    }

    .stat-replied {
        border-top: 4px solid #10b981;
    }

    .stat-replied::after {
        background: #10b981;
    }

    .stat-total-msg {
        border-top: 4px solid #3b82f6;
    }

    .stat-total-msg::after {
        background: #3b82f6;
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
    }

    .stat-label {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .stat-number {
        margin: 8px 0 0;
        color: #1f2937;
        font-size: 32px;
        font-weight: 700;
        line-height: 1;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .stat-icon.unread {
        color: #b45309;
        background: #fef3c7;
    }

    .stat-icon.replied {
        color: #047857;
        background: #d1fae5;
    }

    .stat-icon.total {
        color: #1d4ed8;
        background: #dbeafe;
    }

    .stat-description {
        margin: 12px 0 0;
        color: #9ca3af;
        font-size: 12px;
    }

    .messages-filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .messages-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .message-panel {
        background: #fff;
        border: 1px solid #e7e9ed;
        border-radius: 20px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 20px 23px;
        border-bottom: 1px solid #edf0f2;
    }

    .panel-title {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .panel-title-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #fff1eb;
        color: #ff6b35;
        font-size: 15px;
    }

    .panel-title h3 {
        margin: 0;
        color: #1f2937;
        font-size: 16px;
        font-weight: 600;
    }

    .panel-date {
        color: #9ca3af;
        font-size: 12px;
        background: #f5f6f8;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid #e5eaf0;
        transition: 0.2s;
    }

    .panel-date:hover {
        background: #fff;
        border-color: #ff6b35;
    }

    .panel-body {
        padding: 25px;
    }

    .message-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .message-item {
        padding: 16px;
        background: #f8fafc;
        border: 1px solid #e5eaf0;
        border-radius: 12px;
        transition: 0.2s;
    }

    .message-item:hover {
        background: #fff;
        border-color: #ff6b35;
        box-shadow: 0 3px 10px rgba(255, 107, 53, 0.1);
    }

    .message-item.unread {
        background: #fffbf7;
        border-color: #ffe5d9;
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 8px;
    }

    .message-sender {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #ff6b35;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 12px;
        flex-shrink: 0;
    }

    .message-sender-info {
        flex: 1;
        min-width: 0;
    }

    .message-sender-name {
        display: block;
        color: #1f2937;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .message-sender-room {
        display: block;
        color: #8a94a6;
        font-size: 11px;
    }

    .message-time {
        color: #8a94a6;
        font-size: 12px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .message-status {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f59e0b;
        flex-shrink: 0;
    }

    .message-status.replied {
        background: #10b981;
    }

    .message-content {
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .message-actions {
        display: flex;
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid #e5eaf0;
    }

    .message-action-btn {
        flex: 1;
        padding: 6px 10px;
        background: #fff;
        border: 1px solid #e5eaf0;
        border-radius: 6px;
        color: #ff6b35;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .message-action-btn:hover {
        background: #fff1eb;
        border-color: #ff6b35;
    }

    .empty-state {
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 1px dashed #d6d9de;
        background: #fafbfc;
        border-radius: 15px;
        padding: 35px 20px;
    }

    .empty-state-content {
        max-width: 380px;
    }

    .empty-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff1eb;
        color: #ff6b35;
        font-size: 24px;
    }

    .empty-state h4 {
        margin: 0;
        color: #374151;
        font-size: 16px;
        font-weight: 600;
    }

    .empty-state p {
        margin: 7px auto 0;
        color: #9ca3af;
        font-size: 15px;
        line-height: 1.6;
    }

    .side-stack {
        display: grid;
        gap: 14px;
        align-content: start;
    }

    .quick-filter {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-btn {
        padding: 12px;
        background: #fff;
        border: 1px solid #e5eaf0;
        border-radius: 10px;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: #fff1eb;
        border-color: #ff6b35;
        color: #ff6b35;
    }

    .reply-box {
        padding: 16px;
        background: #fff;
        border: 1px solid #e5eaf0;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .reply-box h4 {
        margin: 0;
        color: #1f2937;
        font-size: 13px;
        font-weight: 600;
    }

    .reply-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .reply-form-label {
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .reply-form-input,
    .reply-form-select {
        padding: 8px 10px;
        border: 1px solid #dfe3e8;
        border-radius: 6px;
        font-family: inherit;
        font-size: 13px;
        background: #fff;
        color: #374151;
    }

    .reply-form-input:focus,
    .reply-form-select:focus {
        outline: none;
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
    }

    .reply-textarea {
        width: 100%;
        min-height: 80px;
        padding: 10px;
        border: 1px solid #dfe3e8;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        resize: vertical;
        box-sizing: border-box;
    }

    .reply-textarea:focus {
        outline: none;
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
    }

    .reply-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e5eaf0;
    }

    .reply-btn {
        flex: 1;
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .reply-btn.primary {
        background: linear-gradient(90deg, #ff6b35, #e85b28);
        color: #fff;
    }

    .reply-btn.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(255, 107, 53, 0.2);
    }

    .reply-btn.secondary {
        background: #fff;
        color: #475569;
        border: 1px solid #e5eaf0;
    }

    .reply-btn.secondary:hover {
        background: #f8f9fa;
    }

    .template-buttons {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .template-btn {
        padding: 8px 10px;
        background: #f8fafc;
        border: 1px solid #e5eaf0;
        border-radius: 6px;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .template-btn:hover {
        background: #fff1eb;
        border-color: #ff6b35;
        color: #ff6b35;
    }

    @media (max-width: 1100px) {
        .messages-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 650px) {
        .messages-page {
            padding: 18px 14px;
        }

        .messages-hero {
            padding: 23px 20px;
            border-radius: 17px;
        }

        .messages-hero h2 {
            font-size: 24px;
        }

        .messages-hero-description {
            font-size: 13px;
        }

        .message-stats {
            grid-template-columns: 1fr;
        }

        .messages-hero-content {
            flex-direction: column;
            align-items: stretch;
        }

        .messages-filter-bar {
            flex-direction: column;
            gap: 8px;
        }

        .filter-btn {
            width: 100%;
        }

        .messages-grid {
            grid-template-columns: 1fr;
        }

        .panel-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .panel-body {
            padding: 16px;
        }
    }
</style>

<main class="messages-page">

    <div class="messages-container">

        <section class="messages-hero">

            <div class="messages-hero-content">

                <div>
                    <span class="hero-label">Housekeeping Desk</span>

                    <h2>Guest Messages</h2>

                    <p class="messages-hero-description">
                        Stay connected with guest messages and provide timely support.
                    </p>
                </div>

            </div>

        </section>

        <section class="message-stats">

            <div class="message-stat-card stat-unread">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Unread Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['unread'] ?? 0 }}
                        </h2>
                    </div>

                    <div class="stat-icon unread">
                        <i class="fas fa-envelope"></i>
                    </div>

                </div>

                <p class="stat-description">
                    Messages awaiting your response
                </p>

            </div>

            <div class="message-stat-card stat-replied">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Replied Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['replied'] ?? 0 }}
                        </h2>
                    </div>

                    <div class="stat-icon replied">
                        <i class="fas fa-check-circle"></i>
                    </div>

                </div>

                <p class="stat-description">
                    Messages you've already responded to
                </p>

            </div>

            <div class="message-stat-card stat-total-msg">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Total Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['total'] ?? 0 }}
                        </h2>
                    </div>

                    <div class="stat-icon total">
                        <i class="fas fa-comments"></i>
                    </div>

                </div>

                <p class="stat-description">
                    All guest messages received
                </p>

            </div>

        </section>

        <section class="messages-filter-bar">
            <button type="button" class="filter-btn active" onclick="filterMessages('today')">
                <i class="fas fa-clock"></i> Today
            </button>
            <button type="button" class="filter-btn" onclick="filterMessages('week')">
                <i class="fas fa-calendar-week"></i> This Week
            </button>
            <button type="button" class="filter-btn" onclick="filterMessages('all')">
                <i class="fas fa-list"></i> All Messages
            </button>
            <button type="button" class="filter-btn" onclick="filterMessages('unread')">
                <i class="fas fa-star"></i> Unread
            </button>
        </section>

        <section class="messages-grid">

            <div class="message-panel">

                <div class="panel-header">

                    <div class="panel-title">
                        <div class="panel-title-icon"><i class="fas fa-comment-dots"></i></div>
                        <h3>Guest Message Box</h3>
                    </div>

                </div>

                <div class="panel-body">
                    @if($messages->count() > 0)
                        <div class="message-list">
                            @foreach($messages as $message)
                                <div class="message-item @if(!$message->is_replied) unread @endif">
                                    <div class="message-header">
                                        <div class="message-sender">
                                            <div class="message-avatar">
                                                {{ strtoupper(substr($message->customer_name ?? 'G', 0, 1)) }}
                                            </div>
                                            <div class="message-sender-info">
                                                <span class="message-sender-name">{{ $message->customer_name ?? 'Guest' }}</span>
                                                <span class="message-sender-room">{{ $message->customer_email ?? 'No email' }}</span>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="message-time">
                                                {{ $message->created_at ? $message->created_at->format('M d, Y') : '—' }}
                                            </span>
                                            <span class="message-status @if($message->is_replied) replied @endif"></span>
                                        </div>
                                    </div>
                                    <div class="message-content">
                                        {{ $message->message ?? 'No message content' }}
                                    </div>
                                    <div class="message-actions">
                                        <button type="button" class="message-action-btn" onclick="replyToMessage({{ $message->id }})">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        <button type="button" class="message-action-btn" onclick="deleteMessage({{ $message->id }})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-content">
                                <div class="empty-icon"><i class="fas fa-comments"></i></div>
                                <h4>No new messages</h4>
                                <p>You're all caught up!</p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="message-panel">
                <div class="panel-header">
                    <div class="panel-title">
                        <div class="panel-title-icon"><i class="fas fa-pen-square"></i></div>
                        <h3>Quick Reply</h3>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="reply-box">
                        <h4>Compose Message</h4>
                        
                        <!-- Guest/Recipient Selector -->
                        <div class="reply-form-group">
                            <label class="reply-form-label">Send To</label>
                            <select id="recipientGuest" class="reply-form-select">
                                <option value="">Select a guest...</option>
                                @foreach($messages as $msg)
                                    <option value="{{ $msg->id }}">{{ $msg->customer_name ?? 'Guest' }} ({{ $msg->customer_email ?? 'No email' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Message Templates -->
                        <div class="reply-form-group">
                            <label class="reply-form-label">Quick Templates</label>
                            <div class="template-buttons">
                                <button type="button" class="template-btn" onclick="insertTemplate('We will assist you shortly.')">Assisting Soon</button>
                                <button type="button" class="template-btn" onclick="insertTemplate('Your request has been completed.')">Completed</button>
                                <button type="button" class="template-btn" onclick="insertTemplate('Please contact front desk for assistance.')">Front Desk</button>
                                <button type="button" class="template-btn" onclick="insertTemplate('Thank you for your message.')">Thank You</button>
                            </div>
                        </div>

                        <!-- Message Textarea -->
                        <div class="reply-form-group">
                            <label class="reply-form-label">Message</label>
                            <textarea id="replyMessage" class="reply-textarea" placeholder="Type your message here..." style="min-height: 120px;"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="reply-actions">
                            <button type="button" class="reply-btn secondary" onclick="clearReply()">Clear</button>
                            <button type="button" class="reply-btn primary" onclick="sendReply()">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </div>

</main>

<script>
    function replyToMessage(messageId) {
        const messageBox = document.getElementById('replyMessage');
        if (messageBox) {
            messageBox.focus();
            messageBox.placeholder = `Replying to message #${messageId}...`;
        }
    }

    function deleteMessage(messageId) {
        if (confirm('Are you sure you want to delete this message?')) {
            // TODO: Implement delete functionality
            alert('Message deleted successfully.');
            location.reload();
        }
    }

    function filterMessages(filter) {
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.closest('.filter-btn').classList.add('active');
        // TODO: Implement filter functionality
    }

    function insertTemplate(text) {
        const textarea = document.getElementById('replyMessage');
        if (textarea.value) {
            textarea.value += '\n\n' + text;
        } else {
            textarea.value = text;
        }
        textarea.focus();
    }

    function clearReply() {
        document.getElementById('replyMessage').value = '';
        document.getElementById('replyMessage').placeholder = 'Type your message here...';
        document.getElementById('recipientGuest').value = '';
    }

    function sendReply() {
        const message = document.getElementById('replyMessage').value;
        const recipient = document.getElementById('recipientGuest').value;

        if (!recipient) {
            alert('Please select a guest to send the message to.');
            return;
        }

        if (!message.trim()) {
            alert('Please enter a message before sending.');
            return;
        }

        // TODO: Implement send functionality
        alert(`Message sent to guest #${recipient}`);
        clearReply();
    }
</script>

@endsection
