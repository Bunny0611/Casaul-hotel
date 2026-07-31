@extends('housekeeping.layout')

@section('content')

<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">
        Housekeeping Dashboard
    </h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Assigned Rooms -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">
                        Assigned Rooms
                    </p>

                    <p class="text-2xl font-bold text-gray-800">
                        {{ $assignedRooms }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-clock mr-1 text-yellow-500"></i>
                        Pending Cleaning
                    </p>
                </div>

                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-bed text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>


        <!-- Completed Cleaning -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">
                        Completed Today
                    </p>

                    <p class="text-2xl font-bold text-gray-800">
                        {{ $completedCleaning }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-check-circle mr-1 text-green-500"></i>
                        Rooms Cleaned
                    </p>
                </div>

                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-broom text-green-600 text-xl"></i>
                </div>
            </div>
        </div>


        <!-- Guest Requests -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">
                        Guest Requests
                    </p>

                    <p class="text-2xl font-bold text-gray-800">
                        {{ $guestRequests }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-bell mr-1 text-orange-500"></i>
                        Pending Requests
                    </p>
                </div>

                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-concierge-bell text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>


        <!-- Maintenance -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">
                        Maintenance Reports
                    </p>

                    <p class="text-2xl font-bold text-gray-800">
                        {{ $maintenanceReports }}
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-tools mr-1 text-red-500"></i>
                        Room Issues
                    </p>
                </div>

                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-screwdriver-wrench text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

    </div>


    <!-- Assigned Rooms Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">

        <div class="flex items-center justify-between mb-4">

            <h3 class="text-lg font-semibold text-gray-800">
                Today's Assigned Rooms
            </h3>

            <a href="{{ route('housekeeping.assigned') }}"
                class="text-sm text-[#ff6b35] hover:underline">
                View All 
                <i class="fas fa-arrow-right ml-1"></i>
            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="bg-gray-50">

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Room
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Task
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                @forelse($recentTasks as $task)

                    <tr class="hover:bg-gray-50 transition-colors">

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $task->room_number }}
                        </td>


                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $task->task }}
                        </td>


                        <td class="px-6 py-4 whitespace-nowrap">

                            @if($task->status == 'Pending')

                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>


                            @elseif($task->status == 'Completed')

                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                            </span>


                            @else

                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Cleaning
                            </span>


                            @endif

                        </td>


                        <td class="px-6 py-4 whitespace-nowrap">

                            <a href="#" class="text-blue-600 hover:underline">
                                Update
                            </a>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">

                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>

                            No assigned rooms today

                        </td>

                    </tr>


                @endforelse


                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection