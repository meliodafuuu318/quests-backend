<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\SocialActivity;
use App\Http\Resources\CommentResource;

class ShowPostCommentsRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'postId' => 'required'
        ]);
        
        $post = SocialActivity::where('type', 'post')
            ->where('id', $request->postId)
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $postComments = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->paginate(10);

        return $this->success('Post comments fetched successfully', CommentResource::collection($postComments), 200);
    }
}
