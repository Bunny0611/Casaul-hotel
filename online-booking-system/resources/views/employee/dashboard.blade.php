@extends('employee.layout')

@section('content')
<style>
    .dashboard-shell {
        max-width: 1200px;
        margin: 0 auto;
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

    @media (max-width: 640px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-shell">
    <div class="summary-grid">
        <div class="summary-card orange">
            <div>
                <p>Today's<br>arrivals</p>
                <h4>18</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-user-plus"></i></div>
        </div>

        <div class="summary-card red">
            <div>
                <p>Today's<br>departures</p>
                <h4>12</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-sign-out-alt"></i></div>
        </div>

        <div class="summary-card green">
            <div>
                <p>Available<br>rooms</p>
                <h4>26</h4>
            </div>
            <div class="icon-wrap"><i class="fas fa-bed"></i></div>
        </div>

        <div class="summary-card yellow">
            <div>
                <p>Pending<br>requests</p>
                <h4>09</h4>
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
            <p class="occupancy-copy">75% occupied (39 of 52 rooms)</p>
            <div style="margin-top: 0.9rem; display: flex; gap: 0.7rem; flex-wrap: wrap;">
                <div style="background: #fff8f2; border: 1px solid #f6dfcc; border-radius: 0.8rem; padding: 0.65rem 0.75rem; flex: 1; min-width: 120px;">
                    <div style="font-size: 0.8rem; color: #9ca3af;">Occupied</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #111827;">39 Rooms</div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 0.8rem; padding: 0.65rem 0.75rem; flex: 1; min-width: 120px;">
                    <div style="font-size: 0.8rem; color: #9ca3af;">Available</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #111827;">13 Rooms</div>
                </div>
            </div>
        </div>

        <div class="dashboard-panel">
            <div class="panel-title">
                <h3>Recent Activity</h3>
                <span>Latest updates</span>
            </div>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon"><i class="fas fa-user-check"></i></div>
                    <div>
                        <strong>Guest John Doe checked in.</strong>
                        <small>10 mins ago</small>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon"><i class="fas fa-broom"></i></div>
                    <div>
                        <strong>Room 203 marked as cleaned.</strong>
                        <small>22 mins ago</small>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <strong>Reservation #102 approved.</strong>
                        <small>41 mins ago</small>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <strong>Guest requested late checkout.</strong>
                        <small>1 hr ago</small>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <strong>Payment received for Suite 401.</strong>
                        <small>2 hrs ago</small>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="dashboard-panel" style="margin-top: 1.2rem;">
        <div class="panel-title">
            <h3>Quick Actions</h3>
            <span>Fast links</span>
        </div>
        <div class="quick-actions">
            <a href="{{ route('employee.reservation') }}" class="quick-action">
                <i class="fas fa-calendar-plus"></i>
                <span>New Reservation</span>
            </a>
            <a href="{{ route('employee.checkin') }}" class="quick-action">
                <i class="fas fa-sign-in-alt"></i>
                <span>Check In</span>
            </a>
            <a href="{{ route('employee.checkin') }}" class="quick-action">
                <i class="fas fa-sign-out-alt"></i>
                <span>Check Out</span>
            </a>
            <a href="{{ route('employee.reservation') }}" class="quick-action">
                <i class="fas fa-list"></i>
                <span>View Reservations</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const occupancyBar = document.getElementById('occupancyBar');

        if (occupancyBar) {
            setTimeout(function () {
                occupancyBar.style.width = '75%';
            }, 120);
        }
    });
</script>
@endsection
