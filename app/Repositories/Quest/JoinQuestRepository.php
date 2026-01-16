<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Quest,
    QuestParticipant
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

        $quest->update([
            'participant_count' => $quest->participant_count + 1
        ]);

        return $this->success('Joined quest successfully', $questParticipant, 200);
    }
}
