@extends('housekeeping.layout')

@section('content')

<style>

    .history-page {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        padding: 8px 0;
        animation: historyFade .35s ease;
    }

    @keyframes historyFade {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .history-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 26px;
    }

    .history-title h2 {
        margin: 0;
        color: #252525;
        font-size: 30px;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -.5px;
    }

    .history-table tbody .empty-row {
        display: table-row;
    }

    .empty-row td {
        padding: 32px 20px;
        color: #858585;
        text-align: center;
    }

    .history-title p {
        margin: 7px 0 0;
        color: #858585;
        font-size: 14px;
    }

    .export-btn {
        height: 44px;
        padding: 0 18px;
        border: none;
        border-radius: 9px;
        background: linear-gradient(135deg, #ff6b35, #e95420);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 5px 14px rgba(233, 84, 32, .20);
        transition: all .2s ease;
    }

    .export-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 18px rgba(233, 84, 32, .28);
    }

    .history-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 26px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 15px;
        padding: 21px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .045);
        transition: all .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 9px 25px rgba(0, 0, 0, .08);
    }

    .stat-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        right: -35px;
        bottom: -40px;
        border-radius: 50%;
        background: rgba(255, 107, 53, .035);
    }

    .stat-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .stat-label {
        margin: 0;
        color: #777;
        font-size: 13px;
        font-weight: 600;
    }

    .stat-value {
        margin: 5px 0 0;
        color: #242424;
        font-size: 31px;
        line-height: 1.15;
        font-weight: 700;
    }

    .stat-unit {
        color: #a1a1a1;
        font-size: 16px;
        font-weight: 600;
    }

    .stat-description {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 9px;
        font-size: 11px;
        font-weight: 600;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        box-shadow: 0 5px 12px rgba(0, 0, 0, .10);
        transition: transform .25s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.07);
    }

    .stat-green .stat-icon {
        background: linear-gradient(135deg, #48c774, #23934e);
    }

    .stat-blue .stat-icon {
        background: linear-gradient(135deg, #4b91e8, #2468bd);
    }

    .stat-purple .stat-icon {
        background: linear-gradient(135deg, #9b72df, #7042b6);
    }

    .stat-green .stat-description {
        color: #2b9651;
    }

    .stat-blue .stat-description {
        color: #3178c5;
    }

    .stat-purple .stat-description {
        color: #774ab7;
    }

    .filter-card {
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 15px;
        padding: 21px;
        margin-bottom: 26px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .045);
    }

    .filter-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 18px;
    }

    .filter-heading-icon {
        width: 31px;
        height: 31px;
        border-radius: 8px;
        background: #fff1ea;
        color: #ef6228;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .filter-heading h3 {
        margin: 0;
        color: #333;
        font-size: 16px;
        font-weight: 700;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    .filter-field label {
        display: block;
        margin-bottom: 7px;
        color: #5d5d5d;
        font-size: 12px;
        font-weight: 600;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper > i:first-child {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 12px;
        pointer-events: none;
    }

    .filter-input,
    .filter-select {
        width: 100%;
        height: 43px;
        padding: 0 12px 0 36px;
        border: 1px solid #dedede;
        border-radius: 8px;
        background: #fff;
        color: #444;
        font-family: inherit;
        font-size: 12px;
        outline: none;
        transition: all .2s ease;
    }

    .filter-input::placeholder {
        color: #aaa;
    }

    .filter-input:hover,
    .filter-select:hover {
        border-color: #c8c8c8;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, .10);
    }

    .select-wrapper {
        position: relative;
    }

    .select-arrow {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 10px;
        pointer-events: none;
    }

    .filter-select {
        appearance: none;
        cursor: pointer;
        padding-right: 32px;
    }

    .filter-btn {
        width: 100%;
        height: 43px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(135deg, #ff6b35, #e95420);
        color: #fff;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 11px rgba(233, 84, 32, .18);
        transition: all .2s ease;
    }

    .filter-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(233, 84, 32, .25);
    }

    .records-card {
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .045);
    }

    .records-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 21px 23px;
        border-bottom: 1px solid #eeeeee;
    }

    .records-title {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .records-title-icon {
        width: 31px;
        height: 31px;
        border-radius: 8px;
        background: #fff1ea;
        color: #ef6228;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .records-title h3 {
        margin: 0;
        color: #333;
        font-size: 16px;
        font-weight: 700;
    }

    .records-total {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 20px;
        background: #f6f6f6;
        color: #777;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .records-total i {
        color: #aaa;
    }

    .table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .history-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .history-table thead {
        background: #fff8f4;
    }

    .history-table th {
        padding: 13px 18px;
        text-align: left;
        color: #b3532e;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .65px;
        border-top: 1px solid #f6dfd4;
        border-bottom: 1px solid #f6dfd4;
        white-space: nowrap;
    }

    .history-table td {
        padding: 15px 18px;
        border-bottom: 1px solid #f0f0f0;
        color: #555;
        font-size: 13px;
        vertical-align: middle;
    }

    .history-table tbody tr {
        transition: background .2s ease;
    }

    .history-table tbody tr:hover {
        background: #fffaf7;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }


    .room-name {
        color: #292929;
        font-size: 13px;
        font-weight: 700;
    }

    .room-type {
        display: inline-flex;
        margin-top: 5px;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 600;
    }

    .room-deluxe {
        background: #edf5ff;
        color: #3976b8;
    }

    .room-suite {
        background: #f4edff;
        color: #7650ae;
    }

    .room-standard {
        background: #f0f0f0;
        color: #686868;
    }


    .task-name {
        color: #555;
        font-size: 12px;
        font-weight: 500;
    }

    .staff-info {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 145px;
    }

    .staff-avatar {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        box-shadow: 0 3px 8px rgba(0, 0, 0, .10);
    }

    .avatar-orange {
        background: linear-gradient(135deg, #ff914d, #e95420);
    }

    .avatar-blue {
        background: linear-gradient(135deg, #579bf0, #2869b9);
    }

    .avatar-purple {
        background: linear-gradient(135deg, #a57ae8, #7242b6);
    }

    .staff-name {
        color: #555;
        font-size: 12px;
        font-weight: 600;
    }


    .date-text,
    .time-text {
        color: #777;
        font-size: 12px;
        white-space: nowrap;
    }

    .completed-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 20px;
        background: #eaf8ee;
        color: #278346;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .completed-status i {
        font-size: 10px;
    }

    .history-view-btn {
        padding: 6px 10px;
        border: 1px solid #ffb08e;
        border-radius: 7px;
        background: #fff;
        color: #e95420;
        font-family: inherit;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
    }

    .history-view-btn:hover {
        background: #fff7f2;
    }

    .history-modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .55);
    }

    .history-modal.open { display: flex; }
    .history-modal-card { width: min(100%, 520px); max-height: 90vh; overflow-y: auto; padding: 24px; border-radius: 15px; background: #fff; box-shadow: 0 20px 45px rgba(15, 23, 42, .2); }
    .history-modal-head { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 18px; }
    .history-modal-head h3 { margin: 0; color: #292929; font-size: 18px; }
    .history-modal-close { border: 0; background: transparent; color: #888; font-size: 22px; cursor: pointer; }
    .history-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .history-detail label { display: block; margin-bottom: 4px; color: #999; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .history-detail p { margin: 0; color: #444; font-size: 13px; }

    .records-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 14px 20px;
        background: #fafafa;
        border-top: 1px solid #eeeeee;
    }

    .records-summary {
        margin: 0;
        color: #888;
        font-size: 11px;
    }

    .records-summary strong {
        color: #555;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .page-btn {
        min-width: 32px;
        height: 32px;
        padding: 0 9px;
        border: 1px solid #dedede;
        border-radius: 7px;
        background: #fff;
        color: #666;
        font-family: inherit;
        font-size: 11px;
        cursor: pointer;
        transition: all .2s ease;
    }

    .page-btn:hover:not(:disabled) {
        border-color: #ffb08e;
        color: #e95420;
        background: #fffaf7;
    }

    .page-btn.active {
        border-color: #ff6b35;
        background: linear-gradient(135deg, #ff6b35, #e95420);
        color: #fff;
        box-shadow: 0 3px 8px rgba(233, 84, 32, .18);
    }

    .page-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }


    @media (max-width: 1050px) {

        .history-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-card:last-child {
            grid-column: span 2;
        }

        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media (max-width: 768px) {

        .history-page {
            padding: 5px 0;
        }

        .history-header {
            align-items: flex-start;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .history-title h2 {
            font-size: 25px;
        }

        .export-btn {
            width: 100%;
        }

        .history-stats {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .stat-card:last-child {
            grid-column: auto;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-card,
        .records-card {
            border-radius: 12px;
        }

        .records-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .records-total {
            align-self: flex-start;
        }

        .records-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .pagination {
            width: 100%;
            justify-content: flex-start;
        }

    }

    @media (max-width: 480px) {

        .history-title h2 {
            font-size: 23px;
        }

        .stat-card {
            padding: 17px;
        }

        .stat-value {
            font-size: 27px;
        }

        .filter-card {
            padding: 17px;
        }

        .records-header {
            padding: 17px;
        }

        .pagination {
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .page-btn {
            flex: 0 0 auto;
        }

    }
</style>


<div class="history-page">

    <div class="history-header">

        <div class="history-title">

            <h2>
                Cleaning History
            </h2>

            <p>
                Track and review all completed housekeeping tasks.
            </p>

        </div>


        <button type="button" class="export-btn" id="exportHistoryButton">

            <i class="fas fa-download"></i>

            Export Report

        </button>

    </div>


    <div class="history-stats">


        <!-- COMPLETED TASKS -->

        <div class="stat-card stat-green">

            <div class="stat-content">

                <div>

                    <p class="stat-label">
                        Completed Tasks
                    </p>

                    <h2 class="stat-value">
                        0
                    </h2>

                    <div class="stat-description">

                        <i class="fas fa-check-circle"></i>

                        Successfully cleaned rooms

                    </div>

                </div>


                <div class="stat-icon">

                    <i class="fas fa-broom"></i>

                </div>

            </div>

        </div>


        <!-- THIS WEEK -->

        <div class="stat-card stat-blue">

            <div class="stat-content">

                <div>

                    <p class="stat-label">
                        This Week
                    </p>

                    <h2 class="stat-value">
                        0
                    </h2>

                    <div class="stat-description">

                        <i class="fas fa-calendar"></i>

                        Completed cleaning

                    </div>

                </div>


                <div class="stat-icon">

                    <i class="fas fa-calendar-check"></i>

                </div>

            </div>

        </div>


        <!-- AVERAGE TIME -->

        <div class="stat-card stat-purple">

            <div class="stat-content">

                <div>

                    <p class="stat-label">
                        Average Cleaning Time
                    </p>

                    <h2 class="stat-value">
                        0
                        <span class="stat-unit">
                            mins
                        </span>
                    </h2>

                    <div class="stat-description">

                        <i class="fas fa-clock"></i>

                        Per room

                    </div>

                </div>


                <div class="stat-icon">

                    <i class="fas fa-stopwatch"></i>

                </div>

            </div>

        </div>


    </div>


    <div class="filter-card">


        <div class="filter-heading">

            <div class="filter-heading-icon">

                <i class="fas fa-filter"></i>

            </div>

            <h3>
                Search Cleaning Records
            </h3>

        </div>


        <div class="filter-grid">


            <div class="filter-field">

                <label>
                    Search Room
                </label>

                <div class="input-wrapper">

                    <i class="fas fa-bed"></i>

                    <input
                        id="historyRoomFilter"
                        type="text"
                        placeholder="Room number"
                        class="filter-input"
                    >

                </div>

            </div>



            <div class="filter-field">

                <label>
                    Date
                </label>

                <div class="input-wrapper">

                    <i class="fas fa-calendar-alt"></i>

                    <input
                        id="historyDateFilter"
                        type="date"
                        class="filter-input"
                    >

                </div>

            </div>


            <div class="filter-field">

                <label>
                    Staff
                </label>

                <div class="select-wrapper">

                    <i class="fas fa-user-cog"
                       style="
                           position:absolute;
                           left:12px;
                           top:50%;
                           transform:translateY(-50%);
                           color:#aaa;
                           font-size:12px;
                           z-index:2;
                       ">
                    </i>

                    <select id="historyStaffFilter" class="filter-select">

                        <option>
                            All Staff
                        </option>

                        <option>
                            Maria Santos
                        </option>

                        <option>
                            John Cruz
                        </option>

                        <option>
                            Anna Reyes
                        </option>

                    </select>

                    <i class="fas fa-chevron-down select-arrow"></i>

                </div>

            </div>


            <div class="filter-field">

                <label>
                    &nbsp;
                </label>

                <button type="button" class="filter-btn" id="filterHistoryButton">

                    <i class="fas fa-search"></i>

                    <span style="margin-left:7px;">
                        Filter Records
                    </span>

                </button>

            </div>


        </div>

    </div>


    <div class="records-card">


        <div class="records-header">

            <div class="records-title">

                <div class="records-title-icon">

                    <i class="fas fa-list-check"></i>

                </div>

                <h3>
                    Completed Cleaning Records
                </h3>

            </div>


            <span class="records-total">

                <i class="fas fa-file-alt"></i>

                Total Records: 0

            </span>

        </div>


        <div class="table-scroll">

            <table class="history-table">

                <thead>

                    <tr>

                        <th>
                            Room
                        </th>

                        <th>
                            Task
                        </th>

                        <th>
                            Assigned Staff
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Time
                        </th>

                                <th>Status</th>
                                <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <tr class="empty-row" id="historyEmptyRow" style="display:none;"><td colspan="7">No completed cleaning records.</td></tr>


                    <tr class="history-record" data-room="101" data-date="2026-07-31" data-staff="Maria Santos">

                        <td>

                            <div class="room-name">
                                Room 101
                            </div>

                            <span class="room-type room-deluxe">
                                Deluxe Room
                            </span>

                        </td>


                        <td>

                            <span class="task-name">
                                Deep Cleaning
                            </span>

                        </td>


                        <td>

                            <div class="staff-info">

                                <div class="staff-avatar avatar-orange">
                                    MS
                                </div>

                                <span class="staff-name">
                                    Maria Santos
                                </span>

                            </div>

                        </td>


                        <td>

                            <span class="date-text">
                                Jul 31, 2026
                            </span>

                        </td>


                        <td>

                            <span class="time-text">
                                09:30 AM
                            </span>

                        </td>


                        <td>

                            <span class="completed-status">

                                <i class="fas fa-check-circle"></i>

                                Completed

                            </span>

                        </td>

                    </tr>

                    <tr class="history-record" data-room="205" data-date="2026-07-30" data-staff="John Cruz">

                        <td>

                            <div class="room-name">
                                Room 205
                            </div>

                            <span class="room-type room-suite">
                                Suite Room
                            </span>

                        </td>


                        <td>

                            <span class="task-name">
                                Linen Replacement
                            </span>

                        </td>


                        <td>

                            <div class="staff-info">

                                <div class="staff-avatar avatar-blue">
                                    JC
                                </div>

                                <span class="staff-name">
                                    John Cruz
                                </span>

                            </div>

                        </td>


                        <td>

                            <span class="date-text">
                                Jul 30, 2026
                            </span>

                        </td>


                        <td>

                            <span class="time-text">
                                02:15 PM
                            </span>

                        </td>


                        <td>

                            <span class="completed-status">

                                <i class="fas fa-check-circle"></i>

                                Completed

                            </span>

                        </td>

                    </tr>


                    <tr class="history-record" data-room="302" data-date="2026-07-29" data-staff="Anna Reyes">

                        <td>

                            <div class="room-name">
                                Room 302
                            </div>

                            <span class="room-type room-standard">
                                Standard Room
                            </span>

                        </td>


                        <td>

                            <span class="task-name">
                                General Cleaning
                            </span>

                        </td>


                        <td>

                            <div class="staff-info">

                                <div class="staff-avatar avatar-purple">
                                    AR
                                </div>

                                <span class="staff-name">
                                    Anna Reyes
                                </span>

                            </div>

                        </td>


                        <td>

                            <span class="date-text">
                                Jul 29, 2026
                            </span>

                        </td>


                        <td>

                            <span class="time-text">
                                10:00 AM
                            </span>

                        </td>


                        <td>

                            <span class="completed-status">

                                <i class="fas fa-check-circle"></i>

                                Completed

                            </span>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>


        <div class="records-footer">


            <p class="records-summary">

                Showing

                <strong id="historyShowingFrom">1</strong>

                to

                <strong id="historyShowingTo">3</strong>

                of

                <strong id="historyTotal">3</strong>

                records

            </p>


            <div class="pagination">

                <button type="button"
                    class="page-btn"
                    id="historyPrevious"
                    disabled
                >
                    <i class="fas fa-chevron-left"></i>
                </button>


                <button type="button" class="page-btn active history-page-number">
                    1
                </button>


                <button type="button" class="page-btn history-page-number">
                    2
                </button>


                <button type="button" class="page-btn history-page-number">
                    3
                </button>


                <button type="button" class="page-btn" id="historyNext">
                    <i class="fas fa-chevron-right"></i>
                </button>

            </div>

        </div>


    </div>


</div>

<div id="historyDetailsModal" class="history-modal" role="dialog" aria-modal="true" aria-labelledby="historyDetailsTitle">
    <div class="history-modal-card">
        <div class="history-modal-head">
            <h3 id="historyDetailsTitle">Cleaning Task Details</h3>
            <button type="button" class="history-modal-close" id="historyDetailsClose" aria-label="Close">&times;</button>
        </div>
        <div id="historyDetailsContent" class="history-detail-grid"></div>
    </div>
</div>

<script>
(() => {
    const tableBody = document.querySelector('.history-table tbody');
    const emptyRow = document.getElementById('historyEmptyRow');
    const roomInput = document.getElementById('historyRoomFilter');
    const dateInput = document.getElementById('historyDateFilter');
    const staffInput = document.getElementById('historyStaffFilter');
    const total = document.getElementById('historyTotal');
    const from = document.getElementById('historyShowingFrom');
    const to = document.getElementById('historyShowingTo');
    let records = [];
    let filteredRecords = [];
    let page = 1;
    const pageSize = 3;

    function escapeHtml(value) {
        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function readCompletedRecords() {
        try {
            const tasks = JSON.parse(localStorage.getItem('housekeeping-assigned-tasks') || '[]');
            return tasks.map(task => {
                const state = JSON.parse(localStorage.getItem(`housekeeping-task-${task.id}`) || 'null');
                return state && state.status === 'completed' ? { ...task, state } : null;
            }).filter(Boolean).reverse();
        } catch (error) {
            return [];
        }
    }

    function formatDate(value) {
        if (!value) return 'Not set';
        const date = new Date(`${value}T00:00:00`);
        return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatTime(value) {
        if (!value) return 'Not set';
        const [hour, minute] = value.split(':');
        const date = new Date(2000, 0, 1, Number(hour), Number(minute));
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }

    function formatStoredTime(value) {
        if (!value) return 'Not started';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? value : date.toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
    }

    function renderRecords() {
        tableBody.querySelectorAll('.history-record').forEach(row => row.remove());
        records.forEach(record => {
            const row = document.createElement('tr');
            row.className = 'history-record';
            row.dataset.room = String(record.roomNumber || record.roomLabel || '').toLowerCase();
            row.dataset.date = record.dayValue || '';
            row.dataset.staff = record.assignedStaff || '';
            row.innerHTML = `<td><div class="room-name">${escapeHtml(record.roomLabel || 'Room not set')}</div><span class="room-type room-standard">${escapeHtml(record.roomType || 'Room type not set')}</span></td><td><span class="task-name">${escapeHtml(record.task || 'General Cleaning')}</span></td><td><div class="staff-info"><div class="staff-avatar avatar-orange">${escapeHtml((record.assignedStaff || 'Staff').split(' ').map(part => part[0]).slice(0, 2).join('').toUpperCase())}</div><span class="staff-name">${escapeHtml(record.assignedStaff || 'Unassigned')}</span></div></td><td><span class="date-text">${escapeHtml(formatDate(record.dayValue) || record.day || 'Not set')}</span></td><td><span class="time-text">${escapeHtml(formatStoredTime(record.state.endTime))}</span></td><td><span class="completed-status"><i class="fas fa-check-circle"></i> Completed</span></td><td><button type="button" class="history-view-btn" data-history-id="${escapeHtml(record.id)}"><i class="fas fa-eye"></i> View</button></td>`;
            tableBody.appendChild(row);
        });
    }

    function updateStats() {
        const completed = document.querySelector('.stat-green .stat-value');
        const week = document.querySelector('.stat-blue .stat-value');
        const average = document.querySelector('.stat-purple .stat-value');
        const durations = records.map(record => parseInt(record.duration, 10)).filter(Number.isFinite);
        const weekStart = new Date();
        weekStart.setDate(weekStart.getDate() - 7);
        const thisWeek = records.filter(record => record.state.endTime && new Date(record.state.endTime) >= weekStart).length;
        if (completed) completed.textContent = records.length;
        if (week) week.textContent = thisWeek;
        if (average) average.firstChild.textContent = durations.length ? Math.round(durations.reduce((sum, value) => sum + value, 0) / durations.length) : '0';
        const recordsTotal = document.querySelector('.records-total');
        if (recordsTotal) recordsTotal.innerHTML = `<i class="fas fa-file-alt"></i> Total Records: ${records.length}`;
    }

    function renderHistory() {
        const pageCount = Math.max(1, Math.ceil(filteredRecords.length / pageSize));
        page = Math.min(page, pageCount);
        const first = (page - 1) * pageSize;
        tableBody.querySelectorAll('.history-record').forEach(row => row.style.display = 'none');
        filteredRecords.slice(first, first + pageSize).forEach(record => tableBody.querySelector(`[data-history-id="${CSS.escape(record.id)}"]`)?.closest('tr')?.style.removeProperty('display'));
        emptyRow.style.display = filteredRecords.length ? 'none' : '';
        total.textContent = filteredRecords.length;
        from.textContent = filteredRecords.length ? first + 1 : 0;
        to.textContent = Math.min(first + pageSize, filteredRecords.length);
        document.querySelectorAll('.history-page-number').forEach((button, index) => {
            button.classList.toggle('active', index + 1 === page);
            button.disabled = index + 1 > pageCount;
        });
        document.getElementById('historyPrevious').disabled = page === 1;
        document.getElementById('historyNext').disabled = page === pageCount;
    }

    function filterHistory() {
        const room = roomInput.value.trim().toLowerCase();
        const date = dateInput.value;
        const staff = staffInput.value;
        filteredRecords = records.filter(record =>
            (!room || String(record.roomNumber || record.roomLabel).toLowerCase().includes(room)) &&
            (!date || record.dayValue === date) &&
            (staff === 'All Staff' || record.assignedStaff === staff)
        );
        page = 1;
        renderHistory();
    }

    function exportHistory() {
        const header = ['Room', 'Room Type', 'Occupancy', 'Task', 'Priority', 'Assigned Staff', 'Contact Number', 'Booking Reference', 'Scheduled Date', 'Scheduled Time', 'Estimated Duration', 'Started At', 'Finished At', 'Status', 'Notes'];
        const data = filteredRecords.map(record => [record.roomLabel, record.roomType, record.occupancy, record.task, record.priority, record.assignedStaff, record.contactNumber, record.bookingReference, record.dayValue || record.day, formatTime(record.scheduleTime), record.duration, formatStoredTime(record.state.startTime), formatStoredTime(record.state.endTime), 'Completed', record.note]);
        const csv = [header, ...data].map(line => line.map(value => `"${String(value ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
        const link = document.createElement('a');
        const downloadUrl = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
        link.href = downloadUrl;
        link.download = 'cleaning-history-report.csv';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(() => URL.revokeObjectURL(downloadUrl), 1000);
    }

    function openDetails(record) {
        const content = document.getElementById('historyDetailsContent');
        const fields = [['Room', record.roomLabel], ['Room Type', record.roomType], ['Occupancy', record.occupancy], ['Task', record.task], ['Priority', record.priority], ['Assigned Staff', record.assignedStaff], ['Contact Number', record.contactNumber || 'Not provided'], ['Booking Reference', record.bookingReference || 'Not provided'], ['Scheduled Date', formatDate(record.dayValue) || record.day], ['Scheduled Time', formatTime(record.scheduleTime)], ['Estimated Duration', record.duration || 'Not set'], ['Started At', formatStoredTime(record.state.startTime)], ['Finished At', formatStoredTime(record.state.endTime)], ['Notes', record.note || 'None']];
        content.innerHTML = fields.map(([label, value]) => `<div class="history-detail"><label>${escapeHtml(label)}</label><p>${escapeHtml(value || 'Not set')}</p></div>`).join('');
        document.getElementById('historyDetailsModal').classList.add('open');
    }

    document.getElementById('filterHistoryButton').addEventListener('click', filterHistory);
    document.getElementById('exportHistoryButton').addEventListener('click', exportHistory);
    document.getElementById('historyPrevious').addEventListener('click', () => { page--; renderHistory(); });
    document.getElementById('historyNext').addEventListener('click', () => { page++; renderHistory(); });
    document.querySelectorAll('.history-page-number').forEach((button, index) => button.addEventListener('click', () => { page = index + 1; renderHistory(); }));
    tableBody.addEventListener('click', event => { const button = event.target.closest('[data-history-id]'); if (button) openDetails(records.find(record => record.id === button.dataset.historyId)); });
    document.getElementById('historyDetailsClose').addEventListener('click', () => document.getElementById('historyDetailsModal').classList.remove('open'));
    document.getElementById('historyDetailsModal').addEventListener('click', event => { if (event.target.id === 'historyDetailsModal') event.currentTarget.classList.remove('open'); });
    records = readCompletedRecords();
    filteredRecords = records;
    renderRecords();
    updateStats();
    renderHistory();
})();
</script>

@endsection
