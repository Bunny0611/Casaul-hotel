@extends('employee.layout')

@section('pageTitle', 'Check-in / Check-out Management')

@section('content')
<style>
    .soft-card {
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.35rem 0.7rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .truncate-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .modal-panel {
        width: min(100%, 34rem);
        border-radius: 1.75rem;
        background: #ffffff;
        padding: 1.75rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
    }

    .modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .checkout-actions button {
        min-width: 140px;
        border-radius: 999px;
        padding: 0.95rem 1.25rem;
        font-weight: 700;
    }

    .checkout-confirm-btn {
        background: #dc2626;
        color: #ffffff;
    }

    .checkin-confirm-btn {
        background: #10b981;
        color: #ffffff;
        border: none;
        border-radius: 999px;
        padding: 0.95rem 1.25rem;
        font-weight: 700;
    }

    .checkout-cancel-btn {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
    }

    .check-form-grid {
        display: grid;
        gap: 1.5rem;
        margin-bottom: 1.75rem;
    }

    .check-form-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        background: #ffffff;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        max-height: 620px;
        min-height: 0;
    }

    .check-form-body {
        overflow-y: auto;
        max-height: 460px;
        flex: 1;
        min-height: 0;
        padding-right: 4px;
        margin-bottom: 0.75rem;
    }

    .check-form-body::-webkit-scrollbar {
        width: 7px;
    }

    .check-form-body::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.3);
        border-radius: 999px;
    }

    .check-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .modal-title {
        margin: 0;
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }

    .modal-subtitle {
        margin-top: 0.35rem;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .modal-details {
        display: grid;
        gap: 0.15rem;
        margin: 1.25rem 0 1.5rem;
        padding: 0.35rem 0;
    }

    .modal-detail-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.7rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-detail-row:last-child {
        border-bottom: 0;
    }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.2rem;
        line-height: 1;
    }

    .modal-close-btn:hover {
        color: #475569;
    }

    .check-form-input,
    .check-form-select {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        padding: 0.95rem 1rem;
        font-size: 0.95rem;
        outline: none;
    }

    .check-form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #334155 50%), linear-gradient(135deg, #334155 50%, transparent 50%);
        background-position: calc(100% - 16px) center, calc(100% - 12px) center;
        background-size: 8px 8px;
        background-repeat: no-repeat;
        cursor: pointer;
        padding-right: 2.5rem;
    }

    .check-form-input[type="date"],
    .check-form-input[type="time"] {
        background: #ffffff;
    }

    .check-form-input:focus,
    .check-form-select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
    }

    .check-form-footer {
        margin-top: 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .check-form-title-group {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .check-form-icon {
        width: 44px;
        height: 44px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #d1fae5;
        color: #047857;
        font-size: 1rem;
    }

    .check-form-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        background: #ffffff;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        max-height: 680px;
    }

    .check-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .modal-detail-label {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }

    .modal-detail-value {
        font-size: 0.95rem;
        color: #334155;
        font-weight: 600;
        text-align: right;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 900px) {
        .check-form-fields {
            grid-template-columns: 1fr;
        }
        .full-width-field {
            grid-column: span 1;
        }
    }

    @media (max-width: 520px) {
        .modal-panel {
            padding: 1.25rem;
            border-radius: 1.25rem;
        }

        .modal-title {
            font-size: 1.2rem;
        }

        .modal-detail-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 0.2rem;
        }

        .modal-detail-value {
            text-align: left;
        }

        .modal-actions {
            flex-direction: column-reverse;
        }

        .modal-btn {
            width: 100%;
        }
    }

    .modal-btn {
        border-radius: 999px;
        min-width: 116px;
        padding: 0.8rem 1.2rem;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .modal-btn-secondary {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #334155;
    }

    .modal-btn-secondary:hover {
        background: #f8fafc;
    }

    .modal-btn-primary {
        background: #10b981;
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .check-form-fields {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">    
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="mt-1 text-sm text-gray-500">Hotel front desk operations</p>
                <h3 class="text-2xl font-semibold">Check-in / Check-out Management</h3>
                <p class="mt-1 text-sm text-gray-500">Review arrivals, departures, and room readiness in one streamlined view.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-ins</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">18</h4>
                </div>
                <div class="stat-icon bg-sky-100 text-sky-700"><i class="fas fa-sign-in-alt"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-outs</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">12</h4>
                </div>
                <div class="stat-icon bg-emerald-100 text-emerald-700"><i class="fas fa-sign-out-alt"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Occupied Rooms</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">24</h4>
                </div>
                <div class="stat-icon bg-violet-100 text-violet-700"><i class="fas fa-bed"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Available Rooms</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">8</h4>
                </div>
                <div class="stat-icon bg-amber-100 text-amber-700"><i class="fas fa-door-open"></i></div>
            </div>
        </div>
    </div>


    <div class="grid gap-6 xl:grid-cols-2">
        <div class="soft-card p-6">
            <h4 class="mb-4 font-semibold text-slate-800">TODAY'S CHECK-INS</h4>
            <div class="overflow-hidden">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-1/4" />
                        <col class="w-2/4" />
                        <col class="w-1/4" />
                    </colgroup>
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100" data-reservation="BK1001" data-guest="Juan Dela Cruz" data-date="2026-07-31" data-time="2:00 PM" data-room="Deluxe 201" data-status="Confirmed" data-balance="₱0" data-payment="Paid">
                            <td class="py-3 pr-3 whitespace-nowrap">BK1001</td>
                            <td class="py-3 pr-3 truncate-cell" title="Juan Dela Cruz">Juan Dela Cruz</td>
                            <td class="py-3 whitespace-nowrap"><button type="button" class="text-sm font-semibold text-emerald-600" onclick="openCheckInModal('BK1001', 'Juan Dela Cruz', 'Deluxe 201', 'July 31', '2:00 PM')">Check In</button></td>
                        </tr>
                        <tr class="border-b border-slate-100" data-reservation="BK1002" data-guest="Ana Reyes" data-date="2026-07-31" data-time="3:00 PM" data-room="Standard 105" data-status="Checked In" data-balance="₱0" data-payment="Partial">
                            <td class="py-3 pr-3 whitespace-nowrap">BK1002</td>
                            <td class="py-3 pr-3 truncate-cell" title="Ana Reyes">Ana Reyes</td>
                            <td class="py-3 whitespace-nowrap"><button type="button" class="text-sm font-semibold text-emerald-600" onclick="openCheckInModal('BK1002', 'Ana Reyes', 'Standard 105', 'July 31', '3:00 PM')">Check In</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="soft-card p-6">
            <h4 class="mb-4 font-semibold text-slate-800">TODAY'S CHECK-OUTS</h4>
            <div class="overflow-hidden">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-1/4" />
                        <col class="w-2/4" />
                        <col class="w-1/4" />
                    </colgroup>
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100" data-reservation="BK1003" data-guest="Mark Villanueva" data-date="2026-07-31" data-time="12:00 PM" data-room="Suite 301" data-status="Checked Out" data-balance="₱0" data-payment="Paid">
                            <td class="py-3 pr-3 whitespace-nowrap">BK1003</td>
                            <td class="py-3 pr-3 truncate-cell" title="Mark Villanueva">Mark Villanueva</td>
                            <td class="py-3 whitespace-nowrap"><button type="button" class="text-sm font-semibold text-rose-600" onclick="openCheckOutModal('BK1003', 'Mark Villanueva', 'Suite 301', 'July 31', '12:00 PM', '₱0')">Check Out</button></td>
                        </tr>
                        <tr class="border-b border-slate-100" data-reservation="BK1004" data-guest="Rose Dizon" data-date="2026-07-31" data-time="11:00 AM" data-room="Deluxe 102" data-status="Checked Out" data-balance="₱500" data-payment="Pending">
                            <td class="py-3 pr-3 whitespace-nowrap">BK1004</td>
                            <td class="py-3 pr-3 truncate-cell" title="Rose Dizon">Rose Dizon</td>
                            <td class="py-3 whitespace-nowrap"><button type="button" class="text-sm font-semibold text-rose-600" onclick="openCheckOutModal('BK1004', 'Rose Dizon', 'Deluxe 102', 'July 31', '11:00 AM', '₱500')">Check Out</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="checkInModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="modal-panel w-full max-w-lg">
        <div class="modal-header">
            <div>
                <h4 class="modal-title">Confirm Check-in</h4>
                <p class="modal-subtitle">Review guest details before proceeding.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('checkInModal')"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-details">
            <div class="modal-detail-row">
                <span class="modal-detail-label">Reservation ID:</span>
                <span class="modal-detail-value" id="checkInReservation"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Guest:</span>
                <span class="modal-detail-value" id="checkInGuest"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Room:</span>
                <span class="modal-detail-value" id="checkInRoom"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-in Date:</span>
                <span class="modal-detail-value" id="checkInDate"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Time:</span>
                <span class="modal-detail-value" id="checkInTime"></span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('checkInModal')">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary">Confirm Check-in</button>
        </div>
    </div>
</div>

<div id="checkOutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="modal-panel w-full max-w-lg">
        <div class="modal-header">
            <div>
                <h4 class="modal-title">Confirm Check-out</h4>
                <p class="modal-subtitle">Review departure details before completing checkout.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('checkOutModal')"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-details">
            <div class="modal-detail-row">
                <span class="modal-detail-label">Reservation ID:</span>
                <span class="modal-detail-value" id="checkOutReservation"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Guest:</span>
                <span class="modal-detail-value" id="checkOutGuest"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Room:</span>
                <span class="modal-detail-value" id="checkOutRoom"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-out:</span>
                <span class="modal-detail-value" id="checkOutDate"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Balance Due:</span>
                <span class="modal-detail-value text-rose-600" id="checkOutBalance"></span>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('checkOutModal')">Cancel</button>
            <button type="button" class="modal-btn modal-btn-primary">Confirm Check-out</button>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const reservation = document.getElementById('searchReservation')?.value.toLowerCase() || '';
        const guest = document.getElementById('searchGuest')?.value.toLowerCase() || '';
        const date = document.getElementById('filterDate')?.value;
        const status = document.getElementById('filterStatus')?.value.toLowerCase() || '';

        document.querySelectorAll('tbody tr').forEach((row) => {
            const rowReservation = row.getAttribute('data-reservation')?.toLowerCase() || '';
            const rowGuest = row.getAttribute('data-guest')?.toLowerCase() || '';
            const rowDate = row.getAttribute('data-date');
            const rowStatus = row.getAttribute('data-status')?.toLowerCase() || '';

            const matchesReservation = rowReservation.includes(reservation);
            const matchesGuest = rowGuest.includes(guest);
            const matchesDate = !date || rowDate === date;
            const matchesStatus = !status || rowStatus === status;

            row.style.display = matchesReservation && matchesGuest && matchesDate && matchesStatus ? '' : 'none';
        });
    }

    ['searchReservation', 'searchGuest', 'filterDate', 'filterStatus'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', applyFilters);
        document.getElementById(id)?.addEventListener('change', applyFilters);
    });

    function openCheckInModal(reservation, guest, room, date, time) {
        document.getElementById('checkInReservation').textContent = reservation;
        document.getElementById('checkInGuest').textContent = guest;
        document.getElementById('checkInRoom').textContent = room;
        document.getElementById('checkInDate').textContent = date;
        document.getElementById('checkInTime').textContent = time;
        document.getElementById('checkInModal').classList.remove('hidden');
        document.getElementById('checkInModal').classList.add('flex');
    }

    function openCheckOutModal(reservation, guest, room, date, time, balance) {
        document.getElementById('checkOutReservation').textContent = reservation;
        document.getElementById('checkOutGuest').textContent = guest;
        document.getElementById('checkOutRoom').textContent = room;
        document.getElementById('checkOutDate').textContent = date + ' ' + time;
        document.getElementById('checkOutBalance').textContent = balance;
        document.getElementById('checkOutModal').classList.remove('hidden');
        document.getElementById('checkOutModal').classList.add('flex');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endsection