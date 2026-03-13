<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestCompletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Broadcast on:
     *   - private "user.{participantUserId}"
     *     → participant's quests tab reflects the new task/quest status in real-time
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->data['participant_user_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'quest-progress';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}