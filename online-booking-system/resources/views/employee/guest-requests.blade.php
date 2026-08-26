@extends('employee.layout')

@section('pageTitle', 'Welcome, Employee!')

@section('content')
@php
    $requestCollection = collect($allRequests);
    $totalRequests = $requestCollection->count();
    $newRequests = $requestCollection->where('status', 'New')->count();
    $inProgressRequests = $requestCollection->where('status', 'In Progress')->count();
    $completedRequests = $requestCollection->where('status', 'Completed')->count();
    $assignedRequests = $requestCollection->whereNotNull('assigned_employee_id')->count();
    $visibleRequests = $requests;
@endphp

<style>
    .requests-page { max-width: 1160px; margin: 0 auto; color: #1f2937; }
    .requests-page h1, .requests-page h2, .requests-page h3, .requests-page p { margin: 0; }
    .requests-heading { margin-bottom: 1rem; }
    .requests-heading h1 { font-size: 1.75rem; font-weight: 700; color: #172238; }
    .requests-heading p { margin-top: .25rem; color: #64748b; font-size: .95rem; }
    .request-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .8rem; margin-bottom: 1rem; }
    .request-stat, .request-panel { background: #fff; border: 1px solid #e8edf2; border-radius: 10px; box-shadow: 0 5px 15px rgba(30, 41, 59, .06); }
    .request-stat { display: flex; align-items: center; gap: .75rem; padding: .85rem; min-height: 78px; }
    .stat-icon, .activity-icon { display: grid; place-items: center; flex: 0 0 2.5rem; width: 2.5rem; height: 2.5rem; border-radius: 10px; }
    .stat-icon.red { color: #dc2626; background: #fff0f1; } .stat-icon.amber { color: #c27800; background: #fff7df; }
    .stat-icon.blue { color: #3979d8; background: #edf4ff; } .stat-icon.green { color: #279c77; background: #eaf8f2; }
    .request-stat span { display: block; color: #64748b; font-size: .88rem; } .request-stat strong { display: block; margin-top: .15rem; font-size: 1.45rem; color: #172238; }
    .request-stat small { display: block; color: #64748b; font-size: .78rem; }
    .request-columns { display: grid; grid-template-columns: minmax(0, 1fr) 212px; gap: 1rem; align-items: start; }
    .request-panel { overflow: hidden; }
    .filters { display: grid; grid-template-columns: 1.5fr .8fr .9fr 1fr; gap: .65rem; padding: .8rem; border-bottom: 1px solid #edf0f3; }
    .filters input, .filters select { min-width: 0; border: 1px solid #dfe5ec; border-radius: 6px; padding: .6rem .7rem; color: #475569; background: #fff; font-size: .84rem; outline: none; }
    .filters input:focus, .filters select:focus { border-color: #dc2626; box-shadow: 0 0 0 2px #fee2e2; }
    .request-table-wrap { overflow-x: auto; } .request-table { width: 100%; min-width: 620px; border-collapse: collapse; }
    .request-table th { padding: .8rem .7rem; text-align: left; color: #64748b; background: #fbfcfd; font-size: .72rem; font-weight: 600; text-transform: uppercase; }
    .request-table td { padding: .85rem .7rem; border-top: 1px solid #edf0f3; vertical-align: middle; font-size: .82rem; }
    .request-id { color: #dc2626; font-weight: 700; } .guest-name, .request-name { font-weight: 600; color: #273449; }
    .muted { display: block; margin-top: .2rem; color: #64748b; font-size: .76rem; }
    .badge { display: inline-flex; align-items: center; gap: .3rem; border-radius: 5px; padding: .35rem .5rem; font-size: .74rem; white-space: nowrap; }
    .badge.blue { color: #2563eb; background: #edf4ff; } .badge.amber { color: #b7791f; background: #fff7df; } .badge.green { color: #168463; background: #eaf8f2; } .badge.purple { color: #7c3aed; background: #f4efff; }
    .priority { white-space: nowrap; } .priority:before { content: ''; display: inline-block; width: 6px; height: 6px; margin-right: .3rem; border-radius: 50%; background: #ef4444; } .priority.medium:before { background: #f59e0b; } .priority.low:before { background: #10b981; }
    .view-button { display: inline-flex; align-items: center; gap: .3rem; border: 1px solid #ef9a9a; border-radius: 5px; padding: .45rem .6rem; color: #b91c1c; background: #fff; font-size: .78rem; cursor: pointer; }
    .view-button:hover { background: #fff5f5; } .panel-footer { display: flex; justify-content: space-between; align-items: center; padding: .85rem 1rem; color: #64748b; font-size: .78rem; }
    .pagination { display: flex; gap: .3rem; } .pagination a, .pagination span { display: grid; place-items: center; width: 1.65rem; height: 1.65rem; border: 1px solid #e2e8f0; border-radius: 5px; color: #64748b; text-decoration: none; } .pagination a:hover { color: #c91c25; border-color: #ef9a9a; } .pagination .current { color: white; background: #c91c25; border-color: #c91c25; } .pagination .disabled { color: #cbd5e1; background: #f8fafc; }
    .side-panel { padding: 1rem; } .side-panel h3 { color: #a8191f; font-size: .9rem; text-transform: uppercase; } .side-panel h3 i { margin-right: .35rem; }
    .ring { width: 92px; height: 92px; margin: 1rem auto .8rem; border-radius: 50%; display: grid; place-items: center; background: conic-gradient(#c91c25 0 20%, #f5a623 20% 32%, #4d8de8 32% 39%, #42aa83 39% 100%); }
    .ring:after { content: ''; width: 61px; height: 61px; border-radius: 50%; background: #fff; grid-area: 1 / 1; } .ring-label { grid-area: 1 / 1; z-index: 1; text-align: center; } .ring-label strong { display: block; font-size: 1.35rem; color: #253247; } .ring-label span { color: #64748b; font-size: .5rem; }
    .legend { display: grid; gap: .65rem; padding-bottom: .6rem; } .legend-row { display: flex; justify-content: space-between; color: #64748b; font-size: .8rem; } .legend-row b { color: #334155; } .legend-dot { display: inline-block; width: 7px; height: 7px; margin-right: .35rem; border-radius: 50%; } .dot-red { background: #c91c25; } .dot-amber { background: #f5a623; } .dot-blue { background: #4d8de8; } .dot-green { background: #42aa83; }
    .activity-panel { margin-top: 1rem; } .activity-list { display: grid; gap: .85rem; margin-top: 1rem; } .activity { display: flex; gap: .65rem; align-items: flex-start; } .activity-icon { width: 2.1rem; height: 2.1rem; flex-basis: 2.1rem; color: #3478d2; background: #edf4ff; font-size: .85rem; } .activity strong { display: block; font-size: .78rem; color: #334155; } .activity span { display: block; margin-top: .2rem; color: #64748b; font-size: .74rem; }
    .activity-link { display: block; margin-top: 1rem; padding: .65rem; border: 1px solid #ef9a9a; border-radius: 5px; color: #b91c1c; text-align: center; font-size: .76rem; font-weight: 600; }
    .request-modal { position: fixed; inset: 0; z-index: 60; display: none; place-items: center; padding: 1rem; background: rgba(15, 23, 42, .55); }
    .request-modal.open { display: grid; } .request-modal-card { width: min(100%, 420px); padding: 1.25rem; border-radius: 10px; background: #fff; box-shadow: 0 20px 45px rgba(15, 23, 42, .2); } .request-modal-head { display: flex; justify-content: space-between; gap: 1rem; align-items: center; margin-bottom: 1rem; } .request-modal-head h2 { color: #172238; font-size: 1.1rem; } .request-modal-close { border: 0; color: #64748b; background: transparent; font-size: 1.25rem; cursor: pointer; } .request-modal-row { padding: .65rem 0; border-top: 1px solid #edf0f3; } .request-modal-row span { display: block; color: #64748b; font-size: .75rem; } .request-modal-row strong { display: block; margin-top: .2rem; color: #273449; font-size: .9rem; }
    .request-left { min-width: 0; }
    .departments { margin-top: 1rem; padding: .85rem; } .departments-heading { display: flex; align-items: center; gap: .5rem; margin-bottom: .75rem; color: #273449; font-size: .78rem; font-weight: 700; } .departments-heading i { color: #3979d8; }
    .department-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .65rem; } .department { display: flex; align-items: center; gap: .6rem; min-width: 0; padding: .75rem; border: 1px solid #edf0f3; border-radius: 8px; } .department i { display: grid; place-items: center; flex: 0 0 2rem; width: 2rem; height: 2rem; border-radius: 7px; color: #3979d8; background: #edf4ff; } .department strong { display: block; font-size: .82rem; } .department span { display: block; margin-top: .2rem; color: #64748b; font-size: .74rem; } .department a { display: block; margin-top: .45rem; color: #3979d8; font-size: .72rem; text-decoration: none; }
    @media (max-width: 900px) { .request-columns { grid-template-columns: 1fr; } .request-sidebar { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; } .activity-panel { margin-top: 0; } }
    @media (max-width: 680px) { .request-stats, .request-sidebar, .department-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filters { grid-template-columns: 1fr 1fr; } .filters input { grid-column: 1 / -1; } .panel-footer { align-items: flex-start; gap: .5rem; flex-direction: column; } }
</style>

<div class="requests-page">
    <div class="requests-heading"><h1>Guest Requests</h1><p>Manage and assign guest requests to the appropriate department.</p></div>

    <div class="request-stats">
        <div class="request-stat"><div class="stat-icon red"><i class="fas fa-comment-dots"></i></div><div><span>New Requests</span><strong>{{ $newRequests }}</strong><small>Needs review</small></div></div>
        <div class="request-stat"><div class="stat-icon amber"><i class="fas fa-user-clock"></i></div><div><span>Assigned</span><strong>{{ $assignedRequests }}</strong><small>Waiting for action</small></div></div>
        <div class="request-stat"><div class="stat-icon blue"><i class="fas fa-clock"></i></div><div><span>In Progress</span><strong>{{ $inProgressRequests }}</strong><small>Being handled</small></div></div>
        <div class="request-stat"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div><span>Completed</span><strong>{{ $completedRequests }}</strong><small>This month</small></div></div>
    </div>

    <div class="request-columns">
        <div class="request-left">
        <section id="request-table" class="request-panel">
            <div class="filters"><input type="search" placeholder="Search by guest name, room, or request ID..."><select><option>All Status</option></select><select><option>All Department</option></select><input type="text" value="May 1 - May 28, 2026" aria-label="Date range"></div>
            <div class="request-table-wrap"><table class="request-table"><thead><tr><th>Request ID</th><th>Guest Name</th><th>Room Number</th><th>Request Type</th><th>Preferred Time</th><th>Priority</th><th>Submitted Date/Time</th><th>Status</th><th>Action</th></tr></thead><tbody>
                @forelse($visibleRequests as $request)
                    @php
                        $status = $request->status ?? 'New';
                        $statusClass = $status === 'Completed' ? 'green' : ($status === 'In Progress' ? 'blue' : 'amber');
                        $room = $request->room ?? $request->reservation?->room;
                    @endphp
                    <tr><td><span class="request-id">REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span></td><td><span class="guest-name">{{ $request->guest?->name ?? $request->reservation?->guest_name ?? 'Guest' }}</span></td><td><span class="request-name">{{ $room?->room_number ?? 'N/A' }}</span></td><td><span class="request-name">{{ $request->request_type }}</span></td><td><span class="guest-name">{{ $request->preferred_time ?? 'Any time' }}</span></td><td><span class="priority {{ strtolower($request->priority) === 'low' ? 'low' : (strtolower($request->priority) === 'medium' ? 'medium' : '') }}">{{ $request->priority }}</span></td><td><span class="muted">{{ optional($request->submitted_at)->format('M d, Y g:i A') }}</span></td><td><span class="badge {{ $statusClass }}">{{ $status }}</span></td><td><a href="{{ route('employee.guest-requests.show', ['id' => $request->id]) }}" class="view-button"><i class="fas fa-eye"></i> View</a></td></tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;padding:2rem;color:#64748b;">No Employee requests found.</td></tr>
                @endforelse
            </tbody></table></div>
            <div class="panel-footer"><span>Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests</span><div class="pagination" aria-label="Guest request pages">
                @if ($requests->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lsaquo;</span>
                @else
                    <a href="{{ $requests->previousPageUrl() }}" aria-label="Previous page">&lsaquo;</a>
                @endif
                @for ($page = 1; $page <= $requests->lastPage(); $page++)
                    @if ($page === $requests->currentPage())
                        <span class="current" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $requests->url($page) }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endfor
                @if ($requests->hasMorePages())
                    <a href="{{ $requests->nextPageUrl() }}" aria-label="Next page">&rsaquo;</a>
                @else
                    <span class="disabled" aria-disabled="true">&rsaquo;</span>
                @endif
            </div></div>
        </section>

        <section class="request-panel departments"><div class="departments-heading"><i class="fas fa-sitemap"></i> Department Status Overview</div><div class="department-grid"><div class="department"><i class="fas fa-bed"></i><div><strong>Housekeeping</strong><span>{{ $totalRequests }} Active Requests</span><a href="#request-table">View Details <i class="fas fa-arrow-right"></i></a></div></div><div class="department"><i class="fas fa-utensils"></i><div><strong>Dining</strong><span>0 Active Requests</span><a href="#request-table">View Details <i class="fas fa-arrow-right"></i></a></div></div><div class="department"><i class="fas fa-wrench"></i><div><strong>Maintenance</strong><span>0 Active Requests</span><a href="#request-table">View Details <i class="fas fa-arrow-right"></i></a></div></div><div class="department"><i class="fas fa-users"></i><div><strong>Front Desk</strong><span>{{ $totalRequests }} Active Requests</span><a href="#request-table">View Details <i class="fas fa-arrow-right"></i></a></div></div></div></section>
        </div>

        <aside class="request-sidebar"><section class="request-panel side-panel"><h3><i class="fas fa-chart-bar"></i> Request Summary</h3><div class="ring"><div class="ring-label"><strong>{{ $totalRequests }}</strong></div></div><div class="legend"><div class="legend-row"><span><i class="legend-dot dot-red"></i>New</span><b>{{ $newRequests }}</b></div><div class="legend-row"><span><i class="legend-dot dot-amber"></i>Assigned</span><b>{{ $assignedRequests }}</b></div><div class="legend-row"><span><i class="legend-dot dot-blue"></i>In Progress</span><b>{{ $inProgressRequests }}</b></div><div class="legend-row"><span><i class="legend-dot dot-green"></i>Completed</span><b>{{ $completedRequests }}</b></div></div></section><section class="request-panel side-panel activity-panel"><h3><i class="fas fa-history"></i> Recent Activity</h3><div class="activity-list">@foreach($visibleRequests->take(4) as $request)<div class="activity"><div class="activity-icon"><i class="fas fa-bell"></i></div><div><strong>Request REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</strong><span>{{ optional($request->submitted_at)->format('M d, Y g:i A') }}</span></div></div>@endforeach</div><a class="activity-link" href="#request-table">View All Activity</a></section></aside>
    </div>

</div>

<div id="guestRequestModal" class="request-modal" role="dialog" aria-modal="true" aria-labelledby="guestRequestModalTitle">
    <div class="request-modal-card"><div class="request-modal-head"><h2 id="guestRequestModalTitle">Request Details</h2><button type="button" class="request-modal-close" aria-label="Close"><i class="fas fa-times"></i></button></div><div class="request-modal-row"><span>Request ID</span><strong id="guestRequestModalId"></strong></div><div class="request-modal-row"><span>Request</span><strong id="guestRequestModalRequest"></strong></div><div class="request-modal-row"><span>Status</span><strong id="guestRequestModalStatus"></strong></div><div class="request-modal-row"><span>Requested</span><strong id="guestRequestModalTime"></strong></div></div>
</div>
<script>
    document.querySelectorAll('.request-view-button').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('guestRequestModalId').textContent = button.dataset.requestId;
            document.getElementById('guestRequestModalRequest').textContent = button.dataset.requestTitle;
            document.getElementById('guestRequestModalStatus').textContent = button.dataset.requestStatus;
            document.getElementById('guestRequestModalTime').textContent = button.dataset.requestTime;
            document.getElementById('guestRequestModal').classList.add('open');
        });
    });
    document.querySelector('.request-modal-close').addEventListener('click', function () { document.getElementById('guestRequestModal').classList.remove('open'); });
    document.getElementById('guestRequestModal').addEventListener('click', function (event) { if (event.target === this) this.classList.remove('open'); });
</script>
@endsection
