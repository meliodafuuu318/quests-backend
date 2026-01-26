<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    QuestParticipantTask,
    SocialActivity
};

class CompleteTaskRepository extends BaseRepository
{
    public function execute($request){
        $task = QuestParticipantTask::where('quest_task_id', $request->taskId)
            ->where('quest_participant_id', auth()->id())
            ->first();

        if (!$task) {
            return $this->error('Task not found', 404);
        }


    }
}
