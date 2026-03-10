<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    QuestParticipant,
    Quest,
    QuestParticipantTask,
    QuestTask
};

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userQuestTasks = QuestParticipantTask::where('quest_participant_id', QuestParticipant::where('user_id', $this->id)->first()->id);
        $quests = [];
        
        foreach (QuestParticipant::where('user_id', $this->id)->get() as $quest) {
            $quests[] = Quest::find($quest->id);
        }

        $quests = collect($quests);

        $quests = $quests->map(function ($q) use ($userQuestTasks) {
            $quest = QuestParticipant::where('quest_id', $q->id)
                ->where('user_id', $this->id)
                ->first();

            $tasks = QuestTask::where('quest_id', $quest->id)->get();
            $userTasks = $userQuestTasks;

            return [
                'id' => $q->id,
                'code' => $q->code,
                'creator_id' => $q->creator_id,
                'reward_exp' => $q->reward_exp,
                'reward_points' => $q->reward_points,
                'joined_at' => $quest->joined_at,
                'completed_at' => $quest->completed_at ?? null,
                'quest_tasks' => $tasks->map(function ($t) use ($tasks, $userTasks) {
                    $task = $userTasks->where('quest_task_id', $t->id)->first();

                    return [
                        'title' => $t->title,
                        'description' => $t->description,
                        'reward_exp' => $t->reward_exp,
                        'reward_points' => $t->reward_points,
                        'order' => $t->order,
                        'completion_status' => $task->completion_status ?? null,
                        'completed_at' => $task->completed_at ?? null,
                        'approved_at' => $task->approved_at ?? null
                    ];
                })
            ];
        });

        return [
            'username' => $this->username,
            'email' => $this->email,
            'exp' => $this->exp,
            'level' => $this->level,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'address' => $this->city . ', ' . $this->province . ', ' . $this->country,
            'contact_number' =>$this->contact_number,
            'bio' => $this->bio,
            'avatar_id' => $this->avatar_id,
            'quests' => $quests
        ];
    }
}
