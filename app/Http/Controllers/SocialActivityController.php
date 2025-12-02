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
    CreatePostRequest
};

class SocialActivityController extends Controller
{
    public function createPost(CreatePostRequest $request) {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        try {
            if ($request->type === 'post') {
                $post = SocialActivity::create([
                    'user_id' => $user->id,
                    'type' => 'post',
                ]);

                $quest = Quest::create([
                    'post_id' => $post->id,
                    'creator_id' => $user->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'visibility' => $request->visibility,
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
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('Post creation failed', 500);
        }
    }

    public function updatePost() {
        //
    }

    public function deletePost() {
        //
    }

    public function createComment() {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        try {
            if ($request->type === 'comment') {
                $comment = SocialActivity::create([
                    'comment_target' => $request->commentTarget,
                    'description' => $request->description
                ]);
            }
        } catch (\Exception $e) {
            //
        }
    }

    public function updateComment() {
        //
    }

    public function deleteComment() {
        //
    }

    public function react() {
        //
    }

    public function indexPosts() {
        //
    }
}
