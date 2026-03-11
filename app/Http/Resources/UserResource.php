<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    QuestParticipant,
    Quest,
    QuestParticipantTask,
    QuestTask,
    Asset
};

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $participations = QuestParticipant::where('user_id', $this->id)->get();

        $quests = $participations->map(function ($participant) {
            $quest = Quest::find($participant->quest_id);
            if (!$quest) return null;

            $questTasks = QuestTask::where('quest_id', $quest->id)
                ->orderBy('order')
                ->get();

            $participantTasks = QuestParticipantTask::where('quest_participant_id', $participant->id)
                ->get()
                ->keyBy('quest_task_id');

            return [
                'id' => $quest->id,
                'code' => $quest->code,
                'creator_id' => $quest->creator_id,
                'reward_exp' => $quest->reward_exp,
                'reward_points' => $quest->reward_points,
                'joined_at' => $participant->joined_at,
                'completed_at' => $participant->completed_at ?? null,
                'quest_tasks' => $questTasks->map(function ($task) use ($participantTasks) {
                    $pt = $participantTasks->get($task->id);
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'reward_exp' => $task->reward_exp,
                        'reward_points' => $task->reward_points,
                        'order' => $task->order,
                        'completion_status' => $pt?->completion_status ?? null,
                        'completed_at' => $pt?->completed_at ?? null,
                        'approved_at' => $pt?->approved_at ?? null,
                    ];
                })->values(),
            ];
        })->filter()->values();

        $avatarUrl = null;
        if ($this->avatar_id) {
            $asset = Asset::find($this->avatar_id);
            $avatarUrl = asset($asset?->filepath);
        }

        return [
            'username' => $this->username,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'exp' => $this->exp,
            'level' => $this->level,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'contact_number' => $this->contact_number,
            'bio' => $this->bio,
            'avatar_id' => $this->avatar_id,
            'avatar_url' => $avatarUrl,
            'quests' => $quests,
        ];
    }
}