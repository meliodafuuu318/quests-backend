<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptFriendRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Broadcast on:
     *   - private "user.{acceptorId}"  → acceptor's friends tab refreshes
     *   - private "user.{requesterId}" → requester gets accepted-request notification
     *                                    and their own friends tab refreshes
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->data['acceptor_id']),
            new PrivateChannel('user.' . $this->data['requester_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'friend-request-accepted';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}