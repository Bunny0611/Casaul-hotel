@extends('admin.layout')

@section('content')
<style>
    .room-management-page {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        font-size: 16px;
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .room-management-page > * {
        max-width: 100%;
    }

    .room-management-page [data-panel] {
        min-width: 0;
    }

    .room-management-page .room-table-shell {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .room-management-page .room-table-shell table {
        width: 100%;
        min-width: 980px;
    }

    .room-management-page .dining-table-scroll {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .room-management-page .dining-table-scroll table {
        width: 100%;
        min-width: 720px;
        table-layout: auto;
    }

    .room-management-page .dining-table-scroll th:last-child,
    .room-management-page .dining-table-scroll td:last-child {
        text-align: right;
    }

    .room-management-page .dining-table-scroll th:last-child {
        min-width: 112px;
    }

    .room-management-page .room-filter-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .room-management-page .room-filter-panel {
        margin-bottom: 1rem;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .room-management-page .room-filter-card {
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        background: #fff;
    }

    .room-management-page .room-filter-card label {
        display: block;
        margin-bottom: 0.5rem;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .room-management-page .room-filter-card input,
    .room-management-page .room-filter-card select {
        width: 100%;
        min-height: 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.625rem;
        padding: 0.55rem 0.75rem;
        color: #374151;
        background: #fff;
        outline: none;
    }

    .room-management-page .room-filter-card input:focus,
    .room-management-page .room-filter-card select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.15);
    }

    @media (max-width: 640px) {
        .room-management-page .room-filter-grid {
            grid-template-columns: 1fr;
        }
    }

    .room-management-page .dining-form-column {
        display: contents;
    }

    @media (max-width: 1023px) {
        .room-management-page #diningFieldsGrid > [id$="Field"] {
            grid-column: span 1;
        }
    }

    .room-management-page [data-dining-subpanel="overview"] {
        display: none !important;
    }

    .room-management-page .dining-menu-category-tabs {
        display: flex;
        gap: 0.625rem;
        overflow-x: auto;
        padding: 1rem 1.25rem;
        scrollbar-width: thin;
    }

    .room-management-page .dining-menu-category-tab {
        flex: 0 0 auto;
        border: 1px solid #eadfd5;
        border-radius: 0.75rem;
        background: #fff;
        padding: 0.6rem 1rem;
        color: #5b3924;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25rem;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease;
    }

    .room-management-page .dining-menu-category-tab:hover {
        border-color: #fca5a5;
        background: #fff7f7;
    }

    .room-management-page .dining-menu-category-tab.is-active {
        border-color: #f97316;
        background: #f97316;
        color: #fff;
    }

    .room-management-page [data-dining-subpanel="tables"] .dining-card-header h3,
    .room-management-page [data-dining-subpanel="menu"] .dining-card-header h3,
    .room-management-page [data-dining-subpanel="schedule"] .dining-card-header h3 {
        display: none;
    }

    .room-management-page [data-dining-subpanel="tables"] .dining-card-header,
    .room-management-page [data-dining-subpanel="menu"] .dining-card-header,
    .room-management-page [data-dining-subpanel="schedule"] .dining-card-header {
        justify-content: flex-end;
    }

    .room-management-page .dining-card-header button[onclick^="confirmBulkDiningDelete"] {
        display: none;
    }

    .room-management-page [data-panel="rooms"] button[onclick="confirmBulkDelete()"],
    .room-management-page [data-panel="facilities"] button[onclick^="confirmBulkInventoryDelete"],
    .room-management-page [data-panel="events"] button[onclick^="confirmBulkInventoryDelete"] {
        display: none;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-edit) {
        border-radius: 0.5rem;
        border: 1px solid #bfdbfe;
        background-color: #eff6ff;
        padding: 0.5rem 0.75rem;
        color: #1d4ed8;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-edit):hover {
        background-color: #dbeafe;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-trash) {
        border-radius: 0.5rem;
        border: 1px solid #fecaca;
        background-color: #fef2f2;
        padding: 0.5rem 0.75rem;
        color: #b91c1c;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-trash):hover {
        background-color: #fee2e2;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-exchange-alt) {
        border-radius: 0.5rem;
        border: 1px solid #a7f3d0;
        background-color: #ecfdf5;
        padding: 0.5rem 0.75rem;
        color: #047857;
    }

    .room-management-page [data-dining-subpanel] td:last-child button:has(.fa-exchange-alt):hover {
        background-color: #d1fae5;
    }

    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span {
        display: inline-flex;
        border: 0;
        border-radius: 9999px;
        background-color: #059669;
        padding: 0.25rem 0.75rem;
        color: #fff;
        font-size: 0.75rem;
        line-height: 1rem;
        font-weight: 500;
    }

    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-available { background-color: #059669; }
    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-reserved { background-color: #2563eb; }
    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-unavailable { background-color: #dc2626; }
    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-occupied { background-color: #2563eb; }
    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-maintenance { background-color: #7c3aed; }
    .room-management-page [data-dining-subpanel] td:nth-last-child(2) span.status-limited { background-color: #ca8a04; }

    @media (max-width: 640px) {
        .room-management-page > .mb-6:first-child {
            margin-bottom: 1rem;
        }

        .room-management-page > .mb-6:first-child h2 {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .room-management-page > .mb-6:first-child .flex {
            width: 100%;
        }

        .room-management-page .tab-button {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 0;
            padding: 0.625rem 0.75rem;
            font-size: 0.75rem;
        }

        .room-management-page [data-panel] > .border-b {
            align-items: stretch;
            flex-direction: column;
            padding: 0.75rem 1rem;
        }

        .room-management-page [data-panel] > .border-b > .flex {
            justify-content: space-between;
        }

        .room-management-page [data-panel="rooms"] > .px-6 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .room-management-page .room-table-shell table {
            min-width: 860px;
        }

        .room-management-page .dining-table-scroll table {
            min-width: 680px;
        }

        .room-management-page [data-dining-subpanel] {
            padding: 0.75rem;
        }

        .room-management-page [data-dining-subpanel] .dining-card-header {
            padding: 0.75rem;
        }

        .room-management-page [data-dining-subpanel] .dining-card-header > .flex {
            align-items: stretch;
            flex-direction: column;
            gap: 0.5rem;
        }

        .room-management-page [data-dining-subpanel] .dining-card-header > .flex > * {
            width: 100%;
        }

        .room-management-page .admin-modal-panel {
            max-height: calc(100vh - 1rem);
            padding: 1rem;
        }

        .room-management-page .dining-card-header {
            align-items: stretch;
            flex-direction: column;
        }

        .room-management-page .dining-card-header button {
            width: 100%;
        }

        .room-management-page .dining-subtab {
            flex: 1 1 calc(50% - 0.5rem);
            justify-content: center;
        }
    }
</style>
<div class="room-management-page animate-fade-in">
    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Room Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage room inventory, availability, and maintenance from one place.</p>
        </div>
        <div class="flex w-full flex-wrap items-center gap-3 sm:w-auto">
            <button id="add-room-button" type="button" onclick="openAddRoomModal()" class="add-panel-button inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Room
            </button>
            <button id="add-facilities-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Facility
            </button>
            <button id="add-event-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Event
            </button>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-2 sm:gap-4">
        <button type="button" data-tab="rooms" class="tab-button rounded-lg bg-orange-500 px-6 py-3 font-medium text-white transition hover:bg-orange-600">ROOMS</button>
        <button type="button" data-tab="facilities" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">FACILITIES</button>
        <button type="button" data-tab="events" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">EVENTS</button>
        <button type="button" data-tab="dining" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">DINING</button>
    </div>

    <div id="roomFilterPanel" class="room-filter-panel">
        <div class="room-filter-grid">
            <div class="room-filter-card">
                <label for="roomSearch">Search</label>
                <input id="roomSearch" type="search" value="{{ request('room_search', '') }}" placeholder="Search by room number or type" autocomplete="off">
            </div>
            <div class="room-filter-card">
                <label for="roomTypeFilter">Room Type</label>
                <select id="roomTypeFilter">
                    <option value="" @selected(request('room_type', '') === '')>All room types</option>
                    <option value="standard" @selected(request('room_type') === 'standard')>Standard Room</option>
                    <option value="deluxe" @selected(request('room_type') === 'deluxe')>Deluxe Room</option>
                </select>
            </div>
        </div>
    </div>

    <div data-panel="rooms" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-none">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="text-sm text-gray-500">{{ $rooms->total() }} room{{ $rooms->total() === 1 ? '' : 's' }}</div>
            <div class="flex items-center gap-3">
                <span id="bulkSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span>
                <button type="button" onclick="confirmBulkDelete()" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected rooms">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.rooms.bulkDestroy') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="room_ids" id="bulkRoomIds">
        </form>
        <div class="px-6 py-2">
            <div class="flex flex-wrap items-center justify-between gap-4">
                {{-- total() = full room count across all pages (count() would only show the current page's 5 rows once paginated) --}}
                <div></div>
                <div></div>
            </div>
            <div class="room-table-shell overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                <input id="selectAllRoomsHeader" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" onclick="toggleAllRoomCheckboxes(this)">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Room No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Capacity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($rooms as $room)
                        <tr class="room-row transition-colors hover:bg-gray-50" data-room-search="{{ strtolower($room->room_number . ' ' . $room->room_type) }}" data-room-type="{{ str_contains(strtolower($room->room_type), 'standard') ? 'standard' : (str_contains(strtolower($room->room_type), 'deluxe') ? 'deluxe' : 'other') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <input type="checkbox" class="room-checkbox h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" value="{{ $room->id }}" onclick="updateSelectAllCheckbox()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->room_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($room->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->floor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="rounded-full px-3 py-1 text-xs font-medium text-white {{ match (strtolower((string) $room->status)) {
                                    'reserved' => 'bg-blue-600',
                                    'occupied' => 'bg-red-600',
                                    'maintenance', 'out_of_order' => 'bg-amber-600',
                                    'blocked', 'unavailable' => 'bg-slate-600',
                                    default => 'bg-emerald-600',
                                } }}">{{ ucfirst(str_replace('_', ' ', $room->status)) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type='button' onclick='editRoom({{ $room->id }}, @json($room->room_number), @json($room->room_type), @json($room->bed_type ?? "1 Queen Bed"), {{ $room->price }}, {{ $room->adult_guest_price ?? 0 }}, {{ $room->kid_guest_price ?? 0 }}, @json($room->floor), {{ $room->capacity }}, @json($room->status), @json($room->description), @json($room->image))' class='inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700 transition hover:bg-blue-100' aria-label='Edit room'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button type="button" onclick="changeStatus({{ $room->id }})" class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700 transition hover:bg-emerald-100" aria-label="Change room status">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-700 transition hover:bg-red-100" aria-label="Delete room">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-bed mb-4 text-4xl text-gray-300"></i>
                                <p>No rooms found. Add your first room to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $rooms->links('pagination.admin-rooms') }}
        </div>
    </div>

    <div data-panel="facilities" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div></div>
            <div class="flex items-center gap-3"><span id="facilitiesSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkInventoryDelete('facilities')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected facilities"><i class="fas fa-trash"></i></button></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"><input id="selectAllFacilitiesHeader" type="checkbox" class="inventory-select-all h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" data-category="facilities" onclick="toggleAllInventoryCheckboxes('facilities', this)"></th><th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Facility Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Capacity / Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="facilities-list" class="divide-y divide-gray-200">
                    @forelse($facilities as $item)
                        @php($facilityStatus = strtolower($item->status ?? 'unavailable'))
                        <tr class="transition-colors hover:bg-gray-50"><td class="px-6 py-4 text-sm"><input type="checkbox" class="inventory-checkbox inventory-checkbox-facilities h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $item->id }}" onclick="updateInventorySelectAll('facilities')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->capacity ?: '—' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-medium text-white {{ $facilityStatus === 'available' ? 'bg-green-500' : ($facilityStatus === 'reserved' ? 'bg-blue-600' : ($facilityStatus === 'limited' ? 'bg-yellow-500' : 'bg-red-500')) }}">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm"><div class="flex items-center gap-2"><button type='button' onclick='editInventory({{ $item->id }}, @json($item), "facilities")' class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700" aria-label="Edit facility"><i class="fas fa-edit"></i></button><button type='button' onclick='changeInventoryStatus({{ $item->id }}, @json($item->status), "facilities")' class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700" aria-label="Change facility status"><i class="fas fa-exchange-alt"></i></button><form action="{{ route('admin.inventory.destroy', $item->id) }}?category=facilities" method="POST" onsubmit="return confirm('Delete this facility?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-700" aria-label="Delete facility"><i class="fas fa-trash"></i></button></form></div></td></tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-concierge-bell mb-4 text-4xl text-gray-300"></i><p>No facilities found. Add your first facility to get started.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $facilities->links('pagination.admin-rooms') }}
    </div>

    <div data-panel="events" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4"><div></div><div class="flex items-center gap-3"><span id="eventSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkInventoryDelete('event')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white" aria-label="Delete selected events"><i class="fas fa-trash"></i></button></div></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"><input id="selectAllEventHeader" type="checkbox" class="inventory-select-all h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" data-category="event" onclick="toggleAllInventoryCheckboxes('event', this)"></th><th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Maximum Guests</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Available Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="events-list" class="divide-y divide-gray-200">
                    @foreach($events as $item)
                        <tr><td class="px-6 py-4 text-sm"><input type="checkbox" class="inventory-checkbox inventory-checkbox-event h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $item->id }}" onclick="updateInventorySelectAll('event')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->capacity ?: '—' }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->available_from && $item->available_to ? \Illuminate\Support\Carbon::parse($item->available_from)->format('g:i A') . ' - ' . \Illuminate\Support\Carbon::parse($item->available_to)->format('g:i A') : 'Any time' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full px-3 py-1 text-xs font-medium text-white {{ strtolower((string) $item->status) === 'reserved' ? 'bg-blue-600' : 'bg-green-500' }}">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm"><div class="flex items-center gap-2"><button type='button' onclick='editInventory({{ $item->id }}, @json($item), "event")' class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700" aria-label="Edit event"><i class="fas fa-edit"></i></button><button type='button' onclick='changeInventoryStatus({{ $item->id }}, @json($item->status), "event")' class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700" aria-label="Change event status"><i class="fas fa-exchange-alt"></i></button><form action="{{ route('admin.inventory.destroy', $item->id) }}?category=event" method="POST" onsubmit="return confirm('Delete this event?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-700" aria-label="Delete event"><i class="fas fa-trash"></i></button></form></div></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $events->links('pagination.admin-rooms') }}
    </div>

    <div data-panel="dining" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3 sm:gap-4 sm:px-6">
            <button type="button" data-dining-subtab="tables" class="dining-subtab inline-flex items-center gap-2 rounded-lg border border-transparent px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-white hover:text-gray-700">
                <i class="fas fa-table text-xs"></i>
                <span>Tables / Seating</span>
            </button>
            <button type="button" data-dining-subtab="menu" class="dining-subtab inline-flex items-center gap-2 rounded-lg border border-transparent px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-white hover:text-gray-700">
                <i class="fas fa-utensils text-xs"></i>
                <span>Menu / Meals</span>
            </button>
            <button type="button" data-dining-subtab="schedule" class="dining-subtab inline-flex items-center gap-2 rounded-lg border border-transparent px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-white hover:text-gray-700">
                <i class="fas fa-calendar-alt text-xs"></i>
                <span>Dining Schedule</span>
            </button>
        </div>

        <div data-dining-subpanel="overview" class="block">
            <div class="grid gap-5 p-5 lg:grid-cols-3 lg:p-6">
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4">
                        <h3 class="text-xl font-semibold text-gray-800">Tables / Seating</h3>
                        <button type="button" data-dining-add="table" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50">
                            <i class="fas fa-plus mr-2"></i>Add Table
                        </button>
                    </div>
                    <div class="dining-table-scroll">
                        <table class="min-w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600"></th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Table No.</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Capacity</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Location</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-4"><button type="button" class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">View All Tables</button></div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4"><h3 class="text-xl font-semibold text-gray-800">Menu / Meals</h3><button type="button" data-dining-add="menu" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Menu</button></div>
                    <div class="dining-table-scroll">
                        <table class="min-w-full text-left">
                            <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600"></th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Meal Name</th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Category</th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Price</th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Available Time</th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th></tr></thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($dining as $menu)
                                    <tr class="bg-white" data-dining-type="menus" data-menu-description="{{ e($menu->description ?? '') }}" data-menu-image="{{ e($menu->image ?? '') }}"><td class="px-4 py-3 text-sm"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $menu->id }}"></td><td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $menu->name }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $menu->category ?: 'Menu / Meal' }}</td><td class="px-4 py-3 text-sm text-gray-700">₱{{ number_format((float) $menu->price, 2) }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $menu->available_from && $menu->available_to ? \Illuminate\Support\Carbon::parse($menu->available_from)->format('g:i A') . ' - ' . \Illuminate\Support\Carbon::parse($menu->available_to)->format('g:i A') : 'Any time' }}</td><td class="px-4 py-3"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium {{ strtolower($menu->status) === 'available' ? 'border-green-200 bg-green-100 text-green-700' : 'border-red-200 bg-red-100 text-red-700' }}">{{ ucfirst($menu->status) }}</span></td><td class="px-4 py-3 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No menu items found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-4"><button type="button" class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">View All Menus</button></div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4"><h3 class="text-xl font-semibold text-gray-800">Dining Schedule</h3><button type="button" data-dining-add="schedule" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Schedule</button></div>
                    <div class="dining-table-scroll">
                        
                    </div>
                    <div class="border-t border-gray-200 px-4 py-4"><button type="button" class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">View All Schedules</button></div>
                </div>
            </div>
        </div>

        <div data-dining-subpanel="tables" class="hidden p-5 lg:p-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4"><h3 class="text-xl font-semibold text-gray-800">Tables / Seating</h3><div class="flex items-center gap-3"><span id="diningTablesSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkDiningDelete('tables')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected tables"><i class="fas fa-trash"></i></button><button type="button" data-dining-add="table" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Table</button></div></div>
                <div class="dining-table-scroll">
                    <table class="min-w-full text-left">
                        <thead class="bg-gray-50"><tr><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="dining-table-select-all h-4 w-4 rounded border-gray-300 text-orange-600" onclick="toggleAllDiningCheckboxes('tables', this)"></th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Table No.</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Type</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Capacity</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Location</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($diningTables as $table)
                                <tr class="dining-table-row bg-white"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-table-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $table->id }}" onclick="updateDiningSelectCount('tables')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $table->table_no }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->type }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->capacity }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->location ?: '—' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium {{ strtolower((string) $table->status) === 'reserved' ? 'border-blue-200 bg-blue-100 text-blue-700' : '' }}">{{ ucfirst($table->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-6 !text-center text-sm text-gray-500">No tables found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div data-dining-pagination="tables" class="flex items-center justify-between border-t border-gray-200 px-6 py-3 text-sm text-gray-600"></div>
            </div>
        </div>

        <div data-dining-subpanel="menu" class="hidden p-5 lg:p-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-2"><h3 class="text-xl font-semibold text-gray-800">Menu / Meals</h3><div class="flex items-center gap-3"><span id="diningMenusSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkDiningDelete('menus')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected menus"><i class="fas fa-trash"></i></button><button type="button" data-dining-add="menu" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Menu</button></div></div>
                <div class="dining-menu-category-tabs" role="tablist" aria-label="Meal categories">
                    @foreach(['Breakfast', 'Appetizer', 'Main Course', 'Soup', 'Salad', 'Dessert', 'Beverage'] as $category)
                        <button type="button" class="dining-menu-category-tab {{ $loop->first ? 'is-active' : '' }}" data-menu-category-tab="{{ $category }}" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $category }}</button>
                    @endforeach
                </div>
                <div class="dining-table-scroll">
                    <table class="min-w-full text-left">
                        <thead class="bg-gray-50"><tr><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="dining-menu-select-all h-4 w-4 rounded border-gray-300 text-orange-600" onclick="toggleAllDiningCheckboxes('menus', this)"></th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Meal Name</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Category</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Price</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Available Time</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($dining as $menu)
                                <tr class="bg-white dining-menu-row" data-menu-category="{{ $menu->category ?: 'Breakfast' }}" data-menu-description="{{ e($menu->description ?? '') }}" data-menu-image="{{ e($menu->image ?? '') }}"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-menu-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $menu->id }}" onclick="updateDiningSelectCount('menus')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $menu->name }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $menu->category ?: 'Menu / Meal' }}</td><td class="px-6 py-4 text-sm text-gray-700">₱{{ number_format((float) $menu->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $menu->available_from && $menu->available_to ? \Illuminate\Support\Carbon::parse($menu->available_from)->format('g:i A') . ' - ' . \Illuminate\Support\Carbon::parse($menu->available_to)->format('g:i A') : 'Any time' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium {{ strtolower($menu->status) === 'available' ? 'border-green-200 bg-green-100 text-green-700' : 'border-red-200 bg-red-100 text-red-700' }}">{{ ucfirst($menu->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr class="dining-menu-empty"><td colspan="7" class="px-6 py-6 !text-center text-sm text-gray-500">No menu items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div data-dining-pagination="menus" class="flex items-center justify-between border-t border-gray-200 px-6 py-3 text-sm text-gray-600"></div>
            </div>
        </div>

        <div data-dining-subpanel="schedule" class="hidden p-5 lg:p-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4"><h3 class="text-xl font-semibold text-gray-800">Dining Schedule</h3><div class="flex items-center gap-3"><span id="diningSchedulesSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkDiningDelete('schedules')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected schedules"><i class="fas fa-trash"></i></button><button type="button" data-dining-add="schedule" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Schedule</button></div></div>
                <div class="dining-table-scroll">
                    <table class="min-w-full text-left">
                        <thead class="bg-gray-50"><tr><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="dining-schedule-select-all h-4 w-4 rounded border-gray-300 text-orange-600" onclick="toggleAllDiningCheckboxes('schedules', this)"></th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Meal Period</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Time</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Max Guests</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($diningSchedules as $schedule)
                                <tr class="dining-schedule-row bg-white"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-schedule-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $schedule->id }}" onclick="updateDiningSelectCount('schedules')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule->period }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ \Illuminate\Support\Carbon::parse($schedule->available_from)->format('g:i A') }} - {{ \Illuminate\Support\Carbon::parse($schedule->available_to)->format('g:i A') }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $schedule->max_guests ?: '—' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium">{{ ucfirst($schedule->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-6 !text-center text-sm text-gray-500">No dining schedules found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div data-dining-pagination="schedules" class="flex items-center justify-between border-t border-gray-200 px-6 py-3 text-sm text-gray-600"></div>
            </div>
        </div>
    </div>
</div>

<form id="bulkInventoryDeleteForm" method="POST" action="{{ route('admin.inventory.bulkDestroy') }}">
    @csrf
    @method('DELETE')
    <input type="hidden" name="category" id="bulkInventoryCategory">
    <input type="hidden" name="inventory_ids" id="bulkInventoryIds">
</form>

<form id="bulkDiningDeleteForm" method="POST" action="{{ route('admin.dining.bulkDestroy') }}">
    @csrf
    @method('DELETE')
    <input type="hidden" name="type" id="bulkDiningType">
    <input type="hidden" name="dining_ids" id="bulkDiningIds">
</form>

<div id="editInventoryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEditInventoryModal()" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
        <div class="mb-6"><h3 class="text-2xl font-bold text-gray-800">Edit Inventory Item</h3><p class="mt-1 text-sm text-gray-500">Update the selected item details.</p></div>
        <form id="editInventoryForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="category" id="editInventoryCategory">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div><label id="editInventoryNameLabel" class="mb-1 block text-sm font-medium text-gray-700">Name</label><input id="editInventoryName" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div id="editInventoryTypeField"><label class="mb-1 block text-sm font-medium text-gray-700">Event Type</label><select id="editInventoryType" name="event_type" class="w-full rounded-lg border border-gray-300 px-3 py-2"><option>Birthday</option><option>Wedding</option></select></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label><input id="editInventoryPrice" name="price" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Pricing Basis</label><select id="editInventoryPricingBasis" name="pricing_basis" class="w-full rounded-lg border border-gray-300 px-3 py-2"><option>Per Stay</option><option>Per Person</option><option>Per Vehicle</option><option>Per Stay + Per Vehicle</option><option>Per Hour</option><option>Per Day</option><option>Fixed Price</option><option>Per Event</option></select></div>
                <div id="editInventoryDurationField" class="hidden"><label id="editInventoryDurationLabel" class="mb-1 block text-sm font-medium text-gray-700">Duration (hours)</label><input id="editInventoryDuration" name="duration_hours" type="number" min="1" max="24" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label id="editInventoryCapacityLabel" class="mb-1 block text-sm font-medium text-gray-700">Capacity / Maximum Guests</label><input id="editInventoryCapacity" name="capacity" type="number" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div id="editInventoryLocationField"><label class="mb-1 block text-sm font-medium text-gray-700">Location</label><select id="editInventoryLocation" name="location" class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="">Select Location</option><option value="Ground Floor">Ground Floor</option><option value="2nd Floor">2nd Floor</option><option value="Garden">Garden</option><option value="Private Room">Private Room</option><option value="Poolside">Poolside</option><option value="Rooftop">Rooftop</option></select></div>
                <div id="editInventorySchedulingField"><label class="mb-1 block text-sm font-medium text-gray-700">Scheduling Requirement</label><select id="editInventoryScheduling" name="scheduling_requirement" class="w-full rounded-lg border border-gray-300 px-3 py-2"><option>No Additional Schedule</option><option>Date Required</option><option>Date &amp; Time Required</option></select></div>
                <div id="editInventoryQuantityField"><label class="mb-1 block text-sm font-medium text-gray-700">Quantity</label><input id="editInventoryQuantity" name="quantity" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div id="editInventoryFromField"><label class="mb-1 block text-sm font-medium text-gray-700">Available From</label><input id="editInventoryFrom" name="available_from" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div id="editInventoryToField"><label class="mb-1 block text-sm font-medium text-gray-700">Available To</label><input id="editInventoryTo" name="available_to" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label id="editInventoryStatusLabel" class="mb-1 block text-sm font-medium text-gray-700">Availability</label><select id="editInventoryStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option></select></div>
            </div>
            <div id="editInventoryImageField" class="hidden">
                <label id="editInventoryImageLabel" class="mb-1 block text-sm font-medium text-gray-700">Catalog Photo</label>
                <img id="editInventoryImagePreview" src="" alt="Current event place photo" class="mb-3 hidden h-40 w-full rounded-lg border border-gray-200 object-cover">
                <p id="editInventoryNoImage" class="mb-3 hidden text-sm text-gray-500">No photo attached.</p>
                <input id="editInventoryImage" name="image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Description</label><textarea id="editInventoryDescription" name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea></div>
            <div class="flex justify-end gap-3"><button type="button" onclick="closeEditInventoryModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Update Item</button></div>
        </form>
    </div>
</div>

<div id="inventoryStatusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl"><button type="button" onclick="closeInventoryStatusModal()" class="absolute right-4 top-4 text-gray-500"><i class="fas fa-times text-xl"></i></button><h3 class="mb-4 text-2xl font-bold text-gray-800">Change Inventory Status</h3><form id="inventoryStatusForm" action="" method="POST" class="space-y-4">@csrf @method('PATCH')<select name="status" id="inventoryStatusSelect" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option></select><div class="flex justify-end gap-3"><button type="button" onclick="closeInventoryStatusModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Update Status</button></div></form></div>
</div>

<div id="addFacilityModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeFacilityModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Facility</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new facility option for guests.</p>
        </div>
        @if($errors->any() && old('category') === 'facilities')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('name') }}</div>
        @endif

        <form id="addFacilityForm" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="facilities">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Facility Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Pricing Basis</label>
                    <select name="pricing_basis" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option>Per Stay</option><option>Per Person</option><option>Per Vehicle</option><option>Per Stay + Per Vehicle</option><option>Per Hour</option><option>Per Day</option><option>Fixed Price</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <select name="location" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">Select Location</option>
                        <option value="Ground Floor">Ground Floor</option>
                        <option value="2nd Floor">2nd Floor</option>
                        <option value="Garden">Garden</option>
                        <option value="Private Room">Private Room</option>
                        <option value="Poolside">Poolside</option>
                        <option value="Rooftop">Rooftop</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Capacity / Quantity (e.g. 2 vehicles)</label>
                    <input type="number" name="capacity" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Scheduling Requirement</label>
                    <select name="scheduling_requirement" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option>No Additional Schedule</option><option>Date Required</option><option>Date &amp; Time Required</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Availability</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"><option value="available">Available</option><option value="unavailable">Unavailable</option></select>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeFacilityModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Facility</button>
            </div>
        </form>
    </div>
</div>

<div id="addEventModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEventModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Event &amp; Catering Service</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new event or catering package for guests.</p>
        </div>
        @if($errors->any() && old('category') === 'event')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('name') }}</div>
        @endif

        <form id="addEventForm" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="event">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Type</label>
                    <select name="event_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option>Birthday</option><option>Wedding</option></select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Package Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Pricing Basis</label>
                    <select id="eventPricingBasis" name="pricing_basis" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option selected>Per Person</option><option>Per Hour</option></select>
                </div>
                <div>
                    <label id="eventDurationLabel" class="mb-1 block text-sm font-medium text-gray-700">Fixed Duration (hours)</label>
                    <input id="eventDurationHours" type="number" name="duration_hours" value="4" min="1" max="24" required class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Maximum Guests</label>
                    <input type="number" name="capacity" min="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <select name="location" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Location</option>
                        <option value="Ground Floor">Ground Floor</option>
                        <option value="2nd Floor">2nd Floor</option>
                        <option value="Garden">Garden</option>
                        <option value="Private Room">Private Room</option>
                        <option value="Poolside">Poolside</option>
                        <option value="Rooftop">Rooftop</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Available From</label>
                    <select id="eventAvailableFrom" name="available_from" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @for($hour = 8; $hour <= 22; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}">{{ \Carbon\Carbon::createFromTime($hour)->format('g:i A') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Available To</label>
                    <select id="eventAvailableTo" name="available_to" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @for($hour = 8; $hour <= 22; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ $hour === 22 ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromTime($hour)->format('g:i A') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Availability</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="available">Available</option><option value="unavailable">Unavailable</option></select>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEventModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Event &amp; Catering Service</button>
            </div>
        </form>
    </div>
</div>

<div id="addDiningModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-6xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-6">
            <h3 id="addDiningTitle" class="text-3xl font-bold text-gray-800">Add Menu / Meal</h3>
            <p class="mt-2 text-base text-gray-600">Add the details needed for this dining section.</p>
        </div>
        @if($errors->any() && old('category') === 'dining')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('name') }}</div>
        @endif

        <form id="addDiningForm" method="POST" action="{{ route('admin.dining.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="dining_type" id="diningFormType" value="menus">
            <div id="diningFieldsGrid" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div id="diningNameField">
                        <label id="diningNameLabel" class="mb-2 block text-base font-medium text-gray-700">Meal Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Grilled Salmon">
                    </div>

                    <div id="diningMenuCategoryField">
                        <label class="mb-2 block text-base font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="menu_category" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Breakfast">Breakfast</option>
                            <option value="Appetizer">Appetizer</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Soup">Soup</option>
                            <option value="Salad">Salad</option>
                            <option value="Dessert">Dessert</option>
                            <option value="Beverage">Beverage</option>
                        </select>
                    </div>

                    <div id="diningTypeField">
                        <label id="diningTypeLabel" class="mb-2 block text-base font-medium text-gray-700">Type</label>
                        <input type="text" name="type" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Indoor">
                    </div>

                    <div id="diningCapacityField" class="hidden">
                        <label class="mb-2 block text-base font-medium text-gray-700">Capacity <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" min="1" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. 4">
                    </div>

                    <div id="diningLocationField" class="hidden">
                        <label class="mb-2 block text-base font-medium text-gray-700">Location</label>
                        <input type="text" name="location" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Main Area">
                    </div>

                    <div id="diningPriceField">
                        <label class="mb-2 block text-base font-medium text-gray-700">Price (₱) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" step="0.01" min="0" required class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. 650.00">
                    </div>

                    <div id="diningImageField">
                        <label class="mb-2 block text-base font-medium text-gray-700">Image (Optional)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div id="diningDescriptionField" class="lg:col-span-2">
                        <label class="mb-2 block text-base font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Describe the meal"></textarea>
                    </div>

                    <div id="diningMaxGuestsField" class="hidden">
                        <label class="mb-2 block text-base font-medium text-gray-700">Maximum Guests</label>
                        <input type="number" name="max_guests" min="1" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. 40">
                    </div>

                    <div id="diningScheduleTimeField" class="hidden">
                        <label class="mb-2 block text-base font-medium text-gray-700">Available Time <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                            <input type="time" name="available_from" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <span class="text-lg text-gray-500">to</span>
                            <input type="time" name="available_to" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div id="diningStatusField">
                        <label class="mb-2 block text-base font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Available">Available</option>
                            <option value="Limited">Limited</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>

            </div>

            <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-6">
                <button type="button" onclick="closeDiningModal()" class="rounded-xl border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button id="addDiningSubmit" type="submit" class="rounded-xl bg-orange-500 px-6 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">Save Menu / Meal</button>
            </div>
        </form>
    </div>
</div>

<div id="editDiningModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEditDiningModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700" aria-label="Close edit dining item">
            <i class="fas fa-times text-xl"></i>
        </button>
        <div class="mb-6">
            <h3 id="editDiningTitle" class="text-2xl font-bold text-gray-800">Edit Dining Item</h3>
            <p class="mt-1 text-sm text-gray-500">Update the selected dining details below.</p>
        </div>
        <form id="editDiningForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label id="editDiningNameLabel" for="editDiningName" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                    <input id="editDiningName" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label id="editDiningTypeLabel" for="editDiningType" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                    <input id="editDiningType" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <select id="editDiningMenuCategory" class="hidden w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="Breakfast">Breakfast</option>
                        <option value="Appetizer">Appetizer</option>
                        <option value="Main Course">Main Course</option>
                        <option value="Soup">Soup</option>
                        <option value="Salad">Salad</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Beverage">Beverage</option>
                    </select>
                </div>
                <div>
                    <label id="editDiningValueLabel" for="editDiningValue" class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input id="editDiningValue" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label id="editDiningDetailLabel" for="editDiningDetail" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <input id="editDiningDetail" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <select id="editDiningMenuTime" class="hidden w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="afternoon-snack">Afternoon Snack</option>
                        <option value="dinner">Dinner</option>
                        <option value="all-day">Available All Day</option>
                    </select>
                </div>
                <div>
                    <label for="editDiningStatus" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select id="editDiningStatus" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="Available">Available</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Limited">Limited</option>
                        <option value="Active">Active</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
            </div>
            <div id="editDiningMenuDetails" class="hidden space-y-4">
                <div>
                    <label for="editDiningDescription" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="editDiningDescription" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div>
                    <label for="editDiningImage" class="mb-1 block text-sm font-medium text-gray-700">Meal Image</label>
                    <img id="editDiningImagePreview" src="" alt="Current meal image" class="mb-3 hidden h-40 w-full rounded-lg border border-gray-200 object-cover">
                    <input id="editDiningImage" type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEditDiningModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Update Item</button>
            </div>
        </form>
    </div>
</div>

<div id="addRoomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-2 sm:p-4">
    <div class="admin-modal-panel relative max-h-[95vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAddRoomModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add New Room</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the details below to create a new room.</p>
        </div>

        @if($errors->any() && !old('category'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="status" value="available">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Number</label>
                    <input type="text" name="room_number" value="{{ old('room_number') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Type</label>
                    <select name="room_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Type</option>
                        <option value="Deluxe Room" @selected(old('room_type') === 'Deluxe Room')>Deluxe Room</option>
                        <option value="Standard Room" @selected(old('room_type') === 'Standard Room')>Standard Room</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Bed Type</label>
                    <select name="bed_type" class="room-bed-type w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" onchange="toggleCustomBedType(this)" required>
                        @include('admin.partials.bed-type-options', ['selectedBedType' => old('bed_type', '1 Queen Bed')])
                    </select>
                    <input type="text" class="bed-type-custom mt-2 hidden w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" maxlength="100" placeholder="Enter custom bed type">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Extra Adult Price (₱)</label>
                    <input type="number" name="adult_guest_price" value="{{ old('adult_guest_price') }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Extra Kid Price (₱)</label>
                    <input type="number" name="kid_guest_price" value="{{ old('kid_guest_price') }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Floor</label>
                    <select name="floor" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Floor</option>
                        <option value="1st" @selected(old('floor') === '1st')>1st Floor</option>
                        <option value="2nd" @selected(old('floor') === '2nd')>2nd Floor</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" required min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Room Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeAddRoomModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Room</button>
            </div>
        </form>
    </div>
</div>

<div id="editRoomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEditRoomModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit Room</h3>
            <p class="mt-1 text-sm text-gray-500">Update the room details below.</p>
        </div>

        <form id="editRoomForm" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editRoomId">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Number</label>
                    <input type="text" name="room_number" id="editRoomNumber" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Type</label>
                    <select name="room_type" id="editRoomType" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Type</option>
                        <option value="Deluxe Room">Deluxe Room</option>
                        <option value="Standard Room">Standard Room</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Bed Type</label>
                    <select name="bed_type" id="editBedType" class="room-bed-type w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" onchange="toggleCustomBedType(this)" required>
                        @include('admin.partials.bed-type-options', ['selectedBedType' => '1 Queen Bed'])
                    </select>
                    <input type="text" id="editBedTypeCustom" class="bed-type-custom mt-2 hidden w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" maxlength="100" placeholder="Enter custom bed type">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" id="editPrice" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Extra Adult Price (₱)</label>
                    <input type="number" name="adult_guest_price" id="editAdultGuestPrice" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Extra Kid Price (₱)</label>
                    <input type="number" name="kid_guest_price" id="editKidGuestPrice" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Floor</label>
                    <select name="floor" id="editFloor" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Floor</option>
                        <option value="1st">1st Floor</option>
                        <option value="2nd">2nd Floor</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" id="editCapacity" required min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="editStatus" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="reserved">Reserved</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="editDescription" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Room Image (Optional)</label>
                <input id="editRoomImage" type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <img id="editRoomImagePreview" src="" alt="Current room image" class="mt-3 hidden h-40 w-full rounded-lg border border-gray-200 object-cover">
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEditRoomModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Update Room</button>
            </div>
        </form>
    </div>
</div>

<div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeStatusModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Change Room Status</h3>
            <p class="mt-1 text-sm text-gray-500">Choose the new status for this room.</p>
        </div>

        <form id="statusForm" action="" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="id" id="statusRoomId">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Select Status</label>
                <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="reserved">Reserved</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeStatusModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddRoomModal() {
        document.getElementById('addRoomModal').classList.remove('hidden');
        document.getElementById('addRoomModal').classList.add('flex');
    }

    function closeAddRoomModal() {
        document.getElementById('addRoomModal').classList.add('hidden');
        document.getElementById('addRoomModal').classList.remove('flex');
    }

    function editRoom(id, roomNumber, roomType, bedType, price, adultGuestPrice, kidGuestPrice, floor, capacity, status, description, image = null) {
        document.getElementById('editRoomId').value = id;
        document.getElementById('editRoomNumber').value = roomNumber;
        document.getElementById('editRoomType').value = roomType;
        const editBedType = document.getElementById('editBedType');
        const editBedTypeCustom = document.getElementById('editBedTypeCustom');
        const savedBedType = bedType || '1 Queen Bed';
        const knownBedType = [...editBedType.options].some(option => option.value === savedBedType);
        if (!knownBedType && savedBedType) {
            editBedTypeCustom.value = savedBedType;
            editBedType.value = '__custom__';
        } else {
            editBedType.value = savedBedType;
            editBedTypeCustom.value = '';
        }
        toggleCustomBedType(editBedType);
        document.getElementById('editPrice').value = price;
        document.getElementById('editAdultGuestPrice').value = adultGuestPrice;
        document.getElementById('editKidGuestPrice').value = kidGuestPrice;

        const normalizedFloor = floor === '1st Floor' || floor === '1st' || floor === '1' ? '1st'
            : floor === '2nd Floor' || floor === '2nd' || floor === '2' ? '2nd'
            : '1st';
        document.getElementById('editFloor').value = normalizedFloor;

        document.getElementById('editCapacity').value = capacity;
        document.getElementById('editStatus').value = status;
        document.getElementById('editDescription').value = description || '';

        const roomImageInput = document.getElementById('editRoomImage');
        const roomImagePreview = document.getElementById('editRoomImagePreview');
        const roomImageUrl = image ? "{{ asset('storage') }}/" + image : '';

        roomImagePreview.src = roomImageUrl;
        roomImagePreview.classList.toggle('hidden', !roomImageUrl);
        roomImageInput.value = '';

        var editRoute = "{{ route('admin.rooms.update', ['id' => '__ID__']) }}";
        document.getElementById('editRoomForm').action = editRoute.replace('__ID__', id);
        document.getElementById('editRoomModal').classList.remove('hidden');
        document.getElementById('editRoomModal').classList.add('flex');
    }

    function toggleCustomBedType(select) {
        const customInput = select.parentElement.querySelector('.bed-type-custom');
        if (!customInput) return;

        const isCustom = select.value === '__custom__';
        customInput.classList.toggle('hidden', !isCustom);
        customInput.required = isCustom;
    }

    function applyCustomBedType(form) {
        const select = form.querySelector('.room-bed-type');
        const customInput = form.querySelector('.bed-type-custom');
        if (!select || select.value !== '__custom__' || !customInput?.value.trim()) return;

        const customValue = customInput.value.trim();
        const customOption = new Option(customValue, customValue, true, true);
        select.add(customOption);
    }

    function closeEditRoomModal() {
        const roomImageInput = document.getElementById('editRoomImage');
        const roomImagePreview = document.getElementById('editRoomImagePreview');

        roomImageInput.value = '';
        roomImagePreview.src = '';
        roomImagePreview.classList.add('hidden');
        document.getElementById('editRoomModal').classList.add('hidden');
        document.getElementById('editRoomModal').classList.remove('flex');
    }

    function changeStatus(id) {
        document.getElementById('statusRoomId').value = id;
        var statusRoute = "{{ route('admin.rooms.status', ['id' => '__ID__']) }}";
        document.getElementById('statusForm').action = statusRoute.replace('__ID__', id);
        document.getElementById('statusModal').classList.remove('hidden');
        document.getElementById('statusModal').classList.add('flex');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
        document.getElementById('statusModal').classList.remove('flex');
    }

    function updateSelectedCount() {
        const count = document.querySelectorAll('.room-checkbox:checked').length;
        const label = document.getElementById('bulkSelectedCount');
        if (label) {
            label.textContent = count + ' selected';
        }
        const deleteButton = document.querySelector('[data-panel="rooms"] button[onclick="confirmBulkDelete()"]');
        if (deleteButton) deleteButton.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function toggleAllRoomCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.room-checkbox');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = source.checked;
        });
        updateSelectedCount();
    }

    function updateSelectAllCheckbox() {
        const checkboxes = document.querySelectorAll('.room-checkbox');
        const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(function (checkbox) {
            return checkbox.checked;
        });
        const selectAll = document.getElementById('selectAllRoomsHeader');
        if (selectAll) {
            selectAll.checked = allChecked;
        }
        updateSelectedCount();
    }

    function confirmBulkDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.room-checkbox:checked')).map(function (checkbox) {
            return checkbox.value;
        });

        if (!selectedIds.length) {
            alert('Please select at least one room to delete.');
            return;
        }

        if (!confirm('Are you sure you want to delete the selected ' + selectedIds.length + ' room(s)?')) {
            return;
        }

        const bulkRoomIds = document.getElementById('bulkRoomIds');
        if (!bulkRoomIds) {
            return;
        }

        bulkRoomIds.value = selectedIds.join(',');
        document.getElementById('bulkDeleteForm').submit();
    }

    function toggleAllInventoryCheckboxes(category, source) {
        document.querySelectorAll('.inventory-checkbox-' + category).forEach(function (checkbox) {
            checkbox.checked = source.checked;
        });
        updateInventorySelectAll(category);
    }

    function updateInventorySelectAll(category) {
        const checkboxes = Array.from(document.querySelectorAll('.inventory-checkbox-' + category));
        const allChecked = checkboxes.length > 0 && checkboxes.every(function (checkbox) { return checkbox.checked; });
        document.querySelectorAll('.inventory-select-all[data-category="' + category + '"]').forEach(function (checkbox) {
            checkbox.checked = allChecked;
        });
        const count = document.querySelectorAll('.inventory-checkbox-' + category + ':checked').length;
        const label = document.getElementById(category + 'SelectedCount');
        if (label) label.textContent = count + ' selected';
        const panelName = category === 'event' ? 'events' : category;
        const deleteButton = document.querySelector('[data-panel="' + panelName + '"] button[onclick^="confirmBulkInventoryDelete"]');
        if (deleteButton) deleteButton.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function confirmBulkInventoryDelete(category) {
        const selectedIds = Array.from(document.querySelectorAll('.inventory-checkbox-' + category + ':checked')).map(function (checkbox) { return checkbox.value; });
        if (!selectedIds.length) { alert('Please select at least one item to delete.'); return; }
        if (!confirm('Are you sure you want to delete the selected ' + selectedIds.length + ' item(s)?')) return;
        document.getElementById('bulkInventoryCategory').value = category;
        document.getElementById('bulkInventoryIds').value = selectedIds.join(',');
        document.getElementById('bulkInventoryDeleteForm').submit();
    }

    function diningCheckboxSelector(type) {
        return type === 'tables' ? '.dining-table-checkbox' : type === 'menus' ? '.dining-menu-checkbox' : '.dining-schedule-checkbox';
    }

    function diningPanelName(type) {
        return type === 'tables' ? 'tables' : type === 'menus' ? 'menu' : 'schedule';
    }

    function diningSelectAllClass(type) {
        return type === 'tables' ? 'dining-table-select-all' : type === 'menus' ? 'dining-menu-select-all' : 'dining-schedule-select-all';
    }

    function diningCheckboxes(type) {
        const checkboxes = Array.from(document.querySelectorAll(diningCheckboxSelector(type)));
        if (type !== 'menus') return checkboxes;

        const activeCategory = document.querySelector('[data-menu-category-tab].is-active')?.getAttribute('data-menu-category-tab');
        return activeCategory
            ? checkboxes.filter((checkbox) => checkbox.closest('tr')?.dataset.menuCategory === activeCategory)
            : checkboxes;
    }

    function updateDiningSelectCount(type) {
        const checkboxes = diningCheckboxes(type);
        const count = checkboxes.filter((checkbox) => checkbox.checked).length;
        const label = document.getElementById('dining' + type.charAt(0).toUpperCase() + type.slice(1) + 'SelectedCount');
        if (label) label.textContent = count + ' selected';

        const panelName = diningPanelName(type);
        const selectAll = document.querySelector('.' + diningSelectAllClass(type));
        if (selectAll) {
            selectAll.checked = count > 0 && count === checkboxes.length;
        }
        const deleteButton = document.querySelector('[data-dining-subpanel="' + panelName + '"] button[onclick^="confirmBulkDiningDelete"]');
        if (deleteButton) deleteButton.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function toggleAllDiningCheckboxes(type, source) {
        diningCheckboxes(type).forEach(function (checkbox) {
            checkbox.checked = source.checked;
        });
        updateDiningSelectCount(type);
    }

    function confirmBulkDiningDelete(type) {
        const selectedIds = diningCheckboxes(type).filter((checkbox) => checkbox.checked).map(function (checkbox) {
            return checkbox.value;
        });
        if (!selectedIds.length) {
            alert('Please select at least one item to delete.');
            return;
        }
        if (!confirm('Are you sure you want to delete the selected ' + selectedIds.length + ' item(s)?')) return;
        document.getElementById('bulkDiningType').value = type;
        document.getElementById('bulkDiningIds').value = selectedIds.join(',');
        document.getElementById('bulkDiningDeleteForm').submit();
    }

    function editInventory(id, item, category) {
        category = category || item.category || 'dining';
        document.getElementById('editInventoryCategory').value = category;
        document.getElementById('editInventoryName').value = item.name || '';
        document.getElementById('editInventoryType').value = item.event_type || item.type || 'Birthday';
        document.getElementById('editInventoryPrice').value = item.price || 0;
        const quantityField = document.getElementById('editInventoryQuantityField');
        const quantityInput = document.getElementById('editInventoryQuantity');
        const durationField = document.getElementById('editInventoryDurationField');
        const durationInput = document.getElementById('editInventoryDuration');
        const typeField = document.getElementById('editInventoryTypeField');
        const typeInput = document.getElementById('editInventoryType');
        const fromField = document.getElementById('editInventoryFromField');
        const toField = document.getElementById('editInventoryToField');
        const isEvent = category === 'event';
        const isFacility = category === 'facilities';
        quantityField.classList.toggle('hidden', isEvent || isFacility);
        quantityInput.disabled = isEvent || isFacility;
        durationField.classList.toggle('hidden', !isEvent);
        durationInput.disabled = !isEvent;
        durationInput.value = item.duration_hours || 4;
        document.getElementById('editInventoryDurationLabel').textContent = item.pricing_basis === 'Per Hour' ? 'Maximum Duration (hours)' : 'Fixed Duration (hours)';
        typeField.classList.toggle('hidden', isFacility);
        typeInput.disabled = isFacility;
        const schedulingField = document.getElementById('editInventorySchedulingField');
        const schedulingInput = document.getElementById('editInventoryScheduling');
        const locationField = document.getElementById('editInventoryLocationField');
        const locationInput = document.getElementById('editInventoryLocation');
        schedulingField.classList.toggle('hidden', isEvent);
        schedulingInput.disabled = isEvent;
        locationField.classList.toggle('hidden', !isFacility && !isEvent);
        locationInput.disabled = !isFacility && !isEvent;
        fromField.classList.toggle('hidden', isFacility);
        toField.classList.toggle('hidden', isFacility);
        const pricingBasis = document.getElementById('editInventoryPricingBasis');
        pricingBasis.innerHTML = isEvent
            ? '<option value="Per Person">Per Person</option><option value="Per Hour">Per Hour</option>'
            : '<option value="Per Stay">Per Stay</option><option value="Per Person">Per Person</option><option value="Per Vehicle">Per Vehicle</option><option value="Per Stay + Per Vehicle">Per Stay + Per Vehicle</option><option value="Per Hour">Per Hour</option><option value="Per Day">Per Day</option><option value="Fixed Price">Fixed Price</option>';
        const allowedPricingBasis = isEvent
            ? ['Per Person', 'Per Hour']
            : ['Per Stay', 'Per Person', 'Per Vehicle', 'Per Stay + Per Vehicle', 'Per Hour', 'Per Day', 'Fixed Price'];
        pricingBasis.value = allowedPricingBasis.includes(item.pricing_basis)
            ? item.pricing_basis
            : (isEvent ? 'Per Person' : 'Per Stay');
        const imageField = document.getElementById('editInventoryImageField');
        const imagePreview = document.getElementById('editInventoryImagePreview');
        const noImage = document.getElementById('editInventoryNoImage');
        imageField.classList.toggle('hidden', !isEvent && !isFacility);
        imagePreview.classList.toggle('hidden', (!isEvent && !isFacility) || !item.image);
        noImage.classList.toggle('hidden', (!isEvent && !isFacility) || Boolean(item.image));
        document.getElementById('editInventoryNameLabel').textContent = isFacility ? 'Facility Name' : 'Name';
        document.getElementById('editInventoryCapacityLabel').textContent = isFacility && ['Per Vehicle', 'Per Stay + Per Vehicle'].includes(item.pricing_basis) ? 'Maximum Vehicles' : (isFacility ? 'Capacity / Quantity' : 'Capacity / Maximum Guests');
        document.getElementById('editInventoryStatusLabel').textContent = isFacility ? 'Availability' : 'Status';
        document.getElementById('editInventoryImageLabel').textContent = isFacility ? 'Image (Optional)' : 'Event Photo';
        const statusInput = document.getElementById('editInventoryStatus');
        statusInput.innerHTML = isFacility
            ? '<option value="available">Available</option><option value="unavailable">Unavailable</option>'
            : '<option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option>';
        statusInput.value = item.status || 'available';
        imagePreview.src = item.image ? "{{ asset('storage') }}/" + item.image : '';
        document.getElementById('editInventoryCapacity').value = item.capacity || '';
        document.getElementById('editInventoryLocation').value = item.location || '';
        document.getElementById('editInventoryScheduling').value = item.scheduling_requirement || 'No Additional Schedule';
        document.getElementById('editInventoryQuantity').value = item.quantity || '';
        document.getElementById('editInventoryFrom').value = item.available_from ? item.available_from.substring(0, 5) : '';
        document.getElementById('editInventoryTo').value = item.available_to ? item.available_to.substring(0, 5) : '';
        document.getElementById('editInventoryStatus').value = item.status || 'available';
        document.getElementById('editInventoryDescription').value = item.description || '';
        var route = "{{ route('admin.inventory.update', ['id' => '__ID__']) }}";
        document.getElementById('editInventoryForm').action = route.replace('__ID__', id);
        document.getElementById('editInventoryModal').classList.remove('hidden');
        document.getElementById('editInventoryModal').classList.add('flex');
    }

    function closeEditInventoryModal() {
        document.getElementById('editInventoryModal').classList.add('hidden');
        document.getElementById('editInventoryModal').classList.remove('flex');
    }

    function changeInventoryStatus(id, status, category) {
        var route = "{{ route('admin.inventory.status', ['id' => '__ID__']) }}";
        document.getElementById('inventoryStatusForm').action = route.replace('__ID__', id) + '?category=' + encodeURIComponent(category || 'dining');
        document.getElementById('inventoryStatusSelect').value = status || 'available';
        document.getElementById('inventoryStatusModal').classList.remove('hidden');
        document.getElementById('inventoryStatusModal').classList.add('flex');
    }

    function closeInventoryStatusModal() {
        document.getElementById('inventoryStatusModal').classList.add('hidden');
        document.getElementById('inventoryStatusModal').classList.remove('flex');
    }

    function openFacilityModal() {
        document.getElementById('addFacilityModal').classList.remove('hidden');
        document.getElementById('addFacilityModal').classList.add('flex');
    }

    function closeFacilityModal() {
        document.getElementById('addFacilityModal').classList.add('hidden');
        document.getElementById('addFacilityModal').classList.remove('flex');
        document.getElementById('addFacilityForm').reset();
    }

    function openEventModal() {
        configureEventDurationFields();
        document.getElementById('addEventModal').classList.remove('hidden');
        document.getElementById('addEventModal').classList.add('flex');
    }

    function closeEventModal() {
        document.getElementById('addEventModal').classList.add('hidden');
        document.getElementById('addEventModal').classList.remove('flex');
        document.getElementById('addEventForm').reset();
    }

    function configureEventDurationFields() {
        const pricingBasis = document.getElementById('eventPricingBasis');
        const durationInput = document.getElementById('eventDurationHours');
        const durationLabel = document.getElementById('eventDurationLabel');
        if (!pricingBasis || !durationInput || !durationLabel) return;

        const isPerHour = pricingBasis.value === 'Per Hour';
        durationLabel.textContent = isPerHour ? 'Maximum Duration (hours)' : 'Fixed Duration (hours)';
        durationInput.readOnly = false;
    }

    function configureEventAvailabilityTimes() {
        const from = document.getElementById('eventAvailableFrom');
        const to = document.getElementById('eventAvailableTo');
        if (!from || !to) return;

        const fromMinutes = Number(from.value.slice(0, 2)) * 60;
        to.querySelectorAll('option').forEach(option => {
            option.disabled = Number(option.value.slice(0, 2)) * 60 <= fromMinutes;
        });
        if (to.selectedOptions[0]?.disabled) {
            const firstAvailable = [...to.options].find(option => !option.disabled);
            to.value = firstAvailable ? firstAvailable.value : '';
        }
    }

    function openDiningModal() {
        document.getElementById('addDiningModal').classList.remove('hidden');
        document.getElementById('addDiningModal').classList.add('flex');
    }

    function closeDiningModal() {
        document.getElementById('addDiningModal').classList.add('hidden');
        document.getElementById('addDiningModal').classList.remove('flex');
        document.getElementById('addDiningForm').reset();
    }

    function closeEditDiningModal() {
        document.getElementById('editDiningModal').classList.add('hidden');
        document.getElementById('editDiningModal').classList.remove('flex');
    }

    function configureDiningFieldLayout(type) {
        const fields = ['diningNameField', 'diningMenuCategoryField', 'diningTypeField', 'diningCapacityField', 'diningLocationField', 'diningPriceField', 'diningImageField', 'diningScheduleTimeField', 'diningMaxGuestsField', 'diningStatusField'];
        fields.forEach(function (id) {
            const field = document.getElementById(id);
            if (field) {
                field.style.order = '';
                field.style.gridColumn = '';
            }
        });

        const layout = type === 'tables'
            ? { diningNameField: 1, diningStatusField: 2, diningTypeField: 3, diningCapacityField: 4, diningLocationField: 5 }
            : type === 'schedules'
                ? { diningNameField: 1, diningStatusField: 2, diningScheduleTimeField: 3, diningMaxGuestsField: 4 }
                : { diningNameField: 1, diningStatusField: 2, diningMenuCategoryField: 3, diningPriceField: 4, diningImageField: 5 };

        Object.keys(layout).forEach(function (id) {
            const field = document.getElementById(id);
            if (field) field.style.order = layout[id];
        });

        const fullWidthField = document.getElementById(type === 'menus' ? 'diningImageField' : 'diningLocationField');
        if (fullWidthField) fullWidthField.style.gridColumn = '1 / -1';
    }

    function openDiningCreateModal(kind) {
        openDiningModal();
        const form = document.getElementById('addDiningForm');
        const category = form.querySelector('[name="menu_category"]');
        const type = kind === 'table' ? 'tables' : kind === 'schedule' ? 'schedules' : 'menus';
        const isTable = type === 'tables';
        const isSchedule = type === 'schedules';
        const sectionTitle = isTable ? 'Table / Seating' : isSchedule ? 'Dining Schedule' : 'Menu / Meal';
        configureDiningFieldLayout(type);
        document.getElementById('addDiningTitle').textContent = 'Add ' + sectionTitle;
        document.getElementById('addDiningSubmit').textContent = 'Save ' + sectionTitle;
        document.getElementById('diningFormType').value = type;
        document.getElementById('diningNameLabel').firstChild.textContent = isTable ? 'Table No. ' : isSchedule ? 'Meal Period ' : 'Meal Name ';
        form.querySelector('[name="name"]').placeholder = isTable ? 'e.g. T01' : isSchedule ? 'e.g. Breakfast' : 'e.g. Grilled Salmon';
        document.getElementById('diningMenuCategoryField').classList.toggle('hidden', isTable || isSchedule);
        document.getElementById('diningTypeField').classList.toggle('hidden', !isTable);
        document.getElementById('diningCapacityField').classList.toggle('hidden', !isTable);
        document.getElementById('diningLocationField').classList.toggle('hidden', !isTable);
        document.getElementById('diningPriceField').classList.toggle('hidden', isTable || isSchedule);
        document.getElementById('diningImageField').classList.toggle('hidden', isTable || isSchedule);
        document.getElementById('diningScheduleTimeField').classList.toggle('hidden', !isSchedule);
        document.getElementById('diningMaxGuestsField').classList.toggle('hidden', !isSchedule);
        form.querySelector('[name="price"]').required = !isTable && !isSchedule;
        form.querySelector('[name="capacity"]').required = isTable;
        form.querySelector('[name="available_from"]').required = isSchedule;
        form.querySelector('[name="available_to"]').required = isSchedule;
        if (category) category.value = 'Dinner';
    }

    function editDiningRow(button) {
        const row = button.closest('tr');
        const cells = row.querySelectorAll('td');
        const panel = row.closest('[data-dining-subpanel]');
        const section = row.dataset.diningType || (panel ? panel.getAttribute('data-dining-subpanel') : 'tables');
        const isTable = section === 'tables';
        const isSchedule = section === 'schedule';
        const statusIndex = isSchedule ? 4 : 5;
        const status = cells[statusIndex].textContent.trim();

        document.getElementById('editDiningTitle').textContent = isTable ? 'Edit Table / Seating' : isSchedule ? 'Edit Dining Schedule' : 'Edit Menu / Meal';
        document.getElementById('editDiningNameLabel').textContent = isTable ? 'Table Number' : isSchedule ? 'Meal Period' : 'Meal Name';
        document.getElementById('editDiningTypeLabel').textContent = isTable ? 'Table Type' : isSchedule ? 'Schedule Type' : 'Category';
        document.getElementById('editDiningValueLabel').textContent = isTable ? 'Capacity' : isSchedule ? 'Maximum Guests' : 'Price (₱)';
        document.getElementById('editDiningDetailLabel').textContent = isTable ? 'Location' : 'Available Time';
        document.getElementById('editDiningName').value = cells[1].textContent.trim();
        document.getElementById('editDiningType').value = cells[2].textContent.trim();
        document.getElementById('editDiningValue').value = cells[3].textContent.replace('₱', '').trim();
        document.getElementById('editDiningDetail').value = cells[isSchedule ? 2 : 4].textContent.trim();
        document.getElementById('editDiningStatus').value = status;
        const menuCategory = document.getElementById('editDiningMenuCategory');
        const typeInput = document.getElementById('editDiningType');
        menuCategory.classList.toggle('hidden', !(!isTable && !isSchedule));
        typeInput.classList.toggle('hidden', !isTable && !isSchedule);
        typeInput.required = isTable || isSchedule;
        menuCategory.required = !isTable && !isSchedule;
        if (!isTable && !isSchedule) {
            menuCategory.value = cells[2].textContent.trim();
        }
        const menuTime = document.getElementById('editDiningMenuTime');
        const detailInput = document.getElementById('editDiningDetail');
        menuTime.classList.toggle('hidden', !(!isTable && !isSchedule));
        detailInput.classList.toggle('hidden', !isTable && !isSchedule);
        detailInput.required = isTable || isSchedule;
        menuTime.required = !isTable && !isSchedule;
        if (!isTable && !isSchedule) {
            const currentTime = cells[4].textContent.trim().toLowerCase();
            const timeOptions = {
                '7:00 am - 10:00 am': 'breakfast',
                '11:00 am - 2:00 pm': 'lunch',
                '2:00 pm - 5:00 pm': 'afternoon-snack',
                '6:00 pm - 9:00 pm': 'dinner',
                'any time': 'all-day',
            };
            menuTime.value = timeOptions[currentTime] || 'all-day';
        }
        const menuDetails = document.getElementById('editDiningMenuDetails');
        const imagePreview = document.getElementById('editDiningImagePreview');
        menuDetails.classList.toggle('hidden', isTable || isSchedule);
        document.getElementById('editDiningDescription').value = row.dataset.menuDescription || '';
        imagePreview.src = row.dataset.menuImage ? "{{ asset('storage') }}/" + row.dataset.menuImage : '';
        imagePreview.classList.toggle('hidden', !row.dataset.menuImage || isTable || isSchedule);
        window.editingDiningRow = row;
        window.editingDiningId = row.querySelector('input[type="checkbox"]').value;
        window.editingDiningType = section;
        document.getElementById('editDiningForm').dataset.section = section;
        document.getElementById('editDiningForm').dataset.table = isTable ? 'true' : 'false';
        document.getElementById('editDiningForm').dataset.schedule = isSchedule ? 'true' : 'false';
        document.getElementById('editDiningModal').classList.remove('hidden');
        document.getElementById('editDiningModal').classList.add('flex');
    }

    async function saveDiningEdit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const row = window.editingDiningRow;
        if (!row) return;

        const cells = row.querySelectorAll('td');
        const value = document.getElementById('editDiningValue').value.trim();
        const isTable = form.dataset.table === 'true';
        const isSchedule = form.dataset.schedule === 'true';
        const status = document.getElementById('editDiningStatus').value;
        const isMenu = !isTable && !isSchedule;
        const statusRoute = "{{ route('admin.dining.status', ['type' => '__TYPE__', 'id' => '__ID__']) }}"
            .replace('__TYPE__', window.editingDiningType)
            .replace('__ID__', window.editingDiningId);

        try {
            if (isMenu) {
                const updateRoute = "{{ route('admin.inventory.update', ['id' => '__ID__']) }}".replace('__ID__', window.editingDiningId);
                const updateData = new FormData();
                updateData.append('_token', document.querySelector('#editDiningForm input[name="_token"]').value);
                updateData.append('_method', 'PUT');
                updateData.append('category', 'dining');
                updateData.append('name', document.getElementById('editDiningName').value.trim());
                updateData.append('menu_category', document.getElementById('editDiningMenuCategory').value);
                updateData.append('price', value);
                updateData.append('status', status);
                updateData.append('description', document.getElementById('editDiningDescription').value.trim());
                const menuTimeRanges = {
                    breakfast: ['07:00', '10:00'],
                    lunch: ['11:00', '14:00'],
                    'afternoon-snack': ['14:00', '17:00'],
                    dinner: ['18:00', '21:00'],
                    'all-day': ['', ''],
                };
                const [availableFrom, availableTo] = menuTimeRanges[document.getElementById('editDiningMenuTime').value];
                updateData.append('available_from', availableFrom);
                updateData.append('available_to', availableTo);
                const image = document.getElementById('editDiningImage').files[0];
                if (image) updateData.append('image', image);

                const updateResponse = await fetch(updateRoute, { method: 'POST', body: updateData, headers: { 'Accept': 'application/json' } });
                if (!updateResponse.ok) {
                    const errorPayload = await updateResponse.json().catch(() => ({}));
                    const errorMessage = errorPayload.message || Object.values(errorPayload.errors || {}).flat()[0];
                    throw new Error(errorMessage || 'Menu update failed');
                }
                window.location.reload();
                return;
            }

            const response = await fetch(statusRoute, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status })
            });

            if (!response.ok) throw new Error('Status update failed');
        } catch (error) {
            alert(error.message || (isMenu ? 'Unable to update the menu item. Please check the details and try again.' : 'Unable to update the dining status. Please try again.'));
            return;
        }

        cells[1].textContent = document.getElementById('editDiningName').value.trim();
        cells[2].textContent = document.getElementById('editDiningType').value.trim();
        cells[3].textContent = isTable || isSchedule ? value : '₱' + value;
        cells[isSchedule ? 2 : 4].textContent = document.getElementById('editDiningDetail').value.trim();
        const statusElement = cells[isSchedule ? 4 : 5].querySelector('span');
        statusElement.textContent = status;
        setDiningStatusColor(statusElement, status);
        closeEditDiningModal();
    }

    function deleteDiningRow(button) {
        const row = button.closest('tr');
        const itemName = row.querySelector('td')?.textContent.trim() || 'this item';

        if (confirm('Delete ' + itemName + '?')) {
            row.remove();
        }
    }

    function setDiningStatusColor(statusElement, status) {
        const statusClass = 'status-' + String(status).toLowerCase().replace(/\s+/g, '-');
        statusElement.className = 'rounded-full px-3 py-1 text-xs font-medium text-white ' + statusClass;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('addRoomModal');
        const editModal = document.getElementById('editRoomModal');
        const statusModal = document.getElementById('statusModal');
        const facilityModal = document.getElementById('addFacilityModal');
        const eventModal = document.getElementById('addEventModal');
        const diningModal = document.getElementById('addDiningModal');
        const editDiningModal = document.getElementById('editDiningModal');
        const editInventoryModal = document.getElementById('editInventoryModal');
        const inventoryStatusModal = document.getElementById('inventoryStatusModal');

        document.querySelectorAll('.room-bed-type').forEach(toggleCustomBedType);
        document.querySelectorAll('#addRoomModal form, #editRoomForm').forEach(function (form) {
            form.addEventListener('submit', function () {
                applyCustomBedType(form);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddRoomModal();
                closeEditRoomModal();
                closeStatusModal();
                closeFacilityModal();
                closeEventModal();
                closeDiningModal();
                closeEditDiningModal();
                closeEditInventoryModal();
                closeInventoryStatusModal();
            }
        });

        const tabButtons = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('[data-panel]');
        const addButtons = document.querySelectorAll('.add-panel-button');
        const diningSubTabs = document.querySelectorAll('.dining-subtab');
        const diningSubPanels = document.querySelectorAll('[data-dining-subpanel]');
        const diningMenuCategoryTabs = document.querySelectorAll('[data-menu-category-tab]');
        const diningPanel = document.querySelector('[data-panel="dining"]');
        const roomFilterPanel = document.getElementById('roomFilterPanel');
        const roomSearch = document.getElementById('roomSearch');
        const roomTypeFilter = document.getElementById('roomTypeFilter');

        function filterRooms() {
            const searchTerm = (roomSearch?.value || '').trim().toLowerCase();
            const roomType = roomTypeFilter?.value || '';

            document.querySelectorAll('[data-panel="rooms"] .room-row').forEach(function (row) {
                const matchesSearch = !searchTerm || (row.dataset.roomSearch || '').includes(searchTerm);
                const matchesType = !roomType || row.dataset.roomType === roomType;
                row.style.display = matchesSearch && matchesType ? '' : 'none';
            });
        }

        function applyRoomFilters() {
            const params = new URLSearchParams(window.location.search);
            const searchTerm = roomSearch?.value.trim() || '';
            const roomType = roomTypeFilter?.value || '';

            params.set('tab', 'rooms');
            params.delete('rooms_page');
            searchTerm ? params.set('room_search', searchTerm) : params.delete('room_search');
            roomType ? params.set('room_type', roomType) : params.delete('room_type');
            window.location.search = params.toString();
        }

        roomSearch?.addEventListener('input', filterRooms);
        roomSearch?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyRoomFilters();
            }
        });
        roomTypeFilter?.addEventListener('change', applyRoomFilters);

        if (diningPanel) {
            diningPanel.querySelectorAll('[data-dining-subpanel] td:nth-last-child(2) span').forEach(function (statusElement) {
                setDiningStatusColor(statusElement, statusElement.textContent.trim());
            });

            diningPanel.querySelectorAll('[data-dining-subpanel] td:last-child > div').forEach(function (actions) {
                const statusButton = document.createElement('button');
                statusButton.type = 'button';
                statusButton.dataset.diningStatusId = actions.closest('tr').querySelector('input[type="checkbox"]').value;
                statusButton.dataset.diningStatusType = actions.closest('[data-dining-subpanel]').getAttribute('data-dining-subpanel');
                statusButton.className = 'inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700 transition hover:bg-emerald-100';
                statusButton.setAttribute('aria-label', 'Change dining status');
                statusButton.innerHTML = '<i class="fas fa-exchange-alt"></i>';
                actions.insertBefore(statusButton, actions.querySelector('.fa-trash')?.closest('button'));
            });
        }

        function activateDiningSubTab(targetName) {
            diningSubTabs.forEach(function(btn) {
                const isActive = btn.getAttribute('data-dining-subtab') === targetName;
                btn.classList.toggle('text-orange-600', isActive);
                btn.classList.toggle('bg-white', isActive);
                btn.classList.toggle('text-gray-600', !isActive);
                btn.classList.toggle('border-transparent', !isActive);
                btn.classList.toggle('border-orange-200', isActive);
            });
            diningSubPanels.forEach(function(panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-dining-subpanel') !== targetName);
            });
        }

        const diningPageSize = 5;
        const diningPageState = { tables: 1, menus: 1, schedules: 1 };

        function paginateDiningRows(type, category = null) {
            const panel = document.querySelector(`[data-dining-subpanel="${type === 'menus' ? 'menu' : type}"]`);
            if (!panel) return;

            const rowSelector = type === 'tables' ? '.dining-table-row' : type === 'menus' ? '.dining-menu-row' : '.dining-schedule-row';
            const allRows = Array.from(panel.querySelectorAll(rowSelector));
            const rows = type === 'menus' && category
                ? allRows.filter((row) => row.dataset.menuCategory === category)
                : allRows;
            const pageCount = Math.max(1, Math.ceil(rows.length / diningPageSize));
            diningPageState[type] = Math.min(diningPageState[type], pageCount);
            const page = diningPageState[type];
            const visibleRows = new Set(rows.slice((page - 1) * diningPageSize, page * diningPageSize));

            allRows.forEach((row) => row.classList.toggle('hidden', !visibleRows.has(row)));
            const emptyRow = panel.querySelector(type === 'menus' ? '.dining-menu-empty' : 'tbody tr:not([class*="dining-"])');
            if (emptyRow) emptyRow.classList.toggle('hidden', rows.length > 0);

            const pagination = panel.querySelector(`[data-dining-pagination="${type}"]`);
            if (!pagination) return;
            pagination.innerHTML = '';
            if (rows.length <= diningPageSize) return;

            const summary = document.createElement('span');
            summary.textContent = `Page ${page} of ${pageCount}`;
            const controls = document.createElement('div');
            controls.className = 'flex items-center gap-2';
            ['Previous', 'Next'].forEach((label) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = 'rounded-lg border border-orange-200 px-3 py-1.5 text-orange-600 disabled:cursor-not-allowed disabled:opacity-40';
                button.disabled = label === 'Previous' ? page === 1 : page === pageCount;
                button.addEventListener('click', () => {
                    diningPageState[type] += label === 'Previous' ? -1 : 1;
                    paginateDiningRows(type, category);
                });
                controls.appendChild(button);
            });
            pagination.append(summary, controls);
        }

        function filterDiningMenu(category) {
            const menuPanel = document.querySelector('[data-dining-subpanel="menu"]');
            if (!menuPanel) return;

            diningPageState.menus = 1;
            const visibleRows = menuPanel.querySelectorAll(`.dining-menu-row[data-menu-category="${category}"]`).length;

            const emptyRow = menuPanel.querySelector('.dining-menu-empty');
            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visibleRows > 0);
                emptyRow.querySelector('td').textContent = visibleRows > 0 ? '' : 'No menu items found in this category.';
            }
            paginateDiningRows('menus', category);
        }

        function normalizeDiningMenuCategory(category) {
            const value = (category || '').trim().toLowerCase();
            const categoryMap = {
                breakfast: 'Breakfast',
                appetizer: 'Appetizer',
                appetisers: 'Appetizer',
                appetizers: 'Appetizer',
                'main course': 'Main Course',
                'main-course': 'Main Course',
                maincourse: 'Main Course',
                lunch: 'Main Course',
                dinner: 'Main Course',
                'afternoon snacks': 'Appetizer',
                'afternoon-snacks': 'Appetizer',
                snack: 'Appetizer',
                snacks: 'Appetizer',
                soup: 'Soup',
                salad: 'Salad',
                dessert: 'Dessert',
                beverage: 'Beverage',
                beverages: 'Beverage',
                drinks: 'Beverage',
                drink: 'Beverage'
            };

            return categoryMap[value] || 'Breakfast';
        }

        function activateTab(targetName) {
            tabButtons.forEach(function(btn) {
                const isActive = btn.getAttribute('data-tab') === targetName;
                btn.classList.toggle('bg-orange-500', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);
            });
            panels.forEach(function(panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== targetName);
            });
            roomFilterPanel?.classList.toggle('hidden', targetName !== 'rooms');
            addButtons.forEach(function(button) {
                const shouldShow =
                    (targetName === 'rooms' && button.id === 'add-room-button') ||
                    (targetName === 'facilities' && button.id === 'add-facilities-button') ||
                    (targetName === 'events' && button.id === 'add-event-button');
                button.classList.toggle('hidden', !shouldShow);
            });
            if (targetName === 'dining') {
                activateDiningSubTab('tables');
            }
        }

        tabButtons.forEach(function(button) {
            button.addEventListener('click', function () {
                activateTab(this.getAttribute('data-tab'));
            });
        });

        diningSubTabs.forEach(function(button) {
            button.addEventListener('click', function () {
                activateDiningSubTab(this.getAttribute('data-dining-subtab'));
            });
        });

        diningMenuCategoryTabs.forEach(function (button) {
            button.addEventListener('click', function () {
                const category = this.getAttribute('data-menu-category-tab');

                diningMenuCategoryTabs.forEach(function (tab) {
                    const isActive = tab === button;
                    tab.classList.toggle('is-active', isActive);
                    tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                document.querySelectorAll('.dining-menu-checkbox').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
                const menuSelectAll = document.querySelector('.dining-menu-select-all');
                if (menuSelectAll) menuSelectAll.checked = false;
                updateDiningSelectCount('menus');

                filterDiningMenu(category);
            });
        });

        if (diningMenuCategoryTabs.length) {
            document.querySelectorAll('[data-dining-subpanel="menu"] .dining-menu-row').forEach(function (row) {
                row.dataset.menuCategory = normalizeDiningMenuCategory(row.dataset.menuCategory);
            });
            filterDiningMenu(diningMenuCategoryTabs[0].getAttribute('data-menu-category-tab'));
        }

        paginateDiningRows('tables');
        paginateDiningRows('schedules');

        if (diningPanel) {
            diningPanel.addEventListener('click', function (event) {
                const button = event.target.closest('button');
                if (!button) return;

                const addType = button.getAttribute('data-dining-add');
                if (addType) {
                    openDiningCreateModal(addType);
                    return;
                }

                if (button.querySelector('.fa-edit')) {
                    editDiningRow(button);
                    return;
                }

                if (button.querySelector('.fa-exchange-alt')) {
                    editDiningRow(button);
                    return;
                }

                if (button.getAttribute('onclick')?.startsWith('confirmBulkDiningDelete')) {
                    return;
                }

                if (button.querySelector('.fa-trash')) {
                    deleteDiningRow(button);
                }
            });
        }

        document.getElementById('editDiningForm').addEventListener('submit', saveDiningEdit);

        document.getElementById('add-facilities-button').addEventListener('click', openFacilityModal);
        document.getElementById('add-event-button').addEventListener('click', openEventModal);
        document.getElementById('eventPricingBasis').addEventListener('change', configureEventDurationFields);
        document.getElementById('eventAvailableFrom').addEventListener('change', configureEventAvailabilityTimes);
        configureEventAvailabilityTimes();

        updateSelectedCount();
        activateTab(@json($activeTab));

        @if($errors->any())
            @if(old('category'))
                activateTab(@json(old('category') === 'event' ? 'events' : old('category')));
                @if(old('category') === 'facilities') openFacilityModal(); @elseif(old('category') === 'event') openEventModal(); @elseif(old('category') === 'dining') openDiningModal(); @endif
            @else
                openAddRoomModal();
            @endif
        @endif
    });
</script>
@endsection
