@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Room Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage room inventory, availability, and maintenance from one place.</p>
        </div>
        <button type="button" onclick="openAddRoomModal()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
            <i class="fas fa-plus mr-2"></i>Add Room
        </button>
    </div>

    <div class="mb-6 flex space-x-4">
        <button class="rounded-lg bg-orange-500 px-6 py-3 font-medium text-white">ROOMS</button>
        <button class="rounded-lg bg-white px-6 py-3 font-medium text-gray-600 hover:bg-gray-100">MAINTENANCE</button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Room No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Capacity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rooms as $room)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->room_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($room->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->floor }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->capacity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="rounded-full px-3 py-1 text-xs font-medium text-white status-{{ $room->status }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="editRoom({{ $room->id }}, @json($room->room_number), @json($room->room_type), {{ $room->price }}, @json($room->floor), {{ $room->capacity }}, @json($room->status))" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="changeStatus({{ $room->id }})" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Status">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-bed mb-4 text-4xl text-gray-300"></i>
                            <p>No rooms found. Add your first room to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <button type="button" class="mt-6 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
        <i class="fas fa-tools mr-2"></i>Schedule Maintenance
    </button>
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Number</label>
                    <input type="text" name="room_number" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room Type</label>
                    <select name="room_type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Type</option>
                        <option value="Deluxe Room">Deluxe Room</option>
                        <option value="Executive Suite">Executive Suite</option>
                        <option value="Presidential Suite">Presidential Suite</option>
                        <option value="Standard Room">Standard Room</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Price (₱)</label>
                    <input type="number" name="price" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Floor</label>
                    <input type="text" name="floor" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Capacity</label>
                    <input type="number" name="capacity" required min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
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
                        <option value="Executive Suite">Executive Suite</option>
                        <option value="Presidential Suite">Presidential Suite</option>
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

    document.addEventListener('DOMContentLoaded', function () {
        const addModal = document.getElementById('addRoomModal');
        const editModal = document.getElementById('editRoomModal');
        const statusModal = document.getElementById('statusModal');

        [addModal, editModal, statusModal].forEach(function (modal) {
            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        if (modal === addModal) {
                            closeAddRoomModal();
                        } else if (modal === editModal) {
                            closeEditRoomModal();
                        } else {
                            closeStatusModal();
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
            }
        });

        @if($errors->any())
            openAddRoomModal();
        @endif
    });
</script>
@endsection
