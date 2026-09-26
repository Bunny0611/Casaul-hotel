@extends('admin.layout')

@section('content')
<style>
    .report-export-document {
        box-sizing: border-box;
        width: 1400px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px;
        color: #1f2937;
        background: #ffffff;
        font-family: Arial, sans-serif;
    }

    .report-export-document.report-print-target {
        display: none;
    }

    .report-export-document .report-export-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
    }

    .report-export-document .report-export-period {
        margin-top: 6px;
        color: #6b7280;
        font-size: 13px;
    }

    .report-export-document [data-panel] {
        display: block !important;
    }

    .report-export-document [data-panel] > * {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .report-export-document .report-export-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 24px;
    }

    .report-export-document .report-export-two-column-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 24px;
    }

    .report-export-document .report-export-three-column-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 24px;
    }

    .report-export-document canvas,
    .report-export-document .report-export-chart {
        display: block;
        width: auto;
        height: auto !important;
        max-width: 100%;
        max-height: 100%;
        margin-left: auto;
        margin-right: auto;
    }

    .report-export-document .shadow-lg,
    .report-export-document .shadow-sm {
        box-shadow: none !important;
    }

    .report-export-document [class~="h-[240px]"] {
        height: 280px !important;
    }

    .report-export-document [class~="h-[280px]"] {
        height: 320px !important;
    }

    .report-export-document [class~="h-[320px]"] {
        height: 360px !important;
    }

    .report-export-document [class~="h-[360px]"] {
        height: 400px !important;
    }

    @media print {
        @page {
            size: 13in 8in;
            margin: 12mm;
        }

        body {
            background: #ffffff !important;
        }

        body.report-printing > *:not(.report-export-document) {
            display: none !important;
        }

        .report-export-document.report-print-target {
            display: block !important;
        }

        .report-export-document {
            width: 100%;
            max-width: none;
        }

        .report-export-document .report-export-chart-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }

        .report-export-page-break {
            break-before: page;
            page-break-before: always;
        }
</style>
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
                    <button id="downloadPdfBtn" type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Download PDF
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
        <div class="grid grid-cols-2 md:grid-cols-5 text-center">
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

            <button type="button" data-tab="maintenance" class="tab-btn py-4 text-gray-600 transition-all duration-200">
                <i class="fas fa-tools mr-2"></i>
                Maintenance
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
                <div class="relative h-[240px]">
                    <canvas id="monthlyRevenue" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Category</h3>
                <div class="relative h-[240px]">
                    <canvas id="revenueByCategoryChart" class="w-full h-full"></canvas>
                </div>
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
                <div class="relative h-[240px]">
                    <canvas id="reservationTrendChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Status Distribution</h3>
                <div class="flex items-center justify-center h-[240px]">
                    <canvas id="reservationStatusChart" class="max-w-[240px] max-h-[240px] w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Most Booked Room Types</h3>
                <div class="relative h-[360px]">
                    <canvas id="roomTypeBookingChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation by Number of Guests</h3>
                <div class="relative h-[360px]">
                    <canvas id="reservationGuestChart" class="w-full h-full"></canvas>
                </div>
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
                <div class="relative h-[240px]">
                    <canvas id="occupancyTrendChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Status Distribution</h3>
                <div class="flex items-center justify-center h-[240px]">
                    <canvas id="roomStatusChart" class="max-w-[240px] max-h-[240px] w-full h-full"></canvas>
                </div>
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
                <div class="relative h-[240px]">
                    <canvas id="guestRegistrationChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">New vs Returning Guests</h3>
                <div class="flex items-center justify-center h-[240px]">
                    <canvas id="guestTypeChart" class="max-w-[320px] max-h-[320px] w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Guests by Origin</h3>
                <div class="relative h-[240px]">
                    <canvas id="guestOriginChart" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Average Stay Duration</h3>
                <div class="relative h-[240px]">
                    <canvas id="stayDurationChart" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div data-panel="maintenance" class="hidden space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Total Reports</p>
                <h2 class="text-3xl font-bold text-gray-800">{{ $maintenanceReports->count() }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Pending Issues</p>
                <h2 class="text-3xl font-bold text-yellow-500">{{ $maintenancePending }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Under Repair</p>
                <h2 class="text-3xl font-bold text-orange-500">{{ $maintenanceRepairing }}</h2>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Completed</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $maintenanceCompleted }}</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"><h3 class="text-lg font-semibold text-gray-800 mb-4">Report Status</h3><div class="relative h-[280px]"><canvas id="maintenanceStatusChart" class="w-full h-full"></canvas></div></div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"><h3 class="text-lg font-semibold text-gray-800 mb-4">Priority Breakdown</h3><div class="relative h-[280px]"><canvas id="maintenancePriorityChart" class="w-full h-full"></canvas></div></div>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"><h3 class="text-lg font-semibold text-gray-800 mb-4">Issues by Category</h3><div class="relative h-[280px]"><canvas id="maintenanceCategoryChart" class="w-full h-full"></canvas></div></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Maintenance Reports</h3>
                    <p class="mt-1 text-sm text-gray-500">Review and manage reported room issues.</p>
                </div>
                <span class="rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700">{{ $maintenanceReports->count() }} reports</span>
            </div>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-4 py-3 font-semibold">Room</th><th class="px-4 py-3 font-semibold">Issue</th><th class="px-4 py-3 font-semibold">Priority</th><th class="px-4 py-3 font-semibold">Reported By</th><th class="px-4 py-3 font-semibold">Date &amp; Time</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 font-semibold">Action</th></tr></thead>
                <tbody>
                    @forelse($maintenanceReports as $report)
                        <tr class="border-b border-gray-100 align-top"><td class="py-3 pr-4 font-medium">{{ $report->room_number }}</td><td class="py-3 pr-4">{{ $report->problem ?: $report->category }}</td><td class="py-3 pr-4">{{ $report->priority }}</td><td class="py-3 pr-4">{{ $report->reported_by }}</td><td class="whitespace-nowrap py-3 pr-4">{{ optional($report->date_reported)->format('d/m/Y h:i A') }}</td><td class="py-3 pr-4">{{ $report->status }}</td><td class="py-3"><details class="group"><summary class="cursor-pointer font-medium text-blue-600 hover:text-blue-800">View</summary><div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" onclick="if (event.target === this) this.querySelector('button[aria-label=\'Close report\']').click()"><div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl" onclick="event.stopPropagation()"><div class="flex items-start justify-between bg-[#800000] px-6 py-5 text-white"><div><p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-200">Admin review</p><h4 class="mt-1 text-xl font-bold">Maintenance Report</h4></div><button type="button" class="rounded-lg p-2 text-2xl leading-none text-white/80 transition hover:bg-white/15 hover:text-white" aria-label="Close report" onclick="this.closest('details').removeAttribute('open')">&times;</button></div><div class="space-y-6 p-6 text-gray-700"><div class="grid grid-cols-1 gap-5 sm:grid-cols-2"><div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Room Number</p><p class="mt-1 text-lg font-bold text-gray-900">{{ $report->room_number }}</p></div><div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Issue Category</p><p class="mt-1 font-semibold text-gray-900">{{ $report->category }}</p></div><div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Priority</p><p class="mt-1 font-semibold text-gray-900">{{ $report->priority }}</p></div><div><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Reported By</p><p class="mt-1 font-semibold text-gray-900">{{ $report->reported_by }}</p></div><div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Problem Description</p><p class="mt-1 rounded-lg bg-gray-50 p-3 leading-relaxed text-gray-800">{{ $report->description }}</p></div><div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Date &amp; Time Reported</p><p class="mt-1 font-semibold text-gray-900">{{ optional($report->date_reported)->format('d / m / Y   h : i A') }}</p></div><div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Photo / Evidence</p>@if($report->photo_path)<div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 p-2"><img src="{{ asset('storage/' . $report->photo_path) }}" alt="Maintenance evidence for room {{ $report->room_number }}" class="max-h-64 w-full rounded-lg object-contain"> </div>@else<p class="mt-2 rounded-lg bg-gray-50 p-3 font-medium text-gray-500">No photo uploaded</p>@endif</div><div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Status</p><p class="mt-1 inline-flex rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-800">{{ $report->status }}</p></div></div><div class="flex flex-wrap gap-3 border-t border-gray-200 pt-5">@if($report->status !== 'In Progress' && $report->status !== 'Completed')<form method="POST" action="{{ route('admin.maintenance-reports.status', $report) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="In Progress"><button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 font-medium text-white shadow-sm transition hover:bg-blue-700">Mark as In Progress</button></form>@endif @if($report->status !== 'Completed')<form method="POST" action="{{ route('admin.maintenance-reports.status', $report) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Completed"><button type="submit" class="rounded-lg bg-green-600 px-4 py-2.5 font-medium text-white shadow-sm transition hover:bg-green-700">Mark as Resolved</button></form>@endif</div></div></div></div></details></td></tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-500">No maintenance reports found for this date range.</td></tr>
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
        let activeTab = document.querySelector('.tab-btn.border-orange-500')?.getAttribute('data-tab') || 'financial';

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                const target = this.getAttribute('data-tab');
                activeTab = target;

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
        // Actions: Refresh, Export Excel, Download PDF, Print
        const refreshBtn = document.getElementById('reportsRefresh');
        const exportCsvBtn = document.getElementById('exportCsvBtn');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        const printBtn = document.getElementById('printBtn');

        const reportTitles = {
            financial: 'Financial Report',
            reservations: 'Reservations Report',
            occupancy: 'Occupancy Report',
            guests: 'Guests Report',
            maintenance: 'Maintenance Report'
        };

        function getReportingPeriod() {
            const from = document.getElementById('reportFrom')?.value || 'All';
            const to = document.getElementById('reportTo')?.value || 'All';
            return `From: ${from}  |  To: ${to}`;
        }

        function prepareExportDocument() {
            const sourcePanel = document.querySelector(`[data-panel="${activeTab}"]`);
            if (!sourcePanel) return null;

            const documentRoot = document.createElement('div');
            documentRoot.className = 'report-export-document';
            documentRoot.innerHTML = `
                <header class="report-export-header">
                    <h1>${reportTitles[activeTab] || 'Hotel Report'}</h1>
                    <p class="report-export-period">${getReportingPeriod()}</p>
                </header>
            `;

            const panelClone = sourcePanel.cloneNode(true);
            panelClone.classList.remove('hidden');
            panelClone.querySelectorAll('details:not([open])').forEach(details => details.remove());
            panelClone.querySelectorAll('button, input, select, textarea, form').forEach(control => control.remove());

            panelClone.querySelectorAll(':scope > .grid').forEach((grid, index) => {
                if (index === 0) {
                    grid.classList.add('report-export-kpi-grid');
                } else if (grid.classList.contains('lg:grid-cols-3')) {
                    grid.classList.add('report-export-three-column-grid');
                } else {
                    grid.classList.add('report-export-two-column-grid');
                }

                if (index >= 2) grid.classList.add('report-export-page-break');
            });

            sourcePanel.querySelectorAll('canvas').forEach(sourceCanvas => {
                const chart = window.Chart?.getChart(sourceCanvas);
                if (chart) chart.resize();

                const clonedCanvas = panelClone.querySelector(`#${sourceCanvas.id}`);
                if (!clonedCanvas) return;

                const chartImage = document.createElement('img');
                chartImage.className = `${sourceCanvas.className} report-export-chart`;
                chartImage.alt = '';
                chartImage.width = sourceCanvas.width;
                chartImage.height = sourceCanvas.height;
                chartImage.src = sourceCanvas.toDataURL('image/png', 1);
                clonedCanvas.replaceWith(chartImage);

                const chartCard = chartImage.closest('.bg-white');
                chartCard?.classList.add('report-export-chart-card');
            });

            documentRoot.appendChild(panelClone);
            return documentRoot;
        }

        function loadPdfLibraries() {
            const loadScript = (src, ready) => new Promise((resolve, reject) => {
                if (ready()) return resolve();
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });

            return Promise.all([
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', () => !!window.html2canvas),
                loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', () => !!window.jspdf?.jsPDF)
            ]);
        }

        function printCurrentReport(reportDocument) {
            reportDocument.classList.add('report-print-target');
            document.body.classList.add('report-printing');
            document.body.appendChild(reportDocument);

            const cleanup = () => {
                reportDocument.remove();
                document.body.classList.remove('report-printing');
            };

            window.addEventListener('afterprint', cleanup, { once: true });
            window.print();
        }

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

        if (downloadPdfBtn) {
            downloadPdfBtn.addEventListener('click', async function () {
                const reportDocument = prepareExportDocument();
                if (!reportDocument) return;

                downloadPdfBtn.disabled = true;
                reportDocument.style.position = 'absolute';
                reportDocument.style.top = '0';
                reportDocument.style.left = '0';
                reportDocument.style.opacity = '0';
                reportDocument.style.pointerEvents = 'none';
                reportDocument.id = 'reportPdfRenderTarget';
                document.body.appendChild(reportDocument);

                try {
                    await loadPdfLibraries();
                    const canvas = await window.html2canvas(reportDocument, {
                        scale: 2,
                        backgroundColor: '#ffffff',
                        useCORS: true,
                        logging: false,
                        onclone: clonedDocument => {
                            const clonedReport = clonedDocument.getElementById('reportPdfRenderTarget');
                            if (clonedReport) clonedReport.style.opacity = '1';
                        }
                    });
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({ unit: 'mm', format: [330.2, 203.2], orientation: 'landscape' });
                    const margin = 12;
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();
                    const contentWidth = pageWidth - (margin * 2);
                    const contentHeight = pageHeight - (margin * 2);
                    const rootRect = reportDocument.getBoundingClientRect();
                    const canvasScale = canvas.width / rootRect.width;
                    const maximumPageHeight = (contentHeight / contentWidth) * canvas.width;
                    const cssBreakpoints = Array.from(reportDocument.querySelectorAll('.report-export-page-break'))
                        .map(element => (element.getBoundingClientRect().top - rootRect.top) * canvasScale)
                        .filter(breakpoint => breakpoint > 0 && breakpoint < canvas.height);
                    const pageBreakpoints = [...new Set([0, ...cssBreakpoints, canvas.height])].sort((a, b) => a - b);

                    for (let breakpointIndex = 0; breakpointIndex < pageBreakpoints.length - 1; breakpointIndex += 1) {
                        let sourceY = pageBreakpoints[breakpointIndex];
                        const sectionEnd = pageBreakpoints[breakpointIndex + 1];

                        while (sourceY < sectionEnd) {
                            const sourceHeight = Math.min(maximumPageHeight, sectionEnd - sourceY);
                        const pageCanvas = document.createElement('canvas');
                        pageCanvas.width = canvas.width;
                        pageCanvas.height = sourceHeight;
                        pageCanvas.getContext('2d').drawImage(
                            canvas, 0, sourceY, canvas.width, sourceHeight,
                            0, 0, pageCanvas.width, pageCanvas.height
                        );

                        if (sourceY > 0) pdf.addPage();
                        const renderedHeight = (sourceHeight * contentWidth) / canvas.width;
                        pdf.addImage(pageCanvas, 'PNG', margin, margin, contentWidth, renderedHeight);
                        sourceY += sourceHeight;
                        }
                    }

                    const tabName = (reportTitles[activeTab] || 'Hotel Report').replace(/\s+Report$/, '');
                    const filename = `${tabName.replace(/[^a-z0-9]+/gi, '_')}_Report.pdf`;
                    pdf.save(filename);
                } finally {
                    reportDocument.remove();
                    downloadPdfBtn.disabled = false;
                }
            });
        }

        if (printBtn) {
            printBtn.addEventListener('click', function () {
                const reportDocument = prepareExportDocument();
                if (reportDocument) printCurrentReport(reportDocument);
            });
        }
    });

    const maintenanceStatusCtx = document.getElementById('maintenanceStatusChart')?.getContext('2d');
    const maintenancePriorityCtx = document.getElementById('maintenancePriorityChart')?.getContext('2d');
    const maintenanceCategoryCtx = document.getElementById('maintenanceCategoryChart')?.getContext('2d');
    const maintenanceStatusLabels = @json($maintenanceStatusLabels);
    const maintenanceStatusData = @json($maintenanceStatusData);
    const maintenancePriorityLabels = @json($maintenancePriorityLabels);
    const maintenancePriorityData = @json($maintenancePriorityData);
    const maintenanceCategoryLabels = @json($maintenanceCategoryLabels);
    const maintenanceCategoryCounts = @json($maintenanceCategoryCounts);

    if (maintenanceStatusCtx) {
        new Chart(maintenanceStatusCtx, {
            type: 'doughnut',
            data: { labels: maintenanceStatusLabels, datasets: [{ data: maintenanceStatusData, backgroundColor: ['#f59e0b', '#f97316', '#3b82f6', '#10b981'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    if (maintenancePriorityCtx) {
        new Chart(maintenancePriorityCtx, {
            type: 'bar',
            data: { labels: maintenancePriorityLabels, datasets: [{ label: 'Reports', data: maintenancePriorityData, backgroundColor: ['#22c55e', '#eab308', '#f97316', '#dc2626'], borderColor: ['#15803d', '#a16207', '#c2410c', '#b91c1c'], borderWidth: 1 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } }
        });
    }

    if (maintenanceCategoryCtx) {
        new Chart(maintenanceCategoryCtx, {
            type: 'bar',
            data: { labels: maintenanceCategoryLabels, datasets: [{ label: 'Reports', data: maintenanceCategoryCounts, backgroundColor: ['#3b82f6', '#14b8a6', '#f97316', '#eab308', '#8b5cf6', '#ef4444'], borderColor: ['#2563eb', '#0f766e', '#c2410c', '#a16207', '#6d28d9', '#b91c1c'], borderWidth: 1 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } }
        });
    }

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

    const revenueByCategoryCtx = document.getElementById('revenueByCategoryChart')?.getContext('2d');
    const revenueByCategoryLabels = @json($revenueByCategoryLabels);
    const revenueByCategoryData = @json($revenueByCategoryData);

    if (revenueByCategoryCtx) {
        new Chart(revenueByCategoryCtx, {
            type: 'bar',
            data: {
                labels: revenueByCategoryLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: revenueByCategoryData,
                    backgroundColor: ['#f97316', '#3b82f6', '#14b8a6', '#8b5cf6'],
                    borderWidth: 0,
                    borderRadius: 5,
                    barThickness: 28
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: { color: '#e5e7eb' }
                    },
                    y: {
                        grid: { display: false }
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

    const reservationGuestCtx = document.getElementById('reservationGuestChart')?.getContext('2d');
    const reservationGuestLabels = @json($reservationGuestLabels);
    const reservationGuestData = @json($reservationGuestData);

    if (reservationGuestCtx) {
        new Chart(reservationGuestCtx, {
            type: 'bar',
            data: {
                labels: reservationGuestLabels,
                datasets: [{
                    label: 'Reservations',
                    data: reservationGuestData,
                    backgroundColor: ['#f97316', '#3b82f6', '#14b8a6', '#8b5cf6', '#ec4899'],
                    borderWidth: 0,
                    borderRadius: 5,
                    barThickness: 32
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
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

    const guestOriginCtx = document.getElementById('guestOriginChart')?.getContext('2d');
    const guestOriginLabels = @json($guestOriginLabels);
    const guestOriginData = @json($guestOriginData).map(value => Number(value) || 0);

    if (guestOriginCtx) {
        new Chart(guestOriginCtx, {
            type: 'bar',
            data: {
                labels: guestOriginLabels,
                datasets: [{
                    label: 'Guests',
                    data: guestOriginData,
                    backgroundColor: ['#8f0e16', '#d97706', '#2563eb', '#059669', '#7c3aed'],
                    borderRadius: 4,
                    barThickness: 24
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    const stayDurationCtx = document.getElementById('stayDurationChart')?.getContext('2d');
    const stayDurationLabels = @json($stayDurationTrendLabels);
    const stayDurationData = @json($stayDurationTrendData);

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
