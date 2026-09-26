<x-mail::message>
<div style="margin: 0 0 22px; overflow: hidden; border-radius: 8px;">
    <img src="{{ asset('image/Royal-Suite-room.jpg') }}" alt="A room at Casaul Hotel" width="570" style="display: block; width: 100%; max-width: 570px; height: auto; border: 0;">
</div>

<p style="margin: 0 0 8px; color: #cc0925; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Reservation Update</p>
<h1 style="margin: 0 0 14px; color: #1f3551; font-size: 28px; line-height: 1.25;">Reservation Confirmed!</h1>

<p style="margin: 0 0 12px; color: #263548;">Hello {{ $reservation->guest_name }},</p>
<p style="margin: 0 0 22px; color: #5b6572; line-height: 1.65;">Thank you for choosing Casaul Hotel. Your reservation has been successfully confirmed, and we are pleased to be part of your upcoming stay.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 22px; border: 1px solid #e7e9ed; border-radius: 8px; background-color: #f8f9fb;">
    <tr>
        <td style="padding: 18px 20px 8px; color: #1f3551; font-size: 16px; font-weight: 700;">Reservation Details</td>
    </tr>
    <tr>
        <td style="padding: 0 20px 18px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="color: #4b5563; font-size: 14px; line-height: 1.5;">
                <tr><td style="padding: 7px 0; color: #737d89;">Reservation ID</td><td align="right" style="padding: 7px 0; color: #1f2937; font-weight: 700;">#{{ $reservation->id }}</td></tr>
                <tr><td style="padding: 7px 0; color: #737d89;">Check-in</td><td align="right" style="padding: 7px 0; color: #1f2937; font-weight: 600;">{{ optional($reservation->check_in)->format('F j, Y') ?? 'N/A' }}</td></tr>
                <tr><td style="padding: 7px 0; color: #737d89;">Check-out</td><td align="right" style="padding: 7px 0; color: #1f2937; font-weight: 600;">{{ optional($reservation->check_out)->format('F j, Y') ?? 'N/A' }}</td></tr>
                @if ($reservation->number_of_guests)
                    <tr><td style="padding: 7px 0; color: #737d89;">Guests</td><td align="right" style="padding: 7px 0; color: #1f2937; font-weight: 600;">{{ $reservation->number_of_guests }}</td></tr>
                @endif
                @if ($reservation->room?->room_type)
                    <tr><td style="padding: 7px 0; color: #737d89;">Room Type</td><td align="right" style="padding: 7px 0; color: #1f2937; font-weight: 600;">{{ $reservation->room->room_type }}</td></tr>
                @endif
                <tr><td style="padding: 10px 0 0; border-top: 1px solid #e3e6eb; color: #1f3551; font-weight: 700;">Total Amount</td><td align="right" style="padding: 10px 0 0; border-top: 1px solid #e3e6eb; color: #cc0925; font-size: 16px; font-weight: 700;">₱{{ number_format((float) ($reservation->total_amount ?? 0), 2) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

@if ($reservation->room_check_in_time || $reservation->check_in_time || $reservation->room_check_out_time || $reservation->check_out_time)
    <p style="margin: 0 0 7px; color: #1f3551; font-size: 15px; font-weight: 700;">Stay Information</p>
    @if ($reservation->room_check_in_time || $reservation->check_in_time)
        <p style="margin: 0 0 5px; color: #5b6572; line-height: 1.5;">Check-in time: {{ $reservation->room_check_in_time ?? $reservation->check_in_time }}</p>
    @endif
    @if ($reservation->room_check_out_time || $reservation->check_out_time)
        <p style="margin: 0 0 14px; color: #5b6572; line-height: 1.5;">Check-out time: {{ $reservation->room_check_out_time ?? $reservation->check_out_time }}</p>
    @endif
@endif

<p style="margin: 0 0 8px; color: #5b6572; line-height: 1.65;">If you have questions or need assistance with your reservation, please contact the Casaul Hotel team.</p>
<p style="margin: 0 0 18px; color: #1f3551; font-weight: 700;">We look forward to welcoming you!</p>
<p style="margin: 0; color: #5b6572; line-height: 1.6;">Regards,<br><strong style="color: #1f3551;">Casaul Hotel Team</strong></p>
</x-mail::message>