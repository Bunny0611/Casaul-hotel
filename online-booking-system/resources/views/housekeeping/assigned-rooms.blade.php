@extends('housekeeping.layout')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap');

    .task-form-shell {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: grid;
        align-items: start;
        justify-content: center;
        overflow-y: auto;
        width: 100%;
        min-height: 100%;
        padding: 1.25rem 1rem;
        background: #fff;
        box-shadow: 0 0 0 100vmax rgba(38, 25, 20, .58), 0 24px 60px rgba(38, 25, 20, .22);
        font-family: 'DM Sans', sans-serif;
    }

    .task-form-shell.hidden { display: none; }

    .task-form-shell > * {
        width: auto;
    }

    .task-form-shell {
        grid-template-columns: repeat(2, minmax(0, 340px));
        width: min(100%, 720px);
        min-height: auto;
        margin: auto;
        border-radius: .8rem;
        background: #fff;
    }

    .task-form-heading {
        border-top: 4px solid #f97316;
        border-radius: .8rem .8rem 0 0;
        background: #fff;
        padding: 1.35rem 1.5rem 1rem;
    }

    .task-form-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        grid-column: 1 / -1;
        margin: 0;
    }

    .task-form-heading h3 {
        margin: 0;
        color: #1f2937;
        font-size: 1.15rem;
        font-weight: 700;
    }

    .task-form-heading p {
        margin: .3rem 0 0;
        color: #64748b;
        font-size: .85rem;
    }

    .task-form-section {
        display: grid;
        gap: .9rem;
        align-content: start;
        padding: 1rem;
        border: 1px solid #e8edf2;
        border-radius: .7rem;
        background: #fbfcfd;
    }

    .task-form-section-wide {
        grid-column: 1 / -1;
    }

    .task-form-section-title {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin: 0;
        color: #334155;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .task-form-section-title i {
        color: #f97316;
    }

    .task-form-field {
        display: grid;
        gap: .35rem;
        color: #475569;
        font-size: .8rem;
        font-weight: 600;
    }

    .task-form-field input,
    .task-form-field select,
    .task-form-field textarea {
        width: 100%;
        border: 1px solid #dbe3ea;
        border-radius: .45rem;
        background: #fff;
        color: #1f2937;
        font: inherit;
        font-weight: 400;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .task-form-field input,
    .task-form-field select {
        min-height: 2.55rem;
        padding: .55rem .7rem;
    }

    .task-form-field textarea {
        min-height: 5.5rem;
        padding: .65rem .7rem;
        resize: vertical;
    }

    .task-form-field input:focus,
    .task-form-field select:focus,
    .task-form-field textarea:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    .task-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: .7rem;
        grid-column: 1 / -1;
        padding-top: .25rem;
        padding-bottom: 1.35rem;
        border-radius: 0 0 .8rem .8rem;
        background: #fff;
    }

    .task-form-submit {
        border: 0;
        border-radius: .45rem;
        background: #1f2937;
        padding: .65rem 1.15rem;
        color: #fff;
        font: inherit;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
    }

    .task-form-submit:hover { background: #111827; }

    .task-form-cancel { border: 0; border-radius: .45rem; background: #f1f5f9; padding: .65rem 1.15rem; color: #475569; font: inherit; font-size: .85rem; font-weight: 700; cursor: pointer; }
    .task-form-cancel:hover { background: #e2e8f0; }
    .task-form-close { border: 0; background: transparent; color: #64748b; font-size: 1.35rem; cursor: pointer; }

    .task-details-modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        place-items: center;
        padding: 1rem;
        background: rgba(15, 23, 42, .55);
    }

    .task-details-modal.is-open { display: grid; }

    .task-details-card {
        width: min(100%, 680px);
        max-height: 90vh;
        overflow-y: auto;
        border-radius: .8rem;
        background: #fff;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .2);
    }

    .task-details-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.15rem 1.35rem;
        border-bottom: 1px solid #e8edf2;
    }

    .task-details-header h3 { margin: 0; color: #1f2937; font-size: 1.1rem; font-weight: 700; }
    .task-details-close { border: 0; background: transparent; color: #64748b; font-size: 1.35rem; cursor: pointer; }
    .task-details-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; padding: 1.35rem; }
    .task-detail-item { min-width: 0; }
    .task-detail-item.full { grid-column: 1 / -1; }
    .task-detail-label { display: block; margin-bottom: .25rem; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .task-detail-value { color: #1f2937; font-size: .9rem; overflow-wrap: anywhere; }
    .assigned-page { max-width: 1600px; margin: 0 auto; animation: housekeeping-fade .35s ease; }
    @keyframes housekeeping-fade { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .assigned-hero { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.35rem; padding: 1.5rem 1.7rem; border: 1px solid #f2d8c9; border-radius: 1rem; background: linear-gradient(135deg, #fff 0%, #fff7f2 100%); box-shadow: 0 8px 24px rgba(73, 52, 42, .06); }
    .assigned-eyebrow { margin: 0 0 .35rem; color: #c2410c; font-size: .72rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
    .assigned-hero h2 { margin: 0; color: #3a2520; font-size: 1.8rem; font-weight: 750; }
    .assigned-hero p { margin: .35rem 0 0; color: #7c706b; font-size: .85rem; }
    .assigned-hero-actions { display: flex; align-items: center; gap: .8rem; }
    .task-panel-header-actions { display: flex; align-items: center; justify-content: flex-end; gap: .8rem; }
    .assigned-hero-icon { display: grid; place-items: center; width: 3.4rem; height: 3.4rem; border-radius: .85rem; background: #ffeadf; color: #c2410c; font-size: 1.3rem; }
    .task-panel { overflow: hidden; border: 1px solid #e8e1de; border-radius: 1rem; background: #fff; box-shadow: 0 7px 24px rgba(54, 38, 31, .055); }
    .task-panel-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.2rem 1.35rem; border-bottom: 1px solid #eee8e5; }
    .task-panel-header h3 { margin: 0; color: #342521; font-size: 1.05rem; font-weight: 750; }
    .task-panel-header p { margin: .25rem 0 0; color: #8a7d77; font-size: .78rem; }
    .task-count-badge { padding: .45rem .75rem; border-radius: 999px; background: #fff0e8; color: #c2410c; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    .task-table thead { background: #faf9f8; }
    .task-table thead th { position: sticky; top: 0; z-index: 2; background: #faf9f8; }
    .task-table thead th:nth-child(8), .task-table thead th:nth-child(9) { display: table-cell; }
    .task-table tbody tr:hover { background: #fffaf7; }
    .status-badge { display: inline-flex; align-items: center; border-radius: 999px; padding: .35rem .6rem; font-size: .7rem; font-weight: 700; white-space: nowrap; }
    .status-pending { background: #fff7df; color: #a16207; }
    .status-in_progress { background: #edf4ff; color: #2563eb; }
    .task-action-icon { display: inline-grid; place-items: center; width: 1.5rem; height: 1.5rem; border: 0; border-radius: .3rem; color: #fff; cursor: pointer; font-size: .6rem; }
    .task-actions { display: flex; align-items: center; flex-wrap: nowrap; gap: .3rem; white-space: nowrap; }
    .task-table th:nth-child(8), .task-table td:nth-child(8),
    .task-table th:nth-child(9), .task-table td:nth-child(9) { display: none; }
    .task-table th:last-child, .task-table td:last-child { min-width: 118px; }
    .task-panel td:last-child > div { display: flex; align-items: center; flex-wrap: nowrap; gap: .2rem; white-space: nowrap; }
    .task-action-icon.view { background: #475569; }
    .task-action-icon.start { background: #2563eb; }
    .task-action-icon.complete { background: #16a34a; }
    .task-action-icon.delete { background: #dc2626; }
    .task-action-icon:hover { filter: brightness(.9); }

    /* Keep the backdrop fixed while the form card owns the scroll. */
    .task-form-shell {
        display: grid;
        grid-template-columns: 1fr;
        align-items: center;
        justify-items: center;
        overflow: hidden;
        width: 100vw;
        height: 100vh;
        min-height: 100vh;
        max-width: none;
        box-sizing: border-box;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        margin: 0;
        padding: 1rem;
        border: 0;
        border-radius: 0;
        background: rgba(38, 25, 20, .58);
        box-shadow: none;
    }

    .task-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 340px));
        width: min(720px, calc(100vw - 2rem));
        min-width: 0;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: .8rem;
        background: #fff;
        box-shadow: 0 24px 60px rgba(38, 25, 20, .22);
        clip-path: inset(0 round .8rem);
    }

    @media (max-width: 767px) {
        .task-form-shell { grid-template-columns: minmax(0, 1fr); }
        .task-form-section-wide { grid-column: auto; }
        .task-form-heading, .task-form-actions { grid-column: 1; }
        .task-form-actions { justify-content: stretch; }
        .task-form-submit { width: 100%; }
        .task-details-grid { grid-template-columns: 1fr; }
        .task-detail-item.full { grid-column: auto; }
    }
</style>
<div class="assigned-page space-y-6">
    <div class="assigned-hero">
        <div>
            <p class="assigned-eyebrow">Housekeeping operations</p>
            <h2>Assigned Cleaning Tasks</h2>
            <p>Assign, start, and complete room cleaning work.</p>
        </div>
        <div class="assigned-hero-actions">
            <div class="assigned-hero-icon"><i class="fas fa-broom" aria-hidden="true"></i></div>
        </div>
    </div>

    @if (session('success'))<div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif

    <form id="task-form" method="POST" action="{{ route('housekeeping.tasks.store') }}" class="task-form-shell hidden">
        @csrf
        <div class="task-form-card">
        <div class="task-form-heading"><div><h3>New cleaning assignment</h3><p>Choose the room, service, schedule, and staff member.</p></div><button type="button" class="task-form-close" onclick="document.getElementById('task-form').classList.add('hidden')" aria-label="Close assign task form">&times;</button></div>
        <section class="task-form-section"><h4 class="task-form-section-title"><i class="fas fa-bed"></i>Room context</h4><label class="task-form-field">Room<select name="room_id" required>@foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }} - {{ $room->room_type }}</option>@endforeach</select></label><label class="task-form-field">Reservation<select name="reservation_id"><option value="">No reservation</option>@foreach($reservations as $reservation)<option value="{{ $reservation->id }}">RES-{{ $reservation->id }} - {{ $reservation->guest_name }} ({{ $reservation->number_of_guests ?? 0 }} guests)</option>@endforeach</select></label></section>
        <section class="task-form-section"><h4 class="task-form-section-title"><i class="fas fa-list-check"></i>Task details</h4><label class="task-form-field">Cleaning task<input name="task" required maxlength="255" placeholder="General Cleaning"></label><label class="task-form-field">Priority<select name="priority" required><option>low</option><option selected>medium</option><option>high</option><option>urgent</option></select></label></section>
        <section class="task-form-section"><h4 class="task-form-section-title"><i class="fas fa-calendar-days"></i>Schedule</h4><div class="grid gap-3 sm:grid-cols-2"><label class="task-form-field">Date<input type="date" name="scheduled_date" required value="{{ now()->toDateString() }}"></label><label class="task-form-field">Time<input type="time" name="scheduled_time"></label></div><label class="task-form-field">Estimated duration (minutes)<input type="number" name="estimated_duration" min="1" max="1440" placeholder="45"></label></section>
        <section class="task-form-section"><h4 class="task-form-section-title"><i class="fas fa-user-check"></i>Assignment</h4><label class="task-form-field">Housekeeping staff<select name="assigned_staff_id"><option value="">Unassigned</option>@foreach($staff as $person)<option value="{{ $person->id }}">{{ $person->name }}{{ $person->contact_no ? ' - '.$person->contact_no : '' }}</option>@endforeach</select></label></section>
        <section class="task-form-section task-form-section-wide"><h4 class="task-form-section-title"><i class="fas fa-note-sticky"></i>Notes</h4><label class="task-form-field">Special instructions<textarea name="notes" maxlength="5000" placeholder="Add instructions for the assigned staff member."></textarea></label></section>
        <div class="task-form-actions"><button type="button" class="task-form-cancel" onclick="document.getElementById('task-form').classList.add('hidden')">Cancel</button><button type="submit" class="task-form-submit"><i class="fas fa-check mr-2"></i>Save Cleaning Task</button></div>
        </div>
    </form>

    <div class="task-panel">
        <div class="task-panel-header"><div><h3>Cleaning Queue</h3><p>Track scheduled work and live cleaning progress.</p></div><div class="task-panel-header-actions"><span class="task-count-badge">{{ $tasks->count() }} task{{ $tasks->count() === 1 ? '' : 's' }}</span><button type="button" onclick="document.getElementById('task-form').classList.toggle('hidden')" class="rounded-lg bg-orange-500 px-4 py-2 font-semibold text-white shadow-sm hover:bg-orange-600"><i class="fas fa-plus mr-2"></i>Assign Task</button></div></div>
        <div class="overflow-x-auto"><table class="task-table min-w-[820px] w-full text-left text-sm"><thead class="text-xs uppercase text-gray-500"><tr><th class="p-4">Room</th><th class="p-4">Room Type</th><th class="p-4">Task</th><th class="p-4">Priority</th><th class="p-4">Assigned Staff</th><th class="p-4">Scheduled Date</th><th class="p-4">Scheduled Time</th><th class="p-4">Status</th><th class="p-4">Action</th></tr></thead>
        <tbody class="divide-y divide-gray-100">@forelse($tasks as $task)<tr>
            <td class="p-4 font-semibold">{{ $task->room->room_number }}</td><td class="p-4">{{ $task->room->room_type }}</td><td class="p-4">{{ $task->task }}</td><td class="p-4">{{ ucfirst($task->priority) }}</td><td class="p-4">{{ $task->assignedStaff?->name ?? 'Unassigned' }}</td><td class="p-4">{{ $task->scheduled_date->format('M d, Y') }}</td><td class="p-4">{{ $task->scheduled_time ? \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') : 'N/A' }}</td><td class="p-4">{{ $task->started_at?->format('M d, Y g:i A') ?? 'N/A' }}</td><td class="p-4">{{ $task->finished_at?->format('M d, Y g:i A') ?? 'N/A' }}</td><td class="p-4"><span class="status-badge status-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></td><td class="p-4"><div class="flex flex-wrap gap-2"><button type="button" class="task-action-icon view" title="View task details" aria-label="View task details" data-task-details="{{ e(json_encode(['occupancy' => $task->reservation?->number_of_guests ?? 'N/A', 'contact' => $task->assignedStaff?->contact_no ?? 'N/A', 'reference' => $task->reservation ? 'RES-'.$task->reservation->id : 'N/A', 'duration' => $task->estimated_duration ? $task->estimated_duration.' minutes' : 'N/A', 'notes' => $task->notes ?? 'N/A'])) }}"><i class="fas fa-eye"></i></button>@if($task->status === 'pending')<form method="POST" action="{{ route('housekeeping.tasks.start', $task) }}">@csrf @method('PATCH')<button class="task-action-icon start" title="Start cleaning" aria-label="Start cleaning"><i class="fas fa-play"></i></button></form><form method="POST" action="{{ route('housekeeping.tasks.destroy', $task) }}" onsubmit="return confirm('Delete this pending task?')">@csrf @method('DELETE')<button class="task-action-icon delete" title="Delete task" aria-label="Delete task"><i class="fas fa-trash"></i></button></form>@else<form method="POST" action="{{ route('housekeeping.tasks.complete', $task) }}">@csrf @method('PATCH')<button class="task-action-icon complete" title="Complete cleaning" aria-label="Complete cleaning"><i class="fas fa-check"></i></button></form>@endif</div></td>
        </tr>@empty<tr><td colspan="9" class="p-10 text-center text-gray-500">No assigned cleaning tasks.</td></tr>@endforelse</tbody></table></div>
    </div>
</div>

<div id="task-details-modal" class="task-details-modal" role="dialog" aria-modal="true" aria-labelledby="task-details-title">
    <div class="task-details-card">
        <div class="task-details-header"><h3 id="task-details-title">Task Details</h3><button type="button" class="task-details-close" aria-label="Close details">&times;</button></div>
        <div class="task-details-grid">
            <div class="task-detail-item"><span class="task-detail-label">Occupancy</span><span id="detail-occupancy" class="task-detail-value"></span></div>
            <div class="task-detail-item"><span class="task-detail-label">Contact Number</span><span id="detail-contact" class="task-detail-value"></span></div>
            <div class="task-detail-item"><span class="task-detail-label">Booking Reference</span><span id="detail-reference" class="task-detail-value"></span></div>
            <div class="task-detail-item"><span class="task-detail-label">Estimated Duration</span><span id="detail-duration" class="task-detail-value"></span></div>
            <div class="task-detail-item full"><span class="task-detail-label">Notes</span><span id="detail-notes" class="task-detail-value"></span></div>
        </div>
    </div>
</div>
<script>
    (() => {
        const modal = document.getElementById('task-details-modal');
        const closeButton = modal.querySelector('.task-details-close');
        const fields = {
            occupancy: document.getElementById('detail-occupancy'),
            contact: document.getElementById('detail-contact'),
            reference: document.getElementById('detail-reference'),
            duration: document.getElementById('detail-duration'),
            notes: document.getElementById('detail-notes')
        };

        function closeDetails() {
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('[data-task-details]').forEach((button) => {
            button.addEventListener('click', () => {
                const details = JSON.parse(button.dataset.taskDetails);
                Object.keys(fields).forEach((key) => { fields[key].textContent = details[key]; });
                modal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            });
        });

        closeButton.addEventListener('click', closeDetails);
        modal.addEventListener('click', (event) => { if (event.target === modal) closeDetails(); });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.getElementById('task-form').classList.add('hidden');
                closeDetails();
            }
        });
    })();
</script>
@endsection