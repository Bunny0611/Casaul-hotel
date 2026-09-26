@extends('app')

@section('content')

<main class="accommodation-reference-page">
    <section class="accommodation-reference-hero">
        <div class="accommodation-reference-hero-copy">
            <p class="accommodation-eyebrow">Accommodation <span></span> Rooms &amp; Suites</p>
            <h1>Rooms Designed<br>for Your <strong>Comfort</strong></h1>
            <p class="accommodation-hero-text">Relax in thoughtfully designed rooms and suites<br class="accommodation-desktop-break"> where comfort meets elegance.</p>
            <div class="accommodation-hero-actions">
                <a class="accommodation-check-button" href="#rooms"><i class="far fa-calendar-alt"></i> Check Availability <b>→</b></a>
                <a class="accommodation-explore-button" href="#rooms">Explore Rooms <b>→</b></a>
            </div>
            <div class="accommodation-hero-benefits" aria-label="Booking benefits">
                <span><i class="fas fa-shield-alt"></i> Best Available Rates</span>
                <span><i class="fas fa-lock"></i> Secure Booking</span>
                <span><i class="fas fa-bolt"></i> Instant Confirmation</span>
            </div>
        </div>
        <div class="accommodation-reference-hero-image">
            <img src="{{ asset('storage/rooms/1790407340_6ab772ac29efa.png') }}" alt="Elegant CASAUL Hotel guest room">
        </div>
        <div class="accommodation-booking-bar" aria-label="Room search" data-accommodation-url="{{ route('accommodation') }}" data-searched="{{ $hasSearched ? 'true' : 'false' }}" data-guests="{{ $guestCount }}" data-room-type="{{ $selectedRoomType }}">
            <button class="accommodation-booking-field" type="button" data-date-target="accommodation-check-in">
                <i class="far fa-calendar-alt"></i><span><small>Check-in</small><strong data-date-label="accommodation-check-in">{{ \Carbon\Carbon::parse($checkInDate)->format('M j, Y') }}</strong></span><b>⌄</b>
                <input id="accommodation-check-in" type="date" value="{{ $checkInDate }}" min="{{ today()->toDateString() }}" aria-label="Check-in date">
            </button>
            <button class="accommodation-booking-field" type="button" data-date-target="accommodation-check-out">
                <i class="far fa-calendar-alt"></i><span><small>Check-out</small><strong data-date-label="accommodation-check-out">{{ \Carbon\Carbon::parse($checkOutDate)->format('M j, Y') }}</strong></span><b>⌄</b>
                <input id="accommodation-check-out" type="date" value="{{ $checkOutDate }}" min="{{ $checkInDate }}" aria-label="Check-out date">
            </button>
            <div class="accommodation-booking-control">
                <button class="accommodation-booking-field" type="button" aria-expanded="false" aria-controls="accommodation-guests-popover">
                    <i class="far fa-user"></i><span><small>Guests</small><strong id="accommodation-guests-label">{{ $guestCount }} Guest{{ $guestCount === 1 ? '' : 's' }}</strong></span><b>⌄</b>
                </button>
                <div class="accommodation-selector-popover" id="accommodation-guests-popover" hidden>
                    <span>Guests</span><button type="button" data-counter="guests" data-step="-1" aria-label="Decrease guests">−</button><strong id="accommodation-guests-count">{{ $guestCount }}</strong><button type="button" data-counter="guests" data-step="1" aria-label="Increase guests">+</button>
                </div>
            </div>
            <div class="accommodation-booking-control">
                <button class="accommodation-booking-field" type="button" aria-expanded="false" aria-controls="accommodation-rooms-popover">
                    <i class="fas fa-bed"></i><span><small>Rooms</small><strong id="accommodation-rooms-label">{{ $selectedRoomType }}</strong></span><b>⌄</b>
                </button>
                <div class="accommodation-selector-popover" id="accommodation-rooms-popover" hidden>
                    <button type="button" class="accommodation-room-type-option" data-room-type="Deluxe Room">Deluxe Room</button>
                    <button type="button" class="accommodation-room-type-option" data-room-type="Standard Room">Standard Room</button>
                </div>
            </div>
            <button class="accommodation-booking-search" type="button"><i class="fas fa-search"></i> Search</button>
            <p class="accommodation-booking-error {{ $searchError ? 'is-visible' : '' }}" role="alert" aria-live="polite">{{ $searchError }}</p>
        </div>
    </section>

    <section class="accommodation-rooms-section" id="rooms">
        <header class="accommodation-section-heading">
            <h2>Featured Rooms</h2>
            <div class="accommodation-heading-rule"><span></span><i class="fas fa-bed"></i><span></span></div>
        </header>

        @if($rooms->isNotEmpty())
        <div class="accommodation-room-grid">
            @foreach($rooms as $index => $room)
                <article class="accommodation-room-card">
                    @if($index === 0)<span class="accommodation-room-badge">Best Seller</span>@endif
                    @php
                        $roomStoragePath = str_starts_with((string) $room->image, 'storage/') ? substr($room->image, 8) : $room->image;
                        $roomImage = $roomStoragePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($roomStoragePath)
                            ? asset('storage/' . $roomStoragePath)
                            : null;
                    @endphp
                    <div class="accommodation-room-image">@if($roomImage)<img src="{{ $roomImage }}" alt="{{ $room->room_type }}">@endif</div>
                    <div class="accommodation-room-content">
                        <h3>{{ $room->room_type }}</h3>
                        <p class="accommodation-room-number">Room {{ $room->room_number ?? 'N/A' }}</p>
                        <p class="accommodation-room-price">₱{{ number_format($room->price, 2) }} <span>/ night</span></p>
                        <p class="accommodation-room-description">{{ $room->description ?? 'Description unavailable.' }}</p>
                        <div class="accommodation-room-meta"><span><i class="fas fa-users"></i> {{ $room->capacity }} Guests</span><span><i class="fas fa-bed"></i> {{ $room->bed_type ?? 'Bed details unavailable' }}</span></div>
                    </div>
                    <button class="accommodation-room-action accommodation-details-trigger" type="button" data-room-name="{{ $room->room_type }}" data-room-number="{{ $room->room_number ?? 'N/A' }}" data-room-price="₱{{ number_format($room->price, 2) }}" data-room-description="{{ $room->description ?? 'Description unavailable.' }}" data-room-image="{{ $roomImage ?? '' }}" data-room-capacity="{{ $room->capacity }}" data-room-bed-type="{{ $room->bed_type ?? 'Bed details unavailable' }}">View Details</button>
                </article>
                    @endforeach
        </div>
        @elseif($hasSearched)
            <div id="accommodation-no-room-results"><strong>No rooms available</strong><p>No rooms match your selected dates, guest count, and room type.</p></div>
        @endif
    </section>

    <section class="accommodation-benefits" aria-label="Why guests love CASAUL Hotel">
        <article><i class="fas fa-crown"></i><div><h3>Premium Facilities</h3><p>Enjoy world-class facilities and thoughtful in-room essentials.</p></div></article>
        <article><i class="fas fa-users"></i><div><h3>Friendly Hospitality</h3><p>We take care of the details so your stay feels effortless.</p></div></article>
        <article><i class="fas fa-map-marker-alt"></i><div><h3>Prime Location</h3><p>Close to dining, attractions, and easy transport access.</p></div></article>
    </section>

    <div class="accommodation-details-modal" id="accommodation-details-modal" aria-hidden="true">
        <div class="accommodation-details-dialog" role="dialog" aria-modal="true" aria-labelledby="accommodation-details-title">
            <button class="accommodation-details-close" type="button" aria-label="Close room details"><i class="fas fa-times"></i></button>
            <img id="accommodation-details-image" src="" alt="">
            <div class="accommodation-details-copy">
                <p class="accommodation-eyebrow">Room Details</p>
                <h2 id="accommodation-details-title"></h2>
                <p class="accommodation-room-number" id="accommodation-details-room-number"></p>
                <p class="accommodation-details-price" id="accommodation-details-price"></p>
                <p id="accommodation-details-description"></p>
                <div class="accommodation-room-meta"><span><i class="fas fa-users"></i> <span id="accommodation-details-capacity"></span> Guests</span><span><i class="fas fa-bed"></i> <span id="accommodation-details-bed-type"></span></span></div>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.accommodation-details-trigger').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('accommodation-details-title').textContent = button.dataset.roomName;
            document.getElementById('accommodation-details-room-number').textContent = 'Room ' + (button.dataset.roomNumber || 'N/A');
            document.getElementById('accommodation-details-price').textContent = button.dataset.roomPrice + ' / night';
            document.getElementById('accommodation-details-description').textContent = button.dataset.roomDescription;
            const detailsImage = document.getElementById('accommodation-details-image');
            if (button.dataset.roomImage) detailsImage.src = button.dataset.roomImage;
            else detailsImage.removeAttribute('src');
            detailsImage.alt = button.dataset.roomName;
            document.getElementById('accommodation-details-capacity').textContent = button.dataset.roomCapacity;
            document.getElementById('accommodation-details-bed-type').textContent = button.dataset.roomBedType;
            document.getElementById('accommodation-details-modal').classList.add('is-open');
            document.getElementById('accommodation-details-modal').setAttribute('aria-hidden', 'false');
        });
    });

    function closeAccommodationDetails() {
        document.getElementById('accommodation-details-modal').classList.remove('is-open');
        document.getElementById('accommodation-details-modal').setAttribute('aria-hidden', 'true');
    }

    document.querySelector('.accommodation-details-close').addEventListener('click', closeAccommodationDetails);
    document.getElementById('accommodation-details-modal').addEventListener('click', function (event) {
        if (event.target === this) closeAccommodationDetails();
    });

    (function () {
        const bookingBar = document.querySelector('.accommodation-booking-bar');
        if (!bookingBar) return;

        const checkIn = document.getElementById('accommodation-check-in');
        const checkOut = document.getElementById('accommodation-check-out');
        const error = bookingBar.querySelector('.accommodation-booking-error');
        const counters = { guests: Number(bookingBar.dataset.guests) || 2 };
        let selectedRoomType = bookingBar.dataset.roomType;
        const formatDate = function (value) {
            if (!value) return 'Select date';
            return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                .format(new Date(value + 'T00:00:00'));
        };
        const updateDateLabel = function (input) {
            const label = bookingBar.querySelector('[data-date-label="' + input.id + '"]');
            if (label) label.textContent = formatDate(input.value);
        };
        const openDatePicker = function (input) {
            if (typeof input.showPicker === 'function') input.showPicker();
            else input.focus();
        };

        checkIn.addEventListener('change', function () {
            checkOut.min = checkIn.value || checkOut.min;
            if (checkOut.value && checkOut.value < checkIn.value) {
                checkOut.value = '';
                updateDateLabel(checkOut);
            }
            updateDateLabel(checkIn);
        });
        checkOut.addEventListener('change', function () {
            if (checkIn.value && checkOut.value < checkIn.value) checkOut.value = '';
            updateDateLabel(checkOut);
        });
        bookingBar.querySelectorAll('[data-date-target]').forEach(function (field) {
            field.addEventListener('click', function () {
                openDatePicker(document.getElementById(field.dataset.dateTarget));
            });
        });

        bookingBar.querySelectorAll('.accommodation-booking-control > .accommodation-booking-field').forEach(function (field) {
            field.addEventListener('click', function () {
                const popover = document.getElementById(field.getAttribute('aria-controls'));
                const isOpen = !popover.hidden;
                bookingBar.querySelectorAll('.accommodation-selector-popover').forEach(function (item) { item.hidden = true; });
                bookingBar.querySelectorAll('.accommodation-booking-control > .accommodation-booking-field').forEach(function (item) { item.setAttribute('aria-expanded', 'false'); });
                popover.hidden = isOpen;
                field.setAttribute('aria-expanded', String(!isOpen));
            });
        });
        bookingBar.querySelectorAll('[data-counter]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                const type = button.dataset.counter;
                counters[type] = Math.max(1, counters[type] + Number(button.dataset.step));
                document.getElementById('accommodation-' + type + '-count').textContent = counters[type];
                document.getElementById('accommodation-' + type + '-label').textContent = counters[type] + ' Guest' + (counters[type] === 1 ? '' : 's');
            });
        });
        bookingBar.querySelectorAll('[data-room-type]').forEach(function (option) {
            option.addEventListener('click', function (event) {
                event.stopPropagation();
                selectedRoomType = option.dataset.roomType;
                document.getElementById('accommodation-rooms-label').textContent = selectedRoomType;
                option.closest('.accommodation-selector-popover').hidden = true;
                option.closest('.accommodation-booking-control').querySelector('[aria-controls="accommodation-rooms-popover"]').setAttribute('aria-expanded', 'false');
            });
        });
        document.addEventListener('click', function (event) {
            if (!bookingBar.contains(event.target)) {
                bookingBar.querySelectorAll('.accommodation-selector-popover').forEach(function (item) { item.hidden = true; });
                bookingBar.querySelectorAll('.accommodation-booking-control > .accommodation-booking-field').forEach(function (item) { item.setAttribute('aria-expanded', 'false'); });
            }
        });
        bookingBar.querySelector('.accommodation-booking-search').addEventListener('click', function () {
            let message = '';
            if (!checkIn.value) message = 'Please select a check-in date.';
            else if (!checkOut.value) message = 'Please select a check-out date.';
            else if (checkOut.value <= checkIn.value) message = 'Check-out must be after check-in.';
            else if (counters.guests < 1) message = 'Please select at least one guest.';
            else if (!selectedRoomType) message = 'Please select a room type.';
            error.textContent = message;
            error.classList.toggle('is-visible', Boolean(message));
            if (message) return;

            const searchUrl = new URL(bookingBar.dataset.accommodationUrl, window.location.origin);
            searchUrl.search = new URLSearchParams({
                search: '1',
                check_in: checkIn.value,
                check_out: checkOut.value,
                guests: String(counters.guests),
                room_type: selectedRoomType
            }).toString();
            window.location.assign(searchUrl.toString());
        });

        if (bookingBar.dataset.searched === 'true') {
            document.getElementById('rooms').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }());
</script>

@endsection
