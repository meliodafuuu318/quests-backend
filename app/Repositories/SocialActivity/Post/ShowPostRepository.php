<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    SocialActivity,
    Quest,
    QuestTask,
    QuestParticipant
};

class ShowPostRepository extends BaseRepository
{
    public function execute($request){
        $post = SocialActivity::where('id', $request->postId)
            ->where('type', 'post')
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $questTasks = QuestTask::where('quest_id', $post->quest->id)
            ->orderBy('order', 'asc')
            ->get();
        $questTaskData = [];

        foreach ($questTasks as $task) {
            $questTaskData[] = [
                'order' => $task->order,
                'title' => $task->title,
                'description' => $task->description,
            ];
        }

        $commentCount = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->count();
        $reactCount = SocialActivity::where('type', 'like')
            ->where('like_target', $post->id)
            ->count();

        $postData = [
            'postId' => $post->id,
            'postTitle' => $post->title,
            'postContent' => $post->content,
            'questCode' => $post->quest->code,
            'questTasks' => $questTaskData,
            'commentCount' => $commentCount,
            'reactCount' => $reactCount
        ];

        return $this->success('Post fetched successfully', $postData, 200);
    }
}
