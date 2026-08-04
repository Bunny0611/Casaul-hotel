@extends('housekeeping.layout')

@section('content')

<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    font-family:'Poppins',sans-serif;
}


.sidebar{
    background:linear-gradient( 180deg, #800000 0%, #5c0000 100%);
}

.header{
    background:linear-gradient( 90deg, #ff6b35 0%, #ff8c42 100% );
}

.nav-item:hover{
    background:rgba(255,255,255,0.1);
}

.nav-item.active{
    background:rgba(255,255,255,0.2);
    border-left:4px solid #ff6b35;
}

.animate-fade-in{
    animation:fade .5s ease-in-out;
}

@keyframes fade{
from{
    opacity:0;
    transform:translateY(10px);
}

to{
    opacity:1;
    transform:translateY(0);
}

}

</style>


<div class="flex h-screen overflow-hidden bg-gray-100">

    <aside id="sidebar" class="sidebar fixed -left-64 lg:left-0 top-0 z-50 w-64 h-screen text-white flex-shrink-0 transition-all duration-300 overflow-y-auto">

<div class="p-6">


    <h1 class="text-2xl font-bold tracking-wider">
        <i class="fas fa-hotel mr-2"></i>
        CASAUL
    </h1>
    <p class="text-sm text-gray-300 mt-1">
        Housekeeping 
    </p>
</div>




    <nav class="mt-6">
        <a href="{{ route('housekeeping.dashboard') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-home w-6"></i>
                <span>
                Dashboard
                </span>
        </a>

        <a href="{{ route('housekeeping.assigned-rooms') }}" class="nav-item active flex items-center px-6 py-3">
            <i class="fas fa-bed w-6"></i>
                <span>
                Assigned Rooms
                </span>
         </a>

        <a href="{{ route('housekeeping.room-status-update') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-sync-alt w-6"></i>

                <span>
                Room Status Update
                </span>
        </a>

        <a href="{{ route('housekeeping.guest-requests') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-bell w-6"></i>
                <span>
                Guest Requests
                </span>
        </a>

        <a href="{{ route('housekeeping.maintenance-report') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-tools w-6"></i>
                <span>
                Maintenance Report
                </span>
        </a>

        <a href="{{ route('housekeeping.cleaning-history') }}" class="nav-item flex items-center px-6 py-3">
            <i class="fas fa-history w-6"></i>
                <span>
                    Cleaning History
                </span>
        </a>

    </nav>

    <div class="absolute bottom-0 w-64 p-6">
        <a href="{{ route('logout') }}" class="flex items-center px-6 py-3 text-gray-300 hover:text-white">
            <i class="fas fa-sign-out-alt w-6"></i>
                Logout
        </a>
    </div>

</aside>

<div class="flex-1 flex flex-col overflow-hidden lg:ml-64">


    <header class="header text-white px-4 md:px-6 py-4 shadow-lg">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center">
                <button id="menuBtn" class="lg:hidden text-2xl mr-4">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="text-xl font-semibold">
                    Welcome to CASAUL Housekeeping
                </h2>
            </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Search..." class="bg-white/20 text-white placeholder-gray-200 px-4 py-2 rounded-lg focus:outline-none w-full">
                <i class="fas fa-search absolute right-3 top-3 text-gray-200"></i>
            </div>

            <div class="flex items-center gap-2 bg-white/20 px-4 py-2 rounded-lg">
                <i class="fas fa-user-circle text-2xl"></i>
                    <span>
                    Housekeeper
                    </span>
            </div>
        </div>
    </div>
</header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="animate-fade-in">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">
                                        Assigned Rooms
                        </h2>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <select id="priorityFilter" class="border rounded-lg px-4 py-2 h-11">

                                <option value="all">
                                    All Priority
                                </option>

                                <option value="high">
                                    High
                                </option>

                                <option value="medium">
                                    Medium
                                </option>

                                <option value="low">
                                    Low
                                </option>

                            </select>

                                <button onclick="openModal()" class="h-11 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-5 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-300 shadow-lg flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>
                                        Assign Room Task
                                </button>
                            </div>
                        </div>

                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            Assigned Cleaning Tasks
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Track cleaning start and completion time for each room in real time.
                                        </p>
                                    </div>

                                    <span id="taskCount" class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700">
                                        Total: 0 Rooms
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                        <table class="w-full min-w-[1100px]">
                                            <thead>
                                                <tr class="bg-gray-50">
                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Room
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Task
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Priority
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Assigned Staff
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Day
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Status
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Start Time
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                                        Finish Time
                                                    </th>

                                                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500 w-[220px]">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                        <tbody class="divide-y divide-gray-200">

                                <tr id="noTasksRow" style="display:none;">
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No tasks match this priority.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div id="assignModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">


    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">


        <!-- Header -->

        <div class="flex justify-between items-center px-6 py-4 border-b">


            <h2 class="text-2xl font-bold text-gray-800">

                Assign Cleaning Task

            </h2>


            <button onclick="closeModal()"
            class="text-2xl text-gray-500 hover:text-red-600">

                &times;

            </button>


        </div>





        <form id="assignTaskForm" class="p-6">


            <!-- Guest Information -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Guest Information

            </h3>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">


                <div>

                    <label>Guest Name</label>

                    <input type="text"
                    placeholder="John Smith"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>



                <div>

                    <label>Contact Number</label>

                    <input type="text"
                    placeholder="09123456789"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>



                <div>

                    <label>Booking Reference No.</label>

                    <input type="text"
                    placeholder="BK-20260731-001"
                    class="w-full border rounded-lg px-4 py-2 mt-2">

                </div>


            </div>






            <!-- Room Assignment -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Room Assignment

            </h3>



            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">


                <input
                id="roomNumber"
                name="roomNumber"
                type="text"
                placeholder="Room Number"
                class="border rounded-lg px-4 py-2">



                <select id="roomType" name="roomType" class="border rounded-lg px-4 py-2">

                    <option value="Deluxe Room">
                        Deluxe Room
                    </option>

                    <option>
                        Suite Room
                    </option>

                    <option>
                        Standard Room
                    </option>


                </select>



                <select id="floor" name="floor" class="border rounded-lg px-4 py-2">


                    <option value="1st Floor">
                        1st Floor
                    </option>

                    <option>
                        2nd Floor
                    </option>

                    <option>
                        3rd Floor
                    </option>


                </select>




                <input
                id="occupancy"
                name="occupancy"
                type="text"
                placeholder="Occupancy"
                class="border rounded-lg px-4 py-2">


            </div>






            <!-- Cleaning Type -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Cleaning Type

            </h3>


            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">


                <label>
                    <input type="checkbox" name="cleaningType[]" value="Check-out Cleaning">
                    Check-out Cleaning
                </label>


                <label>
                    <input type="checkbox" name="cleaningType[]" value="Daily Cleaning">
                    Daily Cleaning
                </label>


                <label>
                    <input type="checkbox" name="cleaningType[]" value="Deep Cleaning">
                    Deep Cleaning
                </label>


                <label>
                    <input type="checkbox" name="cleaningType[]" value="Room Inspection">
                    Room Inspection
                </label>


                <label>
                    <input type="checkbox" name="cleaningType[]" value="Linen Replacement">
                    Linen Replacement
                </label>


            </div>






            <!-- Priority -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Priority Level

            </h3>



            <div class="flex flex-wrap gap-8 mb-8">


                <label>
                    <input type="radio" name="priority" value="low">
                    Low
                </label>


                <label>
                    <input type="radio" name="priority" value="medium" checked>
                    Medium
                </label>


                <label>
                    <input type="radio" name="priority" value="high">
                    High
                </label>


                <label>
                    <input type="radio" name="priority" value="urgent">
                    Urgent
                </label>


            </div>






            <!-- Schedule -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Schedule

            </h3>



            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">


                <input id="scheduleDate" name="scheduleDate" type="date"
                class="border rounded-lg px-4 py-2">



                <input id="scheduleTime" name="scheduleTime" type="time"
                class="border rounded-lg px-4 py-2">



                <input id="duration" name="duration" type="text"
                placeholder="45 Minutes"
                class="border rounded-lg px-4 py-2">


            </div>






            <!-- Staff -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Assigned Staff

            </h3>



            <input
            id="assignedStaff"
            name="assignedStaff"
            type="text"
            placeholder="Maria Santos"
            class="border rounded-lg px-4 py-2 w-full mb-8">






            <!-- Notes -->


            <h3 class="font-semibold text-gray-700 mb-4">

                Special Instructions

            </h3>



            <textarea id="notes" name="notes" rows="4"
            placeholder="Guest requested extra pillows and towels. Check minibar before arrival."
            class="w-full border rounded-lg px-4 py-3 mb-8"></textarea>






            <div class="flex justify-end flex-wrap gap-3">


                <button
                type="button"
                onclick="closeModal()"
                class="px-6 py-3 rounded-lg border">


                    Cancel


                </button>





                <button
                type="submit"
                class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-lg">


                    <i class="fas fa-check mr-2"></i>


                    Assign Task


                </button>


            </div>



        </form>


    </div>


</div>







<script>


// OPEN MODAL

function openModal(){

    document
    .getElementById("assignModal")
    .classList.remove("hidden");


    document
    .getElementById("assignModal")
    .classList.add("flex");

}





// CLOSE MODAL

function closeModal(){

    document
    .getElementById("assignModal")
    .classList.remove("flex");


    document
    .getElementById("assignModal")
    .classList.add("hidden");

}






function formatTimestamp(date) {
    return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit'
    });
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getPriorityLabel(priority) {
    switch (priority) {
        case 'high':
            return 'High';
        case 'medium':
            return 'Medium';
        case 'urgent':
            return 'Urgent';
        default:
            return 'Low';
    }
}

function getPriorityClass(priority) {
    switch (priority) {
        case 'high':
            return 'inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700';
        case 'medium':
            return 'inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700';
        case 'urgent':
            return 'inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700';
        default:
            return 'inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700';
    }
}

function readStoredTasks() {
    try {
        return JSON.parse(localStorage.getItem('housekeeping-assigned-tasks') || '[]');
    } catch (e) {
        return [];
    }
}

function saveStoredTasks(tasks) {
    localStorage.setItem('housekeeping-assigned-tasks', JSON.stringify(tasks));
}

function appendTaskRow(task) {
    const tbody = document.querySelector('tbody');
    const noTasksRow = document.getElementById('noTasksRow');

    if (!tbody) {
        return;
    }

    const row = document.createElement('tr');
    row.className = 'hover:bg-gray-50 transition';
    row.setAttribute('data-row-id', task.id);
    row.setAttribute('data-priority', task.priority);
    row.setAttribute('data-task-name', task.task);

    const initials = (task.assignedStaff || 'Staff').split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase();
    const priorityClass = getPriorityClass(task.priority);

    row.innerHTML = `
        <td class="px-6 py-4">
            <div class="font-semibold text-gray-800">${escapeHtml(task.roomLabel)}</div>
            <div class="text-xs text-gray-500">${escapeHtml(task.roomType)} · ${escapeHtml(task.occupancy)}</div>
        </td>
        <td class="px-6 py-4">
            <div class="flex flex-col">
                <span class="font-semibold text-gray-800">${escapeHtml(task.task)}</span>
                <span class="text-xs text-gray-500">${escapeHtml(task.note || 'New task assigned')}</span>
            </div>
        </td>
        <td class="px-6 py-4">
            <span class="${priorityClass}">${escapeHtml(getPriorityLabel(task.priority))}</span>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700">${escapeHtml(initials)}</div>
                <div>
                    <div class="font-medium text-gray-800">${escapeHtml(task.assignedStaff || 'Unassigned')}</div>
                    <div class="text-xs text-gray-500">Assigned staff</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            <span class="text-sm font-medium text-gray-700">${escapeHtml(task.day || 'Not set')}</span>
        </td>
        <td class="px-6 py-4">
            <span data-status class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">Pending</span>
        </td>
        <td class="px-6 py-4">
            <span data-timestamp="start" class="text-sm text-gray-500">Not started</span>
        </td>
        <td class="px-6 py-4">
            <span data-timestamp="end" class="text-sm text-gray-500">—</span>
        </td>
        <td class="px-6 py-4 w-[260px]" data-action-cell>
            <div class="flex flex-wrap gap-2">
                <button type="button" data-action="start" onclick="startCleaning(this)" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 whitespace-nowrap">Start Cleaning</button>
                <button type="button" data-action="complete" onclick="completeCleaning(this)" class="hidden rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700 whitespace-nowrap">Complete</button>
                <button type="button" onclick="deleteTask(this)" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 whitespace-nowrap">Delete</button>
            </div>
        </td>`;

    if (noTasksRow) {
        noTasksRow.remove();
    }

    tbody.appendChild(row);
}

function renderStoredTasks() {
    const tasks = readStoredTasks();
    const tbody = document.querySelector('tbody');

    if (!tbody) {
        return;
    }

    tbody.querySelectorAll('tr[data-row-id]').forEach((row) => row.remove());

    tasks.forEach((task) => appendTaskRow(task));

    document.querySelectorAll('tbody tr[data-row-id]').forEach((row) => {
        applySavedTaskState(row);
    });

    filterTasks();
}

function filterTasks() {
    const select = document.getElementById('priorityFilter');
    const rows = document.querySelectorAll('tbody tr[data-priority]');
    const noTasksRow = document.getElementById('noTasksRow');
    const taskCount = document.getElementById('taskCount');
    const selected = select ? select.value : 'all';

    let visibleCount = 0;

    rows.forEach((row) => {
        const matches = selected === 'all' || row.getAttribute('data-priority') === selected;
        row.style.display = matches ? '' : 'none';

        if (matches) {
            visibleCount += 1;
        }
    });

    if (noTasksRow) {
        noTasksRow.style.display = visibleCount === 0 ? '' : 'none';
    }

    if (taskCount) {
        taskCount.textContent = `Total: ${visibleCount} Room${visibleCount === 1 ? '' : 's'}`;
    }
}

function getTaskStateKey(row) {
    return `housekeeping-task-${row.getAttribute('data-row-id')}`;
}

function saveTaskState(row, status, startTime = null, endTime = null) {
    const state = {
        status,
        startTime,
        endTime
    };

    localStorage.setItem(getTaskStateKey(row), JSON.stringify(state));
}

function readTaskState(row) {
    const saved = localStorage.getItem(getTaskStateKey(row));

    if (!saved) {
        return null;
    }

    try {
        return JSON.parse(saved);
    } catch (e) {
        return null;
    }
}

function formatStoredTime(value) {
    if (!value) {
        return '';
    }

    const dateValue = new Date(value);

    if (Number.isNaN(dateValue.getTime())) {
        return value;
    }

    return dateValue.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit'
    });
}

function applySavedTaskState(row) {
    const state = readTaskState(row);

    if (!state) {
        return;
    }

    const startCell = row.querySelector('[data-timestamp="start"]');
    const endCell = row.querySelector('[data-timestamp="end"]');
    const statusBadge = row.querySelector('[data-status]');
    const completeButton = row.querySelector('[data-action="complete"]');
    const startButton = row.querySelector('[data-action="start"]');
    const actionCell = row.querySelector('[data-action-cell]');

    if (startCell && state.startTime) {
        startCell.textContent = formatStoredTime(state.startTime);
        startCell.className = 'text-sm font-medium text-blue-700';
    }

    if (endCell && state.endTime) {
        endCell.textContent = formatStoredTime(state.endTime);
        endCell.className = 'text-sm font-medium text-green-700';
    }

    if (statusBadge) {
        if (state.status === 'completed') {
            statusBadge.textContent = 'Completed';
            statusBadge.className = 'inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700';
        } else if (state.status === 'cleaning') {
            statusBadge.textContent = 'Cleaning';
            statusBadge.className = 'inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700';
        }
    }

    if (state.status === 'completed') {
        if (startButton) {
            startButton.textContent = 'Started';
            startButton.className = 'rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition whitespace-nowrap opacity-60 cursor-not-allowed';
            startButton.disabled = true;
        }

        if (completeButton) {
            completeButton.textContent = 'Finished';
            completeButton.className = 'rounded-lg bg-gray-400 px-4 py-2 text-sm font-medium text-white transition whitespace-nowrap cursor-not-allowed';
            completeButton.disabled = true;
        }

        if (actionCell) {
            actionCell.innerHTML = '<span class="text-sm font-semibold text-green-600">Finished</span>';
        }
    } else if (state.status === 'cleaning') {
        if (startButton) {
            startButton.textContent = 'Started';
            startButton.className = 'rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition whitespace-nowrap opacity-60 cursor-not-allowed';
            startButton.disabled = true;
        }

        if (completeButton) {
            completeButton.classList.remove('hidden');
        }
    }
}

function startCleaning(button) {
    const row = button.closest('tr');
    const startCell = row.querySelector('[data-timestamp="start"]');
    const statusBadge = row.querySelector('[data-status]');
    const completeButton = row.querySelector('[data-action="complete"]');
    const startButton = row.querySelector('[data-action="start"]');
    const startTime = new Date();

    if (startCell) {
        startCell.textContent = formatTimestamp(startTime);
        startCell.className = 'text-sm font-medium text-blue-700';
    }

    if (statusBadge) {
        statusBadge.textContent = 'Cleaning';
        statusBadge.className = 'inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700';
    }

    if (startButton) {
        startButton.textContent = 'Started';
        startButton.className = 'rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition whitespace-nowrap opacity-60 cursor-not-allowed';
        startButton.disabled = true;
    }

    if (completeButton) {
        completeButton.classList.remove('hidden');
    }

    saveTaskState(row, 'cleaning', startTime.toISOString(), null);
}

function completeCleaning(button) {
    const row = button.closest('tr');
    const endCell = row.querySelector('[data-timestamp="end"]');
    const statusBadge = row.querySelector('[data-status]');
    const completeButton = row.querySelector('[data-action="complete"]');
    const actionCell = row.querySelector('[data-action-cell]');
    const endTime = new Date();
    const savedState = readTaskState(row) || {};

    if (endCell) {
        endCell.textContent = formatTimestamp(endTime);
        endCell.className = 'text-sm font-medium text-green-700';
    }

    if (statusBadge) {
        statusBadge.textContent = 'Completed';
        statusBadge.className = 'inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700';
    }

    if (completeButton) {
        completeButton.textContent = 'Finished';
        completeButton.className = 'rounded-lg bg-gray-400 px-4 py-2 text-sm font-medium text-white transition whitespace-nowrap cursor-not-allowed';
        completeButton.disabled = true;
    }

    if (actionCell) {
        actionCell.innerHTML = '<span class="text-sm font-semibold text-green-600">Finished</span>';
    }

    saveTaskState(row, 'completed', savedState.startTime || null, endTime.toISOString());
}

function deleteTask(button) {
    const row = button.closest('tr');

    if (!row) {
        return;
    }

    const confirmed = window.confirm('Are you sure you want to delete this?');

    if (!confirmed) {
        return;
    }

    const taskId = row.getAttribute('data-row-id');
    const tasks = readStoredTasks().filter((task) => task.id !== taskId);
    saveStoredTasks(tasks);

    row.remove();
    localStorage.removeItem(`housekeeping-task-${taskId}`);
    filterTasks();
}

function handleTaskSubmit(event) {
    event.preventDefault();

    const form = document.getElementById('assignTaskForm');
    const roomNumber = document.getElementById('roomNumber').value.trim();
    const roomType = document.getElementById('roomType').value;
    const occupancy = document.getElementById('occupancy').value.trim();
    const assignedStaff = document.getElementById('assignedStaff').value.trim();
    const notes = document.getElementById('notes').value.trim();
    const dayValue = document.getElementById('scheduleDate').value;
    const dayLabel = dayValue ? new Date(dayValue + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Not set';
    const selectedTasks = Array.from(document.querySelectorAll('input[name="cleaningType[]"]:checked')).map((checkbox) => checkbox.value);
    const priority = document.querySelector('input[name="priority"]:checked')?.value || 'medium';
    const task = selectedTasks.length ? selectedTasks.join(', ') : 'General Cleaning';
    const roomLabel = roomNumber ? `Room ${roomNumber}` : 'New Room';
    const taskId = `task-${Date.now()}`;

    const taskData = {
        id: taskId,
        roomLabel,
        roomType,
        occupancy: occupancy || 'Occupancy not set',
        task,
        priority,
        assignedStaff: assignedStaff || 'Unassigned',
        note: notes || 'New task assigned',
        day: dayLabel
    };

    const tasks = readStoredTasks();
    tasks.push(taskData);
    saveStoredTasks(tasks);

    appendTaskRow(taskData);
    filterTasks();
    closeModal();
    form.reset();
}

// MOBILE SIDEBAR

document.addEventListener("DOMContentLoaded", function(){


    const menuBtn = document.getElementById("menuBtn");

    const sidebar = document.getElementById("sidebar");
    const priorityFilter = document.getElementById("priorityFilter");

    if (priorityFilter) {
        priorityFilter.addEventListener("change", filterTasks);
    }

    renderStoredTasks();

    const form = document.getElementById('assignTaskForm');
    if (form) {
        form.addEventListener('submit', handleTaskSubmit);
    }

    menuBtn.addEventListener("click", function(){


        if(sidebar.classList.contains("-left-64")){


            sidebar.classList.remove("-left-64");

            sidebar.classList.add("left-0");


        }else{


            sidebar.classList.remove("left-0");

            sidebar.classList.add("-left-64");


        }


    });



});



</script>


@endsection