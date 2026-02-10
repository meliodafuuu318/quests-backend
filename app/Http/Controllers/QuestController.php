<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Quest\{
    UpdateQuestRequest,
    UpdateQuestTaskRequest
};

use App\Repositories\Quest\{
    UpdateQuestRepository,
    JoinQuestRepository,
    UpdateQuestTaskRepository,
    CompleteTaskRepository,
    CompleteQuestRepository
};

class QuestController extends Controller
{
    protected $updateQuest, $joinQuest, $updateQuestTask, $completeTask, $completeQuest;

    public function __construct (
        UpdateQuestRepository $updateQuest,
        JoinQuestRepository $joinQuest,
        UpdateQuestTaskRepository $updateQuestTask,
        CompleteTaskRepository $completeTask,
        CompleteQuestRepository $completeQuest
    ) {
        $this->updateQuest = $updateQuest;
        $this->joinQuest = $joinQuest;
        $this->updateQuestTask = $updateQuestTask;
        $this->completeTask = $completeTask;
        $this->completeQuest = $completeQuest;
    }

    public function updateQuest(UpdateQuestRequest $request) {
        return $this->updateQuest->execute($request);
    }

    public function joinQuest(Request $request) {
        return $this->joinQuest->execute($request);
    }

    public function updateQuestTask(UpdateQuestTaskRequest $request) {
        return $this->updateQuestTask->execute($request);
    }

    public function completeTask(Request $request) {
        return $this->completeTask->execute($request);
    }

    public function completeQuest(Request $request) {
        return $this->completeQuest->execute($request);
    }
}
