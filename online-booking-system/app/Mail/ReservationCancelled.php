<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public object $reservation)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'An update about your Casaul Hotel reservation',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservation-cancelled',
        );
    }
}