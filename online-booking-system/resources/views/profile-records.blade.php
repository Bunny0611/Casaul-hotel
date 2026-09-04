
@extends('app')

@section('content')
<style>
    .reservation-action-group {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        position: relative;
    }

    .records-table:not(.guest-requests-table) th:last-child,
    .records-table:not(.guest-requests-table) td:last-child {
        text-align: left;
        white-space: nowrap;
    }

    .reservation-view-btn,
    .reservation-menu-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .reservation-view-btn {
        min-width: 128px;
        border: 1px solid #d62839;
        background: #fff;
        color: #b91c1c;
        box-shadow: 0 2px 8px rgba(182, 36, 58, 0.08);
    }

    .reservation-view-btn:hover {
        background: #fff3f4;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(182, 36, 58, 0.12);
    }

    .reservation-menu-toggle {
        width: 38px;
        height: 38px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #475569;
        padding: 0;
    }

    .reservation-menu-toggle:hover {
        background: #f8fafc;
    }

    .reservation-menu-wrap {
        position: relative;
    }

    .reservation-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 190px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        z-index: 10;
        overflow: hidden;
    }

    .reservation-menu.is-viewport-menu {
        position: fixed;
        top: auto;
        right: auto;
    }

    .reservation-menu.hidden {
        display: none;
    }

    .reservation-menu-item {
        width: 100%;
        border: 0;
        background: transparent;
        text-align: left;
        padding: 10px 14px;
        font-size: 0.82rem;
        color: #1f2937;
        cursor: pointer;
    }

    .reservation-menu-item:hover {
        background: #f8fafc;
    }

    .reservation-menu-item--danger {
        color: #b91c1c;
        font-weight: 600;
    }

    .reservation-menu-item--danger:hover {
        background: #fff1f2;
    }

    .receipt-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.6);
        z-index: 10000;
    }

    .receipt-modal.open {
        display: flex;
    }

    .receipt-card {
        width: min(720px, 100%);
        max-height: calc(100vh - 40px);
        overflow-y: auto;
        padding: 28px;
        border-radius: 4px;
        background: #fff;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.2);
    }

    .receipt-header {
        position: relative;
        display: block;
        padding-bottom: 14px;
        border-bottom: 4px solid #c7d8e8;
    }

    .receipt-brand {
        margin: 0;
        color: #07549a;
        font-size: 32px;
        font-weight: 400;
        text-align: center;
    }

    .receipt-contact {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 18px;
        margin: 12px 0 4px;
        color: #727b85;
        font-size: 12px;
    }

    .receipt-contact span {
        white-space: nowrap;
    }

    .receipt-contact i {
        margin-right: 5px;
        color: #727b85;
    }

    .receipt-subcontact {
        margin: 0;
        color: #727b85;
        font-size: 12px;
        text-align: center;
    }

    .receipt-subcontact i {
        margin-right: 5px;
    }

    .receipt-heading-row {
        margin: 24px 0 16px;
        text-align: center;
    }

    .receipt-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin: 0 0 18px;
    }

    .receipt-paid-by h4,
    .receipt-booking h4,
    .receipt-notes h4 {
        margin: 0 0 8px;
        color: #07549a;
        font-size: 14px;
    }

    .receipt-paid-by p,
    .receipt-booking p,
    .receipt-notes p {
        margin: 3px 0;
        color: #4b5563;
        font-size: 13px;
    }

    .receipt-heading {
        margin: 0;
        color: #07549a;
        font-size: 32px;
        letter-spacing: 0.04em;
    }

    .receipt-booking {
        min-width: 220px;
    }

    .receipt-booking-details {
        margin: 0 0 18px;
    }

    .receipt-booking p {
        display: flex;
        justify-content: space-between;
        gap: 18px;
    }

    .receipt-booking strong {
        color: #4b5563;
        font-weight: 600;
    }

    .receipt-close {
        position: absolute;
        top: -8px;
        right: -8px;
        border: 0;
        background: transparent;
        color: #64748b;
        font-size: 22px;
        cursor: pointer;
    }

    .receipt-content {
        color: #566176;
        font-size: 12px;
        line-height: 1.5;
    }

    .reservation-detail-section {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #d9e5ef;
    }

    .reservation-detail-section h4 {
        margin: 0 0 10px;
        color: #07549a;
        font-size: 14px;
    }

    .reservation-detail-section p {
        margin: 6px 0 0;
    }

    .reservation-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 16px;
    }

    .reservation-detail-grid--summary {
        padding: 12px;
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
    }

    .reservation-detail-field {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 4px 0;
    }

    .reservation-detail-field span {
        color: #64748b;
    }

    .reservation-detail-field strong {
        color: #334155;
        text-align: right;
        overflow-wrap: anywhere;
    }

    .reservation-detail-item {
        padding: 8px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .reservation-detail-item strong {
        color: #334155;
    }

    .reservation-detail-item > span {
        float: right;
        color: #07549a;
        font-weight: 700;
    }

    .reservation-detail-item p,
    .reservation-detail-empty {
        color: #64748b;
    }

    .receipt-table {
        width: 100%;
        border: 1px solid #7fa9d0;
        border-radius: 4px;
        border-spacing: 0;
        overflow: hidden;
    }

    .receipt-table th {
        padding: 9px 8px;
        color: #fff;
        background: #07549a;
        font-size: 11px;
        text-align: left;
    }

    .receipt-table td {
        padding: 9px 8px;
        border-top: 1px solid #d9e5ef;
        color: #4b5563;
        font-size: 12px;
    }

    .receipt-table th:not(:first-child),
    .receipt-table td:not(:first-child) {
        text-align: right;
    }

    .receipt-table .receipt-total-row td {
        border-top: 2px solid #7fa9d0;
        color: #07549a;
        font-weight: 800;
    }

    .receipt-notes {
        margin-top: 18px;
    }

    .receipt-notes h4 {
        margin: 0 0 6px;
        color: #07549a;
        font-size: 14px;
    }

    .receipt-notes p {
        margin: 0;
        color: #4b5563;
        font-size: 12px;
    }

    .receipt-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
    }

    .receipt-actions button {
        border: 0;
        border-radius: 7px;
        padding: 10px 16px;
        color: #fff;
        background: #d20b26;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .receipt-actions .receipt-print-btn {
        background: #253570;
    }

    @media (max-width:700px) {
        .receipt-card {
            padding: 18px;
        }

        .receipt-brand,
        .receipt-heading {
            font-size: 25px;
        }

        .receipt-title-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .receipt-booking {
            min-width: 0;
            width: 100%;
        }

        .receipt-actions button {
            flex: 1;
        }

        .reservation-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero">
        <p class="eyebrow">My Account</p>
        <h1>My Records</h1>
        <p>Here is a list of all your reservations.</p>
    </section>

    <div class="records-toolbar">
        <a href="{{ route('guest.profile') }}" class="btn btn-back">&larr; Back to Profile</a>
    </div>

    @if(session('success'))
        <div class="reservation-alert reservation-alert--success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="reservation-alert" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    @if($reservations->isEmpty())
        <div class="records-empty">
            <i class="fas fa-inbox"></i>
            <h3>No reservations yet</h3>
            <p>You haven't made any reservations. Book a room to get started!</p>
            <a href="{{ route('reservation') }}" class="btn">Make a Reservation</a>
        </div>
    @else
        <div class="records-table-wrap">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $index => $reservation)
                        @php
                            $reservationReceiptLines = [];
                            if ($reservation->room) {
                                $nights = max(1, \Carbon\Carbon::parse($reservation->check_in)->diffInDays(\Carbon\Carbon::parse($reservation->check_out)));
                                $roomCharge = (float) $reservation->room->price * $nights;
                                $reservationReceiptLines[] = [
                                    'quantity' => 1,
                                    'description' => 'Room - ' . ($reservation->room->room_type ?? 'Room'),
                                    'unitPrice' => '₱' . number_format($roomCharge, 2),
                                    'amount' => '₱' . number_format($roomCharge, 2),
                                ];
                            }

                            $amenityIds = $reservation->amenity_id ? array_values(array_filter(array_map('trim', explode(',', (string) $reservation->amenity_id)))) : [];
                            foreach ($amenityIds as $amenityId) {
                                $amenity = \App\Models\Amenity::find($amenityId);
                                if ($amenity) {
                                    $reservationReceiptLines[] = [
                                        'quantity' => 1,
                                        'description' => 'Amenities - ' . $amenity->name,
                                        'unitPrice' => '₱' . number_format((float) $amenity->price, 2),
                                        'amount' => '₱' . number_format((float) $amenity->price, 2),
                                    ];
                                }
                            }

                            $eventIds = $reservation->event_place_id ? array_values(array_filter(array_map('trim', explode(',', (string) $reservation->event_place_id)))) : [];
                            foreach ($eventIds as $eventId) {
                                $event = \App\Models\EventPlace::find($eventId);
                                if ($event) {
                                    $reservationReceiptLines[] = [
                                        'quantity' => 1,
                                        'description' => 'Event - ' . $event->name,
                                        'unitPrice' => '₱' . number_format((float) $event->price, 2),
                                        'amount' => '₱' . number_format((float) $event->price, 2),
                                    ];
                                }
                            }

                            foreach ($reservation->diningItems as $diningItem) {
                                $menu = $diningItem->diningMenu;
                                if ($menu) {
                                    $lineTotal = (float) $menu->price * (int) $diningItem->quantity;
                                    $reservationReceiptLines[] = [
                                        'quantity' => (int) $diningItem->quantity,
                                        'description' => 'Dining - ' . $menu->name,
                                        'unitPrice' => '₱' . number_format((float) $menu->price, 2),
                                        'amount' => '₱' . number_format($lineTotal, 2),
                                    ];
                                }
                            }

                            $reservationDetails = [
                                'id' => 'RES-' . str_pad($reservation->id, 4, '0', STR_PAD_LEFT),
                                'guestName' => $reservation->guest_name,
                                'guestEmail' => $reservation->guest_email,
                                'guestPhone' => $reservation->guest_phone,
                                'status' => ucfirst($reservation->status),
                                'category' => $reservation->category,
                                'checkIn' => optional($reservation->check_in)->format('M d, Y'),
                                'checkInTime' => $reservation->check_in_time,
                                'checkOut' => optional($reservation->check_out)->format('M d, Y'),
                                'checkOutTime' => $reservation->check_out_time,
                                'guests' => $reservation->number_of_guests,
                                'room' => $reservation->room ? [
                                    'number' => $reservation->room->room_number,
                                    'type' => $reservation->room->room_type,
                                    'floor' => $reservation->room->floor,
                                    'capacity' => $reservation->room->capacity,
                                    'price' => '₱' . number_format((float) $reservation->room->price, 2),
                                    'description' => $reservation->room->description,
                                ] : null,
                                'amenities' => $reservation->amenities->map(fn ($amenity) => [
                                    'name' => $amenity->name,
                                    'description' => $amenity->description,
                                    'price' => '₱' . number_format((float) $amenity->price, 2),
                                ])->values(),
                                'eventPlaces' => $reservation->eventPlaces->map(fn ($eventPlace) => [
                                    'name' => $eventPlace->name,
                                    'type' => $eventPlace->event_type,
                                    'location' => $eventPlace->location,
                                    'capacity' => $eventPlace->capacity,
                                    'price' => '₱' . number_format((float) $eventPlace->price, 2),
                                    'description' => $eventPlace->description,
                                ])->values(),
                                'dining' => $reservation->diningItems->map(fn ($diningItem) => [
                                    'name' => $diningItem->diningMenu?->name,
                                    'category' => $diningItem->diningMenu?->category,
                                    'quantity' => $diningItem->quantity,
                                    'area' => $diningItem->dining_area,
                                    'schedule' => $diningItem->dining_schedule,
                                    'date' => optional($diningItem->dining_date)->format('M d, Y'),
                                    'price' => $diningItem->diningMenu ? '₱' . number_format((float) $diningItem->diningMenu->price, 2) : null,
                                ])->filter(fn ($item) => $item['name'])->values(),
                                'paymentMethod' => $reservation->payment_method,
                                'paymentDetails' => $reservation->payment_details,
                                'payments' => $reservation->payments->map(fn ($payment) => [
                                    'amount' => '₱' . number_format((float) $payment->amount, 2),
                                    'method' => $payment->payment_method,
                                    'date' => optional($payment->payment_date)->format('M d, Y'),
                                    'reference' => $payment->reference_number,
                                    'notes' => $payment->notes,
                                ])->values(),
                                'amountPaid' => '₱' . number_format((float) $reservation->amount_paid, 2),
                                'total' => '₱' . number_format((float) $reservation->total_amount, 2),
                                'specialRequests' => $reservation->special_requests,
                                'eventType' => $reservation->event_type,
                                'diningArea' => $reservation->dining_area,
                                'diningSchedule' => $reservation->dining_schedule,
                            ];
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $reservation->room->room_type ?? 'Room' }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td>
                                @if($reservation->status === 'completed')
                                    <div class="reservation-action-group">
                                        <button type="button"
                                            class="reservation-view-btn"
                                            data-view-receipt
                                            data-reservation='@json($reservationDetails)'
                                            data-receipt-id="RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}"
                                            data-guest-name="{{ auth('guest')->user()->name }}"
                                            data-guest-email="{{ auth('guest')->user()->email }}"
                                            data-check-in="{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}"
                                            data-check-out="{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}"
                                            data-guests="{{ $reservation->number_of_guests ?? 2 }} Guests"
                                            data-room="{{ $reservation->room->room_type ?? 'Room' }}"
                                            data-total="₱{{ number_format($reservation->total_amount, 2) }}"
                                            data-line-items='@json($reservationReceiptLines)'>
                                            View Receipt
                                        </button>
                                        <div class="reservation-menu-wrap">
                                            <button type="button" class="reservation-menu-toggle" aria-label="More actions" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="reservation-menu hidden">
                                                <button type="button" class="reservation-menu-item" data-view-receipt data-reservation='@json($reservationDetails)' data-receipt-id="RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}" data-guest-name="{{ auth('guest')->user()->name }}" data-guest-email="{{ auth('guest')->user()->email }}" data-check-in="{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}" data-check-out="{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}" data-guests="{{ $reservation->number_of_guests ?? 2 }} Guests" data-room="{{ $reservation->room->room_type ?? 'Room' }}" data-total="₱{{ number_format($reservation->total_amount, 2) }}" data-line-items='@json($reservationReceiptLines)'>View Receipt</button>
                                            </div>
                                        </div>
                                    </div>
                                @elseif(in_array($reservation->status, ['pending', 'confirmed'], true))
                                    <div class="reservation-action-group">
                                        <button type="button" class="reservation-view-btn" data-view-receipt data-reservation='@json($reservationDetails)'>View</button>
                                        <div class="reservation-menu-wrap">
                                            <button type="button" class="reservation-menu-toggle" aria-label="More actions" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="reservation-menu hidden">
                                                @if($reservation->status === 'confirmed')
                                                    <form method="POST" action="{{ route('guest.reservations.delete', $reservation) }}" onsubmit="return confirm('Delete this confirmed reservation? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="reservation-menu-item reservation-menu-item--danger">Delete Reservation</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('guest.reservations.cancel', $reservation) }}" onsubmit="return confirm('Cancel this reservation? This action cannot be undone.');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="reservation-menu-item reservation-menu-item--danger">Cancel Reservation</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($reservation->status === 'checked-in')
                                    <div class="reservation-action-group">
                                        <button type="button" class="reservation-view-btn" data-view-receipt data-reservation='@json($reservationDetails)'>View</button>
                                        <div class="reservation-menu-wrap">
                                            <button type="button" class="reservation-menu-toggle" aria-label="More actions" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="reservation-menu hidden">
                                                <form method="POST" action="{{ route('guest.reservations.delete', $reservation) }}" onsubmit="return confirm('Delete this checked-in reservation? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="reservation-menu-item reservation-menu-item--danger">Delete Reservation</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <section class="guest-request-section" id="guest-request-form">
        <div class="guest-request-heading">
            <p class="eyebrow">During Your Stay</p>
            <h2>Request Assistance</h2>
            <p>Let us know what you need and our team will take care of it.</p>
        </div>

        @if(session('request_success'))
            <div class="reservation-alert reservation-alert--success" role="status">
                {{ session('request_success') }} Request ID: REQ-{{ str_pad(session('request_id'), 4, '0', STR_PAD_LEFT) }}.
            </div>
        @endif

        @if(!$activeReservation)
            <div class="records-empty guest-request-unavailable">
                <i class="fas fa-calendar-check"></i>
                <h3>No active reservation</h3>
                <p>Request assistance is available while you have a confirmed or checked-in stay.</p>
            </div>
        @else
            <form method="POST" action="{{ route('guest.requests.store') }}" class="guest-request-form">
                @csrf
                <div class="guest-request-summary">
                    <div><span>Guest Name</span><strong>{{ auth('guest')->user()->name }}</strong></div>
                    <div><span>Room Number</span><strong>{{ $activeReservation->room->room_number ?? 'Assigned room' }}</strong></div>
                    <div><span>Reservation ID</span><strong>RES-{{ str_pad($activeReservation->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
                </div>
                @if($errors->any())
                    <div class="reservation-alert" role="alert">{{ $errors->first() }}</div>
                @endif
                <div class="guest-request-fields">
                    <div class="request-type-picker" data-request-type-picker>
                        <label for="request-type-search">Request Type</label>
                        <div class="request-type-field">
                            <div id="selected-request-items" aria-live="polite"></div>
                            <input id="request-type-search" class="request-type-search" type="search" placeholder="Select what you need" autocomplete="off" aria-label="Search request types" aria-controls="request-type-options" aria-expanded="true" role="combobox">
                            <button type="button" class="request-type-toggle" aria-label="Show request types" aria-controls="request-type-options" aria-expanded="true"><i class="fas fa-chevron-down"></i></button>
                        </div>
                        <div class="request-type-options" id="request-type-options" role="listbox">
                            <div class="request-type-group" data-request-type-group>
                                <span class="request-type-group-label">HOUSEKEEPING</span>
                                @foreach(['Extra Towels', 'Extra Pillows', 'Extra Blanket', 'Toiletries', 'Room Cleaning', 'Change Bedsheets', 'Other Housekeeping Request'] as $type)
                                    <button type="button" role="option" class="request-type-option" data-value="{{ $type }}">{{ $type }}</button>
                                @endforeach
                            </div>
                            <div class="request-type-group" data-request-type-group>
                                <span class="request-type-group-label">General Assistance</span>
                                @foreach(['Broken Aircon', 'Broken TV', 'Broken Light', 'Plumbing/Water Problem', 'Late Checkout', 'Early Check-in', 'Dining/Food Request', 'Transportation Request', 'Other Request'] as $type)
                                    <button type="button" role="option" class="request-type-option" data-value="{{ $type }}">{{ $type }}</button>
                                @endforeach
                            </div>
                            <p class="request-type-empty" hidden>No request types found.</p>
                        </div>
                        <input type="hidden" name="request_items" id="request-items-value" value="{{ old('request_items') }}" required>
                    </div>
                    <label>Priority
                        <select name="priority" required>
                            <option value="Normal" {{ old('priority', 'Normal') === 'Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Urgent" {{ old('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </label>
                    <label>Preferred Time <span>(optional)</span>
                        <input type="time" name="preferred_time" value="{{ old('preferred_time') }}">
                    </label>
                    <label class="guest-request-description">Description
                        <textarea id="request-description" name="description" rows="4" required placeholder="Please describe what you need or the problem you are experiencing.">{{ old('description') }}</textarea>
                        <span id="other-request-hint" class="other-request-hint" hidden><i class="fas fa-info-circle"></i> Please describe the specific request you need in the box above.</span>
                    </label>
                </div>
                <button type="submit" class="btn guest-request-submit"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </form>
        @endif
    </section>

    <section class="my-requests-section">
        <div class="guest-request-heading"><p class="eyebrow">Stay Support</p><h2>My Requests</h2></div>
        @if($guestRequests->isEmpty())
            <div class="records-empty"><i class="fas fa-inbox"></i><h3>No requests yet</h3><p>Your submitted requests will appear here.</p></div>
        @else
            <div class="records-table-wrap">
                <table class="records-table guest-requests-table">
                    <thead><tr><th>Request ID</th><th>Request Type</th><th>Room</th><th>Date Submitted</th><th>Department</th><th>Priority</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($guestRequests as $guestRequest)
                            @php($requestStatusClass = \Illuminate\Support\Str::slug($guestRequest->status))
                            <tr>
                                <td>REQ-{{ str_pad($guestRequest->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $guestRequest->request_type }}</td>
                                <td>{{ $guestRequest->room->room_number ?? '—' }}</td>
                                <td>{{ $guestRequest->submitted_at?->format('M d, Y g:i A') }}</td>
                                <td>{{ $guestRequest->department }}</td>
                                <td>{{ $guestRequest->priority }}</td>
                                <td><span class="guest-request-status status-{{ $requestStatusClass }}">{{ $guestRequest->status }}</span></td>
                                <td><button type="button" class="view-request guest-request-view" data-request-id="REQ-{{ str_pad($guestRequest->id, 4, '0', STR_PAD_LEFT) }}" data-request-type="{{ $guestRequest->request_type }}" data-description="{{ $guestRequest->description }}" data-room="{{ $guestRequest->room->room_number ?? '—' }}" data-submitted="{{ $guestRequest->submitted_at?->format('M d, Y g:i A') }}" data-priority="{{ $guestRequest->priority }}" data-status="{{ $guestRequest->status }}">View</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<div class="receipt-modal" id="guest-receipt-modal" aria-hidden="true">
    <div class="receipt-card" role="dialog" aria-modal="true" aria-labelledby="guest-receipt-title">
        <div class="receipt-header">
            <button type="button" class="receipt-close" id="guest-receipt-close" aria-label="Close receipt">&times;</button>
            <h3 class="receipt-brand">Casaul Hotel</h3>
            <div class="receipt-contact">
                <span><i class="fas fa-map-marker-alt"></i> Casaul Hotel</span>
                <span><i class="fas fa-phone"></i> +63 912 345 6789</span>
                <span><i class="fas fa-envelope"></i> reservations@casaulhotel.com</span>
            </div>
            <p class="receipt-subcontact"><i class="fas fa-globe"></i> www.casaulhotel.com</p>
        </div>
        <div class="receipt-heading-row">
            <h3 class="receipt-heading" id="guest-receipt-title">RECEIPT</h3>
        </div>
        <div class="receipt-title-row">
            <div class="receipt-paid-by">
                <h4>Paid By</h4>
                <p id="guest-receipt-guest-name">Guest</p>
                <p id="guest-receipt-guest-email">guest@example.com</p>
            </div>
            <div class="receipt-booking">
                <p><span>Receipt #</span><strong id="guest-receipt-number">RES-0000</strong></p>
                <p><span>Receipt Date</span><strong id="guest-receipt-date"></strong></p>
            </div>
        </div>
        <div class="receipt-booking receipt-booking-details">
            <h4>Booking Details</h4>
            <p><span>Check-in</span><strong id="guest-receipt-checkin">—</strong></p>
            <p><span>Check-out</span><strong id="guest-receipt-checkout">—</strong></p>
            <p><span>Guests</span><strong id="guest-receipt-guests">2 Guests</strong></p>
            <p><span>Room</span><strong id="guest-receipt-room">None</strong></p>
        </div>
        <div class="receipt-content" id="guest-receipt-content"></div>
        <div class="receipt-notes">
            <h4>Notes</h4>
            <p>Thank you for choosing Casaul Hotel. We look forward to your next visit.</p>
        </div>
        <div class="receipt-actions">
            <button type="button" id="guest-receipt-download-btn"><i class="fas fa-download"></i> Download</button>
            <button type="button" class="receipt-print-btn" id="guest-receipt-print-btn"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>
</div>

<div class="request-modal" id="guest-request-modal" role="dialog" aria-modal="true" aria-labelledby="guest-request-modal-title" aria-hidden="true">
    <div class="request-modal-card">
        <div class="request-modal-head"><h2 id="guest-request-modal-title">Request Details</h2><button type="button" class="request-modal-close" aria-label="Close"><i class="fas fa-times"></i></button></div>
        <dl class="guest-request-details">
            <div><dt>Request ID</dt><dd id="guest-request-id"></dd></div>
            <div><dt>Request Type</dt><dd id="guest-request-type"></dd></div>
            <div><dt>Description</dt><dd id="guest-request-description"></dd></div>
            <div><dt>Room Number</dt><dd id="guest-request-room"></dd></div>
            <div><dt>Date &amp; Time Submitted</dt><dd id="guest-request-submitted"></dd></div>
            <div><dt>Priority</dt><dd id="guest-request-priority"></dd></div>
            <div><dt>Status</dt><dd id="guest-request-status"></dd></div>
        </dl>
    </div>
</div>

<script>
    function openGuestReceiptModal(button) {
        const modal = document.getElementById('guest-receipt-modal');
        const guestName = document.getElementById('guest-receipt-guest-name');
        const guestEmail = document.getElementById('guest-receipt-guest-email');
        const receiptNumber = document.getElementById('guest-receipt-number');
        const receiptDate = document.getElementById('guest-receipt-date');
        const receiptCheckIn = document.getElementById('guest-receipt-checkin');
        const receiptCheckOut = document.getElementById('guest-receipt-checkout');
        const receiptGuests = document.getElementById('guest-receipt-guests');
        const receiptRoom = document.getElementById('guest-receipt-room');
        const receiptContent = document.getElementById('guest-receipt-content');
        const reservationTitle = document.getElementById('guest-receipt-title');
        const reservationNotes = document.querySelector('#guest-receipt-modal .receipt-notes p');

        let reservation = null;
        try {
            reservation = JSON.parse(button.dataset.reservation || 'null');
        } catch (error) {
            reservation = null;
        }

        const escapeHtml = (value) => String(value ?? '—')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        const field = (label, value) => `<div class="reservation-detail-field"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value || '—')}</strong></div>`;
        const list = (items, emptyLabel, renderItem) => items?.length
            ? items.map(renderItem).join('')
            : `<p class="reservation-detail-empty">${escapeHtml(emptyLabel)}</p>`;

        if (reservation) {
            reservationTitle.textContent = 'RESERVATION DETAILS';
            guestName.textContent = reservation.guestName || 'Guest';
            guestEmail.textContent = reservation.guestEmail || '—';
            receiptNumber.textContent = reservation.id || 'RES-0000';
            receiptDate.textContent = reservation.status || '—';
            receiptCheckIn.textContent = reservation.checkIn || '—';
            receiptCheckOut.textContent = reservation.checkOut || '—';
            receiptGuests.textContent = reservation.guests ? `${reservation.guests} Guests` : '—';
            receiptRoom.textContent = reservation.room?.type || '—';
            reservationNotes.textContent = reservation.specialRequests || 'No special requests.';

            const room = reservation.room ? `<div class="reservation-detail-section"><h4>Room</h4><div class="reservation-detail-grid">
                ${field('Room type', reservation.room.type)}${field('Room number', reservation.room.number)}${field('Floor', reservation.room.floor)}${field('Capacity', reservation.room.capacity)}${field('Rate', reservation.room.price)}
                </div><p>${escapeHtml(reservation.room.description || 'No room description.')}</p></div>` : '';
            const amenities = `<div class="reservation-detail-section"><h4>Amenities</h4>${list(reservation.amenities, 'No amenities selected.', item => `<div class="reservation-detail-item"><strong>${escapeHtml(item.name)}</strong><span>${escapeHtml(item.price)}</span><p>${escapeHtml(item.description || 'No description.')}</p></div>`)}</div>`;
            const eventPlaces = `<div class="reservation-detail-section"><h4>Event Place</h4>${list(reservation.eventPlaces, 'No event place selected.', item => `<div class="reservation-detail-item"><strong>${escapeHtml(item.name)}</strong><span>${escapeHtml(item.price)}</span><p>${escapeHtml([item.type, item.location, item.capacity ? `Capacity: ${item.capacity}` : ''].filter(Boolean).join(' | '))}</p><p>${escapeHtml(item.description || 'No description.')}</p></div>`)}</div>`;
            const dining = `<div class="reservation-detail-section"><h4>Dining</h4>${list(reservation.dining, 'No dining items selected.', item => `<div class="reservation-detail-item"><strong>${escapeHtml(item.name)} x${escapeHtml(item.quantity)}</strong><span>${escapeHtml(item.price)}</span><p>${escapeHtml([item.category, item.area, item.schedule, item.date].filter(Boolean).join(' | '))}</p></div>`)}</div>`;
            const payments = `<div class="reservation-detail-section"><h4>Payment</h4><div class="reservation-detail-grid">${field('Reservation method', reservation.paymentMethod)}${field('Payment details', reservation.paymentDetails)}${field('Amount paid', reservation.amountPaid)}${field('Total amount', reservation.total)}</div>${list(reservation.payments, 'No separate payment transactions recorded.', payment => `<div class="reservation-detail-item"><strong>${escapeHtml(payment.method)} - ${escapeHtml(payment.amount)}</strong><p>${escapeHtml([payment.date, payment.reference ? `Reference: ${payment.reference}` : '', payment.notes].filter(Boolean).join(' | '))}</p></div>`)}</div>`;

            receiptContent.innerHTML = `<div class="reservation-detail-grid reservation-detail-grid--summary">${field('Status', reservation.status)}${field('Category', reservation.category)}${field('Phone', reservation.guestPhone)}${field('Check-in time', reservation.checkInTime)}${field('Check-out time', reservation.checkOutTime)}${field('Event type', reservation.eventType)}${field('Dining area', reservation.diningArea)}${field('Dining schedule', reservation.diningSchedule)}</div>${room}${amenities}${eventPlaces}${dining}${payments}`;
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            return;
        }

        const totalValue = button.dataset.total || '₱0.00';
        const guestCount = button.dataset.guests || '2 Guests';
        const roomName = button.dataset.room || 'Room';
        const lineItems = (() => {
            try {
                return JSON.parse(button.dataset.lineItems || '[]');
            } catch (error) {
                return [];
            }
        })();

        guestName.textContent = button.dataset.guestName || 'Guest';
        guestEmail.textContent = button.dataset.guestEmail || 'guest@example.com';
        receiptNumber.textContent = button.dataset.receiptId || 'RES-0000';
        receiptDate.textContent = new Date().toLocaleDateString('en-US');
        receiptCheckIn.textContent = button.dataset.checkIn || '—';
        receiptCheckOut.textContent = button.dataset.checkOut || '—';
        receiptGuests.textContent = guestCount;
        receiptRoom.textContent = roomName;

        const rows = lineItems.length ? lineItems : [{ quantity: 1, description: `Room - ${roomName}`, unitPrice: totalValue, amount: totalValue }];

        receiptContent.innerHTML = `
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows.map(item => `<tr><td>${item.quantity ?? 1}</td><td>${item.description ?? 'Reservation Item'}</td><td>${item.unitPrice ?? totalValue}</td><td>${item.amount ?? totalValue}</td></tr>`).join('')}
                    <tr class="receipt-total-row">
                        <td colspan="3">Total</td>
                        <td>${totalValue}</td>
                    </tr>
                </tbody>
            </table>
        `;

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
    }

    document.querySelectorAll('[data-view-receipt]').forEach(function (button) {
        button.addEventListener('click', function () {
            openGuestReceiptModal(this);
        });
    });

    document.getElementById('guest-receipt-close')?.addEventListener('click', function () {
        document.getElementById('guest-receipt-modal').classList.remove('open');
        document.getElementById('guest-receipt-modal').setAttribute('aria-hidden', 'true');
    });

    const loadReceiptPdfTools = () => Promise.all([
        new Promise((resolve, reject) => {
            if (window.html2canvas) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        }),
        new Promise((resolve, reject) => {
            if (window.jspdf?.jsPDF) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        }),
    ]);

    const downloadGuestReceiptAsPdf = async () => {
        const modal = document.getElementById('guest-receipt-modal');
        const downloadButton = document.getElementById('guest-receipt-download-btn');
        const originalLabel = downloadButton.innerHTML;
        downloadButton.disabled = true;
        downloadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
        try {
            await loadReceiptPdfTools();
            const receiptCard = modal.querySelector('.receipt-card');
            const printableReceipt = receiptCard.cloneNode(true);
            printableReceipt.querySelector('.receipt-close')?.remove();
            printableReceipt.querySelector('.receipt-actions')?.remove();
            printableReceipt.style.position = 'absolute';
            printableReceipt.style.left = '-10000px';
            printableReceipt.style.top = '0';
            printableReceipt.style.width = `${receiptCard.offsetWidth}px`;
            printableReceipt.style.maxHeight = 'none';
            printableReceipt.style.height = 'auto';
            printableReceipt.style.overflow = 'visible';
            document.body.appendChild(printableReceipt);
            const canvas = await window.html2canvas(printableReceipt, { scale: 2, backgroundColor: '#ffffff' });
            printableReceipt.remove();
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const margin = 10;
            const imageWidth = pageWidth - (margin * 2);
            const imageHeight = (canvas.height * imageWidth) / canvas.width;
            const imageData = canvas.toDataURL('image/jpeg', 0.95);
            let remainingHeight = imageHeight;
            let offset = 0;
            pdf.addImage(imageData, 'JPEG', margin, margin, imageWidth, imageHeight);
            remainingHeight -= pageHeight - (margin * 2);
            while (remainingHeight > 0) {
                offset += pageHeight - (margin * 2);
                pdf.addPage();
                pdf.addImage(imageData, 'JPEG', margin, margin - offset, imageWidth, imageHeight);
                remainingHeight -= pageHeight - (margin * 2);
            }
            pdf.save(`${document.getElementById('guest-receipt-number').textContent || 'reservation'}-receipt.pdf`);
        } catch (error) {
            alert('The receipt PDF could not be downloaded. Please try again.');
        } finally {
            downloadButton.disabled = false;
            downloadButton.innerHTML = originalLabel;
        }
    };

    const printGuestReceipt = () => {
        const modal = document.getElementById('guest-receipt-modal');
        const printWindow = window.open('', '_blank', 'width=600,height=700');
        if (!printWindow) {
            return;
        }
        const printableReceipt = modal.querySelector('.receipt-card').cloneNode(true);
        printableReceipt.querySelector('.receipt-close')?.remove();
        printableReceipt.querySelector('.receipt-actions')?.remove();
        const pageStyles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
            .map((style) => style.outerHTML)
            .join('');
        printWindow.document.write(`<html><head><title>${document.getElementById('guest-receipt-number').textContent} Reservation</title>${pageStyles}<style>
            @page { margin: 12mm; }
            html, body { margin: 0; padding: 0; background: #fff; }
            body { display: block; }
            .receipt-card { width: 100%; max-height: none; overflow: visible; box-shadow: none; }
        </style></head><body>${printableReceipt.outerHTML}</body></html>`);
        printWindow.document.close();
        const print = () => {
            printWindow.focus();
            printWindow.print();
        };
        if (printWindow.document.fonts?.ready) {
            printWindow.document.fonts.ready.then(print);
        } else {
            print();
        }
    };

    document.getElementById('guest-receipt-download-btn')?.addEventListener('click', downloadGuestReceiptAsPdf);
    document.getElementById('guest-receipt-print-btn')?.addEventListener('click', printGuestReceipt);

    document.querySelectorAll('.guest-request-view').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('guest-request-id').textContent = button.dataset.requestId;
            document.getElementById('guest-request-type').textContent = button.dataset.requestType;
            document.getElementById('guest-request-description').textContent = button.dataset.description;
            document.getElementById('guest-request-room').textContent = button.dataset.room;
            document.getElementById('guest-request-submitted').textContent = button.dataset.submitted;
            document.getElementById('guest-request-priority').textContent = button.dataset.priority;
            document.getElementById('guest-request-status').textContent = button.dataset.status;
            document.getElementById('guest-request-modal').classList.add('open');
            document.getElementById('guest-request-modal').setAttribute('aria-hidden', 'false');
        });
    });

    document.querySelectorAll('.reservation-menu-toggle').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const menu = this._reservationMenu || this.nextElementSibling;
            const isHidden = menu.classList.contains('hidden');

            document.querySelectorAll('.reservation-menu').forEach(function (item) {
                if (item !== menu) {
                    item.classList.add('hidden');
                    if (item._reservationMenuWrap) {
                        item._reservationMenuWrap.appendChild(item);
                        item.classList.remove('is-viewport-menu');
                    }
                }
            });

            document.querySelectorAll('.reservation-menu-toggle').forEach(function (item) {
                if (item !== button) {
                    item.setAttribute('aria-expanded', 'false');
                }
            });

            if (isHidden) {
                const menuWrap = button.parentElement;
                const buttonRect = button.getBoundingClientRect();
                menu._reservationMenuWrap = menuWrap;
                button._reservationMenu = menu;
                document.body.appendChild(menu);
                menu.classList.add('is-viewport-menu');
                menu.classList.remove('hidden');

                const menuHeight = menu.offsetHeight;
                const spaceBelow = window.innerHeight - buttonRect.bottom;
                const top = spaceBelow >= menuHeight + 8
                    ? buttonRect.bottom + 8
                    : Math.max(8, buttonRect.top - menuHeight - 8);
                const left = Math.min(
                    Math.max(8, buttonRect.right - menu.offsetWidth),
                    window.innerWidth - menu.offsetWidth - 8
                );
                menu.style.top = `${top}px`;
                menu.style.left = `${left}px`;
            } else {
                menu.classList.add('hidden');
                menu.classList.remove('is-viewport-menu');
                if (menu._reservationMenuWrap) {
                    menu._reservationMenuWrap.appendChild(menu);
                }
                menu.style.top = '';
                menu.style.left = '';
            }
            button.setAttribute('aria-expanded', String(isHidden));
        });
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.reservation-menu') && !event.target.closest('.reservation-menu-toggle')) {
            document.querySelectorAll('.reservation-menu').forEach(function (item) {
                item.classList.add('hidden');
                item.classList.remove('is-viewport-menu');
                if (item._reservationMenuWrap) {
                    item._reservationMenuWrap.appendChild(item);
                }
                item.style.top = '';
                item.style.left = '';
            });
            document.querySelectorAll('.reservation-menu-toggle').forEach(function (item) {
                item.setAttribute('aria-expanded', 'false');
            });
        }
    });

    function closeGuestRequestModal() {
        document.getElementById('guest-request-modal').classList.remove('open');
        document.getElementById('guest-request-modal').setAttribute('aria-hidden', 'true');
    }
    document.querySelector('.request-modal-close')?.addEventListener('click', closeGuestRequestModal);
    document.getElementById('guest-request-modal')?.addEventListener('click', function (event) { if (event.target === this) closeGuestRequestModal(); });
</script>

<script>
    const requestTypePicker = document.querySelector('[data-request-type-picker]');
    if (requestTypePicker) {
        const searchInput = requestTypePicker.querySelector('.request-type-search');
        const toggleButton = requestTypePicker.querySelector('.request-type-toggle');
        const optionsPanel = requestTypePicker.querySelector('.request-type-options');
        const options = [...requestTypePicker.querySelectorAll('.request-type-option')];
        const emptyMessage = requestTypePicker.querySelector('.request-type-empty');
        const descriptionInput = document.getElementById('request-description');
        const otherRequestHint = document.getElementById('other-request-hint');
        const selectedItemsContainer = document.getElementById('selected-request-items');
        const requestItemsValue = document.getElementById('request-items-value');
        const selectedItems = new Map();

        function setRequestTypeOpen(isOpen) {
            optionsPanel.hidden = !isOpen;
            searchInput.setAttribute('aria-expanded', String(isOpen));
            toggleButton.setAttribute('aria-expanded', String(isOpen));
        }

        function filterRequestTypes() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;
            options.forEach(function (option) {
                const isVisible = option.textContent.toLowerCase().includes(query);
                option.hidden = !isVisible;
                visibleCount += isVisible ? 1 : 0;
            });
            requestTypePicker.querySelectorAll('[data-request-type-group]').forEach(function (group) {
                group.hidden = !group.querySelector('.request-type-option:not([hidden])');
            });
            emptyMessage.hidden = visibleCount > 0;
        }

        function syncSelectedItems() {
            const items = Array.from(selectedItems.entries()).map(([type, quantity]) => ({ type, quantity }));
            requestItemsValue.value = JSON.stringify(items);

            selectedItemsContainer.innerHTML = '';
            if (items.length === 0) {
                selectedItemsContainer.hidden = true;
                return;
            }

            selectedItemsContainer.hidden = false;
            selectedItemsContainer.style.color = '#111827';
            selectedItemsContainer.style.fontSize = '15px';

            items.forEach(function (item) {
                const chip = document.createElement('div');
                chip.style.display = 'inline-flex';
                chip.style.alignItems = 'center';
                chip.style.gap = '8px';
                chip.style.marginRight = '8px';
                chip.style.marginBottom = '6px';
                chip.style.padding = '4px 8px';
                chip.style.border = '1px solid #d1d5db';
                chip.style.borderRadius = '4px';
                chip.style.background = '#f3f4f6';
                chip.style.fontSize = '14px';
                chip.style.lineHeight = '1.4';

                const text = document.createElement('span');
                text.textContent = item.type;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = '×';
                removeButton.dataset.itemType = item.type;
                removeButton.style.border = 'none';
                removeButton.style.background = 'transparent';
                removeButton.style.cursor = 'pointer';
                removeButton.style.fontSize = '16px';
                removeButton.style.padding = '0';
                removeButton.style.color = '#374151';

                const quantityInput = document.createElement('input');
                quantityInput.type = 'number';
                quantityInput.min = '1';
                quantityInput.value = item.quantity;
                quantityInput.dataset.itemType = item.type;
                quantityInput.style.width = '42px';
                quantityInput.style.border = '1px solid #d1d5db';
                quantityInput.style.borderRadius = '3px';
                quantityInput.style.padding = '2px 4px';
                quantityInput.style.fontSize = '14px';

                chip.appendChild(text);
                chip.appendChild(removeButton);
                chip.appendChild(quantityInput);
                selectedItemsContainer.appendChild(chip);
            });

            selectedItemsContainer.querySelectorAll('input[data-item-type]').forEach(function (input) {
                input.addEventListener('input', function () {
                    const type = input.dataset.itemType;
                    const value = Math.max(1, Number(input.value) || 1);
                    input.value = value;
                    if (selectedItems.has(type)) {
                        selectedItems.set(type, value);
                        syncSelectedItems();
                    }
                });
            });

            selectedItemsContainer.querySelectorAll('button[data-item-type]').forEach(function (button) {
                button.addEventListener('click', function () {
                    selectedItems.delete(button.dataset.itemType);
                    syncSelectedItems();
                });
            });
        }

        function addSelectedType(type) {
            if (!selectedItems.has(type)) {
                selectedItems.set(type, 1);
            }
            syncSelectedItems();
        }

        function selectRequestType(option) {
            const type = option.dataset.value;
            const isOtherRequest = type.startsWith('Other');
            addSelectedType(type);
            searchInput.value = '';
            otherRequestHint.hidden = !isOtherRequest;
            descriptionInput.placeholder = isOtherRequest
                ? 'Please describe exactly what assistance you need.'
                : 'Please describe what you need or the problem you are experiencing.';
            setRequestTypeOpen(true);
            filterRequestTypes();
        }

        searchInput.addEventListener('focus', function () { setRequestTypeOpen(true); filterRequestTypes(); });
        searchInput.addEventListener('input', function () { setRequestTypeOpen(true); filterRequestTypes(); });
        toggleButton.addEventListener('click', function () { setRequestTypeOpen(!optionsPanel.hidden); searchInput.focus(); filterRequestTypes(); });
        options.forEach(function (option) { option.addEventListener('click', function () { selectRequestType(option); }); });
        document.addEventListener('click', function (event) {
            if (!requestTypePicker.contains(event.target)) setRequestTypeOpen(false);
        });

        syncSelectedItems();
        setRequestTypeOpen(true);
    }
</script>

<style>
    .request-type-picker {
        display: flex;
        flex-direction: column;
        gap: 8px;
        position: relative;
    }

    .request-type-field {
        position: relative;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        min-height: 44px;
        padding: 8px 40px 8px 10px;
        border: 1px solid #d5d7db;
        border-radius: 6px;
        background: #fff;
        width: 100%;
    }

    .request-type-search {
        flex: 1 1 100%;
        min-width: 100px;
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        color: #111827;
        padding: 0;
        margin: 0;
    }

    .request-type-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #374151;
        cursor: pointer;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #selected-request-items {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        width: 100%;
        min-height: 24px;
        color: #6b7280;
    }

    #selected-request-items[hidden] {
        display: none;
    }

    .request-type-options {
        margin-top: 6px;
        background: #fff;
        border: 1px solid #d5d7db;
        border-radius: 6px;
        max-height: 220px;
        overflow-y: auto;
        width: 100%;
    }

    .request-type-group {
        display: block;
        padding: 0;
    }

    .request-type-group-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #374151;
        text-transform: uppercase;
        padding: 10px 12px 6px;
        background: #f9fafb;
    }

    .request-type-option {
        width: 100%;
        text-align: left;
        font-size: 15px;
        padding: 10px 12px;
        border: none;
        background: transparent;
        color: #111827;
        cursor: pointer;
    }

    .request-type-option:hover,
    .request-type-option[aria-selected="true"] {
        background: #eef4ff;
    }
</style>

@endsection
