<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    SocialActivity,
    QuestTask,
    Media,
    Asset,
    Quest
};

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

        $avatarUrl = null;
        $creator = $post->user;
        if ($creator && $creator->avatar_id) {
            $asset = Asset::find($creator->avatar_id);
            $avatarUrl = $asset?->filepath;
        }

        $quest = Quest::where('post_id', $post->id)->first();
        $questData = null;
        if ($quest) {
            $tasks = QuestTask::where('quest_id', $quest->id)
                ->orderBy('order')
                ->get()
                ->map(fn($t) => [
                    'order' => $t->order,
                    'title' => $t->title,
                    'description' => $t->description,
                    'reward_exp' => $t->reward_exp,
                    'reward_points' => $t->reward_points,
                ]);
            $questData = [
                'id' => $quest->id,
                'code' => $quest->code,
                'reward_exp' => $quest->reward_exp,
                'reward_points' => $quest->reward_points,
                'participants' => $quest->participant_count,
                'quest_tasks' => $tasks,
            ];
        }

        $result = [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'type' => $post->type,
            'visibility' => $post->visibility,
            'title' => $post->title,
            'content' => $post->content,
            'created_at' => optional($post->created_at)->format('Y-m-d h:i'),
            'updated_at' => optional($post->updated_at)->format('Y-m-d h:i'),
            'liked' => $liked,
            'likes_count' => $likesCount,
            'comments_count' => $commentsCount,
            'media' => $media,
            'creator_username' => $creator?->username,
            'creator_full_name' => trim(($creator?->first_name ?? '') . ' ' . ($creator?->last_name ?? '')),
            'creator_avatar_url' => $avatarUrl,
            'quest' => $questData,
        ];

        return $this->success('Post fetched successfully', $result, 200);
    }
}