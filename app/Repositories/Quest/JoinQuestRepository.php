<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Quest,
    QuestParticipant,
    QuestTask,
    QuestTaskParticipant
};

class JoinQuestRepository extends BaseRepository
{
    public function execute($request){
        $request->validate([
            'questCode' => 'required'
        ]);

        $user = User::find(auth()->id());
        $quest = Quest::where('code', $request->questCode)
            ->first();

        if (!$quest) {
            return $this->error('Quest not found', 404);
        }

        $questParticipant = QuestParticipant::create([
            'user_id' => $user->id,
            'quest_id' => $quest->id,
            'joined_at' => Carbon::now() 
        ]);

        $questTaskModels = QuestTask::where('quest_id', $quest->id)
            ->get();

        $participantQuestTasks = [];

        foreach ($questTaskModels as $task) {
            $participantQuestTasks[] = QuestParticipantTask::create([
                'quest_participant_id' => $questParticipant->id,
                'quest_task_id' => $task->id,
            ]);
        } 

        $quest->update([
            'participant_count' => $quest->participant_count + 1
        ]);

        return $this->success('Joined quest successfully', [$questParticipant, $participantQuestTasks], 200);
    }
}
