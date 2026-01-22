<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    QuestTask,
    User,
    Quest
};

class UpdateQuestTaskRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->id());
        $userQuestIds = Quest::where('creator_id', $user->id)
            ->pluck('id');

        $questTask = QuestTask::where('id', $request->taskId)
            ->whereIn('id', $userQuestIds)
            ->first();

        if (!$questTask) {
            return $this->error('Task not found', 404);
        }

        $questTask->update([
            'title' => $request->title ?? $questTask->title,
            'description' => $request->description ?? $questTask->description,
            'reward_exp' => $request->rewardExp ?? $questTask->reward_exp,
            'reward_points' => $request->rewardPoints ?? $questTask->reward_points,
            'order' => $request->order ?? $questTask->order
        ]);

        return $this->success('Task updated successfully', $questTask, 200);
    }
}
