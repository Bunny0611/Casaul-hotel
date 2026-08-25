@extends('employee.layout')

@section('pageTitle', 'Reservation Management')
@section('content')

@php
    $reservations = $reservations ?? collect([]);
    $roomReservations = $reservations;
    $amenityReservations = $reservations->where('category', 'amenities');
    $eventPlaceReservations = $reservations->where('category', 'event_place');
    $diningReservations = $reservations->where('category', 'dining');

    $stats = [
        'rooms' => [
            'total' => $roomReservations->count(),
            'pending' => $roomReservations->where('status', 'pending')->count(),
            'confirmed' => $roomReservations->where('status', 'confirmed')->count(),
            'completed' => $roomReservations->where('status', 'completed')->count(),
            'cancelled' => $roomReservations->where('status', 'cancelled')->count(),
        ],
        'amenities' => [
            'total' => $amenityReservations->count(),
            'pending' => $amenityReservations->where('status', 'pending')->count(),
            'confirmed' => $amenityReservations->where('status', 'confirmed')->count(),
            'completed' => $amenityReservations->where('status', 'completed')->count(),
            'cancelled' => $amenityReservations->where('status', 'cancelled')->count(),
        ],
        'event_place' => [
            'total' => $eventPlaceReservations->count(),
            'pending' => $eventPlaceReservations->where('status', 'pending')->count(),
            'confirmed' => $eventPlaceReservations->where('status', 'confirmed')->count(),
            'completed' => $eventPlaceReservations->where('status', 'completed')->count(),
            'cancelled' => $eventPlaceReservations->where('status', 'cancelled')->count(),
        ],
        'dining' => [
            'total' => $diningReservations->count(),
            'pending' => $diningReservations->where('status', 'pending')->count(),
            'confirmed' => $diningReservations->where('status', 'confirmed')->count(),
            'completed' => $diningReservations->where('status', 'completed')->count(),
            'cancelled' => $diningReservations->where('status', 'cancelled')->count(),
        ],
    ];
@endphp

<div class="animate-fade-in space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Reservation Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage guest bookings, update statuses, and create new reservations from one place.</p>
        </div>
        <button type="button" onclick="openAddReservationModal()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700" style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">
            <i class="fas fa-plus mr-2"></i>Add Reservation
        </button>
    </div>

    <div id="roomsTab" data-reservation-panel="rooms" class="space-y-4">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['rooms']['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['rooms']['pending'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['rooms']['confirmed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['rooms']['completed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Cancelled</p>
                <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['rooms']['cancelled'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Room</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Check-in</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Check-out</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($roomReservations as $reservation)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}<br><span class="text-xs text-gray-500">{{ $reservation->check_in_time ? \Illuminate\Support\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'Time not set' }}</span></td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_out instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_out->format('M d, Y') : $reservation->check_out }}<br><span class="text-xs text-gray-500">{{ $reservation->check_out_time ? \Illuminate\Support\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'Time not set' }}</span></td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative flex items-center gap-2 text-sm">
                                        <button type="button" onclick="showEmployeeReservationDetails(this)" data-guest="{{ $reservation->guest_name }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-check-in="{{ $reservation->check_in }}" data-check-out="{{ $reservation->check_out }}" data-amount="₱{{ number_format($reservation->total_amount, 2) }}" data-status="{{ ucfirst($reservation->status) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="employee-reservation-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                            @if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button>@endif
                                            @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-in</button>@endif
                                            @if($reservation->status === 'checked-in')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-out</button>@endif
                                            @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>@endif
                                            @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>@endif
                                            <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');">@csrf @method('DELETE')<button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button></form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                    <p class="text-lg font-medium">No room reservations found.</p>
                                    <p class="mt-1 text-sm">Create your first room reservation to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse($roomReservations as $reservation)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $reservation->guest_email }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium text-gray-700">Room:</span> {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Check-in:</span> {{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }} at {{ $reservation->check_in_time ? \Illuminate\Support\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'Time not set' }}</p>
                            <p><span class="font-medium text-gray-700">Check-out:</span> {{ $reservation->check_out instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_out->format('M d, Y') : $reservation->check_out }} at {{ $reservation->check_out_time ? \Illuminate\Support\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'Time not set' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" onclick="showEmployeeReservationDetails(this)" data-guest="{{ $reservation->guest_name }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-check-in="{{ $reservation->check_in }}" data-check-out="{{ $reservation->check_out }}" data-amount="₱{{ number_format($reservation->total_amount, 2) }}" data-status="{{ ucfirst($reservation->status) }}" class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                            <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <div class="relative"><button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"><i class="fas fa-ellipsis-v"></i></button><div class="employee-reservation-menu absolute bottom-10 right-0 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">@if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full px-3 py-2 text-left text-sm">Confirm Reservation</button>@endif @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="block w-full px-3 py-2 text-left text-sm">Mark as Checked-in</button>@endif @if($reservation->status === 'checked-in')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full px-3 py-2 text-left text-sm">Mark as Checked-out</button>@endif <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full px-3 py-2 text-left text-sm">Cancel Reservation</button><form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');">@csrf @method('DELETE')<button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600">Delete Reservation</button></form></div></div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                        <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                        <p class="text-lg font-medium">No room reservations found.</p>
                        <p class="mt-1 text-sm">Create your first room reservation to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="amenitiesTab" data-reservation-panel="amenities" class="hidden space-y-4">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['amenities']['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['amenities']['pending'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['amenities']['confirmed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['amenities']['completed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Cancelled</p>
                <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['amenities']['cancelled'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amenity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity/Guests</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($amenityReservations as $reservation)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->amenity_name ?? $reservation->amenity ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->date ?? $reservation->check_in ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->time ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->quantity ?? $reservation->guests ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm">
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 hover:text-red-800" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="Complete">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                    <p class="text-lg font-medium">No amenity reservations found.</p>
                                    <p class="mt-1 text-sm">Create your first amenity reservation to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse($amenityReservations as $reservation)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $reservation->guest_email }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium text-gray-700">Amenity:</span> {{ $reservation->amenity_name ?? $reservation->amenity ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $reservation->date ?? $reservation->check_in ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Time:</span> {{ $reservation->time ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Guests:</span> {{ $reservation->quantity ?? $reservation->guests ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">Confirm</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700">Cancel</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Complete</button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                        <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                        <p class="text-lg font-medium">No amenity reservations found.</p>
                        <p class="mt-1 text-sm">Create your first amenity reservation to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="eventPlaceTab" data-reservation-panel="event_place" class="hidden space-y-4">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['event_place']['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['event_place']['pending'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['event_place']['confirmed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['event_place']['completed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Cancelled</p>
                <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['event_place']['cancelled'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest/Client</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Place</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Start Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">End Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($eventPlaceReservations as $reservation)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_place ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_type ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_date ?? $reservation->check_in ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->start_time ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->end_time ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm">
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 hover:text-red-800" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="Complete">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center text-gray-500">
                                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                    <p class="text-lg font-medium">No event place reservations found.</p>
                                    <p class="mt-1 text-sm">Create your first event place reservation to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse($eventPlaceReservations as $reservation)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $reservation->guest_email }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium text-gray-700">Place:</span> {{ $reservation->event_place ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Type:</span> {{ $reservation->event_type ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $reservation->event_date ?? $reservation->check_in ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Start:</span> {{ $reservation->start_time ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">End:</span> {{ $reservation->end_time ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">Confirm</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700">Cancel</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Complete</button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                        <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                        <p class="text-lg font-medium">No event place reservations found.</p>
                        <p class="mt-1 text-sm">Create your first event place reservation to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="diningTab" data-reservation-panel="dining" class="hidden space-y-4">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['dining']['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['dining']['pending'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Confirmed</p>
                <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['dining']['confirmed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Completed</p>
                <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['dining']['completed'] }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Cancelled</p>
                <p class="mt-2 text-2xl font-semibold text-red-600">{{ $stats['dining']['cancelled'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dining Area/Table</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Number of Guests</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($diningReservations as $reservation)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->dining_area ?? $reservation->table_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->date ?? $reservation->check_in ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->time ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->number_of_guests ?? $reservation->guests ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-sm">
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 hover:text-red-800" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="Complete">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                    <p class="text-lg font-medium">No dining reservations found.</p>
                                    <p class="mt-1 text-sm">Create your first dining reservation to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="space-y-4 p-4 md:hidden">
                @forelse($diningReservations as $reservation)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $reservation->guest_email }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium text-gray-700">Table:</span> {{ $reservation->dining_area ?? $reservation->table_name ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $reservation->date ?? $reservation->check_in ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Time:</span> {{ $reservation->time ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Guests:</span> {{ $reservation->number_of_guests ?? $reservation->guests ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">Confirm</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700">Cancel</button>
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Complete</button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                        <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                        <p class="text-lg font-medium">No dining reservations found.</p>
                        <p class="mt-1 text-sm">Create your first dining reservation to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="addReservationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAddReservationModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 id="reservationModalTitle" class="text-2xl font-bold text-gray-800">Add New Reservation</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the details below to create a new booking.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="addReservationForm" action="{{ route('employee.reservations.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="reservationFormMethod" value="PUT" disabled>
            <input type="hidden" name="submission_token" value="{{ \Illuminate\Support\Str::uuid() }}">
            <input type="hidden" name="category" id="reservationCategory" value="{{ old('category', 'rooms') }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Email</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Phone</label>
                    <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select a room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->room_type }}</option>
                        @endforeach
                    </select>
                    @error('room_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" name="check_in" value="{{ old('check_in') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_in')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" name="check_out" value="{{ old('check_out') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_out')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-in Time</label>
                    <input type="time" name="check_in_time" value="{{ old('check_in_time') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_in_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-out Time</label>
                    <input type="time" name="check_out_time" value="{{ old('check_out_time') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_out_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" min="0" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('total_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Special Requests</label>
                <textarea name="special_requests" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('special_requests') }}</textarea>
                @error('special_requests')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button id="saveReservationBtn" type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Save Reservation</button>
            </div>
        </form>
    </div>
</div>

<div id="employeeReservationDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-center justify-between"><h3 class="text-xl font-bold text-gray-800">Reservation Details</h3><button type="button" onclick="closeEmployeeReservationDetails()" class="text-gray-500 hover:text-gray-800" aria-label="Close details"><i class="fas fa-times text-xl"></i></button></div>
        <div class="grid gap-3 text-sm sm:grid-cols-2"><div><span class="text-gray-500">Guest</span><p id="employeeDetailsGuest" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Room</span><p id="employeeDetailsRoom" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Check-in</span><p id="employeeDetailsCheckIn" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Check-out</span><p id="employeeDetailsCheckOut" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Amount</span><p id="employeeDetailsAmount" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Status</span><p id="employeeDetailsStatus" class="font-semibold text-gray-900"></p></div></div>
    </div>
</div>

<form id="reservationStatusForm" action="" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="reservationStatus">
</form>

<script>
    function changeReservationStatus(id, status) {
        if (confirm(`Are you sure you want to change the status to ${status}?`)) {
            var actionTemplate = "{{ route('employee.reservations.status', ['id' => '__ID__']) }}";
            document.getElementById('reservationStatusForm').action = actionTemplate.replace('__ID__', id);
            document.getElementById('reservationStatus').value = status;
            document.getElementById('reservationStatusForm').submit();
        }
    }

    function toggleEmployeeReservationMenu(button) {
        const menu = button.nextElementSibling;
        document.querySelectorAll('.employee-reservation-menu').forEach(item => {
            if (item !== menu) item.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    function showEmployeeReservationDetails(button) {
        document.getElementById('employeeDetailsGuest').textContent = button.dataset.guest || 'N/A';
        document.getElementById('employeeDetailsRoom').textContent = button.dataset.room || 'N/A';
        document.getElementById('employeeDetailsCheckIn').textContent = button.dataset.checkIn || 'N/A';
        document.getElementById('employeeDetailsCheckOut').textContent = button.dataset.checkOut || 'N/A';
        document.getElementById('employeeDetailsAmount').textContent = button.dataset.amount || 'N/A';
        document.getElementById('employeeDetailsStatus').textContent = button.dataset.status || 'N/A';
        const modal = document.getElementById('employeeReservationDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEmployeeReservationDetails() {
        const modal = document.getElementById('employeeReservationDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.employee-reservation-menu') && !event.target.closest('[title="More actions"]')) {
            document.querySelectorAll('.employee-reservation-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    function openAddReservationModal() {
        const modal = document.getElementById('addReservationModal');
        if (!modal) return;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
    }

    function resetAddReservationForm() {
        const form = document.getElementById('addReservationForm');
        const saveReservationBtn = document.getElementById('saveReservationBtn');
        const methodInput = document.getElementById('reservationFormMethod');
        if (!form) return;

        form.reset();
        methodInput.disabled = true;
        form.action = "{{ route('employee.reservations.store') }}";
        document.getElementById('reservationModalTitle').textContent = 'Add New Reservation';
        const categoryInput = document.getElementById('reservationCategory');
        if (categoryInput) {
            categoryInput.value = 'rooms';
        }

        const saveBtn = document.getElementById('saveReservationBtn');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Reservation';
            saveBtn.classList.remove('opacity-70', 'cursor-not-allowed');
        }
    }

    function editReservation(reservation) {
        const form = document.getElementById('addReservationForm');
        const fields = form.elements;
        const timeValue = value => value ? String(value).slice(0, 5) : '';

        fields.guest_name.value = reservation.guest_name || '';
        fields.guest_email.value = reservation.guest_email || '';
        fields.guest_phone.value = reservation.guest_phone || '';
        fields.room_id.value = reservation.room_id || '';
        fields.check_in.value = reservation.check_in || '';
        fields.check_in_time.value = timeValue(reservation.check_in_time);
        fields.check_out.value = reservation.check_out || '';
        fields.check_out_time.value = timeValue(reservation.check_out_time);
        fields.status.value = reservation.status || 'pending';
        fields.total_amount.value = reservation.total_amount || 0;
        fields.special_requests.value = reservation.special_requests || '';
        document.getElementById('reservationFormMethod').disabled = false;
        form.action = "{{ route('employee.reservations.update', ['id' => '__ID__']) }}".replace('__ID__', reservation.id);
        document.getElementById('reservationModalTitle').textContent = 'Edit Reservation';
        document.getElementById('saveReservationBtn').textContent = 'Update Reservation';
        openAddReservationModal();
    }

    document.getElementById('addReservationForm').addEventListener('submit', function (event) {
        const saveReservationBtn = document.getElementById('saveReservationBtn');

        if (saveReservationBtn.disabled) {
            event.preventDefault();
            return;
        }

        saveReservationBtn.disabled = true;
        saveReservationBtn.textContent = 'Saving...';
    });

    function closeAddReservationModal() {
        const modal = document.getElementById('addReservationModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.display = 'none';
        }
        resetAddReservationForm();
    }

</script>
@endsection
