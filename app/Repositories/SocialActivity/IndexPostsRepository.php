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
    public function execute($request){
        $userId = auth()->id();

        // Friend IDs
        $friendIds = Friend::where('user_id', $userId)
            ->where('status', 'friend')
            ->pluck('friend_id');

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

            // FRIEND RELATION
            ->leftJoin('friends', function ($join) use ($userId) {
                $join->on('friends.friend_id', '=', 'posts.user_id')
                    ->where('friends.user_id', '=', $userId)
                    ->where('friends.status', 'friend');
            })

            ->where('posts.type', 'post')

            // VISIBILITY
            ->where(function ($q) use ($userId, $friendIds) {
                $q->where('posts.visibility', 'public')
                ->orWhere(function ($q) use ($friendIds) {
                    $q->where('posts.visibility', 'friends')
                        ->whereIn('posts.user_id', $friendIds);
                })
                ->orWhere(function ($q) use ($userId) {
                    $q->where('posts.visibility', 'private')
                        ->where('posts.user_id', $userId);
                });
            })

            ->groupBy('posts.id')

            ->select([
                'posts.*',
                DB::raw('COUNT(DISTINCT comments.id) AS comments_count'),
                DB::raw('COUNT(DISTINCT likes.id) AS likes_count'),
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
                ')
            ])

            ->orderByDesc('rank_score')
            ->cursorPaginate(15);

        return $this->success('Feed loaded', $posts, 200);
    }
}
