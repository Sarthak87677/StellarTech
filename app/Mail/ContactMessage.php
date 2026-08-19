<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        // Sent by connect@, delivered to director@ by the caller, and
        // replying goes straight back to the person who wrote in.
        return new Envelope(
            from: new Address(config('mail.system_address'), config('mail.from.name')),
            subject: 'Website enquiry: ' . $this->data['subject'],
            replyTo: [$this->data['email']],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contact');
    }
}
