@extends('app')

@section('content')

<style>
    /* Reservation page - visual refresh (scoped) */
    :root {
        --res-bg: #f6f7fb;
        --card: #ffffff;
        --muted: #6b7280;
        --accent: #dc2626;
        --accent-2: #f97316;
        --radius-lg: 18px;
        --radius-sm: 10px;
        --shadow-soft: 0 10px 30px rgba(16, 24, 40, 0.08);
    }

    .reservation-page { background: var(--res-bg); padding: 28px 0; }

    .reservation-hero { margin: 6px 0 20px; max-width: 1200px; margin-left: auto; margin-right: auto; padding: 28px; background: linear-gradient(180deg, rgba(255,255,255,0.6), rgba(255,255,255,0.35)); border-radius: 14px; box-shadow: var(--shadow-soft); }
    .reservation-hero .eyebrow { color: var(--muted); font-weight:700; letter-spacing:0.06em; }
    .reservation-hero h1 { margin-top:8px; font-size:2rem; color:#0f172a; }
    .reservation-hero p { color: #374151; margin-top:8px; }

    .reservation-shell { display:flex; gap:24px; max-width:1200px; margin:20px auto; padding:0 20px; }
    .reservation-left { flex:1 1 720px; }
    .reservation-summary { width:360px; flex:0 0 360px; }

    .reservation-tabs { display:flex; gap:10px; margin-bottom:16px; }
    .tab-btn { background:transparent; border-radius:999px; padding:10px 14px; font-weight:700; color:#475569; border:1px solid transparent; transition:all .18s ease; }
    .tab-btn.active { background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:white; box-shadow: 0 8px 24px rgba(220,38,38,0.12); }

    .reservation-panel { background: var(--card); border-radius: 12px; padding:14px; box-shadow: 0 8px 20px rgba(12,18,30,0.04); margin-bottom:18px; }

    .reservation-card-grid { display:grid; grid-template-columns: repeat(2,1fr); gap:14px; }
    .reservation-card { border-radius: 12px; overflow:hidden; background: linear-gradient(180deg,#fff,#fbfdff); display:flex; border:1px solid rgba(15,23,42,0.04); box-shadow: 0 6px 18px rgba(16,24,40,0.04); }
    .reservation-card img{ width:40%; object-fit:cover; }
    .reservation-card-body{ padding:14px; display:flex; flex-direction:column; gap:8px; }
    .reservation-card h4{ margin:0; color:#0f172a; font-size:1.05rem; }
    .reservation-card-meta{ color:var(--muted); font-size:0.92rem; display:flex; gap:8px; }
    .reservation-card-footer{ margin-top:auto; display:flex; justify-content:space-between; align-items:center; gap:8px; }

    .select-option-btn{ background:transparent; border:1px solid rgba(15,23,42,0.06); padding:8px 12px; border-radius:999px; color:#0f172a; font-weight:700; cursor:pointer; transition:all .14s ease; }
    .select-option-btn:disabled{ opacity:0.5; cursor:not-allowed; }
    .select-option-btn:hover:not(:disabled){ transform:translateY(-2px); box-shadow:0 8px 18px rgba(16,24,40,0.06); }

    .summary-card{ background: linear-gradient(180deg,#fff,#fbfdff); padding:18px; border-radius:12px; box-shadow: var(--shadow-soft); border:1px solid rgba(15,23,42,0.04); }
    .summary-card h3{ margin:0 0 12px 0; font-size:1.1rem; color:#0f172a; }
    .summary-item-card{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px; border-radius:10px; background:transparent; border-bottom:1px solid rgba(15,23,42,0.03); }
    .summary-item-card-left{ display:flex; gap:12px; align-items:center; }
    .summary-item-title{ font-weight:700; color:#0f172a; margin:0; }
    .summary-item-subtitle{ margin:0; color:var(--muted); font-size:0.9rem; }
    .summary-item-price{ font-weight:800; color:#0f172a; }
    .summary-edit-btn{ background:transparent; color:var(--accent); border:none; font-weight:700; cursor:pointer; }

    .summary-total{ display:flex; justify-content:space-between; align-items:center; padding:18px 0 6px 0; }
    .summary-total strong{ font-size:1.4rem; color:#0f172a; }

    .confirmation-modal{ position:fixed; inset:0; display:none; align-items:flex-start; justify-content:center; background:rgba(2,6,23,0.55); z-index:9999; padding:20px; overflow-y:auto; }
    .confirmation-modal.open{ display:flex; }
    .confirmation-card{ width:min(760px,100%); max-height:calc(100vh - 40px); overflow-y:auto; box-sizing:border-box; border-radius:20px; padding:28px; background:linear-gradient(180deg,#fff,#fbfdff); box-shadow: 0 32px 80px rgba(2,6,23,0.18); border:1px solid rgba(15,23,42,0.06); }
    .confirmation-item{ border-radius:12px; padding:12px; background:#f8fafc; color:#0f172a; }
    .confirmation-total-amount{ font-size:1.6rem; color:#0f172a; }

    .confirm-submit-btn{ background: var(--accent); color:white; border-radius:999px; padding:12px 22px; }
    .confirm-cancel-btn{ background: #f3f4f6; border-radius:999px; padding:12px 22px; }

    @media (max-width: 640px){
        .confirmation-modal{ padding:12px; }
        .confirmation-card{ max-height:calc(100vh - 24px); padding:20px; }
        .confirmation-grid{ grid-template-columns:1fr !important; }
        .confirmation-actions{ flex-wrap:wrap; }
        .confirmation-actions button{ flex:1 1 100%; }
    }

    /* Responsive */
    @media (max-width: 980px){ .reservation-shell{ flex-direction:column; } .reservation-summary{ width:100%; } .reservation-card-grid{ grid-template-columns: 1fr; } }

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
                        <label class="field-label">Add a Person</label>
                        <select id="additionalGuests" class="field-input">
                            <option value="0" selected>No extra persons</option>
                            <option value="1">1 Person (₱650)</option>
                            <option value="2">2 Persons (₱1,300)</option>
                            <option value="3">3 Persons (₱1,950)</option>
                            <option value="4">4 Persons (₱2,600)</option>
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
                        <button type="button" class="summary-edit-btn" data-target="room-tab">Edit</button>
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
                        <button type="button" class="summary-edit-btn" data-target="amenities-tab">Edit</button>
                    </div>
                </article>
                <article class="summary-item-card">
                    <div class="summary-item-card-left">
                        <div class="summary-item-icon"><i class="fas fa-user-plus"></i></div>
                        <div class="summary-item-details">
                            <p class="summary-item-title">Extra Person</p>
                            <p class="summary-item-subtitle" id="summaryAdditionalGuests">None</p>
                        </div>
                    </div>
                    <div class="summary-item-card-right">
                        <span class="summary-item-price" id="summaryAdditionalGuestsPrice">₱0</span>
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
                        <button type="button" class="summary-edit-btn" data-target="event-tab">Edit</button>
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
                        <button type="button" class="summary-edit-btn" data-target="dining-tab">Edit</button>
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
        const additionalGuests = document.getElementById('additionalGuests');
        const summaryRoom = document.getElementById('summaryRoom');
        const summaryRoomDetails = document.getElementById('summaryRoomDetails');
        const summaryRoomPrice = document.getElementById('summaryRoomPrice');
        const summaryItems = document.getElementById('summaryItems');
        const summaryAdditionalGuests = document.getElementById('summaryAdditionalGuests');
        const summaryAdditionalGuestsPrice = document.getElementById('summaryAdditionalGuestsPrice');
        const summaryEvent = document.getElementById('summaryEvent');
        const summaryDining = document.getElementById('summaryDining');
        const summaryAmenitiesPrice = document.getElementById('summaryAmenitiesPrice');
        const summaryEventPrice = document.getElementById('summaryEventPrice');
        const summaryDiningPrice = document.getElementById('summaryDiningPrice');
        const summaryTotal = document.getElementById('summaryTotal');
        const summaryEditButtons = document.querySelectorAll('.summary-edit-btn');
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

        const updateSummary = () => {
            summaryRoom.textContent = selectedRoom ? selectedRoom : 'None';
            summaryRoomDetails.textContent = selectedRoom ? `${checkIn.value || 'Date'} – ${checkOut.value || 'Date'}${additionalGuests.value > 0 ? ` • ${additionalGuests.value} Extra Person(s)` : ''}` : 'Choose a room and dates';
            summaryRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            summaryItems.textContent = selectedAmenities.length > 0 ? `${selectedAmenities.length} selected` : '0 selected';
            summaryAdditionalGuests.textContent = additionalGuests.value > 0 ? `${additionalGuests.value} added` : 'None';
            summaryEvent.textContent = selectedEvent ? selectedEvent.title : 'None';
            summaryDining.textContent = selectedDining ? selectedDining.title : 'None';
            summaryAmenitiesPrice.textContent = `₱${selectedAmenities.reduce((sum, item) => sum + item.price, 0).toLocaleString()}`;
            summaryAdditionalGuestsPrice.textContent = `₱${(Number(additionalGuests.value) * 650).toLocaleString()}`;
            summaryEventPrice.textContent = `₱${(selectedEvent ? selectedEvent.price : 0).toLocaleString()}`;
            summaryDiningPrice.textContent = `₱${(selectedDining ? selectedDining.price : 0).toLocaleString()}`;

            confirmReservationId.textContent = selectedRoom ? `RES-${Math.floor(Math.random() * 9000) + 1000}` : 'RES-0000';
            confirmRoom.textContent = selectedRoom ? selectedRoom : 'None';
            confirmArrivingOn.textContent = checkIn.value || '—';
            confirmCheckOut.textContent = checkOut.value || '—';
            confirmGuests.textContent = additionalGuests.value > 0 ? `${additionalGuests.value} Extra Person(s)` : 'None';
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
        additionalGuests.addEventListener('change', updateSummary);

        const calculateTotal = () => {
            const amenitiesTotal = selectedAmenities.reduce((sum, item) => sum + item.price, 0);
            const eventTotal = selectedEvent ? selectedEvent.price : 0;
            const diningTotal = selectedDining ? selectedDining.price : 0;
            const extraGuestsTotal = Number(additionalGuests.value) * 650;
            return roomPrice + amenitiesTotal + eventTotal + diningTotal + extraGuestsTotal;
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

        summaryEditButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.dataset.target;
                if (!targetId) {
                    return;
                }

                tabs.forEach(tab => tab.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));

                const targetTab = Array.from(tabs).find(tab => tab.dataset.tab === targetId);
                const targetPanel = document.getElementById(targetId);
                if (targetTab && targetPanel) {
                    targetTab.classList.add('active');
                    targetPanel.classList.add('active');
                    targetPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
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
            additionalGuests.value = '0';
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
