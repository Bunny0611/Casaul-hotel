<h1>Reservation Confirmed</h1>

<p>Hello {{ $reservation->guest_name }},</p>

<p>Your reservation at Casaul Hotel has been confirmed.</p>

<p><strong>Reservation details</strong></p>
<ul>
    <li>Check-in: {{ optional($reservation->check_in)->format('F j, Y') ?? 'N/A' }}</li>
    <li>Check-out: {{ optional($reservation->check_out)->format('F j, Y') ?? 'N/A' }}</li>
    <li>Total amount: ₱{{ number_format((float) ($reservation->total_amount ?? 0), 2) }}</li>
</ul>

<p>We look forward to welcoming you.</p>

<p>Casaul Hotel</p>