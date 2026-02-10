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
use Carbon\Carbon;

class CompleteQuestRepository extends BaseRepository
{
    public function execute($request){
        $user = auth()->user();
        $quest = Quest::find($request->questId);

        $questParticipants[] = QuestParticipant::where('quest_id', $quest->id)->value('id');
        $userParticipant = QuestParticipant::where('user_id', $user->id)->first();

        if (in_array(number_format($userParticipant->id), $questParticipants)) {
            $userTasks = QuestParticipantTask::where('quest_participant_id', $userParticipant->id)->get();
            
            foreach ($userTasks as $task) {
                if ($task->status !== 'completed') {
                    return $this->error("Quest #{$task->order} not yet completed", 401);
                }
            }

            $userParticipant->update([
                'completed_at' => Carbon::now()
            ]);
            $user->update([
                'exp' => $user->exp + $quest->reward_exp
            ]);
            $user->creditAdd($quest->reward_points, 'Completed quest');

            return $this->success('Quest completed', [], 200);
        }
    }
}
