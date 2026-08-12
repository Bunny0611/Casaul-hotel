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
            <p class="mt-1 text-sm text-gray-500">Manage guest bookings, update statuses, and create new reservations from one place.</p>
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

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="rounded-xl bg-gray-100 p-2 text-gray-600">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-700">Pending</p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">{{ $stats['pending'] }}</p>
                </div>
                <div class="rounded-xl bg-amber-100 p-2 text-amber-700">
                    <i class="fas fa-clock text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-green-200 bg-green-50 p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700">Confirmed</p>
                    <p class="mt-2 text-2xl font-bold text-green-800">{{ $stats['confirmed'] }}</p>
                </div>
                <div class="rounded-xl bg-green-100 p-2 text-green-700">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Completed</p>
                    <p class="mt-2 text-2xl font-bold text-blue-800">{{ $stats['completed'] }}</p>
                </div>
                <div class="rounded-xl bg-blue-100 p-2 text-blue-700">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm transition-shadow hover:shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700">Cancelled</p>
                    <p class="mt-2 text-2xl font-bold text-red-800">{{ $reservations->where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="rounded-xl bg-red-100 p-2 text-red-700">
                    <i class="fas fa-times-circle text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">Reservation List</h3>
        <button type="button" onclick="openAddReservationModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#ff6b35] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85a2a]">
            <i class="fas fa-plus"></i> Add New Reservation
        </button>
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
                    @forelse($reservations as $reservation)
                        <tr class="reservation-item transition-colors hover:bg-gray-50" data-status="{{ $reservation->status }}" data-search="{{ strtolower($reservation->guest_name . ' ' . $reservation->guest_email . ' ' . ($reservation->room?->room_number ?? '')) }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $reservation->guest_name }}</div>
                                <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_in instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_in->format('M d, Y') : $reservation->check_in }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $reservation->check_out instanceof \Illuminate\Support\Carbon\Carbon ? $reservation->check_out->format('M d, Y') : $reservation->check_out }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg p-2 text-green-600 transition hover:bg-green-50 hover:text-green-800" title="Confirm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg p-2 text-red-600 transition hover:bg-red-50 hover:text-red-800" title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg p-2 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800" title="Complete">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                                <p class="text-lg font-medium">No reservations found.</p>
                                <p class="mt-1 text-sm">Create your first reservation to get started.</p>
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
                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-700">Confirm</button>
                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-700">Cancel</button>
                        <button type="button" onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700">Complete</button>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times mb-4 text-4xl text-gray-300"></i>
                    <p class="text-lg font-medium">No reservations found.</p>
                    <p class="mt-1 text-sm">Create your first reservation to get started.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<form id="reservationStatusForm" action="" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="reservationStatus">
</form>

<div id="addReservationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
        <button type="button" onclick="closeAddReservationModal()" class="absolute right-4 top-4 text-gray-500 transition hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>

        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add New Reservation</h3>
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

        <form method="POST" action="{{ route('admin.reservations.store') }}">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" name="guest_name" required value="{{ old('guest_name') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Email</label>
                    <input type="email" name="guest_email" required value="{{ old('guest_email') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Phone</label>
                    <input type="text" name="guest_phone" required value="{{ old('guest_phone') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" required class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                        <option value="">Select Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" name="check_in" required value="{{ old('check_in') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" name="check_out" required value="{{ old('check_out') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="number" step="0.01" min="0" name="total_amount" required value="{{ old('total_amount') }}" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Special Requests</label>
                    <textarea name="special_requests" rows="3" class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200">{{ old('special_requests') }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddReservationModal()" class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Cancel</button>
                <button type="submit" class="rounded-xl bg-[#ff6b35] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#e85a2a]">Create Reservation</button>
            </div>
        </form>
    </div>
</div>

<script>
    function changeReservationStatus(id, status) {
        if (confirm(`Are you sure you want to change the status to ${status}?`)) {
            var actionTemplate = "{{ route('admin.reservations.status', ['id' => '__ID__']) }}";
            document.getElementById('reservationStatusForm').action = actionTemplate.replace('__ID__', id);
            document.getElementById('reservationStatus').value = status;
            document.getElementById('reservationStatusForm').submit();
        }
    }

    function openAddReservationModal() {
        document.getElementById('addReservationModal').classList.remove('hidden');
        document.getElementById('addReservationModal').classList.add('flex');
    }

    function closeAddReservationModal() {
        document.getElementById('addReservationModal').classList.add('hidden');
        document.getElementById('addReservationModal').classList.remove('flex');
    }

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

