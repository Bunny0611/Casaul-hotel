@extends('housekeeping.layout')

@section('content')

@php
    $stats = [
        'pending' => 0,
        'resolved' => 0,
        'total' => 0,
    ];
@endphp

<style>

    .guest-request-page {
        width: 100%;
        min-height: 100%;
        padding: 28px;
        background: #f5f6f8;
        font-family: 'Poppins', sans-serif;
    }

    .guest-request-container {
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .request-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #800000 0%, #650000 55%, #4d0000 100%);
        color: #fff;
        border-radius: 22px;
        padding: 32px;
        margin-bottom: 24px;
        box-shadow: 0 12px 30px rgba(80, 0, 0, 0.15);
    }

    .request-hero::before {
        content: "";
        position: absolute;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        top: -110px;
        right: 80px;
    }

    .request-hero::after {
        content: "";
        position: absolute;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: rgba(255,107,53,0.12);
        bottom: -80px;
        right: -30px;
    }

    .request-hero-content {
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

    .request-hero h2 {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.2;
    }

    .request-hero-description {
        margin: 9px 0 0;
        font-size: 14px;
        color: #eadede;
        max-width: 650px;
    }

    .active-request-box {
        min-width: 155px;
        padding: 16px 20px;
        text-align: center;
        border-radius: 16px;
        background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
    }

    .active-request-box span {
        display: block;
        color: #e7dcdc;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .active-request-box strong {
        display: block;
        font-size: 30px;
        line-height: 1;
    }

    .request-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .request-stat-card {
        position: relative;
        overflow: hidden;
        background: #fff;
        border-radius: 18px;
        padding: 23px;
        border: 1px solid #e8eaed;
        box-shadow: 0 5px 18px rgba(15, 23, 42, 0.05);
        transition: 0.25s ease;
    }

    .request-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.09);
    }

    .request-stat-card::after {
        content: "";
        position: absolute;
        width: 85px;
        height: 85px;
        border-radius: 50%;
        right: -35px;
        top: -35px;
        opacity: 0.08;
    }

    .stat-pending {
        border-top: 4px solid #f59e0b;
    }

    .stat-pending::after {
        background: #f59e0b;
    }

    .stat-resolved {
        border-top: 4px solid #10b981;
    }

    .stat-resolved::after {
        background: #10b981;
    }

    .stat-total {
        border-top: 4px solid #3b82f6;
    }

    .stat-total::after {
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

    .stat-icon.pending {
        color: #b45309;
        background: #fef3c7;
    }

    .stat-icon.resolved {
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

    .request-content-grid {
        display: grid;
        grid-template-columns: 1.25fr 0.75fr;
        gap: 20px;
    }

    .request-panel {
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
    }

    .panel-body {
        padding: 25px;
    }

    .empty-state {
        min-height: 270px;
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

    .notification-panel .empty-icon {
        background: #eef4ff;
        color: #3b82f6;
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
        font-size: 13px;
        line-height: 1.6;
    }

    #requestModal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.62);
        backdrop-filter: blur(4px);
        z-index: 9999;
    }

    #requestModal.show {
        display: flex;
    }

    .request-modal {
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0,0,0,0.25);
        animation: modalIn 0.2s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(15px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 25px;
        border-bottom: 1px solid #edf0f2;
    }

    .modal-header h2 {
        margin: 0;
        color: #1f2937;
        font-size: 20px;
        font-weight: 700;
    }

    .modal-close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #9ca3af;
        font-size: 25px;
        cursor: pointer;
        transition: 0.2s;
    }

    .modal-close:hover {
        background: #fff1eb;
        color: #dc2626;
    }

    .request-form {
        padding: 25px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 17px;
        margin-bottom: 17px;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .form-label {
        display: block;
        margin-bottom: 7px;
        color: #4b5563;
        font-size: 13px;
        font-weight: 600;
    }

    .request-form input,
    .request-form select,
    .request-form textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #dfe3e8;
        border-radius: 10px;
        padding: 11px 13px;
        color: #374151;
        background: #fff;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        transition: 0.2s;
    }

    .request-form input:focus,
    .request-form select:focus,
    .request-form textarea:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.12);
    }

    .request-form textarea {
        resize: vertical;
        min-height: 110px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 8px;
        padding-top: 20px;
        border-top: 1px solid #edf0f2;
    }

    .btn-cancel,
    .btn-send {
        border-radius: 9px;
        padding: 10px 19px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-cancel {
        color: #4b5563;
        background: #fff;
        border: 1px solid #dfe3e8;
    }

    .btn-cancel:hover {
        background: #f8f9fa;
    }

    .btn-send {
        color: #fff;
        background: linear-gradient(135deg, #ff6b35, #e85b28);
        border: 0;
        box-shadow: 0 5px 12px rgba(255,107,53,0.2);
    }

    .btn-send:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 16px rgba(255,107,53,0.28);
    }

    @media (max-width: 1100px) {
        .request-content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 850px) {
        .request-stats {
            grid-template-columns: 1fr;
        }

        .request-hero-content {
            align-items: stretch;
            flex-direction: column;
        }

        .active-request-box {
            width: 100%;
            box-sizing: border-box;
        }
    }

    @media (max-width: 650px) {
        .guest-request-page {
            padding: 18px 14px;
        }

        .request-hero {
            padding: 23px 20px;
            border-radius: 17px;
        }

        .request-hero h2 {
            font-size: 24px;
        }

        .request-hero-description {
            font-size: 13px;
        }

        .request-stat-card {
            padding: 20px;
        }

        .request-content-grid {
            gap: 15px;
        }

        .panel-header {
            padding: 17px;
        }

        .panel-body {
            padding: 17px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .request-form {
            padding: 18px;
        }

        .modal-actions {
            flex-direction: column-reverse;
        }

        .btn-cancel,
        .btn-send {
            width: 100%;
        }
    }
</style>


<main class="guest-request-page">

    <div class="guest-request-container">

        <section class="request-hero">

            <div class="request-hero-content">

                <div>
                    <span class="hero-label">Housekeeping Desk</span>

                    <h2>Guest Requests</h2>

                    <p class="request-hero-description">
                        A clear view of guest messages and service needs for the day.
                    </p>
                </div>

                <div class="active-request-box">
                    <span>Active Requests</span>
                    <strong>{{ $stats['pending'] }}</strong>
                </div>

            </div>

        </section>

        <section class="request-stats">

            <div class="request-stat-card stat-pending">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Pending Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['pending'] }}
                        </h2>
                    </div>

                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>

                </div>

                <p class="stat-description">
                    Requests waiting for housekeeping action
                </p>

            </div>

            <div class="request-stat-card stat-resolved">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Resolved Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['resolved'] }}
                        </h2>
                    </div>

                    <div class="stat-icon resolved">
                        <i class="fas fa-check-circle"></i>
                    </div>

                </div>

                <p class="stat-description">
                    Guest requests successfully completed
                </p>

            </div>

            <div class="request-stat-card stat-total">

                <div class="stat-top">

                    <div>
                        <p class="stat-label">Total Messages</p>

                        <h2 class="stat-number">
                            {{ $stats['total'] }}
                        </h2>
                    </div>

                    <div class="stat-icon total">
                        <i class="fas fa-comments"></i>
                    </div>

                </div>

                <p class="stat-description">
                    All guest requests received today
                </p>

            </div>

        </section>

        <section class="request-content-grid">


            <div class="request-panel">

                <div class="panel-header">

                    <div class="panel-title">

                        <div class="panel-title-icon">
                            <i class="fas fa-comment-dots"></i>
                        </div>

                        <h3>Guest Message Box</h3>

                    </div>

                    <span class="panel-date">
                        <i class="far fa-calendar-alt"></i>
                        Today
                    </span>

                </div>


                <div class="panel-body">

                    <div class="empty-state">

                        <div class="empty-state-content">

                            <div class="empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>

                            <h4>No Guest Messages Yet</h4>

                            <p>
                                Requests from guests will appear here once they arrive.
                                New service requests can be reviewed and managed from this section.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div class="request-panel notification-panel">

                <div class="panel-header">

                    <div class="panel-title">

                        <div class="panel-title-icon">
                            <i class="fas fa-bell"></i>
                        </div>

                        <h3>Notification Center</h3>

                    </div>

                    <span class="panel-date">
                        <i class="fas fa-circle" style="font-size:7px;color:#22c55e;"></i>
                        Live
                    </span>

                </div>


                <div class="panel-body">

                    <div class="empty-state">

                        <div class="empty-state-content">

                            <div class="empty-icon">
                                <i class="fas fa-bell-slash"></i>
                            </div>

                            <h4>No Notifications Yet</h4>

                            <p>
                                Housekeeping notifications will appear here when action is required.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


        </section>

    </div>

</main>


<div id="requestModal">

    <div class="request-modal">

        <div class="modal-header">

            <h2>
                <i class="fas fa-paper-plane" style="color:#ff6b35;margin-right:8px;"></i>
                Send New Guest Message
            </h2>

            <button
                type="button"
                onclick="closeRequestModal()"
                class="modal-close">

                &times;

            </button>

        </div>


        <form class="request-form">

            <div class="form-grid">

                <div>
                    <label class="form-label">
                        Guest Name
                    </label>

                    <input
                        type="text"
                        placeholder="Enter guest name">
                </div>


                <div>
                    <label class="form-label">
                        Room Number
                    </label>

                    <input
                        type="text"
                        placeholder="e.g. 101">
                </div>

            </div>


            <div class="form-group">

                <label class="form-label">
                    Request Type
                </label>

                <select>

                    <option>Extra Pillows</option>
                    <option>Towels</option>
                    <option>Amenities</option>
                    <option>Drinking Water</option>

                </select>

            </div>


            <div class="form-group">

                <label class="form-label">
                    Guest Message
                </label>

                <textarea
                    placeholder="Write the guest's message here..."></textarea>

            </div>


            <div class="modal-actions">

                <button
                    type="button"
                    onclick="closeRequestModal()"
                    class="btn-cancel">

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn-send">

                    <i class="fas fa-paper-plane"></i>
                    Send Message

                </button>

            </div>

        </form>

    </div>

</div>


<script>

function openRequestModal() {

    const modal = document.getElementById('requestModal');

    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

}


function closeRequestModal() {

    const modal = document.getElementById('requestModal');

    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

}


document.getElementById('requestModal')?.addEventListener('click', function(event) {

    if (event.target === this) {
        closeRequestModal();
    }

});


document.addEventListener('keydown', function(event) {

    if (event.key === 'Escape') {
        closeRequestModal();
    }

});

</script>

@endsection
