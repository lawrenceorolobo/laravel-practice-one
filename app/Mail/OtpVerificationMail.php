<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $otp
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                env('MAIL_AUTH_FROM', config('mail.from.address')),
                env('MAIL_AUTH_NAME', config('mail.from.name'))
            ),
            subject: 'Your Quizly verification code: ' . $this->otp,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-verification',
            with: [
                'firstName' => $this->user->first_name,
                'otp' => $this->otp,
            ],
        );
    }
}
