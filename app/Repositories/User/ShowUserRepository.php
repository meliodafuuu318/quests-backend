<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class ShowUserRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);
        $targetUser = User::where('username', $request->username)->first();

        $blockExists = Friend::where(function ($self) use ($user) {
                $self->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);                
            })->where(function ($target) use ($targetUser) {
                $target->where('user_id', $targetUser->id)
                    ->orWhere('friend_id', $targetUser->id);
            })->where('status', 'blocked')
            ->first();
            
        if ($blockExists) {
            return $this->error('Blocked user', 400);
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User details fetched successfully', $user, 200);
    }
}
