@extends('housekeeping.layout')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Room Status Update</h2>
            <p class="mt-1 text-sm text-slate-500">Keep the housekeeping workflow clear, simple, and easy to follow.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-sm font-medium text-orange-700">
            <i class="fas fa-broom mr-2"></i> Housekeeping portal
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Cleaning Workflow</h3>
                <p class="mt-1 text-sm text-slate-500">A quick view of each stage in the room cleaning process.</p>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="rounded-xl border border-red-100 bg-red-50 p-3">
                    <div class="mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <i class="fas fa-trash"></i>
                    </div>
                    <p class="font-semibold text-red-700">Dirty</p>
                    <p class="text-xs text-red-500">Needs attention</p>
                </div>

                <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                    <div class="mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fas fa-broom"></i>
                    </div>
                    <p class="font-semibold text-amber-700">Cleaning</p>
                    <p class="text-xs text-amber-500">In progress</p>
                </div>

                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                    <div class="mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="font-semibold text-emerald-700">Cleaned</p>
                    <p class="text-xs text-emerald-500">Ready for review</p>
                </div>

                <div class="rounded-xl border border-sky-100 bg-sky-50 p-3">
                    <div class="mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <p class="font-semibold text-sky-700">Inspected</p>
                    <p class="text-xs text-sky-500">Quality checked</p>
                </div>

                <div class="rounded-xl border border-violet-100 bg-violet-50 p-3">
                    <div class="mb-2 flex h-11 w-11 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <p class="font-semibold text-violet-700">Available</p>
                    <p class="text-xs text-violet-500">Ready for guests</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-slate-900">Update Room Status</h3>
                <p class="mt-1 text-sm text-slate-500">Select a room and apply its latest state.</p>
            </div>

            <form class="space-y-4">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Select Room</label>
                        <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Room 101</option>
                            <option>Room 205</option>
                            <option>Room 302</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Current Status</label>
                        <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Dirty</option>
                            <option>Cleaning</option>
                            <option>Cleaned</option>
                            <option>Inspected</option>
                            <option>Available</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Update Status</label>
                        <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100">
                            <option>Dirty</option>
                            <option>Cleaning</option>
                            <option>Cleaned</option>
                            <option>Inspected</option>
                            <option>Available</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95 sm:w-auto">
                    <i class="fas fa-sync-alt mr-2"></i> Update Status
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Room Status Monitoring</h3>
                <p class="text-sm text-slate-500">A simple overview of recent room updates.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-600">Updated today</span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Room Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Last Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">101</td>
                        <td class="px-4 py-3 text-slate-600">Deluxe Room</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Dirty</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">Jul 31, 2026 09:30 AM</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">205</td>
                        <td class="px-4 py-3 text-slate-600">Suite Room</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Cleaning</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">Jul 31, 2026 10:15 AM</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 font-semibold text-slate-800">302</td>
                        <td class="px-4 py-3 text-slate-600">Standard Room</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">Available</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">Jul 31, 2026 11:00 AM</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
