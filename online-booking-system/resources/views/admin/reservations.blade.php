@extends('admin.layout')

@section('content')
<style>
    .reservation-management-page {
        font-size: 16px;
        -webkit-text-size-adjust: 100%;
        text-size-adjust: 100%;
    }

    .reservation-management-page .reservation-table-shell {
        min-width: 980px;
    }

    .reservation-management-page .reservation-table-shell table {
        width: 100%;
        min-width: 980px;
    }
</style>
@php
    // Calculate stats for each reservation type
    $roomStats = [
        'total' => $roomReservations->count(),
        'pending' => $roomReservations->where('status', 'pending')->count(),
        'confirmed' => $roomReservations->where('status', 'confirmed')->count(),
        'completed' => $roomReservations->where('status', 'completed')->count(),
        'cancelled' => $roomReservations->where('status', 'cancelled')->count(),
    ];
    
    $amenityStats = [
        'total' => $amenityReservations->count(),
        'pending' => $amenityReservations->where('status', 'pending')->count(),
        'confirmed' => $amenityReservations->where('status', 'confirmed')->count(),
        'completed' => $amenityReservations->where('status', 'completed')->count(),
        'cancelled' => $amenityReservations->where('status', 'cancelled')->count(),
    ];
    
    $eventPlaceStats = [
        'total' => $eventPlaceReservations->count(),
        'pending' => $eventPlaceReservations->where('status', 'pending')->count(),
        'confirmed' => $eventPlaceReservations->where('status', 'confirmed')->count(),
        'completed' => $eventPlaceReservations->where('status', 'completed')->count(),
        'cancelled' => $eventPlaceReservations->where('status', 'cancelled')->count(),
    ];
    
    $diningStats = [
        'total' => $diningReservations->count(),
        'pending' => $diningReservations->where('status', 'pending')->count(),
        'confirmed' => $diningReservations->where('status', 'confirmed')->count(),
        'completed' => $diningReservations->where('status', 'completed')->count(),
        'cancelled' => $diningReservations->where('status', 'cancelled')->count(),
    ];
    
    $currentStats = $roomStats;
@endphp

<div class="reservation-management-page animate-fade-in space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm sm:p-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Reservation Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage guest bookings and update reservation statuses from one place.</p>
        </div>
    </div>

    <!-- Tab Buttons -->
    <div class="mb-6 flex flex-wrap gap-2 sm:gap-4">
        <button type="button" data-tab="rooms" class="tab-button rounded-lg bg-orange-500 px-6 py-3 font-medium text-white transition hover:bg-orange-600">ROOMS</button>
        <button type="button" data-tab="amenities" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">AMENITIES</button>
        <button type="button" data-tab="event-place" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">EVENT PLACE</button>
        <button type="button" data-tab="dining" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">DINING</button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Search</label>
            <input id="reservationSearch" type="search" placeholder="Search by guest, email, or item" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200" />
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Status filter</label>
            <select id="reservationStatusFilter" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Stats Section -->
    <div data-panel-stats="rooms" class="grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $roomStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $roomStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $roomStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $roomStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $roomStats['cancelled'] }}</p>
        </div>
    </div>

    <div data-panel-stats="amenities" class="hidden grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $amenityStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $amenityStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $amenityStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $amenityStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $amenityStats['cancelled'] }}</p>
        </div>
    </div>

    <div data-panel-stats="event-place" class="hidden grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $eventPlaceStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $eventPlaceStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $eventPlaceStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $eventPlaceStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $eventPlaceStats['cancelled'] }}</p>
        </div>
    </div>

    <div data-panel-stats="dining" class="hidden grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $diningStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $diningStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $diningStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $diningStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $diningStats['cancelled'] }}</p>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-gray-800">Reservation List</h3>

    <!-- ROOMS PANEL -->
    <div data-panel="rooms" class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
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
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->room?->room_number ?? '')) }}">
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
                                    <button type="button" onclick="showReservationDetails(this)" data-guest="{{ $reservation->guest_name }}" data-email="{{ $reservation->guest_email }}" data-phone="{{ $reservation->guest_phone }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-check-in="{{ $reservation->check_in }}" data-check-out="{{ $reservation->check_out }}" data-amount="₱{{ number_format($reservation->total_amount, 2) }}" data-status="{{ ucfirst($reservation->status) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details" aria-label="View details"><i class="fas fa-eye"></i></button>
                                    <button type="button" onclick='openAdminEditReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation" aria-label="Edit reservation"><i class="fas fa-pen"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button> @endif
                                        @if($reservation->status === 'confirmed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-in</button> @endif
                                        @if($reservation->status === 'checked-in') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-out</button> @endif
                                        @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button> @endif
                                        @if($reservation->status === 'completed') <button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print/Download Receipt</button> @endif
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2 w-4"></i>Delete Reservation</button>@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No room reservations found.</p>
                                <p class="mt-1 text-sm">Room reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AMENITIES PANEL -->
    <div data-panel="amenities" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amenity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($amenityReservations as $reservation)
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->amenity?->name ?? '')) }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->amenity ? $reservation->amenity->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick='openAdminEditReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation" aria-label="Edit reservation"><i class="fas fa-pen"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel</button> @endif
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2 w-4"></i>Delete</button>@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No amenity reservations found.</p>
                                <p class="mt-1 text-sm">Amenity reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- EVENT PLACE PANEL -->
    <div data-panel="event-place" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Place</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guests</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($eventPlaceReservations as $reservation)
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->eventPlace?->name ?? '')) }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->eventPlace ? $reservation->eventPlace->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->number_of_guests ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick='openAdminEditReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation" aria-label="Edit reservation"><i class="fas fa-pen"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel</button> @endif
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2 w-4"></i>Delete</button>@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No event place reservations found.</p>
                                <p class="mt-1 text-sm">Event place reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DINING PANEL -->
    <div data-panel="dining" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
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
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->dining_area ?? '')) }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->dining_area ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->dining_schedule ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->number_of_guests ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick='openAdminEditReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation" aria-label="Edit reservation"><i class="fas fa-pen"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel</button> @endif
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2 w-4"></i>Delete</button>@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No dining reservations found.</p>
                                <p class="mt-1 text-sm">Dining reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="adminEditReservationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-center justify-between"><h3 class="text-xl font-bold text-gray-800">Edit Reservation</h3><button type="button" onclick="closeAdminEditReservation()" class="text-gray-500 hover:text-gray-800" aria-label="Close edit"><i class="fas fa-times text-xl"></i></button></div>
        <form id="adminEditReservationForm" method="POST" class="grid grid-cols-1 gap-4 sm:grid-cols-2">@csrf @method('PUT')
            <input name="guest_name" id="adminEditGuestName" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Guest name" required>
            <input name="guest_email" id="adminEditGuestEmail" type="email" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Guest email" required>
            <input name="guest_phone" id="adminEditGuestPhone" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Guest phone" required>
            <select name="room_id" id="adminEditRoom" class="rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Select a room</option>
                @foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>@endforeach
            </select>
            <select name="amenity_id" id="adminEditAmenity" class="rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Select an amenity</option>
                @foreach(($amenities ?? collect()) as $amenity)<option value="{{ $amenity->id }}">{{ $amenity->name }}</option>@endforeach
            </select>
            <select name="event_place_id" id="adminEditEventPlace" class="rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Select an event place</option>
                @foreach(($eventPlaces ?? collect()) as $place)<option value="{{ $place->id }}">{{ $place->name }}</option>@endforeach
            </select>
            <select name="dining_id" id="adminEditDiningMenu" class="rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Select a menu</option>
                @foreach(($diningMenus ?? collect()) as $menu)<option value="{{ $menu->id }}" data-price="{{ (float) $menu->price }}">{{ $menu->name }} - ₱{{ number_format((float) $menu->price, 2) }}</option>@endforeach
            </select>
            <input name="dining_area" id="adminEditDiningArea" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Dining area">
            <input name="dining_schedule" id="adminEditDiningSchedule" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Dining schedule">
            <input name="number_of_guests" id="adminEditNumberOfGuests" type="number" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Number of guests">
            <input name="check_in" id="adminEditCheckIn" type="date" class="rounded-lg border border-gray-300 px-3 py-2">
            <input name="check_in_time" id="adminEditCheckInTime" type="time" class="rounded-lg border border-gray-300 px-3 py-2">
            <input name="check_out" id="adminEditCheckOut" type="date" class="rounded-lg border border-gray-300 px-3 py-2">
            <input name="check_out_time" id="adminEditCheckOutTime" type="time" class="rounded-lg border border-gray-300 px-3 py-2">
            <select name="status" id="adminEditStatus" class="rounded-lg border border-gray-300 px-3 py-2" required><option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="checked-in">Checked-in</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
            <input name="total_amount" id="adminEditAmount" type="number" min="0" step="0.01" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Total amount" required>
            <textarea name="special_requests" id="adminEditRequests" class="sm:col-span-2 rounded-lg border border-gray-300 px-3 py-2" rows="3" placeholder="Special requests"></textarea>
            <div class="flex justify-end gap-3 sm:col-span-2"><button type="button" onclick="closeAdminEditReservation()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Save Changes</button></div>
        </form>
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
            var actionTemplate = "{{ route('admin.reservations.status', ['id' => '__ID__']) }}";
            document.getElementById('reservationStatusForm').action = actionTemplate.replace('__ID__', id);
            document.getElementById('reservationStatus').value = status;
            document.getElementById('reservationStatusForm').submit();
        }
    }

    function toggleReservationMenu(button) {
        const menu = button.nextElementSibling;
        document.querySelectorAll('.reservation-action-menu').forEach(item => {
            if (item !== menu) item.classList.add('hidden');
        });
        menu.classList.toggle('hidden');
    }

    function openAdminEditReservation(reservation) {
        const form = document.getElementById('adminEditReservationForm');
        form.action = "{{ route('admin.reservations.update', ['id' => '__ID__']) }}".replace('__ID__', reservation.id);
        document.getElementById('adminEditGuestName').value = reservation.guest_name || '';
        document.getElementById('adminEditGuestEmail').value = reservation.guest_email || '';
        document.getElementById('adminEditGuestPhone').value = reservation.guest_phone || '';
        document.getElementById('adminEditRoom').value = reservation.room_id || '';
        document.getElementById('adminEditAmenity').value = reservation.amenity_id || '';
        document.getElementById('adminEditEventPlace').value = reservation.event_place_id || '';
        document.getElementById('adminEditDiningMenu').value = reservation.dining_id || '';
        document.getElementById('adminEditDiningArea').value = reservation.dining_area || '';
        document.getElementById('adminEditDiningSchedule').value = reservation.dining_schedule || '';
        document.getElementById('adminEditNumberOfGuests').value = reservation.number_of_guests || '';
        document.getElementById('adminEditCheckIn').value = reservation.check_in || '';
        document.getElementById('adminEditCheckInTime').value = reservation.check_in_time ? String(reservation.check_in_time).slice(0, 5) : '';
        document.getElementById('adminEditCheckOut').value = reservation.check_out || '';
        document.getElementById('adminEditCheckOutTime').value = reservation.check_out_time ? String(reservation.check_out_time).slice(0, 5) : '';
        document.getElementById('adminEditStatus').value = reservation.status || 'pending';
        document.getElementById('adminEditAmount').value = reservation.total_amount || 0;
        document.getElementById('adminEditRequests').value = reservation.special_requests || '';
        const modal = document.getElementById('adminEditReservationModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAdminEditReservation() {
        const modal = document.getElementById('adminEditReservationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const panels = document.querySelectorAll('[data-panel]');
        const panelStats = document.querySelectorAll('[data-panel-stats]');

        function activateTab(targetName) {
            tabButtons.forEach(function(btn) {
                const isActive = btn.getAttribute('data-tab') === targetName;
                btn.classList.toggle('bg-orange-500', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('bg-white', !isActive);
                btn.classList.toggle('text-gray-600', !isActive);
            });
            panels.forEach(function(panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== targetName);
            });
            panelStats.forEach(function(stats) {
                stats.classList.toggle('hidden', stats.getAttribute('data-panel-stats') !== targetName);
            });
        }

        tabButtons.forEach(function(button) {
            button.addEventListener('click', function () {
                activateTab(this.getAttribute('data-tab'));
            });
        });
    });

    // Search and filter functionality
    document.getElementById('reservationSearch')?.addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.reservation-item').forEach(item => {
            const searchData = item.getAttribute('data-search') || '';
            item.style.display = searchData.includes(searchTerm) ? '' : 'none';
        });
    });

    document.getElementById('reservationStatusFilter')?.addEventListener('change', function(e) {
        const filterStatus = e.target.value;
        document.querySelectorAll('.reservation-item').forEach(item => {
            const itemStatus = item.getAttribute('data-status') || '';
            item.style.display = !filterStatus || itemStatus === filterStatus ? '' : 'none';
        });
    });
</script>

@endsection
