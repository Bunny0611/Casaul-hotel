@extends('app')

@section('content')

<div class="profile-page">
    <section class="profile-hero">
        <p class="eyebrow">My Account</p>
        <h1>My Records</h1>
        <p>Here is a list of all your reservations.</p>
    </section>

    <div class="records-toolbar">
        <a href="{{ route('guest.profile') }}" class="btn btn-back">&larr; Back to Profile</a>
    </div>

    @if($reservations->isEmpty())
        <div class="records-empty">
            <i class="fas fa-inbox"></i>
            <h3>No reservations yet</h3>
            <p>You haven't made any reservations. Book a room to get started!</p>
            <a href="{{ route('reservation') }}" class="btn">Make a Reservation</a>
        </div>
    @else
        <div class="records-table-wrap">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Request</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $index => $reservation)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $reservation->room->room_type ?? 'Room' }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_in)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->check_out)->format('M d, Y') }}</td>
                            <td>₱{{ number_format($reservation->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $reservation->status }}">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </td>
                            <td>{{ $reservation->special_requests ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
