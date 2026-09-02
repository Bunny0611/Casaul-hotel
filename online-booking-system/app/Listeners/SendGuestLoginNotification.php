<?php

namespace App\Listeners;

use App\Events\GuestLoggedIn;
use App\Mail\GuestLoginNotification;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendGuestLoginNotification
{
    public function handle(GuestLoggedIn $event): void
    {
        $reservation = Reservation::with('room')
            ->where('guest_email', $event->user->email)
            ->latest('id')
            ->first();

        try {
            Mail::to($event->user->email)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->send(new GuestLoginNotification($event->user, $reservation, $event->loggedInAt));
        } catch (\Throwable $exception) {
            Log::error('Guest login notification could not be sent.', [
                'recipient' => $event->user->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
