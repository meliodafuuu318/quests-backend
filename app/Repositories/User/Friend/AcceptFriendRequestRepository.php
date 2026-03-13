<?php

namespace App\Repositories\User\Friend;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend,
    Notification
};
use App\Events\AcceptFriendRequestEvent;

class AcceptFriendRequestRepository extends BaseRepository
{
    public function execute($request)
    {
        $user = User::find(auth()->user()->id);

        $friendUsernames = User::whereIn('id', function ($query) use ($user) {
            $query->select('user_id')
                ->from('friends')
                ->where('friend_id', $user->id)
                ->where('status', 'pending_request');
        })->pluck('username');

        $friendRequest = null;
        $requester     = null;

        foreach ($friendUsernames as $username) {
            if ($username !== $request->username) {
                continue;
            }
            $requester     = User::where('username', $username)->first();
            $friendRequest = Friend::where('user_id', $requester->id)
                ->where('friend_id', $user->id)
                ->where('status', 'pending_request')
                ->first();
        }

        if ($friendRequest === null) {
            return $this->error('Friend request not found', 404);
        }

        $friendRequest->update(['status' => 'friend']);

        // ── Persist notification for the original requester ───────────────
        if ($requester) {
            Notification::create([
                'user_id' => $requester->id,
                'type'    => 'friend_accepted',
                'title'   => $user->username . ' accepted your friend request',
                'body'    => '',
                'post_id' => null,
            ]);
        }

        // ── Broadcast ─────────────────────────────────────────────────────
        event(new AcceptFriendRequestEvent([
            'acceptor_id'          => $user->id,
            'acceptor_username'    => $user->username,
            'requester_id'         => $requester?->id,
            'requester_username'   => $requester?->username,
            'friends_since'        => now()->toIso8601String(),
        ]));

        return $this->success('Friend request accepted', 200);
    }
}