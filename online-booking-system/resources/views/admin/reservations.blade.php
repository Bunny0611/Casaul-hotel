@extends('admin.layout')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('admin.refunds') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"><i class="fas fa-rotate-left mr-2"></i>Refund History</a>
</div>
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

    .reservation-management-page .reservation-actions-column,
    .reservation-management-page .reservation-actions-cell {
        position: sticky;
        right: 0;
        min-width: 116px;
        width: 116px;
        white-space: nowrap;
        background: white;
        box-shadow: -8px 0 12px -12px rgba(15, 23, 42, 0.45);
    }

    .reservation-management-page .reservation-actions-column {
        z-index: 3;
        background: #f9fafb;
    }

    .reservation-management-page .reservation-actions-cell {
        z-index: 2;
    }
</style>
@php
    $roomReservationRows = $roomReservations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $roomReservations->getCollection() : $roomReservations;
    $facilityReservationRows = $facilitiesReservations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $facilitiesReservations->getCollection() : $facilitiesReservations;
    $eventReservationRows = $eventsReservations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $eventsReservations->getCollection() : $eventsReservations;
    $diningReservationRows = $diningReservations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $diningReservations->getCollection() : $diningReservations;

    // Calculate stats for each reservation type
    $roomStats = [
        'total' => $roomReservationRows->count(),
        'pending' => $roomReservationRows->where('status', 'pending')->count(),
        'confirmed' => $roomReservationRows->where('status', 'confirmed')->count(),
        'completed' => $roomReservationRows->where('status', 'completed')->count(),
        'cancelled' => $roomReservationRows->where('status', 'cancelled')->count(),
    ];
    
        $facilityStats = [
        'total' => $facilityReservationRows->count(),
        'pending' => $facilityReservationRows->where('status', 'pending')->count(),
        'confirmed' => $facilityReservationRows->where('status', 'confirmed')->count(),
        'completed' => $facilityReservationRows->where('status', 'completed')->count(),
        'cancelled' => $facilityReservationRows->where('status', 'cancelled')->count(),
    ];
    
        $eventStats = [
        'total' => $eventReservationRows->count(),
        'pending' => $eventReservationRows->where('status', 'pending')->count(),
        'confirmed' => $eventReservationRows->where('status', 'confirmed')->count(),
        'completed' => $eventReservationRows->where('status', 'completed')->count(),
        'cancelled' => $eventReservationRows->where('status', 'cancelled')->count(),
    ];
    
    $diningStats = [
        'total' => $diningReservationRows->count(),
        'pending' => $diningReservationRows->where('status', 'pending')->count(),
        'confirmed' => $diningReservationRows->where('status', 'confirmed')->count(),
        'completed' => $diningReservationRows->where('status', 'completed')->count(),
        'cancelled' => $diningReservationRows->where('status', 'cancelled')->count(),
    ];
    
    $currentStats = $roomStats;
        $allReservationRows = collect([$roomReservationRows, $facilityReservationRows, $eventReservationRows, $diningReservationRows])
        ->flatten(1)
        ->unique(fn ($row) => get_class($row) . ':' . $row->id)
        ->values();
    $overallReservationSummary = function ($reservation) use ($allReservationRows) {
        $relatedRows = $allReservationRows->filter(function ($row) use ($reservation) {
            return $row->guest_email === $reservation->guest_email
                && optional($row->check_in)->toDateString() === optional($reservation->check_in)->toDateString();
        });
        $paymentRow = $relatedRows->first(fn ($row) => !empty($row->payment_details) || !empty($row->payment_method));
        $paymentDetails = (string) ($paymentRow?->payment_details ?? $reservation->payment_details ?? '');
        $latestPayment = $relatedRows->flatMap(fn ($row) => $row->payments)->sortByDesc('created_at')->first();
        $reference = $latestPayment?->reference_number;
        if (!$reference && preg_match('/(?:Reference(?: Number)?|Ref)\s*:\s*([^•|]+)/i', $paymentDetails, $matches)) {
            $reference = trim($matches[1]);
        }
        $proof = $latestPayment?->payment_proof;
        if (!$proof && preg_match('/Proof:\s*(https?:\/\/\S+|storage\/[^•|\s]+)/i', $paymentDetails, $matches)) {
            $proof = $matches[1];
        }
        $paid = max(
            (float) $relatedRows->max(fn ($row) => (float) ($row->amount_paid ?? 0)),
            (float) $relatedRows->sum(fn ($row) => (float) $row->payments->sum('amount'))
        );
        $categoryAmounts = [
            'rooms' => (float) $relatedRows->filter(fn ($row) => $row->getTable() === 'room_reservations' || $row->getTable() === 'reservations' && ($row->category ?? null) === 'rooms')->sum(fn ($row) => (float) ($row->total_amount ?? 0)),
            'facilities' => (float) $relatedRows->filter(fn ($row) => $row->getTable() === 'facility_reservations' || $row->getTable() === 'reservations' && ($row->category ?? null) === 'facilities')->sum(fn ($row) => (float) ($row->total_amount ?? 0)),
            'events' => (float) $relatedRows->filter(fn ($row) => $row->getTable() === 'event_reservations' || $row->getTable() === 'reservations' && ($row->category ?? null) === 'event')->sum(fn ($row) => (float) ($row->total_amount ?? 0)),
            'dining' => (float) $relatedRows->filter(fn ($row) => $row->getTable() === 'dining_reservations' || $row->getTable() === 'reservations' && ($row->category ?? null) === 'dining')->sum(fn ($row) => (float) ($row->total_amount ?? 0)),
        ];
        $grandTotal = array_sum($categoryAmounts);
        $paid = min($paid, $grandTotal);

        return [
            'room_amount' => $categoryAmounts['rooms'],
            'facilities_amount' => $categoryAmounts['facilities'],
            'event_amount' => $categoryAmounts['events'],
            'dining_amount' => $categoryAmounts['dining'],
            'grand_total' => $grandTotal,
            'amount_paid' => $paid,
            'balance_due' => max($grandTotal - $paid, 0),
            'payment_method' => $paymentRow?->payment_method ?: ($latestPayment?->payment_method ?? 'N/A'),
            'reference_number' => $reference ?: 'N/A',
            'payment_proof' => $proof,
        ];
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

        $roomSelectedServices = function ($roomReservation) use ($facilitiesReservations) {
        $roomDate = $roomReservation->check_in ? \Carbon\Carbon::parse($roomReservation->check_in)->toDateString() : null;

        return $facilitiesReservations
            ->filter(function ($facilityReservation) use ($roomReservation, $roomDate) {
                $facilityDate = $facilityReservation->check_in ? \Carbon\Carbon::parse($facilityReservation->check_in)->toDateString() : null;
                return $facilityReservation->guest_email === $roomReservation->guest_email && $facilityDate === $roomDate;
            })
            ->map(function ($facilityReservation) {
                $name = $facilityReservation->facility?->name ?? 'Facility';
                $quantity = $facilityReservation->facility_quantity ?? $facilityReservation->quantity ?? 1;
                return 'Facility: ' . $name . ' (x' . $quantity . ')';
            })
            ->unique()
            ->values()
            ->all();
    };

    $employeeReservationDetails = function ($reservation, string $category) use ($uniqueCsvValue, $roomSelectedServices, $overallReservationSummary) {
        $latestPayment = $reservation->payments->last();
        $overallPayment = $overallReservationSummary($reservation);
        $paymentDetails = $reservation->payment_details ?: ($latestPayment?->reference_number
            ? 'Reference: ' . $latestPayment->reference_number . ($latestPayment->notes ? ' • ' . $latestPayment->notes : '')
            : ($latestPayment?->notes ?: 'No additional payment details'));
        $details = [
            'id' => $reservation->id,
            'category' => $category,
            'reservation_date' => $reservation->created_at ? $reservation->created_at->format('F j, Y g:i A') : 'N/A',
            'status' => ucfirst($reservation->status),
            'guest_name' => $reservation->guest_name,
            'guest_email' => $reservation->guest_email,
            'guest_phone' => $reservation->guest_phone,
            'special_requests' => $reservation->special_requests ?: 'No special requests',
            'selected_services' => $category === 'rooms' ? $roomSelectedServices($reservation) : [],
            'amount_paid' => $reservation->amount_paid ?? $latestPayment?->amount ?? 0,
            'payment_method' => $reservation->payment_method ?: ($latestPayment?->payment_method ?? 'N/A'),
            'payment_details' => $paymentDetails,
            'payment_proof' => $latestPayment?->payment_proof,
            'total_amount' => $reservation->total_amount ?? 0,
            'grand_total' => $overallPayment['grand_total'],
            'overall_amount_paid' => $overallPayment['amount_paid'],
            'balance_due' => $overallPayment['balance_due'],
            'overall_payment_method' => $overallPayment['payment_method'],
            'overall_reference_number' => $overallPayment['reference_number'],
            'overall_payment_proof' => $overallPayment['payment_proof'],
            'room_amount' => $overallPayment['room_amount'],
            'facilities_amount' => $overallPayment['facilities_amount'],
            'event_amount' => $overallPayment['event_amount'],
            'dining_amount' => $overallPayment['dining_amount'],
            'category_amounts' => [
                'Room' => $overallPayment['room_amount'],
                'Facilities' => $overallPayment['facilities_amount'],
                'Event' => $overallPayment['event_amount'],
                'Dining' => $overallPayment['dining_amount'],
            ],
        ];

        if ($category === 'rooms') {
            $details += [
                'room_number' => $reservation->room?->room_number ?? 'N/A',
                'room_type' => $reservation->room?->room_type ?? 'N/A',
                'room_check_in' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                'room_check_in_time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                'room_check_out' => $reservation->check_out?->format('Y-m-d') ?? 'N/A',
                'room_check_out_time' => $reservation->check_out_time ? \Carbon\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'N/A',
                'room_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
                'room_rate' => $reservation->room?->price ?? 'N/A',
            ];
        } elseif ($category === 'facilities') {
            $details += [
                'facility_name' => $reservation->facility?->name ?? 'N/A',
                'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                'time' => $reservation->check_in_time ? \Carbon\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'N/A',
                'quantity' => $reservation->quantity ?? 'N/A',
            ];
            $details['selected_services'] = $reservation->facility ? ['Facility: ' . $reservation->facility->name] : [];
        } elseif ($category === 'event') {
            $details += [
                'event_name' => $reservation->event?->name ?? 'N/A',
                'event_type' => $reservation->event_type ?? 'N/A',
                'event_date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                'event_start_time' => $reservation->event_start_time ? \Carbon\Carbon::parse($reservation->event_start_time)->format('g:i A') : 'N/A',
                'event_end_time' => $reservation->event_end_time ? \Carbon\Carbon::parse($reservation->event_end_time)->format('g:i A') : 'N/A',
                'event_duration' => $reservation->event_start_time && $reservation->event_end_time ? \Carbon\Carbon::parse($reservation->event_start_time)->diffInHours(\Carbon\Carbon::parse($reservation->event_end_time)) . 'h' : 'N/A',
                'event_number_of_guests' => $reservation->number_of_guests ?? 'N/A',
            ];
            $details['selected_services'] = $reservation->event ? ['Event: ' . $reservation->event->name . ($reservation->event_type ? ' — ' . $reservation->event_type : '')] : [];
        } else {
            $reservationMealDetails = $reservation->diningItems
                ->map(function ($item) {
                    $mealName = $item->diningMenu?->name ?? 'Meal Item';
                    return $mealName . ' (x' . ($item->quantity ?? 1) . ')';
                })
                ->unique()
                ->values()
                ->all();
            $details += [
                'dining_area' => $uniqueCsvValue($reservation->dining_area ?? 'N/A'),
                'date' => $reservation->check_in?->format('Y-m-d') ?? 'N/A',
                'time' => $uniqueCsvValue($reservation->dining_schedule ?? 'N/A'),
                'number_of_guests' => $reservation->quantity ?? $reservation->number_of_guests ?? 'N/A',
            ];
            $details['selected_services'] = $reservationMealDetails ?: ($reservation->dining_area ? ['Dining Area: ' . $reservation->dining_area] : []);
        }

        return $details;
    };
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
        <button type="button" data-tab="facilities" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">FACILITIES</button>
        <button type="button" data-tab="event" class="tab-button rounded-lg bg-white px-6 py-3 font-medium text-gray-600 transition hover:bg-gray-100">EVENTS</button>
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

    <div data-panel-stats="facilities" class="hidden grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $facilityStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $facilityStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $facilityStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $facilityStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $facilityStats['cancelled'] }}</p>
        </div>
    </div>

    <div data-panel-stats="event" class="hidden grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $eventStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $eventStats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $eventStats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $eventStats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $eventStats['cancelled'] }}</p>
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
                        <th class="reservation-actions-column px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($roomReservations as $reservation)
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-source="{{ $reservation->getTable() }}" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->room?->room_number ?? '')) }}">
                            <td class="reservation-actions-cell px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}<br><span class="text-xs text-gray-500">{{ $reservation->check_in_time ? \Illuminate\Support\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'Time not set' }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_out instanceof \Illuminate\Support\Carbon ? $reservation->check_out->format('M d, Y') : $reservation->check_out }}<br><span class="text-xs text-gray-500">{{ $reservation->check_out_time ? \Illuminate\Support\Carbon::parse($reservation->check_out_time)->format('g:i A') : 'Time not set' }}</span></td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick="showAdminReservationDetails(this)" data-reservation="{{ e(json_encode($employeeReservationDetails($reservation, 'rooms'))) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details" aria-label="View details"><i class="fas fa-eye"></i></button>
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
        @if($roomReservations->hasPages())
            <div class="border-t border-gray-200 bg-white">
                {{ $roomReservations->links('pagination.reservations') }}
            </div>
        @endif
    </div>

    <!-- FACILITIES PANEL -->
    <div data-panel="facilities" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Facility</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="reservation-actions-column px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($facilitiesReservations as $reservation)
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-source="{{ $reservation->getTable() }}" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->facility?->name ?? '')) }}">
                            <td class="reservation-actions-cell px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->facility ? $reservation->facility->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->facility_start_time ? \Illuminate\Support\Carbon::parse($reservation->facility_start_time)->format('g:i A') : 'Time not set' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->quantity ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick="showAdminReservationDetails(this)" data-reservation="{{ e(json_encode($employeeReservationDetails($reservation, 'facilities'))) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details" aria-label="View details"><i class="fas fa-eye"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status === 'confirmed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button> @endif
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
                                <p class="text-lg font-medium">No facility reservations found.</p>
                                <p class="mt-1 text-sm">Facility reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($facilitiesReservations->hasPages())
            <div class="border-t border-gray-200 bg-white">
                {{ $facilitiesReservations->links('pagination.reservations') }}
            </div>
        @endif
    </div>

    <!-- EVENT PLACE PANEL -->
    <div data-panel="event" class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="reservation-table-shell hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Guest/Client</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Event Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Start Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">End Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="reservation-actions-column px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($eventsReservations as $reservation)
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-source="{{ $reservation->getTable() }}" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->event?->name ?? '')) }}">
                            <td class="reservation-actions-cell px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->event ? $reservation->event->name : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_type ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_start_time ? \Illuminate\Support\Carbon::parse($reservation->event_start_time)->format('g:i A') : 'Time not set' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->event_end_time ? \Illuminate\Support\Carbon::parse($reservation->event_end_time)->format('g:i A') : 'Time not set' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick="showAdminReservationDetails(this)" data-reservation="{{ e(json_encode($employeeReservationDetails($reservation, 'event'))) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details" aria-label="View details"><i class="fas fa-eye"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status === 'confirmed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button> @endif
                                        @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel</button> @endif
                                        <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');"><button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50"><i class="fas fa-trash mr-2 w-4"></i>Delete</button>@csrf @method('DELETE')</form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No event reservations found.</p>
                                <p class="mt-1 text-sm">Event reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($eventsReservations->hasPages())
            <div class="border-t border-gray-200 bg-white">
                {{ $eventsReservations->links('pagination.reservations') }}
            </div>
        @endif
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
                        <th class="reservation-actions-column px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($diningReservations as $reservation)
                            <tr class="reservation-item transition-colors hover:bg-gray-50" data-source="{{ $reservation->getTable() }}" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->dining_area ?? '')) }}">
                            <td class="reservation-actions-cell px-4 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $uniqueCsvValue($reservation->dining_area ?? 'N/A') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon ? $reservation->check_in->format('F j, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $uniqueCsvValue($reservation->dining_schedule ?? 'N/A') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->quantity ?? $reservation->number_of_guests ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="relative flex items-center gap-2 text-sm">
                                    <button type="button" onclick="showAdminReservationDetails(this)" data-reservation="{{ e(json_encode($employeeReservationDetails($reservation, 'dining'))) }}" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="View details" aria-label="View details"><i class="fas fa-eye"></i></button>
                                    <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg p-2 text-slate-600 transition hover:bg-slate-100" title="More actions" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                        @if($reservation->status === 'pending') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Confirm</button> @endif
                                        @if($reservation->status === 'confirmed') <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Mark as Completed</button> @endif
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
        @if($diningReservations->hasPages())
            <div class="border-t border-gray-200 bg-white">
                {{ $diningReservations->links('pagination.reservations') }}
            </div>
        @endif
    </div>
</div>

<div id="adminReservationDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="max-h-[85vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800">Reservation Details</h3>
            <button type="button" onclick="closeAdminReservationDetails()" class="text-gray-500 hover:text-gray-800" aria-label="Close details"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div id="adminReservationDetailsContent" class="max-h-[70vh] space-y-5 overflow-y-auto p-6"></div>
    </div>
</div>

<form id="reservationStatusForm" action="" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="reservationStatus">
</form>

<script>
    function formatAdminMoney(value) {
        const amount = Number(value || 0);
        return Number.isFinite(amount) ? `₱${amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '₱0.00';
    }

    function formatAdminDate(value) {
        if (!value || value === 'N/A') return 'N/A';
        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? value : parsed.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    function escapeAdminHtml(value) {
        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderAdminDetailsCard(title, entries) {
        const entriesHtml = entries.map((entry) => `
            <div class="rounded-xl border border-gray-200 bg-white p-3">
                <div class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">${escapeAdminHtml(entry.label)}</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">${escapeAdminHtml(entry.value ?? 'N/A')}</div>
            </div>
        `).join('');
        return `<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4"><h4 class="mb-3 text-base font-semibold text-gray-800">${escapeAdminHtml(title)}</h4><div class="grid gap-3 sm:grid-cols-2">${entriesHtml}</div></div>`;
    }

    function parseAdminPaymentDetails(value) {
        const details = { accountName: 'N/A', accountNumber: 'N/A', amount: 'N/A', referenceNumber: 'N/A', proof: '' };
        String(value || '').split(/\s*[•|]\s*/).forEach((part) => {
            const separator = part.indexOf(':');
            if (separator < 0) return;
            const label = part.slice(0, separator).trim().toLowerCase();
            const detail = part.slice(separator + 1).trim();
            if (label === 'account' || label === 'account name') details.accountName = detail || 'N/A';
            if (label === 'number' || label === 'account number') details.accountNumber = detail || 'N/A';
            if (label === 'amount') details.amount = detail || 'N/A';
            if (label === 'reference' || label === 'reference number') details.referenceNumber = detail || 'N/A';
            if (label === 'proof' || label === 'payment proof') details.proof = detail;
        });
        return details;
    }

    function resolveAdminPaymentProof(value) {
        const proof = String(value || '').trim();
        if (!proof) return '';
        if (/^(https?:\/\/|data:image\/|\/)/i.test(proof)) return proof;
        if (!/^storage\//i.test(proof) && !proof.includes('/')) return '';
        return `{{ asset('storage') }}/${proof.replace(/^storage\//i, '')}`;
    }

    function showAdminReservationDetails(button) {
        const rawReservation = button.getAttribute('data-reservation') || '{}';
        let reservation;
        try {
            reservation = JSON.parse(rawReservation);
        } catch (error) {
            const decodedReservation = rawReservation
                .replace(/&quot;/g, '"')
                .replace(/&#039;/g, "'")
                .replace(/&amp;/g, '&');
            reservation = JSON.parse(decodedReservation);
        }
        const services = Array.isArray(reservation.selected_services) && reservation.selected_services.length ? reservation.selected_services : ['No additional services selected'];
        const detailsSections = [
            renderAdminDetailsCard('Reservation Information', [
                { label: 'Reservation ID', value: reservation.id ? `RES-${reservation.id}` : 'N/A' },
                { label: 'Reservation Date', value: reservation.reservation_date || 'N/A' },
                { label: 'Status', value: reservation.status || 'N/A' },
            ]),
            renderAdminDetailsCard('Guest Information', [
                { label: 'Full Name', value: reservation.guest_name || 'N/A' },
                { label: 'Email', value: reservation.guest_email || 'N/A' },
                { label: 'Mobile Number', value: reservation.guest_phone || 'N/A' },
                { label: 'Special Request', value: reservation.special_requests || 'No special requests' },
            ])
        ];
        const categoryEntries = {
            rooms: [
                ['Room Number', reservation.room_number], ['Room Type', reservation.room_type], ['Check-in Date', formatAdminDate(reservation.room_check_in)], ['Check-in Time', reservation.room_check_in_time], ['Check-out Date', formatAdminDate(reservation.room_check_out)], ['Check-out Time', reservation.room_check_out_time], ['Number of Guests', reservation.room_number_of_guests], ['Room Rate', reservation.room_rate && reservation.room_rate !== 'N/A' ? formatAdminMoney(reservation.room_rate) : 'N/A']
            ],
            facilities: [['Facility', reservation.facility_name], ['Date', formatAdminDate(reservation.date)], ['Time', reservation.time], ['Quantity', reservation.quantity]],
            event: [['Event', reservation.event_name], ['Event Type', reservation.event_type], ['Event Date', formatAdminDate(reservation.event_date)], ['Start Time', reservation.event_start_time], ['End Time', reservation.event_end_time], ['Event Hours', reservation.event_duration], ['Number of Guests', reservation.event_number_of_guests]],
            dining: [['Dining Area/Table', reservation.dining_area], ['Date', formatAdminDate(reservation.date)], ['Time', reservation.time], ['Number of Guests', reservation.number_of_guests]],
        };
        const categoryTitles = { rooms: 'Room Information', facilities: 'Facility Information', event: 'Event Information', dining: 'Dining Information' };
        detailsSections.push(renderAdminDetailsCard(categoryTitles[reservation.category] || 'Reservation Information', (categoryEntries[reservation.category] || []).map(([label, value]) => ({ label, value: value || 'N/A' }))));
        const servicesTitle = reservation.category === 'dining' ? 'Menu/Meals' : 'Selected Services';
        detailsSections.push(`<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4"><h4 class="mb-3 text-base font-semibold text-gray-800">${servicesTitle}</h4><ul class="space-y-2">${services.map((service) => `<li class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">${escapeAdminHtml(service)}</li>`).join('')}</ul></div>`);
        const categoryAmountLabels = { rooms: 'Room', facilities: 'Facilities', event: 'Event', dining: 'Dining' };
        detailsSections.push(renderAdminDetailsCard('Reservation Amounts', [
            { label: categoryAmountLabels[reservation.category] || 'Reservation', value: formatAdminMoney(reservation.total_amount || 0) },
        ]));
        const paymentDetails = parseAdminPaymentDetails(reservation.payment_details);
        const paymentProofUrl = resolveAdminPaymentProof(reservation.overall_payment_proof || reservation.payment_proof || paymentDetails.proof);
        detailsSections.push(renderAdminDetailsCard('Payment Summary', [
            { label: 'Grand Total', value: formatAdminMoney(reservation.grand_total || reservation.total_amount || 0) },
            { label: 'Amount Paid', value: formatAdminMoney(reservation.overall_amount_paid || 0) },
            { label: 'Balance Due', value: formatAdminMoney(reservation.balance_due || 0) },
            { label: 'Payment Method', value: reservation.overall_payment_method || reservation.payment_method || 'N/A' },
            { label: 'Reference Number', value: reservation.overall_reference_number || paymentDetails.referenceNumber || 'N/A' },
        ]));
        detailsSections.push(`<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4"><h4 class="mb-3 text-base font-semibold text-gray-800">Payment Proof</h4>${paymentProofUrl ? `<a href="${escapeAdminHtml(paymentProofUrl)}" target="_blank" rel="noopener noreferrer" class="block"><img src="${escapeAdminHtml(paymentProofUrl)}" alt="Payment proof" class="max-h-72 w-full rounded-xl border border-gray-200 bg-white object-contain p-2" /></a>` : '<p class="text-sm text-gray-600">No payment proof uploaded.</p>'}</div>`);
        document.getElementById('adminReservationDetailsContent').innerHTML = detailsSections.join('');
        const modal = document.getElementById('adminReservationDetailsModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAdminReservationDetails() {
        const modal = document.getElementById('adminReservationDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

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

    document.querySelectorAll('[data-panel]').forEach(panel => {
        const category = panel.dataset.panel;
        const rows = [...panel.querySelectorAll('.reservation-item')];
        if (!rows.length) return;

        const toolbar = document.createElement('div');
        toolbar.className = 'flex flex-wrap items-center justify-end gap-3 border-b border-gray-200 bg-white px-6 py-4';
        toolbar.innerHTML = `<span class="bulk-selected-count text-sm font-medium text-gray-500">0 selected</span><button type="button" class="bulk-delete-button hidden rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700"><i class="fas fa-trash mr-2"></i>Delete Selected</button>`;
        const table = panel.querySelector('table');
        table?.parentElement?.parentElement?.prepend(toolbar);

        const headerCell = table?.querySelector('thead tr th');
        const selectAll = document.createElement('input');
        selectAll.type = 'checkbox';
        selectAll.className = 'bulk-select-all h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500';
        selectAll.setAttribute('aria-label', 'Select all reservations on this page');
        const selectAllCell = document.createElement('th');
        selectAllCell.className = 'w-14 px-4 py-4 text-left';
        selectAllCell.appendChild(selectAll);
        headerCell?.parentElement?.insertBefore(selectAllCell, headerCell);

        const selectedCount = toolbar.querySelector('.bulk-selected-count');
        const deleteButton = toolbar.querySelector('.bulk-delete-button');
        const updateSelection = () => {
            const checkboxes = [...panel.querySelectorAll('.reservation-select')];
            const selected = checkboxes.filter(checkbox => checkbox.checked);
            selectedCount.textContent = `${selected.length} selected`;
            deleteButton.classList.toggle('hidden', selected.length === 0);
            selectAll.checked = checkboxes.length > 0 && selected.length === checkboxes.length;
        };

        rows.forEach(row => {
            const id = row.querySelector('form[action*="/reservations/"]')?.action.match(/\/reservations\/(\d+)/)?.[1];
            const source = row.dataset.source;
            if (!id || !source) return;
            const cell = row.querySelector('td');
            if (!cell) return;
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = `${source}:${id}`;
            checkbox.className = 'reservation-select h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500';
            checkbox.setAttribute('aria-label', 'Select reservation');
            checkbox.addEventListener('change', updateSelection);
            const checkboxCell = document.createElement('td');
            checkboxCell.className = 'w-14 px-4 py-4 align-top';
            checkboxCell.appendChild(checkbox);
            cell.parentElement.insertBefore(checkboxCell, cell);
        });

        selectAll.addEventListener('change', () => {
            const checkboxes = panel.querySelectorAll('.reservation-select');
            checkboxes.forEach(checkbox => { checkbox.checked = selectAll.checked; });
            updateSelection();
        });

        deleteButton.addEventListener('click', () => {
            const selected = [...panel.querySelectorAll('.reservation-select:checked')].map(checkbox => checkbox.value);
            if (!selected.length || !confirm(`Delete ${selected.length} selected reservation(s)?`)) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('admin.reservations.bulk-destroy') }}";
            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="category" value="${category}">${selected.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}`;
            document.body.appendChild(form);
            form.submit();
        });
    });
</script>

@endsection
