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
        $quest = Quest::where('code', $request->questCode)
            ->where('creator_id', $user->id)
            ->first();

        if (!$quest) {
            return $this->error('Quest not found', 404);
        }

        $quest->update([
            'reward_exp' => $request->rewardExp ?? $quest->reward_exp,
            'reward_points' => $request->rewardPoints ?? $quest->reward_points
        ]);

        if ($request->filled('questTasks')) {
            $taskData = $request->questTasks;

            $lastOrder = QuestTask::where('quest_id', $quest->id)
                ->max('order');

            $newTasks = [];

            foreach ($taskData as $task) {
                $newTasks[] = QuestTask::create([
                    'quest_id' => $quest->id,
                    'title' => $task['title'],
                    'description' => $task['title'],
                    'reward_exp' => $task['rewardExp'],
                    'reward_points' => $task['rewardPoints'],
                    'order' => $lastOrder + 1
                ]);
            }
        }
    }
}
