<?php

namespace App\Repositories\User\Friend;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend,
    Notification
};
use App\Events\AddFriendEvent;

class SendFriendRequestRepository extends BaseRepository
{
    public function execute($request)
    {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if ($request->has('username')) {
            $friend = User::where('username', $request->username)->first();

            if (!$friend) {
                return $this->error('User not found', 404);
            }

            $blockExists = Friend::where(function ($self) use ($user) {
                    $self->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);
                })->where(function ($target) use ($friend) {
                    $target->where('user_id', $friend->id)
                        ->orWhere('friend_id', $friend->id);
                })->where('status', 'blocked')
                ->first();

            if ($blockExists) {
                return $this->error('Blocked user', 400);
            }

            $requestExists = Friend::where(function ($self) use ($user) {
                    $self->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);
                })->where(function ($target) use ($friend) {
                    $target->where('user_id', $friend->id)
                        ->orWhere('friend_id', $friend->id);
                })->whereIn('status', ['pending_request', 'friend'])
                ->first();

            if ($requestExists) {
                return $this->error('Friend request already exists', 400);
            }

            $friendRequest = Friend::create([
                'user_id'   => $user->id,
                'friend_id' => $friend->id,
            ]);

            // ── Persist notification for the receiver ─────────────────────
            Notification::create([
                'user_id' => $friend->id,
                'type'    => 'friend_request',
                'title'   => $user->username . ' sent you a friend request',
                'body'    => '',
                'post_id' => null,
            ]);

            // ── Broadcast ─────────────────────────────────────────────────
            event(new AddFriendEvent([
                'sender_id'       => $user->id,
                'sender_username' => $user->username,
                'receiver_id'     => $friend->id,
                'receiver_username' => $friend->username,
                'sent_on'         => $friendRequest->created_at->toIso8601String(),
            ]));

            return $this->success('Friend request sent', $friendRequest, 200);
        }
    }
}