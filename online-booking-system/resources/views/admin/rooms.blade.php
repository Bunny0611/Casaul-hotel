@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Room Management</h2>
        <button onclick="document.getElementById('addRoomModal').classList.remove('hidden')" class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add Room
        </button>
    </div>
    
    <!-- Tabs -->
    <div class="flex space-x-4 mb-6">
        <button class="px-6 py-3 bg-orange-500 text-white rounded-lg font-medium">ROOMS</button>
        <button class="px-6 py-3 bg-white text-gray-600 rounded-lg font-medium hover:bg-gray-100">MAINTENANCE</button>
    </div>
    
    <!-- Rooms Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room No</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->room_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($room->price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->floor }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->capacity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $room->status }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button onclick="editRoom({{ $room->id }}, @json($room->room_number), @json($room->room_type), {{ $room->price }}, @json($room->floor), {{ $room->capacity }}, @json($room->status) )" class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="changeStatus({{ $room->id }})" class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                            <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-bed text-4xl mb-4 text-gray-300"></i>
                            <p>No rooms found. Add your first room to get started.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <button class="mt-6 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg">
        <i class="fas fa-tools mr-2"></i>Schedule Maintenance
    </button>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 animate-fade-in">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Add New Room</h3>
        </div>
        <form action="{{ route('admin.rooms.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                <input type="text" name="room_number" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                <select name="room_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Select Type</option>
                    <option value="Deluxe Room">Deluxe Room</option>
                    <option value="Executive Suite">Executive Suite</option>
                    <option value="Presidential Suite">Presidential Suite</option>
                    <option value="Standard Room">Standard Room</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price (₱)</label>
                <input type="number" name="price" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                <input type="text" name="floor" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                <input type="number" name="capacity" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"></textarea>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('addRoomModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">Add Room</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="editRoomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 animate-fade-in">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Edit Room</h3>
        </div>
        <form id="editRoomForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editRoomId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                <input type="text" name="room_number" id="editRoomNumber" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                <select name="room_type" id="editRoomType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Select Type</option>
                    <option value="Deluxe Room">Deluxe Room</option>
                    <option value="Executive Suite">Executive Suite</option>
                    <option value="Presidential Suite">Presidential Suite</option>
                    <option value="Standard Room">Standard Room</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price (₱)</label>
                <input type="number" name="price" id="editPrice" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                <input type="text" name="floor" id="editFloor" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                <input type="number" name="capacity" id="editCapacity" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="editStatus" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('editRoomModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">Update Room</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Status Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 animate-fade-in">
        <div class="p-6 border-b">
            <h3 class="text-xl font-semibold text-gray-800">Change Room Status</h3>
        </div>
        <form id="statusForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="id" id="statusRoomId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Status</label>
                <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('statusModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">Update Status</button>
            </div>
        </form>
    </div>
</div>

<script>
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
    }
    
    function changeStatus(id) {
        document.getElementById('statusRoomId').value = id;
        var statusRoute = "{{ route('admin.rooms.status', ['id' => '__ID__']) }}";
        document.getElementById('statusForm').action = statusRoute.replace('__ID__', id);
        document.getElementById('statusModal').classList.remove('hidden');
    }
</script>
@endsection
