++++++++@extends('housekeeping.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">
        <i class="fas fa-broom text-blue-500 mr-2"></i>Housekeeping Dashboard
    </h2>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Rooms</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalRooms }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-bed text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Clean Rooms</p>
                    <p class="text-2xl font-bold text-green-600">{{ $cleanRooms }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Dirty Rooms</p>
                    <p class="text-2xl font-bold text-red-600">{{ $dirtyRooms }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">In Progress</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $inProgress }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-spinner text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Occupied Rooms</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $occupiedRooms }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fas fa-users text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Room Cleaning Status Grid -->
    <div id="rooms" class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Room Cleaning Status</h3>
            <div class="flex space-x-4 text-sm">
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>Clean</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>Dirty</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>In Progress</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($rooms as $room)
            <div class="border rounded-xl p-4 {{ $room->cleaning_status === 'clean' ? 'bg-green-50 border-green-200' : ($room->cleaning_status === 'dirty' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl font-bold text-gray-800">{{ $room->room_number }}</span>
                    <span class="w-3 h-3 rounded-full {{ $room->cleaning_status === 'clean' ? 'bg-green-500' : ($room->cleaning_status === 'dirty' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                </div>
                <p class="text-sm text-gray-600 mb-1">{{ $room->room_type }}</p>
                <p class="text-xs text-gray-500 mb-3">
                    Status: 
                    <span class="px-2 py-0.5 rounded-full text-white text-xs status-{{ $room->status }}">
                        {{ ucfirst($room->status) }}
                    </span>
                </p>
                <p class="text-xs text-gray-500 mb-3">Cleaning: {{ ucfirst(str_replace('_', ' ', $room->cleaning_status)) }}</p>
                <form method="POST" action="{{ route('housekeeping.rooms.cleaning', $room->id) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')
                    <select name="cleaning_status" onchange="this.form.submit()" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="clean" {{ $room->cleaning_status === 'clean' ? 'selected' : '' }}>Clean</option>
                        <option value="dirty" {{ $room->cleaning_status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                        <option value="in_progress" {{ $room->cleaning_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </form>
            </div>
            @empty
            <div class="col-span-full p-12 text-center text-gray-500">
                <i class="fas fa-bed text-4xl mb-4 text-gray-300"></i>
                <p>No rooms found.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

