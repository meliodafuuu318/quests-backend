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
    CreateCommentRequest
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

    public function updatePost() {
        //
    }

    public function deletePost() {
        //
    }

    public function createComment(CreateCommentRequest $request) {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'comment') {
            try {
                $post = SocialActivity::where('id', $request->commentTarget)
                    ->where('type', 'post')
                    ->get();

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

    public function updateComment() {
        //
    }

    public function deleteComment() {
        //
    }

    public function react() {
        $user = User::find(auth()->user()->id);

        if ($user) {
            if ($request->type === 'like') {
                $target = SocialActivity::whereIn('type', ['post', 'comment'])
                    ->where('id', $request->likeTarget)
                    ->get();

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

    public function indexPosts() {
        //
    }
}
