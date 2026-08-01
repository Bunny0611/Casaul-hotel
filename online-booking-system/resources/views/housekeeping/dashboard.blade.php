@extends('housekeeping.layout')

@section('content')
<<<<<<< HEAD
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

=======

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    font-family:'Poppins',sans-serif;
}

.sidebar{
    background:linear-gradient(180deg,#800000 0%,#5c0000 100%);
}

.header{
    background:linear-gradient(90deg,#ff6b35 0%,#ff8c42 100%);
}

.card-hover:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 40px rgba(0,0,0,.15);
}

.animate-fade-in{
    animation:fadeIn .5s ease-in-out;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>

<div class="lg:ml-64 min-h-screen bg-gray-100">

    <!-- SIDEBAR -->
    <div id="sidebar"
        class="sidebar fixed top-0 -left-64 lg:left-0 w-64 h-screen z-50 flex flex-col text-white shadow-xl transition-all duration-300">

        <!-- LOGO -->
        <div class="p-6 border-b border-white/20">

            <h1 class="text-2xl font-bold tracking-wider">
                <i class="fas fa-hotel mr-2"></i>
                CASAUL
            </h1>

            <p class="text-sm text-gray-300">
                Housekeeping
            </p>

        </div>

        <!-- NAVIGATION -->
        <nav class="mt-6 flex-1">

            <a href="{{ route('housekeeping.dashboard') }}"
                class="flex items-center px-6 py-3 bg-white/20 border-l-4 border-red-900">

                <i class="fas fa-home w-6"></i>
                <span>Dashboard</span>

            </a>

            <a href="{{ route('housekeeping.assigned-rooms') }}"
                class="flex items-center px-6 py-3 hover:bg-white/10 transition">

                <i class="fas fa-bed w-6"></i>
                <span>Assigned Rooms</span>

            </a>

            <a href="{{ route('housekeeping.room-status-update') }}"
                class="flex items-center px-6 py-3 hover:bg-white/10 transition">

                <i class="fas fa-sync-alt w-6"></i>
                <span>Room Status Update</span>

            </a>

            <a href="{{ route('housekeeping.guest-requests') }}"
                class="flex items-center px-6 py-3 hover:bg-white/10 transition">

                <i class="fas fa-bell w-6"></i>
                <span>Guest Requests</span>

            </a>

            <a href="{{ route('housekeeping.maintenance-report') }}"
                class="flex items-center px-6 py-3 hover:bg-white/10 transition">

                <i class="fas fa-tools w-6"></i>
                <span>Maintenance Report</span>

            </a>

            <a href="{{ route('housekeeping.cleaning-history') }}"
                class="flex items-center px-6 py-3 hover:bg-white/10 transition">

                <i class="fas fa-history w-6"></i>
                <span>Cleaning History</span>

            </a>

        </nav>

        <!-- LOGOUT -->
        <div class="absolute bottom-0 w-64 p-6">

            <a href="{{ route('logout') }}"
                class="flex items-center text-gray-300 hover:text-white">

                <i class="fas fa-sign-out-alt w-6"></i>
                Logout

            </a>

        </div>

    </div>

    <!-- HEADER -->
    <header class="header text-white px-4 md:px-6 py-4 shadow-lg">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <!-- LEFT -->
            <div class="flex items-center">

                <!-- Mobile Menu -->
                <button id="menuBtn"
                    class="lg:hidden text-2xl mr-4">

                    <i class="fas fa-bars"></i>

                </button>

                <div>

                    <h2 class="text-xl font-semibold">
                        Welcome to CASAUL Housekeeping
                    </h2>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                <div class="relative w-full sm:w-64">

                    <input
                        type="text"
                        placeholder="Search..."
                        class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none w-full">

                    <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>

                </div>

                <div class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-lg">

                    <i class="fas fa-user-circle text-2xl"></i>

                    <span>
                        Housekeeper
                    </span>

                </div>

            </div>

        </div>

    </header>

    <!-- CONTENT -->
    <div class="animate-fade-in p-6">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">
            Housekeeping Dashboard
        </h2>

    <!-- CARDS -->

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Assigned Rooms -->
    <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition">

        <div class="flex justify-between">

            <div>

                <p class="text-gray-500 text-sm">
                    Assigned Rooms
                </p>

                <p class="text-3xl font-bold">
                    12
                </p>

                <p class="text-xs text-yellow-500 mt-2">

                    <i class="fas fa-clock mr-1"></i>

                    Pending Cleaning

                </p>

            </div>

            <div class="bg-blue-100 w-14 h-14 rounded-full flex items-center justify-center">

                <i class="fas fa-bed text-blue-600 text-xl"></i>

            </div>

        </div>

    </div>

    <!-- Completed Today -->
    <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition">

        <div class="flex justify-between">

            <div>

                <p class="text-gray-500 text-sm">
                    Completed Today
                </p>

                <p class="text-3xl font-bold">
                    8
                </p>

                <p class="text-xs text-green-500 mt-2">

                    <i class="fas fa-check-circle mr-1"></i>

                    Rooms Cleaned

                </p>

            </div>

            <div class="bg-green-100 w-14 h-14 rounded-full flex items-center justify-center">

                <i class="fas fa-broom text-green-600 text-xl"></i>

            </div>

        </div>

    </div>

    <!-- Guest Requests -->
    <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition">

        <div class="flex justify-between">

            <div>

                <p class="text-gray-500 text-sm">
                    Guest Requests
                </p>

                <p class="text-3xl font-bold">
                    5
                </p>

                <p class="text-xs text-red-800 mt-2">

                    <i class="fas fa-bell mr-1"></i>

                    Pending Requests

                </p>

            </div>

            <div class="bg-red-100 w-14 h-14 rounded-full flex items-center justify-center">

                <i class="fas fa-concierge-bell text-red-800 text-xl"></i>

            </div>

        </div>

    </div>

    <!-- Maintenance Reports -->
    <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition">

        <div class="flex justify-between">

            <div>

                <p class="text-gray-500 text-sm">
                    Maintenance Reports
                </p>

                <p class="text-3xl font-bold">
                    3
                </p>

                <p class="text-xs text-red-500 mt-2">

                    <i class="fas fa-tools mr-1"></i>

                    Room Issues

                </p>

            </div>

            <div class="bg-red-100 w-14 h-14 rounded-full flex items-center justify-center">

                <i class="fas fa-screwdriver-wrench text-red-600 text-xl"></i>

            </div>

        </div>

    </div>

</div>

<!-- TABLE -->

<div class="bg-white rounded-xl shadow-lg p-6">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-5">

        <h3 class="text-lg font-semibold">
            Today's Assigned Rooms
        </h3>

        <a href="{{ route('housekeeping.assigned-rooms') }}"
            class="text-red-800 hover:underline">

            View All →

        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full min-w-[650px]">

            <thead>

                <tr class="bg-gray-50">

                    <th class="px-6 py-3 text-left text-xs text-gray-500">
                        Room
                    </th>

                    <th class="px-6 py-3 text-left text-xs text-gray-500">
                        Task
                    </th>

                    <th class="px-6 py-3 text-left text-xs text-gray-500">
                        Status
                    </th>

                    <th class="px-6 py-3 text-left text-xs text-gray-500">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                <tr>

                    <td class="px-6 py-4">
                        Room 101
                    </td>

                    <td class="px-6 py-4">
                        Deep Cleaning
                    </td>

                    <td class="px-6 py-4">

                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">

                            Pending

                        </span>

                    </td>

                    <td class="px-6 py-4">

                        <a href="{{ route('housekeeping.room-status-update') }}"
                            class="text-blue-600 hover:underline">

                            Update

                        </a>

                    </td>

                </tr>

                <tr>

                    <td class="px-6 py-4">
                        Room 205
                    </td>

                    <td class="px-6 py-4">
                        Change Linens
                    </td>

                    <td class="px-6 py-4">

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">

                            Completed

                        </span>

                    </td>

                    <td class="px-6 py-4">

                        <a href="{{ route('housekeeping.room-status-update') }}"
                            class="text-blue-600 hover:underline">

                            Update

                        </a>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>
    </div>
</div>

<!-- MOBILE SIDEBAR SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');

    menuBtn.addEventListener('click', function () {

        if (sidebar.classList.contains('-left-64')) {

            sidebar.classList.remove('-left-64');
            sidebar.classList.add('left-0');

        } else {

            sidebar.classList.remove('left-0');
            sidebar.classList.add('-left-64');

        }

    });

    // Close sidebar when clicking outside (Mobile)
    document.addEventListener('click', function (e) {

        if (
            window.innerWidth < 1024 &&
            !sidebar.contains(e.target) &&
            !menuBtn.contains(e.target)
        ) {

            sidebar.classList.remove('left-0');
            sidebar.classList.add('-left-64');

        }

    });

});
</script>

@endsection
>>>>>>> 81b40b1048ddfbb91c0830e80e2e884163d90a17
