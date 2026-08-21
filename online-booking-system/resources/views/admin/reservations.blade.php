@extends('admin.layout')

@section('content')
@php
    $stats = [
        'total' => $reservations->count(),
        'pending' => $reservations->where('status', 'pending')->count(),
        'confirmed' => $reservations->where('status', 'confirmed')->count(),
        'completed' => $reservations->where('status', 'completed')->count(),
    ];
@endphp

<div class="animate-fade-in space-y-6">
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

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Search</label>
            <input id="reservationSearch" type="search" placeholder="Search by guest, email, or room" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200" />
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

    <div class="grid grid-cols-5 gap-3 pb-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total</p>
            <p class="mt-2 text-xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending</p>
            <p class="mt-2 text-xl font-semibold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Confirmed</p>
            <p class="mt-2 text-xl font-semibold text-green-600">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Completed</p>
            <p class="mt-2 text-xl font-semibold text-blue-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-gray-500">Cancelled</p>
            <p class="mt-2 text-xl font-semibold text-red-600">{{ $reservations->where('status', 'cancelled')->count() }}</p>
        </div>
    </div>

    <h3 class="text-lg font-semibold text-gray-800">Reservation List</h3>

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
                    @forelse($reservations as $reservation)
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
                                <p class="text-lg font-medium">No reservations found.</p>
                                <p class="mt-1 text-sm">Reservations will appear here when guests make a booking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-4 p-4 md:hidden">
            @forelse($reservations as $reservation)
                <div class="reservation-item rounded-2xl border border-gray-200 bg-gray-50 p-4" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->room?->room_number ?? '')) }}">
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
                        <p><span class="font-medium text-gray-700">Check-in:</span> {{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</p>
                        <p><span class="font-medium text-gray-700">Check-out:</span> {{ $reservation->check_out instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_out->format('M d, Y') : $reservation->check_out }}</p>
                        <p><span class="font-medium text-gray-700">Amount:</span> ₱{{ number_format($reservation->total_amount, 2) }}</p>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <button type="button" onclick="showReservationDetails(this)" data-guest="{{ $reservation->guest_name }}" data-email="{{ $reservation->guest_email }}" data-phone="{{ $reservation->guest_phone }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-check-in="{{ $reservation->check_in }}" data-check-out="{{ $reservation->check_out }}" data-amount="₱{{ number_format($reservation->total_amount, 2) }}" data-status="{{ ucfirst($reservation->status) }}" class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><i class="fas fa-eye mr-1"></i>View</button>
                        @if($reservation->status === 'pending')
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">Confirm</button>
                        @elseif($reservation->status === 'confirmed')
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'checked-in')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Check-in</button>
                        @elseif($reservation->status === 'checked-in')
                            <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Check-out</button>
                        @endif
                        <div class="relative">
                            <button type="button" onclick="toggleReservationMenu(this)" class="rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700" aria-label="More actions"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="reservation-action-menu absolute right-0 top-10 z-20 hidden w-48 rounded-xl border border-gray-200 bg-white p-1 text-left shadow-lg">
                                <button type="button" onclick='editReservation(@json($reservation))' class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Edit Reservation</button>
                                @if($reservation->status !== 'cancelled' && $reservation->status !== 'completed')
                                    <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Cancel Reservation</button>
                                @endif
                                @if($reservation->status === 'completed')
                                    <button type="button" onclick="window.print()" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">Print Receipt</button>
                                @endif
                                <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Delete this reservation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete Reservation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                    <p class="text-lg font-medium">No reservations found.</p>
                    <p class="mt-1 text-sm">Reservations will appear here when guests make a booking.</p>
                </div>
            @endforelse
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
                <select name="room_id" id="adminEditRoom" class="rounded-lg border border-gray-300 px-3 py-2" required>@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>@endforeach</select>
                <input name="check_in" id="adminEditCheckIn" type="date" class="rounded-lg border border-gray-300 px-3 py-2" required><input name="check_in_time" id="adminEditCheckInTime" type="time" class="rounded-lg border border-gray-300 px-3 py-2">
                <input name="check_out" id="adminEditCheckOut" type="date" class="rounded-lg border border-gray-300 px-3 py-2" required><input name="check_out_time" id="adminEditCheckOutTime" type="time" class="rounded-lg border border-gray-300 px-3 py-2">
                <select name="status" id="adminEditStatus" class="rounded-lg border border-gray-300 px-3 py-2" required><option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="checked-in">Checked-in</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
                <input name="total_amount" id="adminEditAmount" type="number" min="0" step="0.01" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Total amount" required>
                <textarea name="special_requests" id="adminEditRequests" class="sm:col-span-2 rounded-lg border border-gray-300 px-3 py-2" rows="3" placeholder="Special requests"></textarea>
                <div class="flex justify-end gap-3 sm:col-span-2"><button type="button" onclick="closeAdminEditReservation()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700">Cancel</button><button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white">Save Changes</button></div>
            </form>
        </div>
    </div>

    <div id="reservationDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" aria-hidden="true">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-5 flex items-center justify-between"><h3 class="text-xl font-bold text-gray-800">Reservation Details</h3><button type="button" onclick="closeReservationDetails()" class="text-gray-500 hover:text-gray-800" aria-label="Close details"><i class="fas fa-times text-xl"></i></button></div>
            <div class="grid gap-3 text-sm sm:grid-cols-2"><div><span class="text-gray-500">Guest</span><p id="detailsGuest" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Email</span><p id="detailsEmail" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Phone</span><p id="detailsPhone" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Room</span><p id="detailsRoom" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Check-in</span><p id="detailsCheckIn" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Check-out</span><p id="detailsCheckOut" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Amount</span><p id="detailsAmount" class="font-semibold text-gray-900"></p></div><div><span class="text-gray-500">Status</span><p id="detailsStatus" class="font-semibold text-gray-900"></p></div></div>
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

    function showReservationDetails(button) {
        const modal = document.getElementById('reservationDetailsModal');
        document.getElementById('detailsGuest').textContent = button.dataset.guest || 'N/A';
        document.getElementById('detailsEmail').textContent = button.dataset.email || 'N/A';
        document.getElementById('detailsPhone').textContent = button.dataset.phone || 'N/A';
        document.getElementById('detailsRoom').textContent = button.dataset.room || 'N/A';
        document.getElementById('detailsCheckIn').textContent = button.dataset.checkIn || 'N/A';
        document.getElementById('detailsCheckOut').textContent = button.dataset.checkOut || 'N/A';
        document.getElementById('detailsAmount').textContent = button.dataset.amount || 'N/A';
        document.getElementById('detailsStatus').textContent = button.dataset.status || 'N/A';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReservationDetails() {
        const modal = document.getElementById('reservationDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.reservation-action-menu') && !event.target.closest('[aria-label="More actions"]')) {
            document.querySelectorAll('.reservation-action-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('reservationSearch');
        const statusFilter = document.getElementById('reservationStatusFilter');
        const reservationItems = document.querySelectorAll('.reservation-item');

        function filterReservations() {
            if (!searchInput || !statusFilter) return;

            const query = searchInput.value.trim().toLowerCase();
            const status = statusFilter.value;

            reservationItems.forEach(item => {
                const itemStatus = item.getAttribute('data-status') || '';
                const itemSearch = item.getAttribute('data-search') || '';

                const matchesQuery = query === '' || itemSearch.includes(query);
                const matchesStatus = status === '' || itemStatus === status;

                item.style.display = matchesQuery && matchesStatus ? '' : 'none';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterReservations);
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', filterReservations);
        }

        filterReservations();
    });

</script>
@endsection

