<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $subject,
        public string $body,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                env('MAIL_CONTACT_FROM', config('mail.from.address')),
                env('MAIL_CONTACT_NAME', config('mail.from.name'))
            ),
            subject: "Contact Form: {$this->subject}",
            replyTo: [$this->senderEmail],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            with: [
                'senderName' => $this->senderName,
                'senderEmail' => $this->senderEmail,
                'contactSubject' => $this->subject,
                'body' => $this->body,
            ],
        );
    }
}
