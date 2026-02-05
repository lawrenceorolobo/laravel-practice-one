<?php

namespace App\Mail;

use App\Models\Assessment;
use App\Models\Invitee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Assessment $assessment;
    public Invitee $invitee;
    public string $testUrl;
    public array $calendarLinks;

    public function __construct(Assessment $assessment, Invitee $invitee)
    {
        $this->assessment = $assessment;
        $this->invitee = $invitee;
        $this->testUrl = url('/test/' . $invitee->access_token);
        $this->calendarLinks = $this->generateCalendarLinks();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're Invited: {$this->assessment->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }

    private function generateCalendarLinks(): array
    {
        $startDateTime = Carbon::parse($this->assessment->start_datetime);
        $endDateTime = $startDateTime->copy()->addMinutes($this->assessment->duration_minutes ?? 60);
        
        $title = urlencode($this->assessment->title);
        $description = urlencode("Assessment: {$this->assessment->title}\n\nTest Link: {$this->testUrl}");
        $location = urlencode($this->testUrl);
        
        // Google Calendar
        $googleStart = $startDateTime->format('Ymd\THis\Z');
        $googleEnd = $endDateTime->format('Ymd\THis\Z');
        $googleUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$googleStart}/{$googleEnd}&details={$description}&location={$location}";
        
        // Outlook Calendar
        $outlookStart = $startDateTime->toIso8601String();
        $outlookEnd = $endDateTime->toIso8601String();
        $outlookUrl = "https://outlook.live.com/calendar/0/deeplink/compose?subject={$title}&startdt={$outlookStart}&enddt={$outlookEnd}&body={$description}&location={$location}";
        
        // iCal (.ics) download - we'll generate this inline
        $icsContent = $this->generateIcsContent($startDateTime, $endDateTime);
        
        return [
            'google' => $googleUrl,
            'outlook' => $outlookUrl,
            'ics' => $icsContent,
        ];
    }
    
    private function generateIcsContent(Carbon $start, Carbon $end): string
    {
        $uid = uniqid('quizly-');
        $now = Carbon::now()->format('Ymd\THis\Z');
        $startFormatted = $start->format('Ymd\THis\Z');
        $endFormatted = $end->format('Ymd\THis\Z');
        
        return "BEGIN:VCALENDAR\r\n" .
               "VERSION:2.0\r\n" .
               "PRODID:-//Quizly//Assessment//EN\r\n" .
               "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$now}\r\n" .
               "DTSTART:{$startFormatted}\r\n" .
               "DTEND:{$endFormatted}\r\n" .
               "SUMMARY:{$this->assessment->title}\r\n" .
               "DESCRIPTION:Assessment Invitation\\n\\nTest Link: {$this->testUrl}\r\n" .
               "URL:{$this->testUrl}\r\n" .
               "END:VEVENT\r\n" .
               "END:VCALENDAR";
    }
}
