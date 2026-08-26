@extends('admin.layout')

@section('content')
<style>
    .room-management-page {
        font-size: 16px;
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .room-management-page .room-table-shell {
        min-width: 980px;
    }

    .room-management-page .room-table-shell table {
        width: 100%;
        min-width: 980px;
    }

    .room-management-page .dining-table-scroll {
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
    .room-management-page [data-panel="amenities"] button[onclick^="confirmBulkInventoryDelete"],
    .room-management-page [data-panel="event-place"] button[onclick^="confirmBulkInventoryDelete"] {
        display: none;
    }

    @media (max-width: 640px) {
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
            <button id="add-amenities-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Amenities
            </button>
            <button id="add-event-place-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Event Place
            </button>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-2 sm:gap-4">
        <button type="button" data-tab="rooms" class="tab-button rounded-lg bg-orange-500 px-6 py-3 font-medium text-white transition hover:bg-orange-600">ROOMS</button>
        <button type="button" data-tab="amenities" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">AMENITIES</button>
        <button type="button" data-tab="event-place" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">EVENT PLACE</button>
        <button type="button" data-tab="dining" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">DINING</button>
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
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <input type="checkbox" class="room-checkbox h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" value="{{ $room->id }}" onclick="updateSelectAllCheckbox()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->room_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($room->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->floor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->capacity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="rounded-full px-3 py-1 text-xs font-medium text-white bg-emerald-600">{{ ucfirst($room->status) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type='button' onclick='editRoom({{ $room->id }}, @json($room->room_number), @json($room->room_type), {{ $room->price }}, @json($room->floor), {{ $room->capacity }}, @json($room->status))' class='inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700 transition hover:bg-blue-100' aria-label='Edit room'>
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

    <div data-panel="amenities" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div></div>
            <div class="flex items-center gap-3"><span id="amenitiesSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkInventoryDelete('amenities')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected amenities"><i class="fas fa-trash"></i></button></div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"><input id="selectAllAmenitiesHeader" type="checkbox" class="inventory-select-all h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" data-category="amenities" onclick="toggleAllInventoryCheckboxes('amenities', this)"></th><th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="amenities-list" class="divide-y divide-gray-200">
                    @foreach($amenities as $item)
                        <tr><td class="px-6 py-4 text-sm"><input type="checkbox" class="inventory-checkbox inventory-checkbox-amenities h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $item->id }}" onclick="updateInventorySelectAll('amenities')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">—</td><td class="px-6 py-4 text-sm"><span class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm"><div class="flex items-center gap-2"><button type='button' onclick='editInventory({{ $item->id }}, @json($item), "amenities")' class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700" aria-label="Edit amenity"><i class="fas fa-edit"></i></button><button type='button' onclick='changeInventoryStatus({{ $item->id }}, @json($item->status), "amenities")' class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700" aria-label="Change amenity status"><i class="fas fa-exchange-alt"></i></button><form action="{{ route('admin.inventory.destroy', $item->id) }}?category=amenities" method="POST" onsubmit="return confirm('Delete this amenity?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-700" aria-label="Delete amenity"><i class="fas fa-trash"></i></button></form></div></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $amenities->links('pagination.admin-rooms') }}
    </div>

    <div data-panel="event-place" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4"><div></div><div class="flex items-center gap-3"><span id="event_placeSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkInventoryDelete('event_place')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white" aria-label="Delete selected event places"><i class="fas fa-trash"></i></button></div></div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"><input id="selectAllEventPlaceHeader" type="checkbox" class="inventory-select-all h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" data-category="event_place" onclick="toggleAllInventoryCheckboxes('event_place', this)"></th><th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="event-place-list" class="divide-y divide-gray-200">
                    @foreach($eventPlaces as $item)
                        <tr><td class="px-6 py-4 text-sm"><input type="checkbox" class="inventory-checkbox inventory-checkbox-event_place h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $item->id }}" onclick="updateInventorySelectAll('event_place')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm"><div class="flex items-center gap-2"><button type='button' onclick='editInventory({{ $item->id }}, @json($item), "event_place")' class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-blue-700" aria-label="Edit event place"><i class="fas fa-edit"></i></button><button type='button' onclick='changeInventoryStatus({{ $item->id }}, @json($item->status), "event_place")' class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-700" aria-label="Change event place status"><i class="fas fa-exchange-alt"></i></button><form action="{{ route('admin.inventory.destroy', $item->id) }}?category=event_place" method="POST" onsubmit="return confirm('Delete this event place?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-red-700" aria-label="Delete event place"><i class="fas fa-trash"></i></button></form></div></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $eventPlaces->links('pagination.admin-rooms') }}
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
                                    <tr class="bg-white"><td class="px-4 py-3 text-sm"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $menu->id }}"></td><td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $menu->name }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $menu->category ?: 'Menu / Meal' }}</td><td class="px-4 py-3 text-sm text-gray-700">₱{{ number_format((float) $menu->price, 2) }}</td><td class="px-4 py-3 text-sm text-gray-700">{{ $menu->available_from && $menu->available_to ? \Illuminate\Support\Carbon::parse($menu->available_from)->format('g:i A') . ' - ' . \Illuminate\Support\Carbon::parse($menu->available_to)->format('g:i A') : 'Any time' }}</td><td class="px-4 py-3"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium {{ strtolower($menu->status) === 'available' ? 'border-green-200 bg-green-100 text-green-700' : 'border-red-200 bg-red-100 text-red-700' }}">{{ ucfirst($menu->status) }}</span></td><td class="px-4 py-3 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
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
                                <tr class="bg-white"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-table-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $table->id }}" onclick="updateDiningSelectCount('tables')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $table->table_no }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->type }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->capacity }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $table->location ?: '—' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium">{{ ucfirst($table->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-6 !text-center text-sm text-gray-500">No tables found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div data-dining-subpanel="menu" class="hidden p-5 lg:p-6">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="dining-card-header flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-5 py-2"><h3 class="text-xl font-semibold text-gray-800">Menu / Meals</h3><div class="flex items-center gap-3"><span id="diningMenusSelectedCount" class="text-sm font-medium text-gray-500">0 selected</span><button type="button" onclick="confirmBulkDiningDelete('menus')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700" aria-label="Delete selected menus"><i class="fas fa-trash"></i></button><button type="button" data-dining-add="menu" class="dining-add-button inline-flex items-center justify-center rounded-lg border border-orange-300 bg-white px-3 py-2 text-sm font-medium text-orange-600 transition hover:bg-orange-50"><i class="fas fa-plus mr-2"></i>Add Menu</button></div></div>
                <div class="dining-table-scroll">
                    <table class="min-w-full text-left">
                        <thead class="bg-gray-50"><tr><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500"><input type="checkbox" class="dining-menu-select-all h-4 w-4 rounded border-gray-300 text-orange-600" onclick="toggleAllDiningCheckboxes('menus', this)"></th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Meal Name</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Category</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Price</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Available Time</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Status</th><th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-500">Action</th></tr></thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($dining as $menu)
                                <tr class="bg-white"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-menu-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $menu->id }}" onclick="updateDiningSelectCount('menus')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $menu->name }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $menu->category ?: 'Menu / Meal' }}</td><td class="px-6 py-4 text-sm text-gray-700">₱{{ number_format((float) $menu->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $menu->available_from && $menu->available_to ? \Illuminate\Support\Carbon::parse($menu->available_from)->format('g:i A') . ' - ' . \Illuminate\Support\Carbon::parse($menu->available_to)->format('g:i A') : 'Any time' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium {{ strtolower($menu->status) === 'available' ? 'border-green-200 bg-green-100 text-green-700' : 'border-red-200 bg-red-100 text-red-700' }}">{{ ucfirst($menu->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-6 !text-center text-sm text-gray-500">No menu items found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                                <tr class="bg-white"><td class="px-6 py-4 text-sm"><input type="checkbox" class="dining-schedule-checkbox h-4 w-4 rounded border-gray-300 text-orange-600" value="{{ $schedule->id }}" onclick="updateDiningSelectCount('schedules')"></td><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule->period }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ \Illuminate\Support\Carbon::parse($schedule->available_from)->format('g:i A') }} - {{ \Illuminate\Support\Carbon::parse($schedule->available_to)->format('g:i A') }}</td><td class="px-6 py-4 text-sm text-gray-700">{{ $schedule->max_guests ?: '—' }}</td><td class="px-6 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium">{{ ucfirst($schedule->status) }}</span></td><td class="px-6 py-4 text-right"><div class="flex items-center justify-end gap-2"><button type="button" class="rounded-md border border-blue-200 bg-blue-50 p-2 text-blue-700"><i class="fas fa-edit text-xs"></i></button><button type="button" class="rounded-md border border-red-200 bg-red-50 p-2 text-red-700"><i class="fas fa-trash text-xs"></i></button></div></td></tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-6 !text-center text-sm text-gray-500">No dining schedules found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
        <form id="editInventoryForm" action="" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="category" id="editInventoryCategory">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Name</label><input id="editInventoryName" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Type</label><input id="editInventoryType" name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label><input id="editInventoryPrice" name="price" type="number" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Location / Category</label><input id="editInventoryLocation" name="location" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Capacity</label><input id="editInventoryCapacity" name="capacity" type="number" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Quantity</label><input id="editInventoryQuantity" name="quantity" type="number" min="0" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Available From</label><input id="editInventoryFrom" name="available_from" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Available To</label><input id="editInventoryTo" name="available_to" type="time" class="w-full rounded-lg border border-gray-300 px-3 py-2"></div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">Status</label><select id="editInventoryStatus" name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option></select></div>
            </div>
            <div><label class="mb-1 block text-sm font-medium text-gray-700">Description</label><textarea id="editInventoryDescription" name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea></div>
            <div class="flex justify-end gap-3"><button type="button" onclick="closeEditInventoryModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Update Item</button></div>
        </form>
    </div>
</div>

<div id="inventoryStatusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl"><button type="button" onclick="closeInventoryStatusModal()" class="absolute right-4 top-4 text-gray-500"><i class="fas fa-times text-xl"></i></button><h3 class="mb-4 text-2xl font-bold text-gray-800">Change Inventory Status</h3><form id="inventoryStatusForm" action="" method="POST" class="space-y-4">@csrf @method('PATCH')<select name="status" id="inventoryStatusSelect" required class="w-full rounded-lg border border-gray-300 px-3 py-2"><option value="available">Available</option><option value="limited">Limited</option><option value="unavailable">Unavailable</option></select><div class="flex justify-end gap-3"><button type="button" onclick="closeInventoryStatusModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Update Status</button></div></form></div>
</div>

<div id="addAmenityModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAmenityModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Amenity</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new amenity option for guests.</p>
        </div>
        @if($errors->any() && old('category') === 'amenities')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('name') }}</div>
        @endif

        <form id="addAmenityForm" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="amenities">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Amenity Name</label>
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
                    <label class="mb-1 block text-sm font-medium text-gray-700">Availability</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="available">Available</option>
                        <option value="limited">Limited</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeAmenityModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Amenity</button>
            </div>
        </form>
    </div>
</div>

<div id="addEventPlaceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="admin-modal-panel relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEventPlaceModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Event Place</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new event package or venue option.</p>
        </div>
        @if($errors->any() && old('category') === 'event_place')
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first('name') }}</div>
        @endif

        <form id="addEventPlaceForm" method="POST" action="{{ route('admin.inventory.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="event_place">
            <input type="hidden" name="status" value="available">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Name</label>
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
                    <label class="mb-1 block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" min="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEventPlaceModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Event Place</button>
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
                            <option value="Dinner">Dinner</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Dessert">Dessert</option>
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label id="editDiningNameLabel" for="editDiningName" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                    <input id="editDiningName" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label id="editDiningTypeLabel" for="editDiningType" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                    <input id="editDiningType" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label id="editDiningValueLabel" for="editDiningValue" class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input id="editDiningValue" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label id="editDiningDetailLabel" for="editDiningDetail" class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <input id="editDiningDetail" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Floor</label>
                    <input type="text" name="floor" value="{{ old('floor') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" id="editPrice" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Floor</label>
                    <input type="text" name="floor" id="editFloor" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
                <label class="mb-1 block text-sm font-medium text-gray-700">Room Image (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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

    function editRoom(id, roomNumber, roomType, price, floor, capacity, status) {
        document.getElementById('editRoomId').value = id;
        document.getElementById('editRoomNumber').value = roomNumber;
        document.getElementById('editRoomType').value = roomType;
        document.getElementById('editPrice').value = price;
        document.getElementById('editFloor').value = floor;
        document.getElementById('editCapacity').value = capacity;
        document.getElementById('editStatus').value = status;
        var editRoute = "{{ route('admin.rooms.update', ['id' => '__ID__']) }}";
        document.getElementById('editRoomForm').action = editRoute.replace('__ID__', id);
        document.getElementById('editRoomModal').classList.remove('hidden');
        document.getElementById('editRoomModal').classList.add('flex');
    }

    function closeEditRoomModal() {
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
            label.textContent = count + (count === 1 ? ' selected' : ' selected');
        }
        const selectAll = document.getElementById('selectAllRoomsHeader');
        const deleteButton = document.querySelector('[data-panel="rooms"] button[onclick="confirmBulkDelete()"]');
        if (deleteButton) deleteButton.style.display = selectAll && selectAll.checked ? 'inline-flex' : 'none';
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
        const allChecked = Array.from(checkboxes).every(function (checkbox) {
            return checkbox.checked;
        });
        const selectAll = document.getElementById('selectAllRooms');
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
        const panelName = category === 'event_place' ? 'event-place' : category;
        const deleteButton = document.querySelector('[data-panel="' + panelName + '"] button[onclick^="confirmBulkInventoryDelete"]');
        const selectAll = document.querySelector('.inventory-select-all[data-category="' + category + '"]');
        if (deleteButton) deleteButton.style.display = selectAll && selectAll.checked ? 'inline-flex' : 'none';
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

    function updateDiningSelectCount(type) {
        const selector = diningCheckboxSelector(type);
        const count = document.querySelectorAll(selector + ':checked').length;
        const label = document.getElementById('dining' + type.charAt(0).toUpperCase() + type.slice(1) + 'SelectedCount');
        if (label) label.textContent = count + ' selected';

        const panelName = diningPanelName(type);
        const selectAll = document.querySelector('.' + diningSelectAllClass(type));
        if (selectAll) {
            selectAll.checked = count > 0 && count === document.querySelectorAll(selector).length;
            const deleteButton = document.querySelector('[data-dining-subpanel="' + panelName + '"] button[onclick^="confirmBulkDiningDelete"]');
            if (deleteButton) deleteButton.style.display = selectAll.checked ? 'inline-flex' : 'none';
        }
    }

    function toggleAllDiningCheckboxes(type, source) {
        document.querySelectorAll(diningCheckboxSelector(type)).forEach(function (checkbox) {
            checkbox.checked = source.checked;
        });
        updateDiningSelectCount(type);
    }

    function confirmBulkDiningDelete(type) {
        const selectedIds = Array.from(document.querySelectorAll(diningCheckboxSelector(type) + ':checked')).map(function (checkbox) {
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
        document.getElementById('editInventoryType').value = item.type || '';
        document.getElementById('editInventoryPrice').value = item.price || 0;
        document.getElementById('editInventoryLocation').value = item.location || '';
        document.getElementById('editInventoryCapacity').value = item.capacity || '';
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

    function openAmenityModal() {
        document.getElementById('addAmenityModal').classList.remove('hidden');
        document.getElementById('addAmenityModal').classList.add('flex');
    }

    function closeAmenityModal() {
        document.getElementById('addAmenityModal').classList.add('hidden');
        document.getElementById('addAmenityModal').classList.remove('flex');
        document.getElementById('addAmenityForm').reset();
    }

    function openEventPlaceModal() {
        document.getElementById('addEventPlaceModal').classList.remove('hidden');
        document.getElementById('addEventPlaceModal').classList.add('flex');
    }

    function closeEventPlaceModal() {
        document.getElementById('addEventPlaceModal').classList.add('hidden');
        document.getElementById('addEventPlaceModal').classList.remove('flex');
        document.getElementById('addEventPlaceForm').reset();
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
        const section = panel ? panel.getAttribute('data-dining-subpanel') : 'tables';
        const isTable = section === 'tables';
        const isSchedule = section === 'schedule';
        const status = cells[isSchedule ? 3 : 4].textContent.trim();

        document.getElementById('editDiningTitle').textContent = isTable ? 'Edit Table / Seating' : isSchedule ? 'Edit Dining Schedule' : 'Edit Menu / Meal';
        document.getElementById('editDiningNameLabel').textContent = isTable ? 'Table Number' : isSchedule ? 'Meal Period' : 'Meal Name';
        document.getElementById('editDiningTypeLabel').textContent = isTable ? 'Table Type' : isSchedule ? 'Schedule Type' : 'Category';
        document.getElementById('editDiningValueLabel').textContent = isTable ? 'Capacity' : isSchedule ? 'Maximum Guests' : 'Price (₱)';
        document.getElementById('editDiningDetailLabel').textContent = isTable ? 'Location' : 'Available Time';
        document.getElementById('editDiningName').value = cells[0].textContent.trim();
        document.getElementById('editDiningType').value = cells[1].textContent.trim();
        document.getElementById('editDiningValue').value = cells[2].textContent.replace('₱', '').trim();
        document.getElementById('editDiningDetail').value = cells[3].textContent.trim();
        document.getElementById('editDiningStatus').value = status;
        window.editingDiningRow = row;
        document.getElementById('editDiningForm').dataset.section = section;
        document.getElementById('editDiningForm').dataset.table = isTable ? 'true' : 'false';
        document.getElementById('editDiningForm').dataset.schedule = isSchedule ? 'true' : 'false';
        document.getElementById('editDiningModal').classList.remove('hidden');
        document.getElementById('editDiningModal').classList.add('flex');
    }

    function saveDiningEdit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const row = window.editingDiningRow;
        if (!row) return;

        const cells = row.querySelectorAll('td');
        const value = document.getElementById('editDiningValue').value.trim();
        const isTable = form.dataset.table === 'true';
        const isSchedule = form.dataset.schedule === 'true';
        cells[0].textContent = document.getElementById('editDiningName').value.trim();
        cells[1].textContent = document.getElementById('editDiningType').value.trim();
        cells[2].textContent = isTable || isSchedule ? value : '₱' + value;
        cells[3].textContent = document.getElementById('editDiningDetail').value.trim();
        cells[isSchedule ? 3 : 4].querySelector('span').textContent = document.getElementById('editDiningStatus').value;
        closeEditDiningModal();
    }

    function deleteDiningRow(button) {
        const row = button.closest('tr');
        const itemName = row.querySelector('td')?.textContent.trim() || 'this item';

        if (confirm('Delete ' + itemName + '?')) {
            row.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('addRoomModal');
        const editModal = document.getElementById('editRoomModal');
        const statusModal = document.getElementById('statusModal');
        const amenityModal = document.getElementById('addAmenityModal');
        const eventPlaceModal = document.getElementById('addEventPlaceModal');
        const diningModal = document.getElementById('addDiningModal');
        const editDiningModal = document.getElementById('editDiningModal');
        const editInventoryModal = document.getElementById('editInventoryModal');
        const inventoryStatusModal = document.getElementById('inventoryStatusModal');

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddRoomModal();
                closeEditRoomModal();
                closeStatusModal();
                closeAmenityModal();
                closeEventPlaceModal();
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
        const diningPanel = document.querySelector('[data-panel="dining"]');

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
            addButtons.forEach(function(button) {
                const shouldShow =
                    (targetName === 'rooms' && button.id === 'add-room-button') ||
                    (targetName === 'amenities' && button.id === 'add-amenities-button') ||
                    (targetName === 'event-place' && button.id === 'add-event-place-button');
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

                if (button.getAttribute('onclick')?.startsWith('confirmBulkDiningDelete')) {
                    return;
                }

                if (button.querySelector('.fa-trash')) {
                    deleteDiningRow(button);
                }
            });
        }

        document.getElementById('editDiningForm').addEventListener('submit', saveDiningEdit);

        document.getElementById('add-amenities-button').addEventListener('click', openAmenityModal);
        document.getElementById('add-event-place-button').addEventListener('click', openEventPlaceModal);

        updateSelectedCount();
        activateTab(@json($activeTab));

        @if($errors->any())
            @if(old('category'))
                activateTab(@json(old('category') === 'event_place' ? 'event-place' : old('category')));
                @if(old('category') === 'amenities') openAmenityModal(); @elseif(old('category') === 'event_place') openEventPlaceModal(); @elseif(old('category') === 'dining') openDiningModal(); @endif
            @else
                openAddRoomModal();
            @endif
        @endif
    });
</script>
@endsection
