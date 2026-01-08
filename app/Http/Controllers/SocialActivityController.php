<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    SocialActivity,
    User,
    Quest,
    QuestTask
};
use App\Requests\SocialActivity\{
    CreatePostRequest,
    CreateCommentRequest,
    UpdatePostRequest,
    UpdateCommentRequest,
};

class SocialActivityController extends Controller
{
    public function createPost(CreatePostRequest $request) {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'post') {
            try {
                $post = SocialActivity::create([
                    'user_id' => $user->id,
                    'type' => 'post',
                    'title' => $request->title,
                    'content' => $request->content,
                    'visibility' => $request->visibility,
                ]);

                $quest = Quest::create([
                    'post_id' => $post->id,
                    'creator_id' => $user->id,
                    'reward_exp' => $request->rewardExp,
                    'reward_points' => $request->rewardPoints,
                ]);

                foreach ($request->tasks as $task) {
                    QuestTask::create([
                        'quest_id' => $quest->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'reward_exp' => $task->rewardExp,
                        'reward_points' => $task->rewardPoints,
                        'order' => $task->order
                    ]);
                }

                $data = [
                    'post' => $post,
                    'quest' => $quest,
                    'tasks' => $quest->questTask
                ];

                DB::commit();
                return $this->success('Post created successfully.', $data, 200);
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('Something went wrong', 500, $e);
            }
        }
    }

    public function updatePost(UpdatePostRequest $request) {
        $user = User::find(auth()->user()->id);
        $post = SocialActivity::where('id', $request->postId)
            ->where('user_id', $user->id)
            ->where('type', 'post')
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $post->update([
            'visibility' => $request->visibility ?? $post->visibility,
            'title' => $request->title ?? $post->title,
            'content' => $request->content ?? $post->content,
        ]);

        return $this->success('Post updated successfully', $post, 200);
    }

    public function deletePost(Request $request) {
        $user = User::find(auth()->user()->id);
        $post = SocialActivity::where('id', $request->postId)
            ->where('user_id', $user->id)
            ->where('type', 'post')
            ->first();

        $post->delete();

        return $this->success('Post deleted', 200);
    }

    public function createComment(CreateCommentRequest $request) {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'comment') {
            try {
                $post = SocialActivity::where('id', $request->commentTarget)
                    ->where('type', 'post')
                    ->first();

                if (!$post) {
                    return $this->error('Post not found', 404);
                } else {
                    $comment = SocialActivity::create([
                        'user_id' => $user->id,
                        'type' => 'comment',
                        'comment_target' => $request->commentTarget,
                        'content' => $request->content
                    ]);
                }
                DB::commit();
                return $this->success('Comment created successfully', $comment, 200);
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('Something went wrong', 500, $e);
            }
        }
    }

    public function updateComment(UpdateCommentRequest $request) {
        $user = User::find(auth()->user()->id);
        $comment = SocialActivity::where('id', $request->commentId)
            ->where('user_id', $user->id)
            ->where('type', 'comment')
            ->first();

        if (!$comment) {
            return $this->error('Comment not found', 404);
        }

        $comment->update([
            'content' => $request->content
        ]);

        return $this->success('Comment updated', $comment, 200);
    }

    public function deleteComment(Request $request) {
        $user = User::find(auth()->user()->id);
        $comment = SocialActivity::where('id', $request->commentId)
            ->where('user_id', $user->id)
            ->where('type', 'comment')
            ->first();

        if (!$comment) {
            return $this->error('Comment not found', 404);
        }

        $comment->delete();

        return $this->success('Comment deleted', 200);
    }

    public function react(Request $request) {
        $user = User::find(auth()->user()->id);

        if ($user) {
            if ($request->type === 'like') {
                $target = SocialActivity::whereIn('type', ['post', 'comment'])
                    ->where('id', $request->likeTarget)
                    ->first();

                if (!$target) {
                    return $this->error('Content not found', 404);
                } else {
                    $like = SocialActivity::create([
                        'user_id' => $user->id,
                        'type' => 'like',
                        'like_target' => $request->likeTarget
                    ]);
                }
            }
        }
    }

    public function indexPosts(Request $request) {
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
                            WHEN friends.id IS NOT NULL THEN 1.4
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
