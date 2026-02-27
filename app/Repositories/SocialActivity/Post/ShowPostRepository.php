<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    SocialActivity,
    QuestTask,
    Media
};
use App\Http\Resources\PostResource;

class ShowPostRepository extends BaseRepository
{
    public function execute($request)
    {
        $userId = auth()->id();

        $post = SocialActivity::where('id', $request->postId)
            ->where('type', 'post')
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $liked = SocialActivity::where('type', 'like')
            ->where('like_target', $post->id)
            ->where('user_id', $userId)
            ->exists();

        $likesCount = SocialActivity::where('type', 'like')
            ->where('like_target', $post->id)
            ->count();

        $commentsCount = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->count();

        $media = Media::where('social_activity_id', $post->id)
            ->get()
            ->map(fn($m) => ['filepath' => $m->filepath, 'id' => $m->id])
            ->values();

        $postData = new PostResource($post);
        $result   = $postData->toArray($request);

        // Inject extra fields the Flutter app needs
        $result['id']             = $post->id;
        $result['liked']          = $liked;
        $result['likes_count']    = $likesCount;
        $result['comments_count'] = $commentsCount;
        $result['media']          = $media;

        return $this->success('Post fetched successfully', $result, 200);
    }
}