@extends('app')

@section('content')
<style>
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

    .receipt-heading {
        margin: 0;
        color: #07549a;
        font-size: 32px;
        letter-spacing: 0.04em;
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

    .receipt-payment-details {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #d9e5ef;
    }

    .receipt-payment-details h4 {
        margin: 0 0 8px;
        color: #07549a;
        font-size: 14px;
    }

    .receipt-payment-details p {
        display: grid;
        grid-template-columns: minmax(120px, 0.8fr) minmax(0, 1.2fr);
        gap: 12px;
        margin: 6px 0;
        color: #4b5563;
        font-size: 13px;
    }

    .receipt-payment-details strong {
        overflow-wrap: anywhere;
        color: #334155;
    }

    .receipt-payment-proof {
        margin-top: 12px;
    }

    .receipt-payment-proof-label {
        display: block;
        margin-bottom: 8px;
        color: #64748b;
    }

    .receipt-payment-proof img {
        display: block;
        width: min(360px, 100%);
        max-height: 280px;
        border: 1px solid #d9e5ef;
        border-radius: 6px;
        object-fit: contain;
        background: #f8fafc;
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

    .receipt-table {
        width: 100%;
        border: 1px solid #7fa9d0;
        border-spacing: 0;
        border-radius: 4px;
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

    .reservation-view-btn {
        min-width: 128px;
        border: 1px solid #d62839;
        background: #fff;
        color: #b91c1c;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        box-shadow: 0 2px 8px rgba(182, 36, 58, 0.08);
        transition: all 0.2s ease;
    }

    .reservation-view-btn:hover {
        background: #fff3f4;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(182, 36, 58, 0.12);
    }

    @media (max-width: 700px) {
        .receipt-card {
            padding: 18px;
        }

        .receipt-brand,
        .receipt-heading {
            font-size: 25px;
        }

        .receipt-title-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .receipt-booking {
            min-width: 0;
            width: 100%;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero">
        <p class="eyebrow">My Account</p>
        <h1>My Receipts</h1>
        <p>View the confirmed reservation receipts tied to your account.</p>
    </section>

    <div class="records-toolbar">
        <a href="{{ route('guest.records') }}" class="btn btn-back">&larr; Back to Records</a>
    </div>

    @if($receipts->isEmpty())
        <div class="records-empty">
            <i class="fas fa-receipt"></i>
            <h3>No receipts yet</h3>
            <p>Your confirmed reservation receipts will appear here.</p>
            <a href="{{ route('reservation') }}" class="btn">Make a Reservation</a>
        </div>
    @else
        <div class="records-table-wrap">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt ID</th>
                        <th>Category</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $index => $reservation)
                        @php
                            $reservationReceiptLines = [];
                            $roomCharge = 0.0;
                            if ($reservation->room) {
                                $roomCharge = \App\Support\ReservationPricing::room(
                                    $reservation->room,
                                    $reservation->check_in,
                                    $reservation->check_out,
                                    (int) ($reservation->number_of_guests ?? 1)
                                );
                                $reservationReceiptLines[] = [
                                    'quantity' => 1,
                                    'description' => 'Room - ' . ($reservation->room->room_type ?? 'Room'),
                                    'unitPrice' => '₱' . number_format($roomCharge, 2),
                                    'amount' => '₱' . number_format($roomCharge, 2),
                                ];
                            }

                            $facilityIds = $reservation->facility_id ? array_values(array_filter(array_map('trim', explode(',', (string) $reservation->facility_id)))) : [];
                            foreach ($facilityIds as $facilityId) {
                                $facility = \App\Models\Facility::find($facilityId);
                                if ($facility) {
                                    $facilityCharge = \App\Support\ReservationPricing::facilities(
                                        collect([$facility]),
                                        (int) ($reservation->facility_quantity ?? $reservation->quantity ?? 1),
                                        $reservation->check_in,
                                        $reservation->check_out
                                    );
                                    $reservationReceiptLines[] = [
                                        'quantity' => 1,
                                        'description' => 'Facilities - ' . $facility->name,
                                        'unitPrice' => '₱' . number_format($facilityCharge, 2),
                                        'amount' => '₱' . number_format($facilityCharge, 2),
                                    ];
                                }
                            }

                            $eventIds = $reservation->event_id ? array_values(array_filter(array_map('trim', explode(',', (string) $reservation->event_id)))) : [];
                            foreach ($eventIds as $eventId) {
                                $event = \App\Models\Event::find($eventId);
                                if ($event) {
                                    $eventCharge = \App\Support\ReservationPricing::events(
                                        collect([$event]),
                                        (int) ($reservation->number_of_guests ?? 1),
                                        max(1, \Carbon\Carbon::parse($reservation->event_start_time ?? $reservation->check_in_time ?? '00:00')->diffInHours(\Carbon\Carbon::parse($reservation->event_end_time ?? $reservation->check_out_time ?? '01:00')))
                                    );
                                    $reservationReceiptLines[] = [
                                        'quantity' => 1,
                                        'description' => 'Event - ' . $event->name,
                                        'unitPrice' => '₱' . number_format($eventCharge, 2),
                                        'amount' => '₱' . number_format($eventCharge, 2),
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
                            $paymentProofUrl = null;
                            if (preg_match('/(?:^|•)\s*Proof:\s*([^•\s]+)/i', (string) $reservation->payment_details, $paymentProofMatches)) {
                                $paymentProofPath = $paymentProofMatches[1];
                                $paymentProofUrl = str_starts_with($paymentProofPath, 'http')
                                    ? $paymentProofPath
                                    : asset(str_starts_with($paymentProofPath, 'storage/')
                                        ? $paymentProofPath
                                        : 'storage/payment-proofs/' . ltrim($paymentProofPath, '/'));
                            }
                            $receiptCategory = 'all';
                            $categoryReceiptLines = $reservationReceiptLines;
                            $categoryTotal = collect($categoryReceiptLines)->sum(fn ($line) => (float) str_replace(['₱', ','], '', $line['amount']));
                            $categoryName = collect($categoryReceiptLines)
                                ->map(fn ($line) => explode(' - ', $line['description'])[0])
                                ->unique()
                                ->implode(', ') ?: 'Reservation';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $categoryName }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($categoryTotal, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td>
                                <button type="button"
                                    class="reservation-view-btn"
                                    data-view-receipt
                                    data-receipt-id="RES-{{ str_pad($reservation->id, 4, '0', STR_PAD_LEFT) }}"
                                    data-guest-name="{{ auth('guest')->user()->name }}"
                                    data-guest-email="{{ auth('guest')->user()->email }}"
                                    data-check-in="{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}"
                                    data-check-out="{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}"
                                    data-guests="{{ $reservation->number_of_guests ?? 2 }} Guests"
                                    data-category="{{ $receiptCategory }}"
                                    data-room="{{ $reservation->room->room_type ?? 'Room' }}"
                                    data-total="₱{{ number_format($categoryTotal, 2) }}"
                                    data-payment-method='@json($reservation->payment_method ?? "")'
                                    data-payment-details='@json($reservation->payment_details ?? "")'
                                    data-payment-proof='@json($paymentProofUrl)'
                                    data-line-items='@json($categoryReceiptLines)'>View Receipt</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
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
            <p id="guest-receipt-room-row"><span>Room</span><strong id="guest-receipt-room">None</strong></p>
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
        const receiptRoomRow = document.getElementById('guest-receipt-room-row');
        const receiptContent = document.getElementById('guest-receipt-content');

        const totalValue = button.dataset.total || '₱0.00';
        const guestCount = button.dataset.guests || '2 Guests';
        const roomName = button.dataset.room || 'Room';
        const receiptCategory = button.dataset.category || 'rooms';
        const parseDataValue = (value, fallback = '') => {
            try {
                return JSON.parse(value);
            } catch (error) {
                return value || fallback;
            }
        };
        const paymentMethod = parseDataValue(button.dataset.paymentMethod, '—') || '—';
        const paymentDetails = parseDataValue(button.dataset.paymentDetails, '') || '';
        const paymentProof = parseDataValue(button.dataset.paymentProof, '') || '';
        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        let lineItems = [];
        try {
            lineItems = JSON.parse(button.dataset.lineItems || '[]');
        } catch (error) {
            lineItems = [];
        }

        guestName.textContent = button.dataset.guestName || 'Guest';
        guestEmail.textContent = button.dataset.guestEmail || 'guest@example.com';
        receiptNumber.textContent = button.dataset.receiptId || 'RES-0000';
        receiptDate.textContent = new Date().toLocaleDateString('en-US');
        receiptCheckIn.textContent = button.dataset.checkIn || '—';
        receiptCheckOut.textContent = button.dataset.checkOut || '—';
        receiptGuests.textContent = guestCount;
        receiptRoom.textContent = roomName;
        receiptRoomRow.hidden = receiptCategory !== 'rooms';
        document.getElementById('guest-receipt-title').textContent = `${receiptCategory.toUpperCase()} RECEIPT`;
        const paymentDetailRows = paymentDetails
            .split('•')
            .map(detail => detail.trim())
            .filter(Boolean)
            .map(detail => {
                const separatorIndex = detail.indexOf(':');
                const label = separatorIndex > -1 ? detail.slice(0, separatorIndex).trim() : 'Details';
                const value = separatorIndex > -1 ? detail.slice(separatorIndex + 1).trim() : detail;
                return `<p><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></p>`;
            })
            .join('');

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
                    ${lineItems.length ? lineItems.map(item => `<tr>
                        <td>${escapeHtml(item.quantity ?? 1)}</td>
                        <td>${escapeHtml(item.description ?? 'Reservation Item')}</td>
                        <td>${escapeHtml(item.unitPrice ?? totalValue)}</td>
                        <td>${escapeHtml(item.amount ?? totalValue)}</td>
                    </tr>`).join('') : `<tr><td colspan="4">No ${escapeHtml(receiptCategory)} items selected.</td></tr>`}
                    <tr class="receipt-total-row">
                        <td colspan="3">Total</td>
                        <td>${totalValue}</td>
                    </tr>
                </tbody>
            </table>
            <div class="receipt-payment-details">
                <h4>Payment</h4>
                <p><span>Payment method</span><strong>${escapeHtml(paymentMethod)}</strong></p>
                ${paymentDetailRows || '<p><span>Payment details</span><strong>—</strong></p>'}
                ${paymentProof ? `<div class="receipt-payment-proof"><span class="receipt-payment-proof-label">Payment proof</span><a href="${escapeHtml(paymentProof)}" target="_blank" rel="noopener"><img src="${escapeHtml(paymentProof)}" alt="Payment proof"></a></div>` : ''}
            </div>
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

    document.getElementById('guest-receipt-download-btn')?.addEventListener('click', function () {
        window.print();
    });

    document.getElementById('guest-receipt-print-btn')?.addEventListener('click', function () {
        window.print();
    });
</script>

@endsection
