@extends('housekeeping.layout')

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


<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
    color-scheme: light;
}

* {
    font-family: 'Poppins', sans-serif;
}

.sidebar {
    background: linear-gradient(180deg, #7a0f22 0%, #5a0000 100%);
}

.header {
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
}

.card-hover {
    transition: all 0.25s ease;
}

.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div class="min-h-screen bg-slate-100 lg:ml-64">

    <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

    <div id="sidebar"
        class="sidebar fixed top-0 left-0 z-50 flex h-screen w-64 -translate-x-full flex-col text-white shadow-2xl transition-transform duration-300 lg:translate-x-0">

        <div class="border-b border-white/20 p-6">

            <h1 class="text-2xl font-bold tracking-wider">
                <i class="fas fa-hotel mr-2"></i>
                CASAUL
            </h1>

            <p class="mt-1 text-sm text-gray-300">
                Housekeeping
            </p>

        </div>

        <nav class="mt-6 flex-1">

            <a href="{{ route('housekeeping.dashboard') }}"
                class="flex items-center border-l-4 border-red-900 bg-white/20 px-6 py-3">

                <i class="fas fa-home mr-3 w-5"></i>
                <span>Dashboard</span>

            </a>

            <a href="{{ route('housekeeping.assigned-rooms') }}"
                class="flex items-center px-6 py-3 transition hover:bg-white/10">

                <i class="fas fa-bed mr-3 w-5"></i>
                <span>Assigned Rooms</span>

            </a>

            <a href="{{ route('housekeeping.room-status-update') }}"
                class="flex items-center px-6 py-3 transition hover:bg-white/10">

                <i class="fas fa-sync-alt mr-3 w-5"></i>
                <span>Room Status Update</span>

            </a>

            <a href="{{ route('housekeeping.guest-requests') }}"
                class="flex items-center px-6 py-3 transition hover:bg-white/10">

                <i class="fas fa-bell mr-3 w-5"></i>
                <span>Guest Requests</span>

            </a>

            <a href="{{ route('housekeeping.maintenance-report') }}"
                class="flex items-center px-6 py-3 transition hover:bg-white/10">

                <i class="fas fa-tools mr-3 w-5"></i>
                <span>Maintenance Report</span>

            </a>

            <a href="{{ route('housekeeping.cleaning-history') }}"
                class="flex items-center px-6 py-3 transition hover:bg-white/10">

                <i class="fas fa-history mr-3 w-5"></i>
                <span>Cleaning History</span>

            </a>

        </nav>

        <div class="w-full p-6">

            <a href="{{ route('logout') }}"
                class="flex items-center text-gray-300 transition hover:text-white">

                <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                Logout

            </a>

        </div>

    </div>

    <header class="header px-4 py-4 text-white shadow-lg sm:px-6 lg:px-8">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div class="flex items-center">

                <button id="menuBtn"
                    class="mr-4 rounded-full border border-white/30 p-2 text-xl transition hover:bg-white/15 lg:hidden"
                    aria-expanded="false"
                    aria-label="Toggle menu">

                    <i class="fas fa-bars"></i>

                </button>

                <div>

                    <h2 class="text-xl font-semibold sm:text-2xl">
                        Welcome to CASAUL Housekeeping
                    </h2>

                </div>

            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                <div class="relative w-full sm:w-64">

                    <input
                        type="text"
                        placeholder="Search..."
                        class="w-full rounded-xl border border-white/30 bg-white/20 px-4 py-2.5 text-white placeholder-gray-200 outline-none focus:border-white/60 focus:bg-white/25">

                    <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>

                </div>

                <div class="flex items-center gap-2 rounded-xl bg-white/20 px-4 py-2.5">

                    <i class="fas fa-user-circle text-xl"></i>

                    <span class="text-sm font-medium">
                        Housekeeper
                    </span>

                </div>

            </div>

        </div>

    </header>

    <div class="animate-fade-in p-4 sm:p-6 lg:p-8">

        <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">
                    Daily overview
                </p>
                <h2 class="text-2xl font-bold text-slate-800 sm:text-3xl">
                    Housekeeping Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Stay on top of cleaning priorities, guest requests, and maintenance tasks.
                </p>
            </div>

            <div class="flex items-center gap-2 rounded-2xl bg-white px-4 py-3 shadow-sm">
                <i class="fas fa-calendar-alt text-amber-600"></i>
                <span class="text-sm font-medium text-slate-700">Tuesday, 4 Aug 2026</span>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Assigned Rooms</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">12</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-amber-600">
                            <i class="fas fa-clock mr-1"></i>
                            Pending Cleaning
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100">
                        <i class="fas fa-bed text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Completed Today</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">8</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            Rooms Cleaned
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-100">
                        <i class="fas fa-broom text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Guest Requests</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">5</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-red-700">
                            <i class="fas fa-bell mr-1"></i>
                            Pending Requests
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100">
                        <i class="fas fa-concierge-bell text-xl text-red-700"></i>
                    </div>
                </div>
            </div>

            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Maintenance Reports</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">3</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-red-500">
                            <i class="fas fa-tools mr-1"></i>
                            Room Issues
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-100">
                        <i class="fas fa-screwdriver-wrench text-xl text-red-600"></i>
                    </div>
                </div>
            </div>

        </div>

        <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">

            <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-6">

                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <h3 class="text-lg font-semibold text-slate-800">
                        Today's Assigned Rooms
                    </h3>

                    <a href="{{ route('housekeeping.assigned-rooms') }}"
                        class="text-sm font-medium text-red-800 transition hover:underline">

                        View All →

                    </a>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[560px]">

                        <thead>

                            <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">

                                <th class="px-4 py-3">
                                    Room
                                </th>

                                <th class="px-4 py-3">
                                    Task
                                </th>

                                <th class="px-4 py-3">
                                    Status
                                </th>

                                <th class="px-4 py-3">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <tr class="transition hover:bg-orange-50/70">

                                <td class="px-4 py-4 text-sm font-medium text-slate-700">
                                    Room 101
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600">
                                    Deep Cleaning
                                </td>

                                <td class="px-4 py-4">

                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                        Pending
                                    </span>

                                </td>

                                <td class="px-4 py-4">

                                    <a href="{{ route('housekeeping.room-status-update') }}"
                                        class="text-sm font-medium text-blue-600 transition hover:underline">
                                        Update
                                    </a>

                                </td>

                            </tr>

                            <tr class="transition hover:bg-orange-50/70">

                                <td class="px-4 py-4 text-sm font-medium text-slate-700">
                                    Room 205
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600">
                                    Change Linens
                                </td>

                                <td class="px-4 py-4">

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Completed
                                    </span>

                                </td>

                                <td class="px-4 py-4">

                                    <a href="{{ route('housekeeping.room-status-update') }}"
                                        class="text-sm font-medium text-blue-600 transition hover:underline">
                                        Update
                                    </a>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-6">
                <h3 class="text-lg font-semibold text-slate-800">
                    Priority Tasks
                </h3>

                <div class="mt-4 space-y-3">
                    <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-800">Room 303</p>
                                <p class="text-sm text-slate-600">Urgent preparation for guest arrival</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">High</span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-800">Room 410</p>
                                <p class="text-sm text-slate-600">Bathroom maintenance check completed</p>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Ready</span>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-800">Room 512</p>
                                <p class="text-sm text-slate-600">Fresh linens requested by guest</p>
                            </div>
                            <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">Medium</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function setSidebar(open) {
        sidebar.classList.toggle('-translate-x-full', !open);
        sidebar.classList.toggle('translate-x-0', open);
        overlay.classList.toggle('hidden', !open);
        menuBtn.setAttribute('aria-expanded', String(open));
    }

    menuBtn.addEventListener('click', function () {
        const isOpen = sidebar.classList.contains('-translate-x-full');
        setSidebar(isOpen);
    });

    overlay.addEventListener('click', function () {
        setSidebar(false);
    });

    document.addEventListener('click', function (e) {
        if (
            window.innerWidth < 1024 &&
            !sidebar.contains(e.target) &&
            !menuBtn.contains(e.target) &&
            !overlay.classList.contains('hidden')
        ) {
            setSidebar(false);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            setSidebar(false);
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1024) {
            setSidebar(true);
        } else {
            setSidebar(false);
        }
    });

});
</script>

@endsection

