@extends('app')

@section('content')

<div class="profile-page">
    <section class="profile-hero">
        <p class="eyebrow">My Account</p>
        <h1>My Records</h1>
        <p>Here is a list of all your reservations.</p>
    </section>

    <div class="records-toolbar">
        <a href="{{ route('guest.profile') }}" class="btn btn-back">&larr; Back to Profile</a>
    </div>

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
                        <th>Request</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $index => $reservation)
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
                            <td>{{ $reservation->special_requests ?? '—' }}</td>
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
                    <label for="request-type-search">Request Type
                        <div class="request-type-picker" data-request-type-picker>
                            <input id="request-type-value" type="hidden" name="request_type" value="{{ old('request_type') }}" required>
                            <input id="request-type-search" class="request-type-search" type="search" placeholder="Select what you need" autocomplete="off" aria-label="Search request types" aria-controls="request-type-options" aria-expanded="false" role="combobox">
                            <button type="button" class="request-type-toggle" aria-label="Show request types" aria-controls="request-type-options" aria-expanded="false"><i class="fas fa-chevron-down"></i></button>
                            <div class="request-type-options" id="request-type-options" role="listbox" hidden>
                                <div class="request-type-group" data-request-type-group>
                                    <span class="request-type-group-label">Housekeeping</span>
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
                        </div>
                    </label>
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
        const valueInput = requestTypePicker.querySelector('#request-type-value');
        const toggleButton = requestTypePicker.querySelector('.request-type-toggle');
        const optionsPanel = requestTypePicker.querySelector('.request-type-options');
        const options = [...requestTypePicker.querySelectorAll('.request-type-option')];
        const emptyMessage = requestTypePicker.querySelector('.request-type-empty');
        const descriptionInput = document.getElementById('request-description');
        const otherRequestHint = document.getElementById('other-request-hint');

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

        function selectRequestType(option) {
            valueInput.value = option.dataset.value;
            searchInput.value = option.dataset.value;
            options.forEach(function (item) { item.setAttribute('aria-selected', String(item === option)); });
            const isOtherRequest = option.dataset.value.startsWith('Other');
            otherRequestHint.hidden = !isOtherRequest;
            descriptionInput.placeholder = isOtherRequest
                ? 'Please describe exactly what assistance you need.'
                : 'Please describe what you need or the problem you are experiencing.';
            setRequestTypeOpen(false);
        }

        const oldRequestType = valueInput.value;
        if (oldRequestType) {
            searchInput.value = oldRequestType;
            const oldOption = options.find(function (option) { return option.dataset.value === oldRequestType; });
            if (oldOption) {
                oldOption.setAttribute('aria-selected', 'true');
                selectRequestType(oldOption);
            }
        }
        searchInput.addEventListener('focus', function () { setRequestTypeOpen(true); filterRequestTypes(); });
        searchInput.addEventListener('input', function () { valueInput.value = ''; setRequestTypeOpen(true); filterRequestTypes(); });
        toggleButton.addEventListener('click', function () { setRequestTypeOpen(optionsPanel.hidden); searchInput.focus(); filterRequestTypes(); });
        options.forEach(function (option) { option.addEventListener('click', function () { selectRequestType(option); }); });
        document.addEventListener('click', function (event) {
            if (!requestTypePicker.contains(event.target)) setRequestTypeOpen(false);
        });
    }
</script>

@endsection
