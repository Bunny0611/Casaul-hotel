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
        box-sizing: border-box;
        width: min(100%, 34rem);
        max-height: calc(100vh - 2rem);
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-radius: 1.75rem;
        background: #ffffff;
        padding: 1.75rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
    }

    .modal-header {
        flex-shrink: 0;
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

    .modal-body {
        min-height: 0;
        overflow-y: auto;
        padding-right: 0.25rem;
    }

    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 999px;
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
        flex-shrink: 0;
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
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">{{ $checkIns->count() }}</h4>
                </div>
                <div class="stat-icon bg-sky-100 text-sky-700"><i class="fas fa-sign-in-alt"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-outs</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">{{ $checkOuts->count() }}</h4>
                </div>
                <div class="stat-icon bg-emerald-100 text-emerald-700"><i class="fas fa-sign-out-alt"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Occupied Rooms</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">{{ $occupiedRooms }}</h4>
                </div>
                <div class="stat-icon bg-violet-100 text-violet-700"><i class="fas fa-bed"></i></div>
            </div>
        </div>
        <div class="soft-card p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-slate-500">Available Rooms</p>
                    <h4 class="mt-1 text-2xl font-semibold text-slate-800">{{ $availableRooms }}</h4>
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
                        <col class="w-1/5" />
                        <col class="w-2/5" />
                        <col class="w-1/4" />
                    </colgroup>
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2 pr-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checkIns as $reservation)
                            <tr class="border-b border-slate-100" data-reservation="RES-{{ $reservation->id }}" data-guest="{{ $reservation->guest_name }}" data-date="{{ $reservation->check_in?->format('F j, Y') }}" data-time="{{ $reservation->check_in_time ? \Illuminate\Support\Carbon::parse($reservation->check_in_time)->format('g:i A') : 'Time not set' }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-room-type="{{ $reservation->room?->room_type ?? 'N/A' }}" data-guests="{{ $reservation->number_of_guests ?? 'N/A' }}" data-status="{{ ucfirst($reservation->status) }}" data-balance="₱{{ number_format($reservation->total_amount, 2) }}" data-payment-status="{{ $reservation->payment_method ? 'Paid' : 'Unpaid' }}">
                                <td class="py-3 pr-3 whitespace-nowrap text-sm font-semibold text-slate-800">RES-{{ $reservation->id }}</td>
                                <td class="py-3 pr-3 text-sm text-slate-700">{{ $reservation->guest_name }}</td>
                                <td class="py-3 pr-3 whitespace-nowrap">
                                    @if($reservation->status === 'checked-in')
                                        <span class="inline-flex cursor-pointer items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 hover:opacity-80 transition" onclick="openCheckInModal(this.closest('tr'))"><i class="fas fa-check-circle"></i> Confirmed</span>
                                    @else
                                        <span class="inline-flex cursor-pointer items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 hover:opacity-80 transition" onclick="openCheckInModal(this.closest('tr'))"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-500">No check-ins scheduled for today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="soft-card p-6">
            <h4 class="mb-4 font-semibold text-slate-800">TODAY'S CHECK-OUTS</h4>
            <div class="overflow-hidden">
                <table class="w-full table-fixed text-sm">
                    <colgroup>
                        <col class="w-1/5" />
                        <col class="w-2/5" />
                        <col class="w-1/4" />
                    </colgroup>
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2 pr-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checkOuts as $reservation)
                            @php($paid = $reservation->payments->sum('amount'))
                            <tr class="border-b border-slate-100" data-reservation="RES-{{ $reservation->id }}" data-guest="{{ $reservation->guest_name }}" data-check-in-date="{{ $reservation->check_in?->format('Y-m-d') }}" data-check-out-date="{{ $reservation->check_out?->format('Y-m-d') }}" data-time="{{ ($reservation->check_out_time ?: $reservation->check_in_time) ? \Illuminate\Support\Carbon::parse($reservation->check_out_time ?: $reservation->check_in_time)->format('g:i A') : 'Time not set' }}" data-room="{{ $reservation->room?->room_number ?? 'N/A' }}" data-room-type="{{ $reservation->room?->room_type ?? 'N/A' }}" data-total="{{ number_format($reservation->total_amount, 2, '.', '') }}" data-paid="{{ number_format($paid, 2, '.', '') }}" data-status="{{ ucfirst($reservation->status) }}">
                                <td class="py-3 pr-3 whitespace-nowrap text-sm font-semibold text-slate-800">RES-{{ $reservation->id }}</td>
                                <td class="py-3 pr-3 text-sm text-slate-700">{{ $reservation->guest_name }}</td>
                                <td class="py-3 pr-3 whitespace-nowrap">
                                    @if($reservation->status === 'completed')
                                        <span class="inline-flex cursor-pointer items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 hover:opacity-80 transition" onclick="openCheckOutModal(this.closest('tr'))"><i class="fas fa-check-circle"></i> Confirmed</span>
                                    @else
                                        <span class="inline-flex cursor-pointer items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 hover:opacity-80 transition" onclick="openCheckOutModal(this.closest('tr'))"><i class="fas fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-slate-500">No check-outs scheduled for today.</td>
                            </tr>
                        @endforelse
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
                <p class="modal-subtitle">Review guest and reservation details before proceeding.</p>
            </div>
            <button type="button" class="modal-close-btn" onclick="closeModal('checkInModal')"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body">
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
                <span class="modal-detail-label">Room Type:</span>
                <span class="modal-detail-value" id="checkInRoomType"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Number of Guests:</span>
                <span class="modal-detail-value" id="checkInGuests"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-in Date:</span>
                <span class="modal-detail-value" id="checkInDate"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-in Time:</span>
                <span class="modal-detail-value" id="checkInTime"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Payment Status:</span>
                <span class="modal-detail-value" id="checkInPaymentStatus"></span>
            </div>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('checkInModal')">Cancel</button>
            <form id="checkInForm" method="POST" action="{{ route('employee.reservations.status', ['id' => '__ID__']) }}" onsubmit="handleCheckInSubmit(event)">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="checked-in">
                <button type="submit" class="modal-btn modal-btn-primary">Confirm Check-in</button>
            </form>
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

        <div class="modal-body">
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
                <span class="modal-detail-label">Room Type:</span>
                <span class="modal-detail-value" id="checkOutRoomType"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-in Date:</span>
                <span class="modal-detail-value" id="checkOutCheckInDate"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-out Date:</span>
                <span class="modal-detail-value" id="checkOutDate"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Check-out Time:</span>
                <span class="modal-detail-value" id="checkOutTime"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Total Amount:</span>
                <span class="modal-detail-value" id="checkOutTotal"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Amount Paid:</span>
                <span class="modal-detail-value" id="checkOutPaid"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Balance Due:</span>
                <span class="modal-detail-value text-rose-600" id="checkOutBalance"></span>
            </div>
            <div class="modal-detail-row">
                <span class="modal-detail-label">Payment Status:</span>
                <span class="modal-detail-value" id="checkOutPaymentStatus"></span>
            </div>
            <button type="button" id="recordPaymentButton" class="modal-btn modal-btn-secondary mt-4" onclick="openPaymentModal()">Record Payment</button>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('checkOutModal')">Cancel</button>
            <form id="checkOutForm" method="POST" action="{{ route('employee.reservations.status', ['id' => '__ID__']) }}" onsubmit="handleCheckOutSubmit(event)">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="completed">
                <button type="submit" id="confirmCheckOutButton" class="modal-btn modal-btn-primary" disabled>Confirm Check-out</button>
            </form>
        </div>
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="modal-panel w-full max-w-lg">
        <div class="modal-header">
            <div><h4 class="modal-title">Record Payment</h4><p class="modal-subtitle">Save a payment against this reservation.</p></div>
            <button type="button" class="modal-close-btn" onclick="closeModal('paymentModal')"><i class="fas fa-times"></i></button>
        </div>
        <form id="paymentForm" class="modal-body" method="POST">
            @csrf
            <div class="modal-details">
                <div class="modal-detail-row"><span class="modal-detail-label">Reservation ID:</span><span class="modal-detail-value" id="paymentReservation"></span></div>
                <div class="modal-detail-row"><span class="modal-detail-label">Guest:</span><span class="modal-detail-value" id="paymentGuest"></span></div>
                <div class="modal-detail-row"><span class="modal-detail-label">Total Amount:</span><span class="modal-detail-value" id="paymentTotal"></span></div>
                <div class="modal-detail-row"><span class="modal-detail-label">Amount Already Paid:</span><span class="modal-detail-value" id="paymentPaid"></span></div>
                <div class="modal-detail-row"><span class="modal-detail-label">Balance Due:</span><span class="modal-detail-value text-rose-600" id="paymentBalance"></span></div>
                <label class="mt-3 block text-sm font-medium text-slate-700">Payment Amount<input id="paymentAmount" name="amount" type="number" min="0.01" step="0.01" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required></label>
                <label class="mt-3 block text-sm font-medium text-slate-700">Payment Method<select id="paymentMethod" name="payment_method" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required><option>Cash</option><option>GCash</option><option>Bank Transfer</option><option>Credit/Debit Card</option></select></label>
                <label class="mt-3 block text-sm font-medium text-slate-700">Payment Date<input name="payment_date" type="date" value="{{ now()->toDateString() }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2" required></label>
                <label class="mt-3 block text-sm font-medium text-slate-700">Reference Number<input id="paymentReference" name="reference_number" type="text" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></label>
                <label class="mt-3 block text-sm font-medium text-slate-700">Notes<textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2"></textarea></label>
            </div>
            <div class="modal-actions mt-5"><button type="button" class="modal-btn modal-btn-secondary" onclick="closeModal('paymentModal')">Cancel</button><button type="submit" class="modal-btn modal-btn-primary">Record Payment</button></div>
        </form>
    </div>
</div>

<script>
    let currentCheckInRow = null;

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

    function openCheckInModal(row) {
        currentCheckInRow = row;
        const reservation = row.dataset.reservation;
        const reservationId = reservation.replace('RES-', '');
        const date = row.dataset.date ? new Date(`${row.dataset.date}T00:00:00`).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : 'N/A';
        const isConfirmed = row.dataset.status.toLowerCase() === 'checked-in';
        
        document.getElementById('checkInReservation').textContent = reservation;
        document.getElementById('checkInGuest').textContent = row.dataset.guest || 'N/A';
        document.getElementById('checkInRoom').textContent = row.dataset.room || 'N/A';
        document.getElementById('checkInRoomType').textContent = row.dataset.roomType || 'N/A';
        document.getElementById('checkInGuests').textContent = row.dataset.guests || 'N/A';
        document.getElementById('checkInDate').textContent = date;
        document.getElementById('checkInTime').textContent = row.dataset.time || 'N/A';
        document.getElementById('checkInPaymentStatus').textContent = row.dataset.paymentStatus || 'Unpaid';
        
        const submitBtn = document.querySelector('#checkInForm button[type="submit"]');
        if (isConfirmed) {
            submitBtn.textContent = 'Already Confirmed';
            submitBtn.disabled = true;
        } else {
            submitBtn.textContent = 'Confirm Check-in';
            submitBtn.disabled = false;
        }
        
        document.getElementById('checkInForm').action = document.getElementById('checkInForm').action.replace('__ID__', reservationId);
        document.getElementById('checkInModal').classList.remove('hidden');
        document.getElementById('checkInModal').classList.add('flex');
    }

    function handleCheckInSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (currentCheckInRow) {
                const statusCell = currentCheckInRow.querySelector('td:nth-child(3)');
                const actionCell = currentCheckInRow.querySelector('td:nth-child(4)');
                statusCell.innerHTML = '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"><i class="fas fa-check-circle"></i> Confirmed</span>';
                actionCell.innerHTML = '<span class="text-sm font-semibold text-slate-400">Completed</span>';
                currentCheckInRow.dataset.status = 'Checked-in';
            }
            closeModal('checkInModal');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Unable to confirm check-in. Please try again.');
        });
    }

    function openCheckOutModal(row) {
        window.currentCheckOutRow = row;
        const reservation = row.dataset.reservation;
        const reservationId = reservation.replace('RES-', '');
        const isCompleted = row.dataset.status.toLowerCase() === 'completed';
        
        document.getElementById('checkOutReservation').textContent = reservation;
        document.getElementById('checkOutGuest').textContent = row.dataset.guest || 'N/A';
        document.getElementById('checkOutRoom').textContent = row.dataset.room || 'N/A';
        document.getElementById('checkOutRoomType').textContent = row.dataset.roomType || 'N/A';
        document.getElementById('checkOutCheckInDate').textContent = formatReservationDate(row.dataset.checkInDate);
        document.getElementById('checkOutDate').textContent = formatReservationDate(row.dataset.checkOutDate);
        document.getElementById('checkOutTime').textContent = row.dataset.time || 'N/A';
        const total = Number(row.dataset.total || 0);
        const paid = Number(row.dataset.paid || 0);
        document.getElementById('checkOutTotal').textContent = formatCurrency(total);
        document.getElementById('checkOutPaid').textContent = formatCurrency(paid);
        document.getElementById('checkOutBalance').textContent = formatCurrency(Math.max(total - paid, 0));
        
        const recordPaymentButton = document.getElementById('recordPaymentButton');
        const submitBtn = document.querySelector('#checkOutForm button[type="submit"]');
        
        if (isCompleted) {
            recordPaymentButton.classList.add('hidden');
            submitBtn.textContent = 'Already Confirmed';
            submitBtn.disabled = true;
            updateCheckoutPaymentState(total, paid, true);
        } else {
            recordPaymentButton.classList.remove('hidden');
            submitBtn.textContent = 'Confirm Check-out';
            submitBtn.disabled = false;
            updateCheckoutPaymentState(total, paid, false);
        }
        
        document.getElementById('checkOutForm').action = `{{ url('/employee/reservations') }}/${reservationId}/status`;
        document.getElementById('checkOutModal').classList.remove('hidden');
        document.getElementById('checkOutModal').classList.add('flex');
    }

    function handleCheckOutSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (window.currentCheckOutRow) {
                const statusCell = window.currentCheckOutRow.querySelector('td:nth-child(3)');
                const actionCell = window.currentCheckOutRow.querySelector('td:nth-child(4)');
                statusCell.innerHTML = '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"><i class="fas fa-check-circle"></i> Confirmed</span>';
                actionCell.innerHTML = '<span class="text-sm font-semibold text-slate-400">Completed</span>';
                window.currentCheckOutRow.dataset.status = 'Completed';
            }
            closeModal('checkOutModal');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Unable to confirm check-out. Please try again.');
        });
    }

    function updateCheckoutPaymentState(total, paid, isCompleted = false) {
        const balance = Math.max(total - paid, 0);
        document.getElementById('checkOutPaymentStatus').textContent = balance === 0 ? 'Paid' : 'Unpaid';
        const recordPaymentButton = document.getElementById('recordPaymentButton');
        if (isCompleted) {
            recordPaymentButton.classList.add('hidden');
        } else {
            recordPaymentButton.classList.toggle('hidden', balance === 0);
        }
        document.getElementById('confirmCheckOutButton').disabled = balance > 0 && !isCompleted;
    }

    function openPaymentModal() {
        const row = window.currentCheckOutRow;
        const total = Number(row.dataset.total || 0);
        const paid = Number(row.dataset.paid || 0);
        const balance = Math.max(total - paid, 0);
        document.getElementById('paymentReservation').textContent = row.dataset.reservation;
        document.getElementById('paymentGuest').textContent = row.dataset.guest || 'N/A';
        document.getElementById('paymentTotal').textContent = formatCurrency(total);
        document.getElementById('paymentPaid').textContent = formatCurrency(paid);
        document.getElementById('paymentBalance').textContent = formatCurrency(balance);
        document.getElementById('paymentAmount').value = balance.toFixed(2);
        document.getElementById('paymentForm').action = `{{ url('/employee/reservations') }}/${row.dataset.reservation.replace('RES-', '')}/payments`;
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }

    document.getElementById('paymentMethod').addEventListener('change', (event) => {
        document.getElementById('paymentReference').required = event.target.value !== 'Cash';
    });

    document.getElementById('paymentForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } });
        const data = await response.json();
        if (!response.ok) {
            alert(data.message || 'Unable to record payment.');
            return;
        }
        const row = window.currentCheckOutRow;
        row.dataset.paid = data.paid.toFixed(2);
        document.getElementById('checkOutPaid').textContent = formatCurrency(data.paid);
        document.getElementById('checkOutBalance').textContent = formatCurrency(data.balance);
        updateCheckoutPaymentState(data.total, data.paid);
        closeModal('paymentModal');
    });

    function formatReservationDate(value) {
        return value ? new Date(`${value}T00:00:00`).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : 'N/A';
    }

    function formatCurrency(value) {
        return `₱${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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