<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quizly subscription is active',
        );
    }

    public function content(): Content
    {
        $user = $this->payment->user;
        $plan = $this->payment->plan;

        return new Content(
            view: 'emails.subscription-confirmation',
            with: [
                'firstName' => $user->first_name,
                'planName' => $plan->name ?? 'Professional',
                'amount' => number_format($this->payment->amount),
                'currency' => $this->payment->currency ?? 'NGN',
                'expiresAt' => $user->subscription_expires_at?->format('M d, Y'),
                'dashboardUrl' => url('/dashboard'),
            ],
        );
    }
}
