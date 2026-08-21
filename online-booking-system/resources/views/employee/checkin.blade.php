 Premium is ad free YouTube, and ad free videos on the YouTube kids app. Try one one free sleeping bag I'm not gonna move got some words on carboard got you picture in my heads. Can you tell where I am? So try money understands can I do all way I'm sitting up here for you on the corruptsleep she all enjoy back it's all been done before and if you could only let it be you would see I like you the way you are when we're driving a car and your charge to me what on you become somebody else in your back that you can relax a fool to meet this few happywhere you are in twenty thousand when we start to vote take off you don't know they don't folk when you become twentieth anyway when we bumped into each other and maybe bounds the big mistake on alive since@extends('employee.layout')

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

    .checkout-detail-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        background: #fafbff;
        padding: 1.25rem;
    }

    .checkout-detail-row {
        display: grid;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .checkout-detail-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
    }

    .checkout-detail-value {
        padding: 0.95rem 1rem;
        border-radius: 1rem;
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #0f172a;
        font-weight: 600;
    }

    .checkout-meta {
        display: grid;
        gap: 1rem;
    }

    .checkout-meta-summary {
        border-radius: 1.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }

    .checkout-meta-summary strong {
        display: block;
        margin-top: 0.25rem;
        font-size: 1.05rem;
        color: #0f172a;
    }

    .checkout-note {
        border-radius: 1.5rem;
        background: #fdf2f8;
        border: 1px solid #fbcfe8;
        color: #9d174d;
        padding: 1rem 1.25rem;
        font-size: 0.95rem;
    }

    .checkout-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-end;
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
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.75rem;
        align-items: stretch;
    }

    .check-form-card {
        border: 1px solid #e2e8f0;
        border-radius: 1.5rem;
        background: #ffffff;
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        max-height: 640px;
        min-height: 0;
        overflow: hidden;
    }

    .check-form-body {
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        min-height: 0;
        max-height: 420px;
        padding-right: 8px;
        margin-bottom: 0.75rem;
    }

    .check-form-body::-webkit-scrollbar {
        width: 8px;
    }

    .check-form-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .check-form-body::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.45);
        border-radius: 999px;
    }

    .check-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .check-form-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }

    .check-form-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .check-form-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem 1.25rem;
    }

    .check-form-field {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .full-width-field {
        grid-column: span 2;
    }

    .check-form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #475569;
    }

    .check-form-input,
    .check-form-select,
    .check-form-textarea {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #0f172a;
        padding: 0.95rem 1rem;
        font-size: 0.95rem;
        outline: none;
    }

    .check-form-textarea {
        min-height: 110px;
        resize: vertical;
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
    .check-form-select:focus,
    .check-form-textarea:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
    }

    .check-form-footer {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eef2f7;
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

    .check-form-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }

    .check-form-header p {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    @media (max-width: 900px) {
        .check-form-grid {
            grid-template-columns: 1fr;
        }

        .check-form-fields {
            grid-template-columns: 1fr;
        }
        .full-width-field {
            grid-column: span 1;
        }
    }

    .checkin-action-btn,
    .checkout-action-btn {
        border-radius: 999px;
        padding: 0.7rem 1.1rem;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        min-width: 110px;
    }

    .checkin-action-btn {
        background: #059669;
        color: #ffffff;
    }

    .checkout-action-btn {
        background: #dc2626;
        color: #ffffff;
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

    <div class="check-form-grid">
        <div class="check-form-card">
            <div class="check-form-header">
                <div class="check-form-title-group">
                    <span class="check-form-icon"><i class="fas fa-user-check"></i></span>
                    <div>
                        <h3>Check-in Form</h3>
                        <p>Register arrivals and confirm stay details.</p>
                    </div>
                </div>
                <span class="pill bg-emerald-100 text-emerald-700">Check In</span>
            </div>
            <div class="check-form-body">
                <div class="check-form-fields">
                    <div class="check-form-field">
                        <label class="check-form-label">Reservation ID</label>
                        <input type="text" class="check-form-input" value="RES-1024" readonly>
                    </div>
                    <div class="check-form-field">
                        <label class="check-form-label">Room</label>
                        <input type="text" class="check-form-input" value="102 - Deluxe King" readonly>
                    </div>
                    <div class="check-form-field">
                        <label class="check-form-label">Guests</label>
                        <input type="text" class="check-form-input" value="2" readonly>
                    </div>
                    <div class="check-form-field">
                        <label class="check-form-label">Arriving On</label>
                        <input type="date" class="check-form-input" value="2026-07-31" readonly>
                    </div>
                    <div class="check-form-field">
                        <label class="check-form-label">Status</label>
                        <select class="check-form-select">
                            <option>Fully Paid</option>
                            <option>Reserved</option>
                        </select>
                    </div>
                    <div class="check-form-field">
                        <label class="check-form-label">Payment Method</label>
                        <select class="check-form-select">
                            <option>GCash</option>
                            <option>Credit Card</option>
                            <option>Landbank</option>
                            <option>PayMaya</option>
                            <option>Cash</option>
                        </select>
                    </div>
                    <div class="check-form-field full-width-field">
                        <label class="check-form-label">Remarks</label>
                        <textarea class="check-form-textarea" rows="3" placeholder="Add special request or note"></textarea>
                    </div>
                    <div class="check-form-field full-width-field">
                        <label class="check-form-label">Arrival Notes</label>
                        <textarea class="check-form-textarea" rows="3" placeholder="Early check-in requested, late arrival, etc."></textarea>
                    </div>
                    <div class="check-form-field full-width-field">
                        <label class="check-form-label">Guest Preferences</label>
                        <textarea class="check-form-textarea" rows="3" placeholder="Room view, bedding preference, accessibility needs"></textarea>
                    </div>
                </div>
            </div>
            <div class="check-form-footer">
                <button type="button" class="checkin-action-btn">Check In</button>
            </div>
        </div>
        <div class="check-form-card">
            <div class="check-form-header">
                <div class="check-form-title-group">
                    <span class="check-form-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <div>
                        <h3>Check-out Form</h3>
                        <p>Finalize departures and review room billing.</p>
                    </div>
                </div>
                <span class="pill bg-rose-100 text-rose-700">Check Out</span>
            </div>
            <div class="check-form-body">
                <div class="check-form-fields">
                    <div class="check-form-field">
                        <label class="check-form-label">Reservation ID</label>
                        <input type="text" class="check-form-input" value="RES-1042" readonly>
                    </div>
                <div class="check-form-field">
                    <label class="check-form-label">Guest Name</label>
                    <input type="text" class="check-form-input" value="James Rivera" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Room Number</label>
                    <input type="text" class="check-form-input" value="305" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Room Type</label>
                    <input type="text" class="check-form-input" value="Executive Suite" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Check-in Date</label>
                    <input type="date" class="check-form-input" value="2026-07-27" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Check-out Date</label>
                    <input type="date" class="check-form-input" value="2026-07-31" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Total Nights</label>
                    <input type="text" class="check-form-input" value="4 Nights" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Total Amount</label>
                    <input type="text" class="check-form-input" value="₱13,500" readonly>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Payment Method</label>
                    <select class="check-form-select">
                        <option>GCash</option>
                        <option>Credit Card</option>
                        <option>Landbank</option>
                        <option>PayMaya</option>
                        <option>Cash</option>
                    </select>
                </div>
                <div class="check-form-field">
                    <label class="check-form-label">Status</label>
                    <select class="check-form-select">
                        <option>Paid</option>
                        <option>Pending</option>
                    </select>
                </div>
                <div class="check-form-field full-width-field">
                    <label class="check-form-label">Remarks</label>
                    <textarea class="check-form-input" rows="3" placeholder="Add departure note"></textarea>
                </div>
                </div>
            </div>
            <div class="check-form-footer">
                <button type="button" class="checkout-action-btn">Check Out</button>
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
    <div class="w-full max-w-xl rounded-[32px] bg-white p-8 shadow-2xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h4 class="text-2xl font-semibold text-slate-900">Confirm Check-in</h4>
                <p class="mt-1 text-sm text-slate-500">Review guest details and register arrival.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('checkInModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="grid gap-4 lg:grid-cols-2 lg:gap-6">
            <div class="checkout-detail-card">
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Reservation ID</span>
                    <div class="checkout-detail-value" id="checkInReservation"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Guest</span>
                    <div class="checkout-detail-value" id="checkInGuest"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Room</span>
                    <div class="checkout-detail-value" id="checkInRoom"></div>
                </div>
            </div>
            <div class="checkout-detail-card">
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Check-in Date</span>
                    <div class="checkout-detail-value" id="checkInDate"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Time</span>
                    <div class="checkout-detail-value" id="checkInTime"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Notes</span>
                    <div class="checkout-detail-value">No special notes</div>
                </div>
            </div>
        </div>
        <div class="checkout-actions mt-8">
            <button type="button" class="checkout-cancel-btn" onclick="closeModal('checkInModal')">Cancel</button>
            <button type="button" class="checkin-confirm-btn">Confirm Check-in</button>
        </div>
    </div>
</div>

<div id="checkOutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="w-full max-w-xl rounded-[32px] bg-white p-8 shadow-2xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h4 class="text-2xl font-semibold text-slate-900">Finalize Check-out</h4>
                <p class="mt-1 text-sm text-slate-500">Confirm departure details and post any outstanding charges.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('checkOutModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="grid gap-4 lg:grid-cols-2 lg:gap-6">
            <div class="checkout-detail-card">
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Reservation ID</span>
                    <div class="checkout-detail-value" id="checkOutReservation"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Guest Name</span>
                    <div class="checkout-detail-value" id="checkOutGuest"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Room</span>
                    <div class="checkout-detail-value" id="checkOutRoom"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Check-out Date</span>
                    <div class="checkout-detail-value" id="checkOutDate"></div>
                </div>
            </div>
            <div class="checkout-detail-card">
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Balance Due</span>
                    <div class="checkout-detail-value font-semibold text-rose-600" id="checkOutBalance"></div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Status</span>
                    <div class="checkout-detail-value">Ready for departure</div>
                </div>
                <div class="checkout-detail-row">
                    <span class="checkout-detail-label">Next Step</span>
                    <div class="checkout-detail-value checkout-note">Room will be marked for cleaning after checkout.</div>
                </div>
            </div>
        </div>
        <div class="checkout-actions mt-8">
            <button type="button" class="checkout-cancel-btn" onclick="closeModal('checkOutModal')">Cancel</button>
            <button type="button" class="checkout-confirm-btn">Confirm Check-out</button>
        </div>
    </div>
</div>

<script>
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

    function applyFilters() {
        const reservation = document.getElementById('searchReservation').value.toLowerCase();
        const guest = document.getElementById('searchGuest').value.toLowerCase();
        const date = document.getElementById('filterDate').value;
        const status = document.getElementById('filterStatus').value.toLowerCase();

        document.querySelectorAll('tbody tr').forEach((row) => {
            const rowReservation = row.getAttribute('data-reservation').toLowerCase();
            const rowGuest = row.getAttribute('data-guest').toLowerCase();
            const rowDate = row.getAttribute('data-date');
            const rowStatus = row.getAttribute('data-status').toLowerCase();

            const matchesReservation = rowReservation.includes(reservation);
            const matchesGuest = rowGuest.includes(guest);
            const matchesDate = !date || rowDate === date;
            const matchesStatus = !status || rowStatus === status;

            row.style.display = matchesReservation && matchesGuest && matchesDate && matchesStatus ? '' : 'none';
        });
    }

    const toggleFormsBtn = document.getElementById('toggleFormsBtn');
    const formsSection = document.getElementById('formsSection');

    toggleFormsBtn?.addEventListener('click', () => {
        const isHidden = formsSection.classList.contains('hidden');
        formsSection.classList.toggle('hidden', !isHidden);
        toggleFormsBtn.textContent = isHidden ? 'Hide Forms' : 'Show Forms';
    });

    ['searchReservation', 'searchGuest', 'filterDate', 'filterStatus'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', applyFilters);
        document.getElementById(id)?.addEventListener('change', applyFilters);
    });

    // Wire confirm check-in button to close modal (placeholder for actual action)
    document.querySelector('.checkin-confirm-btn')?.addEventListener('click', function () {
        closeModal('checkInModal');
    });
</script>
@endsection
