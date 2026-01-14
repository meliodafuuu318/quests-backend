<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    Quest,
    QuestTask,
    QuestParticipant
};

class UpdateQuestRepository extends BaseRepository
{
    public function execute($request){
        $user = User::where('username', auth()->id())->first();
        $quest = Quest::where('id', $request->questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$quest) {
            return $this->error('Quest not found', 404);
        }
    }
}
