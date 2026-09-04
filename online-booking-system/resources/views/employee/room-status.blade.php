@extends('employee.layout')

@section('pageTitle', 'Room Status')

@section('content')
@php
    $reservations = $reservations ?? collect([]);
    $rooms = $rooms ?? collect([]);
    $inventoryItems = $inventoryItems ?? collect([]);
    $amenities = $amenities ?? collect([]);
    $eventPlaces = $eventPlaces ?? collect([]);
    $diningTables = $diningTables ?? collect([]);
    $dining = $dining ?? collect([]);
    $diningSchedules = $diningSchedules ?? collect([]);
    $roomReservations = $reservations;
    $amenityReservations = $reservations->where('category', 'amenities');
    $eventPlaceReservations = $reservations->where('category', 'event_place');
    $diningReservations = $reservations->where('category', 'dining');

    $stats = [
        'rooms' => [
            'total' => $rooms->count(),
            'available' => $rooms->where('status', 'available')->count(),
            'reserved' => $rooms->where('status', 'reserved')->count(),
            'occupied' => $rooms->where('status', 'occupied')->count(),
            'dirty' => $rooms->where('cleaning_status', 'dirty')->count(),
            'cleaning' => $rooms->where('cleaning_status', 'in_progress')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ],
        'amenities' => [
            'total' => $amenities->count(),
            'available' => $amenities->where('status', 'available')->count(),
            'reserved' => $amenities->where('status', 'reserved')->count(),
            'in_use' => $amenities->where('status', 'occupied')->count(),
            'unavailable' => $amenities->whereIn('status', ['unavailable', 'maintenance'])->count(),
        ],
        'event_place' => [
            'total' => $eventPlaces->count(),
            'available' => $eventPlaces->where('status', 'available')->count(),
            'reserved' => $eventPlaces->where('status', 'reserved')->count(),
            'ongoing' => $eventPlaces->where('status', 'occupied')->count(),
            'unavailable' => $eventPlaces->whereIn('status', ['unavailable', 'maintenance'])->count(),
        ],
        'dining' => [
            'total' => $diningTables->count(),
            'available' => $diningTables->filter(fn ($table) => strtolower($table->status) === 'available')->count(),
            'reserved' => $diningTables->filter(fn ($table) => strtolower($table->status) === 'reserved')->count(),
            'occupied' => $diningTables->filter(fn ($table) => strtolower($table->status) === 'occupied')->count(),
            'unavailable' => $diningTables->filter(fn ($table) => strtolower($table->status) === 'unavailable')->count(),
        ],
    ];
@endphp
<link rel="stylesheet" href="{{ asset('css/employee-room-status.css') }}">
<style>
    #diningTab:not(.hidden) {
        display: flex;
    }

    #diningTab.hidden {
        display: none;
    }

    #diningTab > .order-1 {
        order: 1;
    }

    #diningTab > #dining-filter-form {
        order: 3;
    }

    #diningTab > .order-2 {
        order: 2;
    }

    #diningTab > .order-3 {
        order: 3;
    }

    #diningTab > .dining-live-grid {
        display: block;
        order: 4;
    }

    #diningTab > .dining-live-grid > [data-dining-content] {
        width: 100%;
    }

    #diningTab > .dining-live-grid .table-scroll,
    #diningTab > .dining-live-grid .room-table {
        width: 100%;
    }
</style>

<div class="room-status-page">
    <section class="room-status-shell" aria-label="Room status overview">
        <div class="page-header">
            <div>
                <h3 class="page-title">Room Overview</h3>
                <p class="page-subtitle">View room availability and housekeeping status</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm pb-2">
            <div class="flex flex-wrap gap-2">
                <button type="button" data-reservation-tab="rooms" class="reservation-tab inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition">ROOMS</button>
                <button type="button" data-reservation-tab="amenities" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">AMENITIES</button>
                <button type="button" data-reservation-tab="event_place" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">EVENT PLACE</button>
                <button type="button" data-reservation-tab="dining" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">DINING</button>
            </div>
        </div>

        <div id="roomsTab" data-reservation-panel="rooms" class="space-y-4 pt-4 pb-3">
            <div class="summary-grid">
                <article class="summary-card" data-tone="available">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Available Rooms</span>
                        <span class="summary-dot" style="background:#16a34a"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['available'] }}</div>
                </article>
                <article class="summary-card" data-tone="reserved">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Reserved Rooms</span>
                        <span class="summary-dot" style="background:#2563eb"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['reserved'] }}</div>
                </article>
                <article class="summary-card" data-tone="occupied">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Occupied Rooms</span>
                        <span class="summary-dot" style="background:#dc2626"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['occupied'] }}</div>
                </article>
                <article class="summary-card" data-tone="dirty">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Dirty Rooms</span>
                        <span class="summary-dot" style="background:#6b7280"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['dirty'] }}</div>
                </article>
                <article class="summary-card" data-tone="cleaning">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Cleaning Rooms</span>
                        <span class="summary-dot" style="background:#7c3aed"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['cleaning'] }}</div>
                </article>
                <article class="summary-card" data-tone="maintenance">
                    <div class="summary-card-head">
                        <span class="summary-label"><span class="summary-icon"></span>Maintenance Rooms</span>
                        <span class="summary-dot" style="background:#d97706"></span>
                    </div>
                    <div class="summary-value">{{ $stats['rooms']['maintenance'] }}</div>
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
        </div>
            
        <div id="amenitiesTab" data-reservation-panel="amenities" class="hidden space-y-4 pt-4 pb-3">
            <div class="grid gap-4 md:grid-cols-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Amenities</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['amenities']['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Available</p>
                    <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['amenities']['available'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Reserved</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['amenities']['reserved'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">In Use</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['amenities']['in_use'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Unavailable</p>
                    <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['amenities']['unavailable'] }}</p>
                </div>
                
            </div>
            <form class="filter-panel" id="amenities-filter-form">
                <div class="field-group"><label class="field-label" for="amenity-search-name">Search Amenity</label><input class="field-input" id="amenity-search-name" type="text" placeholder="e.g. Swimming Pool"></div>
                <div class="field-group"><label class="field-label" for="amenity-search-type">Search Amenity Type</label><input class="field-input" id="amenity-search-type" type="text" placeholder="e.g. Recreation"></div>
                <div class="field-group"><label class="field-label" for="amenity-filter-status">Filter by Status</label><select class="field-select" id="amenity-filter-status"><option value="">All Status</option><option value="available">Available</option><option value="reserved">Reserved</option><option value="occupied">In Use</option><option value="cleaning">Cleaning</option></select></div>
                <div class="field-group"><label class="field-label" for="amenity-filter-location">Filter by Location</label><select class="field-select" id="amenity-filter-location"><option value="">All Locations</option><option>Ground Floor</option><option>2nd Floor</option><option>3rd Floor</option></select></div>
                <button class="secondary-btn" type="button">Reset Filters</button>
            </form>
            <div class="table-card">
                <div class="table-scroll-hint">Tap the Details button for complete amenity information</div>
                <div class="table-scroll">
                    <table class="room-table">
                        <thead><tr><th>Amenity</th><th>Type</th><th>Location</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($amenities as $item)
                                <tr data-amenity-row data-amenity-name="{{ $item->name }}" data-amenity-type="Amenity" data-amenity-location="—" data-amenity-capacity="{{ $item->capacity ?: '—' }}" data-amenity-status="{{ $item->status }}" data-amenity-hours="—" data-amenity-description="{{ $item->description ?: '—' }}" data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-start-time="—" data-end-time="—" data-guests="—" data-last-cleaned="—" data-maintenance-status="{{ ucfirst($item->status) }}" data-notes="—"><td>{{ $item->name }}</td><td>Amenity</td><td>—</td><td>{{ $item->capacity ?: '—' }}</td><td><span class="status-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span></td><td><button class="action-btn" type="button" data-action="view-amenity">Details</button></td></tr>
                            @endforeach
                            @if($amenities->isEmpty())
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No amenities added by the admin.</td></tr>
                            @endif
                            @if(false)
                            <tr data-amenity-row data-amenity-name="Swimming Pool" data-amenity-type="Recreation" data-amenity-location="Ground Floor" data-amenity-capacity="30" data-amenity-status="available" data-amenity-hours="6:00 AM - 10:00 PM" data-amenity-description="Outdoor swimming pool for hotel guests." data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-start-time="—" data-end-time="—" data-guests="—" data-last-cleaned="Today, 6:00 AM" data-maintenance-status="Operational" data-notes="—"><td>Swimming Pool</td><td>Recreation</td><td>Ground Floor</td><td>30</td><td><span class="status-badge available">Available</span></td><td><button class="action-btn" type="button" data-action="view-amenity">Details</button></td></tr>
                            <tr data-amenity-row data-amenity-name="Gym" data-amenity-type="Fitness" data-amenity-location="2nd Floor" data-amenity-capacity="15" data-amenity-status="occupied" data-amenity-hours="5:00 AM - 11:00 PM" data-amenity-description="Fitness center with cardio and strength equipment." data-reservation-status="In Use" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-start-time="—" data-end-time="—" data-guests="—" data-last-cleaned="Today, 7:00 AM" data-maintenance-status="Operational" data-notes="—"><td>Gym</td><td>Fitness</td><td>2nd Floor</td><td>15</td><td><span class="status-badge occupied">In Use</span></td><td><button class="action-btn" type="button" data-action="view-amenity">Details</button></td></tr>
                            <tr data-amenity-row data-amenity-name="Spa" data-amenity-type="Wellness" data-amenity-location="2nd Floor" data-amenity-capacity="8" data-amenity-status="reserved" data-amenity-hours="9:00 AM - 9:00 PM" data-amenity-description="Relaxation and wellness treatment area." data-reservation-status="Reserved" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-start-time="—" data-end-time="—" data-guests="—" data-last-cleaned="Yesterday, 8:00 PM" data-maintenance-status="Operational" data-notes="—"><td>Spa</td><td>Wellness</td><td>2nd Floor</td><td>8</td><td><span class="status-badge reserved">Reserved</span></td><td><button class="action-btn" type="button" data-action="view-amenity">Details</button></td></tr>
                            <tr data-amenity-row data-amenity-name="Jacuzzi" data-amenity-type="Recreation" data-amenity-location="3rd Floor" data-amenity-capacity="6" data-amenity-status="cleaning" data-amenity-hours="8:00 AM - 10:00 PM" data-amenity-description="Private jacuzzi area." data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-start-time="—" data-end-time="—" data-guests="—" data-last-cleaned="In progress" data-maintenance-status="Cleaning" data-notes="Temporarily unavailable for cleaning."><td>Jacuzzi</td><td>Recreation</td><td>3rd Floor</td><td>6</td><td><span class="status-badge cleaning">Cleaning</span></td><td><button class="action-btn" type="button" data-action="view-amenity">Details</button></td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="eventPlaceTab" data-reservation-panel="event_place" class="hidden space-y-4 pt-4 pb-3">
            <div class="grid gap-4 md:grid-cols-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Venues</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['event_place']['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Available</p>
                    <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['event_place']['available'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Reserved</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['event_place']['reserved'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Ongoing</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['event_place']['ongoing'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Unavailable</p>
                    <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['event_place']['unavailable'] }}</p>
                </div>
            </div>
            <form class="filter-panel" id="event-place-filter-form">
                <div class="field-group"><label class="field-label" for="event-place-search-name">Search Event Place</label><input class="field-input" id="event-place-search-name" type="text" placeholder="e.g. Grand Ballroom"></div>
                <div class="field-group"><label class="field-label" for="event-place-search-type">Search Venue Type</label><input class="field-input" id="event-place-search-type" type="text" placeholder="e.g. Ballroom"></div>
                <div class="field-group"><label class="field-label" for="event-place-filter-status">Filter by Status</label><select class="field-select" id="event-place-filter-status"><option value="">All Status</option><option value="available">Available</option><option value="reserved">Reserved</option><option value="occupied">In Use</option></select></div>
                <div class="field-group"><label class="field-label" for="event-place-filter-location">Filter by Location</label><select class="field-select" id="event-place-filter-location"><option value="">All Locations</option><option>Ground Floor</option><option>Garden</option><option>2nd Floor</option></select></div>
                <button class="secondary-btn" type="button">Reset Filters</button>
            </form>
            <div class="table-card">
                <div class="table-scroll-hint">Tap the Details button for complete event place information</div>
                <div class="table-scroll"><table class="room-table"><thead><tr><th>Event Place</th><th>Type</th><th>Location</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                    @foreach($eventPlaces as $item)
                        <tr data-event-row data-event-name="{{ $item->name }}" data-event-type="{{ $item->event_type ?: 'Event Place' }}" data-event-location="{{ $item->location ?: '—' }}" data-event-capacity="{{ $item->capacity ?: '—' }}" data-event-status="{{ $item->status }}" data-event-size="—" data-event-description="{{ $item->description ?: '—' }}" data-reservation-status="—" data-guest="—" data-reservation-id="—" data-event-date="—" data-start-time="—" data-end-time="—" data-expected-guests="—" data-setup-status="—" data-cleaning-status="—" data-maintenance-status="{{ ucfirst($item->status) }}" data-notes="—"><td>{{ $item->name }}</td><td>{{ $item->event_type ?: 'Event Place' }}</td><td>{{ $item->location ?: '—' }}</td><td>{{ $item->capacity ?: '—' }}</td><td><span class="status-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span></td><td><button class="action-btn" type="button" data-action="view-event">Details</button></td></tr>
                    @endforeach
                    @if($eventPlaces->isEmpty())<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No event places added by the admin.</td></tr>@endif
                    @if(false)
                    <tr data-event-row data-event-name="Grand Ballroom" data-event-type="Ballroom" data-event-location="Ground Floor" data-event-capacity="300" data-event-status="available" data-event-size="500 sq m" data-event-description="Large formal event venue." data-reservation-status="—" data-guest="—" data-reservation-id="—" data-event-date="—" data-start-time="—" data-end-time="—" data-expected-guests="—" data-setup-status="Ready" data-cleaning-status="Complete" data-maintenance-status="Operational" data-notes="—"><td>Grand Ballroom</td><td>Ballroom</td><td>Ground Floor</td><td>300</td><td><span class="status-badge available">Available</span></td><td><button class="action-btn" type="button" data-action="view-event">Details</button></td></tr>
                    <tr data-event-row data-event-name="Garden Pavilion" data-event-type="Outdoor" data-event-location="Garden" data-event-capacity="150" data-event-status="reserved" data-event-size="350 sq m" data-event-description="Open-air venue surrounded by gardens." data-reservation-status="Reserved" data-guest="—" data-reservation-id="—" data-event-date="—" data-start-time="—" data-end-time="—" data-expected-guests="—" data-setup-status="Scheduled" data-cleaning-status="Complete" data-maintenance-status="Operational" data-notes="—"><td>Garden Pavilion</td><td>Outdoor</td><td>Garden</td><td>150</td><td><span class="status-badge reserved">Reserved</span></td><td><button class="action-btn" type="button" data-action="view-event">Details</button></td></tr>
                    <tr data-event-row data-event-name="Conference Hall" data-event-type="Meeting" data-event-location="2nd Floor" data-event-capacity="80" data-event-status="occupied" data-event-size="120 sq m" data-event-description="Meeting and conference venue." data-reservation-status="Ongoing" data-guest="—" data-reservation-id="—" data-event-date="—" data-start-time="—" data-end-time="—" data-expected-guests="—" data-setup-status="In Progress" data-cleaning-status="Pending" data-maintenance-status="Operational" data-notes="—"><td>Conference Hall</td><td>Meeting</td><td>2nd Floor</td><td>80</td><td><span class="status-badge occupied">In Use</span></td><td><button class="action-btn" type="button" data-action="view-event">Details</button></td></tr>
                    <tr data-event-row data-event-name="Function Room A" data-event-type="Function Room" data-event-location="2nd Floor" data-event-capacity="50" data-event-status="available" data-event-size="75 sq m" data-event-description="Flexible function room for private events." data-reservation-status="—" data-guest="—" data-reservation-id="—" data-event-date="—" data-start-time="—" data-end-time="—" data-expected-guests="—" data-setup-status="Ready" data-cleaning-status="Complete" data-maintenance-status="Operational" data-notes="—"><td>Function Room A</td><td>Function Room</td><td>2nd Floor</td><td>50</td><td><span class="status-badge available">Available</span></td><td><button class="action-btn" type="button" data-action="view-event">Details</button></td></tr>
                    @endif
                </tbody></table></div>
            </div>
        </div>

        <div id="diningTab" data-reservation-panel="dining" class="hidden flex-col space-y-4 pt-4 pb-3">
            <div class="order-1 grid gap-4 md:grid-cols-5">
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Tables</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['dining']['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Available</p>
                    <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['dining']['available'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Reserved</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['dining']['reserved'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Occupied</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['dining']['occupied'] }}</p>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Unavailable</p>
                    <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['dining']['unavailable'] }}</p>
                </div>
            </div>
            <div class="dining-live-grid order-4 grid gap-4 xl:grid-cols-3">
                <div class="table-card" data-dining-content="tables">
                    <div class="border-b border-gray-200 px-5 py-4"><h3 class="text-lg font-semibold text-gray-800">Tables / Seating</h3><p class="text-sm text-gray-500">Live availability from admin dining setup</p></div>
                    <div class="table-scroll"><table class="room-table"><thead><tr><th>Table</th><th>Type</th><th>Capacity</th><th>Status</th></tr></thead><tbody>
                        @forelse($diningTables as $table)
                            <tr><td>{{ $table->table_no }}</td><td>{{ $table->type }}</td><td>{{ $table->capacity }}</td><td><span class="status-badge {{ strtolower($table->status) }}">{{ ucfirst($table->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">No dining tables found.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div>
                <div class="table-card" data-dining-content="menu">
                    <div class="border-b border-gray-200 px-5 py-4"><h3 class="text-lg font-semibold text-gray-800">Menu / Meals</h3><p class="text-sm text-gray-500">Current menu items and availability</p></div>
                    <div class="table-scroll"><table class="room-table"><thead><tr><th>Meal</th><th>Category</th><th>Price</th><th>Status</th></tr></thead><tbody>
                        @forelse($dining as $menu)
                            <tr><td>{{ $menu->name }}</td><td>{{ $menu->category ?: 'Menu / Meal' }}</td><td>₱{{ number_format((float) $menu->price, 2) }}</td><td><span class="status-badge {{ strtolower($menu->status) }}">{{ ucfirst($menu->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">No menu items found.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div>
                <div class="table-card" data-dining-content="schedule">
                    <div class="border-b border-gray-200 px-5 py-4"><h3 class="text-lg font-semibold text-gray-800">Dining Schedule</h3><p class="text-sm text-gray-500">Service hours configured by admin</p></div>
                    <div class="table-scroll"><table class="room-table"><thead><tr><th>Period</th><th>Hours</th><th>Max Guests</th><th>Status</th></tr></thead><tbody>
                        @forelse($diningSchedules as $schedule)
                            <tr><td>{{ $schedule->period }}</td><td>{{ \Illuminate\Support\Carbon::parse($schedule->available_from)->format('g:i A') }} - {{ \Illuminate\Support\Carbon::parse($schedule->available_to)->format('g:i A') }}</td><td>{{ $schedule->max_guests ?: '—' }}</td><td><span class="status-badge {{ strtolower($schedule->status) }}">{{ ucfirst($schedule->status) }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">No dining schedules found.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div>
            </div>
            <form class="order-3 filter-panel" id="dining-filter-form">
                <div class="field-group"><label class="field-label" for="dining-search-name">Search Table / Area</label><input class="field-input" id="dining-search-name" type="text" placeholder="e.g. Table 01"></div>
                <div class="field-group"><label class="field-label" for="dining-search-type">Search Dining Type</label><input class="field-input" id="dining-search-type" type="text" placeholder="e.g. Standard"></div>
                <div class="field-group"><label class="field-label" for="dining-filter-status">Filter by Status</label><select class="field-select" id="dining-filter-status"><option value="">All Status</option><option value="available">Available</option><option value="reserved">Reserved</option><option value="occupied">Occupied</option></select></div>
                <div class="field-group"><label class="field-label" for="dining-filter-location">Filter by Location</label><select class="field-select" id="dining-filter-location"><option value="">All Locations</option><option>Main Dining</option><option>Private Area</option></select></div>
                <button class="secondary-btn" type="button">Reset Filters</button>
            </form>
            <div class="order-2 flex flex-wrap gap-2 border-b border-gray-200 pb-3" role="tablist" aria-label="Dining sections">
                <button type="button" data-dining-tab="tables" class="dining-tab rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white">Tables / Seating</button>
                <button type="button" data-dining-tab="menu" class="dining-tab rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700">Menu / Meals</button>
                <button type="button" data-dining-tab="schedule" class="dining-tab rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700">Dining Schedule</button>
            </div>
            <div class="hidden table-card">
                <div class="table-scroll-hint">Tap the Details button for complete dining information</div>
                <div class="table-scroll"><table class="room-table"><thead><tr><th>Table / Area</th><th>Dining Type</th><th>Location</th><th>Capacity</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                    @foreach($inventoryItems->where('category', 'dining') as $item)
                        <tr data-dining-row data-dining-name="{{ $item->name }}" data-dining-type="{{ $item->type ?: 'Dining' }}" data-dining-location="{{ $item->location ?: '—' }}" data-dining-capacity="{{ $item->capacity ?: '—' }}" data-dining-status="{{ $item->status }}" data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-reservation-time="—" data-guests="—" data-order="—" data-seating-status="{{ ucfirst($item->status) }}" data-special-requests="—" data-notes="{{ $item->description ?: '—' }}"><td>{{ $item->name }}</td><td>{{ $item->type ?: 'Dining' }}</td><td>{{ $item->location ?: '—' }}</td><td>{{ $item->capacity ?: '—' }}</td><td><span class="status-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span></td><td><button class="action-btn" type="button" data-action="view-dining">Details</button></td></tr>
                    @endforeach
                    @if($inventoryItems->where('category', 'dining')->isEmpty())<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No dining items added by the admin.</td></tr>@endif
                    @if(false)
                    <tr data-dining-row data-dining-name="Table 01" data-dining-type="Standard" data-dining-location="Main Dining" data-dining-capacity="4" data-dining-status="available" data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-reservation-time="—" data-guests="—" data-order="—" data-seating-status="Ready" data-special-requests="—" data-notes="—"><td>Table 01</td><td>Standard</td><td>Main Dining</td><td>4</td><td><span class="status-badge available">Available</span></td><td><button class="action-btn" type="button" data-action="view-dining">Details</button></td></tr>
                    <tr data-dining-row data-dining-name="Table 02" data-dining-type="Standard" data-dining-location="Main Dining" data-dining-capacity="4" data-dining-status="reserved" data-reservation-status="Reserved" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-reservation-time="—" data-guests="—" data-order="—" data-seating-status="Reserved" data-special-requests="—" data-notes="—"><td>Table 02</td><td>Standard</td><td>Main Dining</td><td>4</td><td><span class="status-badge reserved">Reserved</span></td><td><button class="action-btn" type="button" data-action="view-dining">Details</button></td></tr>
                    <tr data-dining-row data-dining-name="Table 03" data-dining-type="Family" data-dining-location="Main Dining" data-dining-capacity="6" data-dining-status="occupied" data-reservation-status="Occupied" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-reservation-time="—" data-guests="—" data-order="—" data-seating-status="Occupied" data-special-requests="—" data-notes="—"><td>Table 03</td><td>Family</td><td>Main Dining</td><td>6</td><td><span class="status-badge occupied">Occupied</span></td><td><button class="action-btn" type="button" data-action="view-dining">Details</button></td></tr>
                    <tr data-dining-row data-dining-name="Table 04" data-dining-type="VIP" data-dining-location="Private Area" data-dining-capacity="8" data-dining-status="available" data-reservation-status="—" data-guest="—" data-reservation-id="—" data-reservation-date="—" data-reservation-time="—" data-guests="—" data-order="—" data-seating-status="Ready" data-special-requests="—" data-notes="—"><td>Table 04</td><td>VIP</td><td>Private Area</td><td>8</td><td><span class="status-badge available">Available</span></td><td><button class="action-btn" type="button" data-action="view-dining">Details</button></td></tr>
                    @endif
                </tbody></table></div>
            </div>
        </div>

        <div class="table-card" data-reservation-panel="rooms">
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
                    @foreach($rooms as $room)
                    @php($roomStatus = $room->status === 'maintenance' ? 'maintenance' : ($room->cleaning_status === 'in_progress' ? 'cleaning' : ($room->cleaning_status === 'dirty' ? 'dirty' : $room->status)))
                    <tr data-room-row data-room-number="{{ $room->room_number }}" data-room-type="{{ $room->room_type }}" data-floor="{{ $room->floor }}" data-capacity="{{ $room->capacity }}" data-status="{{ $roomStatus }}" data-status-label="{{ ucfirst($roomStatus) }}" data-housekeeping-status="{{ ucfirst(str_replace('_', ' ', $room->cleaning_status ?: 'clean')) }}" data-housekeeper="—" data-guest="—" data-checkin="—" data-checkout="—" data-notes="{{ $room->description ?: '—' }}" data-room-label="{{ $room->room_number }}">
                        <td>{{ $room->room_number }}</td><td>{{ $room->room_type }}</td><td>Floor {{ $room->floor }}</td><td><span class="status-badge {{ $roomStatus }}">{{ ucfirst($roomStatus) }}</span></td><td><span class="housekeeping-pill">{{ ucfirst(str_replace('_', ' ', $room->cleaning_status ?: 'clean')) }}</span></td><td><button class="action-btn" type="button" data-action="view">Details</button></td>
                    </tr>
                    @endforeach
                    @if($rooms->isEmpty())<tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No rooms have been added by the admin.</td></tr>@endif
                    @if(false)
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
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div id="empty-state" class="summary-pill" data-reservation-panel="rooms" style="display:none; margin-top:1rem;">
            <strong>No rooms match the current filters.</strong>
            <span>Try changing the search terms or the dropdown values.</span>
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

<div class="modal-overlay" id="amenity-details-modal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="amenity-modal-title">
        <div class="modal-head"><h4 class="modal-title" id="amenity-modal-title">Amenity Details</h4><button class="close-btn" type="button" data-close-modal="amenity-details-modal" aria-label="Close dialog">×</button></div>
        <div class="modal-body"><div class="modal-section-title">Amenity Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Amenity Name</strong><span id="amenity-detail-name">—</span></div><div class="modal-item"><strong>Amenity Type</strong><span id="amenity-detail-type">—</span></div><div class="modal-item"><strong>Location</strong><span id="amenity-detail-location">—</span></div><div class="modal-item"><strong>Capacity</strong><span id="amenity-detail-capacity">—</span></div><div class="modal-item"><strong>Current Status</strong><span id="amenity-detail-status">—</span></div><div class="modal-item"><strong>Operating Hours</strong><span id="amenity-detail-hours">—</span></div><div class="modal-item modal-notes"><strong>Description</strong><span id="amenity-detail-description">—</span></div>
        </div><div class="modal-section-title">Management Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Last Cleaned</strong><span id="amenity-detail-last-cleaned">—</span></div><div class="modal-item"><strong>Maintenance Status</strong><span id="amenity-detail-maintenance">—</span></div><div class="modal-item modal-notes"><strong>Notes</strong><span id="amenity-detail-notes">—</span></div>
        </div></div><div class="modal-actions"><button class="secondary-btn" type="button" data-close-modal="amenity-details-modal">Close</button></div>
    </div>
</div>

<div class="modal-overlay" id="event-details-modal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="event-modal-title">
        <div class="modal-head"><h4 class="modal-title" id="event-modal-title">Event Place Details</h4><button class="close-btn" type="button" data-close-modal="event-details-modal" aria-label="Close dialog">×</button></div>
        <div class="modal-body"><div class="modal-section-title">Venue Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Event Place Name</strong><span id="event-detail-name">—</span></div><div class="modal-item"><strong>Event Place Type</strong><span id="event-detail-type">—</span></div><div class="modal-item"><strong>Location</strong><span id="event-detail-location">—</span></div><div class="modal-item"><strong>Capacity</strong><span id="event-detail-capacity">—</span></div><div class="modal-item"><strong>Size</strong><span id="event-detail-size">—</span></div><div class="modal-item"><strong>Current Status</strong><span id="event-detail-status">—</span></div><div class="modal-item modal-notes"><strong>Description</strong><span id="event-detail-description">—</span></div>
        </div><div class="modal-section-title">Management Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Setup Status</strong><span id="event-detail-setup">—</span></div><div class="modal-item"><strong>Cleaning Status</strong><span id="event-detail-cleaning">—</span></div><div class="modal-item"><strong>Maintenance Status</strong><span id="event-detail-maintenance">—</span></div><div class="modal-item modal-notes"><strong>Notes</strong><span id="event-detail-notes">—</span></div>
        </div></div><div class="modal-actions"><button class="secondary-btn" type="button" data-close-modal="event-details-modal">Close</button></div>
    </div>
</div>

<div class="modal-overlay" id="dining-details-modal" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="dining-modal-title">
        <div class="modal-head"><h4 class="modal-title" id="dining-modal-title">Dining Details</h4><button class="close-btn" type="button" data-close-modal="dining-details-modal" aria-label="Close dialog">×</button></div>
        <div class="modal-body"><div class="modal-section-title">Dining Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Table/Area Number</strong><span id="dining-detail-name">—</span></div><div class="modal-item"><strong>Dining Type</strong><span id="dining-detail-type">—</span></div><div class="modal-item"><strong>Location</strong><span id="dining-detail-location">—</span></div><div class="modal-item"><strong>Capacity</strong><span id="dining-detail-capacity">—</span></div><div class="modal-item"><strong>Current Status</strong><span id="dining-detail-status">—</span></div>
        </div><div class="modal-section-title">Reservation Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Reservation Status</strong><span id="dining-detail-reservation-status">—</span></div><div class="modal-item"><strong>Guest Name</strong><span id="dining-detail-guest">—</span></div><div class="modal-item"><strong>Reservation ID</strong><span id="dining-detail-reservation-id">—</span></div><div class="modal-item"><strong>Reservation Date</strong><span id="dining-detail-date">—</span></div><div class="modal-item"><strong>Reservation Time</strong><span id="dining-detail-time">—</span></div><div class="modal-item"><strong>Number of Guests</strong><span id="dining-detail-guests">—</span></div>
        </div><div class="modal-section-title">Dining Information</div><div class="modal-grid">
            <div class="modal-item"><strong>Current Order</strong><span id="dining-detail-order">—</span></div><div class="modal-item"><strong>Seating Status</strong><span id="dining-detail-seating">—</span></div><div class="modal-item"><strong>Special Requests</strong><span id="dining-detail-requests">—</span></div><div class="modal-item modal-notes"><strong>Notes</strong><span id="dining-detail-notes">—</span></div>
        </div></div><div class="modal-actions"><button class="secondary-btn" type="button" data-close-modal="dining-details-modal">Close</button></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setActiveReservationTab(tabKey) {
            const buttons = document.querySelectorAll('[data-reservation-tab]');
            const panels = document.querySelectorAll('[data-reservation-panel]');

            buttons.forEach((button) => {
                const isActive = button.dataset.reservationTab === tabKey;
                button.classList.toggle('bg-orange-500', isActive);
                button.classList.toggle('border-orange-500', isActive);
                button.classList.toggle('text-white', isActive);
                button.classList.toggle('bg-white', !isActive);
                button.classList.toggle('border-gray-200', !isActive);
                button.classList.toggle('text-gray-700', !isActive);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.reservationPanel !== tabKey);
            });

        }

        const tabButtons = document.querySelectorAll('[data-reservation-tab]');
        tabButtons.forEach((button) => {
            button.addEventListener('click', () => setActiveReservationTab(button.dataset.reservationTab));
        });

        function setActiveDiningTab(tabKey) {
            document.querySelectorAll('[data-dining-tab]').forEach((button) => {
                const isActive = button.dataset.diningTab === tabKey;
                button.classList.toggle('bg-orange-500', isActive);
                button.classList.toggle('text-white', isActive);
                button.classList.toggle('bg-white', !isActive);
                button.classList.toggle('text-gray-700', !isActive);
            });

            document.querySelectorAll('[data-dining-content]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.diningContent !== tabKey);
            });
        }

        document.querySelectorAll('[data-dining-tab]').forEach((button) => {
            button.addEventListener('click', () => setActiveDiningTab(button.dataset.diningTab));
        });

        setActiveReservationTab('rooms');
        setActiveDiningTab('tables');
    });
</script>

<script src="{{ asset('js/employee-room-status.js') }}"></script>
@endsection
