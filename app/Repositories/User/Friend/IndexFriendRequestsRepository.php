<?php

namespace App\Repositories\User\Friend;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class IndexFriendRequestsRepository extends BaseRepository
{
    public function execute(){
        $user = User::find(auth()->user()->id);

        $incomingRequests = Friend::where('friend_id', $user->id)
            ->where('status', 'pending_request')
            ->get();
        $sentRequests = Friend::where('user_id', $user->id)
            ->where('status', 'pending_request')
            ->get();
        
        $allRequests = [
            'incomingRequests' => $incomingRequests->map(function ($friendRequest) {
                    return  [
                        'sender' => $friendRequest->user->username,
                        'sentOn' => $friendRequest->created_at,
                    ];
                }),
            'sentRequests' => $sentRequests->map(function ($friendRequest) {
                    return [
                        'sentTo' => $friendRequest->friend->username,
                        'sentOn' => $friendRequest->created_at,
                    ];
                }),
        ];

        return $this->success('Friend requests fetched successfully', $allRequests, 200);
    }
}
