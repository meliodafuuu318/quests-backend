<?php

namespace App\Repositories\SocialActivity;

use App\Repositories\BaseRepository;
use App\Models\{
    Friend,
    SocialActivity,
};
use Illuminate\Support\Facades\DB;

class IndexPostsRepository extends BaseRepository
{
    public function execute($request)
    {
        $userId = auth()->id();
        $perPage = (int) ($request->per_page ?? 10);
        $isDiscovery = $request->boolean('discovery', false);

        // Collect friend IDs
        $friendIds = Friend::where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->where('status', 'friend')
            ->get()
            ->map(fn($f) => $f->user_id === $userId ? $f->friend_id : $f->user_id)
            ->values();

        $posts = SocialActivity::query()
            ->from('social_activities as posts')

            // COMMENTS
            ->leftJoin('social_activities as comments', function ($join) {
                $join->on('comments.comment_target', '=', 'posts.id')
                     ->where('comments.type', 'comment');
            })

            // LIKES
            ->leftJoin('social_activities as likes', function ($join) {
                $join->on('likes.like_target', '=', 'posts.id')
                     ->where('likes.type', 'like');
            })

            // USER'S OWN LIKE (for liked state)
            ->leftJoin('social_activities as my_like', function ($join) use ($userId) {
                $join->on('my_like.like_target', '=', 'posts.id')
                     ->where('my_like.type', 'like')
                     ->where('my_like.user_id', $userId);
            })

            // FRIEND RELATION
            ->leftJoin('friends', function ($join) use ($userId) {
                $join->on('friends.friend_id', '=', 'posts.user_id')
                     ->where('friends.user_id', '=', $userId)
                     ->where('friends.status', 'friend');
            })

            ->where('posts.type', 'post');

        if ($isDiscovery) {
            // Discovery: posts from non-friends (and not self)
            $posts->whereNotIn('posts.user_id', $friendIds)
                  ->where('posts.user_id', '!=', $userId)
                  ->where('posts.visibility', 'public');
        } else {
            // Main feed: own posts + friend posts + public posts
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
                  DB::raw('COUNT(DISTINCT likes.id)    AS likes_count'),
                  DB::raw('MAX(my_like.id) IS NOT NULL AS liked'),   // ← persist react state
                  DB::raw('
                      (
                          (
                              COUNT(DISTINCT likes.id)    * 1.0
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

        // Attach creator info + media to each post
        $paginated->getCollection()->transform(function ($post) use ($userId) {
            $user   = \App\Models\User::find($post->user_id);
            $media  = \App\Models\Media::where('social_activity_id', $post->id)->get()
                        ->map(fn($m) => ['filepath' => $m->filepath])->values();

            $post->id                  = $post->id;  // ensure top-level id
            $post->creator_username    = $user?->username;
            $post->creator_full_name   = ($user?->first_name ?? '') . ' ' . ($user?->last_name ?? '');
            $post->visibility          = $post->visibility;
            $post->liked               = (bool) $post->liked;
            $post->media               = $media;
            return $post;
        });

        return $this->success('Feed loaded', $paginated, 200);
    }
}