<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Quest\{
    UpdateQuestRequest
};

use App\Repositories\Quest\{
    UpdateQuestRepository
};

class QuestController extends Controller
{
    protected $updateQuest;

    public function __construct (
        UpdateQuestRepository $updateQuest
    ) {
        $this->updateQuest = $updateQuest;
    }

    public function updateQuest(UpdateQuestRequest $request) {
        return $this->updateQuest->execute($request);
    }
}
