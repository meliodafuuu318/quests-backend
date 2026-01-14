<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\SocialActivity;

class ShowPostCommentsRepository extends BaseRepository
{
    public function execute($request){
        $post = SocialActivity::where('type', 'post')
            ->where('id', $request->postId)
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $postComments = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->paginate(10);

        return $this->success('Post comments fetched successfully', $postComments, 200);
    }
}
