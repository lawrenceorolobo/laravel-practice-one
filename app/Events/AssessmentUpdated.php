<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssessmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $userId;
    public string $action; // created, updated, published, deleted

    public function __construct(string $userId, string $action = 'updated')
    {
        $this->userId = $userId;
        $this->action = $action;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
