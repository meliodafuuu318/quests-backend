<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    Quest,
    QuestParticipant,
    QuestParticipantTask,
    User,
    SocialActivity
};

class CompleteQuestRepository extends BaseRepository
{
    public function execute($request){
        $user = auth()->user();
        $quest = Quest::find($request->questId);

        $questParticipants = QuestParticipant::where('quest_id', $quest->id)->pluck('id');
        $userParticipant = QuestParticipant::where('user_id', $user->id)->get();

        if ($userParticipant->id->in_array($questParticipants)) {
            $userTasks = QuestParticipantTask::where('quest_participant_id', $userParticipant->id)->get();
            
            foreach ($userTasks as $task) {
                if ($task->status !== 'completed') {
                    return $this->error("Quest #{$task->order} not yet completed", 401);
                }
            }

            $userParticipant->update([
                'completed_at' => Carbon::now()
            ]);

            return $this->success('Quest completed', [], 200);
        }
    }
}
