<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $assessmentId;
    public string $userId;  // assessment owner
    public string $inviteeEmail;

    public function __construct(string $assessmentId, string $userId, string $inviteeEmail)
    {
        $this->assessmentId = $assessmentId;
        $this->userId = $userId;
        $this->inviteeEmail = $inviteeEmail;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('assessment.' . $this->assessmentId),
            new PrivateChannel('user.' . $this->userId),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'assessment_id' => $this->assessmentId,
            'invitee_email' => $this->inviteeEmail,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
