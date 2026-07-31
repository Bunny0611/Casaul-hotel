@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Comprehensive Reporting System</h2>
    
    <!-- Date Range & Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>Export Excel
                </button>
                 <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>Export PDF
                </button>
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
    
    <!-- Report Tabs -->
    <div class="bg-white rounded-xl shadow-lg mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 text-center">
            <button type="button" data-tab="financial" class="tab-btn py-4 border-b-2 border-orange-500 font-semibold text-orange-600 transition-all duration-200">
                <i class="fas fa-coins mr-2"></i>
                Financial
            </button>

            <button type="button" data-tab="reservations" class="tab-btn py-4 text-gray-600 transition-all duration-200">
                <i class="fas fa-calendar-check mr-2"></i>
                Reservations
            </button>

            <button type="button" data-tab="occupancy" class="tab-btn py-4 text-gray-600 transition-all duration-200">
                <i class="fas fa-bed mr-2"></i>
                Occupancy
            </button>

            <button type="button" data-tab="guests" class="tab-btn py-4 text-gray-600 transition-all duration-200">
                <i class="fas fa-users mr-2"></i>
                Guests
            </button>
        </div>
    </div>

    <div data-panel="financial" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Average Payment</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($averagePayment, 2) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-calculator text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Highest Payment</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($highestPayment, 2) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-arrow-up text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Lowest Payment</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($lowestPayment, 2) }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Reservations</p>
                <h2 class="text-3xl font-bold">{{ $reservations->count() }}</h2>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Completed</p>
                <h2 class="text-3xl font-bold text-green-600">
                    {{ $reservations->where('status','completed')->count() }}
                </h2>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500">
                    {{ $reservations->where('status','pending')->count() }}
                </h2>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Cancelled</p>
                <h2 class="text-3xl font-bold text-red-500">
                    {{ $reservations->where('status','cancelled')->count() }}
                </h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Reservation Status</h3>
            <div class="mx-auto max-w-[320px]">
                <canvas id="reservationStatusChart" class="w-full h-auto"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method Breakdown</h3>
                <canvas id="paymentMethodChart" height="250"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Room Type</h3>
                <canvas id="roomTypeChart" height="250"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
            <canvas id="monthlyRevenue"></canvas>
        </div>
    </div>

    <div data-panel="reservations" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Reservations</p>
                <h2 class="text-3xl font-bold">{{ $reservations->count() }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Confirmed</p>
                <h2 class="text-3xl font-bold text-blue-600">{{ $reservations->where('status','confirmed')->count() }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Completed</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $reservations->where('status','completed')->count() }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Pending</p>
                <h2 class="text-3xl font-bold text-yellow-500">{{ $reservations->where('status','pending')->count() }}</h2>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reservations->take(8) as $reservation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->guest_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservation->room ? $reservation->room->room_number : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <p>No transactions found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-panel="occupancy" class="hidden space-y-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold">Room Occupancy</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="text-center p-4 rounded-lg bg-green-50">
                    <h2 class="text-4xl font-bold text-green-600">{{ $availableRooms }}</h2>
                    <p class="mt-2 text-gray-600">Available</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-red-50">
                    <h2 class="text-4xl font-bold text-red-600">{{ $occupiedRooms }}</h2>
                    <p class="mt-2 text-gray-600">Occupied</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-yellow-50">
                    <h2 class="text-4xl font-bold text-yellow-500">{{ $maintenanceRooms }}</h2>
                    <p class="mt-2 text-gray-600">Maintenance</p>
                </div>
            </div>
        </div>
    </div>

    <div data-panel="guests" class="hidden space-y-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Activity</h3>
            <div class="grid gap-4">
                @forelse($reservations->take(8) as $reservation)
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $reservation->guest_name }}</p>
                        <p class="text-sm text-gray-500">{{ $reservation->guest_email }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $reservation->status }}">
                        {{ ucfirst($reservation->status) }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500">No guest activity found.</p>
                @endforelse
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const panels = document.querySelectorAll('[data-panel]');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const target = this.getAttribute('data-tab');

                tabButtons.forEach(btn => {
                    btn.classList.remove('border-orange-500', 'font-semibold', 'text-orange-600');
                    btn.classList.add('text-gray-600');
                });

                this.classList.remove('text-gray-600');
                this.classList.add('border-orange-500', 'font-semibold', 'text-orange-600');

                panels.forEach(panel => {
                    panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== target);
                });
            });
        });
    });

    // Payment Method Chart
    const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cash', 'Credit Card', 'Bank Transfer', 'Online Payment'],
            datasets: [{
                label: 'Payments',
                data: [35, 40, 15, 10],
                backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f97316'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Room Type Chart
    const roomTypeCtx2 = document.getElementById('roomTypeChart').getContext('2d');
    new Chart(roomTypeCtx2, {
        type: 'bar',
        data: {
            labels: ['Deluxe', 'Executive', 'Presidential', 'Standard'],
            datasets: [{
                label: 'Revenue (₱)',
                data: [45000, 68000, 92000, 28000],
                backgroundColor: ['#8b5cf6', '#ec4899', '#f97316', '#06b6d4'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } }
                }
            }
        }
    });
    // Monthly Revenue Chart
const monthlyRevenueCtx = document.getElementById('monthlyRevenue').getContext('2d');

new Chart(monthlyRevenueCtx, {
    type: 'line',
    data: {
        labels: [
            'Jan','Feb','Mar','Apr',
            'May','Jun','Jul','Aug',
            'Sep','Oct','Nov','Dec'
        ],
        datasets: [{
            label: 'Revenue',
            data: [
                12000,
                18000,
                15000,
                22000,
                30000,
                28000,
                35000,
                40000,
                25000,
                32000,
                45000,
                50000
            ],
            borderColor: '#f97316',
            backgroundColor: 'rgba(249,115,22,0.2)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true
    }
});

    const reservationStatusChart = new Chart(
document.getElementById('reservationStatusChart'),
{
    type:'pie',

    data:{
        labels:[
            'Pending',
            'Confirmed',
            'Completed',
            'Cancelled'
        ],

        datasets:[{

            data:[
                {{ $pendingReservations }},
                {{ $confirmedReservations }},
                {{ $completedCount }},
                {{ $cancelledReservations }}
            ],

            backgroundColor:[
                '#f59e0b',
                '#3b82f6',
                '#10b981',
                '#ef4444'
            ]
        }]
    }

});
</script>
@endsection
