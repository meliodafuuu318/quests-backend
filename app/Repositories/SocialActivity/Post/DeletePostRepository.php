<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};

class DeletePostRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'postId' => 'required'
        ]);

        $user = User::find(auth()->user()->id);
        $post = SocialActivity::where('id', $request->postId)
            ->where('user_id', $user->id)
            ->where('type', 'post')
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }
        
        $post->delete();

        return $this->success('Post and quest deleted', 200);
    }
}
