@extends('housekeeping.layout')

@section('content')

<style>

    .assigned-page {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        animation: pageFade .35s ease;
    }

    @keyframes pageFade {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .assigned-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .page-title-area h2 {
        margin: 0;
        color: #242424;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: -.5px;
    }

    .page-title-area p {
        margin: 6px 0 0;
        color: #777;
        font-size: 14px;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .priority-select {
        height: 44px;
        min-width: 160px;
        padding: 0 38px 0 14px;
        border: 1px solid #ddd;
        border-radius: 10px;
        background-color: #fff;
        color: #444;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .priority-select:hover {
        border-color: #bbb;
    }

    .priority-select:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, .12);
    }


    .btn-primary {
        height: 44px;
        padding: 0 18px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #ff6b35, #e95420);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 5px 14px rgba(233, 84, 32, .22);
        transition: .2s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 18px rgba(233, 84, 32, .28);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .task-card {
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .task-card-header {
        padding: 22px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-bottom: 1px solid #eee;
    }

    .task-card-title h3 {
        margin: 0;
        color: #252525;
        font-size: 18px;
        font-weight: 700;
    }

    .task-card-title p {
        margin: 5px 0 0;
        color: #888;
        font-size: 13px;
    }

    .task-count {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 0 13px;
        border-radius: 20px;
        background: #fff3ed;
        color: #d9531e;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .task-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1050px;
    }

    .task-table thead {
        background: #fafafa;
    }

    .task-table th {
        padding: 14px 16px;
        text-align: left;
        color: #777;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid #eee;
        white-space: nowrap;
    }

    .task-table td {
        padding: 15px 16px;
        border-bottom: 1px solid #f0f0f0;
        color: #444;
        font-size: 13px;
        vertical-align: middle;
    }

    .task-table tbody tr {
        transition: background .2s ease;
    }

    .task-table tbody tr:hover {
        background: #fffaf7;
    }

    .room-number {
        color: #262626;
        font-size: 14px;
        font-weight: 700;
    }

    .room-meta {
        margin-top: 4px;
        color: #999;
        font-size: 11px;
    }

    .task-name {
        color: #333;
        font-weight: 600;
        line-height: 1.4;
    }

    .task-note {
        margin-top: 4px;
        max-width: 220px;
        color: #999;
        font-size: 11px;
        line-height: 1.4;
    }


    .staff-wrapper {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 150px;
    }

    .staff-avatar {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 50%;
        background: #f3f3f3;
        color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .staff-name {
        color: #333;
        font-size: 13px;
        font-weight: 600;
    }

    .staff-label {
        margin-top: 2px;
        color: #aaa;
        font-size: 10px;
    }

    .priority-badge,
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .priority-high {
        background: #fff0f0;
        color: #d93636;
    }

    .priority-medium {
        background: #fff8df;
        color: #a87500;
    }

    .priority-urgent {
        background: #fff0e8;
        color: #e35c16;
    }

    .priority-low {
        background: #edf9f0;
        color: #258342;
    }

    .status-pending {
        background: #fff7df;
        color: #a36e00;
    }

    .status-cleaning {
        background: #eaf4ff;
        color: #1769aa;
    }

    .status-completed {
        background: #eaf8ee;
        color: #278346;
    }

    .time-text {
        color: #999;
        font-size: 12px;
        white-space: nowrap;
    }

    .time-started {
        color: #1769aa;
        font-weight: 600;
    }

    .time-finished {
        color: #278346;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .action-btn {
        min-width: 70px;
        height: 34px;
        padding: 0 11px;
        border-radius: 7px;
        border: 1px solid transparent;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
        white-space: nowrap;
    }

    .start-btn {
        background: #2878d4;
        color: #fff;
    }

    .start-btn:hover {
        background: #1767c0;
    }

    .complete-btn {
        background: #2e9b59;
        color: #fff;
    }

    .complete-btn:hover {
        background: #258349;
    }

    .delete-btn {
        background: #fff;
        border-color: #f0cccc;
        color: #d64545;
    }

    .delete-btn:hover {
        background: #fff5f5;
        border-color: #e7aaaa;
    }

    .finished-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #2e9b59;
        font-size: 12px;
        font-weight: 700;
    }

    .empty-state {
        padding: 55px 20px !important;
        text-align: center !important;
        color: #999 !important;
    }

    .empty-state-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: #f7f7f7;
        color: #aaa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .empty-state strong {
        display: block;
        color: #666;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .empty-state span {
        font-size: 12px;
    }


    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(25, 25, 25, .62);
        backdrop-filter: blur(3px);
    }

    .modal-overlay.show {
        display: flex;
    }

    .assignment-modal {
        width: 100%;
        max-width: 900px;
        max-height: 92vh;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
        overflow: hidden;
        animation: modalOpen .25s ease;
    }

    @keyframes modalOpen {
        from {
            opacity: 0;
            transform: translateY(15px) scale(.98);
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
        border-bottom: 1px solid #eee;
        background: #fff;
    }

    .modal-heading {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-heading-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #fff1ea;
        color: #ef6228;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-heading h2 {
        margin: 0;
        color: #252525;
        font-size: 20px;
        font-weight: 700;
    }

    .modal-heading p {
        margin: 3px 0 0;
        color: #999;
        font-size: 11px;
    }

    .modal-close {
        width: 35px;
        height: 35px;
        border: 0;
        border-radius: 8px;
        background: #f7f7f7;
        color: #777;
        font-size: 21px;
        cursor: pointer;
        transition: .2s ease;
    }

    .modal-close:hover {
        background: #fff0f0;
        color: #d93636;
    }

    .modal-body {
        max-height: calc(92vh - 85px);
        overflow-y: auto;
        padding: 25px;
    }

    .form-section {
        margin-bottom: 28px;
    }

    .form-section:last-of-type {
        margin-bottom: 10px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 15px;
        padding-bottom: 9px;
        border-bottom: 1px solid #f0f0f0;
    }

    .section-number {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #fff1ea;
        color: #e75e27;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .section-heading h3 {
        margin: 0;
        color: #333;
        font-size: 14px;
        font-weight: 700;
    }

    .form-grid {
        display: grid;
        gap: 16px;
    }

    .form-grid-3 {
        grid-template-columns: repeat(3, 1fr);
    }

    .form-grid-4 {
        grid-template-columns: repeat(4, 1fr);
    }

    .form-field label {
        display: block;
        margin-bottom: 7px;
        color: #555;
        font-size: 12px;
        font-weight: 600;
    }

    .form-control {
        width: 100%;
        height: 43px;
        padding: 0 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        color: #333;
        font-size: 13px;
        outline: none;
        transition: .2s ease;
    }

    .form-control:hover {
        border-color: #c9c9c9;
    }

    .form-control:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, .10);
    }

    textarea.form-control {
        height: auto;
        min-height: 105px;
        padding: 12px;
        resize: vertical;
    }

    .choice-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }

    .choice-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 9px;
        min-height: 43px;
        padding: 10px 12px;
        border: 1px solid #e2e2e2;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        color: #555;
        font-size: 12px;
        transition: .2s ease;
    }

    .choice-card:hover {
        border-color: #ffb08e;
        background: #fffaf7;
    }

    .choice-card input {
        width: 15px;
        height: 15px;
        accent-color: #ff6b35;
        cursor: pointer;
    }

    .priority-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .priority-option {
        position: relative;
    }

    .priority-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .priority-option label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        border: 1px solid #ddd;
        border-radius: 8px;
        color: #666;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
    }

    .priority-option input:checked + label {
        border-color: #ff6b35;
        background: #fff3ed;
        color: #e75e27;
        box-shadow: 0 0 0 2px rgba(255, 107, 53, .08);
    }


    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        margin-top: 15px;
        border-top: 1px solid #eee;
    }

    .btn-secondary {
        height: 43px;
        padding: 0 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        color: #666;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-secondary:hover {
        background: #f7f7f7;
    }

    .btn-submit {
        height: 43px;
        padding: 0 20px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(135deg, #ff6b35, #e95420);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(233, 84, 32, .2);
        transition: .2s ease;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 1100px) {
        .assigned-header {
            align-items: flex-start;
        }

        .form-grid-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .choice-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .priority-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .assigned-header {
            flex-direction: column;
        }

        .header-actions {
            width: 100%;
        }

        .priority-select,
        .btn-primary {
            flex: 1;
        }

        .task-card-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 18px;
        }

        .task-card {
            border-radius: 12px;
        }

        .modal-overlay {
            padding: 10px;
        }

        .assignment-modal {
            max-height: 95vh;
            border-radius: 14px;
        }

        .modal-header {
            padding: 16px;
        }

        .modal-body {
            padding: 18px;
            max-height: calc(95vh - 75px);
        }

        .form-grid-3,
        .form-grid-4,
        .choice-grid {
            grid-template-columns: 1fr;
        }

        .priority-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 520px) {
        .page-title-area h2 {
            font-size: 24px;
        }

        .header-actions {
            flex-direction: column;
        }

        .priority-select,
        .btn-primary {
            width: 100%;
        }

        .modal-footer {
            flex-direction: column-reverse;
        }

        .btn-secondary,
        .btn-submit {
            width: 100%;
        }

        .priority-options {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>


<div class="assigned-page">

    <div class="assigned-header">

        <div class="page-title-area">
            <h2>Assigned Rooms</h2>
            <p>Manage and monitor housekeeping cleaning assignments.</p>
        </div>

        <div class="header-actions">

            <select id="priorityFilter" class="priority-select">
                <option value="all">All Priority</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
                <option value="urgent">Urgent</option>
            </select>

            <button type="button" onclick="openModal()" class="btn-primary">
                <i class="fas fa-plus"></i>
                Assign Room Task
            </button>

        </div>

    </div>

    <div class="task-card">

        <div class="task-card-header">

            <div class="task-card-title">
                <h3>Assigned Cleaning Tasks</h3>
                <p>
                    Track cleaning start and completion time for each assigned room.
                </p>
            </div>

            <span id="taskCount" class="task-count">
                Total: 0 Rooms
            </span>

        </div>


        <div class="table-wrapper">

            <table class="task-table">

                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Assigned Staff</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Start Time</th>
                        <th>Finish Time</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr id="noTasksRow">

                        <td colspan="9" class="empty-state">

                            <div class="empty-state-icon">
                                <i class="fas fa-bed"></i>
                            </div>

                            <strong>No assigned rooms</strong>

                            <span>
                                Assign a cleaning task to see it here.
                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>



<div id="assignModal" class="modal-overlay">

    <div class="assignment-modal">

        <div class="modal-header">

            <div class="modal-heading">

                <div class="modal-heading-icon">
                    <i class="fas fa-broom"></i>
                </div>

                <div>
                    <h2>Assign Cleaning Task</h2>
                    <p>Create a new housekeeping room assignment.</p>
                </div>

            </div>

            <button type="button"
                    onclick="closeModal()"
                    class="modal-close">
                &times;
            </button>

        </div>


 

        <div class="modal-body">

            <form id="assignTaskForm">

                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">1</span>
                        <h3>Guest Information</h3>
                    </div>

                    <div class="form-grid form-grid-3">

                        <div class="form-field">

                            <label for="guestName">
                                Guest Name
                            </label>

                            <input
                                id="guestName"
                                type="text"
                                placeholder="John Smith"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="form-field">

                            <label for="contactNumber">
                                Contact Number
                            </label>

                            <input
                                id="contactNumber"
                                type="text"
                                placeholder="09123456789"
                                class="form-control"
                            >

                        </div>


                        <div class="form-field">

                            <label for="bookingReference">
                                Booking Reference No.
                            </label>

                            <input
                                id="bookingReference"
                                type="text"
                                placeholder="BK-20260731-001"
                                class="form-control"
                            >

                        </div>

                    </div>

                </div>

                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">2</span>
                        <h3>Room Assignment</h3>
                    </div>

                    <div class="form-grid form-grid-4">

                        <div class="form-field">

                            <label for="roomNumber">
                                Room Number
                            </label>

                            <input
                                id="roomNumber"
                                name="roomNumber"
                                type="text"
                                placeholder="101"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="form-field">

                            <label for="roomType">
                                Room Type
                            </label>

                            <select
                                id="roomType"
                                name="roomType"
                                class="form-control"
                            >
                                <option value="Deluxe Room">
                                    Deluxe Room
                                </option>

                                <option value="Suite Room">
                                    Suite Room
                                </option>

                                <option value="Standard Room">
                                    Standard Room
                                </option>
                            </select>

                        </div>


                        <div class="form-field">

                            <label for="floor">
                                Floor
                            </label>

                            <select
                                id="floor"
                                name="floor"
                                class="form-control"
                            >
                                <option value="1st Floor">
                                    1st Floor
                                </option>

                                <option value="2nd Floor">
                                    2nd Floor
                                </option>

                                <option value="3rd Floor">
                                    3rd Floor
                                </option>
                            </select>

                        </div>
                        <div class="form-field">

                            <label for="occupancy">
                                Occupancy
                            </label>

                            <input
                                id="occupancy"
                                name="occupancy"
                                type="text"
                                placeholder="2 Guests"
                                class="form-control"
                                required
                            >

                        </div>

                    </div>
                </div>


                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">3</span>
                        <h3>Cleaning Type</h3>
                    </div>

                    <div class="choice-grid">

                        <label class="choice-card">
                            <input
                                type="checkbox"
                                name="cleaningType[]"
                                value="Check-out Cleaning"
                            >
                            <span>Check-out Cleaning</span>
                        </label>


                        <label class="choice-card">
                            <input
                                type="checkbox"
                                name="cleaningType[]"
                                value="Daily Cleaning"
                            >
                            <span>Daily Cleaning</span>
                        </label>


                        <label class="choice-card">
                            <input
                                type="checkbox"
                                name="cleaningType[]"
                                value="Deep Cleaning"
                            >
                            <span>Deep Cleaning</span>
                        </label>


                        <label class="choice-card">
                            <input
                                type="checkbox"
                                name="cleaningType[]"
                                value="Room Inspection"
                            >
                            <span>Room Inspection</span>
                        </label>


                        <label class="choice-card">
                            <input
                                type="checkbox"
                                name="cleaningType[]"
                                value="Linen Replacement"
                            >
                            <span>Linen Replacement</span>
                        </label>

                    </div>

                </div>


                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">4</span>
                        <h3>Priority Level</h3>
                    </div>

                    <div class="priority-options">

                        <div class="priority-option">

                            <input
                                id="priorityLow"
                                type="radio"
                                name="priority"
                                value="low"
                            >

                            <label for="priorityLow">
                                Low
                            </label>

                        </div>


                        <div class="priority-option">

                            <input
                                id="priorityMedium"
                                type="radio"
                                name="priority"
                                value="medium"
                                checked
                            >

                            <label for="priorityMedium">
                                Medium
                            </label>

                        </div>


                        <div class="priority-option">

                            <input
                                id="priorityHigh"
                                type="radio"
                                name="priority"
                                value="high"
                            >

                            <label for="priorityHigh">
                                High
                            </label>

                        </div>


                        <div class="priority-option">

                            <input
                                id="priorityUrgent"
                                type="radio"
                                name="priority"
                                value="urgent"
                            >

                            <label for="priorityUrgent">
                                Urgent
                            </label>

                        </div>

                    </div>

                </div>


                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">5</span>
                        <h3>Schedule</h3>
                    </div>

                    <div class="form-grid form-grid-3">

                        <div class="form-field">

                            <label for="scheduleDate">
                                Schedule Date
                            </label>

                            <input
                                id="scheduleDate"
                                name="scheduleDate"
                                type="date"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="form-field">

                            <label for="scheduleTime">
                                Schedule Time
                            </label>

                            <input
                                id="scheduleTime"
                                name="scheduleTime"
                                type="time"
                                class="form-control"
                            >

                        </div>


                        <div class="form-field">

                            <label for="duration">
                                Estimated Duration
                            </label>

                            <input
                                id="duration"
                                name="duration"
                                type="text"
                                placeholder="45 Minutes"
                                class="form-control"
                            >

                        </div>

                    </div>

                </div>


                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">6</span>
                        <h3>Assigned Staff</h3>
                    </div>

                    <div class="form-field">

                        <label for="assignedStaff">
                            Housekeeping Staff
                        </label>

                        <input
                            id="assignedStaff"
                            name="assignedStaff"
                            type="text"
                            placeholder="Maria Santos"
                            class="form-control"
                                required
                        >

                    </div>

                </div>


                <div class="form-section">

                    <div class="section-heading">
                        <span class="section-number">7</span>
                        <h3>Special Instructions</h3>
                    </div>

                    <div class="form-field">

                        <label for="notes">
                            Additional Notes
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            class="form-control"
                            placeholder="Guest requested extra pillows and towels. Check minibar before arrival."
                        ></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        onclick="closeModal()"
                        class="btn-secondary"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn-submit"
                    >
                        <i class="fas fa-check"></i>
                        Assign Task
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

    function openModal() {
        const modal = document.getElementById('assignModal');

        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }


    function closeModal() {
        const modal = document.getElementById('assignModal');

        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }


    function formatTimestamp(date) {

        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });

    }


    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

    }


    function getPriorityLabel(priority) {

        switch (priority) {

            case 'high':
                return 'High';

            case 'medium':
                return 'Medium';

            case 'urgent':
                return 'Urgent';

            default:
                return 'Low';

        }

    }


    function getPriorityClass(priority) {

        switch (priority) {

            case 'high':
                return 'priority-badge priority-high';

            case 'medium':
                return 'priority-badge priority-medium';

            case 'urgent':
                return 'priority-badge priority-urgent';

            default:
                return 'priority-badge priority-low';

        }

    }


    function readStoredTasks() {

        try {

            return JSON.parse(
                localStorage.getItem('housekeeping-assigned-tasks') || '[]'
            );

        } catch (e) {

            return [];

        }

    }


    function saveStoredTasks(tasks) {

        localStorage.setItem(
            'housekeeping-assigned-tasks',
            JSON.stringify(tasks)
        );

    }



    function appendTaskRow(task) {

        const tbody = document.querySelector('.task-table tbody');
        const noTasksRow = document.getElementById('noTasksRow');

        if (!tbody) return;


        const row = document.createElement('tr');

        row.setAttribute('data-row-id', task.id);
        row.setAttribute('data-priority', task.priority);
        row.setAttribute('data-task-name', task.task);


        const initials = (task.assignedStaff || 'Staff')
            .split(' ')
            .map(part => part[0])
            .slice(0, 2)
            .join('')
            .toUpperCase();


        const priorityClass = getPriorityClass(task.priority);


        row.innerHTML = `

            <td>

                <div class="room-number">
                    ${escapeHtml(task.roomLabel)}
                </div>

                <div class="room-meta">
                    ${escapeHtml(task.roomType)}
                    ·
                    ${escapeHtml(task.occupancy)}
                </div>

            </td>


            <td>

                <div class="task-name">
                    ${escapeHtml(task.task)}
                </div>

                <div class="task-note">
                    ${escapeHtml(task.note || 'New task assigned')}
                </div>

            </td>


            <td>

                <span class="${priorityClass}">
                    ${escapeHtml(getPriorityLabel(task.priority))}
                </span>

            </td>


            <td>

                <div class="staff-wrapper">

                    <div class="staff-avatar">
                        ${escapeHtml(initials)}
                    </div>

                    <div>

                        <div class="staff-name">
                            ${escapeHtml(task.assignedStaff || 'Unassigned')}
                        </div>

                        <div class="staff-label">
                            Assigned staff
                        </div>

                    </div>

                </div>

            </td>


            <td>

                <span>
                    ${escapeHtml(task.day || 'Not set')}
                </span>

            </td>


            <td>

                <span
                    data-status
                    class="status-badge status-pending"
                >
                    Pending
                </span>

            </td>


            <td>

                <span
                    data-timestamp="start"
                    class="time-text"
                >
                    Not started
                </span>

            </td>


            <td>

                <span
                    data-timestamp="end"
                    class="time-text"
                >
                    —
                </span>

            </td>


            <td data-action-cell>

                <div class="action-buttons">

                    <button
                        type="button"
                        data-action="start"
                        onclick="startCleaning(this)"
                        class="action-btn start-btn"
                    >
                        Start
                    </button>


                    <button
                        type="button"
                        data-action="complete"
                        onclick="completeCleaning(this)"
                        class="action-btn complete-btn"
                        style="display:none;"
                    >
                        Complete
                    </button>


                    <button
                        type="button"
                        onclick="deleteTask(this)"
                        class="action-btn delete-btn"
                    >
                        Delete
                    </button>

                </div>

            </td>

        `;


        if (noTasksRow) {
            noTasksRow.style.display = 'none';
        }


        tbody.appendChild(row);

    }

    function renderStoredTasks() {

        const tasks = readStoredTasks();

        const tbody = document.querySelector('.task-table tbody');

        if (!tbody) return;


        tbody.querySelectorAll('tr[data-row-id]')
            .forEach(row => row.remove());


        tasks.forEach(task => {
            appendTaskRow(task);
        });


        document
            .querySelectorAll('.task-table tbody tr[data-row-id]')
            .forEach(row => applySavedTaskState(row));


        filterTasks();

    }


    function filterTasks() {

        const select = document.getElementById('priorityFilter');

        const rows = document.querySelectorAll(
            '.task-table tbody tr[data-priority]'
        );

        const noTasksRow = document.getElementById('noTasksRow');

        const taskCount = document.getElementById('taskCount');

        const selected = select
            ? select.value
            : 'all';


        let visibleCount = 0;


        rows.forEach(row => {

            const matches =
                selected === 'all' ||
                row.getAttribute('data-priority') === selected;


            row.style.display = matches
                ? ''
                : 'none';


            if (matches) {
                visibleCount++;
            }

        });


        if (noTasksRow) {

            noTasksRow.style.display =
                visibleCount === 0
                    ? ''
                    : 'none';

        }


        if (taskCount) {

            taskCount.textContent =
                `Total: ${visibleCount} Room${visibleCount === 1 ? '' : 's'}`;

        }

    }


    function getTaskStateKey(row) {

        return `housekeeping-task-${row.getAttribute('data-row-id')}`;

    }


    function saveTaskState(
        row,
        status,
        startTime = null,
        endTime = null
    ) {

        localStorage.setItem(
            getTaskStateKey(row),
            JSON.stringify({
                status,
                startTime,
                endTime
            })
        );

    }


    function readTaskState(row) {

        const saved =
            localStorage.getItem(getTaskStateKey(row));


        if (!saved) {
            return null;
        }


        try {

            return JSON.parse(saved);

        } catch (e) {

            return null;

        }

    }


    function formatStoredTime(value) {

        if (!value) {
            return '';
        }


        const dateValue = new Date(value);


        if (Number.isNaN(dateValue.getTime())) {
            return value;
        }


        return formatTimestamp(dateValue);

    }


    function applySavedTaskState(row) {

        const state = readTaskState(row);

        if (!state) return;


        const startCell =
            row.querySelector('[data-timestamp="start"]');

        const endCell =
            row.querySelector('[data-timestamp="end"]');

        const statusBadge =
            row.querySelector('[data-status]');

        const completeButton =
            row.querySelector('[data-action="complete"]');

        const startButton =
            row.querySelector('[data-action="start"]');

        const actionCell =
            row.querySelector('[data-action-cell]');


        if (startCell && state.startTime) {

            startCell.textContent =
                formatStoredTime(state.startTime);

            startCell.className =
                'time-text time-started';

        }


        if (endCell && state.endTime) {

            endCell.textContent =
                formatStoredTime(state.endTime);

            endCell.className =
                'time-text time-finished';

        }


        if (statusBadge) {

            if (state.status === 'completed') {

                statusBadge.textContent = 'Completed';

                statusBadge.className =
                    'status-badge status-completed';

            }


            else if (state.status === 'cleaning') {

                statusBadge.textContent = 'Cleaning';

                statusBadge.className =
                    'status-badge status-cleaning';

            }

        }


        if (state.status === 'completed') {

            if (actionCell) {

                actionCell.innerHTML = `

                    <span class="finished-label">
                        <i class="fas fa-check-circle"></i>
                        Finished
                    </span>

                `;

            }

        }


        else if (state.status === 'cleaning') {

            if (startButton) {

                startButton.textContent = 'Started';

                startButton.disabled = true;

                startButton.style.opacity = '.55';

                startButton.style.cursor = 'not-allowed';

            }


            if (completeButton) {

                completeButton.style.display = 'inline-flex';

            }

        }

    }


    function startCleaning(button) {

        const row = button.closest('tr');

        if (!row) return;


        const startCell =
            row.querySelector('[data-timestamp="start"]');

        const statusBadge =
            row.querySelector('[data-status]');

        const completeButton =
            row.querySelector('[data-action="complete"]');

        const startButton =
            row.querySelector('[data-action="start"]');


        const startTime = new Date();


        if (startCell) {

            startCell.textContent =
                formatTimestamp(startTime);

            startCell.className =
                'time-text time-started';

        }


        if (statusBadge) {

            statusBadge.textContent = 'Cleaning';

            statusBadge.className =
                'status-badge status-cleaning';

        }


        if (startButton) {

            startButton.textContent = 'Started';

            startButton.disabled = true;

            startButton.style.opacity = '.55';

            startButton.style.cursor = 'not-allowed';

        }


        if (completeButton) {

            completeButton.style.display = 'inline-flex';

        }


        saveTaskState(
            row,
            'cleaning',
            startTime.toISOString(),
            null
        );

    }


    function completeCleaning(button) {

        const row = button.closest('tr');

        if (!row) return;


        const endCell =
            row.querySelector('[data-timestamp="end"]');

        const statusBadge =
            row.querySelector('[data-status]');

        const actionCell =
            row.querySelector('[data-action-cell]');


        const endTime = new Date();

        const savedState =
            readTaskState(row) || {};


        if (endCell) {

            endCell.textContent =
                formatTimestamp(endTime);

            endCell.className =
                'time-text time-finished';

        }


        if (statusBadge) {

            statusBadge.textContent = 'Completed';

            statusBadge.className =
                'status-badge status-completed';

        }


        if (actionCell) {

            actionCell.innerHTML = `

                <span class="finished-label">
                    <i class="fas fa-check-circle"></i>
                    Finished
                </span>

            `;

        }


        saveTaskState(
            row,
            'completed',
            savedState.startTime || null,
            endTime.toISOString()
        );

    }


    function deleteTask(button) {

        const row = button.closest('tr');

        if (!row) return;


        const confirmed = window.confirm(
            'Are you sure you want to delete this cleaning task?'
        );


        if (!confirmed) {
            return;
        }


        const taskId =
            row.getAttribute('data-row-id');


        const tasks =
            readStoredTasks()
                .filter(task => task.id !== taskId);


        saveStoredTasks(tasks);


        row.remove();


        localStorage.removeItem(
            `housekeeping-task-${taskId}`
        );


        filterTasks();

    }


    function handleTaskSubmit(event) {

        event.preventDefault();


        const form =
            document.getElementById('assignTaskForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }


        const roomNumber =
            document.getElementById('roomNumber')
                .value
                .trim();


        const roomType =
            document.getElementById('roomType')
                .value;


        const occupancy =
            document.getElementById('occupancy')
                .value
                .trim();


        const assignedStaff =
            document.getElementById('assignedStaff')
                .value
                .trim();


        const notes =
            document.getElementById('notes')
                .value
                .trim();


        const dayValue =
            document.getElementById('scheduleDate')
                .value;


        const dayLabel =
            dayValue
                ? new Date(
                    dayValue + 'T00:00:00'
                  ).toLocaleDateString(
                    'en-US',
                    {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    }
                  )
                : 'Not set';


        const selectedTasks =
            Array.from(
                document.querySelectorAll(
                    'input[name="cleaningType[]"]:checked'
                )
            ).map(
                checkbox => checkbox.value
            );


        const selectedPriority =
            document.querySelector(
                'input[name="priority"]:checked'
            );


        const priority =
            selectedPriority
                ? selectedPriority.value
                : 'medium';


        const task =
            selectedTasks.length
                ? selectedTasks.join(', ')
                : 'General Cleaning';


        const roomLabel =
            roomNumber
                ? `Room ${roomNumber}`
                : 'New Room';


        const taskId =
            `task-${Date.now()}`;


        const taskData = {

            id: taskId,

            roomLabel,

            roomNumber,

            contactNumber:
                document.getElementById('contactNumber').value.trim(),

            bookingReference:
                document.getElementById('bookingReference').value.trim(),

            roomType,

            occupancy:
                occupancy || 'Occupancy not set',

            task,

            priority,

            assignedStaff:
                assignedStaff || 'Unassigned',

            note:
                notes || 'New task assigned',

            day:
                dayLabel,

            dayValue,

            scheduleTime:
                document.getElementById('scheduleTime').value,

            duration:
                document.getElementById('duration').value.trim()

        };


        const tasks =
            readStoredTasks();


        tasks.push(taskData);


        saveStoredTasks(tasks);


        appendTaskRow(taskData);


        filterTasks();


        closeModal();


        form.reset();


        document.getElementById('priorityMedium').checked = true;

    }


    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const priorityFilter =
                document.getElementById(
                    'priorityFilter'
                );


            if (priorityFilter) {

                priorityFilter.addEventListener(
                    'change',
                    filterTasks
                );

            }


            renderStoredTasks();


            const form =
                document.getElementById(
                    'assignTaskForm'
                );


            if (form) {

                form.addEventListener(
                    'submit',
                    handleTaskSubmit
                );

            }

        }
    );

</script>

@endsection