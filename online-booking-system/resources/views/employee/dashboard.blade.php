@extends('employee.layout')

@section('content')
<style>
    .dashboard-shell {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.2rem;
        margin-bottom: 1.4rem;
    }

    .summary-card {
        background: #ffffff;
        border: 1px solid #f0e0d8;
        border-left: 5px solid #ff8c42;
        border-radius: 1.2rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        padding: 1.2rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .summary-card.orange { border-left-color: #ff8c42; }
    .summary-card.red { border-left-color: #ef4444; }
    .summary-card.green { border-left-color: #10b981; }
    .summary-card.yellow { border-left-color: #f59e0b; }

    .summary-card .icon-wrap {
        width: 3rem;
        height: 3rem;
        border-radius: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .summary-card.orange .icon-wrap { background: #fff4eb; color: #ff8c42; }
    .summary-card.red .icon-wrap { background: #fef2f2; color: #ef4444; }
    .summary-card.green .icon-wrap { background: #ecfdf5; color: #10b981; }
    .summary-card.yellow .icon-wrap { background: #fffbeb; color: #f59e0b; }

    .summary-card h4 {
        margin: 0.25rem 0 0;
        font-size: 1.7rem;
        color: #111827;
    }

    .summary-card p {
        margin: 0;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.35;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 1.2rem;
    }

    .dashboard-panel {
        background: #ffffff;
        border: 1px solid #f0e0d8;
        border-radius: 1.2rem;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        padding: 1.2rem 1.2rem 1rem;
    }

    .panel-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.9rem;
    }

    .panel-title h3 {
        margin: 0;
        font-size: 1.05rem;
        color: #111827;
    }

    .panel-title span {
        color: #9ca3af;
        font-size: 0.85rem;
    }

    .progress-track {
        width: 100%;
        height: 0.7rem;
        background: #f3f4f6;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 0.6rem;
    }

    .progress-fill {
        height: 100%;
        width: 0;
        border-radius: inherit;
        background: linear-gradient(90deg, #ff8c42 0%, #ff6b35 100%);
        transition: width 0.8s ease;
    }

    .occupancy-copy {
        color: #4b5563;
        font-size: 0.95rem;
        margin: 0;
    }

    .snapshot-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.7rem;
        margin-top: 0.9rem;
    }

    .snapshot-card {
        background: #fffaf7;
        border: 1px solid #f6dfcc;
        border-radius: 0.9rem;
        padding: 0.7rem 0.75rem;
    }

    .snapshot-card strong {
        display: block;
        font-size: 1.05rem;
        color: #111827;
        margin-bottom: 0.2rem;
    }

    .snapshot-card span {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 0.8rem;
    }

    .activity-item {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.7rem 0.8rem;
        background: #fffaf7;
        border: 1px solid #f7e7dc;
        border-radius: 0.9rem;
    }

    .activity-icon {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ffe8d9;
        color: #c2410c;
        flex-shrink: 0;
    }

    .activity-item strong {
        display: block;
        color: #111827;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }

    .activity-item small {
        color: #9ca3af;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.8rem;
    }

    .quick-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        text-decoration: none;
        color: #111827;
        background: #fdf8f4;
        border: 1px solid #f4dfd0;
        border-radius: 0.9rem;
        padding: 0.85rem 0.9rem;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .quick-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
    }

    .quick-action i {
        color: #c2410c;
    }

    @media (max-width: 960px) {
        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .summary-card {
            padding: 1rem;
        }

        .dashboard-panel {
            padding: 1rem;
        }

        .panel-title h3 {
            font-size: 1rem;
        }

        .occupancy-copy {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 640px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .dashboard-shell {
            padding: 0 0.75rem;
        }

        .summary-card,
        .dashboard-panel,
        .snapshot-card,
        .activity-item {
            border-radius: 1rem;
        }

        .panel-title {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .activity-list {
            gap: 0.6rem;
        }
    }
</style>

<div class="dashboard-shell">
    <div class="summary-grid">
        <div class="summary-card orange">
            <div>
                <p>Today's<br>arrivals</p>
                <h4>{{ $todayArrivals }}</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-user-plus"></i></div>
        </div>

        <div class="summary-card red">
            <div>
                <p>Today's<br>departures</p>
                <h4>{{ $todayDepartures }}</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-sign-out-alt"></i></div>
        </div>

        <div class="summary-card green">
            <div>
                <p>Available<br>rooms</p>
                <h4>{{ $availableRooms }}</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-bed"></i></div>
        </div>

        <div class="summary-card yellow">
            <div>
                <p>Pending<br>reservations</p>
                <h4>{{ $pendingRequests }}</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-bell"></i></div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-panel">
            <div class="panel-title">
                <h3>Occupancy Rate</h3>
                <span>Live overview</span>
            </div>
            <div class="progress-track" aria-label="Occupancy rate">
                <div class="progress-fill" id="occupancyBar"></div>
            </div>
            <p class="occupancy-copy">{{ $occupancyRate }}% occupied ({{ $occupiedRooms }} of {{ $totalRooms }} rooms)</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-[#f6dfcc] bg-[#fff8f2] p-4">
                    <div class="text-sm text-[#9ca3af]">Occupied</div>
                    <div class="text-lg font-bold text-[#111827]">{{ $occupiedRooms }} Rooms</div>
                </div>
                <div class="rounded-xl border border-[#e5e7eb] bg-[#f8fafc] p-4">
                    <div class="text-sm text-[#9ca3af]">Available</div>
                    <div class="text-lg font-bold text-[#111827]">{{ $availableRooms }} Rooms</div>
                </div>
            </div>

            <div class="snapshot-grid">
                <div class="snapshot-card">
                    <strong>{{ $occupancyRate }}%</strong>
                    <span>Occupancy rate</span>
                </div>
                <div class="snapshot-card">
                    <strong>{{ $todayArrivals }}</strong>
                    <span>Arrivals today</span>
                </div>
                <div class="snapshot-card">
                    <strong>{{ $pendingRequests }}</strong>
                    <span>Pending reservations</span>
                </div>
                <div class="snapshot-card">
                    <strong>{{ $maintenanceRooms }}</strong>
                    <span>Under maintenance</span>
                </div>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="panel-title">
                <h3>Recent Activity</h3>
                <span>Latest updates</span>
            </div>
            @if($recentActivity->isNotEmpty())
                <ul class="activity-list">
                    @foreach($recentActivity as $activity)
                        <li class="activity-item">
                            <div class="activity-icon"><i class="{{ $activity['icon'] }}"></i></div>
                            <div>
                                <strong>{{ $activity['title'] }}</strong>
                                <small>{{ $activity['time'] }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center text-sm text-gray-500">
                    No recent activity yet.
                </div>
            @endif
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const occupancyBar = document.getElementById('occupancyBar');

        if (occupancyBar) {
            setTimeout(function () {
                occupancyBar.style.width = '{{ $occupancyRate }}%';
            }, 120);
        }
    });
</script>
@endsection
