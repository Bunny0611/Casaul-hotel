<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestLoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?Reservation $reservation,
        public \DateTimeInterface $loggedInAt,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Successful Login - Casaul Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-login-notification',
        );
    }
}
