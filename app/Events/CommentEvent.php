<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Broadcast on:
     *   - public "post.{postId}"        channel (anyone viewing that post sees the new comment)
     *   - private "user.{postOwnerId}"  channel (owner gets a notification badge)
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('post.' . $this->data['post_id']),
        ];

        // Only notify the owner when the commenter is someone else
        if (isset($this->data['post_owner_id']) &&
            $this->data['post_owner_id'] !== $this->data['commenter_id']) {
            $channels[] = new PrivateChannel('user.' . $this->data['post_owner_id']);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'new-comment';
    }

    public function broadcastWith(): array
    {
        return $this->data;
    }
}