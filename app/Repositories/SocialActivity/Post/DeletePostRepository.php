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
        $user = User::find(auth()->user()->id);
        $post = SocialActivity::where('id', $request->postId)
            ->where('user_id', $user->id)
            ->where('type', 'post')
            ->first();

        $post->delete();

        return $this->success('Post and quest deleted', 200);
    }
}
