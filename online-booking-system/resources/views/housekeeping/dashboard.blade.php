```blade
@extends('housekeeping.layout')

@section('content')

<style>

    .hk-dashboard {
        font-family: 'Poppins', sans-serif;
        color: #1f2937;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dashboard-header {
        background: linear-gradient(135deg, #800000, #5c0000);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(92, 0, 0, 0.12);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        right: -60px;
        top: -80px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 25px;
        position: relative;
        z-index: 2;
    }

    .header-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #ffbd91;
        margin-bottom: 8px;
    }

    .dashboard-header h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
    }

    .dashboard-header p {
        margin: 8px 0 0;
        color: #f7d8cb;
        font-size: 14px;
        line-height: 1.6;
        max-width: 650px;
    }

    .date-card {
        min-width: 150px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 15px;
        padding: 15px 18px;
        backdrop-filter: blur(8px);
    }

    .date-card .date-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ffbd91;
    }

    .date-card .date-value {
        margin-top: 5px;
        font-size: 14px;
        font-weight: 500;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 25px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: #800000;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
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
        color: #6b7280;
        font-size: 13px;
        margin-top: 14px;
    }

    .stat-number {
        font-size: 30px;
        font-weight: 700;
        margin-top: 3px;
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

    .rooms-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .rooms-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }

    .rooms-title h2 {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: #111827;
    }

    .rooms-title p {
        margin: 5px 0 0;
        font-size: 13px;
        color: #6b7280;
    }

    .status-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
    }

    .legend-item::before {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
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

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }

    .room-card {
        border-radius: 18px;
        padding: 20px;
        border: 1px solid;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.07);
    }

    .room-card.clean {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .room-card.dirty {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .room-card.in-progress {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .room-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .room-number {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }

    .room-type {
        margin-top: 2px;
        font-size: 12px;
        color: #6b7280;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 0 5px rgba(255,255,255,0.7);
    }

    .clean .status-dot {
        background: #16a34a;
    }

    .dirty .status-dot {
        background: #dc2626;
    }

    .in-progress .status-dot {
        background: #d97706;
    }

    .room-info {
        background: rgba(255,255,255,0.65);
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 16px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        padding: 5px 0;
    }

    .info-row:not(:last-child) {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .info-label {
        color: #6b7280;
    }

    .info-value {
        color: #374151;
        font-weight: 600;
        text-align: right;
    }

    .room-status {
        display: inline-flex;
        padding: 4px 9px;
        border-radius: 20px;
        background: #111827;
        color: white;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .status-form label {
        display: block;
        margin-bottom: 7px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
    }

    .status-select {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: white;
        color: #374151;
        font-size: 13px;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-select:hover {
        border-color: #9ca3af;
    }

    .status-select:focus {
        border-color: #800000;
        box-shadow: 0 0 0 3px rgba(128,0,0,0.08);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border: 1px dashed #d1d5db;
        border-radius: 18px;
    }

    .empty-state i {
        font-size: 42px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    .empty-state p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .rooms-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 900px) {
        .header-content {
            align-items: flex-start;
            flex-direction: column;
        }

        .date-card {
            width: 100%;
        }

        .rooms-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .status-legend {
            width: 100%;
        }
    }

    @media (max-width: 700px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .rooms-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-header {
            padding: 22px;
        }

        .dashboard-header h1 {
            font-size: 24px;
        }

        .rooms-section {
            padding: 18px;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-header h1 {
            font-size: 21px;
        }

        .dashboard-header p {
            font-size: 12px;
        }

        .stat-number {
            font-size: 26px;
        }
    }
</style>


<div class="hk-dashboard">

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
                    Stay on top of room readiness, cleaning progress,
                    and priority tasks from one streamlined workspace.
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
                {{ $totalRooms }}
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
                {{ $cleanRooms }}
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
                {{ $dirtyRooms }}
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
                {{ $inProgress }}
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
                {{ $occupiedRooms }}
            </div>

        </div>

    </div>

    <div class="rooms-section">

        <div class="rooms-header">

            <div class="rooms-title">

                <h2>
                    Room Cleaning Status
                </h2>

                <p>
                    Update cleaning progress and monitor room readiness.
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


        <div class="rooms-grid">

            @forelse($rooms as $room)

                @php
                    $roomClass = match($room->cleaning_status) {
                        'clean' => 'clean',
                        'dirty' => 'dirty',
                        default => 'in-progress'
                    };
                @endphp


                <div class="room-card {{ $roomClass }}">

                    <div class="room-top">

                        <div>
                            <div class="room-number">
                                {{ $room->room_number }}
                            </div>

                            <div class="room-type">
                                {{ $room->room_type }}
                            </div>
                        </div>

                        <div class="status-dot"></div>

                    </div>


                    <div class="room-info">

                        <div class="info-row">

                            <span class="info-label">
                                Floor
                            </span>

                            <span class="info-value">
                                {{ $room->floor ?? 'N/A' }}
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="info-label">
                                Room Status
                            </span>

                            <span class="info-value">
                                <span class="room-status">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </span>

                        </div>


                        <div class="info-row">

                            <span class="info-label">
                                Cleaning
                            </span>

                            <span class="info-value">
                                {{ ucfirst(str_replace('_', ' ', $room->cleaning_status)) }}
                            </span>

                        </div>

                    </div>



                    <form
                        method="POST"
                        action="{{ route('housekeeping.rooms.cleaning', $room->id) }}"
                        class="status-form"
                    >

                        @csrf
                        @method('PATCH')

                        <label>
                            Update Cleaning Status
                        </label>

                        <select
                            name="cleaning_status"
                            onchange="this.form.submit()"
                            class="status-select"
                        >

                            <option
                                value="clean"
                                {{ $room->cleaning_status === 'clean' ? 'selected' : '' }}
                            >
                                Clean
                            </option>

                            <option
                                value="dirty"
                                {{ $room->cleaning_status === 'dirty' ? 'selected' : '' }}
                            >
                                Dirty
                            </option>

                            <option
                                value="in_progress"
                                {{ $room->cleaning_status === 'in_progress' ? 'selected' : '' }}
                            >
                                In Progress
                            </option>

                        </select>

                    </form>

                </div>

            @empty

                <div class="empty-state">

                    <i class="fas fa-bed"></i>

                    <p>
                        No rooms available for housekeeping.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection
```
