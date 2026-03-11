<?php

namespace App\Repositories\SocialActivity;

use App\Repositories\BaseRepository;
use App\Models\{
    Friend,
    SocialActivity,
    User,
    Media,
    Asset,
    Quest,
    QuestTask
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IndexPostsRepository extends BaseRepository
{
    public function execute($request)
    {
        $userId  = auth()->id();
        $perPage = (int) ($request->per_page ?? 10);
        $isDiscovery = $request->boolean('discovery', false);

        $friendIds = Friend::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->where('status', 'friend')
            ->get()
            ->map(fn($f) => $f->user_id === $userId ? $f->friend_id : $f->user_id)
            ->values();

        $posts = SocialActivity::query()
            ->from('social_activities as posts')

            ->leftJoin('social_activities as comments', function ($join) {
                $join->on('comments.comment_target', '=', 'posts.id')
                     ->where('comments.type', 'comment');
            })
            ->leftJoin('social_activities as likes', function ($join) {
                $join->on('likes.like_target', '=', 'posts.id')
                     ->where('likes.type', 'like');
            })
            ->leftJoin('social_activities as my_like', function ($join) use ($userId) {
                $join->on('my_like.like_target', '=', 'posts.id')
                     ->where('my_like.type', 'like')
                     ->where('my_like.user_id', $userId);
            })
            ->leftJoin('friends', function ($join) use ($userId) {
                $join->on('friends.friend_id', '=', 'posts.user_id')
                     ->where('friends.user_id', '=', $userId)
                     ->where('friends.status', 'friend');
            })
            ->where('posts.type', 'post');

        if ($isDiscovery) {
            $posts->whereNotIn('posts.user_id', $friendIds)
                  ->where('posts.user_id', '!=', $userId)
                  ->where('posts.visibility', 'public');
        } else {
            $posts->where(function ($q) use ($userId, $friendIds) {
                $q->where('posts.visibility', 'public')
                  ->orWhere(function ($q) use ($friendIds) {
                      $q->where('posts.visibility', 'friends')
                        ->whereIn('posts.user_id', $friendIds);
                  })
                  ->orWhere(function ($q) use ($userId) {
                      $q->where('posts.visibility', 'private')
                        ->where('posts.user_id', $userId);
                  });
            });
        }

        $posts->groupBy('posts.id')
              ->select([
                  'posts.*',
                  DB::raw('COUNT(DISTINCT comments.id) AS comments_count'),
                  DB::raw('COUNT(DISTINCT likes.id) AS likes_count'),
                  DB::raw('MAX(my_like.id) IS NOT NULL AS liked'),
                  DB::raw('
                      (
                          (
                              COUNT(DISTINCT likes.id) * 1.0
                            + COUNT(DISTINCT comments.id) * 2.0
                          )
                          *
                          CASE
                              WHEN MAX(friends.id) IS NOT NULL THEN 1.4
                              ELSE 1.0
                          END
                          *
                          EXP(-TIMESTAMPDIFF(HOUR, posts.created_at, NOW()) / 36)
                      ) AS rank_score
                  '),
              ])
              ->orderByDesc('rank_score');

        $paginated = $posts->paginate($perPage);

        $paginated->getCollection()->transform(function ($post) use ($userId) {
            $user = User::find($post->user_id);
            $media = Media::where('social_activity_id', $post->id)->get()
                         ->map(fn($m) => ['filepath' => $m->filepath])->values();

            $avatarUrl = null;
            if ($user && $user->avatar_id) {
                $asset = Asset::find($user->avatar_id);
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

            $post->creator_username = $user?->username;
            $post->creator_full_name = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));
            $post->creator_avatar_url = $avatarUrl;
            $post->liked = (bool) $post->liked;
            $post->media = $media;
            $post->quest = $questData;

            $post->created_at = $post->created_at instanceof Carbon
                ? $post->created_at->format('Y-m-d h:i')
                : substr((string) $post->created_at, 0, 16);
            $post->updated_at = $post->updated_at instanceof Carbon
                ? $post->updated_at->format('Y-m-d h:i')
                : substr((string) $post->updated_at, 0, 16);

            return $post;
        });

        return $this->success('Feed loaded', $paginated, 200);
    }
}