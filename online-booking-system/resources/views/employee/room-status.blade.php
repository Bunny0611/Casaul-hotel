@extends('employee.layout')

@section('pageTitle', 'Room Status')

@section('content')
<link rel="stylesheet" href="{{ asset('css/employee-room-status.css') }}">

<div class="room-status-page">
    <section class="room-status-shell" aria-label="Room status overview">
        <div class="page-header">
            <div>
                <h3 class="page-title">Room Overview</h3>
                <p class="page-subtitle">View room availability and housekeeping status</p>
            </div>
        </div>

        <div class="summary-grid">
            <article class="summary-card" data-tone="available">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Available Rooms</span>
                    <span class="summary-dot" style="background:#16a34a"></span>
                </div>
                <div class="summary-value">12</div>
            </article>
            <article class="summary-card" data-tone="reserved">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Reserved Rooms</span>
                    <span class="summary-dot" style="background:#2563eb"></span>
                </div>
                <div class="summary-value">8</div>
            </article>
            <article class="summary-card" data-tone="occupied">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Occupied Rooms</span>
                    <span class="summary-dot" style="background:#dc2626"></span>
                </div>
                <div class="summary-value">17</div>
            </article>
            <article class="summary-card" data-tone="dirty">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Dirty Rooms</span>
                    <span class="summary-dot" style="background:#6b7280"></span>
                </div>
                <div class="summary-value">4</div>
            </article>
            <article class="summary-card" data-tone="cleaning">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Cleaning Rooms</span>
                    <span class="summary-dot" style="background:#7c3aed"></span>
                </div>
                <div class="summary-value">3</div>
            </article>
            <article class="summary-card" data-tone="maintenance">
                <div class="summary-card-head">
                    <span class="summary-label"><span class="summary-icon"></span>Maintenance Rooms</span>
                    <span class="summary-dot" style="background:#d97706"></span>
                </div>
                <div class="summary-value">2</div>
            </article>
        </div>

        <form class="filter-panel" id="filter-form">
            <div class="field-group">
                <label class="field-label" for="search-room-number">Search Room Number</label>
                <input class="field-input" id="search-room-number" type="text" placeholder="e.g. 101">
            </div>
            <div class="field-group">
                <label class="field-label" for="search-room-type">Search Room Type</label>
                <input class="field-input" id="search-room-type" type="text" placeholder="e.g. Deluxe">
            </div>
            <div class="field-group">
                <label class="field-label" for="filter-status">Filter by Status</label>
                <select class="field-select" id="filter-status">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="reserved">Reserved</option>
                    <option value="occupied">Occupied</option>
                    <option value="dirty">Dirty</option>
                    <option value="cleaning">Cleaning</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="field-group">
                <label class="field-label" for="filter-floor">Filter by Floor</label>
                <select class="field-select" id="filter-floor">
                    <option value="">All Floors</option>
                    <option value="1">Floor 1</option>
                    <option value="2">Floor 2</option>
                    <option value="3">Floor 3</option>
                </select>
            </div>
            <button class="secondary-btn" id="reset-filters" type="button">Reset Filters</button>
        </form>

        <div class="table-card">
            <div class="table-scroll-hint">Tap the Details button for full room information</div>
            <div class="table-scroll">
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Room Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Housekeeping Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr data-room-row
                        data-room-number="101"
                        data-room-type="Deluxe"
                        data-floor="1"
                        data-capacity="2"
                        data-status="available"
                        data-status-label="Available"
                        data-housekeeping-status="Ready"
                        data-housekeeper="Maria Santos"
                        data-guest="—"
                        data-checkin="—"
                        data-checkout="—"
                        data-notes="Ready for arrival and inspected this morning."
                        data-room-label="101">
                        <td>101</td>
                        <td>Deluxe</td>
                        <td>Floor 1</td>
                        <td><span class="status-badge available">Available</span></td>
                        <td><span class="housekeeping-pill">Ready</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    <tr data-room-row
                        data-room-number="205"
                        data-room-type="Executive"
                        data-floor="2"
                        data-capacity="4"
                        data-status="occupied"
                        data-status-label="Occupied"
                        data-housekeeping-status="Not Required"
                        data-housekeeper="Rina Cruz"
                        data-guest="Sofia Alvarez"
                        data-checkin="2026-07-28"
                        data-checkout="2026-08-02"
                        data-notes="Guest requests extra towels and late checkout."
                        data-room-label="205">
                        <td>205</td>
                        <td>Executive</td>
                        <td>Floor 2</td>
                        <td><span class="status-badge occupied">Occupied</span></td>
                        <td><span class="housekeeping-pill">Not Required</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    <tr data-room-row
                        data-room-number="312"
                        data-room-type="Suite"
                        data-floor="3"
                        data-capacity="6"
                        data-status="cleaning"
                        data-status-label="Cleaning"
                        data-housekeeping-status="In Progress"
                        data-housekeeper="Jessa Lim"
                        data-guest="—"
                        data-checkin="—"
                        data-checkout="—"
                        data-notes="Deep cleaning scheduled before the next arrival."
                        data-room-label="312">
                        <td>312</td>
                        <td>Suite</td>
                        <td>Floor 3</td>
                        <td><span class="status-badge cleaning">Cleaning</span></td>
                        <td><span class="housekeeping-pill">In Progress</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    <tr data-room-row
                        data-room-number="408"
                        data-room-type="Deluxe"
                        data-floor="4"
                        data-capacity="2"
                        data-status="reserved"
                        data-status-label="Reserved"
                        data-housekeeping-status="Pending Touch-up"
                        data-housekeeper="Nico Dela Cruz"
                        data-guest="Arielle Gomez"
                        data-checkin="2026-08-01"
                        data-checkout="2026-08-03"
                        data-notes="Check-in scheduled for late afternoon."
                        data-room-label="408">
                        <td>408</td>
                        <td>Deluxe</td>
                        <td>Floor 4</td>
                        <td><span class="status-badge reserved">Reserved</span></td>
                        <td><span class="housekeeping-pill">Pending Touch-up</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    <tr data-room-row
                        data-room-number="510"
                        data-room-type="Standard"
                        data-floor="5"
                        data-capacity="2"
                        data-status="dirty"
                        data-status-label="Dirty"
                        data-housekeeping-status="Needs Attention"
                        data-housekeeper="Liza Tan"
                        data-guest="—"
                        data-checkin="—"
                        data-checkout="—"
                        data-notes="Needs full linen replacement and restroom sanitation."
                        data-room-label="510">
                        <td>510</td>
                        <td>Standard</td>
                        <td>Floor 5</td>
                        <td><span class="status-badge dirty">Dirty</span></td>
                        <td><span class="housekeeping-pill">Needs Attention</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    <tr data-room-row
                        data-room-number="602"
                        data-room-type="Penthouse"
                        data-floor="6"
                        data-capacity="8"
                        data-status="maintenance"
                        data-status-label="Maintenance"
                        data-housekeeping-status="Paused"
                        data-housekeeper="—"
                        data-guest="—"
                        data-checkin="—"
                        data-checkout="—"
                        data-notes="Air-conditioning unit is being serviced."
                        data-room-label="602">
                        <td>602</td>
                        <td>Penthouse</td>
                        <td>Floor 6</td>
                        <td><span class="status-badge maintenance">Maintenance</span></td>
                        <td><span class="housekeeping-pill">Paused</span></td>
                        <td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="empty-state" class="summary-pill" style="display:none; margin-top:1rem;">
            <strong>No rooms match the current filters.</strong>
            <span>Try changing the search terms or the dropdown values.</span>
        </div>

        <div class="insight-panel">
            <div class="insight-header">
                <div>
                    <h4>Operational Snapshot</h4>
                    <p>Daily housekeeping and occupancy focus areas</p>
                </div>
            </div>
            <div class="insight-grid">
                <div class="insight-card">
                    <strong>37%</strong>
                    <span>Occupancy rate</span>
                </div>
                <div class="insight-card">
                    <strong>12</strong>
                    <span>Ready for check-in</span>
                </div>
                <div class="insight-card">
                    <strong>4</strong>
                    <span>Need cleaning</span>
                </div>
                <div class="insight-card">
                    <strong>2</strong>
                    <span>Under maintenance</span>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal-overlay" id="details-modal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-head">
            <h4 class="modal-title" id="modal-title">Room Details</h4>
            <button class="close-btn" id="close-modal" type="button" aria-label="Close dialog">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-grid">
                <div class="modal-item"><strong>Room Number</strong><span id="detail-room-number">—</span></div>
                <div class="modal-item"><strong>Room Type</strong><span id="detail-room-type">—</span></div>
                <div class="modal-item"><strong>Floor</strong><span id="detail-floor">—</span></div>
                <div class="modal-item"><strong>Capacity</strong><span id="detail-capacity">—</span></div>
                <div class="modal-item"><strong>Current Status</strong><span id="detail-status">—</span></div>
                <div class="modal-item"><strong>Housekeeping Status</strong><span id="detail-housekeeping-status">—</span></div>
                <div class="modal-item"><strong>Current Guest</strong><span id="detail-guest">—</span></div>
                <div class="modal-item"><strong>Check-in Date</strong><span id="detail-checkin">—</span></div>
                <div class="modal-item"><strong>Check-out Date</strong><span id="detail-checkout">—</span></div>
                <div class="modal-item"><strong>Assigned Housekeeper</strong><span id="detail-housekeeper">—</span></div>
                <div class="modal-item modal-notes"><strong>Notes</strong><span id="detail-notes">—</span></div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="primary-btn" id="modal-change-status" type="button">Change Status</button>
            <button class="secondary-btn" id="modal-assign-housekeeper" type="button">Assign Housekeeper</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/employee-room-status.js') }}"></script>
@endsection
