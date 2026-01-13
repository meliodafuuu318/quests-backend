<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity,
    Quest,
    QuestTask
};
use Illuminate\Support\Facades\DB;

class CreatePostRepository extends BaseRepository
{
    public function execute($request){
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
                    'code' => $this->questCode(),
                    'post_id' => $post->id,
                    'creator_id' => $user->id,
                    'reward_exp' => $request->rewardExp,
                    'reward_points' => $request->rewardPoints,
                ]);

                foreach ($request->tasks as $task) {
                    QuestTask::create([
                        'quest_id' => $quest->id,
                        'title' => $task['title'],
                        'description' => $task['description'],
                        'reward_exp' => $task['rewardExp'],
                        'reward_points' => $task['rewardPoints'],
                        'order' => $task['order']
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
}
