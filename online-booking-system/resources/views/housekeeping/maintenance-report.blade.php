@extends('housekeeping.layout')

@section('content')

<style>

     .maintenance-page {
    width: 100%;
    max-width: 100%;
    min-height: 100vh;
    margin: 0;
    padding: 28px 0 40px;
    background: #f6f7f9;
    color: #1f2937;
    box-sizing: border-box;
    overflow-x: hidden;
}

    .maintenance-container {
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
    box-sizing: border-box;
}

   .maintenance-header-container {
    width: 100%;
    max-width: 1600px;
    margin: 0 auto 28px;
    padding: 24px 30px;
    background: #f8f9fb;
    border: 1px solid #edf0f3;
    border-radius: 16px;
    box-sizing: border-box;
}

.maintenance-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    margin-bottom: 0;
}

    .header-label {
        margin: 0 0 7px;
        color: #d97706;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .maintenance-title {
        margin: 0;
        font-size: 30px;
        line-height: 1.2;
        font-weight: 750;
        color: #172033;
    }

    .maintenance-description {
        max-width: 650px;
        margin: 8px 0 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .search-wrapper {
        position: relative;
        width: 280px;
    }

    .search-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 14px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        height: 46px;
        padding: 0 15px 0 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        color: #374151;
        font-size: 13px;
        outline: none;
        transition: .2s ease;
    }

    .search-input:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, .10);
    }

    .primary-button {
        height: 46px;
        padding: 0 19px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        cursor: pointer;
        box-shadow: 0 7px 18px rgba(234, 88, 12, .18);
        transition: .2s ease;
        white-space: nowrap;
    }

    .primary-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(234, 88, 12, .24);
    }

    .statistics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 17px;
        margin-bottom: 24px;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        background: #fff;
        border: 1px solid #edf0f3;
        border-radius: 17px;
        padding: 20px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .045);
        transition: .2s ease;
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: var(--stat-color);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
    }

    .stat-content {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
    }

    .stat-label {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-number {
        margin: 7px 0 5px;
        color: #172033;
        font-size: 29px;
        line-height: 1;
        font-weight: 750;
    }

    .stat-note {
        margin: 0;
        font-size: 11px;
        font-weight: 600;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-blue {
        --stat-color: #3b82f6;
    }

    .stat-blue .stat-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .stat-blue .stat-note {
        color: #2563eb;
    }

    .stat-amber {
        --stat-color: #f59e0b;
    }

    .stat-amber .stat-icon {
        background: #fffbeb;
        color: #d97706;
    }

    .stat-amber .stat-note {
        color: #d97706;
    }

    .stat-orange {
        --stat-color: #f97316;
    }

    .stat-orange .stat-icon {
        background: #fff7ed;
        color: #ea580c;
    }

    .stat-orange .stat-note {
        color: #ea580c;
    }

    .stat-green {
        --stat-color: #10b981;
    }

    .stat-green .stat-icon {
        background: #ecfdf5;
        color: #059669;
    }

    .stat-green .stat-note {
        color: #059669;
    }

    .records-panel {
        background: #fff;
        border: 1px solid #edf0f3;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .045);
        overflow: hidden;
    }

    .records-toolbar {
        padding: 21px 23px;
        border-bottom: 1px solid #eef0f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .records-title {
        margin: 0;
        color: #172033;
        font-size: 17px;
        font-weight: 750;
    }

    .records-subtitle {
        margin: 4px 0 0;
        color: #9ca3af;
        font-size: 12px;
    }

    .filter-badges {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
    }

    .filter-badge {
        padding: 6px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-all {
        background: #f3f4f6;
        color: #4b5563;
    }

    .badge-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .badge-repairing {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .badge-completed {
        background: #ecfdf5;
        color: #047857;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .maintenance-table {
    width: 100%;
    min-width: 950px;
    border-collapse: collapse;
}

    .maintenance-table thead {
        background: #fafafa;
    }

    .maintenance-table th {
        padding: 13px 15px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1px;
        text-align: left;
        text-transform: uppercase;
        border-bottom: 1px solid #eef0f3;
        white-space: nowrap;
    }

    .maintenance-table td {
        padding: 16px 15px;
        border-bottom: 1px solid #f1f2f4;
        vertical-align: middle;
        font-size: 12px;
    }

    .maintenance-table tbody tr {
        transition: .18s ease;
    }

    .maintenance-table tbody tr:hover {
        background: #fffaf5;
    }

    .room-number {
        color: #172033;
        font-weight: 750;
        font-size: 13px;
    }

    .room-type {
        margin-top: 3px;
        color: #9ca3af;
        font-size: 10px;
    }

    .person-name {
        color: #4b5563;
        font-size: 12px;
        font-weight: 600;
    }

    .issue-title {
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .issue-description {
        margin-top: 3px;
        color: #9ca3af;
        font-size: 10px;
    }

    .category-priority {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
    }

    .category-badge,
    .priority-badge,
    .status-badge {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .category-badge {
        background: #f3f4f6;
        color: #4b5563;
    }

    .priority-high {
        background: #fef2f2;
        color: #dc2626;
    }

    .priority-medium {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .status-repairing {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-completed {
        background: #ecfdf5;
        color: #047857;
    }

    .date-text,
    .technician-text {
        color: #6b7280;
        white-space: nowrap;
    }

    .action-buttons {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .table-action {
        height: 32px;
        padding: 0 9px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: .18s ease;
    }

    .view-button {
        color: #2563eb;
    }

    .view-button:hover {
        background: #eff6ff;
    }

    .edit-button {
        color: #059669;
    }

    .edit-button:hover {
        background: #ecfdf5;
    }


    .mobile-records {
        display: none;
    }

    .mobile-record {
        padding: 18px;
        border-bottom: 1px solid #eef0f3;
    }

    .mobile-record:last-child {
        border-bottom: 0;
    }

    .mobile-record-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 13px;
    }

    .mobile-room {
        color: #172033;
        font-size: 14px;
        font-weight: 750;
    }

    .mobile-room-type {
        margin-top: 3px;
        color: #9ca3af;
        font-size: 10px;
    }

    .mobile-issue {
        margin-bottom: 12px;
    }

    .mobile-issue-title {
        margin: 0;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
    }

    .mobile-issue-description {
        margin: 3px 0 0;
        color: #9ca3af;
        font-size: 11px;
    }

    .mobile-meta {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        padding-top: 12px;
        border-top: 1px solid #f0f1f3;
    }

    .meta-item {
        color: #6b7280;
        font-size: 10px;
    }

    .meta-item strong {
        display: block;
        margin-bottom: 2px;
        color: #4b5563;
        font-size: 10px;
    }

    .mobile-actions {
        display: flex;
        justify-content: flex-end;
        gap: 5px;
        margin-top: 12px;
    }


    .custom-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .58);
        backdrop-filter: blur(3px);
        opacity: 0;
        transition: opacity .2s ease;
    }

    .custom-modal.active {
        display: flex;
        opacity: 1;
    }

    .modal-box {
        width: 100%;
        max-width: 680px;
        max-height: 90vh;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 25px 70px rgba(15, 23, 42, .22);
        display: flex;
        flex-direction: column;
        transform: translateY(8px);
        transition: transform .2s ease;
    }

    .custom-modal.active .modal-box {
        transform: translateY(0);
    }

    .modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid #edf0f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-shrink: 0;
    }

    .modal-title {
        margin: 0;
        color: #172033;
        font-size: 18px;
        font-weight: 750;
    }

    .modal-subtitle {
        margin: 4px 0 0;
        color: #9ca3af;
        font-size: 11px;
    }

    .modal-close {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 9px;
        background: #f8f9fa;
        color: #9ca3af;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        transition: .18s ease;
    }

    .modal-close:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    .modal-body {
        padding: 22px;
        overflow-y: auto;
    }

    .modal-section {
        margin-bottom: 24px;
    }

    .modal-section:last-child {
        margin-bottom: 0;
    }

    .modal-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 13px;
        color: #6b7280;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .modal-section-title i {
        color: #f97316;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        color: #4b5563;
        font-size: 12px;
        font-weight: 650;
    }

    .form-control {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
        color: #374151;
        font-family: inherit;
        font-size: 12px;
        outline: none;
        transition: .18s ease;
    }

    textarea.form-control {
        height: auto;
        min-height: 90px;
        padding: 11px 12px;
        resize: vertical;
    }

    .form-control:focus {
        background: #fff;
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, .09);
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .detail-item {
        padding: 13px;
        border: 1px solid #edf0f3;
        border-radius: 11px;
        background: #fafafa;
    }

    .detail-item.full {
        grid-column: 1 / -1;
    }

    .detail-label {
        color: #9ca3af;
        font-size: 10px;
        font-weight: 600;
    }

    .detail-value {
        margin-top: 4px;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .modal-footer {
        padding: 15px 22px;
        border-top: 1px solid #edf0f3;
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        flex-shrink: 0;
    }

    .secondary-button,
    .save-button {
        height: 40px;
        padding: 0 17px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: .18s ease;
    }

    .secondary-button {
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
    }

    .secondary-button:hover {
        background: #f8f9fa;
    }

    .save-button {
        border: 0;
        background: #059669;
        color: #fff;
        box-shadow: 0 5px 15px rgba(5, 150, 105, .15);
    }

    .save-button:hover {
        background: #047857;
    }

    @media (max-width: 1200px) {
        .statistics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

         .maintenance-header-container {
        padding: 22px 24px;
        }

        .maintenance-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .header-actions {
            width: 100%;
        }

        .search-wrapper {
            flex: 1;
        }
    }

    @media (max-width: 900px) {
        .maintenance-page {
            padding: 22px 20px 35px;
        }

        .records-toolbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .filter-badges {
            width: 100%;
        }

        .table-wrapper {
            display: none;
        }

        .mobile-records {
            display: block;
        }
    }

    @media (max-width: 650px) {

    .maintenance-page {
        padding: 18px 14px 30px;
    }

    .maintenance-header-container {
        padding: 20px 17px;
        border-radius: 14px;
    }

    .maintenance-title {
        font-size: 25px;
    }

    .maintenance-description {
        font-size: 12px;
    }

    .header-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .search-wrapper {
        width: 100%;
    }

    .primary-button {
        width: 100%;
    }

        .statistics-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .stat-card {
            padding: 17px;
        }

        .stat-number {
            font-size: 26px;
        }

        .records-toolbar {
            padding: 17px;
        }

        .filter-badge {
            font-size: 10px;
            padding: 5px 9px;
        }

        .modal-box {
            max-height: 94vh;
            border-radius: 15px;
        }

        .modal-header {
            padding: 15px 17px;
        }

        .modal-body {
            padding: 17px;
        }

        .form-grid,
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full,
        .detail-item.full {
            grid-column: auto;
        }

        .modal-footer {
            padding: 13px 17px;
            flex-direction: column-reverse;
        }

        .secondary-button,
        .save-button {
            width: 100%;
        }
    }

    @media (max-width: 420px) {
        .mobile-meta {
            grid-template-columns: 1fr;
        }

        .mobile-actions {
            justify-content: stretch;
        }

        .mobile-actions .table-action {
            flex: 1;
        }
    }
</style>


<main class="maintenance-page">

  <div class="maintenance-header-container">

    <div class="maintenance-header">

        <div>
            <p class="header-label">
                Maintenance Overview
            </p>

            <h1 class="maintenance-title">
                Maintenance Report
            </h1>

            <p class="maintenance-description">
                Track room repairs, pending issues, and completed
                maintenance work in one dashboard.
            </p>
        </div>

        <div class="header-actions">

            <div class="search-wrapper">
                <i class="fas fa-search"></i>

                <input
                    id="reportSearch"
                    type="search"
                    class="search-input"
                    placeholder="Search reports..."
                >
            </div>

            <button
                type="button"
                onclick="openReportModal()"
                class="primary-button"
            >
                <i class="fas fa-plus"></i>
                Create Report
            </button>

        </div>

    </div>

</div>

        <div class="statistics-grid">

            <div class="stat-card stat-blue">

                <div class="stat-content">

                    <div>
                        <p class="stat-label">Total Reports</p>
                        <p class="stat-number">2</p>

                        <p class="stat-note">
                            <i class="fas fa-file-alt"></i>
                            All time records
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                </div>

            </div>


            <div class="stat-card stat-amber">

                <div class="stat-content">

                    <div>
                        <p class="stat-label">Pending Issues</p>
                        <p class="stat-number">1</p>

                        <p class="stat-note">
                            <i class="fas fa-clock"></i>
                            Awaiting action
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>

                </div>

            </div>


            <div class="stat-card stat-orange">

                <div class="stat-content">

                    <div>
                        <p class="stat-label">Under Repair</p>
                        <p class="stat-number">1</p>

                        <p class="stat-note">
                            <i class="fas fa-wrench"></i>
                            In progress
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-wrench"></i>
                    </div>

                </div>

            </div>


            <div class="stat-card stat-green">

                <div class="stat-content">

                    <div>
                        <p class="stat-label">Completed</p>
                        <p class="stat-number">0</p>

                        <p class="stat-note">
                            <i class="fas fa-check-circle"></i>
                            Resolved issues
                        </p>
                    </div>

                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                </div>

            </div>

        </div>

        <section class="records-panel">

            <div class="records-toolbar">

                <div>
                    <h2 class="records-title">
                        Maintenance Records
                    </h2>

                    <p class="records-subtitle">
                        Showing all reported maintenance issues
                    </p>
                </div>

                <div class="filter-badges">

                    <span class="filter-badge badge-all">
                        All (2)
                    </span>

                    <span class="filter-badge badge-pending">
                        Pending (1)
                    </span>

                    <span class="filter-badge badge-repairing">
                        Repairing (1)
                    </span>

                    <span class="filter-badge badge-completed">
                        Completed (0)
                    </span>

                </div>

            </div>

            <div class="table-wrapper">

                <table class="maintenance-table">

                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Reported By</th>
                            <th>Category / Priority</th>
                            <th>Problem</th>
                            <th>Date Reported</th>
                            <th>Technician</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>


                        <tr>

                            <td>
                                <div class="room-number">
                                    Room 101
                                </div>

                                <div class="room-type">
                                    Deluxe Room
                                </div>
                            </td>

                            <td>
                                <span class="person-name">
                                    Maria Santos
                                </span>
                            </td>

                            <td>
                                <div class="category-priority">

                                    <span class="category-badge">
                                        Air Conditioning
                                    </span>

                                    <span class="priority-badge priority-high">
                                        High
                                    </span>

                                </div>
                            </td>

                            <td>

                                <div class="issue-title">
                                    Air Conditioner
                                </div>

                                <div class="issue-description">
                                    Not cooling properly
                                </div>

                            </td>

                            <td>
                                <span class="date-text">
                                    Jul 31, 2026
                                </span>
                            </td>

                            <td>
                                <span class="technician-text">
                                    John Reyes
                                </span>
                            </td>

                            <td>
                                <span class="status-badge status-pending">
                                    Pending
                                </span>
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button
                                        type="button"
                                        class="table-action view-button"
                                        onclick="openDetailModal(
                                            'Room 101',
                                            'Deluxe Room',
                                            'Maria Santos',
                                            'Air Conditioning',
                                            'Air Conditioner',
                                            'Not cooling properly',
                                            'High',
                                            'Jul 31, 2026',
                                            'Aug 2, 2026',
                                            'John Reyes',
                                            'Pending'
                                        )"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        class="table-action edit-button"
                                        onclick="openEditModal(
                                            'Room 101',
                                            'Deluxe Room',
                                            'Maria Santos',
                                            'Air Conditioning',
                                            'Air Conditioner',
                                            'Not cooling properly',
                                            'High',
                                            'Jul 31, 2026',
                                            'Aug 2, 2026',
                                            'John Reyes',
                                            'Pending'
                                        )"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Edit
                                    </button>

                                </div>

                            </td>

                        </tr>


                        <tr>

                            <td>
                                <div class="room-number">
                                    Room 205
                                </div>

                                <div class="room-type">
                                    Suite Room
                                </div>
                            </td>

                            <td>
                                <span class="person-name">
                                    Ana Cruz
                                </span>
                            </td>

                            <td>
                                <div class="category-priority">

                                    <span class="category-badge">
                                        Plumbing
                                    </span>

                                    <span class="priority-badge priority-medium">
                                        Medium
                                    </span>

                                </div>
                            </td>

                            <td>

                                <div class="issue-title">
                                    Bathroom Faucet
                                </div>

                                <div class="issue-description">
                                    Water leakage
                                </div>

                            </td>

                            <td>
                                <span class="date-text">
                                    Jul 30, 2026
                                </span>
                            </td>

                            <td>
                                <span class="technician-text">
                                    Pedro Lim
                                </span>
                            </td>

                            <td>
                                <span class="status-badge status-repairing">
                                    Repairing
                                </span>
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button
                                        type="button"
                                        class="table-action view-button"
                                        onclick="openDetailModal(
                                            'Room 205',
                                            'Suite Room',
                                            'Ana Cruz',
                                            'Plumbing',
                                            'Bathroom Faucet',
                                            'Water leakage',
                                            'Medium',
                                            'Jul 30, 2026',
                                            'Aug 1, 2026',
                                            'Pedro Lim',
                                            'Repairing'
                                        )"
                                    >
                                        <i class="fas fa-eye"></i>
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        class="table-action edit-button"
                                        onclick="openEditModal(
                                            'Room 205',
                                            'Suite Room',
                                            'Ana Cruz',
                                            'Plumbing',
                                            'Bathroom Faucet',
                                            'Water leakage',
                                            'Medium',
                                            'Jul 30, 2026',
                                            'Aug 1, 2026',
                                            'Pedro Lim',
                                            'Repairing'
                                        )"
                                    >
                                        <i class="fas fa-pen"></i>
                                        Edit
                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="mobile-records">



                <article class="mobile-record">

                    <div class="mobile-record-header">

                        <div>
                            <div class="mobile-room">
                                Room 101
                            </div>

                            <div class="mobile-room-type">
                                Deluxe Room
                            </div>
                        </div>

                        <span class="status-badge status-pending">
                            Pending
                        </span>

                    </div>

                    <div class="mobile-issue">

                        <p class="mobile-issue-title">
                            Air Conditioner
                        </p>

                        <p class="mobile-issue-description">
                            Not cooling properly
                        </p>

                    </div>

                    <div class="category-priority">

                        <span class="priority-badge priority-high">
                            High
                        </span>

                        <span class="category-badge">
                            Air Conditioning
                        </span>

                    </div>

                    <div class="mobile-meta">

                        <div class="meta-item">
                            <strong>Reported By</strong>
                            Maria Santos
                        </div>

                        <div class="meta-item">
                            <strong>Date Reported</strong>
                            Jul 31, 2026
                        </div>

                        <div class="meta-item">
                            <strong>Technician</strong>
                            John Reyes
                        </div>

                        <div class="meta-item">
                            <strong>Expected</strong>
                            Aug 2, 2026
                        </div>

                    </div>

                    <div class="mobile-actions">

                        <button
                            type="button"
                            class="table-action view-button"
                            onclick="openDetailModal(
                                'Room 101',
                                'Deluxe Room',
                                'Maria Santos',
                                'Air Conditioning',
                                'Air Conditioner',
                                'Not cooling properly',
                                'High',
                                'Jul 31, 2026',
                                'Aug 2, 2026',
                                'John Reyes',
                                'Pending'
                            )"
                        >
                            <i class="fas fa-eye"></i>
                            View
                        </button>

                        <button
                            type="button"
                            class="table-action edit-button"
                            onclick="openEditModal(
                                'Room 101',
                                'Deluxe Room',
                                'Maria Santos',
                                'Air Conditioning',
                                'Air Conditioner',
                                'Not cooling properly',
                                'High',
                                'Jul 31, 2026',
                                'Aug 2, 2026',
                                'John Reyes',
                                'Pending'
                            )"
                        >
                            <i class="fas fa-pen"></i>
                            Edit
                        </button>

                    </div>

                </article>

                <article class="mobile-record">

                    <div class="mobile-record-header">

                        <div>
                            <div class="mobile-room">
                                Room 205
                            </div>

                            <div class="mobile-room-type">
                                Suite Room
                            </div>
                        </div>

                        <span class="status-badge status-repairing">
                            Repairing
                        </span>

                    </div>

                    <div class="mobile-issue">

                        <p class="mobile-issue-title">
                            Bathroom Faucet
                        </p>

                        <p class="mobile-issue-description">
                            Water leakage
                        </p>

                    </div>

                    <div class="category-priority">

                        <span class="priority-badge priority-medium">
                            Medium
                        </span>

                        <span class="category-badge">
                            Plumbing
                        </span>

                    </div>

                    <div class="mobile-meta">

                        <div class="meta-item">
                            <strong>Reported By</strong>
                            Ana Cruz
                        </div>

                        <div class="meta-item">
                            <strong>Date Reported</strong>
                            Jul 30, 2026
                        </div>

                        <div class="meta-item">
                            <strong>Technician</strong>
                            Pedro Lim
                        </div>

                        <div class="meta-item">
                            <strong>Expected</strong>
                            Aug 1, 2026
                        </div>

                    </div>

                    <div class="mobile-actions">

                        <button
                            type="button"
                            class="table-action view-button"
                            onclick="openDetailModal(
                                'Room 205',
                                'Suite Room',
                                'Ana Cruz',
                                'Plumbing',
                                'Bathroom Faucet',
                                'Water leakage',
                                'Medium',
                                'Jul 30, 2026',
                                'Aug 1, 2026',
                                'Pedro Lim',
                                'Repairing'
                            )"
                        >
                            <i class="fas fa-eye"></i>
                            View
                        </button>

                        <button
                            type="button"
                            class="table-action edit-button"
                            onclick="openEditModal(
                                'Room 205',
                                'Suite Room',
                                'Ana Cruz',
                                'Plumbing',
                                'Bathroom Faucet',
                                'Water leakage',
                                'Medium',
                                'Jul 30, 2026',
                                'Aug 1, 2026',
                                'Pedro Lim',
                                'Repairing'
                            )"
                        >
                            <i class="fas fa-pen"></i>
                            Edit
                        </button>

                    </div>

                </article>

            </div>

        </section>

    </div>
</main>


<div id="reportModal" class="custom-modal">

    <div class="modal-box">

        <div class="modal-header">

            <div>
                <h2 class="modal-title">
                    Create Maintenance Report
                </h2>

                <p class="modal-subtitle">
                    Record a new maintenance issue for a room.
                </p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeReportModal()"
            >
                &times;
            </button>

        </div>


        <form class="modal-body">

            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-door-open"></i>
                    Room Information
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Room Number
                        </label>

                        <select class="form-control">
                            <option>Room 101</option>
                            <option>Room 205</option>
                            <option>Room 302</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Room Type
                        </label>

                        <select class="form-control">
                            <option>Deluxe Room</option>
                            <option>Suite Room</option>
                            <option>Standard Room</option>
                        </select>

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Reported By
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Housekeeper Name"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Issue Details
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Maintenance Category
                        </label>

                        <select class="form-control">
                            <option>Air Conditioning</option>
                            <option>Electrical</option>
                            <option>Plumbing</option>
                            <option>Furniture</option>
                            <option>Appliance</option>
                            <option>Others</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Priority Level
                        </label>

                        <select class="form-control">
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Problem Description
                        </label>

                        <textarea
                            class="form-control"
                            placeholder="Example: Air conditioner is not cooling properly."
                        ></textarea>

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Repair Schedule
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Date Reported
                        </label>

                        <input
                            type="date"
                            class="form-control"
                        >

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Expected Repair Date
                        </label>

                        <input
                            type="date"
                            class="form-control"
                        >

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Assigned Technician
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Technician Name"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-tasks"></i>
                    Status
                </h3>

                <div class="form-group">

                    <label class="form-label">
                        Current Status
                    </label>

                    <select class="form-control">
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                    </select>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="secondary-button"
                    onclick="closeReportModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="save-button"
                >
                    <i class="fas fa-save"></i>
                    Submit Report
                </button>

            </div>

        </form>

    </div>

</div>


<div id="detailModal" class="custom-modal">

    <div class="modal-box">

        <div class="modal-header">

            <div>
                <h2 class="modal-title">
                    Maintenance Report Details
                </h2>

                <p class="modal-subtitle">
                    Full details of the reported maintenance issue.
                </p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeDetailModal()"
            >
                &times;
            </button>

        </div>


        <div class="modal-body">

            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-door-open"></i>
                    Room Information
                </h3>

                <div class="detail-grid">

                    <div class="detail-item">
                        <div class="detail-label">Room Number</div>
                        <div id="detailRoom" class="detail-value">-</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Room Type</div>
                        <div id="detailRoomType" class="detail-value">-</div>
                    </div>

                    <div class="detail-item full">
                        <div class="detail-label">Reported By</div>
                        <div id="detailReportedBy" class="detail-value">-</div>
                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Issue Details
                </h3>

                <div class="detail-grid">

                    <div class="detail-item">
                        <div class="detail-label">Category</div>
                        <div id="detailCategory" class="detail-value">-</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Priority</div>
                        <div id="detailPriority" class="detail-value">-</div>
                    </div>

                    <div class="detail-item full">
                        <div class="detail-label">Problem</div>
                        <div id="detailProblem" class="detail-value">-</div>
                    </div>

                    <div class="detail-item full">
                        <div class="detail-label">Description</div>
                        <div id="detailDescription" class="detail-value">-</div>
                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Repair Schedule
                </h3>

                <div class="detail-grid">

                    <div class="detail-item">
                        <div class="detail-label">Date Reported</div>
                        <div id="detailDateReported" class="detail-value">-</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Expected Repair Date</div>
                        <div id="detailExpectedDate" class="detail-value">-</div>
                    </div>

                    <div class="detail-item full">
                        <div class="detail-label">Assigned Technician</div>
                        <div id="detailTechnician" class="detail-value">-</div>
                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-tasks"></i>
                    Status
                </h3>

                <div class="detail-item">

                    <div class="detail-label">
                        Current Status
                    </div>

                    <div>
                        <span
                            id="detailStatus"
                            class="status-badge status-pending"
                        >
                            -
                        </span>
                    </div>

                </div>

            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="secondary-button"
                onclick="closeDetailModal()"
            >
                Close
            </button>

        </div>

    </div>

</div>


<div id="editModal" class="custom-modal">

    <div class="modal-box">

        <div class="modal-header">

            <div>
                <h2 class="modal-title">
                    Edit Maintenance Report
                </h2>

                <p class="modal-subtitle">
                    Update the details of the reported maintenance issue.
                </p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeEditModal()"
            >
                &times;
            </button>

        </div>


        <form class="modal-body">

            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-door-open"></i>
                    Room Information
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Room Number
                        </label>

                        <select id="editRoom" class="form-control">
                            <option>Room 101</option>
                            <option>Room 205</option>
                            <option>Room 302</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Room Type
                        </label>

                        <select id="editRoomType" class="form-control">
                            <option>Deluxe Room</option>
                            <option>Suite Room</option>
                            <option>Standard Room</option>
                        </select>

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Reported By
                        </label>

                        <input
                            id="editReportedBy"
                            type="text"
                            class="form-control"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Issue Details
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Maintenance Category
                        </label>

                        <select id="editCategory" class="form-control">
                            <option>Air Conditioning</option>
                            <option>Electrical</option>
                            <option>Plumbing</option>
                            <option>Furniture</option>
                            <option>Appliance</option>
                            <option>Others</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Priority Level
                        </label>

                        <select id="editPriority" class="form-control">
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Problem
                        </label>

                        <input
                            id="editProblem"
                            type="text"
                            class="form-control"
                        >

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Problem Description
                        </label>

                        <textarea
                            id="editDescription"
                            class="form-control"
                        ></textarea>

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Repair Schedule
                </h3>

                <div class="form-grid">

                    <div class="form-group">

                        <label class="form-label">
                            Date Reported
                        </label>

                        <input
                            id="editDateReported"
                            type="text"
                            class="form-control"
                        >

                    </div>

                    <div class="form-group">

                        <label class="form-label">
                            Expected Repair Date
                        </label>

                        <input
                            id="editExpectedDate"
                            type="text"
                            class="form-control"
                        >

                    </div>

                    <div class="form-group full">

                        <label class="form-label">
                            Assigned Technician
                        </label>

                        <input
                            id="editTechnician"
                            type="text"
                            class="form-control"
                        >

                    </div>

                </div>

            </div>


            <div class="modal-section">

                <h3 class="modal-section-title">
                    <i class="fas fa-tasks"></i>
                    Status
                </h3>

                <div class="form-group">

                    <label class="form-label">
                        Current Status
                    </label>

                    <select id="editStatus" class="form-control">

                        <option>Pending</option>
                        <option>Repairing</option>
                        <option>In Progress</option>
                        <option>Completed</option>

                    </select>

                </div>

            </div>

        </form>


        <div class="modal-footer">

            <button
                type="button"
                class="secondary-button"
                onclick="closeEditModal()"
            >
                Cancel
            </button>

            <button
                type="button"
                class="save-button"
                onclick="closeEditModal()"
            >
                <i class="fas fa-save"></i>
                Save Changes
            </button>

        </div>

    </div>

</div>


<script>

    function showModal(id) {

        const modal = document.getElementById(id);

        modal.classList.add("active");

        document.body.style.overflow = "hidden";
    }


    function hideModal(id) {

        const modal = document.getElementById(id);

        modal.classList.remove("active");

        document.body.style.overflow = "";
    }


    function openDetailModal(
        room,
        roomType,
        reportedBy,
        category,
        problem,
        description,
        priority,
        dateReported,
        expectedDate,
        technician,
        status
    ) {

        document.getElementById("detailRoom").textContent = room;
        document.getElementById("detailRoomType").textContent = roomType;
        document.getElementById("detailReportedBy").textContent = reportedBy;
        document.getElementById("detailCategory").textContent = category;
        document.getElementById("detailPriority").textContent = priority;
        document.getElementById("detailProblem").textContent = problem;
        document.getElementById("detailDescription").textContent = description;
        document.getElementById("detailDateReported").textContent = dateReported;
        document.getElementById("detailExpectedDate").textContent = expectedDate;
        document.getElementById("detailTechnician").textContent = technician;

        const statusEl = document.getElementById("detailStatus");

        statusEl.textContent = status;

        statusEl.className = "status-badge";

        if (status === "Pending") {

            statusEl.classList.add("status-pending");

        } else if (
            status === "Repairing" ||
            status === "In Progress"
        ) {

            statusEl.classList.add("status-repairing");

        } else if (status === "Completed") {

            statusEl.classList.add("status-completed");

        } else {

            statusEl.classList.add("status-pending");

        }

        showModal("detailModal");
    }


    function closeDetailModal() {
        hideModal("detailModal");
    }


    function openEditModal(
        room,
        roomType,
        reportedBy,
        category,
        problem,
        description,
        priority,
        dateReported,
        expectedDate,
        technician,
        status
    ) {

        document.getElementById("editRoom").value = room;
        document.getElementById("editRoomType").value = roomType;
        document.getElementById("editReportedBy").value = reportedBy;
        document.getElementById("editCategory").value = category;
        document.getElementById("editPriority").value = priority;
        document.getElementById("editProblem").value = problem;
        document.getElementById("editDescription").value = description;
        document.getElementById("editDateReported").value = dateReported;
        document.getElementById("editExpectedDate").value = expectedDate;
        document.getElementById("editTechnician").value = technician;
        document.getElementById("editStatus").value = status;

        showModal("editModal");
    }


    function closeEditModal() {
        hideModal("editModal");
    }


    function openReportModal() {
        showModal("reportModal");
    }


    function closeReportModal() {
        hideModal("reportModal");
    }


    document.addEventListener("click", function (event) {

        if (event.target.id === "detailModal") {
            closeDetailModal();
        }

        if (event.target.id === "editModal") {
            closeEditModal();
        }

        if (event.target.id === "reportModal") {
            closeReportModal();
        }

    });


    document.addEventListener("keydown", function (event) {

        if (event.key !== "Escape") {
            return;
        }

        closeDetailModal();
        closeEditModal();
        closeReportModal();

    });


    document
        .getElementById("reportSearch")
        .addEventListener("input", function () {

            const searchValue = this.value.toLowerCase().trim();

            const rows = document.querySelectorAll(
                ".maintenance-table tbody tr"
            );

            rows.forEach(function (row) {

                const text = row.textContent.toLowerCase();

                row.style.display =
                    text.includes(searchValue)
                        ? ""
                        : "none";

            });


            const mobileRecords =
                document.querySelectorAll(".mobile-record");

            mobileRecords.forEach(function (record) {

                const text = record.textContent.toLowerCase();

                record.style.display =
                    text.includes(searchValue)
                        ? ""
                        : "none";

            });

        });

</script>

@endsection