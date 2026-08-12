@extends('housekeeping.layout')

@section('content')
<div class="animate-fade-in space-y-8">
    <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-amber-600">Housekeeping dashboard</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Room cleaning overview</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Stay on top of room readiness, cleaning progress, and priority tasks from a streamlined, professional interface.</p>
            </div>
            <div class="rounded-3xl bg-slate-50 px-4 py-3 text-sm text-slate-700 shadow-sm">
                <span class="font-semibold">Today</span>
                <div class="mt-1 text-slate-500">August 7, 2026</div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-5">
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Total rooms</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalRooms }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Clean rooms</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-700">{{ $cleanRooms }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Dirty rooms</p>
            <p class="mt-3 text-3xl font-semibold text-red-700">{{ $dirtyRooms }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">In progress</p>
            <p class="mt-3 text-3xl font-semibold text-amber-700">{{ $inProgress }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm border border-slate-200">
            <p class="text-sm text-slate-500">Occupied rooms</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $occupiedRooms }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Room cleaning status</h2>
                <p class="mt-1 text-sm text-slate-500">Update cleaning progress and view room readiness in one place.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">Clean</span>
                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-red-700">Dirty</span>
                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-amber-700">In Progress</span>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($rooms as $room)
            <div class="rounded-3xl border p-5 shadow-sm transition hover:-translate-y-0.5 {{ $room->cleaning_status === 'clean' ? 'border-emerald-200 bg-emerald-50' : ($room->cleaning_status === 'dirty' ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50') }}">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $room->room_number }}</p>
                        <p class="text-sm text-slate-500">{{ $room->room_type }}</p>
                    </div>
                    <span class="h-3 w-3 rounded-full {{ $room->cleaning_status === 'clean' ? 'bg-emerald-700' : ($room->cleaning_status === 'dirty' ? 'bg-red-700' : 'bg-amber-700') }}"></span>
                </div>

                <div class="space-y-2 text-sm text-slate-600">
                    <p>Floor: {{ $room->floor ?? 'N/A' }}</p>
                    <p>Status:
                        <span class="inline-flex rounded-full bg-slate-900 px-2 py-0.5 text-xs font-semibold text-white">{{ ucfirst($room->status) }}</span>
                    </p>
                    <p>Cleaning: {{ ucfirst(str_replace('_', ' ', $room->cleaning_status)) }}</p>
                </div>

                <form method="POST" action="{{ route('housekeeping.rooms.cleaning', $room->id) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Update cleaning status</label>
                    <select name="cleaning_status" onchange="this.form.submit()" class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-100">
                        <option value="clean" {{ $room->cleaning_status === 'clean' ? 'selected' : '' }}>Clean</option>
                        <option value="dirty" {{ $room->cleaning_status === 'dirty' ? 'selected' : '' }}>Dirty</option>
                        <option value="in_progress" {{ $room->cleaning_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </form>
            </div>
            @empty
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-12 text-center text-slate-500">
                <i class="fas fa-bed mb-4 text-4xl text-slate-300"></i>
                <p class="text-sm">No rooms available for housekeeping.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection