@extends('housekeeping.layout')

@section('content')

<style>
.room-status-page {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 24px;
    color: #263238;
}

/* =========================================================
   PAGE HEADER
   ========================================================= */

.page-header {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 28px 30px;
    border-radius: 18px;
    background: linear-gradient(135deg, #ffffff 0%, #fff8f3 100%);
    border: 1px solid #eadfda;
    box-shadow: 0 8px 25px rgba(73, 52, 42, 0.06);
}

.page-header::after {
    content: "";
    position: absolute;
    right: -60px;
    top: -80px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(128, 0, 0, 0.05);
}

.page-header-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}

.page-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 8px;
    color: #9b4d2e;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.page-header h2 {
    margin: 0;
    color: #3a2520;
    font-size: 28px;
    font-weight: 750;
}

.page-header p {
    margin: 7px 0 0;
    max-width: 650px;
    color: #7c706b;
    font-size: 14px;
    line-height: 1.6;
}

.portal-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    padding: 10px 15px;
    border-radius: 30px;
    background: #fff0e8;
    border: 1px solid #f3d7c7;
    color: #9b4d2e;
    font-size: 13px;
    font-weight: 700;
}


/* =========================================================
   CARDS
   ========================================================= */

.status-card {
    margin-bottom: 24px;
    padding: 24px;
    background: #ffffff;
    border: 1px solid #e8e1de;
    border-radius: 18px;
    box-shadow: 0 7px 24px rgba(54, 38, 31, 0.055);
}

.section-heading {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 22px;
}

.section-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    flex-shrink: 0;
    border-radius: 12px;
    background: #f8ece7;
    color: #8b3f28;
    font-size: 17px;
}

.section-icon.purple {
    background: #f1edff;
    color: #7658bd;
}

.section-icon.blue {
    background: #eaf4ff;
    color: #3978b8;
}

.section-heading h3 {
    margin: 0;
    color: #342521;
    font-size: 18px;
    font-weight: 750;
}

.section-heading p {
    margin: 4px 0 0;
    color: #8a7d77;
    font-size: 13px;
}


/* =========================================================
   TODAY'S OVERVIEW
   ========================================================= */

.overview-card {
    margin-bottom: 24px;
}

.summary-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    border-radius: 13px;
    background: #faf9f8;
    border: 1px solid #eee8e5;
    transition: 0.2s ease;
}

.summary-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 16px rgba(54, 38, 31, 0.06);
}

.summary-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 11px;
    flex-shrink: 0;
}

.summary-item strong {
    display: block;
    color: #3b2b26;
    font-size: 21px;
    line-height: 1;
}

.summary-item span {
    display: block;
    margin-top: 4px;
    color: #8b7f79;
    font-size: 11px;
}

.dirty-bg {
    background: #ffe5e2;
    color: #b3443b;
}

.cleaning-bg {
    background: #ffefcb;
    color: #ae751a;
}

.cleaned-bg {
    background: #def4e6;
    color: #348152;
}

.available-bg {
    background: #eae1ff;
    color: #6d4db0;
}


/* =========================================================
   MONITORING
   ========================================================= */

.monitoring-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.updated-badge {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 12px;
    border-radius: 20px;
    background: #f3f7f4;
    border: 1px solid #dce9df;
    color: #52715d;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #45a866;
}


/* =========================================================
   TABLE
   ========================================================= */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border: 1px solid #e9e2df;
    border-radius: 13px;
}

.room-table {
    width: 100%;
    min-width: 820px;
    border-collapse: collapse;
    background: #ffffff;
}

.room-table th {
    padding: 14px 16px;
    background: #faf8f7;
    border-bottom: 1px solid #e5ddda;
    color: #81746e;
    font-size: 10px;
    font-weight: 800;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.room-table td {
    padding: 15px 16px;
    border-bottom: 1px solid #eee9e6;
    color: #776b66;
    font-size: 13px;
    vertical-align: middle;
}

.room-table tbody tr:last-child td {
    border-bottom: 0;
}

.room-table tbody tr {
    transition: 0.15s ease;
}

.room-table tbody tr:hover {
    background: #fffbf9;
}


/* Room */

.room-number {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #3d2d28;
}

.room-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: #f7ebe6;
    color: #9b4d2e;
    font-size: 12px;
}


/* =========================================================
   STATUS BADGES
   ========================================================= */

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 750;
}

.status-badge span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.dirty-badge {
    background: #ffebe8;
    color: #a63d35;
}

.dirty-badge span {
    background: #c74d43;
}

.cleaning-badge {
    background: #fff2d5;
    color: #a86e14;
}

.cleaning-badge span {
    background: #d89428;
}

.cleaned-badge {
    background: #e4f6ea;
    color: #347b4d;
}

.cleaned-badge span {
    background: #45a866;
}

.inspected-badge {
    background: #e7f4ff;
    color: #2873a5;
}

.inspected-badge span {
    background: #3e8ac1;
}

.available-badge {
    background: #eee8ff;
    color: #6748a4;
}

.available-badge span {
    background: #7958bd;
}


/* =========================================================
   UPDATE BUTTON
   ========================================================= */

.table-update-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 36px;
    padding: 0 13px;
    border: 1px solid #ead2c6;
    border-radius: 9px;
    background: #fff7f2;
    color: #93472b;
    font-size: 11px;
    font-weight: 750;
    cursor: pointer;
    transition: all 0.2s ease;
}

.table-update-button:hover {
    background: #93472b;
    border-color: #93472b;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(147, 71, 43, 0.18);
}


/* =========================================================
   MODAL
   ========================================================= */

.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(39, 29, 25, 0.55);
    backdrop-filter: blur(4px);
}

.modal-overlay.active {
    display: flex;
}

.status-modal {
    width: 100%;
    max-width: 520px;
    max-height: calc(100vh - 40px);
    overflow: hidden;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 25px 70px rgba(39, 29, 25, 0.25);
    animation: modalOpen 0.2s ease;
}

@keyframes modalOpen {
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
    gap: 15px;
    padding: 20px 22px;
    border-bottom: 1px solid #eee6e2;
}

.modal-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-title-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: #fff0e6;
    color: #a34d2c;
}

.modal-title h3 {
    margin: 0;
    color: #382823;
    font-size: 18px;
}

.modal-title p {
    margin: 3px 0 0;
    color: #91837c;
    font-size: 12px;
}

.modal-close {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 8px;
    background: #f6f3f1;
    color: #766a64;
    cursor: pointer;
    transition: 0.2s ease;
}

.modal-close:hover {
    background: #eee7e3;
    color: #3e302b;
}

.modal-body {
    padding: 22px;
    max-height: calc(100vh - 135px);
    overflow-y: auto;
}

.room-information {
    display: flex;
    align-items: center;
    gap: 13px;
    margin-bottom: 20px;
    padding: 14px;
    border: 1px solid #eee5e1;
    border-radius: 12px;
    background: #faf8f7;
}

.room-information-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #f6e9e3;
    color: #95472d;
}

.room-information strong {
    display: block;
    color: #3d2d28;
    font-size: 15px;
}

.room-information span {
    display: block;
    margin-top: 3px;
    color: #897b75;
    font-size: 11px;
}

.modal-form-group {
    margin-bottom: 17px;
}

.modal-form-group label {
    display: block;
    margin-bottom: 7px;
    color: #4b3d37;
    font-size: 12px;
    font-weight: 750;
}

.modal-form-group select {
    width: 100%;
    min-height: 44px;
    padding: 0 13px;
    border: 1px solid #ddd5d1;
    border-radius: 10px;
    outline: none;
    background: #faf9f8;
    color: #403530;
    font-size: 13px;
    cursor: pointer;
    transition: 0.2s ease;
}

.modal-form-group select:focus {
    border-color: #d87543;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(216, 117, 67, 0.12);
}

.modal-note {
    display: flex;
    gap: 9px;
    align-items: flex-start;
    padding: 11px 12px;
    margin-bottom: 20px;
    border: 1px solid #f2dfc8;
    border-radius: 9px;
    background: #fff8ef;
    color: #85694e;
    font-size: 11px;
    line-height: 1.5;
}

.modal-note i {
    margin-top: 2px;
    color: #d58a3a;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.cancel-button,
.confirm-button {
    min-height: 42px;
    padding: 0 17px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 750;
    cursor: pointer;
    transition: 0.2s ease;
}

.cancel-button {
    border: 1px solid #ddd5d1;
    background: #ffffff;
    color: #665a54;
}

.cancel-button:hover {
    background: #f7f4f2;
}

.confirm-button {
    border: 0;
    background: linear-gradient(135deg, #8b2d1f, #b84e2c);
    color: #ffffff;
    box-shadow: 0 5px 13px rgba(139, 45, 31, 0.2);
}

.confirm-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(139, 45, 31, 0.27);
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .summary-list {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {

    .room-status-page {
        padding: 18px;
    }

    .page-header {
        padding: 22px;
    }

    .page-header-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .portal-badge {
        align-self: flex-start;
    }

    .status-card {
        padding: 20px;
    }

    .monitoring-header {
        flex-direction: column;
        gap: 5px;
    }

    .updated-badge {
        margin-bottom: 15px;
    }
}

@media (max-width: 560px) {

    .room-status-page {
        padding: 12px;
    }

    .page-header {
        padding: 19px;
        border-radius: 14px;
    }

    .page-header h2 {
        font-size: 23px;
    }

    .page-header p {
        font-size: 12px;
    }

    .portal-badge {
        font-size: 11px;
        padding: 8px 11px;
    }

    .status-card {
        padding: 16px;
        border-radius: 14px;
    }

    .section-heading {
        align-items: flex-start;
    }

    .section-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
    }

    .section-heading h3 {
        font-size: 16px;
    }

    .section-heading p {
        font-size: 12px;
    }

    .summary-list {
        grid-template-columns: 1fr;
    }

    .room-table {
        min-width: 760px;
    }

    .modal-overlay {
        padding: 12px;
    }

    .status-modal {
        border-radius: 15px;
    }

    .modal-header,
    .modal-body {
        padding: 17px;
    }

    .modal-actions {
        flex-direction: column-reverse;
    }

    .cancel-button,
    .confirm-button {
        width: 100%;
    }
}
</style>


<div class="room-status-page">

    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <section class="page-header">

        <div class="page-header-content">

            <div>

                <div class="page-eyebrow">
                    <i class="fas fa-concierge-bell"></i>
                    Housekeeping Management
                </div>

                <h2>Room Status Update</h2>

                <p>
                    Monitor room conditions and update housekeeping status
                    directly from the room monitoring table.
                </p>

            </div>

            <div class="portal-badge">
                <i class="fas fa-broom"></i>
                Housekeeping Portal
            </div>

        </div>

    </section>


    <!-- =====================================================
         TODAY'S OVERVIEW
    ====================================================== -->

    <section class="status-card overview-card">

        <div class="section-heading">

            <div class="section-icon">
                <i class="fas fa-chart-pie"></i>
            </div>

            <div>
                <h3>Today's Overview</h3>
                <p>Current room activity and housekeeping status.</p>
            </div>

        </div>


        <div class="summary-list">

            <div class="summary-item">

                <div class="summary-icon dirty-bg">
                    <i class="fas fa-bed"></i>
                </div>

                <div>
                    <strong>{{ $rooms->where('cleaning_status', 'dirty')->count() }}</strong>
                    <span>Dirty Rooms</span>
                </div>

            </div>


            <div class="summary-item">

                <div class="summary-icon cleaning-bg">
                    <i class="fas fa-broom"></i>
                </div>

                <div>
                    <strong>{{ $rooms->where('cleaning_status', 'in_progress')->count() }}</strong>
                    <span>Being Cleaned</span>
                </div>

            </div>


            <div class="summary-item">

                <div class="summary-icon cleaned-bg">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div>
                    <strong>{{ $rooms->where('cleaning_status', 'clean')->count() }}</strong>
                    <span>Cleaned Rooms</span>
                </div>

            </div>


            <div class="summary-item">

                <div class="summary-icon available-bg">
                    <i class="fas fa-door-open"></i>
                </div>

                <div>
                    <strong>{{ $rooms->where('status', 'available')->count() }}</strong>
                    <span>Available Rooms</span>
                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         ROOM STATUS MONITORING
    ====================================================== -->

    <section class="status-card monitoring-card">

        <div class="monitoring-header">

            <div class="section-heading">

                <div class="section-icon blue">
                    <i class="fas fa-list-check"></i>
                </div>

                <div>
                    <h3>Room Status Monitoring</h3>

                    <p>
                        Review current room conditions and update each room
                        when necessary.
                    </p>
                </div>

            </div>


            <div class="updated-badge">
                <span class="live-dot"></span>
                Updated Today
            </div>

        </div>


        <div class="table-wrapper">

            <table class="room-table">

                <thead>

                    <tr>
                        <th>Room</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>
                    @foreach($rooms as $room)
                        @php($status = $room->cleaning_status ?: ($room->status === 'maintenance' ? 'maintenance' : 'clean'))
                        @php($statusLabel = $status === 'in_progress' ? 'Cleaning' : ucfirst(str_replace('_', ' ', $status)))
                        @php($statusClass = $status === 'in_progress' ? 'cleaning-badge' : ($status === 'clean' ? 'available-badge' : ($status === 'maintenance' ? 'maintenance-badge' : 'dirty-badge')))
                        <tr>
                            <td><div class="room-number"><span class="room-icon"><i class="fas fa-door-open"></i></span><strong>{{ $room->room_number }}</strong></div></td>
                            <td>{{ $room->room_type }}</td>
                            <td><span class="status-badge {{ $statusClass }}"><span></span>{{ $statusLabel }}</span></td>
                            <td>{{ $room->updated_at ? $room->updated_at->format('M d, Y · h:i A') : '—' }}</td>
                            <td><button type="button" class="table-update-button" onclick="openStatusModal('{{ $room->room_number }}', '{{ addslashes($room->room_type) }}', '{{ $statusLabel }}')"><i class="fas fa-edit"></i> Update</button></td>
                        </tr>
                    @endforeach
                    @if($rooms->isEmpty())<tr><td colspan="5" class="py-10 text-center">No rooms have been added by the admin.</td></tr>@endif
                    @if(false)

                    <!-- ROOM 101 -->

                    <tr>

                        <td>

                            <div class="room-number">

                                <span class="room-icon">
                                    <i class="fas fa-door-open"></i>
                                </span>

                                <strong>101</strong>

                            </div>

                        </td>

                        <td>Deluxe Room</td>

                        <td>

                            <span class="status-badge dirty-badge">
                                <span></span>
                                Dirty
                            </span>

                        </td>

                        <td>
                            Jul 31, 2026 · 09:30 AM
                        </td>

                        <td>

                            <button
                                type="button"
                                class="table-update-button"
                                onclick="openStatusModal('101', 'Deluxe Room', 'Dirty')"
                            >
                                <i class="fas fa-edit"></i>
                                Update
                            </button>

                        </td>

                    </tr>


                    <!-- ROOM 205 -->

                    <tr>

                        <td>

                            <div class="room-number">

                                <span class="room-icon">
                                    <i class="fas fa-door-open"></i>
                                </span>

                                <strong>205</strong>

                            </div>

                        </td>

                        <td>Suite Room</td>

                        <td>

                            <span class="status-badge cleaning-badge">
                                <span></span>
                                Cleaning
                            </span>

                        </td>

                        <td>
                            Jul 31, 2026 · 10:15 AM
                        </td>

                        <td>

                            <button
                                type="button"
                                class="table-update-button"
                                onclick="openStatusModal('205', 'Suite Room', 'Cleaning')"
                            >
                                <i class="fas fa-edit"></i>
                                Update
                            </button>

                        </td>

                    </tr>


                    <!-- ROOM 302 -->

                    <tr>

                        <td>

                            <div class="room-number">

                                <span class="room-icon">
                                    <i class="fas fa-door-open"></i>
                                </span>

                                <strong>302</strong>

                            </div>

                        </td>

                        <td>Standard Room</td>

                        <td>

                            <span class="status-badge available-badge">
                                <span></span>
                                Available
                            </span>

                        </td>

                        <td>
                            Jul 31, 2026 · 11:00 AM
                        </td>

                        <td>

                            <button
                                type="button"
                                class="table-update-button"
                                onclick="openStatusModal('302', 'Standard Room', 'Available')"
                            >
                                <i class="fas fa-edit"></i>
                                Update
                            </button>

                        </td>

                    </tr>

                    @endif
                </tbody>

            </table>

        </div>

    </section>

</div>


<!-- =========================================================
     UPDATE STATUS MODAL
========================================================= -->

<div
    class="modal-overlay"
    id="statusModal"
    onclick="closeStatusModal(event)"
>

    <div
        class="status-modal"
        onclick="event.stopPropagation()"
    >

        <!-- Modal Header -->

        <div class="modal-header">

            <div class="modal-title">

                <div class="modal-title-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>

                <div>

                    <h3>Update Room Status</h3>

                    <p>
                        Change the housekeeping status of this room.
                    </p>

                </div>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeStatusModal()"
                aria-label="Close"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>


        <!-- Modal Body -->

        <div class="modal-body">

            <div class="room-information">

                <div class="room-information-icon">
                    <i class="fas fa-door-open"></i>
                </div>

                <div>

                    <strong id="modalRoomNumber">
                        Room 101
                    </strong>

                    <span id="modalRoomType">
                        Deluxe Room
                    </span>

                </div>

            </div>


            <form
                id="statusUpdateForm"
                method="POST"
                action="{{ route('housekeeping.rooms.cleaning', ['id' => '__ROOM_ID__']) }}"
                data-action-template="{{ route('housekeeping.rooms.cleaning', ['id' => '__ROOM_ID__']) }}"
            >

                @csrf
                @method('PATCH')


                <div class="modal-form-group">

                    <label for="current_status">
                        Current Status
                    </label>

                    <select
                        id="current_status"
                        name="current_status"
                    >

                        <option value="dirty">
                            Dirty
                        </option>

                        <option value="in_progress">
                            In Progress
                        </option>

                        <option value="clean">
                            Clean
                        </option>

                    </select>

                </div>


                <div class="modal-form-group">

                    <label for="update_status">
                        New Room Status
                    </label>

                    <select
                        id="update_status"
                        name="cleaning_status"
                    >

                        <option value="dirty">
                            Dirty
                        </option>

                        <option value="in_progress">
                            In Progress
                        </option>

                        <option value="clean">
                            Clean
                        </option>

                    </select>

                </div>


                <div class="modal-note">

                    <i class="fas fa-lightbulb"></i>

                    <span>
                        Make sure the new status accurately reflects
                        the actual condition of the room.
                    </span>

                </div>


                <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-button"
                        onclick="closeStatusModal()"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="confirm-button"
                    >
                        <i class="fas fa-check"></i>
                        Save Status
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

function openStatusModal(roomNumber, roomType, currentStatus) {

    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusUpdateForm');

    const roomNumberElement =
        document.getElementById('modalRoomNumber');

    const roomTypeElement =
        document.getElementById('modalRoomType');

    const currentStatusElement =
        document.getElementById('current_status');

    const updateStatusElement =
        document.getElementById('update_status');


    roomNumberElement.textContent =
        'Room ' + roomNumber;

    roomTypeElement.textContent =
        roomType;

    form.action = form.dataset.actionTemplate.replace(
        '__ROOM_ID__',
        roomNumber
    );

    const statusValueMap = {
        Dirty: 'dirty',
        'In Progress': 'in_progress',
        Cleaning: 'in_progress',
        Clean: 'clean',
        Cleaned: 'clean',
        Inspected: 'clean',
        Available: 'clean'
    };


    currentStatusElement.value =
        statusValueMap[currentStatus] || currentStatus;

    updateStatusElement.value =
        statusValueMap[currentStatus] || 'dirty';


    modal.classList.add('active');

    document.body.style.overflow = 'hidden';
}


function closeStatusModal(event) {

    if (
        event &&
        event.target !== document.getElementById('statusModal')
    ) {
        return;
    }

    const modal =
        document.getElementById('statusModal');

    modal.classList.remove('active');

    document.body.style.overflow = '';
}


/*
 * Close modal using ESC key
 */

document.addEventListener('keydown', function(event) {

    if (event.key === 'Escape') {
        closeStatusModal();
    }

});

</script>

@endsection