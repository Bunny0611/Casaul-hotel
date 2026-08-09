@extends('housekeeping.layout')

@section('content')

<main class="flex-1 overflow-y-auto p-4 md:p-6">
    <div class="animate-fade-in space-y-8">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-600">
                    Maintenance overview
                </p>
                <h2 class="text-2xl font-bold text-slate-800 sm:text-3xl">
                    Maintenance Report
                </h2>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">
                    Track room repairs, pending issues, and completed maintenance work in one dashboard.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full lg:w-auto">
                <div class="relative w-full sm:w-72">
                    <input id="reportSearch" type="search" placeholder="Search reports..." class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"/>
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>

                <button
                    onclick="openReportModal()"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-200/40 transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 sm:w-48"
                >
                    <i class="fas fa-plus"></i>
                    Create Report
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <!-- TOTAL REPORTS -->
            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Total Reports</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">2</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-blue-600">
                            <i class="fas fa-file-alt mr-1"></i>
                            All time records
                        </p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100">
                        <i class="fas fa-file-alt text-xl text-blue-700"></i>
                    </div>
                </div>
            </div>

            <!-- PENDING -->
            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Pending Issues</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">1</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-amber-600">
                            <i class="fas fa-clock mr-1"></i>
                            Awaiting action
                        </p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
                        <i class="fas fa-clock text-xl text-amber-700"></i>
                    </div>
                </div>
            </div>

            <!-- REPAIR -->
            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Under Repair</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">1</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-orange-600">
                            <i class="fas fa-wrench mr-1"></i>
                            In progress
                        </p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-orange-100">
                        <i class="fas fa-wrench text-xl text-orange-700"></i>
                    </div>
                </div>
            </div>

            <!-- COMPLETED -->
            <div class="card-hover rounded-2xl bg-white p-5 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-slate-500">Completed</p>
                        <p class="mt-2 text-3xl font-bold text-slate-800">0</p>
                        <p class="mt-2 flex items-center text-xs font-medium text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            Resolved issues
                        </p>
                    </div>
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-100">
                        <i class="fas fa-check-circle text-xl text-emerald-700"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAINTENANCE TABLE -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-lg">
            <!-- Table toolbar -->
            <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        Maintenance Records
                    </h3>
                    <p class="text-sm text-slate-500">
                        Showing all reported maintenance issues
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                        All (2)
                    </span>
                    <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">
                        Pending (1)
                    </span>
                    <span class="rounded-full bg-orange-100 px-3 py-1.5 text-xs font-semibold text-orange-700">
                        Repairing (1)
                    </span>
                    <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                        Completed (0)
                    </span>
                </div>
            </div>

<!-- Desktop table -->
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[640px]">
                    <thead>
<tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-3 py-3">Room</th>
                            <th class="px-3 py-3">Reported By</th>
                            <th class="px-3 py-3">Category / Priority</th>
                            <th class="px-3 py-3">Problem</th>
                            <th class="px-3 py-3">Date Reported</th>
                            <th class="px-3 py-3">Technician</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
<tr class="transition hover:bg-orange-50/70">
                            <td class="px-3 py-3">
                                <div class="font-semibold text-slate-800">Room 101</div>
                                <div class="text-xs text-slate-500">Deluxe Room</div>
                            </td>
<td class="px-3 py-3 text-sm text-slate-600">Maria Santos</td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Air Conditioning</span>
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">High</span>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-slate-800">Air Conditioner</p>
                                <p class="text-xs text-slate-500">Not cooling properly</p>
                            </td>
                            <td class="px-3 py-3 text-sm text-slate-600">Jul 31, 2026</td>
                            <td class="px-3 py-3 text-sm text-slate-600">John Reyes</td>
                            <td class="px-3 py-3">
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                            </td>
<td class="px-3 py-3">
                                <div class="flex items-center gap-1">
                                    <button
                                        onclick="openDetailModal('Room 101', 'Deluxe Room', 'Maria Santos', 'Air Conditioning', 'Air Conditioner', 'Not cooling properly', 'High', 'Jul 31, 2026', 'Aug 2, 2026', 'John Reyes', 'Pending')"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                        View
                                    </button>
                                    <button
                                        onclick="openEditModal('Room 101', 'Deluxe Room', 'Maria Santos', 'Air Conditioning', 'Air Conditioner', 'Not cooling properly', 'High', 'Jul 31, 2026', 'Aug 2, 2026', 'John Reyes', 'Pending')"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50"
                                    >
                                        <i class="fas fa-pen text-xs"></i>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>

<tr class="transition hover:bg-orange-50/70">
                            <td class="px-3 py-3">
                                <div class="font-semibold text-slate-800">Room 205</div>
                                <div class="text-xs text-slate-500">Suite Room</div>
                            </td>
<td class="px-3 py-3 text-sm text-slate-600">Ana Cruz</td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Plumbing</span>
                                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Medium</span>
                                </div>
                            </td>
                            <td class="px-3 py-3">
                                <p class="font-medium text-slate-800">Bathroom Faucet</p>
                                <p class="text-xs text-slate-500">Water leakage</p>
                            </td>
                            <td class="px-3 py-3 text-sm text-slate-600">Jul 30, 2026</td>
                            <td class="px-3 py-3 text-sm text-slate-600">Pedro Lim</td>
<td class="px-3 py-3">
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Repairing</span>
                            </td>
<td class="px-3 py-3">
                                <div class="flex items-center gap-1">
                                    <button
                                        onclick="openDetailModal('Room 205', 'Suite Room', 'Ana Cruz', 'Plumbing', 'Bathroom Faucet', 'Water leakage', 'Medium', 'Jul 30, 2026', 'Aug 1, 2026', 'Pedro Lim', 'Repairing')"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                        View
                                    </button>
                                    <button
                                        onclick="openEditModal('Room 205', 'Suite Room', 'Ana Cruz', 'Plumbing', 'Bathroom Faucet', 'Water leakage', 'Medium', 'Jul 30, 2026', 'Aug 1, 2026', 'Pedro Lim', 'Repairing')"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50"
                                    >
                                        <i class="fas fa-pen text-xs"></i>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile card list -->
            <div class="divide-y divide-slate-100 md:hidden">
                <!-- Card: Room 101 -->
<div class="p-4">
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">Room 101</p>
                            <p class="text-xs text-slate-500">Deluxe Room</p>
                        </div>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                    </div>
                    <p class="text-sm font-medium text-slate-800">Air Conditioner</p>
                    <p class="text-xs text-slate-500">Not cooling properly</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
<span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">High</span>
                        <span class="text-xs text-slate-500">Jul 31, 2026</span>
                        <span class="text-xs text-slate-500">Expected: Aug 2, 2026</span>
<button
                            onclick="openDetailModal('Room 101', 'Deluxe Room', 'Maria Santos', 'Air Conditioning', 'Air Conditioner', 'Not cooling properly', 'High', 'Jul 31, 2026', 'Aug 2, 2026', 'John Reyes', 'Pending')"
                            class="ml-auto inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                        >
                            <i class="fas fa-eye text-xs"></i>
                            View
                        </button>
                        <button
                            onclick="openEditModal('Room 101', 'Deluxe Room', 'Maria Santos', 'Air Conditioning', 'Air Conditioner', 'Not cooling properly', 'High', 'Jul 31, 2026', 'Aug 2, 2026', 'John Reyes', 'Pending')"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50"
                        >
                            <i class="fas fa-pen text-xs"></i>
                            Edit
                        </button>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-1 border-t border-slate-100 pt-3 text-xs text-slate-500 sm:grid-cols-2">
                        <p><span class="font-semibold text-slate-600">Reported By:</span> Maria Santos</p>
                        <p><span class="font-semibold text-slate-600">Category:</span> Air Conditioning</p>
                        <p><span class="font-semibold text-slate-600">Technician:</span> John Reyes</p>
                    </div>
                </div>

<!-- Card: Room 205 -->
                <div class="p-4">
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">Room 205</p>
                            <p class="text-xs text-slate-500">Suite Room</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Repairing</span>
                    </div>
                    <p class="text-sm font-medium text-slate-800">Bathroom Faucet</p>
                    <p class="text-xs text-slate-500">Water leakage</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
<span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Medium</span>
                        <span class="text-xs text-slate-500">Jul 30, 2026</span>
                        <span class="text-xs text-slate-500">Expected: Aug 1, 2026</span>
<button
                            onclick="openDetailModal('Room 205', 'Suite Room', 'Ana Cruz', 'Plumbing', 'Bathroom Faucet', 'Water leakage', 'Medium', 'Jul 30, 2026', 'Aug 1, 2026', 'Pedro Lim', 'Repairing')"
                            class="ml-auto inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50"
                        >
                            <i class="fas fa-eye text-xs"></i>
                            View
                        </button>
                        <button
                            onclick="openEditModal('Room 205', 'Suite Room', 'Ana Cruz', 'Plumbing', 'Bathroom Faucet', 'Water leakage', 'Medium', 'Jul 30, 2026', 'Aug 1, 2026', 'Pedro Lim', 'Repairing')"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-50"
                        >
                            <i class="fas fa-pen text-xs"></i>
                            Edit
                        </button>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-1 border-t border-slate-100 pt-3 text-xs text-slate-500 sm:grid-cols-2">
                        <p><span class="font-semibold text-slate-600">Reported By:</span> Ana Cruz</p>
                        <p><span class="font-semibold text-slate-600">Category:</span> Plumbing</p>
                        <p><span class="font-semibold text-slate-600">Technician:</span> Pedro Lim</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- CREATE MAINTENANCE REPORT MODAL -->
<div
    id="reportModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-5 opacity-0 transition-opacity duration-200"
>
        <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
<!-- MODAL HEADER -->
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800 sm:text-xl">
                    Create Maintenance Report
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    Record a new maintenance issue for a room.
                </p>
            </div>
            <button
                onclick="closeReportModal()"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-2xl text-slate-400 transition hover:bg-slate-100 hover:text-red-600"
                aria-label="Close"
            >
                &times;
            </button>
        </div>

<form class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
            <!-- ROOM INFORMATION -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-door-open text-orange-600"></i>
                    Room Information
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Room Number</label>
                        <select class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Room 101</option>
                            <option>Room 205</option>
                            <option>Room 302</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-600">Room Type</label>
                        <select class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Deluxe Room</option>
                            <option>Suite Room</option>
                            <option>Standard Room</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-600">Reported By</label>
                        <input
                            type="text"
                            placeholder="Housekeeper Name"
                            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        />
                    </div>
                </div>
            </div>

            <!-- ISSUE DETAILS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                    Issue Details
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Maintenance Category</label>
                        <select class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Air Conditioning</option>
                            <option>Electrical</option>
                            <option>Plumbing</option>
                            <option>Furniture</option>
                            <option>Appliance</option>
                            <option>Others</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-600">Priority Level</label>
                        <select class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-600">Problem Description</label>
                    <textarea
                        rows="3"
                        placeholder="Example: Air conditioner is not cooling properly."
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    ></textarea>
                </div>
            </div>

            <!-- REPAIR SCHEDULE -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-calendar-alt text-orange-600"></i>
                    Repair Schedule
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Date Reported</label>
                        <input
                            type="date"
                            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-600">Expected Repair Date</label>
                        <input
                            type="date"
                            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-600">Assigned Technician</label>
                        <input
                            type="text"
                            placeholder="Technician Name"
                            class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        />
                    </div>
                </div>
            </div>

            <!-- STATUS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-tasks text-orange-600"></i>
                    Status
                </h3>

                <div>
                    <label class="text-sm font-medium text-slate-600">Current Status</label>
                    <select class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100 sm:max-w-xs">
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                    </select>
                </div>
            </div>
            
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeReportModal()"
                    class="w-full rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 sm:w-auto"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-200/40 transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 sm:w-auto"
                >
                    <i class="fas fa-save"></i>
                    Submit Report
                </button>
            </div>
</form>
    </div>
</div>

<!-- VIEW MAINTENANCE REPORT DETAILS MODAL -->
<div
    id="detailModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-5 opacity-0 transition-opacity duration-200"
>
    <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <!-- MODAL HEADER -->
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800 sm:text-xl">
                    Maintenance Report Details
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    Full details of the reported maintenance issue.
                </p>
            </div>
            <button
                onclick="closeDetailModal()"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-2xl text-slate-400 transition hover:bg-slate-100 hover:text-red-600"
                aria-label="Close"
            >
                &times;
            </button>
        </div>

        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto p-5">
            <!-- ROOM INFORMATION -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-door-open text-orange-600"></i>
                    Room Information
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Room Number</p>
                        <p id="detailRoom" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Room Type</p>
                        <p id="detailRoomType" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500">Reported By</p>
                        <p id="detailReportedBy" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                </div>
            </div>

            <!-- ISSUE DETAILS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                    Issue Details
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Category</p>
                        <p id="detailCategory" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Priority</p>
                        <p id="detailPriority" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500">Problem</p>
                        <p id="detailProblem" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500">Description</p>
                        <p id="detailDescription" class="mt-1 text-sm text-slate-600">-</p>
                    </div>
                </div>
            </div>

            <!-- REPAIR SCHEDULE -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-calendar-alt text-orange-600"></i>
                    Repair Schedule
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Date Reported</p>
                        <p id="detailDateReported" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <p class="text-xs font-medium text-slate-500">Expected Repair Date</p>
                        <p id="detailExpectedDate" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 sm:col-span-2">
                        <p class="text-xs font-medium text-slate-500">Assigned Technician</p>
                        <p id="detailTechnician" class="mt-1 text-sm font-semibold text-slate-800">-</p>
                    </div>
                </div>
            </div>

            <!-- STATUS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-tasks text-orange-600"></i>
                    Status
                </h3>
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                    <p class="text-xs font-medium text-slate-500">Current Status</p>
                    <span id="detailStatus" class="mt-2 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">-</span>
                </div>
            </div>

<div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeDetailModal()"
                    class="w-full rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 sm:w-auto"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MAINTENANCE REPORT MODAL -->
<div
    id="editModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-5 opacity-0 transition-opacity duration-200"
>
    <div class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <!-- MODAL HEADER -->
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800 sm:text-xl">
                    Edit Maintenance Report
                </h2>
                <p class="text-xs text-slate-500 sm:text-sm">
                    Update the details of the reported maintenance issue.
                </p>
            </div>
            <button
                onclick="closeEditModal()"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-2xl text-slate-400 transition hover:bg-slate-100 hover:text-red-600"
                aria-label="Close"
            >
                &times;
            </button>
        </div>

        <form class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
            <!-- ROOM INFORMATION -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-door-open text-orange-600"></i>
                    Room Information
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Room Number</label>
                        <select id="editRoom" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Room 101</option>
                            <option>Room 205</option>
                            <option>Room 302</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Room Type</label>
                        <select id="editRoomType" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Deluxe Room</option>
                            <option>Suite Room</option>
                            <option>Standard Room</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-600">Reported By</label>
                        <input id="editReportedBy" type="text" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100" />
                    </div>
                </div>
            </div>

            <!-- ISSUE DETAILS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                    Issue Details
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Maintenance Category</label>
                        <select id="editCategory" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Air Conditioning</option>
                            <option>Electrical</option>
                            <option>Plumbing</option>
                            <option>Furniture</option>
                            <option>Appliance</option>
                            <option>Others</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Priority Level</label>
                        <select id="editPriority" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-600">Problem</label>
                    <input id="editProblem" type="text" placeholder="Problem title" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100" />
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-slate-600">Problem Description</label>
                    <textarea id="editDescription" rows="3" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"></textarea>
                </div>
            </div>

            <!-- REPAIR SCHEDULE -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-calendar-alt text-orange-600"></i>
                    Repair Schedule
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600">Date Reported</label>
                        <input id="editDateReported" type="text" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600">Expected Repair Date</label>
                        <input id="editExpectedDate" type="text" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-600">Assigned Technician</label>
                        <input id="editTechnician" type="text" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100" />
                    </div>
                </div>
            </div>

            <!-- STATUS -->
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                    <i class="fas fa-tasks text-orange-600"></i>
                    Status
                </h3>
                <div>
                    <label class="text-sm font-medium text-slate-600">Current Status</label>
                    <select id="editStatus" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100 sm:max-w-xs">
                        <option>Pending</option>
                        <option>Repairing</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="w-full rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 sm:w-auto"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    onclick="closeEditModal()"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-200/40 transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300 sm:w-auto"
                >
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDetailModal(room, roomType, reportedBy, category, problem, description, priority, dateReported, expectedDate, technician, status) {
        document.getElementById("detailRoom").textContent = room;
        document.getElementById("detailRoomType").textContent = roomType;
        document.getElementById("detailReportedBy").textContent = reportedBy;
        document.getElementById("detailCategory").textContent = category;
        document.getElementById("detailPriority").textContent = priority;
        document.getElementById("detailProblem").textContent = problem;
        document.getElementById("detailDescription").textContent = description;
        document.getElementById("detailDateReported").textContent = dateReported;
        document.getElementById("detailExpectedDate").textContent = expectedDate;
        document.getElementById("detailTechnician").textContent = technician;

        const statusEl = document.getElementById("detailStatus");
        statusEl.textContent = status;
        // Reset classes
        statusEl.className = "mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold";
        if (status === "Pending") {
            statusEl.classList.add("bg-amber-100", "text-amber-700");
        } else if (status === "Repairing" || status === "In Progress") {
            statusEl.classList.add("bg-blue-100", "text-blue-700");
        } else if (status === "Completed") {
            statusEl.classList.add("bg-emerald-100", "text-emerald-700");
        } else {
            statusEl.classList.add("bg-slate-100", "text-slate-600");
        }

        const modal = document.getElementById("detailModal");
        modal.classList.remove("hidden");
        requestAnimationFrame(() => {
            modal.classList.add("flex", "opacity-100");
        });
        document.body.style.overflow = "hidden";
    }

    function closeDetailModal() {
        const modal = document.getElementById("detailModal");
        modal.classList.remove("opacity-100");
        setTimeout(() => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }, 200);
document.body.style.overflow = "";
    }

    // Close on backdrop click
    document.addEventListener("click", function (e) {
        const modal = document.getElementById("detailModal");
        if (e.target === modal) {
            closeDetailModal();
        }
    });

    // Close on Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeDetailModal();
        }
    });

    function openEditModal(room, roomType, reportedBy, category, problem, description, priority, dateReported, expectedDate, technician, status) {
        document.getElementById("editRoom").value = room;
        document.getElementById("editRoomType").value = roomType;
        document.getElementById("editReportedBy").value = reportedBy;
        document.getElementById("editCategory").value = category;
        document.getElementById("editPriority").value = priority;
        document.getElementById("editProblem").value = problem;
        document.getElementById("editDescription").value = description;
        document.getElementById("editDateReported").value = dateReported;
        document.getElementById("editExpectedDate").value = expectedDate;
        document.getElementById("editTechnician").value = technician;
        document.getElementById("editStatus").value = status;

        const modal = document.getElementById("editModal");
        modal.classList.remove("hidden");
        requestAnimationFrame(() => {
            modal.classList.add("flex", "opacity-100");
        });
        document.body.style.overflow = "hidden";
    }

    function closeEditModal() {
        const modal = document.getElementById("editModal");
        modal.classList.remove("opacity-100");
        setTimeout(() => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }, 200);
        document.body.style.overflow = "";
    }

    // Close on backdrop click
    document.addEventListener("click", function (e) {
        const modal = document.getElementById("editModal");
        if (e.target === modal) {
            closeEditModal();
        }
    });

    // Close on Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeEditModal();
        }
    });

    function openReportModal() {
        const modal = document.getElementById("reportModal");
        modal.classList.remove("hidden");
        // trigger transition
        requestAnimationFrame(() => {
            modal.classList.add("flex", "opacity-100");
        });
        document.body.style.overflow = "hidden";
    }

    function closeReportModal() {
        const modal = document.getElementById("reportModal");
        modal.classList.remove("opacity-100");
        setTimeout(() => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }, 200);
        document.body.style.overflow = "";
    }

    // Close on backdrop click
    document.addEventListener("click", function (e) {
        const modal = document.getElementById("reportModal");
        if (e.target === modal) {
            closeReportModal();
        }
    });

    // Close on Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeReportModal();
        }
    });
</script>

@endsection

