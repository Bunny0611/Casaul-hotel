<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public object $reservation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Casaul Hotel reservation is confirmed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation-confirmed',
        );
    }
}