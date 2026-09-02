<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Successful Login - Casaul Hotel</title>
</head>
<body>
    <p><strong>CASAUL HOTEL</strong></p>

    <p>Hello {{ $user->name }},</p>

    <p>You have successfully signed in to your Casaul Hotel Guest Portal account.</p>

    <p><strong>Login Details:</strong></p>
    <p>
        Email: {{ $user->email }}<br>
        Date: {{ $loggedInAt->format('F j, Y') }}<br>
        Time: {{ $loggedInAt->format('g:i A') }}
    </p>

    @if ($reservation)
        <p>
            @if ($reservation->room)
                Room Number: {{ $reservation->room->room_number }}<br>
            @endif
            Reservation ID: {{ $reservation->id }}
        </p>
    @endif

    <p>For security purposes, if you did not perform this login, please contact Casaul Hotel.</p>

    <p>Thank you,<br>Casaul Hotel</p>
</body>
</html>
