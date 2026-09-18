@extends('housekeeping.layout')

@section('content')
@php
    $requestData = $requestData ?? ['requestId' => 'REQ-' . str_pad($request->id, 4, '0', STR_PAD_LEFT), 'guest' => $request->guest?->name ?? $request->reservation?->guest_name ?? 'Guest', 'room' => $request->room ? ($request->room->room_type ? $request->room->room_type . ' - ' . $request->room->room_number : $request->room->room_number) : 'Room info unavailable', 'requestType' => $request->request_type, 'description' => $request->description, 'status' => $request->status, 'preferredTime' => $request->preferred_time ? date('g:i A', strtotime($request->preferred_time)) : 'Not specified', 'priority' => $request->priority, 'submitted' => $request->submitted_at ? $request->submitted_at->format('M d, Y \a\t g:i A') : '—'];
@endphp

<div style="max-width: 900px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
    <a href="{{ route('housekeeping.guest-requests') }}" style="display:inline-block; margin-bottom:1rem; color:#a31d1d; text-decoration:none; font-weight:600;">&larr; Back to Guest Requests</a>

    <h1 style="margin:0 0 1rem; color:#2b3a4d; font-size:2rem;">Housekeeping Add-On Request</h1>

    <div style="display:grid; gap:0.75rem; padding:1rem 1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:1.5rem;">
        <div><strong>Guest:</strong> {{ $requestData['guest'] }}</div>
        <div><strong>Room:</strong> {{ $requestData['room'] }}</div>
        <div><strong>Reservation:</strong> {{ $requestData['reservation'] }}</div>
        <div><strong>Request Category:</strong> {{ $requestData['requestCategory'] }}</div>
        <div><strong>Request:</strong> {{ $requestData['requestType'] }}</div>
        <div><strong>Quantity:</strong> {{ $requestData['quantity'] }}</div>
        <div><strong>Unit Price:</strong> {{ $requestData['unitPriceFormatted'] }}</div>
        <div><strong>Subtotal:</strong> {{ $requestData['subtotalFormatted'] }}</div>
        <div><strong>Status:</strong> {{ $requestData['statusLabel'] }}</div>
    </div>

    <div style="border-top:1px solid #edf0f3; padding-top:1rem;">
        <h3 style="margin:0 0 .5rem; color:#2b3a4d;">Guest Description</h3>
        <p style="margin:0; line-height:1.6; color:#475569;">{{ $requestData['description'] ?? 'No note provided' }}</p>
    </div>
</div>
@endsection
