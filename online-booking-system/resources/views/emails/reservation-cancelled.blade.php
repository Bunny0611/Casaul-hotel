<x-mail::message>
@php
	$roomImagePath = ltrim((string) $reservation->room?->image, '/');
	if (str_starts_with($roomImagePath, 'storage/')) {
		$roomImagePath = substr($roomImagePath, 8);
	}
	$roomImageUrl = $roomImagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($roomImagePath)
		? asset('storage/' . $roomImagePath)
		: asset('image/Royal-Suite-room.jpg');
@endphp

@if ($reservation->room)
<div style="margin: 0 0 22px; overflow: hidden; border-radius: 8px;">
	<img src="{{ $roomImageUrl }}" alt="Room {{ $reservation->room->room_number }}" width="570" style="display: block; width: 100%; max-width: 570px; height: auto; border: 0;">
</div>
@endif

# Reservation Cancelled

Hello {{ $reservation->guest_name }},

Your Casaul Hotel reservation has been cancelled by our team. Here are the reservation details for your records.

**Reservation ID:** #{{ $reservation->id }}  
**Check-in:** {{ optional($reservation->check_in)->format('F j, Y') ?? 'N/A' }}  
**Check-out:** {{ optional($reservation->check_out)->format('F j, Y') ?? 'N/A' }}
@if($reservation->room?->room_number)
**Room:** {{ $reservation->room->room_number }}
@endif

If you believe this cancellation was made in error or need assistance, please contact Casaul Hotel.

Regards,  
Casaul Hotel Team
</x-mail::message>