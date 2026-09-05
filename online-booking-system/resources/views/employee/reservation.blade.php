@extends('employee.layout')

@section('pageTitle', 'Reservation Management')
@section('content')

@php
    $reservations = $reservations ?? collect([]);
    $formatDate = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('F j, Y') : 'N/A';
    $formatTime = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('g:i A') : 'Time not set';
    $formatEventDuration = function ($start, $end) {
        if (!$start || !$end) {
            return 'N/A';
        }

        $minutes = abs(\Carbon\Carbon::parse($end)->diffInMinutes(\Carbon\Carbon::parse($start)));
        return floor($minutes / 60) . 'h' . ($minutes % 60 ? ' ' . ($minutes % 60) . 'm' : '');
    };
    $uniqueCsvValue = function ($value) {
        if (is_null($value) || $value === '') {
            return 'N/A';
        }

        $parts = collect(explode(',', (string) $value))
            ->map(fn ($item) => trim((string) $item))
            ->filter(fn ($item) => $item !== '' && $item !== 'null')
            ->unique()
            ->values()
            ->all();

        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    };
    $roomReservations = $roomReservations ?? $reservations->filter(fn ($reservation) => $reservation->room_id || $reservation->category === 'rooms');
    $amenityReservations = $amenityReservations ?? $reservations->filter(fn ($reservation) => $reservation->amenity_id || $reservation->category === 'amenities');
    $eventPlaceReservations = $eventPlaceReservations ?? $reservations->filter(fn ($reservation) => $reservation->event_place_id || $reservation->category === 'event_place');
    $diningReservations = $diningReservations ?? $reservations->filter(function ($reservation) {
        return $reservation->category === 'dining'
            || !empty($reservation->dining_id)
            || !empty($reservation->dining_area)
            || !empty($reservation->dining_schedule)
            || (method_exists($reservation, 'diningItems') && $reservation->diningItems()->exists());
    });
    $roomSelectedServices = function ($roomReservation) use ($amenityReservations) {
        $roomDate = $roomReservation->check_in ? \Carbon\Carbon::parse($roomReservation->check_in)->toDateString() : null;

        return $amenityReservations
            ->filter(function ($amenityReservation) use ($roomReservation, $roomDate) {
                $amenityDate = $amenityReservation->check_in ? \Carbon\Carbon::parse($amenityReservation->check_in)->toDateString() : null;
                return $amenityReservation->guest_email === $roomReservation->guest_email && $amenityDate === $roomDate;
            })
            ->map(function ($amenityReservation) {
                $name = $amenityReservation->amenity?->name ?? 'Amenity';
                $quantity = $amenityReservation->amenity_quantity ?? $amenityReservation->quantity ?? 1;
                return 'Amenity: ' . $name . ' (x' . $quantity . ')';
            })
            ->unique()
            ->values()
            ->all();
    };

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
        <button id="addReservationButton" type="button" onclick="openAddReservationModal()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700" style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">
            <i class="fas fa-plus mr-2"></i><span id="addReservationButtonText">Add Room Reservation</span>
        </button>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <button type="button" data-reservation-tab="rooms" class="reservation-tab inline-flex items-center rounded-full border border-orange-500 bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition">ROOMS</button>
            <button type="button" data-reservation-tab="amenities" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">AMENITIES</button>
            <button type="button" data-reservation-tab="event_place" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">EVENT PLACE</button>
            <button type="button" data-reservation-tab="dining" class="reservation-tab inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition">DINING</button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500" for="reservationSearch">Search</label>
            <input id="reservationSearch" type="search" placeholder="Search by guest, email, or item" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500" for="reservationStatusFilter">Status filter</label>
            <select id="reservationStatusFilter" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="checked-in">Checked-in</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
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
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->room?->room_number ?? '')) }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatDate($reservation->check_in) }}<br><span class="text-xs text-gray-500">{{ $formatTime($reservation->check_in_time) }}</span></td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatDate($reservation->check_out) }}<br><span class="text-xs text-gray-500">{{ $formatTime($reservation->check_out_time) }}</span></td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative flex items-center gap-2 text-sm">
                                        @php($latestPayment = $reservation->payments->last())
                                        @php($paymentMethod = $reservation->payment_method ?: ($latestPayment?->payment_method ?? 'N/A'))
                                        @php($paymentDetails = $reservation->payment_details ?: ($latestPayment?->reference_number ? 'Reference: ' . $latestPayment->reference_number . ($latestPayment->notes ? ' • ' . $latestPayment->notes : '') : ($latestPayment?->notes ?: 'No additional payment details')))
                                        @php($reservationDetails = [
                                            'id' => $reservation->id,
                                            'category' => 'rooms',
                                            'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                            'status' => ucfirst($reservation->status),
                                            'guest_name' => $reservation->guest_name,
                                            'guest_email' => $reservation->guest_email,
                                            'guest_phone' => $reservation->guest_phone,
                                            'special_requests' => $reservation->special_requests ?: 'No special requests',
                                            'room_number' => $reservation->room?->room_number ?? 'N/A',
                                            'room_type' => $reservation->room?->room_type ?? 'N/A',
                                            'room_check_in' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                            'room_check_in_time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                                            'room_check_out' => $reservation->check_out?->format('Y-m-d') ?? 'N/A',
                                            'room_check_out_time' => $reservation->check_out_time ? \Carbon\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'N/A',
                                            'room_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
                                            'room_rate' => $reservation->room?->price ?? 'N/A',
                                            'selected_services' => $roomSelectedServices($reservation),
                                            'amount_paid' => $reservation->amount_paid ?? $latestPayment?->amount ?? 0,
                                            'payment_method' => $paymentMethod,
                                            'payment_details' => $paymentDetails,
                                            'payment_proof' => $latestPayment?->payment_proof ?? (preg_match('/https?:\/\/\S+|\/storage\/\S+|data:image\/[a-zA-Z0-9.+-]+;base64,[A-Za-z0-9\/+=]+/', (string) $paymentDetails) ? preg_replace('/.*?(https?:\/\/\S+|\/storage\/\S+|data:image\/[a-zA-Z0-9.+-]+;base64,[A-Za-z0-9\/+=]+).*/i', '$1', (string) $paymentDetails) : null),
                                            'total_amount' => $reservation->total_amount ?? 0,
                                        ])
                                        <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="employee-reservation-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                            @if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button>@endif
                                            @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-in</button>@endif
                                            @if($reservation->status === 'checked-in')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Checked-out</button>@endif
                                            @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>@endif
                                            @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>@endif
                                            <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button></form>
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
                            <p><span class="font-medium text-gray-700">Check-in:</span> {{ $formatDate($reservation->check_in) }} at {{ $formatTime($reservation->check_in_time) }}</p>
                            <p><span class="font-medium text-gray-700">Check-out:</span> {{ $formatDate($reservation->check_out) }} at {{ $formatTime($reservation->check_out_time) }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @php($latestPayment = $reservation->payments->last())
                            @php($paymentMethod = $reservation->payment_method ?: ($latestPayment?->payment_method ?? 'N/A'))
                            @php($paymentDetails = $latestPayment?->reference_number ? 'Reference: ' . $latestPayment->reference_number . ($latestPayment->notes ? ' • ' . $latestPayment->notes : '') : ($latestPayment?->notes ?: 'No additional payment details'))
                            @php($reservationDetails = [
                                'id' => $reservation->id,
                                'category' => 'rooms',
                                'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                'status' => ucfirst($reservation->status),
                                'guest_name' => $reservation->guest_name,
                                'guest_email' => $reservation->guest_email,
                                'guest_phone' => $reservation->guest_phone,
                                'special_requests' => $reservation->special_requests ?: 'No special requests',
                                'room_number' => $reservation->room?->room_number ?? 'N/A',
                                'room_type' => $reservation->room?->room_type ?? 'N/A',
                                'room_check_in' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                'room_check_in_time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                                'room_check_out' => $reservation->check_out?->format('Y-m-d') ?? 'N/A',
                                'room_check_out_time' => $reservation->check_out_time ? \Carbon\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'N/A',
                                'room_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
                                'room_rate' => $reservation->room?->price ?? 'N/A',
                                'selected_services' => $roomSelectedServices($reservation),
                                'amount_paid' => $reservation->amount_paid ?? 0,
                                'payment_method' => $paymentMethod,
                                'payment_details' => $paymentDetails,
                                'total_amount' => $reservation->total_amount ?? 0,
                            ])
                            <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                            <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <div class="relative"><button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"><i class="fas fa-ellipsis-v"></i></button><div class="employee-reservation-menu absolute bottom-10 right-0 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">@if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full px-3 py-2 text-left text-sm">Confirm Reservation</button>@endif @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="block w-full px-3 py-2 text-left text-sm">Mark as Checked-in</button>@endif @if($reservation->status === 'checked-in')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full px-3 py-2 text-left text-sm">Mark as Checked-out</button>@endif <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full px-3 py-2 text-left text-sm">Cancel Reservation</button><form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600">Delete Reservation</button></form></div></div>
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
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</th>
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
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->amenity?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatDate($reservation->check_in) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->dining_schedule ?: $formatTime($reservation->check_in_time) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->quantity ?? $reservation->guests ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative flex items-center gap-2 text-sm">
                                        @php($reservationDetails = [
                                            'id' => $reservation->id,
                                            'category' => 'amenities',
                                            'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                            'status' => ucfirst($reservation->status),
                                            'guest_name' => $reservation->guest_name,
                                            'guest_email' => $reservation->guest_email,
                                            'guest_phone' => $reservation->guest_phone,
                                            'special_requests' => $reservation->special_requests ?: 'No special requests',
                                            'amenity_name' => $reservation->amenity?->name ?? 'N/A',
                                            'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                            'time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                                            'quantity' => $reservation->quantity ?? $reservation->guests ?? 'N/A',
                                            'selected_services' => $reservation->amenity ? ['Amenity: ' . $reservation->amenity->name] : [],
                                            'amount_paid' => $reservation->amount_paid ?? 0,
                                            'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                            'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                            'total_amount' => $reservation->total_amount ?? 0,
                                        ])
                                        <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="employee-reservation-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                            @if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button>@endif
                                            @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button>@endif
                                            @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>@endif
                                            @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>@endif
                                            <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf @method('DELETE')<button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button></form>
                                        </div>
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
                            <p><span class="font-medium text-gray-700">Amenity:</span> {{ $reservation->amenity?->name ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $formatDate($reservation->check_in) }}</p>
                            <p><span class="font-medium text-gray-700">Time:</span> {{ $reservation->dining_schedule ?: $formatTime($reservation->check_in_time) }}</p>
                            <p><span class="font-medium text-gray-700">Guests:</span> {{ $reservation->quantity ?? $reservation->guests ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @php($reservationDetails = [
                                'id' => $reservation->id,
                                'category' => 'amenities',
                                'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                'status' => ucfirst($reservation->status),
                                'guest_name' => $reservation->guest_name,
                                'guest_email' => $reservation->guest_email,
                                'guest_phone' => $reservation->guest_phone,
                                'special_requests' => $reservation->special_requests ?: 'No special requests',
                                'amenity_name' => $reservation->amenity?->name ?? 'N/A',
                                'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                'time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                                'quantity' => $reservation->quantity ?? $reservation->guests ?? 'N/A',
                                'selected_services' => $reservation->amenity ? ['Amenity: ' . $reservation->amenity->name] : [],
                                'amount_paid' => $reservation->amount_paid ?? 0,
                                'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                'total_amount' => $reservation->total_amount ?? 0,
                            ])
                            <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                            <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <div class="relative"><button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"><i class="fas fa-ellipsis-v"></i></button><div class="employee-reservation-menu absolute bottom-10 right-0 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">@if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full px-3 py-2 text-left text-sm">Confirm Reservation</button>@endif @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full px-3 py-2 text-left text-sm">Mark as Completed</button>@endif @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full px-3 py-2 text-left text-sm">Cancel Reservation</button>@endif @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full px-3 py-2 text-left text-sm">Print Receipt</button>@endif <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600">Delete Reservation</button></form></div></div>
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
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->eventPlace?->name ?? '')) }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->eventPlace?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_type ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatDate($reservation->check_in) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatTime($reservation->event_start_time) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatTime($reservation->event_end_time) }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative flex items-center gap-2 text-sm">
                                        @php($reservationDetails = [
                                            'id' => $reservation->id,
                                            'category' => 'event_place',
                                            'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                            'status' => ucfirst($reservation->status),
                                            'guest_name' => $reservation->guest_name,
                                            'guest_email' => $reservation->guest_email,
                                            'guest_phone' => $reservation->guest_phone,
                                            'special_requests' => $reservation->special_requests ?: 'No special requests',
                                            'event_place' => $reservation->eventPlace?->name ?? 'N/A',
                                            'event_type' => $reservation->event_type ?? 'N/A',
                                            'event_date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                            'event_start_time' => $reservation->event_start_time ? \Carbon\Carbon::parse($reservation->event_start_time)->format('g:i A') : 'N/A',
                                            'event_end_time' => $reservation->event_end_time ? \Carbon\Carbon::parse($reservation->event_end_time)->format('g:i A') : 'N/A',
                                            'event_duration' => $formatEventDuration($reservation->event_start_time, $reservation->event_end_time),
                                            'event_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
                                            'selected_services' => $reservation->eventPlace ? ['Event Place: ' . $reservation->eventPlace->name . ($reservation->event_type ? ' — ' . $reservation->event_type : '')] : [],
                                            'amount_paid' => $reservation->amount_paid ?? 0,
                                            'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                            'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                            'total_amount' => $reservation->total_amount ?? 0,
                                        ])
                                        <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="employee-reservation-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                            @if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button>@endif
                                            @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button>@endif
                                            @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>@endif
                                            @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>@endif
                                            <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button></form>
                                        </div>
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
                            <p><span class="font-medium text-gray-700">Place:</span> {{ $reservation->eventPlace?->name ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Type:</span> {{ $reservation->event_type ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $formatDate($reservation->check_in) }}</p>
                            <p><span class="font-medium text-gray-700">Start:</span> {{ $formatTime($reservation->event_start_time) }}</p>
                            <p><span class="font-medium text-gray-700">End:</span> {{ $formatTime($reservation->event_end_time) }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @php($reservationDetails = [
                                'id' => $reservation->id,
                                'category' => 'event_place',
                                'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                'status' => ucfirst($reservation->status),
                                'guest_name' => $reservation->guest_name,
                                'guest_email' => $reservation->guest_email,
                                'guest_phone' => $reservation->guest_phone,
                                'special_requests' => $reservation->special_requests ?: 'No special requests',
                                'event_place' => $reservation->eventPlace?->name ?? 'N/A',
                                'event_type' => $reservation->event_type ?? 'N/A',
                                'event_date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                'event_start_time' => $reservation->event_start_time ? \Carbon\Carbon::parse($reservation->event_start_time)->format('g:i A') : 'N/A',
                                'event_end_time' => $reservation->event_end_time ? \Carbon\Carbon::parse($reservation->event_end_time)->format('g:i A') : 'N/A',
                                'event_duration' => $formatEventDuration($reservation->event_start_time, $reservation->event_end_time),
                                'event_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
                                'selected_services' => $reservation->eventPlace ? ['Event Place: ' . $reservation->eventPlace->name . ($reservation->event_type ? ' — ' . $reservation->event_type : '')] : [],
                                'amount_paid' => $reservation->amount_paid ?? 0,
                                'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                'total_amount' => $reservation->total_amount ?? 0,
                            ])
                            <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                            <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <div class="relative"><button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"><i class="fas fa-ellipsis-v"></i></button><div class="employee-reservation-menu absolute bottom-10 right-0 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">@if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full px-3 py-2 text-left text-sm">Confirm Reservation</button>@endif @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full px-3 py-2 text-left text-sm">Mark as Completed</button>@endif @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full px-3 py-2 text-left text-sm">Cancel Reservation</button>@endif @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full px-3 py-2 text-left text-sm">Print Receipt</button>@endif <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600">Delete Reservation</button></form></div></div>
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
                            @php($reservationDiningItems = $reservation->diningItems()->with('diningMenu')->get())
                            @php($reservationMealNames = $reservationDiningItems->map(fn ($item) => $item->diningMenu?->name ?? 'Meal Item')->filter()->unique()->values()->all())
                            @php($reservationMealText = !empty($reservationMealNames) ? implode(', ', $reservationMealNames) : ($reservation->diningMenu ? $reservation->diningMenu->name : 'N/A'))
                            @php($reservationMealDetails = $reservationDiningItems->map(function ($item) {
                                $mealName = $item->diningMenu?->name ?? 'Meal Item';
                                $qty = $item->quantity ?? 1;
                                return $mealName . ' (x' . $qty . ')';
                            })->values()->all())
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->dining_area ?? '')) }}">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $uniqueCsvValue($reservation->dining_area ?? $reservation->table_name ?? 'N/A') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $formatDate($reservation->check_in) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $uniqueCsvValue($reservation->dining_schedule ?? 'N/A') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->quantity ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount ?? 0, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative flex items-center gap-2 text-sm">
                                        @php($reservationDetails = [
                                            'id' => $reservation->id,
                                            'category' => 'dining',
                                            'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                            'status' => ucfirst($reservation->status),
                                            'guest_name' => $reservation->guest_name,
                                            'guest_email' => $reservation->guest_email,
                                            'guest_phone' => $reservation->guest_phone,
                                            'special_requests' => $reservation->special_requests ?: 'No special requests',
                                            'dining_area' => $uniqueCsvValue($reservation->dining_area ?? $reservation->table_name ?? 'N/A'),
                                            'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                            'time' => $uniqueCsvValue($reservation->dining_schedule ?: ($reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A')),
                                            'number_of_guests' => $reservation->quantity ?? $reservation->number_of_guests ?? 'N/A',
                                            'selected_services' => $reservationMealDetails ?: ($reservation->dining_area ? ['Dining Area: ' . $reservation->dining_area] : []),
                                            'amount_paid' => $reservation->amount_paid ?? 0,
                                            'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                            'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                            'total_amount' => $reservation->total_amount ?? 0,
                                        ])
                                        <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details"><i class="fas fa-eye"></i></button>
                                        <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50" title="Edit reservation"><i class="fas fa-pen"></i></button>
                                        <button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="employee-reservation-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                            @if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm Reservation</button>@endif
                                            @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button>@endif
                                            @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>@endif
                                            @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>@endif
                                            <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button></form>
                                        </div>
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
                        @php($reservationDiningItems = $reservation->diningItems()->with('diningMenu')->get())
                        @php($reservationMealNames = $reservationDiningItems->map(fn ($item) => $item->diningMenu?->name ?? 'Meal Item')->filter()->unique()->values()->all())
                        @php($reservationMealText = !empty($reservationMealNames) ? implode(', ', $reservationMealNames) : ($reservation->diningMenu ? $reservation->diningMenu->name : 'N/A'))
                        @php($reservationMealDetails = $reservationDiningItems->map(function ($item) {
                            $mealName = $item->diningMenu?->name ?? 'Meal Item';
                            $qty = $item->quantity ?? 1;
                            return $mealName . ' (x' . $qty . ')';
                        })->values()->all())
                        <div class="mt-3 space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium text-gray-700">Table:</span> {{ $uniqueCsvValue($reservation->dining_area ?? $reservation->table_name ?? 'N/A') }}</p>
                            <p><span class="font-medium text-gray-700">Date:</span> {{ $formatDate($reservation->check_in) }}</p>
                            <p><span class="font-medium text-gray-700">Time:</span> {{ $uniqueCsvValue($reservation->dining_schedule ?? 'N/A') }}</p>
                            <p><span class="font-medium text-gray-700">Meals:</span> {{ $reservationMealText }}</p>
                            <p><span class="font-medium text-gray-700">Guests:</span> {{ $reservation->quantity ?? 'N/A' }}</p>
                            <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount ?? 0, 2) }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @php($reservationDetails = [
                                'id' => $reservation->id,
                                'category' => 'dining',
                                'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
                                'status' => ucfirst($reservation->status),
                                'guest_name' => $reservation->guest_name,
                                'guest_email' => $reservation->guest_email,
                                'guest_phone' => $reservation->guest_phone,
                                'special_requests' => $reservation->special_requests ?: 'No special requests',
                                'dining_area' => $uniqueCsvValue($reservation->dining_area ?? $reservation->table_name ?? 'N/A'),
                                'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                                'time' => $uniqueCsvValue($reservation->dining_schedule ?: ($reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A')),
                                'number_of_guests' => $reservation->quantity ?? $reservation->number_of_guests ?? 'N/A',
                                'selected_services' => $reservationMealDetails ?: ($reservation->dining_area ? ['Dining Area: ' . $reservation->dining_area] : []),
                                'amount_paid' => $reservation->amount_paid ?? 0,
                                'payment_method' => $reservation->payment_method ?: ($reservation->payments->last()?->payment_method ?? 'N/A'),
                                'payment_details' => $reservation->payments->last()?->reference_number ? 'Reference: ' . $reservation->payments->last()->reference_number . ($reservation->payments->last()->notes ? ' • ' . $reservation->payments->last()->notes : '') : ($reservation->payments->last()?->notes ?: 'No additional payment details'),
                                'total_amount' => $reservation->total_amount ?? 0,
                            ])
                            <button type="button" onclick="showEmployeeReservationDetails(this)" data-reservation='@json($reservationDetails)' class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                            <button type="button" onclick='editReservation(@json($reservation))' class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700"><i class="fas fa-pen mr-1"></i>Edit</button>
                            <div class="relative"><button type="button" onclick="toggleEmployeeReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"><i class="fas fa-ellipsis-v"></i></button><div class="employee-reservation-menu absolute bottom-10 right-0 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">@if($reservation->status === 'pending')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full px-3 py-2 text-left text-sm">Confirm Reservation</button>@endif @if($reservation->status === 'confirmed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full px-3 py-2 text-left text-sm">Mark as Completed</button>@endif @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')<button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full px-3 py-2 text-left text-sm">Cancel Reservation</button>@endif @if($reservation->status === 'completed')<button type="button" onclick="window.print()" class="block w-full px-3 py-2 text-left text-sm">Print Receipt</button>@endif <form action="{{ route('employee.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="event.stopPropagation(); return confirm('Delete this reservation?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600">Delete Reservation</button></form></div></div>
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
                <div id="roomReservationField" data-reservation-fields="rooms">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" data-required-for="rooms" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select a room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->room_type }}</option>
                        @endforeach
                    </select>
                    @error('room_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="hidden" data-reservation-fields="amenities">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Amenity</label>
                    <select name="amenity_id" data-reservation-input="amenities" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select an amenity</option>
                        @foreach(($amenities ?? collect()) as $item)
                            <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }} - ₱{{ number_format($item->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Place</label>
                    <select name="event_place_id" data-reservation-input="event_place" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select an event place</option>
                        @foreach(($eventPlaces ?? collect()) as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Date</label>
                    <input type="date" id="eventDate" name="check_in" value="{{ old('check_in') }}" data-event-date data-reservation-input="event_place" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Event Type</label>
                    <select name="event_type" data-reservation-input="event_place" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select event type</option>
                        <option value="Wedding">Wedding</option>
                        <option value="Birthday">Birthday</option>
                        <option value="Conference">Conference</option>
                        <option value="Party">Party</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" name="event_start_time" data-reservation-input="event_place" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" id="employeeEventEndTime" name="event_end_time" data-reservation-input="event_place" required readonly class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-gray-600 focus:outline-none">
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">How Many Hours?</label>
                    <input type="number" id="employeeEventDuration" name="event_duration_hours" data-reservation-input="event_place" min="1" max="12" step="1" value="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="hidden" data-reservation-fields="event_place">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Number of Guests</label>
                    <input type="number" name="number_of_guests" data-reservation-input="event_place" min="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <input type="hidden" name="check_out" value="{{ old('check_out', old('check_in')) }}" data-event-date-end>
                </div>
                <div class="hidden" data-reservation-fields="dining">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Dining Area/Table</label>
                    <select name="dining_area" data-reservation-input="dining" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select a table</option>
                        @foreach(($diningTables ?? collect())->where('status', 'Available') as $table)
                            <option value="{{ $table['table_no'] }}">{{ $table['table_no'] }} - {{ $table['type'] }} ({{ $table['capacity'] }} seats, {{ $table['location'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="hidden md:col-span-2" data-reservation-fields="dining">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Dining Date</label>
                            <input type="date" id="diningDate" name="check_in" value="{{ old('check_in') }}" data-dining-date data-reservation-input="dining" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Menu</label>
                            <div id="diningMenuPicker" class="relative rounded-lg border border-gray-300 bg-white p-2">
                                <div id="selectedDiningMenuItems" class="flex min-h-10 flex-wrap items-center gap-2 rounded-md border border-gray-200 bg-white p-1.5"></div>
                                <div class="mt-2 flex items-center gap-2 rounded-md border border-gray-200 px-2">
                                    <input id="diningMenuSearch" type="text" placeholder="Select menu items" class="w-full border-0 px-1 py-2 text-sm outline-none focus:ring-0">
                                    <button type="button" id="diningMenuToggle" class="px-2 text-slate-600" aria-label="Show dining menu" aria-expanded="false"><i class="fas fa-chevron-down"></i></button>
                                </div>
                                <div id="diningMenuSelect" class="mt-2 hidden max-h-72 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-2">
                                    <button type="button" id="diningSelectAllBtn" class="mb-1 rounded border border-orange-200 bg-orange-50 px-2 py-1 text-xs font-medium text-orange-600 hover:bg-orange-100">Select All</button>
                                    <label class="flex w-full cursor-pointer items-center justify-between gap-2 rounded border border-gray-200 bg-white p-2 shadow-sm transition hover:border-orange-300 hover:bg-orange-50">
                                        <span class="flex items-center gap-2">
                                            <input type="checkbox" value="upon_arriving" data-price="0" data-name="Upon Arriving" class="dining-menu-checkbox h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                            <span class="text-sm font-medium text-gray-700">Upon Arriving</span>
                                        </span>
                                        <span class="text-xs text-gray-500">₱0.00</span>
                                    </label>
                                    @foreach(($diningMenus ?? collect()) as $item)
                                        <label class="dining-menu-item flex w-full cursor-pointer items-center justify-between gap-2 rounded border border-gray-200 bg-white p-2 shadow-sm transition hover:border-orange-300 hover:bg-orange-50" data-menu-name="{{ strtolower($item->name) }}">
                                            <span class="flex items-center gap-2">
                                                <input type="checkbox" value="{{ $item->id }}" data-price="{{ (float) $item->price }}" data-name="{{ $item->name }}" class="dining-menu-checkbox h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                                                <span class="text-sm font-medium text-gray-700">{{ $item->name }}</span>
                                            </span>
                                            <span class="text-xs text-gray-500">₱{{ number_format((float) $item->price, 2) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" name="dining_id" id="diningSelectedMenuIds" value="">
                            <input type="hidden" name="quantity" id="diningSelectedMenuQuantity" value="">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Dining Schedule</label>
                            <select name="dining_schedule" data-reservation-input="dining" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Select a dining schedule</option>
                                @foreach(($diningSchedules ?? collect())->where('status', 'Active') as $schedule)
                                    <option value="{{ $schedule->period }}" {{ old('dining_schedule') === $schedule->period ? 'selected' : '' }}>{{ $schedule->period }} ({{ date('g:i A', strtotime($schedule->available_from)) }} - {{ date('g:i A', strtotime($schedule->available_to)) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Total Amount</label>
                            <input id="reservationTotalAmount" type="number" name="total_amount" value="{{ old('total_amount') }}" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @error('total_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                <div class="hidden" data-reservation-fields="dining">
                    <input type="hidden" name="check_out" value="{{ old('check_out', old('check_in')) }}" data-dining-date-end>
                    <input type="hidden" name="check_out_time" value="{{ old('check_out_time', old('check_in_time')) }}" data-dining-time-end>
                </div>
                <div data-standard-reservation-field>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" name="check_in" value="{{ old('check_in') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_in')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div data-standard-reservation-field>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" name="check_out" value="{{ old('check_out') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_out')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div data-standard-reservation-field>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-in Time</label>
                    <input type="time" name="check_in_time" value="{{ old('check_in_time') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_in_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div data-standard-reservation-field>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-out Time</label>
                    <input type="time" name="check_out_time" value="{{ old('check_out_time') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_out_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div data-standard-reservation-field>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Number of Guests</label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests') }}" min="1" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('number_of_guests')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="hidden md:col-span-2" data-amenity-reservation-field>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="employeeAmenityQuantity" name="amenity_quantity" min="1" value="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Amenity Date</label>
                            <input type="date" id="amenityDate" name="check_in" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" id="amenityStartTime" name="check_in_time" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Stay Duration (Hours)</label>
                            <input type="number" id="amenityDurationHours" name="duration_hours" min="1" max="24" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Automatic End Time</label>
                            <input type="time" id="amenityEndTime" name="check_out_time" readonly class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-gray-600 focus:outline-none">
                        </div>
                    </div>
                    <input type="hidden" id="amenityEndDate" name="check_out">
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
    <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800">Reservation Details</h3>
            <button type="button" onclick="closeEmployeeReservationDetails()" class="text-gray-500 hover:text-gray-800" aria-label="Close details"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div id="employeeDetailsBody" class="max-h-[70vh] space-y-5 overflow-y-auto p-6"></div>
    </div>
</div>

<form id="reservationStatusForm" action="" method="POST">
    @csrf
    <input type="hidden" name="_method" value="PATCH">
    <input type="hidden" name="status" id="reservationStatus">
    <input type="hidden" name="category" id="reservationStatusCategory">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reservationTypeLabels = {
            rooms: 'Room',
            amenities: 'Amenities',
            event_place: 'Event Place',
            dining: 'Dining'
        };

        function setActiveReservationTab(tabKey) {
            const reservationType = reservationTypeLabels[tabKey] || reservationTypeLabels.rooms;

            document.querySelectorAll('[data-reservation-tab]').forEach((button) => {
                const isActive = button.dataset.reservationTab === tabKey;
                button.classList.toggle('bg-orange-500', isActive);
                button.classList.toggle('border-orange-500', isActive);
                button.classList.toggle('text-white', isActive);
                button.classList.toggle('bg-white', !isActive);
                button.classList.toggle('border-gray-200', !isActive);
                button.classList.toggle('text-gray-700', !isActive);
            });

            document.querySelectorAll('[data-reservation-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.reservationPanel !== tabKey);
            });

            document.querySelectorAll('[data-reservation-fields]').forEach((fieldGroup) => {
                const isActive = fieldGroup.dataset.reservationFields === tabKey;
                fieldGroup.classList.toggle('hidden', !isActive);
                fieldGroup.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.disabled = !isActive;
                    input.required = isActive && input.dataset.reservationInput === tabKey;
                });
            });

            document.querySelectorAll('[data-standard-reservation-field]').forEach((fieldGroup) => {
                const isVisible = !['amenities', 'event_place', 'dining'].includes(tabKey);
                fieldGroup.classList.toggle('hidden', !isVisible);
                fieldGroup.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.disabled = !isVisible;
                });
            });

            document.querySelectorAll('[data-amenity-reservation-field]').forEach((fieldGroup) => {
                const isVisible = tabKey === 'amenities';
                fieldGroup.classList.toggle('hidden', !isVisible);
                fieldGroup.querySelectorAll('input').forEach((input) => {
                    input.disabled = !isVisible;
                    input.required = isVisible && ['amenityDate', 'amenityStartTime', 'amenityDurationHours'].includes(input.id);
                });
            });

            const totalInput = document.getElementById('reservationTotalAmount');

            if (totalInput) {
                const isDiningTab = tabKey === 'dining';
                totalInput.disabled = !isDiningTab;
                totalInput.required = isDiningTab;
                totalInput.readOnly = ['amenities', 'dining'].includes(tabKey);
            }

            updateAmenityReservationTotal();
            updateDiningReservationTotal();
            document.getElementById('addReservationButtonText').textContent = `Add ${reservationType} Reservation`;
            document.getElementById('reservationModalTitle').textContent = `Add ${reservationType} Reservation`;
            document.getElementById('reservationCategory').value = tabKey;
        }

        function updateDiningReservationTotal() {
            const diningCheckboxes = document.querySelectorAll('.dining-menu-checkbox');
            const totalInput = document.getElementById('reservationTotalAmount');

            if (!diningCheckboxes.length || !totalInput) {
                return;
            }

            const selectedOptions = [...diningCheckboxes].filter((checkbox) => checkbox.checked);
            const total = selectedOptions.reduce((sum, option) => {
                const qtyInput = document.querySelector(`[data-dining-menu-id="${option.value}"]`);
                const quantity = Number(qtyInput?.value || 1);
                const price = Number(option.dataset.price || 0);
                return sum + (price * quantity);
            }, 0);

            totalInput.value = selectedOptions.length ? total.toFixed(2) : '';

            const selectedIds = selectedOptions
                .filter(option => option.value !== 'upon_arriving')
                .map(option => option.value);
            const selectedQuantity = selectedOptions.reduce((sum, option) => {
                const qtyInput = document.querySelector(`[data-dining-menu-id="${option.value}"]`);
                return sum + Number(qtyInput?.value || 1);
            }, 0);

            document.getElementById('diningSelectedMenuIds').value = selectedIds.join(',');
            document.getElementById('diningSelectedMenuQuantity').value = selectedQuantity || '';
        }

        function renderSelectedDiningMenuItems() {
            const diningCheckboxes = document.querySelectorAll('.dining-menu-checkbox');
            const container = document.getElementById('selectedDiningMenuItems');

            if (!diningCheckboxes.length || !container) {
                return;
            }

            const selectedOptions = [...diningCheckboxes].filter((checkbox) => checkbox.checked);
            if (!selectedOptions.length) {
                container.innerHTML = '<p class="text-sm text-gray-500">No menu selected.</p>';
                updateDiningReservationTotal();
                return;
            }

            container.innerHTML = selectedOptions.map((option) => {
                const value = option.value;
                const name = option.dataset.name || option.value;
                const price = Number(option.dataset.price || 0);
                const quantity = Number(document.querySelector(`[data-dining-menu-id="${value}"]`)?.value || 1);

                return `
                    <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 p-2">
                        <span class="flex-1 text-sm font-medium text-gray-700">${name}</span>
                        <span class="text-xs text-gray-500">₱${price.toFixed(2)}</span>
                        <input type="number" min="1" value="${quantity}" data-dining-menu-id="${value}" class="w-20 rounded border border-gray-300 px-2 py-1 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                `;
            }).join('');

            container.querySelectorAll('[data-dining-menu-id]').forEach((input) => {
                input.addEventListener('input', updateDiningReservationTotal);
            });

            updateDiningReservationTotal();
        }

        const diningMenuSearch = document.getElementById('diningMenuSearch');
        const diningSelectAllBtn = document.getElementById('diningSelectAllBtn');
        const diningMenuSelect = document.getElementById('diningMenuSelect');
        const diningMenuToggle = document.getElementById('diningMenuToggle');

        const setDiningMenuOpen = (isOpen) => {
            diningMenuSelect?.classList.toggle('hidden', !isOpen);
            diningMenuToggle?.setAttribute('aria-expanded', String(isOpen));
        };

        diningMenuToggle?.addEventListener('click', () => {
            setDiningMenuOpen(diningMenuSelect?.classList.contains('hidden'));
            diningMenuSearch?.focus();
        });

        if (diningMenuSearch) {
            diningMenuSearch.addEventListener('focus', function () {
                setDiningMenuOpen(true);
            });
            diningMenuSearch.addEventListener('input', function () {
                setDiningMenuOpen(true);
                const query = this.value.trim().toLowerCase();
                document.querySelectorAll('.dining-menu-item').forEach((item) => {
                    const name = (item.dataset.menuName || '').toLowerCase();
                    item.style.display = name.includes(query) ? '' : 'none';
                });
            });
        }

        if (diningSelectAllBtn) {
            diningSelectAllBtn.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('.dining-menu-checkbox');
                const shouldCheck = ![...checkboxes].every((checkbox) => checkbox.checked);

                checkboxes.forEach((checkbox) => {
                    checkbox.checked = shouldCheck;
                });

                renderSelectedDiningMenuItems();
            });
        }

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#diningMenuPicker')) {
                setDiningMenuOpen(false);
            }
        });

        function updateAmenityReservationTotal() {
            const amenitySelect = document.querySelector('[data-reservation-input="amenities"]');
            const durationInput = document.getElementById('amenityDurationHours');
            const endTimeInput = document.getElementById('amenityEndTime');
            const endDateInput = document.getElementById('amenityEndDate');
            const totalInput = document.getElementById('reservationTotalAmount');
            const startTime = document.getElementById('amenityStartTime').value;
            const duration = Number(durationInput.value || 0);
            const selectedOption = amenitySelect.options[amenitySelect.selectedIndex];
            const price = Number(selectedOption?.dataset.price || 0);

            totalInput.value = price && duration ? (price * duration).toFixed(2) : '';
            endTimeInput.value = '';
            endDateInput.value = document.getElementById('amenityDate').value;

            if (startTime && duration > 0) {
                const [hours, minutes] = startTime.split(':').map(Number);
                const endMinutes = (hours * 60) + minutes + (duration * 60);
                endTimeInput.value = `${String(Math.floor((endMinutes % 1440) / 60)).padStart(2, '0')}:${String(endMinutes % 60).padStart(2, '0')}`;
                if (endMinutes >= 1440) {
                    const endDate = new Date(`${endDateInput.value}T00:00:00`);
                    endDate.setDate(endDate.getDate() + 1);
                    endDateInput.value = endDate.toISOString().slice(0, 10);
                }
            }
        }

        document.querySelectorAll('[data-reservation-tab]').forEach((button) => {
            button.addEventListener('click', () => setActiveReservationTab(button.dataset.reservationTab));
        });

        document.querySelector('[data-reservation-input="amenities"]')?.addEventListener('change', updateAmenityReservationTotal);
        document.querySelectorAll('.dining-menu-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', renderSelectedDiningMenuItems);
        });
        document.getElementById('amenityDate')?.addEventListener('input', updateAmenityReservationTotal);
        document.getElementById('amenityStartTime')?.addEventListener('input', updateAmenityReservationTotal);
        document.getElementById('amenityDurationHours')?.addEventListener('input', updateAmenityReservationTotal);
        const updateEmployeeEventEndTime = () => {
            const startInput = document.querySelector('[name="event_start_time"]');
            const durationInput = document.getElementById('employeeEventDuration');
            const endInput = document.getElementById('employeeEventEndTime');
            if (!startInput || !durationInput || !endInput || !startInput.value) {
                return;
            }

            const [hours, minutes] = startInput.value.split(':').map(Number);
            const endMinutes = (hours * 60) + minutes + (Number(durationInput.value || 1) * 60);
            endInput.value = `${String(Math.floor((endMinutes % 1440) / 60)).padStart(2, '0')}:${String(endMinutes % 60).padStart(2, '0')}`;
        };
        document.querySelector('[name="event_start_time"]')?.addEventListener('input', updateEmployeeEventEndTime);
        document.getElementById('employeeEventDuration')?.addEventListener('input', updateEmployeeEventEndTime);
        document.querySelector('[data-event-date]')?.addEventListener('input', (event) => {
            document.querySelector('[data-event-date-end]').value = event.target.value;
        });
        document.querySelector('[data-dining-date]')?.addEventListener('input', (event) => {
            document.querySelector('[data-dining-date-end]').value = event.target.value;
        });
        document.querySelector('[data-dining-time]')?.addEventListener('input', (event) => {
            document.querySelector('[data-dining-time-end]').value = event.target.value;
        });

        setActiveReservationTab('rooms');
    });

    function changeReservationStatus(id, status) {
        if (confirm(`Are you sure you want to change the status to ${status}?`)) {
            var actionTemplate = "{{ route('employee.reservations.status', ['id' => '__ID__']) }}";
            document.getElementById('reservationStatusForm').action = actionTemplate.replace('__ID__', id);
            document.getElementById('reservationStatus').value = status;
            document.getElementById('reservationStatusCategory').value = document.querySelector('[data-reservation-tab].bg-orange-500')?.dataset.reservationTab || 'rooms';
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

    function filterEmployeeReservations() {
        const searchTerm = (document.getElementById('reservationSearch')?.value || '').toLowerCase().trim();
        const statusFilter = document.getElementById('reservationStatusFilter')?.value || '';

        document.querySelectorAll('.reservation-item').forEach((row) => {
            const matchesSearch = (row.dataset.search || '').includes(searchTerm);
            const matchesStatus = !statusFilter || row.dataset.status === statusFilter;
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    document.getElementById('reservationSearch')?.addEventListener('input', filterEmployeeReservations);
    document.getElementById('reservationStatusFilter')?.addEventListener('change', filterEmployeeReservations);

    function formatMoney(value) {
        const amount = Number(value || 0);
        if (!Number.isFinite(amount)) {
            return '₱0.00';
        }
        return `₱${amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function formatDateValue(value) {
        if (!value || value === 'N/A') return 'N/A';
        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) return value;
        return parsed.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderDetailsCard(title, entries) {
        const entriesHtml = entries.map((entry) => {
            const value = entry.value ?? 'N/A';
            return `
                <div class="rounded-xl border border-gray-200 bg-white p-3">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">${escapeHtml(entry.label)}</div>
                    <div class="mt-1 text-sm font-semibold text-gray-900">${escapeHtml(value)}</div>
                </div>
            `;
        }).join('');

        return `
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <h4 class="mb-3 text-base font-semibold text-gray-800">${escapeHtml(title)}</h4>
                <div class="grid gap-3 sm:grid-cols-2">${entriesHtml}</div>
            </div>
        `;
    }

    function showEmployeeReservationDetails(button) {
        const reservation = JSON.parse(button.dataset.reservation || '{}');
        const category = reservation.category || 'rooms';
        const status = reservation.status || 'N/A';
        const services = Array.isArray(reservation.selected_services) && reservation.selected_services.length
            ? reservation.selected_services
            : ['No additional services selected'];
        const detailsSections = [
            renderDetailsCard('Reservation Information', [
                { label: 'Reservation ID', value: reservation.id ? `RES-${reservation.id}` : 'N/A' },
                { label: 'Reservation Date', value: reservation.reservation_date || 'N/A' },
                { label: 'Status', value: status },
            ]),
            renderDetailsCard('Guest Information', [
                { label: 'Full Name', value: reservation.guest_name || 'N/A' },
                { label: 'Email', value: reservation.guest_email || 'N/A' },
                { label: 'Mobile Number', value: reservation.guest_phone || 'N/A' },
                { label: 'Special Request', value: reservation.special_requests || 'No special requests' },
            ])
        ];

        const roomEntries = [
            { label: 'Room Number', value: reservation.room_number || reservation.room || 'N/A' },
            { label: 'Room Type', value: reservation.room_type || 'N/A' },
            { label: 'Check-in Date', value: formatDateValue(reservation.room_check_in || 'N/A') },
            { label: 'Check-in Time', value: reservation.room_check_in_time || 'N/A' },
            { label: 'Check-out Date', value: formatDateValue(reservation.room_check_out || 'N/A') },
            { label: 'Check-out Time', value: reservation.room_check_out_time || 'N/A' },
            { label: 'Number of Guests', value: reservation.room_number_of_guests !== undefined && reservation.room_number_of_guests !== null && reservation.room_number_of_guests !== '' && reservation.room_number_of_guests !== 'N/A' ? reservation.room_number_of_guests : 'N/A' },
            { label: 'Room Rate', value: reservation.room_rate && reservation.room_rate !== 'N/A' ? formatMoney(reservation.room_rate) : 'N/A' },
        ];

        const amenityEntries = [
            { label: 'Amenity', value: reservation.amenity_name || 'N/A' },
            { label: 'Date', value: formatDateValue(reservation.date || reservation.check_in) },
            { label: 'Time', value: reservation.time || reservation.check_in_time || 'N/A' },
            { label: 'Quantity', value: reservation.quantity || 'N/A' },
        ];

        const eventEntries = [
            { label: 'Event Place', value: reservation.event_place || 'N/A' },
            { label: 'Event Type', value: reservation.event_type || 'N/A' },
            { label: 'Event Date', value: formatDateValue(reservation.event_date || 'N/A') },
            { label: 'Start Time', value: reservation.event_start_time || 'N/A' },
            { label: 'End Time', value: reservation.event_end_time || 'N/A' },
            { label: 'Event Hours', value: reservation.event_duration || 'N/A' },
            { label: 'Number of Guests', value: reservation.event_number_of_guests !== undefined && reservation.event_number_of_guests !== null && reservation.event_number_of_guests !== '' && reservation.event_number_of_guests !== 'N/A' ? reservation.event_number_of_guests : 'N/A' },
        ];

        const diningEntries = [
            { label: 'Dining Area/Table', value: reservation.dining_area || 'N/A' },
            { label: 'Date', value: formatDateValue(reservation.date || reservation.check_in) },
            { label: 'Time', value: reservation.time || reservation.dining_schedule || 'N/A' },
            { label: 'Number of Guests', value: reservation.number_of_guests || reservation.quantity || 'N/A' },
        ];

        if (category === 'rooms') {
            detailsSections.push(renderDetailsCard('Room Information', roomEntries));
        } else if (category === 'amenities') {
            detailsSections.push(renderDetailsCard('Amenity Information', amenityEntries));
        } else if (category === 'event_place') {
            detailsSections.push(renderDetailsCard('Event Information', eventEntries));
        } else if (category === 'dining') {
            detailsSections.push(renderDetailsCard('Dining Information', diningEntries));
        }

        const serviceList = services.map((name) => `<li class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">${escapeHtml(name)}</li>`).join('');
        const servicesTitle = category === 'dining' ? 'Menu/Meals' : 'Selected Services';
        detailsSections.push(`
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <h4 class="mb-3 text-base font-semibold text-gray-800">${servicesTitle}</h4>
                <ul class="space-y-2">${serviceList}</ul>
            </div>
        `);

        const paymentEntries = [
            { label: 'Payment Method', value: reservation.payment_method || 'N/A' },
            { label: 'Payment Details', value: reservation.payment_details || 'No payment details recorded' },
            { label: 'Amount Paid', value: formatMoney(reservation.amount_paid || 0) },
            { label: 'Total Amount', value: reservation.total_amount ? formatMoney(reservation.total_amount) : 'N/A' },
            { label: 'Status', value: status },
        ];

        const paymentProofUrl = reservation.payment_proof || (() => {
            if (typeof reservation.payment_details !== 'string') {
                return null;
            }

            const match = reservation.payment_details.match(/https?:\/\/\S+|\/storage\/\S+|data:image\/[a-zA-Z0-9.+-]+;base64,[A-Za-z0-9+/=]+/i);
            return match ? match[0] : null;
        })();

        detailsSections.push(renderDetailsCard('Payment Information', paymentEntries));

        if (paymentProofUrl) {
            detailsSections.push(`
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <h4 class="mb-3 text-base font-semibold text-gray-800">Payment Proof</h4>
                    <img src="${escapeHtml(paymentProofUrl)}" alt="Payment proof" class="max-h-72 w-full rounded-xl border border-gray-200 bg-white object-contain p-2" />
                </div>
            `);
        }

        document.getElementById('employeeDetailsBody').innerHTML = detailsSections.join('');

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

    document.querySelectorAll('form[action*="/reservations/"]').forEach((form) => {
        form.addEventListener('submit', function () {
            const category = this.closest('[data-reservation-panel]')?.dataset.reservationPanel;
            if (!category) return;

            let categoryInput = this.querySelector('input[name="category"]');
            if (!categoryInput) {
                categoryInput = document.createElement('input');
                categoryInput.type = 'hidden';
                categoryInput.name = 'category';
                this.appendChild(categoryInput);
            }
            categoryInput.value = category;
        });
    });

    function openAddReservationModal() {
        const modal = document.getElementById('addReservationModal');
        if (!modal) return;

        const activeTab = document.querySelector('[data-reservation-tab].bg-orange-500')?.dataset.reservationTab || 'rooms';
        const isEditing = !document.getElementById('reservationFormMethod').disabled;

        if (!isEditing) {
            resetAddReservationForm();
            document.getElementById('reservationCategory').value = activeTab;
            document.getElementById('reservationModalTitle').textContent = 'Add Reservation';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.style.display = 'flex';
        if (isEditing) {
            document.getElementById('reservationModalTitle').textContent = 'Edit Reservation';
        }
    }

    function resetAddReservationForm() {
        const form = document.getElementById('addReservationForm');
        const saveReservationBtn = document.getElementById('saveReservationBtn');
        const methodInput = document.getElementById('reservationFormMethod');
        if (!form) return;

        form.reset();
        methodInput.disabled = true;
        form.action = "{{ route('employee.reservations.store') }}";
        const categoryInput = document.getElementById('reservationCategory');
        if (categoryInput) {
            categoryInput.value = document.querySelector('[data-reservation-tab].bg-orange-500')?.dataset.reservationTab || 'rooms';
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
        const timeValue = value => value ? String(value).slice(0, 5) : '';
        const dateValue = value => value ? String(value).slice(0, 10) : '';
        const setValue = (selector, value) => {
            const input = form.querySelector(selector);
            if (input) input.value = value ?? '';
        };
        const category = reservation.category
            || (reservation.event_place_id ? 'event_place'
            : (reservation.amenity_id ? 'amenities'
            : (reservation.dining_area || reservation.dining_id ? 'dining' : 'rooms')));

        document.querySelector(`[data-reservation-tab="${category}"]`)?.click();
        setValue('[name="guest_name"]', reservation.guest_name);
        setValue('[name="guest_email"]', reservation.guest_email);
        setValue('[name="guest_phone"]', reservation.guest_phone);
        setValue(`[data-reservation-fields="${category}"] [name="room_id"], [data-reservation-fields="${category}"] [name="event_place_id"], [data-reservation-fields="${category}"] [name="amenity_id"], [data-reservation-fields="${category}"] [name="dining_area"]`, reservation.room_id || reservation.event_place_id || reservation.amenity_id || reservation.dining_area);

        if (category === 'event_place') {
            setValue('#eventDate', dateValue(reservation.check_in));
            setValue('[data-event-date-end]', dateValue(reservation.check_out || reservation.check_in));
            setValue('[name="event_type"]', reservation.event_type);
            const eventStart = timeValue(reservation.event_start_time || reservation.check_in_time);
            const eventEnd = timeValue(reservation.event_end_time || reservation.check_out_time);
            setValue('[name="event_start_time"]', eventStart);
            setValue('[name="event_end_time"]', eventEnd);
            const startMinutes = eventStart ? (Number(eventStart.slice(0, 2)) * 60 + Number(eventStart.slice(3, 5))) : 0;
            const endMinutes = eventEnd ? (Number(eventEnd.slice(0, 2)) * 60 + Number(eventEnd.slice(3, 5))) : 0;
            const eventDuration = eventStart && eventEnd ? Math.max(1, Math.round(((endMinutes - startMinutes + 1440) % 1440) / 60)) : 1;
            setValue('#employeeEventDuration', eventDuration);
            setValue('[data-reservation-fields="event_place"] [name="number_of_guests"]', reservation.number_of_guests);
        } else if (category === 'dining') {
            setValue('#diningDate', dateValue(reservation.check_in));
            setValue('[data-dining-time-end]', timeValue(reservation.check_out_time || reservation.check_in_time));
            setValue('[data-dining-date-end]', dateValue(reservation.check_out || reservation.check_in));
            setValue('[name="dining_schedule"]', reservation.dining_schedule);
            setValue('[name="quantity"]', reservation.quantity);
            const diningIds = String(reservation.dining_id || '').split(',').filter(Boolean);
            (reservation.dining_items || []).forEach((item) => {
                if (item.dining_id) diningIds.push(String(item.dining_id));
            });
            document.querySelectorAll('.dining-menu-checkbox').forEach((checkbox) => {
                checkbox.checked = diningIds.includes(checkbox.value);
                checkbox.dispatchEvent(new Event('change'));
            });
        } else if (category === 'amenities') {
            setValue('#amenityDate', dateValue(reservation.check_in));
            setValue('#amenityStartTime', timeValue(reservation.amenity_start_time || reservation.check_in_time));
            setValue('#amenityEndTime', timeValue(reservation.amenity_end_time || reservation.check_out_time));
            setValue('#amenityEndDate', dateValue(reservation.check_out || reservation.check_in));
            setValue('#amenityDurationHours', reservation.duration_hours || '');
            setValue('#employeeAmenityQuantity', reservation.amenity_quantity || reservation.quantity || '1');
        } else {
            setValue('[data-standard-reservation-field] [name="check_in"]', dateValue(reservation.check_in));
            setValue('[data-standard-reservation-field] [name="check_in_time"]', timeValue(reservation.room_check_in_time || reservation.check_in_time));
            setValue('[data-standard-reservation-field] [name="check_out"]', dateValue(reservation.check_out));
            setValue('[data-standard-reservation-field] [name="check_out_time"]', timeValue(reservation.room_check_out_time || reservation.check_out_time));
            setValue('[data-standard-reservation-field] [name="number_of_guests"]', reservation.number_of_guests);
        }

        setValue('[name="total_amount"]', reservation.total_amount || 0);
        setValue('[name="special_requests"]', reservation.special_requests);
        document.getElementById('reservationCategory').value = category;
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
