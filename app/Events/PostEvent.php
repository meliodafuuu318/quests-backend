<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;
    public array $friendIds; // IDs of friends who should get a private notification

    public function __construct(array $data, array $friendIds = [])
    {
        $this->data      = $data;
        $this->friendIds = $friendIds;
    }

    /**
     * Broadcast on:
     *   - public  "feed"              channel (all users see new-post signal)
     *   - private "user.{id}"         channel for each friend (friends-only badge)
     */
    public function broadcastOn(): array
    {
        $channels = [new Channel('feed')];

        foreach ($this->friendIds as $friendId) {
            $channels[] = new PrivateChannel('user.' . $friendId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'new-post';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}