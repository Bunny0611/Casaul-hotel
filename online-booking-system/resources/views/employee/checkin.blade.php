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

    <div class="soft-card p-4 mb-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4 flex-1">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Search Reservation ID</label>
                    <input id="searchReservation" type="text" placeholder="BK1001" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Search Guest Name</label>
                    <input id="searchGuest" type="text" placeholder="Juan" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Filter by Date</label>
                    <input id="filterDate" type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Filter by Status</label>
                    <select id="filterStatus" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                        <option value="">All</option>
                        <option>Confirmed</option>
                        <option>Checked In</option>
                        <option>Checked Out</option>
                    </select>
                </div>
            </div>
            <button type="button" id="toggleFormsBtn" class="rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                Show Forms
            </button>
        </div>
    </div>

    <div id="formsSection" class="hidden mb-6 grid gap-6 xl:grid-cols-2">
        <div class="soft-card p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="stat-icon bg-emerald-100 text-emerald-700"><i class="fas fa-user-check"></i></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Check-in Form</h4>
                    <p class="text-sm text-slate-500">Register arrivals and confirm stay details.</p>
                </div>
            </div>
            <form class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Reservation ID</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="RES-1024">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Guest Name</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="Maria Santos">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Room Number</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="102">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Room Type</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="Deluxe King">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Number of Guests</label>
                    <input type="number" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="2">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Check-in Date</label>
                    <input type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="2026-07-31">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Check-in Time</label>
                    <input type="time" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="14:30">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Expected Checkout</label>
                    <input type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="2026-08-02">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Payment Status</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                        <option>Paid</option>
                        <option>Partial</option>
                        <option>Unpaid</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Booking Status</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                        <option>Reserved</option>
                        <option>Walk-in</option>
                        <option>Confirmed</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-600">Remarks</label>
                    <textarea rows="2" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" placeholder="Add special request or note"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="button" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Check In</button>
                </div>
            </form>
        </div>

        <div class="soft-card p-6">
            <div class="mb-4 flex items-center gap-3">
                <div class="stat-icon bg-rose-100 text-rose-700"><i class="fas fa-sign-out-alt"></i></div>
                <div>
                    <h4 class="font-semibold text-slate-800">Check-out Form</h4>
                    <p class="text-sm text-slate-500">Finalize departures and review room billing.</p>
                </div>
            </div>
            <form class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Reservation ID</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="RES-1042">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Guest Name</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="James Rivera">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Room Number</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="305">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Room Type</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="Executive Suite">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Check-in Date</label>
                    <input type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="2026-07-27">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Check-out Date</label>
                    <input type="date" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="2026-07-31">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Total Nights</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="4 Nights">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Total Amount</label>
                    <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" value="₱13,500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Payment Method</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                        <option>Cash</option>
                        <option>GCash</option>
                        <option>Card</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600">Status</label>
                    <select class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500">
                        <option>Paid</option>
                        <option>Partial</option>
                        <option>Unpaid</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-600">Remarks</label>
                    <textarea rows="2" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-sky-500" placeholder="Add departure note"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="button" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Check Out</button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="soft-card p-6">
            <h4 class="mb-4 font-semibold text-slate-800">TODAY'S CHECK-INS</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2 pr-3">Room</th>
                            <th class="pb-2 pr-3">Check-in Date</th>
                            <th class="pb-2 pr-3">Time</th>
                            <th class="pb-2 pr-3">Payment Status</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100" data-reservation="BK1001" data-guest="Juan Dela Cruz" data-date="2026-07-31" data-status="Confirmed">
                            <td class="py-3 pr-3">BK1001</td>
                            <td class="py-3 pr-3">Juan Dela Cruz</td>
                            <td class="py-3 pr-3">Deluxe 201</td>
                            <td class="py-3 pr-3">July 31</td>
                            <td class="py-3 pr-3">2:00 PM</td>
                            <td class="py-3 pr-3"><span class="pill bg-emerald-100 text-emerald-700">Paid</span></td>
                            <td class="py-3"><button type="button" class="text-sm font-semibold text-emerald-600" onclick="openCheckInModal('BK1001', 'Juan Dela Cruz', 'Deluxe 201', 'July 31', '2:00 PM')">Check In</button></td>
                        </tr>
                        <tr class="border-b border-slate-100" data-reservation="BK1002" data-guest="Ana Reyes" data-date="2026-07-31" data-status="Checked In">
                            <td class="py-3 pr-3">BK1002</td>
                            <td class="py-3 pr-3">Ana Reyes</td>
                            <td class="py-3 pr-3">Standard 105</td>
                            <td class="py-3 pr-3">July 31</td>
                            <td class="py-3 pr-3">3:00 PM</td>
                            <td class="py-3 pr-3"><span class="pill bg-amber-100 text-amber-700">Partial</span></td>
                            <td class="py-3"><button type="button" class="text-sm font-semibold text-emerald-600" onclick="openCheckInModal('BK1002', 'Ana Reyes', 'Standard 105', 'July 31', '3:00 PM')">Check In</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="soft-card p-6">
            <h4 class="mb-4 font-semibold text-slate-800">TODAY'S CHECK-OUTS</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="pb-2 pr-3">Reservation ID</th>
                            <th class="pb-2 pr-3">Guest</th>
                            <th class="pb-2 pr-3">Room</th>
                            <th class="pb-2 pr-3">Check-out Date</th>
                            <th class="pb-2 pr-3">Time</th>
                            <th class="pb-2 pr-3">Balance</th>
                            <th class="pb-2 pr-3">Status</th>
                            <th class="pb-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100" data-reservation="BK1003" data-guest="Mark Villanueva" data-date="2026-07-31" data-status="Checked Out">
                            <td class="py-3 pr-3">BK1003</td>
                            <td class="py-3 pr-3">Mark Villanueva</td>
                            <td class="py-3 pr-3">Suite 301</td>
                            <td class="py-3 pr-3">July 31</td>
                            <td class="py-3 pr-3">12:00 PM</td>
                            <td class="py-3 pr-3">₱0</td>
                            <td class="py-3 pr-3"><span class="pill bg-sky-100 text-sky-700">Occupied</span></td>
                            <td class="py-3"><button type="button" class="text-sm font-semibold text-rose-600" onclick="openCheckOutModal('BK1003', 'Mark Villanueva', 'Suite 301', 'July 31', '12:00 PM', '₱0')">Check Out</button></td>
                        </tr>
                        <tr data-reservation="BK1004" data-guest="Rose Dizon" data-date="2026-07-31" data-status="Checked Out">
                            <td class="py-3 pr-3">BK1004</td>
                            <td class="py-3 pr-3">Rose Dizon</td>
                            <td class="py-3 pr-3">Deluxe 102</td>
                            <td class="py-3 pr-3">July 31</td>
                            <td class="py-3 pr-3">11:00 AM</td>
                            <td class="py-3 pr-3">₱500</td>
                            <td class="py-3 pr-3"><span class="pill bg-amber-100 text-amber-700">Pending</span></td>
                            <td class="py-3"><button type="button" class="text-sm font-semibold text-rose-600" onclick="openCheckOutModal('BK1004', 'Rose Dizon', 'Deluxe 102', 'July 31', '11:00 AM', '₱500')">Check Out</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="checkInModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h4 class="text-lg font-semibold text-slate-800">Confirm Check-in</h4>
                <p class="text-sm text-slate-500">Review guest details before proceeding.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('checkInModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="space-y-3 text-sm text-slate-700">
            <div class="rounded-lg bg-slate-50 p-3"><strong>Reservation ID:</strong> <span id="checkInReservation"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Guest:</strong> <span id="checkInGuest"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Room:</strong> <span id="checkInRoom"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Check-in Date:</strong> <span id="checkInDate"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Time:</strong> <span id="checkInTime"></span></div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600" onclick="closeModal('checkInModal')">Cancel</button>
            <button type="button" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Confirm Check-in</button>
        </div>
    </div>
</div>

<div id="checkOutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h4 class="text-lg font-semibold text-slate-800">Confirm Check-out</h4>
                <p class="text-sm text-slate-500">Review the final bill and room status before proceeding.</p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600" onclick="closeModal('checkOutModal')"><i class="fas fa-times"></i></button>
        </div>
        <div class="space-y-3 text-sm text-slate-700">
            <div class="rounded-lg bg-slate-50 p-3"><strong>Reservation ID:</strong> <span id="checkOutReservation"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Guest:</strong> <span id="checkOutGuest"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Room:</strong> <span id="checkOutRoom"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Check-out Date:</strong> <span id="checkOutDate"></span></div>
            <div class="rounded-lg bg-slate-50 p-3"><strong>Balance:</strong> <span id="checkOutBalance"></span></div>
            <div class="rounded-lg bg-amber-50 p-3 text-amber-700">Room will be marked as Cleaning after checkout.</div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600" onclick="closeModal('checkOutModal')">Cancel</button>
            <button type="button" class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Confirm Check-out</button>
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
</script>
@endsection
