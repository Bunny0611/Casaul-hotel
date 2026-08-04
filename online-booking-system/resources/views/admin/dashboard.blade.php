@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>
    
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">₱{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs text-green-500 mt-1">
                        <i class="fas fa-chart-line mr-1"></i>Avg Daily: ₱{{ number_format($avgDailyRevenue, 2) }}
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Room Availability</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $availableRooms }}/{{ $totalRooms }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-green-500">{{ $availableRooms }} Available</span> · 
                        <span class="text-red-500">{{ $occupiedRooms }} Occupied</span>
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-bed text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Active Reservations</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $activeReservations }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-yellow-500">{{ $pendingReservations }} Pending</span> · 
                        <span class="text-blue-500">{{ $completedReservations }} Completed</span>
                    </p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Guests</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalGuests }}</p>
                    <p class="text-xs text-red-500 mt-1">
                        <i class="fas fa-times-circle mr-1"></i>{{ $cancelledReservations }} Cancelled
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-users text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $occupancyRate }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-gradient-to-r from-green-500 via-yellow-500 to-red-500 rounded-full h-1.5" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-chart-pie text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Notifications</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $unreadMessages }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-envelope mr-1"></i>Unread Messages
                    </p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-bell text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
  
    
    

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 h-[420px] lg:h-[380px]">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Status <span class="text-sm font-normal text-gray-400">(Overview)</span></h3>
            <div class="h-[calc(100%-3rem)]">
                <canvas id="reservationChart" class="h-full w-full"></canvas>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 h-[420px] lg:h-[380px]">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Type Distribution <span class="text-sm font-normal text-gray-400">(By Type)</span></h3>
            <div class="h-[calc(100%-3rem)]">
                <canvas id="roomTypeChart" class="h-full w-full"></canvas>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Latest Customer Receipts</h3>
            <a href="{{ route('admin.reservations') }}" class="text-sm text-[#ff6b35] hover:underline">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentReservations as $reservation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $reservation->guest_name }}</div>
                            <div class="text-xs text-gray-500">{{ $reservation->guest_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $reservation->room->room_number ?? 'N/A' }} - {{ $reservation->room->room_type ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($reservation->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $color = $statusColors[$reservation->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $reservation->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2 block"></i>
                            No recent transactions yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  
    // (Revenue chart removed)
    // (Occupancy chart removed)

   
    const reservationCtx = document.getElementById('reservationChart').getContext('2d');
    new Chart(reservationCtx, {
        type: 'line',
        data: {
            labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
            datasets: [{
                label: 'Reservations',
                data: [{{ $pendingReservations }}, {{ $confirmedReservations }}, {{ $completedReservations }}, {{ $cancelledReservations }}],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.15)',
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' reservation(s)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

   
    const roomTypeCtx = document.getElementById('roomTypeChart').getContext('2d');
    const roomTypes = @json($roomTypes);
    new Chart(roomTypeCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(roomTypes),
            datasets: [{
                label: 'Rooms',
                data: Object.values(roomTypes),
                backgroundColor: ['#8b5cf6', '#ec4899', '#f97316', '#06b6d4', '#84cc16'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' room(s)';
                        }
                    }
                }
            }
        }
    });

});
</script>
@endsection
