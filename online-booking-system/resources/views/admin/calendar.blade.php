@extends('admin.layout')

@section('content')
<style>
    .calendar-shell {
        background: #f4f5f7;
        border-radius: 0.9rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
    }

    .calendar-toolbar {
        background: linear-gradient(90deg, #d86b35 0%, #c95d2c 100%);
        border-bottom: 1px solid rgba(255,255,255,0.25);
        color: white;
    }

    .calendar-toolbar .toolbar-btn {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.22);
        color: white;
        border-radius: 0.7rem;
        transition: all 0.2s ease;
    }

    .calendar-toolbar .toolbar-btn:hover {
        background: rgba(255,255,255,0.18);
    }

    .calendar-toolbar .toolbar-select {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        border-radius: 0.7rem;
        position: relative;
    }

    .calendar-toolbar .dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        min-width: 12rem;
        display: none;
        background: white;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        overflow: hidden;
        z-index: 30;
    }

    .calendar-toolbar .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        width: 100%;
        background: white;
        border: 0;
        padding: 0.7rem 0.9rem;
        text-align: left;
        font-size: 0.85rem;
        color: #334155;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .dropdown-item:hover,
    .dropdown-item.active {
        background: #fef2f2;
        color: #b91c1c;
    }

    .calendar-room-row.hidden-room,
    .calendar-timeline-row.hidden-room {
        display: none !important;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: 220px minmax(720px, 1fr);
        background: #f8fafc;
        min-height: 480px;
    }

    .calendar-room-column {
        background: #f8fafc;
        border-right: 1px solid #e5e7eb;
    }

    .calendar-room-header {
        height: 58px;
        background: rgba(255,255,255,0.6);
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        padding: 0 1rem;
        font-weight: 600;
        color: #334155;
    }

    .calendar-room-list {
        background: #f8fafc;
    }

    .calendar-room-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
        padding: 0 1rem;
        border-bottom: 1px solid #e5e7eb;
        background: rgba(255,255,255,0.2);
        color: #1f2937;
    }

    .calendar-room-row span {
        font-size: 0.9rem;
    }

    .calendar-room-row small {
        color: #64748b;
    }

    .calendar-body {
        overflow-x: auto;
    }

    .calendar-days {
        display: grid;
        grid-template-columns: repeat({{ count($days) }}, minmax(72px, 1fr));
        min-width: 720px;
    }

    .calendar-day-header {
        height: 58px;
        border-bottom: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        background: rgba(255,255,255,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .calendar-day-header strong {
        font-size: 0.88rem;
    }

    .calendar-day-header.today {
        background: rgba(255, 107, 53, 0.08);
        color: #c2410c;
    }

    .calendar-timeline-row {
        position: relative;
        display: grid;
        grid-template-columns: repeat({{ count($days) }}, minmax(72px, 1fr));
        min-height: 60px;
        border-bottom: 1px solid #e5e7eb;
        background: rgba(255,255,255,0.1);
    }

    .calendar-cell {
        border-right: 1px solid #e5e7eb;
        position: relative;
        background: rgba(255,255,255,0.2);
    }

    .calendar-reservation {
        position: absolute;
        top: 10px;
        height: 34px;
        border-radius: 0.7rem;
        padding: 0.45rem 0.55rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        z-index: 1;
        cursor: pointer;
    }

    .calendar-detail-popover {
        position: fixed;
        display: none;
        min-width: 220px;
        max-width: 280px;
        padding: 0.85rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
        color: #334155;
        z-index: 60;
    }

    .calendar-detail-popover.show { display: block; }
    .calendar-legend {
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        padding: 0.8rem 1rem;
        color: #475569;
        font-size: 0.8rem;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .legend-dot {
        width: 0.7rem;
        height: 0.7rem;
        border-radius: 50%;
        display: inline-block;
    }

    @media (max-width: 1024px) {
        .calendar-grid {
            grid-template-columns: 180px minmax(620px, 1fr);
        }
    }
</style>

<div class="calendar-shell animate-fade-in">
    <div class="calendar-toolbar px-4 py-3 sm:px-5">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.calendar', ['date' => $calendarStart->copy()->subWeek()->format('Y-m-d')]) }}" class="toolbar-btn h-10 w-10 flex items-center justify-center">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('admin.calendar', ['date' => $calendarStart->copy()->addWeek()->format('Y-m-d')]) }}" class="toolbar-btn h-10 w-10 flex items-center justify-center">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('admin.calendar', ['date' => now()->format('Y-m-d')]) }}" class="toolbar-btn px-3 py-2 text-sm font-medium ml-2">Today</a>
                <div class="ml-2 text-sm font-semibold text-white/90">
                    {{ $calendarStart->format('M d, Y') }} - {{ $calendarEnd->format('M d, Y') }}
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                <div class="toolbar-select px-3 py-2 text-sm flex items-center gap-2 cursor-pointer" data-dropdown="roomView">
                    <i class="fas fa-bed"></i>
                    <span class="dropdown-label">Rooms</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item active" data-value="Rooms">Rooms</button>
                        <button type="button" class="dropdown-item" data-value="Facilities">Facilities</button>
                        <button type="button" class="dropdown-item" data-value="Events">Events</button>
                        <button type="button" class="dropdown-item" data-value="Dining">Dining</button>
                    </div>
                </div>

                <div class="toolbar-select px-3 py-2 text-sm flex items-center gap-2 cursor-pointer" data-dropdown="typeFilter">
                    <span class="dropdown-label">All Types</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item active" data-value="All Types">All Types</button>
                        <button type="button" class="dropdown-item" data-value="Deluxe">Deluxe</button>
                        <button type="button" class="dropdown-item" data-value="Standard">Standard</button>
                    </div>
                </div>

                <div class="toolbar-select px-3 py-2 text-sm flex items-center gap-2 cursor-pointer" data-dropdown="viewMode">
                    <span class="dropdown-label">Weekly</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item active" data-value="Weekly">Weekly</button>
                        <button type="button" class="dropdown-item" data-value="Monthly">Monthly</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="calendar-grid">
        <div class="calendar-room-column">
            <div class="calendar-room-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bed text-sm text-slate-500" data-calendar-view-icon></i>
                    <span data-calendar-view-label>Room</span>
                </div>
            </div>
            <div class="calendar-room-list">
                @foreach($rooms as $room)
                    <div class="calendar-room-row" data-view="rooms" data-room-type="{{ $room->room_type }}">
                        <div class="flex flex-col">
                            <span>{{ $room->room_number }}</span>
                        </div>
                        <small>{{ $room->room_type }}</small>
                    </div>
                @endforeach

                @foreach($facilityRows as $facility)
                    <div class="calendar-room-row" data-view="facilities" data-room-type="{{ $facility['type'] }}" style="display:none;">
                        <div class="flex flex-col">
                            <span>{{ $facility['name'] }}</span>
                        </div>
                        <small>{{ $facility['type'] }}</small>
                    </div>
                @endforeach

                @foreach($eventRows as $event)
                    <div class="calendar-room-row" data-view="events" data-room-type="{{ $event['type'] }}" style="display:none;">
                        <div class="flex flex-col">
                            <span>{{ $event['name'] }}</span>
                        </div>
                        <small>{{ $event['type'] }}</small>
                    </div>
                @endforeach

                @foreach($diningRows as $table)
                    <div class="calendar-room-row" data-view="dining" data-room-type="{{ $table['type'] }}" style="display:none;">
                        <div class="flex flex-col">
                            <span>{{ $table['name'] }}</span>
                        </div>
                        <small>{{ $table['type'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="calendar-body">
            <div class="calendar-days">
                @foreach($days as $day)
                    <div class="calendar-day-header {{ $day->isToday() ? 'today' : '' }}">
                        <span>{{ $day->translatedFormat('D') }}</span>
                        <strong>{{ $day->format('d') }}</strong>
                    </div>
                @endforeach
            </div>

            @foreach($rooms as $room)
                <div class="calendar-timeline-row" data-view="rooms" data-room-type="{{ $room->room_type }}" style="min-height: {{ $roomTimelineHeights[$room->id] ?? 60 }}px;">
                    @foreach($days as $day)
                        <div class="calendar-cell"></div>
                    @endforeach

                    @foreach($roomTimeline[$room->id] ?? [] as $segment)
                        <div class="calendar-reservation" data-status="{{ $segment['status'] }}" data-start="{{ $segment['start'] }}" data-span="{{ $segment['span'] }}" data-details="{{ json_encode($segment['details']) }}" title="View booking details" style="top: {{ 10 + (($segment['lane'] ?? 0) * 42) }}px; {{ $segment['style'] }}">
                            {{ $segment['guest'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach

            @foreach($facilityRows as $facility)
                <div class="calendar-timeline-row" data-view="facilities" data-room-type="{{ $facility['type'] }}" style="display:none;">
                    @foreach($days as $day)
                        <div class="calendar-cell"></div>
                    @endforeach

                    @foreach($facilityTimeline[$facility['id']] ?? [] as $segment)
                        <div class="calendar-reservation" data-status="{{ $segment['status'] }}" data-start="{{ $segment['start'] }}" data-span="{{ $segment['span'] }}" data-details="{{ json_encode($segment['details']) }}" title="View booking details" style="{{ $segment['style'] }}">
                            {{ $segment['guest'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach

            @foreach($eventRows as $event)
                <div class="calendar-timeline-row" data-view="events" data-room-type="{{ $event['type'] }}" style="display:none;">
                    @foreach($days as $day)
                        <div class="calendar-cell"></div>
                    @endforeach

                    @foreach($eventTimeline[$event['id']] ?? [] as $segment)
                        <div class="calendar-reservation" data-status="{{ $segment['status'] }}" data-start="{{ $segment['start'] }}" data-span="{{ $segment['span'] }}" data-details="{{ json_encode($segment['details']) }}" title="View booking details" style="{{ $segment['style'] }}">
                            {{ $segment['guest'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach

            @foreach($diningRows as $table)
                <div class="calendar-timeline-row" data-view="dining" data-room-type="{{ $table['type'] }}" style="display:none;">
                    @foreach($days as $day)
                        <div class="calendar-cell"></div>
                    @endforeach

                    @foreach($diningTimeline[$table['id']] ?? [] as $segment)
                        <div class="calendar-reservation" data-status="{{ $segment['status'] }}" data-start="{{ $segment['start'] }}" data-span="{{ $segment['span'] }}" data-details="{{ json_encode($segment['details']) }}" title="View booking details" style="{{ $segment['style'] }}">
                            {{ $segment['guest'] }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="calendar-legend">
        <span class="legend-item"><span class="legend-dot" style="background:#ef4444"></span>Booked</span>
        <span class="legend-item"><span class="legend-dot" style="background:#3b82f6"></span>Confirmed</span>
        <span class="legend-item"><span class="legend-dot" style="background:#10b981"></span>Checked-In</span>
        <span class="legend-item"><span class="legend-dot" style="background:#f59e0b"></span>Pending</span>
    </div>
</div>

<div class="calendar-detail-popover" data-calendar-detail-popover></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdowns = document.querySelectorAll('[data-dropdown]');

        function closeAllMenus() {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
        }

        function applyCalendarFilter() {
            const roomView = document.querySelector('[data-dropdown="roomView"] .dropdown-label')?.textContent.trim() || 'Rooms';
            const typeValue = document.querySelector('[data-dropdown="typeFilter"] .dropdown-label')?.textContent.trim() || 'All Types';
            const rangeValue = document.querySelector('[data-dropdown="viewMode"] .dropdown-label')?.textContent.trim() || 'Weekly';
            const visibleDays = { Weekly: 7, Monthly: 31 };
            const maxVisible = visibleDays[rangeValue] || 7;
            const gridColumns = `repeat(${maxVisible}, minmax(72px, 1fr))`;

            document.querySelectorAll('.calendar-days, .calendar-timeline-row').forEach((grid) => {
                grid.style.gridTemplateColumns = gridColumns;
            });

            document.querySelectorAll('.calendar-day-header').forEach((header, index) => {
                header.style.display = index < maxVisible ? 'flex' : 'none';
            });

            document.querySelectorAll('.calendar-timeline-row').forEach((row) => {
                row.querySelectorAll('.calendar-cell').forEach((cell, index) => {
                    cell.style.display = index < maxVisible ? 'block' : 'none';
                });
            });

            document.querySelectorAll('.calendar-reservation[data-start]').forEach((bar) => {
                const start = Number(bar.dataset.start);
                const span = Number(bar.dataset.span);
                bar.style.left = `${(start / maxVisible) * 100}%`;
                bar.style.width = `${(span / maxVisible) * 100}%`;
            });

            const normalizedView = roomView.toLowerCase();
            const viewKey = ['rooms', 'facilities', 'events', 'dining'].includes(normalizedView) ? normalizedView : 'rooms';
            const viewDetails = {
                rooms: { label: 'Room', icon: 'fa-bed' },
                facilities: { label: 'Facility', icon: 'fa-spa' },
                events: { label: 'Event', icon: 'fa-calendar-check' },
                dining: { label: 'Dining', icon: 'fa-utensils' },
            };
            const selectedView = viewDetails[viewKey];
            const calendarViewLabel = document.querySelector('[data-calendar-view-label]');
            const calendarViewIcon = document.querySelector('[data-calendar-view-icon]');

            if (calendarViewLabel && calendarViewIcon && selectedView) {
                calendarViewLabel.textContent = selectedView.label;
                calendarViewIcon.className = `fas ${selectedView.icon} text-sm text-slate-500`;
            }

            document.querySelectorAll('.calendar-room-row').forEach((row) => {
                const rowView = (row.dataset.view || 'rooms');
                const rowType = (row.dataset.roomType || '').trim();
                const matchesView = viewKey === 'rooms' ? rowView === 'rooms' : rowView === viewKey;
                const matchesType = typeValue === 'All Types' || rowType === '' || rowType === typeValue || rowType.toLowerCase() === typeValue.toLowerCase();
                row.style.display = matchesView && matchesType ? 'flex' : 'none';
            });

            document.querySelectorAll('.calendar-timeline-row').forEach((row) => {
                const rowView = (row.dataset.view || 'rooms');
                const rowType = (row.dataset.roomType || '').trim();
                const matchesView = viewKey === 'rooms' ? rowView === 'rooms' : rowView === viewKey;
                const matchesType = typeValue === 'All Types' || rowType === '' || rowType === typeValue || rowType.toLowerCase() === typeValue.toLowerCase();
                row.style.display = matchesView && matchesType ? 'grid' : 'none';
            });
        }

        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');
            const label = dropdown.querySelector('.dropdown-label');

            dropdown.addEventListener('click', function (event) {
                event.stopPropagation();
                const isOpen = menu.classList.contains('show');
                closeAllMenus();
                if (!isOpen) menu.classList.add('show');
            });

            menu.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function (event) {
                    event.stopPropagation();
                    menu.querySelectorAll('.dropdown-item').forEach(btn => btn.classList.remove('active'));
                    item.classList.add('active');
                    label.textContent = item.dataset.value;
                    closeAllMenus();
                    applyCalendarFilter();
                });
            });
        });

        document.addEventListener('click', closeAllMenus);

        const detailPopover = document.querySelector('[data-calendar-detail-popover]');
        document.querySelectorAll('.calendar-reservation[data-details]').forEach((bar) => {
            bar.addEventListener('click', function (event) {
                event.stopPropagation();
                const details = JSON.parse(this.dataset.details);
                const detailRows = details.type === 'Facility'
                    ? `<div><b>Guest:</b> ${details.guest}</div><div><b>Facility:</b> ${details.item}</div><div><b>Date:</b> ${details.check_in}</div><div><b>Time:</b> ${details.time}</div><div><b>Amount:</b> ₱${details.amount}</div><div><b>Status:</b> ${details.status}</div>`
                    : details.type === 'Event'
                        ? `<div><b>Guest:</b> ${details.guest}</div><div><b>Event:</b> ${details.item}</div><div><b>Event Type:</b> ${details.event_type}</div><div><b>Event Date:</b> ${details.check_in}</div><div><b>Start Time:</b> ${details.start_time}</div><div><b>End Time:</b> ${details.end_time}</div><div><b>Amount:</b> ₱${details.amount}</div><div><b>Status:</b> ${details.status}</div>`
                        : details.type === 'Dining'
                            ? `<div><b>Guest:</b> ${details.guest}</div><div><b>Dining:</b> ${details.item}</div><div><b>Dining Area/Table:</b> ${details.dining_area}</div><div><b>Date:</b> ${details.check_in}</div><div><b>Time:</b> ${details.time}</div><div><b>Number of Guests:</b> ${details.number_of_guests}</div><div><b>Amount:</b> ₱${details.amount}</div><div><b>Status:</b> ${details.status}</div>`
                            : `<div><b>Room:</b> ${details.room}</div><div><b>Check-in:</b> ${details.check_in}</div><div><b>Check-out:</b> ${details.check_out}</div><div><b>Amount:</b> ₱${details.amount}</div><div><b>Status:</b> ${details.status}</div>`;
                detailPopover.innerHTML = `<div class="mb-2 flex items-center justify-between gap-3"><strong class="text-sm text-slate-900">Booking Details</strong><button type="button" data-close-calendar-detail class="text-slate-400 hover:text-slate-700">&times;</button></div><div class="space-y-1 text-xs">${detailRows}</div>`;
                const rect = this.getBoundingClientRect();
                detailPopover.style.left = `${Math.min(rect.left, window.innerWidth - 295)}px`;
                detailPopover.style.top = `${Math.min(rect.bottom + 8, window.innerHeight - 190)}px`;
                detailPopover.classList.add('show');
            });
        });
        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-close-calendar-detail]') || !event.target.closest('.calendar-reservation[data-details]')) {
                detailPopover?.classList.remove('show');
            }
        });
        applyCalendarFilter();
    });
</script>
@endsection
