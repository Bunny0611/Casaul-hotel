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

    <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Reservation Management</h2>
            <p class="mt-1 text-sm text-gray-500">Manage guest bookings, update statuses, and create new reservations from one place.</p>
        </div>
        <button type="button" onclick="openAddReservationModal()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:from-orange-600 hover:to-orange-700">
            <i class="fas fa-plus mr-2"></i>Add Reservation
        </button>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Total</p>
            <p class="mt-2 text-2xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="mt-2 text-2xl font-semibold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Confirmed</p>
            <p class="mt-2 text-2xl font-semibold text-green-600">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-gray-500">Completed</p>
            <p class="mt-2 text-2xl font-semibold text-blue-600">{{ $stats['completed'] }}</p>
        </div>
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
                        <tr class="transition-colors hover:bg-gray-50">
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
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
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

<div id="addReservationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
    <div class="relative w-full max-w-2xl rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
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

        <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Email</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Guest Phone</label>
                    <input type="text" name="guest_phone" value="{{ old('guest_phone') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('guest_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Room</label>
                    <select name="room_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select a room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->room_number }} - {{ $room->room_type }}</option>
                        @endforeach
                    </select>
                    @error('room_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-in</label>
                    <input type="date" name="check_in" value="{{ old('check_in') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_in')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Check-out</label>
                    <input type="date" name="check_out" value="{{ old('check_out') }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('check_out')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="number" name="total_amount" value="{{ old('total_amount') }}" min="0" step="0.01" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('total_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Special Requests</label>
                <textarea name="special_requests" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('special_requests') }}</textarea>
                @error('special_requests')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closeAddReservationModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition hover:bg-gray-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 font-medium text-white transition hover:bg-orange-600">Save Reservation</button>
            </div>
        </form>
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

    function openAddReservationModal() {
        document.getElementById('addReservationModal').classList.remove('hidden');
        document.getElementById('addReservationModal').classList.add('flex');
    }

    function closeAddReservationModal() {
        document.getElementById('addReservationModal').classList.add('hidden');
        document.getElementById('addReservationModal').classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('addReservationModal');
        if (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeAddReservationModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddReservationModal();
            }
        });

        @if($errors->any())
            openAddReservationModal();
        @endif
    });
</script>
@endsection
