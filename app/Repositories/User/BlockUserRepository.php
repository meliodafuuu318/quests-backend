<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};

class BlockUserRepository extends BaseRepository
{
    public function execute($request){
        if ($request->block === true) {
            $user = User::find(auth()->user()->id);
            $blockedUser = User::find($request->userId);

            if (!$blockedUser) {
                return $this->error('User not found', 404);
            }

            $friendExists = Friend::where('user_id', $user->id)
                ->orWhere('friend_id', $user->id)
                ->whereNot('status', 'blocked')
                ->first();

            $friendExists = Friend::where(function ($friend) use ($request, $user) {
                    $friend->where('user_id', $user->id)
                        ->where('friend_id', $request->userId);
                })->orWhere(function ($friend) use ($request, $user) {
                    $friend->where('friend_id', $user->id)
                        ->where('user_id', $request->userId);
                })->first();
            
            if ($friendExists) {
                $friendExists->update([
                    'user_id' => auth()->user()->id,
                    'friend_id' => $blockedUser->id,
                    'status' => 'blocked'
                ]);

                return $this->success('User has been blocked', 200);
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
                return $this->error('User already blocked', 400);
            }

            $block = Friend::create([
                'user_id' => $user->id,
                'friend_id' => $blockedUser->id,
                'status' => 'blocked'
            ]);

            return $this->success('User has been blocked', 200);
        }
    }
}
