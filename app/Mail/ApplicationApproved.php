<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Carries a one-time password. It is generated at approval, sent once,
 * and never stored anywhere in readable form — the database holds only
 * the hash. The member is told to change it on first sign-in.
 */
class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public string $temporaryPassword,
    ) {}

    public function envelope(): Envelope
    {
        // Member-facing -> sent from the system mailbox (connect@).
        return new Envelope(
            from: new Address(config('mail.system_address'), config('mail.from.name')),
            subject: 'Your StellarTech Explorers Club account is ready',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.application-approved');
    }
}
