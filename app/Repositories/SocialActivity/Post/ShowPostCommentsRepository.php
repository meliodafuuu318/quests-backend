<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{SocialActivity, Media};

class ShowPostCommentsRepository extends BaseRepository
{
    public function execute($request)
    {
        $request->validate(['postId' => 'required']);

        $post = SocialActivity::where('type', 'post')
            ->where('id', $request->postId)
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $postComments = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $transformed = $postComments->getCollection()->map(function ($comment) {
            $media = Media::where('social_activity_id', $comment->id)
                ->get()
                ->map(fn($m) => ['filepath' => $m->filepath])
                ->values();

            return [
                'id'        => $comment->id,
                'username'  => $comment->user->username,
                'postId'    => $comment->comment_target,
                'content'   => $comment->content,
                'media'     => $media,
                'createdAt' => $comment->created_at->format('Y-m-d h:i'),
            ];
        });

        $postComments->setCollection($transformed);

        return $this->success('Post comments fetched successfully', $postComments, 200);
    }
}