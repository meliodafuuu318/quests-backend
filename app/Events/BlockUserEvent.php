<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlockUserEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Broadcast on:
     *   - private "user.{blockerId}"  → blocker's feed purges blocked user's posts
     *   - private "user.{blockedId}"  → blocked user's feed purges blocker's posts
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->data['blocker_id']),
            new PrivateChannel('user.' . $this->data['blocked_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user-blocked';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}