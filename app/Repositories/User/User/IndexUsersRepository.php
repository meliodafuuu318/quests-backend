<?php

namespace App\Repositories\User\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class IndexUsersRepository extends BaseRepository
{
    public function execute(){
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $friendIds = Friend::where('user_id', $user->id)
            ->where('status', 'friend')
            ->pluck('friend_id')
            ->merge(
                Friend::where('friend_id', $user->id)->pluck('user_id')
            )
            ->unique()
            ->values();

        $mutualIds = Friend::whereIn('user_id', $friendIds)
            ->orWhereIn('friend_id', $friendIds)
            ->get()
            ->flatMap(function ($friend) {
                return [$friend->user_id, $friend->friend_id];
            })
            ->unique()
            ->reject(fn ($id) => $id == auth()->id())
            ->reject(fn ($id) => $friendIds->contains($id))
            ->values();

        $recommended = User::whereIn('id', $mutualIds)
            ->get();
            // ->paginate(10);

        $transformedRecommended = $recommended->map(function ($rec) {
            return [
                'username' => $rec->username,
                'firstName' => $rec->first_name,
                'lastName' => $rec->last_name,
            ];
        });

        return $this->success('Recommended friends fetched successfully.', $transformedRecommended, 200);
    }
}
