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

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
                    <p class="text-sm text-gray-500">Latest guest activity and payment movement</p>
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
                        @forelse($reservations->take(8) as $reservation)
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
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No transactions found.</td>
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

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Reservation Status</h3>
                    <p class="text-sm text-gray-500">Snapshot of booking progress</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                    <p class="text-sm font-medium text-yellow-700">Pending</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-800">12</p>
                    <p class="mt-1 text-xs text-yellow-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-medium text-blue-700">Confirmed</p>
                    <p class="mt-2 text-2xl font-bold text-blue-800">18</p>
                    <p class="mt-1 text-xs text-blue-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-700">Completed</p>
                    <p class="mt-2 text-2xl font-bold text-green-800">24</p>
                    <p class="mt-1 text-xs text-green-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-700">Cancelled</p>
                    <p class="mt-2 text-2xl font-bold text-red-800">6</p>
                    <p class="mt-1 text-xs text-red-600">Sample dummy data</p>
                </div>
            </div>
        </div>
    </div>

    <div data-panel="occupancy" class="hidden space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800">Room Occupancy</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="text-center p-4 rounded-lg bg-green-50 border border-green-100">
                    <h2 class="text-4xl font-bold text-green-600">{{ $availableRooms }}</h2>
                    <p class="mt-2 text-gray-600">Available</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-red-50 border border-red-100">
                    <h2 class="text-4xl font-bold text-red-600">{{ $occupiedRooms }}</h2>
                    <p class="mt-2 text-gray-600">Occupied</p>
                </div>
                <div class="text-center p-4 rounded-lg bg-yellow-50 border border-yellow-100">
                    <h2 class="text-4xl font-bold text-yellow-500">{{ $maintenanceRooms }}</h2>
                    <p class="mt-2 text-gray-600">Maintenance</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Housekeeping Report</h3>
                    <p class="text-sm text-gray-500">Daily service snapshot and support needs</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-lg border border-cyan-200 bg-cyan-50 p-4">
                    <p class="text-sm font-medium text-cyan-700">Rooms Cleaned</p>
                    <p class="mt-2 text-2xl font-bold text-cyan-800">28</p>
                    <p class="mt-1 text-xs text-cyan-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-4">
                    <p class="text-sm font-medium text-indigo-700">Rooms Inspected</p>
                    <p class="mt-2 text-2xl font-bold text-indigo-800">19</p>
                    <p class="mt-1 text-xs text-indigo-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-medium text-emerald-700">Maintenance Requests</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-800">4</p>
                    <p class="mt-1 text-xs text-emerald-600">Sample dummy data</p>
                </div>
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-medium text-amber-700">Inventory Low</p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">2</p>
                    <p class="mt-1 text-xs text-amber-600">Sample dummy data</p>
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
    const monthlyRevenueLabels = @json($monthlyLabels);
    const monthlyRevenueData = @json($monthlyRevenue);

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

</script>
@endsection
