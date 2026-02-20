<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\SocialActivity;
use App\Http\Resources\ReactResource;

class ShowPostReactsRepository extends BaseRepository
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

        $postReacts = SocialActivity::where('type', 'like')
            ->where('like_target', $post->id)
            ->get();

        return $this->success('Post reacts fetched successfully', ReactResource::collection($postReacts), 200);
    }
}
