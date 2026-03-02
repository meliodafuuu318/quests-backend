<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    QuestTask,
    SocialActivity,
    Media
};

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $questTasks = QuestTask::where('quest_id', $this->quest->id)
            ->orderBy('order', 'asc')
            ->get();

        $media  = Media::where('social_activity_id', $post->id)->get()
            ->map(fn($m) => ['filepath' => $m->filepath])->values();

        return [
            'creator_username' => $this->user->username,
            'creator_full_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'post' => [
                'id' => $this->id,
                'visibility' => $this->visibility,
                'title' => $this->title,
                'content' => $this->content ?? null,
                'media' => $media,
                'created_at' => $this->created_at->format('Y-m-d h:i'),
                'updated_at' => $this->updated_at->format('Y-m-d h:i')
            ],
            'quest' => [
                'code' => $this->quest->code,
                'reward_exp' => $this->quest->reward_exp,
                'reward_points' => $this->quest->reward_points,
                'quest_tasks' => $questTasks->map(function ($task) {
                    return [
                        'order' => $task->order,
                        'title' => $task->title,
                        'description' => $task->description,
                        'reward_exp' => $task->reward_exp,
                        'reward_points' => $task->reward_points
                    ];
                }, $questTasks),
                'participants' => $this->quest->participant_count,
                'comment_count' => SocialActivity::where('type', 'comment')
                    ->where('comment_target', $this->id)
                    ->count(),
                'react_count' => SocialActivity::where('type', 'comment')
                    ->where('comment_target', $this->id)
                    ->count(),
            ]
        ];
    }
}
