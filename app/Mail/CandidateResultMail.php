<?php

namespace App\Mail;

use App\Models\Assessment;
use App\Models\Invitee;
use App\Models\TestSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assessment $assessment;
    public Invitee $invitee;
    public TestSession $session;
    public string $candidateName;

    public function __construct(Assessment $assessment, Invitee $invitee, TestSession $session)
    {
        $this->assessment = $assessment;
        $this->invitee = $invitee;
        $this->session = $session;
        $this->candidateName = trim(($invitee->first_name ?? '') . ' ' . ($invitee->last_name ?? '')) ?: 'Candidate';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Assessment Result: {$this->assessment->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidate-result',
        );
    }

    public function attachments(): array
    {
        $this->assessment->load(['questions.options']);
        $this->session->load('answers');

        $pdf = Pdf::loadView('pdf.candidate-report', [
            'assessment' => $this->assessment,
            'invitee' => $this->invitee,
            'session' => $this->session,
            'candidateName' => $this->candidateName,
        ]);

        $filename = str_replace(' ', '_', $this->assessment->title) . '_Your_Result.pdf';

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                $filename
            )->withMime('application/pdf'),
        ];
    }
}
