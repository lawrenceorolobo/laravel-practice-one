<?php

namespace App\Jobs;

use App\Mail\InvitationMail;
use App\Models\Assessment;
use App\Models\Invitee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvitationBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public int $backoff = 30;

    public function __construct(
        public string $assessmentId,
    ) {}

    public function handle(): void
    {
        $assessment = Assessment::findOrFail($this->assessmentId);

        Invitee::where('assessment_id', $this->assessmentId)
            ->where('status', 'pending')
            ->whereIn('email_status', ['pending', 'failed'])
            ->chunkById(100, function ($invitees) use ($assessment) {
                foreach ($invitees as $invitee) {
                    try {
                        $invitee->update(['email_status' => 'queued']);
                        Mail::to($invitee->email)->send(new InvitationMail($assessment, $invitee));
                        $invitee->update([
                            'email_status' => 'sent',
                            'email_sent_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        $invitee->update(['email_status' => 'failed']);
                        Log::warning("Failed to send invitation to {$invitee->email}: " . $e->getMessage());
                    }
                    usleep(100000); // 100ms throttle per email
                }
            });
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Invitation batch job failed', [
            'assessment_id' => $this->assessmentId,
            'error' => $e->getMessage(),
        ]);
    }
}
