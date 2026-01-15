<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Quest\{
    UpdateQuestRequest
};

use App\Repositories\Quest\{
    UpdateQuestRepository,
    JoinQuestRepository,
    DeleteQuestRepository,
    UpdateQuestTaskRepository
};

class QuestController extends Controller
{
    protected $updateQuest;

    public function __construct (
        UpdateQuestRepository $updateQuest,
        JoinQuestRepository $joinQuest,
        DeleteQuestRepository $deleteQuest,
        UpdateQuestTaskRepository $updateQuestTask
    ) {
        $this->updateQuest = $updateQuest;
        $this->joinQuest = $joinQuest;
        $this->deleteQuest = $deleteQuest;
        $this->updateQuestTask = $updateQuestTask;
    }

    public function updateQuest(UpdateQuestRequest $request) {
        return $this->updateQuest->execute($request);
    }

    public function joinQuest(Request $request) {
        return $this->joinQuest->execute($request);
    }

    public function deleteQuest(Request $request) {
        return $this->deleteQuest->execute($request);
    }

    public function updateQuestTask(Request $request) {
        return $this->updateQuestTask->execute($request);
    }
}
