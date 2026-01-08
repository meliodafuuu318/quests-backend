<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};

class UpdatePostRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);
        $post = SocialActivity::where('id', $request->postId)
            ->where('user_id', $user->id)
            ->where('type', 'post')
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $post->update([
            'visibility' => $request->visibility ?? $post->visibility,
            'title' => $request->title ?? $post->title,
            'content' => $request->content ?? $post->content,
        ]);

        return $this->success('Post updated successfully', $post, 200);
    }
}
