<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\{
    SocialActivity,
    User,
    Friend
};

class IndexUserPostsRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);
        $targetUser = User::where('username', $request->username)->first();

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $blockExists = Friend::where('status', 'blocked')
            ->where(function ($self) use ($user) {
                $self->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);                
            })->where(function ($target) use ($targetUser) {
                $target->where('user_id', $targetUser->id)
                    ->orWhere('friend_id', $targetUser->id);
            })->first();
            
        if ($blockExists) {
            return $this->error('Blocked user', 400);
        }

        $userPosts = SocialActivity::where('user_id', $targetUser->id)
            ->where('type', 'post')
            ->orderBy('created_at', 'desc')
            ->get();

        $trasformedUserPosts = $userPosts->map(function ($post) {
            return [
                'username' => $post->user->username,
                'id' => $post->id,
                // 'title' => $post->quest->title,
                'datetime' => $post->created_at,
            ];
        });

        return $this->success('User posts fetched', $trasformedUserPosts, 200);
    }
}
