@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Reservation Management</h2>
        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add Reservation
        </button>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest Name</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reservation->guest_name }}</div>
                            <div class="text-sm text-gray-500">{{ $reservation->guest_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $reservation->room ? $reservation->room->room_number : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->check_in->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->check_out->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $reservation->status }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <button onclick="changeReservationStatus({{ $reservation->id }}, 'confirmed')" class="text-green-600 hover:text-green-800 transition-colors" title="Confirm">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="changeReservationStatus({{ $reservation->id }}, 'cancelled')" class="text-red-600 hover:text-red-800 transition-colors" title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                            <button onclick="changeReservationStatus({{ $reservation->id }}, 'completed')" class="text-blue-600 hover:text-blue-800 transition-colors" title="Complete">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-4xl mb-4 text-gray-300"></i>
                            <p>No reservations found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<form id="reservationStatusForm" action="" method="POST">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" id="reservationStatus">
</form>

<script>
    function changeReservationStatus(id, status) {
        if(confirm(`Are you sure you want to change the status to ${status}?`)) {
            var actionTemplate = "{{ route('admin.reservations.status', ['id' => '__ID__']) }}";
            document.getElementById('reservationStatusForm').action = actionTemplate.replace('__ID__', id);
            document.getElementById('reservationStatus').value = status;
            document.getElementById('reservationStatusForm').submit();
        }
    }
</script>
@endsection
