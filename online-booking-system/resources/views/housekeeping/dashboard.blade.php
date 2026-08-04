@extends('housekeeping.layout')

@section('content')
<div class="animate-fade-in space-y-8">
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

        <div class="flex items-center gap-2 rounded-2xl bg-whi
        te px-4 py-3 shadow-sm">
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
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100">
                    <i class="fas fa-bed text-xl text-amber-700"></i>
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
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100">
                    <i class="fas fa-broom text-xl text-emerald-700"></i>
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
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100">
                    <i class="fas fa-concierge-bell text-xl text-orange-700"></i>
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
                    <i class="fas fa-screwdriver-wrench text-xl text-rose-700"></i>
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
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Task</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="transition hover:bg-orange-50/70">
                            <td class="px-4 py-4 text-sm font-medium text-slate-700">Room 101</td>
                            <td class="px-4 py-4 text-sm text-slate-600">Deep Cleaning</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('housekeeping.room-status-update') }}"
                                    class="text-sm font-medium text-amber-700 transition hover:underline">
                                    Update
                                </a>
                            </td>
                        </tr>

                        <tr class="transition hover:bg-orange-50/70">
                            <td class="px-4 py-4 text-sm font-medium text-slate-700">Room 205</td>
                            <td class="px-4 py-4 text-sm text-slate-600">Change Linens</td>
                            <td class="px-4 py-4">
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Completed</span>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('housekeeping.room-status-update') }}"
                                    class="text-sm font-medium text-amber-700 transition hover:underline">
                                    Update
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-4 shadow-lg sm:p-6">
            <h3 class="text-lg font-semibold text-slate-800">Priority Tasks</h3>

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
@endsection

