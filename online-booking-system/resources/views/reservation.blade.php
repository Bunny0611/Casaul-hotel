@extends('app')

@section('content')

<style>
    .confirmation-modal {
        position: fixed;
        inset: 0;
        display: none;
        justify-content: center;
        align-items: center;
        background: rgba(15, 23, 42, 0.6);
        padding: 24px;
        z-index: 9999;
    }

    .confirmation-modal.open {
        display: flex;
    }

    .confirmation-card {
        width: min(680px, 100%);
        background: #ffffff;
        border-radius: 28px;
        padding: 32px;
        box-shadow: 0 32px 80px rgba(15, 23, 42, 0.18);
        border: 1px solid rgba(15, 23, 42, 0.08);
    }

    .confirmation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .confirmation-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: #111827;
    }

    .confirmation-close {
        border: none;
        background: transparent;
        color: #334155;
        font-size: 1.5rem;
        cursor: pointer;
        line-height: 1;
    }

    .confirmation-text {
        margin: 0 0 24px;
        color: #4b5563;
        font-size: 0.98rem;
        line-height: 1.6;
    }

    .confirmation-form {
        display: grid;
        gap: 20px;
    }

    .confirmation-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .confirmation-col {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .confirmation-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .confirmation-label {
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .confirmation-input {
        width: 100%;
        border-radius: 16px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
        padding: 12px 14px;
        color: #111827;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .confirmation-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .confirmation-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .confirmation-total-label {
        color: #6b7280;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .confirmation-total-amount {
        font-size: 1.2rem;
        color: #111827;
        font-weight: 700;
    }

    .confirmation-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .confirm-submit-btn,
    .confirm-cancel-btn,
    .summary-actions button {
        border-radius: 999px;
        padding: 12px 20px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }

    .confirm-submit-btn {
        background: #dc2626;
        color: #ffffff;
    }

    .confirm-cancel-btn,
    .summary-clear {
        background: #f3f4f6;
        color: #111827;
    }

    .summary-actions button {
        background: linear-gradient(135deg, #dc2626 0%, #f97316 100%);
        color: white;
    }

    @media (max-width: 860px) {
        .confirmation-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="reservation-page animate-on-scroll">
    <section class="reservation-hero">
        <p class="eyebrow">Make a Reservation</p>
        <h1>Choose the type of reservation you want to make.</h1>
        <p>Book rooms, add amenities, include an event package, or reserve dining—all in one seamless checkout experience.</p>
    </section>

    @if(session('success'))
        <div class="reservation-alert reservation-alert--success">
            {{ session('success') }}
        </div>
    @endif

    <div class="reservation-shell">
        <div class="reservation-left">
            <div class="reservation-tabs">
                <button type="button" class="tab-btn active" data-tab="room-tab"><span class="tab-icon"><i class="fas fa-bed"></i></span>Room</button>
                <button type="button" class="tab-btn" data-tab="amenities-tab"><span class="tab-icon"><i class="fas fa-concierge-bell"></i></span>Amenities</button>
                <button type="button" class="tab-btn" data-tab="event-tab"><span class="tab-icon"><i class="fas fa-calendar-check"></i></span>Event</button>
                <button type="button" class="tab-btn" data-tab="dining-tab"><span class="tab-icon"><i class="fas fa-utensils"></i></span>Dining</button>
            </div>

            <div id="room-tab" class="reservation-panel active">
                <div class="panel-header">
                    <h3>Room Reservation</h3>
                    <p>Select a room that suits your needs.</p>
                </div>
                <div class="panel-row">
                    <div class="panel-field">
                        <label class="field-label">Check-in</label>
                        <input id="checkIn" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label">Check-out</label>
                        <input id="checkOut" type="date" class="field-input" />
                    </div>
                    <div class="panel-field">
                        <label class="field-label">Guests</label>
                        <select id="guestCount" class="field-input">
                            <option value="1">1 Guest</option>
                            <option value="2" selected>2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                        </select>
                    </div>
                </div>

                <div class="reservation-card-grid">
                    @foreach($rooms as $room)
                        <article class="reservation-card" data-category="room" data-price="{{ $room->price }}" data-name="{{ $room->room_type }}" data-room-id="{{ $room->id }}">
                            <img src="{{ asset($room->image ?? 'image/Royal-Suite-room.jpg') }}" alt="{{ $room->room_type }}">
                            <div class="reservation-card-body">
                                <h4>{{ $room->room_type }}</h4>
                                <div class="reservation-card-meta">
                                    <span><i class="fas fa-users"></i>{{ $room->capacity ?? 2 }} Guests</span>
                                    <span><i class="fas fa-bed"></i>1 Queen Bed</span>
                                </div>
                                <p>{{ $room->description ?? 'Premium stay with comfortable bedding and modern amenities.' }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($room->price, 0) }}/night</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $room->room_type }}" data-price="{{ $room->price }}" data-room-id="{{ $room->id }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="amenities-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Amenities</h3>
                    <p>Choose amenities to enhance your stay.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($amenities as $amenity)
                        <article class="reservation-card" data-category="amenities" data-price="{{ $amenity['price'] }}" data-title="{{ $amenity['name'] }}">
                            <div class="reservation-card-body">
                                <h4>{{ $amenity['name'] }}</h4>
                                <p>{{ $amenity['description'] }}</p>
                                <p class="text-muted">{{ $amenity['details'] }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($amenity['price'], 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $amenity['name'] }}" data-price="{{ $amenity['price'] }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="event-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Event</h3>
                    <p>Select an event package for your occasion.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($events as $event)
                        <article class="reservation-card" data-category="event" data-price="{{ $event['price'] }}" data-title="{{ $event['name'] }}">
                            <div class="reservation-card-body">
                                <h4>{{ $event['name'] }}</h4>
                                <p>{{ $event['description'] }}</p>
                                <p class="text-muted">{{ $event['details'] }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($event['price'], 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $event['name'] }}" data-price="{{ $event['price'] }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="dining-tab" class="reservation-panel">
                <div class="panel-header">
                    <h3>Dining</h3>
                    <p>Choose a dining experience for your stay.</p>
                </div>
                <div class="reservation-card-grid">
                    @foreach($dining as $meal)
                        <article class="reservation-card" data-category="dining" data-price="{{ $meal['price'] }}" data-title="{{ $meal['name'] }}">
                            <div class="reservation-card-body">
                                <h4>{{ $meal['name'] }}</h4>
                                <p>{{ $meal['description'] }}</p>
                                <p class="text-muted">{{ $meal['details'] }}</p>
                                <div class="reservation-card-footer">
                                    <span class="price">₱{{ number_format($meal['price'], 0) }}</span>
                                    <button type="button" class="select-option-btn" data-title="{{ $meal['name'] }}" data-price="{{ $meal['price'] }}">Add to Reservation</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="reservation-summary">
            <div class="summary-card">
                <h3>Reservation Summary</h3>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-bed"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Rooms</p>
                            <p class="summary-item-subtitle" id="summaryRoom">None</p>
                            <p class="summary-item-caption" id="summaryRoomDetails">Choose a room and dates</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryRoomPrice">₱0</span>
                        <button type="button" class="summary-edit-btn">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-concierge-bell"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Amenities</p>
                            <p class="summary-item-subtitle" id="summaryItems">0 selected</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryAmenitiesPrice">₱0</span>
                        <button type="button" class="summary-edit-btn">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-calendar-check"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Event</p>
                            <p class="summary-item-subtitle" id="summaryEvent">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryEventPrice">₱0</span>
                        <button type="button" class="summary-edit-btn">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-utensils"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Dining</p>
                            <p class="summary-item-subtitle" id="summaryDining">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryDiningPrice">₱0</span>
                        <button type="button" class="summary-edit-btn">Edit</button>
                    </div>
                </article>
                <div class="summary-total">
                    <span>Total</span>
                    <strong id="summaryTotal">₱0</strong>
                </div>
                <p class="summary-note">Your reservation details will be submitted as a request. A staff member will contact you to confirm availability.</p>
                <div class="summary-actions">
                    <button type="button" id="confirmReservationBtn">Confirm Reservation</button>
                    <button type="button" id="clearReservationBtn" class="summary-clear">Clear All</button>
                </div>
            </div>
        </aside>
    </div>

    <div class="confirmation-modal" id="confirmationModal" aria-hidden="true" style="position:fixed; inset:0; justify-content:center; align-items:center; background:rgba(15,23,42,0.6); padding:24px; z-index:9999; display:none;">
        <div class="confirmation-card" style="width:min(720px,100%); background:#ffffff; border-radius:28px; padding:32px; box-shadow:0 32px 80px rgba(15,23,42,0.18); border:1px solid rgba(15,23,42,0.08);">
            <div class="confirmation-header" style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px;">
                <h3 style="margin:0; font-size:1.5rem; color:#111827;">Confirm Your Reservation</h3>
                <button type="button" class="confirmation-close" id="confirmationCloseBtn" style="border:none; background:transparent; color:#334155; font-size:1.5rem; cursor:pointer; line-height:1;">×</button>
            </div>
            <p class="confirmation-text" style="margin:0 0 24px; color:#4b5563; font-size:0.98rem; line-height:1.6;">Review all reservation details before submitting.</p>
            <div class="confirmation-grid" style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; margin-bottom:20px;">
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Reservation ID</span>
                    <div id="confirmReservationId" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">RES-0000</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Status</span>
                    <div id="confirmStatus" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">Reserved</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Room</span>
                    <div id="confirmRoom" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">None</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Payment Method</span>
                    <div id="confirmPaymentMethod" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">Cash</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Guests</span>
                    <div id="confirmGuests" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">0 Guests</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Arriving On</span>
                    <div id="confirmArrivingOn" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">—</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Check-out</span>
                    <div id="confirmCheckOut" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">—</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Amenities</span>
                    <div id="confirmAmenities" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">0 selected</div>
                </div>
                <div class="confirmation-item" style="display:flex; flex-direction:column; gap:8px;">
                    <span class="confirmation-label" style="color:#6b7280; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">Event / Dining</span>
                    <div id="confirmEventDining" style="border-radius:18px; border:1px solid #e5e7eb; background:#f8fafc; padding:16px 18px; color:#111827; font-weight:700;">None</div>
                </div>
            </div>

            <div class="confirmation-total-row" style="display:flex; justify-content:space-between; align-items:center; padding-top:16px; border-top:1px solid #e5e7eb; margin-bottom:20px;">
                <div>
                    <span class="confirmation-total-label" style="color:#6b7280; font-weight:700; font-size:0.9rem;">Total Amount</span>
                    <strong class="confirmation-total-amount" id="confirmTotalAmount" style="font-size:1.5rem; color:#111827; font-weight:800; display:block; margin-top:8px;">₱0</strong>
                </div>
            </div>

            <div class="confirmation-actions" style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" id="modalConfirmBtn" class="confirm-submit-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#dc2626; color:#ffffff;">Submit Reservation</button>
                <button type="button" id="modalCancelBtn" class="confirm-cancel-btn" style="border-radius:999px; padding:14px 26px; font-weight:700; border:none; cursor:pointer; background:#f3f4f6; color:#111827;">Cancel</button>
            </div>
        </div>
    </div>
</div>

<form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="room_id" id="reservationRoomId">
    <input type="hidden" name="check_in" id="reservationCheckIn">
    <input type="hidden" name="check_out" id="reservationCheckOut">
<input type="hidden" name="guest_name" id="reservationGuestName" value="{{ auth()->user()->name ?? 'Guest' }}">
    <input type="hidden" name="guest_email" id="reservationGuestEmail" value="{{ auth()->user()->email ?? 'guest@example.com' }}">
    <input type="hidden" name="guest_phone" id="reservationGuestPhone" value="{{ auth()->user()->contact_no ?? '0000000000' }}">
    <input type="hidden" name="total_amount" id="reservationTotalAmount">
    <input type="hidden" name="special_requests" id="reservationSpecialRequests" value="">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('.reservation-panel');
        const checkIn = document.getElementById('checkIn');
        const checkOut = document.getElementById('checkOut');
        const guestCount = document.getElementById('guestCount');
        const summaryRoom = document.getElementById('summaryRoom');
        const summaryRoomDetails = document.getElementById('summaryRoomDetails');
        const summaryRoomPrice = document.getElementById('summaryRoomPrice');
        const summaryItems = document.getElementById('summaryItems');
        const summaryEvent = document.getElementById('summaryEvent');
        const summaryDining = document.getElementById('summaryDining');
        const summaryAmenitiesPrice = document.getElementById('summaryAmenitiesPrice');
        const summaryEventPrice = document.getElementById('summaryEventPrice');
        const summaryDiningPrice = document.getElementById('summaryDiningPrice');
        const summaryTotal = document.getElementById('summaryTotal');
        const confirmBtn = document.getElementById('confirmReservationBtn');
        const clearBtn = document.getElementById('clearReservationBtn');
        const reservationForm = document.getElementById('reservationForm');
        const reservationRoomId = document.getElementById('reservationRoomId');
        const reservationCheckIn = document.getElementById('reservationCheckIn');
        const reservationCheckOut = document.getElementById('reservationCheckOut');
        const reservationTotalAmount = document.getElementById('reservationTotalAmount');
        const confirmationModal = document.getElementById('confirmationModal');
        const confirmationCloseBtn = document.getElementById('confirmationCloseBtn');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const confirmReservationId = document.getElementById('confirmReservationId');
        const confirmRoom = document.getElementById('confirmRoom');
        const confirmGuests = document.getElementById('confirmGuests');
        const confirmArrivingOn = document.getElementById('confirmArrivingOn');
        const confirmCheckOut = document.getElementById('confirmCheckOut');
        const confirmStatus = document.getElementById('confirmStatus');
        const confirmPaymentMethod = document.getElementById('confirmPaymentMethod');
        const confirmAmenities = document.getElementById('confirmAmenities');
        const confirmEventDining = document.getElementById('confirmEventDining');
        const confirmTotalAmount = document.getElementById('confirmTotalAmount');

        let selectedRoom = null;
        let roomPrice = 0;
        let selectedAmenities = [];
        let selectedEvent = null;
        let selectedDining = null;

        const items = document.querySelectorAll('.select-option-btn');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(btn => btn.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        const summaryEditButtons = document.querySelectorAll('.summary-edit-btn');
        summaryEditButtons.forEach(button => {
            button.addEventListener('click', function () {
                const title = this.closest('.summary-item-card').querySelector('.summary-item-title').textContent.trim().toLowerCase();
                let targetId = 'room-tab';

                if (title.startsWith('amenities')) {
                    targetId = 'amenities-tab';
                } else if (title.startsWith('event')) {
                    targetId = 'event-tab';
                } else if (title.startsWith('dining')) {
                    targetId = 'dining-tab';
                }

                tabs.forEach(btn => btn.classList.toggle('active', btn.dataset.tab === targetId));
                panels.forEach(panel => panel.classList.toggle('active', panel.id === targetId));
                document.getElementById(targetId).scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        const updateSummary = () => {
            summaryRoom.textContent = selectedRoom ? selectedRoom : 'None';
            summaryRoomDetails.textContent = selectedRoom ? `${checkIn.value || 'Date'} – ${checkOut.value || 'Date'} • ${guestCount.value} Guests` : 'Choose a room and dates';
            summaryRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            summaryItems.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            summaryEvent.textContent = selectedEvent ? selectedEvent.title : 'None';
            summaryDining.textContent = selectedDining ? selectedDining.title : 'None';
            summaryAmenitiesPrice.textContent = `₱${selectedAmenities.reduce((sum, item) => sum + item.price, 0).toLocaleString()}`;
            summaryEventPrice.textContent = `₱${(selectedEvent ? selectedEvent.price : 0).toLocaleString()}`;
            summaryDiningPrice.textContent = `₱${(selectedDining ? selectedDining.price : 0).toLocaleString()}`;

            confirmReservationId.textContent = selectedRoom ? `RES-${Math.floor(Math.random() * 9000) + 1000}` : 'RES-0000';
            confirmRoom.textContent = selectedRoom ? selectedRoom : 'None';
            confirmArrivingOn.textContent = checkIn.value || '—';
            confirmCheckOut.textContent = checkOut.value || '—';
            confirmGuests.textContent = `${guestCount.value} Guests`;
            confirmStatus.textContent = 'Reserved';
            confirmPaymentMethod.textContent = 'Cash';
            confirmAmenities.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            confirmEventDining.textContent = (() => {
                const parts = [];
                if (selectedEvent) parts.push(selectedEvent.title);
                if (selectedDining) parts.push(selectedDining.title);
                return parts.length ? parts.join(' / ') : 'None';
            })();
            confirmTotalAmount.textContent = `₱${calculateTotal().toLocaleString()}`;

            const total = calculateTotal();
            summaryTotal.textContent = `₱${total.toLocaleString()}`;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
        }

        checkIn.addEventListener('change', updateSummary);
        checkOut.addEventListener('change', updateSummary);
        guestCount.addEventListener('change', updateSummary);

        const calculateTotal = () => {
            const amenitiesTotal = selectedAmenities.reduce((sum, item) => sum + item.price, 0);
            const eventTotal = selectedEvent ? selectedEvent.price : 0;
            const diningTotal = selectedDining ? selectedDining.price : 0;
            return roomPrice + amenitiesTotal + eventTotal + diningTotal;
        };

        items.forEach(button => {
            button.addEventListener('click', function () {
                const title = this.dataset.title;
                const price = Number(this.dataset.price);
                const card = this.closest('.reservation-card');
                const category = card.dataset.category;
                const dataId = `${category}:${title}`;

                if (category === 'room') {
                    selectedRoom = title;
                    roomPrice = price;
                    reservationRoomId.value = card.dataset.roomId || '';
                    items.forEach(btn => {
                        if (btn.closest('.reservation-card').dataset.category === 'room') {
                            btn.textContent = 'Add to Reservation';
                            btn.disabled = false;
                        }
                    });
                    this.textContent = 'Selected';
                    this.disabled = true;
                } else if (category === 'amenities') {
                    const itemIndex = selectedAmenities.findIndex(item => item.id === dataId);
                    if (itemIndex === -1) {
                        selectedAmenities.push({ id: dataId, title, price });
                        this.textContent = 'Added';
                        this.disabled = true;
                    }
                } else if (category === 'event') {
                    if (selectedEvent) {
                        const previousEventBtn = Array.from(items).find(btn => btn.closest('.reservation-card').dataset.category === 'event' && btn.textContent === 'Selected');
                        if (previousEventBtn) {
                            previousEventBtn.textContent = 'Add to Reservation';
                            previousEventBtn.disabled = false;
                        }
                    }
                    selectedEvent = { title, price };
                    this.textContent = 'Selected';
                    this.disabled = true;
                } else if (category === 'dining') {
                    if (selectedDining) {
                        const previousDiningBtn = Array.from(items).find(btn => btn.closest('.reservation-card').dataset.category === 'dining' && btn.textContent === 'Selected');
                        if (previousDiningBtn) {
                            previousDiningBtn.textContent = 'Add to Reservation';
                            previousDiningBtn.disabled = false;
                        }
                    }
                    selectedDining = { title, price };
                    this.textContent = 'Selected';
                    this.disabled = true;
                }

                updateSummary();
            });
        });

        const openConfirmationModal = () => {
            confirmationModal.classList.add('open');
            confirmationModal.style.display = 'flex';
            confirmationModal.setAttribute('aria-hidden', 'false');
        };

        const closeConfirmationModal = () => {
            confirmationModal.classList.remove('open');
            confirmationModal.style.display = 'none';
            confirmationModal.setAttribute('aria-hidden', 'true');
        };

        confirmBtn.addEventListener('click', function () {
            if (!selectedRoom) {
                alert('Please select a room before confirming your reservation.');
                return;
            }
            if (!checkIn.value || !checkOut.value) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            openConfirmationModal();
        });

        confirmationCloseBtn.addEventListener('click', closeConfirmationModal);
        modalCancelBtn.addEventListener('click', closeConfirmationModal);
        confirmationModal.addEventListener('click', function (event) {
            if (event.target === confirmationModal) {
                closeConfirmationModal();
            }
        });

        modalConfirmBtn.addEventListener('click', function () {
            reservationForm.submit();
        });

        clearBtn.addEventListener('click', function () {
            selectedRoom = null;
            selectedAmenities = [];
            selectedEvent = null;
            selectedDining = null;
            roomPrice = 0;
            checkIn.value = '';
            checkOut.value = '';
            guestCount.value = '2';
            items.forEach(btn => {
                btn.textContent = 'Add to Reservation';
                btn.disabled = false;
            });
            updateSummary();
        });

        updateSummary();
    });
</script>

@endsection
