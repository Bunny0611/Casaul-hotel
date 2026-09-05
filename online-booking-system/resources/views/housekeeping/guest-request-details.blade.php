@extends('housekeeping.layout')

@section('content')
@php
    $requestData = $requestData ?? ['requestId' => 'REQ-' . str_pad($request->id, 4, '0', STR_PAD_LEFT), 'guest' => $request->guest?->name ?? $request->reservation?->guest_name ?? 'Guest', 'room' => $request->room ? ($request->room->room_type ? $request->room->room_type . ' - ' . $request->room->room_number : $request->room->room_number) : 'Room info unavailable', 'requestType' => $request->request_type, 'description' => $request->description, 'status' => $request->status, 'preferredTime' => $request->preferred_time ? date('g:i A', strtotime($request->preferred_time)) : 'Not specified', 'priority' => $request->priority, 'submitted' => $request->submitted_at ? $request->submitted_at->format('M d, Y \a\t g:i A') : '—'];
@endphp

<div style="max-width: 900px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
    <a href="{{ route('housekeeping.guest-requests') }}" style="display:inline-block; margin-bottom:1rem; color:#a31d1d; text-decoration:none; font-weight:600;">&larr; Back to Guest Requests</a>

    <h1 style="margin:0 0 1rem; color:#2b3a4d; font-size:2rem;">Housekeeping Add-On Request</h1>

    <div style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; align-items:center;">
        <span style="background:#f0f4f8; padding:0.5rem 1rem; border-radius:8px; font-weight:600; display:inline-block;"><strong>{{ $requestData['requestId'] }}</strong> • {{ $requestData['guest'] }} • {{ $requestData['room'] }} • <span style="background:#06b6d4; color:white; padding:0.25rem 0.75rem; border-radius:4px; white-space:nowrap; display:inline-block;">{{ $requestData['status'] }}</span> • Priority: <span style="color:#ef4444; font-weight:700;">{{ $requestData['priority'] }}</span> • {{ $requestData['preferredTime'] }}</span>
    </div>

    <div style="border-top:1px solid #edf0f3; padding-top:1rem;">
        <h3 style="margin:0 0 .5rem; color:#2b3a4d;">Request Type</h3>
        <p style="margin:0 0 1rem; font-size:1.1rem; font-weight:600; color:#172238;">{{ $requestData['requestType'] }}</p>

        <h3 style="margin:0 0 .5rem; color:#2b3a4d;">Guest Description</h3>
        <p style="margin:0; line-height:1.6; color:#475569;">{{ $requestData['description'] ?? 'No note provided' }}</p>
    </div>
</div>
@endsection
