@extends('admin.layout')

@section('content')
<div class="animate-fade-in">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Comprehensive Reporting System</h2>
    
    <!-- Date Range & Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <form id="reportsFilterForm" method="GET" action="{{ route('admin.reports') }}">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <input id="reportFrom" name="from" type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" value="{{ request('from') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input id="reportTo" name="to" type="date" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" value="{{ request('to') }}">
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button id="reportsRefresh" type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    <button id="exportCsvBtn" type="button" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export Excel
                    </button>
                     <button id="exportPdfBtn" type="button" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                    <button id="printBtn" type="button" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </form>
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
                        <i class="fas fa-coins text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Payments Received</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($totalPaymentsReceived, 2) }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-wallet text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Revenue This Month</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($revenueThisMonth, 2) }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Average Revenue Per Reservation</p>
                        <p class="text-2xl font-bold text-gray-800">₱{{ number_format($averageRevenuePerReservation, 2) }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-calculator text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Recent Payment Transactions</h3>
                    <p class="text-sm text-gray-500">Latest completed reservations and revenue movement</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-4 py-3">Guest</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentPayments as $reservation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->guest_name }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->room ? $reservation->room->room_number : 'N/A' }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No payment transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method Breakdown</h3>
                <div class="relative h-[320px]">
                    <canvas id="paymentMethodChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Room Type</h3>
                <div class="relative h-[320px]">
                    <canvas id="roomTypeChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
            <div class="relative h-[340px]">
                <canvas id="monthlyRevenue" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <div data-panel="reservations" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Reservations</p>
                <h2 class="text-3xl font-bold">{{ $reservations->count() }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Confirmed Reservations</p>
                <h2 class="text-3xl font-bold text-blue-600">{{ count($confirmedReservations) }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Pending Reservations</p>
                <h2 class="text-3xl font-bold text-yellow-500">{{ count($pendingReservations) }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Cancelled Reservations</p>
                <h2 class="text-3xl font-bold text-red-500">{{ count($cancelledReservations) }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Trend</h3>
                <div class="relative h-[320px]">
                    <canvas id="reservationTrendChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Status Distribution</h3>
                <div class="flex items-center justify-center h-[320px]">
                    <canvas id="reservationStatusChart" class="max-w-[320px] max-h-[320px] w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Most Booked Room Types</h3>
            <div>
                <div class="w-[620px] h-[520px]">
                    <canvas id="roomTypeBookingChart" class="w-full h-full max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Recent Reservations</h3>
                    <p class="text-sm text-gray-500">Latest booking activity from the system</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-4 py-3">Guest</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Dates</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reservations->take(8) as $reservation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->guest_name }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->room ? $reservation->room->room_number : 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-900">
                                {{ optional($reservation->check_in)->format('M d') ?? 'N/A' }}
                                -
                                {{ optional($reservation->check_out)->format('M d') ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium text-white status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No reservations found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-panel="occupancy" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Occupancy Rate</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $occupancyRate }}%</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Occupied Rooms</p>
                <h2 class="text-3xl font-bold text-red-600">{{ $occupiedRooms }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Available Rooms</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $availableRooms }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Rooms Under Maintenance</p>
                <h2 class="text-3xl font-bold text-yellow-500">{{ $maintenanceRooms }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Occupancy Trend</h3>
                <div class="relative h-[320px]">
                    <canvas id="occupancyTrendChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Status Distribution</h3>
                <div class="flex items-center justify-center h-[320px]">
                    <canvas id="roomStatusChart" class="max-w-[320px] max-h-[320px] w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Current Room Status</h3>
                    <p class="text-sm text-gray-500">Live room availability snapshot</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Capacity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($rooms->take(10) as $room)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-900">{{ $room->room_number }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $room->room_type }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ ucfirst($room->status) }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $room->capacity }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No room data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div data-panel="guests" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Guests</p>
                <h2 class="text-3xl font-bold">{{ $totalGuests }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">New Guests</p>
                <h2 class="text-3xl font-bold text-blue-600">{{ $newGuests }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Returning Guests</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $returningGuests }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Average Length of Stay</p>
                <h2 class="text-3xl font-bold text-purple-600">{{ $averageStayDuration }} days</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guest Registration Trend</h3>
                <div class="relative h-[320px]">
                    <canvas id="guestRegistrationChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">New vs Returning Guests</h3>
                <div class="flex items-center justify-center h-[320px]">
                    <canvas id="guestTypeChart" class="max-w-[320px] max-h-[320px] w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Average Stay Duration</h3>
            <div class="relative h-[320px]">
                <canvas id="stayDurationChart" class="w-full h-full"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Recent Guest Activity</h3>
                    <p class="text-sm text-gray-500">Latest guest booking behavior</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <th class="px-4 py-3">Guest</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Room</th>
                            <th class="px-4 py-3">Stay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentGuestActivity as $reservation)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->guest_name }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->guest_email }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $reservation->room ? $reservation->room->room_number : 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-900">
                                @if($reservation->check_in && $reservation->check_out)
                                    {{ $reservation->check_in->diffInDays($reservation->check_out) }} days
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No guest activity found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
        // Actions: Refresh, Export CSV, Export PDF (print view), Print
        const refreshBtn = document.getElementById('reportsRefresh');
        const exportCsvBtn = document.getElementById('exportCsvBtn');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        const printBtn = document.getElementById('printBtn');

        function buildQuery() {
            const from = document.getElementById('reportFrom')?.value || '';
            const to = document.getElementById('reportTo')?.value || '';
            const params = new URLSearchParams();
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            return params.toString() ? ('?' + params.toString()) : '';
        }

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                document.getElementById('reportsFilterForm').submit();
            });
        }

        if (exportCsvBtn) {
            exportCsvBtn.addEventListener('click', function () {
                const q = buildQuery();
                const url = "{{ route('admin.reports.export.csv') }}" + q;
                window.location.href = url;
            });
        }

        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', function () {
                const q = buildQuery();
                const url = "{{ route('admin.reports.print') }}" + q;
                window.open(url, '_blank');
            });
        }

        if (printBtn) {
            printBtn.addEventListener('click', function () {
                const q = buildQuery();
                const url = "{{ route('admin.reports.print') }}" + q;
                window.open(url, '_blank');
            });
        }
    });

    const paymentCtx = document.getElementById('paymentMethodChart')?.getContext('2d');
    const paymentMethodLabels = @json($paymentMethodLabels);
    const paymentMethodData = @json($paymentMethodData);

    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentMethodLabels,
                datasets: [{
                    label: 'Payments',
                    data: paymentMethodData,
                    backgroundColor: ['#0741ff', '#03ff18', '#f65c97', '#f97316', '#ef4444'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const roomTypeCtx2 = document.getElementById('roomTypeChart')?.getContext('2d');
    const roomTypeRevenueLabels = @json($roomTypeRevenueLabels);
    const roomTypeRevenueData = @json($roomTypeRevenueData);

    if (roomTypeCtx2) {
        new Chart(roomTypeCtx2, {
            type: 'bar',
            data: {
                labels: roomTypeRevenueLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: roomTypeRevenueData,
                    backgroundColor: ['#8b5cf6', '#ec4899', '#f97316', '#06b6d4', '#14b8a6'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        stacked: false,
                        ticks: { autoSkip: false },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } }
                    }
                },
                datasets: {
                    bar: {
                        barThickness: 64,
                        maxBarThickness: 78,
                        categoryPercentage: 0.6,
                        barPercentage: 0.6
                    }
                }
            }
        });
    }

    const monthlyRevenueCtx = document.getElementById('monthlyRevenue')?.getContext('2d');
    const monthlyRevenueLabels = @json($monthlyLabels);
    const monthlyRevenueData = @json($monthlyRevenue);

    if (monthlyRevenueCtx) {
        new Chart(monthlyRevenueCtx, {
            type: 'line',
            data: {
                labels: monthlyRevenueLabels,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: monthlyRevenueData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249,115,22,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    const reservationTrendCtx = document.getElementById('reservationTrendChart')?.getContext('2d');
    const reservationTrendLabels = @json($reservationTrendLabels);
    const reservationTrendData = @json($reservationTrendData);

    if (reservationTrendCtx) {
        new Chart(reservationTrendCtx, {
            type: 'line',
            data: {
                labels: reservationTrendLabels,
                datasets: [{
                    label: 'Reservations',
                    data: reservationTrendData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    }

    const reservationStatusCtx = document.getElementById('reservationStatusChart')?.getContext('2d');
    const reservationStatusLabels = @json($reservationStatusLabels);
    const reservationStatusData = @json($reservationStatusData);

    if (reservationStatusCtx) {
        new Chart(reservationStatusCtx, {
            type: 'doughnut',
            data: {
                labels: reservationStatusLabels,
                datasets: [{
                    data: reservationStatusData,
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', align: 'center' }
                }
            }
        });
    }

    const roomTypeBookingCtx = document.getElementById('roomTypeBookingChart')?.getContext('2d');
    const mostBookedRoomTypeLabels = @json($mostBookedRoomTypeLabels);
    const mostBookedRoomTypeData = @json($mostBookedRoomTypeData);

    if (roomTypeBookingCtx) {
        new Chart(roomTypeBookingCtx, {
            type: 'bar',
            data: {
                labels: mostBookedRoomTypeLabels,
                datasets: [{
                    label: 'Bookings',
                    data: mostBookedRoomTypeData,
                    backgroundColor: ['#8b5cf6', '#ec4899', '#f97316', '#06b6d4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: false }
                    },
                    y: { beginAtZero: true }
                },
                datasets: {
                    bar: {
                        barThickness: 64,
                        maxBarThickness: 78,
                        categoryPercentage: 0.55,
                        barPercentage: 0.55
                    }
                }
            }
        });
    }

    const occupancyTrendCtx = document.getElementById('occupancyTrendChart')?.getContext('2d');
    const occupancyTrendLabels = @json($occupancyTrendLabels);
    const occupancyTrendData = @json($occupancyTrendData);

    if (occupancyTrendCtx) {
        new Chart(occupancyTrendCtx, {
            type: 'line',
            data: {
                labels: occupancyTrendLabels,
                datasets: [{
                    label: 'Occupied Bookings',
                    data: occupancyTrendData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    }

    const roomStatusCtx = document.getElementById('roomStatusChart')?.getContext('2d');
    const roomStatusLabels = @json($roomStatusLabels);
    const roomStatusData = @json($roomStatusData);

    if (roomStatusCtx) {
        new Chart(roomStatusCtx, {
            type: 'doughnut',
            data: {
                labels: roomStatusLabels,
                datasets: [{
                    data: roomStatusData,
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', align: 'center' }
                }
            }
        });
    }

    const guestRegistrationCtx = document.getElementById('guestRegistrationChart')?.getContext('2d');
    const guestRegistrationLabels = @json($reservationTrendLabels);
    const guestRegistrationData = @json($reservationTrendData);

    if (guestRegistrationCtx) {
        new Chart(guestRegistrationCtx, {
            type: 'line',
            data: {
                labels: guestRegistrationLabels,
                datasets: [{
                    label: 'Guest Registrations',
                    data: guestRegistrationData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true }
        });
    }

    const guestTypeCtx = document.getElementById('guestTypeChart')?.getContext('2d');
    const guestTypeLabels = ['New Guests', 'Returning Guests'];
    const guestTypeDataRaw = [@json($newGuests), @json($returningGuests)];
    const guestTypeData = guestTypeDataRaw.map(v => (v === null || typeof v === 'undefined') ? 0 : Number(v));
    const guestTypeSum = guestTypeData.reduce((a, b) => a + b, 0);

    if (guestTypeCtx) {
        const dataToUse = guestTypeSum === 0 ? [1, 1] : guestTypeData;
        new Chart(guestTypeCtx, {
            type: 'doughnut',
            data: {
                labels: guestTypeLabels,
                datasets: [{
                    data: dataToUse,
                    backgroundColor: ['#3b82f6', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', align: 'center' } }
            }
        });
    }

    const stayDurationCtx = document.getElementById('stayDurationChart')?.getContext('2d');
    const stayDurationLabels = @json($reservationTrendLabels);
    const stayDurationData = @json($reservationTrendData);

    if (stayDurationCtx) {
        new Chart(stayDurationCtx, {
            type: 'bar',
            data: {
                labels: stayDurationLabels,
                datasets: [{
                    label: 'Average Stay Days',
                    data: stayDurationData,
                    backgroundColor: ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#06b6d4']
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }

</script>
@endsection
