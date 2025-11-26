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
                    'reward_exp' => $request->reward_exp,
                    'reward_points' => $request->reward_points,
                ]);

                foreach ($request->task as $task) {
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

                return $this->success('Post created successfully.', $data, 200);
            }
        } catch (\Exception $e) {
            //
        }
    }

    public function updatePost() {
        //
    }

    public function deletePost() {
        //
    }

    public function createComment() {
        //
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
