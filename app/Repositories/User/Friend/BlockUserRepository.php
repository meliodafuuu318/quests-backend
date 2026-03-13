<?php

namespace App\Repositories\User\Friend;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Friend
};
use App\Events\BlockUserEvent;

class BlockUserRepository extends BaseRepository
{
    public function execute($request)
    {
        if ($request->block === true) {
            $user        = User::find(auth()->user()->id);
            $blockedUser = User::find($request->userId);

            if (!$blockedUser) {
                return $this->error('User not found', 404);
            }

            // Check for an existing relationship between the two users
            $friendExists = Friend::where(function ($q) use ($user, $request) {
                    $q->where('user_id', $user->id)
                      ->where('friend_id', $request->userId);
                })->orWhere(function ($q) use ($user, $request) {
                    $q->where('friend_id', $user->id)
                      ->where('user_id', $request->userId);
                })->first();

            if ($friendExists) {
                // Reuse the existing row and flip it to blocked
                $friendExists->update([
                    'user_id'   => $user->id,
                    'friend_id' => $blockedUser->id,
                    'status'    => 'blocked',
                ]);
            } else {
                // No prior relationship — check it isn't already blocked
                $blockExists = Friend::where(function ($self) use ($user) {
                        $self->where('user_id', $user->id)
                             ->orWhere('friend_id', $user->id);
                    })->where(function ($target) use ($blockedUser) {
                        $target->where('user_id', $blockedUser->id)
                               ->orWhere('friend_id', $blockedUser->id);
                    })->where('status', 'blocked')
                    ->first();

                if ($blockExists) {
                    return $this->error('User already blocked', 400);
                }

                Friend::create([
                    'user_id'   => $user->id,
                    'friend_id' => $blockedUser->id,
                    'status'    => 'blocked',
                ]);
            }

            // ── Broadcast to both sides ───────────────────────────────────
            event(new BlockUserEvent([
                'blocker_id'          => $user->id,
                'blocker_username'    => $user->username,
                'blocked_id'          => $blockedUser->id,
                'blocked_username'    => $blockedUser->username,
            ]));

            return $this->success('User has been blocked', 200);
        }

        return $this->error('Invalid request', 400);
    }
}