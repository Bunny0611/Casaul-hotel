@extends('app')

@section('content')

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
                        <span class="summary-item-price">₱0</span>
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
                        <span class="summary-item-price">₱0</span>
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
                        <span class="summary-item-price">₱0</span>
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
</div>

<form id="reservationForm" action="{{ route('reservation.store') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="room_id" id="reservationRoomId">
    <input type="hidden" name="check_in" id="reservationCheckIn">
    <input type="hidden" name="check_out" id="reservationCheckOut">
    <input type="hidden" name="guest_name" id="reservationGuestName" value="Guest">
    <input type="hidden" name="guest_email" id="reservationGuestEmail" value="guest@example.com">
    <input type="hidden" name="guest_phone" id="reservationGuestPhone" value="0000000000">
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
        const summaryCheckIn = document.getElementById('summaryCheckIn');
        const summaryCheckOut = document.getElementById('summaryCheckOut');
        const summaryGuests = document.getElementById('summaryGuests');
        const summaryItems = document.getElementById('summaryItems');
        const summaryEvent = document.getElementById('summaryEvent');
        const summaryDining = document.getElementById('summaryDining');
        const summaryTotal = document.getElementById('summaryTotal');
        const confirmBtn = document.getElementById('confirmReservationBtn');
        const clearBtn = document.getElementById('clearReservationBtn');
        const reservationForm = document.getElementById('reservationForm');
        const reservationRoomId = document.getElementById('reservationRoomId');
        const reservationCheckIn = document.getElementById('reservationCheckIn');
        const reservationCheckOut = document.getElementById('reservationCheckOut');
        const reservationTotalAmount = document.getElementById('reservationTotalAmount');

        let selectedRoom = null;
        const selectedItems = [];
        let roomPrice = 0;
        let additionalTotal = 0;

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
            summaryCheckIn.textContent = checkIn.value || '—';
            summaryCheckOut.textContent = checkOut.value || '—';
            summaryGuests.textContent = `${guestCount.value} Guests`;
            summaryRoom.textContent = selectedRoom ? selectedRoom : 'None';
            summaryRoomDetails.textContent = selectedRoom ? `${checkIn.value || 'Date'} – ${checkOut.value || 'Date'} • ${guestCount.value} Guests` : 'Choose a room and dates';
            summaryRoomPrice.textContent = `₱${roomPrice.toLocaleString()}`;
            summaryItems.textContent = selectedItems.length > 0 ? `${selectedItems.length} selected` : '0 selected';
            summaryEvent.textContent = 'None';
            summaryDining.textContent = 'None';

            const total = roomPrice + additionalTotal;
            summaryTotal.textContent = `₱${total.toLocaleString()}`;
            reservationTotalAmount.value = total;
            reservationCheckIn.value = checkIn.value;
            reservationCheckOut.value = checkOut.value;
        }

        checkIn.addEventListener('change', updateSummary);
        checkOut.addEventListener('change', updateSummary);
        guestCount.addEventListener('change', updateSummary);

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
                } else {
                    const itemIndex = selectedItems.indexOf(dataId);
                    if (itemIndex === -1) {
                        selectedItems.push(dataId);
                        additionalTotal += price;
                        this.textContent = 'Added';
                        this.disabled = true;
                    }
                }
                updateSummary();
            });
        });

        confirmBtn.addEventListener('click', function () {
            if (!selectedRoom) {
                alert('Please select a room before confirming your reservation.');
                return;
            }
            if (!checkIn.value || !checkOut.value) {
                alert('Please select check-in and check-out dates.');
                return;
            }
            reservationForm.submit();
        });

        clearBtn.addEventListener('click', function () {
            selectedRoom = null;
            selectedItems.length = 0;
            additionalTotal = 0;
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
