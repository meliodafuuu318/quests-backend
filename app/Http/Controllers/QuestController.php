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
    CompleteTaskRepository
};

class QuestController extends Controller
{
    protected $updateQuest, $joinQuest, $updateQuestTask, $completeTask;

    public function __construct (
        UpdateQuestRepository $updateQuest,
        JoinQuestRepository $joinQuest,
        UpdateQuestTaskRepository $updateQuestTask,
        CompleteTaskRepository $completeTask
    ) {
        $this->updateQuest = $updateQuest;
        $this->joinQuest = $joinQuest;
        $this->updateQuestTask = $updateQuestTask;
        $this->completeTask = $completeTask;
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
}
