@extends('employee.layout')

@section('pageTitle', 'Guest Request Details')

@section('content')
@php
    $status = $guestRequest->status;
    $guestName = $guestRequest->guest?->name ?? $guestRequest->reservation?->guest_name ?? 'Guest';
    $room = $guestRequest->room ?? $guestRequest->reservation?->room;
    $roomNumber = $room?->room_number ?? 'N/A';
    $isComplete = $status === 'Completed';
@endphp

<style>
    .detail-page { max-width: 1080px; margin: 0 auto; color: #273449; }
    .detail-back { display: inline-flex; align-items: center; gap: .45rem; margin-bottom: 1rem; color: #b91c1c; font-size: .9rem; text-decoration: none; }
    .detail-heading { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1rem; }
    .detail-heading h1 { color: #172238; font-size: 1.7rem; font-weight: 700; } .detail-heading p { margin-top: .3rem; color: #64748b; font-size: .9rem; }
    .detail-status { border-radius: 999px; padding: .5rem .8rem; color: {{ $isComplete ? '#168463' : '#b7791f' }}; background: {{ $isComplete ? '#eaf8f2' : '#fff7df' }}; font-size: .8rem; font-weight: 600; white-space: nowrap; }
    .detail-form { padding: 1.15rem; } .detail-form label { display: block; margin-bottom: .35rem; color: #64748b; font-size: .75rem; } .detail-form select, .detail-form textarea { width: 100%; border: 1px solid #dfe5ec; border-radius: 6px; padding: .6rem; color: #273449; font-size: .85rem; }
    .detail-grid { display: grid; grid-template-columns: minmax(0, 1fr) 250px; gap: 1rem; align-items: start; }
    .detail-panel { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; box-shadow: 0 5px 15px rgba(30, 41, 59, .06); }
    .detail-panel + .detail-panel { margin-top: 1rem; } .detail-panel-title { display: flex; align-items: center; gap: .5rem; padding: 1rem 1.15rem; border-bottom: 1px solid #edf0f3; color: #a8191f; font-size: .85rem; font-weight: 700; text-transform: uppercase; } .detail-panel-title i { font-size: .8rem; }
    .reservation-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding: 1.15rem; } .detail-label { display: block; color: #64748b; font-size: .72rem; text-transform: uppercase; } .detail-value { display: block; margin-top: .35rem; color: #273449; font-size: .9rem; font-weight: 600; }
    .addon-table { width: 100%; border-collapse: collapse; } .addon-table th { padding: .75rem 1.15rem; color: #64748b; background: #fbfcfd; font-size: .7rem; text-align: left; text-transform: uppercase; } .addon-table td { padding: .9rem 1.15rem; border-top: 1px solid #edf0f3; font-size: .85rem; } .addon-name { color: #7f3030; font-weight: 700; } .addon-note { display: block; margin-top: .2rem; color: #64748b; font-size: .75rem; } .addon-status { display: inline-block; border-radius: 5px; padding: .4rem .55rem; color: {{ $isComplete ? '#168463' : '#b7791f' }}; background: {{ $isComplete ? '#eaf8f2' : '#fff7df' }}; font-size: .76rem; }
    .summary-body { padding: 1rem 1.15rem; } .summary-number { display: flex; align-items: center; gap: .75rem; padding: 1rem; border-radius: 9px; background: #f8fafc; } .summary-number i { color: #3979d8; font-size: 1.5rem; } .summary-number span { display: block; color: #64748b; font-size: .72rem; } .summary-number strong { display: block; margin-top: .2rem; color: #172238; font-size: 1.45rem; } .summary-row { padding: .85rem 0; border-bottom: 1px solid #edf0f3; } .summary-row:last-child { border-bottom: 0; } .summary-row span { display: block; color: #64748b; font-size: .72rem; text-transform: uppercase; } .summary-row strong { display: block; margin-top: .3rem; color: #273449; font-size: .84rem; } .detail-action { display: block; width: 100%; margin-top: 1rem; border: 1px solid #e38a8a; border-radius: 6px; padding: .7rem; color: #b91c1c; background: #fff; font-size: .8rem; font-weight: 600; cursor: pointer; }
    @media (max-width: 760px) { .detail-grid { grid-template-columns: 1fr; } .reservation-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 520px) { .detail-heading { flex-direction: column; } .reservation-grid { grid-template-columns: 1fr; } .addon-table { min-width: 560px; } .detail-table-wrap { overflow-x: auto; } }
</style>

<div class="detail-page">
    <a class="detail-back" href="{{ route('employee.guest-requests') }}"><i class="fas fa-arrow-left"></i> Back to Guest Requests</a>
    <div class="detail-heading"><div><h1>{{ $guestRequest->request_type }}</h1><p>View and manage guest requested service items.</p></div><span class="detail-status">Status: {{ $status }}</span></div>
    <div class="detail-grid">
        <div>
            <section class="detail-panel"><div class="detail-panel-title"><i class="fas fa-clipboard-list"></i> Guest Information</div><div class="reservation-grid"><div><span class="detail-label">Guest Name</span><span class="detail-value">{{ $guestName }}</span></div><div><span class="detail-label">Room Number</span><span class="detail-value">{{ $roomNumber }}</span></div><div><span class="detail-label">Reservation ID</span><span class="detail-value">{{ $guestRequest->reservation_id ? 'RES-' . str_pad($guestRequest->reservation_id, 4, '0', STR_PAD_LEFT) : 'N/A' }}</span></div></div></section>
            <section class="detail-panel"><div class="detail-panel-title"><i class="fas fa-concierge-bell"></i> Request Information</div><div class="reservation-grid"><div><span class="detail-label">Request ID</span><span class="detail-value">REQ-{{ str_pad($guestRequest->id, 4, '0', STR_PAD_LEFT) }}</span></div><div><span class="detail-label">Request Type</span><span class="detail-value">{{ $guestRequest->request_type }}</span></div><div><span class="detail-label">Preferred Time</span><span class="detail-value">{{ $guestRequest->preferred_time ?? 'Any time' }}</span></div><div><span class="detail-label">Priority</span><span class="detail-value">{{ $guestRequest->priority }}</span></div><div><span class="detail-label">Submitted Date/Time</span><span class="detail-value">{{ optional($guestRequest->submitted_at)->format('M d, Y g:i A') }}</span></div><div><span class="detail-label">Department</span><span class="detail-value">{{ $guestRequest->department }}</span></div></div><div class="detail-form"><span class="detail-label">Full Description</span><p class="detail-value">{{ $guestRequest->description }}</p></div></section>
        </div>
        <aside><section class="detail-panel"><div class="detail-panel-title"><i class="fas fa-user-cog"></i> Handling</div><form class="detail-form" method="POST" action="{{ route('employee.guest-requests.update', $guestRequest->id) }}">@csrf @method('PATCH')<div class="summary-row"><span>Assigned Employee</span><select name="assigned_employee_id"><option value="">Unassigned</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" {{ $guestRequest->assigned_employee_id === $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>@endforeach</select></div><div class="summary-row"><label for="employee-notes">Employee Notes</label><textarea id="employee-notes" name="employee_notes" rows="4">{{ old('employee_notes', $guestRequest->employee_notes) }}</textarea></div><div class="summary-row"><label for="request-status">Status</label><select id="request-status" name="status"><option value="New" {{ $status === 'New' ? 'selected' : '' }}>New</option><option value="In Progress" {{ $status === 'In Progress' ? 'selected' : '' }}>In Progress</option><option value="Completed" {{ $status === 'Completed' ? 'selected' : '' }}>Completed</option></select></div><button class="detail-action" type="submit">Save Request Updates <i class="fas fa-arrow-right"></i></button></form></section></aside>
    </div>
</div>
@endsection