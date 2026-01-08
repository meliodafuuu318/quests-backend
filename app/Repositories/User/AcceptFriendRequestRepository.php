<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class AcceptFriendRequestRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);

        $friendIds = Friend::where('friend_id', $user->id)
            ->where('status', 'pending_request')
            ->pluck('user_id');
        
        $friendUsernames = User::whereIn('id', function ($query) use ($user) {
            $query->select('user_id')
                ->from('friends')
                ->where('friend_id', $user->id)
                ->where('status', 'pending_request');
        })->pluck('username');

        $friendRequest = null;

        foreach ($friendUsernames as $username) {
            if ($username !== $request->username) {
                continue;
            } else {
                $friend = User::where('username', $username)->first();
                $friendRequest = Friend::where('user_id', $friend->id)
                    ->where('friend_id', $user->id)
                    ->where('status', 'pending_request')
                    ->first();
            }
        }

        if ($friendRequest === null) {
            return $this->error('Friend request not found', 404);
        } else {
            $friendRequest->update(['status' => 'friend']);
            return $this->success('Friend request accepted', 200);
        }
    }
}
