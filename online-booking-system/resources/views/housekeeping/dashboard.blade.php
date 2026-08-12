@extends('housekeeping.layout')

@section('content')

<style>
    /* =========================================================
       DASHBOARD
    ========================================================= */

    .hk-dashboard {
        width: 100%;
        max-width: 100%;
        font-family: 'Poppins', sans-serif;
        color: #1f2937;
        animation: fadeIn 0.4s ease;
        box-sizing: border-box;
    }

    .hk-dashboard *,
    .hk-dashboard *::before,
    .hk-dashboard *::after {
        box-sizing: border-box;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    /* =========================================================
       HEADER
    ========================================================= */

    .dashboard-header {
        width: 100%;
        background: linear-gradient(135deg, #800000, #5c0000);
        border-radius: 18px;
        padding: 26px 30px;
        color: white;
        margin-bottom: 22px;
        box-shadow: 0 8px 22px rgba(92, 0, 0, 0.12);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .header-content > div:first-child {
        min-width: 0;
    }

    .header-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #ffbd91;
        margin-bottom: 6px;
    }

    .dashboard-header h1 {
        margin: 0;
        font-size: 27px;
        font-weight: 700;
        line-height: 1.25;
    }

    .dashboard-header p {
        margin: 7px 0 0;
        color: #f7d8cb;
        font-size: 13px;
        line-height: 1.5;
    }

    .date-card {
        min-width: 145px;
        padding: 12px 16px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }

    .date-title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ffbd91;
        font-weight: 600;
    }

    .date-value {
        margin-top: 4px;
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
    }


    /* =========================================================
       STATISTICS
    ========================================================= */

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
        width: 100%;
    }

    .stat-card {
        min-width: 0;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 17px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.035);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #800000;
    }

    .stat-top {
        display: flex;
        justify-content: flex-end;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .stat-card.total .stat-icon {
        background: #f3f4f6;
        color: #374151;
    }

    .stat-card.clean .stat-icon {
        background: #dcfce7;
        color: #15803d;
    }

    .stat-card.dirty .stat-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-card.progress .stat-icon {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-card.occupied .stat-icon {
        background: #ede9fe;
        color: #7c3aed;
    }

    .stat-label {
        margin-top: 11px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.4;
    }

    .stat-number {
        margin-top: 2px;
        font-size: 25px;
        font-weight: 700;
        color: #111827;
    }

    .stat-card.clean .stat-number {
        color: #15803d;
    }

    .stat-card.dirty .stat-number {
        color: #dc2626;
    }

    .stat-card.progress .stat-number {
        color: #d97706;
    }

    .stat-card.occupied .stat-number {
        color: #7c3aed;
    }


    /* =========================================================
       ROOM TABLE SECTION
    ========================================================= */

    .rooms-section {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .rooms-header {
        padding: 20px 22px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .rooms-title {
        min-width: 0;
    }

    .rooms-title h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }

    .rooms-title p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.5;
    }


    /* =========================================================
       LEGEND
    ========================================================= */

    .status-legend {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .legend-item::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-clean {
        background: #ecfdf5;
        color: #047857;
    }

    .legend-clean::before {
        background: #10b981;
    }

    .legend-dirty {
        background: #fef2f2;
        color: #b91c1c;
    }

    .legend-dirty::before {
        background: #ef4444;
    }

    .legend-progress {
        background: #fffbeb;
        color: #b45309;
    }

    .legend-progress::before {
        background: #f59e0b;
    }


    /* =========================================================
       TABLE
    ========================================================= */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    .rooms-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .rooms-table thead {
        background: #f8fafc;
    }

    .rooms-table th {
        padding: 13px 18px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .rooms-table td {
        padding: 15px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
        vertical-align: middle;
    }


    /* =========================================================
       EMPTY STATE
    ========================================================= */

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        width: 100%;
    }

    .empty-icon {
        width: 55px;
        height: 55px;
        margin: 0 auto 14px;
        border-radius: 14px;
        background: #f8eeee;
        color: #800000;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .empty-state h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #374151;
    }

    .empty-state p {
        margin: 6px auto 0;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.5;
        max-width: 450px;
    }


    /* =========================================================
       LARGE TABLETS / SMALL DESKTOP
    ========================================================= */

    @media (max-width: 1200px) {

        .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

    }


    /* =========================================================
       TABLET
    ========================================================= */

    @media (max-width: 900px) {

        .dashboard-header {
            padding: 24px;
        }

        .header-content {
            align-items: flex-start;
            flex-direction: column;
        }

        .date-card {
            width: 100%;
            min-width: 0;
        }

        .date-value {
            white-space: normal;
        }

        .rooms-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .status-legend {
            width: 100%;
            justify-content: flex-start;
        }

    }


    /* =========================================================
       SMALL TABLET
    ========================================================= */

    @media (max-width: 700px) {

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .stat-card {
            padding: 15px;
        }

        .dashboard-header {
            padding: 22px;
            border-radius: 15px;
        }

        .dashboard-header h1 {
            font-size: 23px;
        }

        .dashboard-header p {
            font-size: 12px;
        }

        .rooms-header {
            padding: 18px;
        }

        .rooms-title h2 {
            font-size: 17px;
        }

        .rooms-title p {
            font-size: 11px;
        }

        .table-wrapper {
            border-top: 0;
        }

    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 500px) {

        .hk-dashboard {
            width: 100%;
        }

        .dashboard-header {
            padding: 18px;
            margin-bottom: 16px;
            border-radius: 13px;
        }

        .header-label {
            font-size: 9px;
            letter-spacing: 1.2px;
        }

        .dashboard-header h1 {
            font-size: 20px;
        }

        .dashboard-header p {
            font-size: 11px;
            line-height: 1.5;
        }

        .date-card {
            padding: 10px 12px;
        }

        .date-title {
            font-size: 9px;
        }

        .date-value {
            font-size: 11px;
        }


        /* Statistics */

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 16px;
        }

        .stat-card {
            padding: 13px;
            border-radius: 11px;
        }

        .stat-card::before {
            width: 3px;
        }

        .stat-icon {
            width: 31px;
            height: 31px;
            border-radius: 8px;
            font-size: 13px;
        }

        .stat-label {
            margin-top: 9px;
            font-size: 10px;
        }

        .stat-number {
            font-size: 21px;
        }


        /* Room section */

        .rooms-section {
            border-radius: 12px;
        }

        .rooms-header {
            padding: 15px;
            gap: 14px;
        }

        .rooms-title h2 {
            font-size: 16px;
        }

        .rooms-title p {
            font-size: 10px;
        }

        .status-legend {
            gap: 6px;
        }

        .legend-item {
            padding: 5px 8px;
            font-size: 9px;
        }

        .legend-item::before {
            width: 6px;
            height: 6px;
        }


        /* Table */

        .rooms-table {
            min-width: 680px;
        }

        .rooms-table th {
            padding: 11px 13px;
            font-size: 9px;
        }

        .rooms-table td {
            padding: 12px 13px;
            font-size: 12px;
        }

        .empty-state {
            padding: 45px 15px;
        }

        .empty-icon {
            width: 48px;
            height: 48px;
            font-size: 19px;
            margin-bottom: 11px;
        }

        .empty-state h3 {
            font-size: 14px;
        }

        .empty-state p {
            font-size: 11px;
        }

    }


    /* =========================================================
       VERY SMALL MOBILE
    ========================================================= */

    @media (max-width: 360px) {

        .dashboard-header {
            padding: 15px;
        }

        .dashboard-header h1 {
            font-size: 18px;
        }

        .dashboard-header p {
            font-size: 10px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 14px;
        }

        .stat-top {
            justify-content: flex-start;
        }

        .rooms-header {
            padding: 13px;
        }

        .status-legend {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .legend-item {
            justify-content: center;
        }

        .rooms-table {
            min-width: 650px;
        }

    }
</style>


<div class="hk-dashboard">

    <!-- =====================================================
         DASHBOARD HEADER
    ====================================================== -->

    <div class="dashboard-header">

        <div class="header-content">

            <div>

                <div class="header-label">
                    Housekeeping Dashboard
                </div>

                <h1>
                    Room Cleaning Overview
                </h1>

                <p>
                    Monitor room readiness, cleaning progress, and housekeeping tasks.
                </p>

            </div>


            <div class="date-card">

                <div class="date-title">
                    Today
                </div>

                <div class="date-value">
                    {{ now()->format('F j, Y') }}
                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <div class="stats-grid">

        <div class="stat-card total">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>

            <div class="stat-label">
                Total Rooms
            </div>

            <div class="stat-number">
                0
            </div>

        </div>


        <div class="stat-card clean">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <div class="stat-label">
                Clean Rooms
            </div>

            <div class="stat-number">
                0
            </div>

        </div>


        <div class="stat-card dirty">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fas fa-broom"></i>
                </div>
            </div>

            <div class="stat-label">
                Dirty Rooms
            </div>

            <div class="stat-number">
                0
            </div>

        </div>


        <div class="stat-card progress">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>

            <div class="stat-label">
                In Progress
            </div>

            <div class="stat-number">
                0
            </div>

        </div>


        <div class="stat-card occupied">

            <div class="stat-top">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="stat-label">
                Occupied Rooms
            </div>

            <div class="stat-number">
                0
            </div>

        </div>

    </div>


    <!-- =====================================================
         ROOM CLEANING TABLE
    ====================================================== -->

    <div class="rooms-section">

        <div class="rooms-header">

            <div class="rooms-title">

                <h2>
                    Room Cleaning Status
                </h2>

                <p>
                    Room information will appear here once rooms are assigned.
                </p>

            </div>


            <div class="status-legend">

                <span class="legend-item legend-clean">
                    Clean
                </span>

                <span class="legend-item legend-dirty">
                    Dirty
                </span>

                <span class="legend-item legend-progress">
                    In Progress
                </span>

            </div>

        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="table-wrapper">

            <table class="rooms-table">

                <thead>

                    <tr>

                        <th>
                            Room
                        </th>

                        <th>
                            Floor
                        </th>

                        <th>
                            Room Status
                        </th>

                        <th>
                            Cleaning Status
                        </th>

                        <th>
                            Update Cleaning
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr>

                        <td colspan="5">

                            <div class="empty-state">

                                <div class="empty-icon">
                                    <i class="fas fa-bed"></i>
                                </div>

                                <h3>
                                    No Room Information Yet
                                </h3>

                                <p>
                                    Room information will appear here once rooms are added to the system.
                                </p>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection