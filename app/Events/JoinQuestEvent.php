<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinQuestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }

    public function broadcastAs(): string
    {
        return 'quest-joined';
    }

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->data['participant_id']),
        ];
 
        if (
            isset($this->data['creator_id']) &&
            $this->data['creator_id'] !== $this->data['participant_id']
        ) {
            $channels[] = new PrivateChannel('user.' . $this->data['creator_id']);
        }
 
        return $channels;
    }
}
