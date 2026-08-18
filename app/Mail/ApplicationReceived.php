<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public bool $forFounder = false,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->forFounder
                ? 'New membership application — ' . $this->application->name
                : 'We received your application — StellarTech Explorers Club',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: $this->forFounder
            ? 'emails.application-founder'
            : 'emails.application-received');
    }
}
