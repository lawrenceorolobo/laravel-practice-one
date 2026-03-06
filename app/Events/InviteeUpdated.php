<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InviteeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $assessmentId;
    public string $action; // added, removed, status_changed, email_sent

    public function __construct(string $assessmentId, string $action = 'updated')
    {
        $this->assessmentId = $assessmentId;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('assessment.' . $this->assessmentId),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'assessment_id' => $this->assessmentId,
            'action' => $this->action,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
