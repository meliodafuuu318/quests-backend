<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class IndexFriendsRepository extends BaseRepository
{
    public function execute(){
        $user = User::find(auth()->user()->id);

        $friends = Friend::where('status', 'friend')
            ->where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)
            ->get();

        $blocked = Friend::where('status', 'blocked')
            ->where('user_id', $user->id)
            ->get();

        $transformedFriends = $friends->map(function ($friend) use ($user) {
            if ($user->id === $friend->user_id) {
                $username = $friend->friend->username;
            } elseif ($user->id === $friend->friend_id) {
                $username = $friend->user->username;
            }
            return [
                'username' => $username,
                'friendsSince' => $friend->updated_at
            ];
        });

        $transformedBlocked = $blocked->map(function ($block) use ($user) {
            return [
                'username' => $block->friend->username,
                'blockedSince' => $block->updated_at
            ];
        });

        return $this->success('Friend list fetched successfully', ['friends' => $transformedFriends, 'blocked' => $transformedBlocked], 200);
    }
}
