@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Room Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage room inventory, availability, and maintenance from one place.</p>
        </div>
        <div class="flex items-center gap-3">
            <button id="add-room-button" type="button" onclick="openAddRoomModal()" class="add-panel-button inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Room
            </button>
            <button id="add-amenities-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Amenities
            </button>
            <button id="add-event-place-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Event Place
            </button>
            <button id="add-dining-button" type="button" class="add-panel-button hidden inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
                <i class="fas fa-plus mr-2"></i>Add Dining
            </button>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-4">
        <button type="button" data-tab="rooms" class="tab-button rounded-lg bg-orange-500 px-6 py-3 font-medium text-white transition hover:bg-orange-600">ROOMS</button>
        <button type="button" data-tab="amenities" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">AMENITIES</button>
        <button type="button" data-tab="event-place" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">EVENT PLACE</button>
        <button type="button" data-tab="dining" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">DINING</button>
    </div>

    <div data-panel="rooms" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-none">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div></div>
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
        <div class="px-6 py-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <input id="selectAllRooms" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500" onclick="toggleAllRoomCheckboxes(this)">
                    <label for="selectAllRooms">Select all rooms</label>
                </div>
                {{-- total() = full room count across all pages (count() would only show the current page's 5 rows once paginated) --}}
                <div class="text-sm text-gray-500">{{ $rooms->total() }} room{{ $rooms->total() === 1 ? '' : 's' }}</div>
            </div>
            <div class="overflow-x-auto">
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
        <div class="border-b border-gray-200 px-6 py-5">
            <h3 class="text-lg font-semibold text-gray-800">Amenities</h3>
            <p class="mt-1 text-sm text-gray-500">Add premium guest amenities and service upgrades for each room package.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="amenities-list" class="divide-y divide-gray-200">
                    @foreach($inventoryItems->where('category', 'amenities') as $item)
                        <tr><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm">—</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div data-panel="event-place" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-5">
            <h3 class="text-lg font-semibold text-gray-800">Event Place</h3>
            <p class="mt-1 text-sm text-gray-500">Manage wedding, corporate, and celebration offerings for your event venue.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="event-place-list" class="divide-y divide-gray-200">
                    @foreach($inventoryItems->where('category', 'event_place') as $item)
                        <tr><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm">—</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div data-panel="dining" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-5">
            <h3 class="text-lg font-semibold text-gray-800">Dining</h3>
            <p class="mt-1 text-sm text-gray-500">Track dining packages and culinary experiences available to your guests.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Available Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody id="dining-list" class="divide-y divide-gray-200">
                    @foreach($inventoryItems->where('category', 'dining') as $item)
                        <tr><td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->type ?: 'Menu / Meal' }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->name }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->location ?: '—' }}</td><td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($item->price, 2) }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->available_from ? substr($item->available_from, 0, 5) : '—' }} - {{ $item->available_to ? substr($item->available_to, 0, 5) : '—' }}</td><td class="px-6 py-4 text-sm text-gray-900">{{ $item->quantity ?: 'Unlimited' }}</td><td class="px-6 py-4 text-sm"><span class="rounded-full bg-green-500 px-3 py-1 text-xs font-medium text-white">{{ ucfirst($item->status) }}</span></td><td class="px-6 py-4 text-sm">—</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addAmenityModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAmenityModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Amenity</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new amenity option for guests.</p>
        </div>

        <form id="addAmenityForm" method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="amenities">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Amenity Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEventPlaceModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Event Place</h3>
            <p class="mt-1 text-sm text-gray-500">Create a new event package or venue option.</p>
        </div>

        <form id="addEventPlaceForm" method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="category" value="event_place">
            <input type="hidden" name="status" value="available">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Name</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
    <div class="relative max-h-[90vh] w-full max-w-6xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-6">
            <h3 class="text-3xl font-bold text-gray-800">Add Dining Item</h3>
            <p class="mt-2 text-base text-gray-600">Add a new menu item, package, or dining service available for guests.</p>
        </div>

        <form id="addDiningForm" method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="category" value="dining">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Item Type <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Menu / Meal">Menu / Meal</option>
                            <option value="Package">Package</option>
                            <option value="Dinner">Dinner</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Grilled Salmon">
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="menu_category" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Dinner">Dinner</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Dessert">Dessert</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Price (₱) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" step="0.01" min="0" required class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. 650.00">
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="5" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Grilled salmon served with vegetables and mashed potatoes."></textarea>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Available Time <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                            <input type="time" name="available_from" value="17:00" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <span class="text-lg text-gray-500">to</span>
                            <input type="time" name="available_to" value="21:00" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Available Quantity</label>
                        <input type="number" name="quantity" min="0" class="w-full rounded-xl border border-gray-300 px-3 py-3 text-base text-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. 25">
                        <p class="mt-2 text-sm text-gray-500">Optional. Leave blank if unlimited.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Available">Available</option>
                            <option value="Limited">Limited</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-base font-medium text-gray-700">Image (Optional)</label>
                        <div class="flex min-h-[220px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center text-gray-500">
                            <div class="mb-4 text-5xl text-gray-400">
                                <i class="fas fa-image"></i>
                            </div>
                            <p class="text-lg text-gray-600">Click to upload or drag and drop</p>
                            <p class="mt-2 text-sm text-gray-500">PNG, JPG up to 2MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-6">
                <button type="button" onclick="closeDiningModal()" class="rounded-xl border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-xl bg-orange-500 px-6 py-3 text-base font-semibold text-white shadow-lg transition hover:bg-orange-600">Save Dining Item</button>
            </div>
        </form>
    </div>
</div>

<div id="addRoomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAddRoomModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add New Room</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the details below to create a new room.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rooms.store') }}" method="POST" class="space-y-4">
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
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeAddRoomModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Add Room</button>
            </div>
        </form>
    </div>
</div>

<div id="editRoomModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeEditRoomModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit Room</h3>
            <p class="mt-1 text-sm text-gray-500">Update the room details below.</p>
        </div>

        <form id="editRoomForm" action="" method="POST" class="space-y-4">
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
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeEditRoomModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Update Room</button>
            </div>
        </form>
    </div>
</div>

<div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
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

    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('addRoomModal');
        const editModal = document.getElementById('editRoomModal');
        const statusModal = document.getElementById('statusModal');
        const amenityModal = document.getElementById('addAmenityModal');
        const eventPlaceModal = document.getElementById('addEventPlaceModal');
        const diningModal = document.getElementById('addDiningModal');

        [addModal, editModal, statusModal, amenityModal, eventPlaceModal, diningModal].forEach(function (modal) {
            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        if (modal === addModal) {
                            closeAddRoomModal();
                        } else if (modal === editModal) {
                            closeEditRoomModal();
                        } else if (modal === statusModal) {
                            closeStatusModal();
                        } else if (modal === amenityModal) {
                            closeAmenityModal();
                        } else if (modal === eventPlaceModal) {
                            closeEventPlaceModal();
                        } else if (modal === diningModal) {
                            closeDiningModal();
                        }
                    }
                });
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddRoomModal();
                closeEditRoomModal();
                closeStatusModal();
                closeAmenityModal();
                closeEventPlaceModal();
                closeDiningModal();
            }
        });

        const tabButtons = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('[data-panel]');
        const addButtons = document.querySelectorAll('.add-panel-button');

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
                    (targetName === 'event-place' && button.id === 'add-event-place-button') ||
                    (targetName === 'dining' && button.id === 'add-dining-button');
                button.classList.toggle('hidden', !shouldShow);
            });
        }

        tabButtons.forEach(function(button) {
            button.addEventListener('click', function () {
                activateTab(this.getAttribute('data-tab'));
            });
        });

        document.getElementById('add-amenities-button').addEventListener('click', openAmenityModal);
        document.getElementById('add-event-place-button').addEventListener('click', openEventPlaceModal);
        document.getElementById('add-dining-button').addEventListener('click', openDiningModal);

        updateSelectedCount();
        activateTab('rooms');

        @if($errors->any())
            openAddRoomModal();
        @endif
    });
</script>
@endsection
