<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFriendEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Broadcast on:
     *   - private "user.{senderId}"    → sender's sent-requests tab refreshes
     *   - private "user.{receiverId}"  → receiver gets a friend-request notification
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->data['sender_id']),
            new PrivateChannel('user.' . $this->data['receiver_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'friend-request-sent';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}