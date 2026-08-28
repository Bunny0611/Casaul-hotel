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
        grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
        gap: 20px;
    }

    .request-table-panel {
        min-width: 0;
    }

    .request-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 20px;
        border-bottom: 1px solid #edf0f2;
    }

    .request-table-toolbar .panel-title {
        gap: 10px;
    }

    .request-table-toolbar .panel-title-icon {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        font-size: 12px;
    }

    .request-table-toolbar .panel-title h3 {
        font-size: 14px;
    }

    .request-table-toolbar p {
        margin: 2px 0 0;
        color: #8a94a6;
        font-size: 12px;
    }

    .view-all-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e5eaf0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .request-table-wrap {
        overflow-x: auto;
    }

    .request-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .request-table th {
        padding: 13px 15px;
        color: #718096;
        background: #fbfcfd;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .2px;
        text-align: left;
        text-transform: uppercase;
    }

    .request-table td {
        padding: 14px 15px;
        color: #516074;
        border-top: 1px solid #edf0f2;
        font-size: 12px;
        vertical-align: middle;
    }

    .request-table th:nth-child(1), .request-table td:nth-child(1) { width: 16%; }
    .request-table th:nth-child(2), .request-table td:nth-child(2) { width: 25%; }
    .request-table th:nth-child(3), .request-table td:nth-child(3) { width: 20%; }
    .request-table th:nth-child(4), .request-table td:nth-child(4) { width: 12%; }
    .request-table th:nth-child(5), .request-table td:nth-child(5) { width: 15%; }
    .request-table th:nth-child(6), .request-table td:nth-child(6) { width: 12%; }

    .request-id {
        display: block;
        color: #dc2626;
        font-weight: 700;
    }

    .new-badge, .addon-badge, .status-badge {
        display: inline-block;
        border-radius: 5px;
        padding: 3px 7px;
        font-size: 11px;
        font-weight: 600;
    }

    .new-badge { margin-top: 5px; color: #2563eb; background: #dbeafe; }
    .addon-badge { color: #dc2626; background: #fee2e2; }
    .status-badge { color: #475569; background: transparent; padding-left: 0; }
    .status-badge::before { content: ''; display: inline-block; width: 7px; height: 7px; margin-right: 6px; border-radius: 50%; background: #f59e0b; }
    .status-badge.in-progress::before { background: #2563eb; }
    .status-badge.completed::before { background: #10b981; }

    .guest-name, .room-name { display: block; color: #273449; font-weight: 600; }
    .room-detail, .date-detail { display: block; margin-top: 3px; color: #718096; font-size: 11px; }

    .view-request {
        width: 64px;
        height: 30px;
        padding: 0;
        color: #dc2626;
        background: #fff;
        border: 1px solid #f08a8a;
        border-radius: 6px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .request-details-page { display: none; }
    .request-details-page.show { display: block; }
    .details-breadcrumb { display: none; }
    .details-breadcrumb span { margin: 0 8px; color: #c0c7d1; }
    .details-heading { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 16px; }
    .details-heading h2 { margin: 0; color: #273449; font-size: 22px; }
    .details-heading p { margin: 4px 0 0; color: #718096; font-size: 12px; }
    .details-status { padding: 8px 13px; color: #c2410c; background: #fff7ed; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .details-grid { display: grid; grid-template-columns: minmax(0, 1fr) 245px; gap: 20px; }
    .details-grid.all-requests-mode { grid-template-columns: 1fr; }
    .details-grid.all-requests-mode .summary-card { display: none; }
    .details-main { display: grid; gap: 12px; min-width: 0; }
    .specific-request-content { display: none; }
    .specific-request-content.is-visible { display: block; }
    .details-lower.specific-request-content.is-visible { display: grid; }
    .details-actions.specific-request-content.is-visible { display: flex; }
    .details-card { padding: 16px 18px; background: #fff; border: 1px solid #e7e9ed; border-radius: 8px; box-shadow: 0 4px 14px rgba(15,23,42,.04); }
    .details-card h3 { margin: 0 0 14px; color: #991b1b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .all-requests-table { width: 100%; min-width: 0; border-collapse: collapse; table-layout: fixed; }
    .all-requests-table th { padding: 0 8px 10px; color: #718096; font-size: 10px; text-align: left; text-transform: uppercase; }
    .all-requests-table td { padding: 11px 8px; border-top: 1px solid #edf0f2; color: #475569; font-size: 13px; vertical-align: middle; }
    .all-requests-table th:nth-child(1), .all-requests-table td:nth-child(1) { width: 8%; }
    .all-requests-table th:nth-child(2), .all-requests-table td:nth-child(2) { width: 10%; }
    .all-requests-table th:nth-child(3), .all-requests-table td:nth-child(3) { width: 8%; }
    .all-requests-table th:nth-child(4), .all-requests-table td:nth-child(4) { width: 10%; }
    .all-requests-table th:nth-child(5), .all-requests-table td:nth-child(5) { width: 16%; }
    .all-requests-table th:nth-child(6), .all-requests-table td:nth-child(6) { width: 10%; }
    .all-requests-table th:nth-child(7), .all-requests-table td:nth-child(7) { width: 7%; }
    .all-requests-table th:nth-child(8), .all-requests-table td:nth-child(8) { width: 12%; }
    .all-requests-table th:nth-child(9), .all-requests-table td:nth-child(9) { width: 10%; }
    .all-requests-table th:nth-child(10), .all-requests-table td:nth-child(10) { width: 9%; }
    .all-requests-table td:first-child { color: #dc2626; font-weight: 700; }
    .all-requests-table small { color: #718096; font-size: 10px; font-weight: 400; }
    .all-requests-table td:nth-child(5), .all-requests-table td:nth-child(6), .all-requests-table td:nth-child(8) { overflow-wrap: anywhere; }
    .all-request-status { display: inline-block; min-width: 76px; padding: 5px 8px; border-radius: 6px; color: #92400e; background: #fff7ed; font-size: 10px; font-weight: 600; }
    .all-request-status.progress { color: #1d4ed8; background: #eff6ff; }
    .all-request-status.completed { color: #047857; background: #ecfdf5; }
    .priority-high, .priority-medium, .priority-low { display: inline-block; padding: 4px 7px; border-radius: 5px; font-size: 10px; font-weight: 600; }
    .priority-high { color: #b91c1c; background: #fee2e2; }
    .priority-medium { color: #b45309; background: #fef3c7; }
    .priority-low { color: #047857; background: #d1fae5; }
    .reservation-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .reservation-grid + .reservation-grid { margin-top: 14px; padding-top: 14px; border-top: 1px solid #edf0f2; }
    .detail-label { display: block; color: #8a94a6; font-size: 11px; text-transform: uppercase; }
    .detail-value { display: block; margin-top: 4px; color: #273449; font-size: 14px; font-weight: 600; }
    .addon-table { width: 100%; border-collapse: collapse; }
    .addon-table th { padding: 0 8px 10px; color: #718096; font-size: 10px; text-align: left; text-transform: uppercase; }
    .addon-table td { padding: 9px 8px; border-top: 1px solid #edf0f2; color: #475569; font-size: 13px; }
    .addon-table td:first-child { color: #7f1d1d; font-weight: 600; }
    .addon-status { display: inline-block; min-width: 72px; padding: 5px 8px; color: #475569; background: #fff7ed; border-radius: 6px; font-size: 10px; }
    .addon-status.delivered { color: #047857; background: #ecfdf5; }
    .details-lower { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .details-note { min-height: 54px; color: #475569; font-size: 14px; line-height: 1.5; }
    #housekeepingNotes {
        display: block;
        width: 100%;
        min-height: 110px;
        box-sizing: border-box;
        padding: 12px;
        border: 1px solid #dfe3e8;
        border-radius: 8px;
        resize: vertical;
    }
    #housekeepingNotes:focus {
        border-color: #ff6b35;
        outline: none;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.12);
    }
    .details-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 2px; }
    .details-action { height: 36px; padding: 0 14px; border: 1px solid #ff6b35; border-radius: 6px; color: #dc2626; background: #fff; font-family: inherit; font-size: 13px; font-weight: 600; }
    .details-action.primary { color: #fff; border-color: #dc2626; background: linear-gradient(90deg, #dc2626, #ff7a00); }
    .summary-card { height: fit-content; }
    .summary-total { display: flex; align-items: center; gap: 12px; padding: 15px; margin-bottom: 15px; background: #f8fafc; border-radius: 10px; }
    .summary-total i { color: #2563eb; font-size: 24px; }
    .summary-total strong { display: block; color: #273449; font-size: 20px; }
    .summary-total span { color: #475569; font-size: 10px; }
    .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #edf0f2; color: #475569; font-size: 11px; }
    .summary-row b { color: #273449; }
    .summary-info { padding: 12px 0; border-bottom: 1px solid #edf0f2; }
    .summary-info label { display: block; color: #8a94a6; font-size: 10px; text-transform: uppercase; }
    .summary-info strong { display: block; margin-top: 5px; color: #273449; font-size: 11px; }
    .details-back { display: inline-block; margin-bottom: 14px; padding: 0; color: #718096; background: none; border: 0; font-family: inherit; font-size: 12px; cursor: pointer; }

    .request-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        color: #718096;
        font-size: 10px;
        border-top: 1px solid #edf0f2;
    }

    .pagination { display: flex; align-items: center; gap: 8px; }
    .pagination button { width: 27px; height: 27px; color: #718096; background: #fff; border: 1px solid #e5eaf0; border-radius: 6px; }
    .pagination .current { color: #fff; background: #dc2626; border-color: #dc2626; }

    .request-side-stack { display: grid; gap: 14px; align-content: stretch; }
    .request-side-stack .request-panel { height: 100%; display: flex; flex-direction: column; border-radius: 15px; }
    .request-side-stack .panel-header { padding: 13px 15px; }
    .request-side-stack .panel-body { flex: 1; display: flex; padding: 12px 15px; }
    .request-side-stack .panel-title-icon { width: 29px; height: 29px; border-radius: 9px; font-size: 12px; }
    .request-side-stack .panel-title { gap: 9px; }
    .request-side-stack .panel-title h3 { font-size: 13px; }
    .request-side-stack .empty-state { flex: 1; min-height: 0; padding: 14px; }
    .request-side-stack .empty-icon { width: 30px; height: 30px; margin-bottom: 7px; font-size: 13px; }
    .request-side-stack .empty-state h4 { font-size: 11px; }
    .request-side-stack .empty-state p { margin-top: 3px; font-size: 10px; }

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
        font-size: 15px;
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
        font-size: 15px;
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

        .details-grid { grid-template-columns: 1fr; }
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

        .request-table-toolbar { align-items: flex-start; flex-direction: column; }

        .request-table-footer { align-items: flex-start; flex-direction: column; gap: 10px; }

        .details-heading { align-items: flex-start; flex-direction: column; }
        .reservation-grid, .details-lower { grid-template-columns: 1fr; }
        .addon-table { min-width: 620px; }

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
            <div class="request-panel request-table-panel">
                <div class="request-table-toolbar">
                    <div class="panel-title">
                        <div class="panel-title-icon"><i class="fas fa-inbox"></i></div>
                        <div>
                            <h3>Housekeeping Add-On Requests</h3>
                            <p>View and manage guest requested add-ons and amenities.</p>
                        </div>
                    </div>
                    <button type="button" class="view-all-button" onclick="openRequestDetails()">
                        <i class="fas fa-list"></i> View All Requests
                    </button>
                </div>

                <div class="request-table-wrap">
                    <table class="request-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Guest &amp; Room</th>
                                <th>Date</th>
                                <th>Add-Ons</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-state-content">
                                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                            <h4>No guest requests</h4>
                                            <p>There are no add-on requests to display.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="request-table-footer">
                    <span>Showing 0 to 0 of 0 requests</span>
                    <div class="pagination"><button type="button" disabled aria-label="Previous page"><i class="fas fa-chevron-left"></i></button><button type="button" disabled aria-label="Current page">0</button><button type="button" disabled aria-label="Next page"><i class="fas fa-chevron-right"></i></button></div>
                </div>
            </div>

            <div class="request-side-stack">
                <div class="request-panel">
                    <div class="panel-header">
                        <div class="panel-title"><div class="panel-title-icon"><i class="fas fa-comment-dots"></i></div><h3>Guest Message Box</h3></div>
                        <span class="panel-date"><i class="far fa-calendar-alt"></i> Today</span>
                    </div>
                    <div class="panel-body"><div class="empty-state"><div class="empty-state-content"><div class="empty-icon"><i class="fas fa-comments"></i></div><h4>No new messages</h4><p>You're all caught up!</p></div></div></div>
                </div>
            </div>
        </section>

    </div>

</main>

<section id="requestDetails" class="guest-request-page request-details-page" aria-hidden="true">
    <div class="guest-request-container">
        <button type="button" class="details-back" onclick="closeRequestDetails()"><i class="fas fa-chevron-left"></i> Back to Guest Requests</button>
        <p class="details-breadcrumb">Guest Requests <span>&gt;</span> <strong>Add-On Request Details</strong></p>

        <div class="details-heading">
            <div>
                <h2 id="detailsPageTitle">All Housekeeping Add-On Requests</h2>
                <p id="detailsPageDescription">View and manage all guest requested add-ons and amenities.</p>
            </div>
            <span id="detailsPageStatus" class="details-status"><i class="far fa-clock"></i> All Requests</span>
        </div>

        <div id="detailsGrid" class="details-grid">
            <div class="details-main">
                <div id="allRequestsCard" class="details-card">
                    <h3><i class="fas fa-list"></i> All Requests <small style="color:#718096;font-size:10px;font-weight:400;text-transform:none;">0 total requests</small></h3>
                    <div class="request-table-wrap">
                        <table class="all-requests-table">
                            <thead><tr><th>Request ID</th><th>Guest Name</th><th>Room Number</th><th>Request Type</th><th>Description / Preview</th><th>Preferred Time</th><th>Priority</th><th>Submitted Date / Time</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state">
                                            <div class="empty-state-content">
                                                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                                <h4>No guest requests</h4>
                                                <p>There are no requests to display.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="details-card specific-request-content">
                    <h3><i class="far fa-calendar-alt"></i> Reservation Information</h3>
                    <div class="reservation-grid">
                        <div><span class="detail-label">Reservation ID</span><span id="detailReservationId" class="detail-value">RES-3011</span></div>
                        <div><span class="detail-label">Guest Name</span><span id="detailGuestName" class="detail-value">Juan Dela Cruz</span></div>
                        <div><span class="detail-label">Room</span><span id="detailRoom" class="detail-value">Deluxe Room - 101</span></div>
                    </div>
                    <div class="reservation-grid">
                        <div><span class="detail-label">Check-in</span><span id="detailCheckIn" class="detail-value">Aug 25, 2026</span></div>
                        <div><span class="detail-label">Check-out</span><span id="detailCheckOut" class="detail-value">Aug 28, 2026</span></div>
                        <div><span class="detail-label">Nights / Guests</span><span id="detailNights" class="detail-value">3 Nights &nbsp;&nbsp; 2 Adults</span></div>
                    </div>
                </div>

                <div class="details-card specific-request-content">
                    <h3><i class="fas fa-concierge-bell"></i> Requested Add-Ons</h3>
                    <div class="request-table-wrap">
                        <table class="addon-table">
                            <thead><tr><th>Add-On Item</th><th>Quantity</th><th>Guest Note</th><th>Status</th></tr></thead>
                            <tbody>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <div class="empty-state-content">
                                                <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                                <h4>No requested add-ons</h4>
                                                <p>There are no add-on items to display.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="details-lower specific-request-content">
                    <div class="details-card"><h3><i class="far fa-comment"></i> Special Request</h3><div class="details-note">Please deliver the extra towels before 3:00 PM.<br>Thank you!</div></div>
                    <div class="details-card"><h3><i class="far fa-clock"></i> Estimated Arrival Time</h3><div class="details-note"><strong>3:00 PM</strong> <i class="fas fa-chevron-down" style="float:right"></i></div></div>
                </div>
                <div class="details-card specific-request-content"><h3><i class="far fa-edit"></i> Housekeeping Notes</h3><textarea id="housekeepingNotes" class="details-note" placeholder="Enter notes about request fulfillment, delivery time, or any issues..."></textarea></div>
                <div class="details-actions specific-request-content"><button type="button" class="details-action" onclick="saveRequestProgress()">Save Progress</button><button type="button" class="details-action primary" onclick="markAllDelivered()">Mark All as Delivered</button></div>
            </div>

            <aside id="summaryCard" class="details-card summary-card">
                <h3><i class="fas fa-clipboard-list"></i> Requests Summary</h3>
                <div class="summary-total"><i class="far fa-clipboard"></i><div><span>All Requests</span><strong>0</strong><span>0 Total Add-Ons</span></div></div>
                <div class="summary-row"><span><i class="fas fa-circle" style="color:#f59e0b;font-size:7px"></i> Pending</span><b>0</b></div>
                <div class="summary-row"><span><i class="fas fa-circle" style="color:#3b82f6;font-size:7px"></i> In Progress</span><b>0</b></div>
                <div class="summary-row"><span><i class="fas fa-circle" style="color:#10b981;font-size:7px"></i> Completed</span><b>0</b></div>
                <div class="summary-info"><label>Requested On</label><strong>Aug 24, 2026 &middot; 10:45 AM</strong></div>
                <div class="summary-info"><label>Requested By</label><strong id="summaryGuestName">Juan Dela Cruz<br><small>(Guest)</small></strong></div>
                <div class="summary-info"><label>Related Booking</label><strong id="summaryBooking">Deluxe Room - 101<br>Aug 25 - Aug 28, 2026</strong></div>
                <button type="button" class="details-action" style="width:100%;margin-top:15px" onclick="openRequestDetails()">View Reservation Details <i class="fas fa-arrow-right"></i></button>
            </aside>
        </div>
    </div>
</section>


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


        <form class="request-form" onsubmit="sendGuestMessage(event)">

            <div class="form-grid">

                <div>
                    <label class="form-label">
                        Guest Name
                    </label>

                    <input
                        type="text"
                        name="guest_name"
                        placeholder="Enter guest name"
                        required>
                </div>


                <div>
                    <label class="form-label">
                        Room Number
                    </label>

                    <input
                        type="text"
                        name="room_number"
                        placeholder="e.g. 101"
                        required>
                </div>

            </div>


            <div class="form-group">

                <label class="form-label">
                    Request Type
                </label>

                <select name="request_type">

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
                    name="message"
                    placeholder="Write the guest's message here..."
                    required></textarea>

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

function openRequestDetails() {
    const overview = document.querySelector('.guest-request-page:not(.request-details-page)');
    const details = document.getElementById('requestDetails');
    const detailsGrid = document.getElementById('detailsGrid');
    const allRequests = document.getElementById('allRequestsCard');
    const specificContent = document.querySelectorAll('.specific-request-content');
    const title = document.getElementById('detailsPageTitle');
    const description = document.getElementById('detailsPageDescription');
    const status = document.getElementById('detailsPageStatus');

    if (overview && details) {
        overview.style.display = 'none';
        details.classList.add('show');
        detailsGrid.classList.add('all-requests-mode');
        details.setAttribute('aria-hidden', 'false');
        allRequests.style.display = '';
        specificContent.forEach((element) => {
            element.classList.remove('is-visible');
            element.style.display = '';
        });
        title.textContent = 'All Housekeeping Add-On Requests';
        description.textContent = 'View and manage all guest requested add-ons and amenities.';
        status.innerHTML = '<i class="fas fa-list"></i> All Requests';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function openGuestRequest(requestId) {
    const overview = document.querySelector('.guest-request-page:not(.request-details-page)');
    const details = document.getElementById('requestDetails');
    const detailsGrid = document.getElementById('detailsGrid');
    const allRequests = document.getElementById('allRequestsCard');
    const specificContent = document.querySelectorAll('.specific-request-content');
    const title = document.getElementById('detailsPageTitle');
    const description = document.getElementById('detailsPageDescription');
    const status = document.getElementById('detailsPageStatus');
    const requests = {
        'REQ-3011': { reservation: 'RES-3011', guest: 'Juan Dela Cruz', room: 'Deluxe Room - 101', checkIn: 'Aug 25, 2026', checkOut: 'Aug 28, 2026', nights: '3 Nights &nbsp;&nbsp; 2 Adults', status: 'Pending' },
        'REQ-3010': { reservation: 'RES-3010', guest: 'Maria Santos', room: 'Standard Room - 205', checkIn: 'Aug 24, 2026', checkOut: 'Aug 26, 2026', nights: '2 Nights &nbsp;&nbsp; 2 Adults', status: 'In Progress' },
        'REQ-3009': { reservation: 'RES-3009', guest: 'Ana Reyes', room: 'Suite Room - 301', checkIn: 'Aug 23, 2026', checkOut: 'Aug 27, 2026', nights: '4 Nights &nbsp;&nbsp; 3 Adults', status: 'Completed' }
    };
    const request = requests[requestId];

    if (overview && details && request) {
        currentGuestRequestId = requestId;
        overview.style.display = 'none';
        details.classList.add('show');
        detailsGrid.classList.remove('all-requests-mode');
        details.setAttribute('aria-hidden', 'false');
        allRequests.style.display = 'none';
        specificContent.forEach((element) => element.classList.add('is-visible'));
        title.textContent = 'Housekeeping Add-On Request';
        description.textContent = 'View and manage this guest\'s requested add-ons and amenities.';
        status.innerHTML = '<i class="far fa-clock"></i> Status: ' + (localStorage.getItem('housekeeping-request-status-' + requestId) || request.status);
        document.getElementById('detailReservationId').textContent = request.reservation;
        document.getElementById('detailGuestName').textContent = request.guest;
        document.getElementById('detailRoom').textContent = request.room;
        document.getElementById('detailCheckIn').textContent = request.checkIn;
        document.getElementById('detailCheckOut').textContent = request.checkOut;
        document.getElementById('detailNights').innerHTML = request.nights;
        document.getElementById('summaryGuestName').innerHTML = request.guest + '<br><small>(Guest)</small>';
        document.getElementById('summaryBooking').innerHTML = request.room + '<br>' + request.checkIn + ' - ' + request.checkOut;
        document.getElementById('housekeepingNotes').value = localStorage.getItem('housekeeping-request-notes-' + requestId) || '';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function closeRequestDetails() {
    const overview = document.querySelector('.guest-request-page:not(.request-details-page)');
    const details = document.getElementById('requestDetails');

    if (overview && details) {
        overview.style.display = '';
        details.classList.remove('show');
        details.setAttribute('aria-hidden', 'true');
    }
}

let currentGuestRequestId = null;

function saveRequestProgress() {
    if (!currentGuestRequestId) return;
    localStorage.setItem(
        'housekeeping-request-notes-' + currentGuestRequestId,
        document.getElementById('housekeepingNotes').value
    );
    alert('Housekeeping progress saved.');
}

function markAllDelivered() {
    if (!currentGuestRequestId) return;
    document.querySelectorAll('.addon-status').forEach((element) => {
        element.classList.add('delivered');
        element.innerHTML = 'Delivered <i class="fas fa-chevron-down"></i>';
    });
    document.getElementById('detailsPageStatus').innerHTML = '<i class="fas fa-check-circle"></i> Status: Completed';
    localStorage.setItem('housekeeping-request-status-' + currentGuestRequestId, 'Completed');
    alert('All requested add-ons marked as delivered.');
}

function sendGuestMessage(event) {
    event.preventDefault();
    const form = event.target;
    if (!form.reportValidity()) return;
    closeRequestModal();
    form.reset();
    alert('Guest message sent successfully.');
}

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
