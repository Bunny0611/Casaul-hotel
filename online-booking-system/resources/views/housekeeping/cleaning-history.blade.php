@extends('housekeeping.layout')

@section('content')

<div class="animate-fade-in py-2">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Cleaning History</h2>
            <p class="text-sm text-gray-500 mt-1">Track and review all completed housekeeping tasks.</p>
        </div>
        <a href="#" class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-lg hover:opacity-90 transition font-medium text-sm shadow-sm">
            <i class="fas fa-download"></i>
            Export Report
        </a>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8">

        <!-- COMPLETED TASKS -->
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 p-5 md:p-6 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Completed Tasks</p>
                    <h2 class="text-3xl font-bold text-gray-800 mt-1">45</h2>
                    <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i>
                        Successfully cleaned rooms
                    </p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-green-600 p-4 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-broom text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- THIS WEEK -->
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 p-5 md:p-6 transition-all duration-300 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">This Week</p>
                    <h2 class="text-3xl font-bold text-gray-800 mt-1">18</h2>
                    <p class="text-xs text-blue-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-calendar"></i>
                        Completed cleaning
                    </p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-4 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- AVERAGE TIME -->
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 p-5 md:p-6 transition-all duration-300 group sm:col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Average Cleaning Time</p>
                    <h2 class="text-3xl font-bold text-gray-800 mt-1">35 <span class="text-lg font-semibold text-gray-400">mins</span></h2>
                    <p class="text-xs text-purple-600 mt-2 flex items-center gap-1">
                        <i class="fas fa-clock"></i>
                        Per room
                    </p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-purple-600 p-4 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-stopwatch text-white text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- SEARCH AND FILTER -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center gap-2">
            <i class="fas fa-filter text-orange-500"></i>
            Search Cleaning Records
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">

            <div>
                <label class="text-sm font-medium text-gray-600">Search Room</label>
                <div class="relative mt-2">
                    <i class="fas fa-bed absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Room number"
                        class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500 transition">
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Date</label>
                <div class="relative mt-2">
                    <i class="fas fa-calendar-alt absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="date"
                        class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500 transition">
                </div>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Staff</label>
                <div class="relative mt-2">
                    <i class="fas fa-user-cog absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <select class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500 transition appearance-none">
                        <option>All Staff</option>
                        <option>Maria Santos</option>
                        <option>John Cruz</option>
                        <option>Anna Reyes</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div class="flex items-end">
                <button class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 py-2.5 rounded-lg hover:opacity-90 transition font-medium text-sm shadow-sm">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
            </div>

        </div>
    </div>

    <!-- CLEANING HISTORY TABLE -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-5 md:p-6 mb-2">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list-check text-orange-500"></i>
                Completed Cleaning Records
            </h3>
            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fas fa-file-alt text-gray-400"></i>
                Total Records: 45
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px]">
                <thead>
                    <tr class="bg-gradient-to-r from-orange-50 to-orange-100/60 border-y border-orange-100">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Room</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Task</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Assigned Staff</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Date</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Time</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-orange-700">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    <!-- ROOM 101 -->
                    <tr class="hover:bg-orange-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">Room 101</div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium mt-0.5 inline-block">Deluxe Room</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Deep Cleaning</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-semibold shadow-sm">MS</div>
                                <span class="text-sm text-gray-700">Maria Santos</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">Jul 31, 2026</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">09:30 AM</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 inline-flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                Completed
                            </span>
                        </td>
                    </tr>

                    <!-- ROOM 205 -->
                    <tr class="hover:bg-orange-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">Room 205</div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 font-medium mt-0.5 inline-block">Suite Room</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">Linen Replacement</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold shadow-sm">JC</div>
                                <span class="text-sm text-gray-700">John Cruz</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">Jul 30, 2026</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">02:15 PM</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 inline-flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                Completed
                            </span>
                        </td>
                    </tr>

                    <!-- ROOM 302 -->
                    <tr class="hover:bg-orange-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">Room 302</div>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium mt-0.5 inline-block">Standard Room</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">General Cleaning</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white text-xs font-semibold shadow-sm">AR</div>
                                <span class="text-sm text-gray-700">Anna Reyes</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">Jul 29, 2026</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">10:00 AM</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 inline-flex items-center gap-1">
                                <i class="fas fa-check-circle"></i>
                                Completed
                            </span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- TABLE FOOTER / PAGINATION -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 md:px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500">Showing <span class="font-semibold text-gray-700">1</span> to <span class="font-semibold text-gray-700">3</span> of <span class="font-semibold text-gray-700">45</span> records</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-500 hover:bg-white hover:text-orange-600 transition disabled:opacity-40" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 text-white text-sm font-medium shadow-sm">1</button>
                <button class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-white hover:text-orange-600 transition">2</button>
                <button class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-600 hover:bg-white hover:text-orange-600 transition">3</button>
                <button class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm text-gray-500 hover:bg-white hover:text-orange-600 transition">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

    </div>

</div>

@endsection
