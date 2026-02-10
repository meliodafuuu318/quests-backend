<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    QuestParticipant,
    QuestParticipantTask,
    SocialActivity,
    CompletionVerification
};
use Carbon\Carbon;

class CompleteTaskRepository extends BaseRepository
{
    public function execute($request){
        $task = QuestParticipantTask::where('id', $request->taskId)
            ->first();

        if (!$task) {
            return $this->error('Task not found', 404);
        }

        if (($task->questParticipant->user_id === auth()->id())&&($task->completion_status === null)) {
            $completionComment = SocialActivity::create([
                'user_id' => auth()->id(),
                'type' => 'comment',
                'visibility' => 'public',
                'content' => 'Task: ' . $task->questTask->title . ' completed.',
                'comment_target' => $task->questTask->quest->socialActivity->id,
            ]);

            $task->update([
                'completion_status' => 'submitted',
                'completed_at' => Carbon::now()
            ]);

            return $this->success('Task completion submitted for approval', $task, 200);

        } elseif (($task->questParticipant->user_id === auth()->id())&&($task->completion_status !== null)) {

            return $this->error('Task completion already submitted', 401);

        } else {
            if (auth()->user()->id() === $task->questTask->quest->creator_id) {
                if ($request->approve) {
                    if ($task->completion_status === 'community_verified') {
                        $task->update([
                            'completion_status' => 'completed',
                            'approved_at' => Carbon::now()
                        ]);

                        return $this->success('Task completion approved', [], 200);
                    } else {
                        return $this->error('Completion not yet community verified', 401);
                    }
                }
            }

            $verifies = CompletionVerification::where('quest_participant_task_id', $task->id)
                ->where('type', 'verification')
                ->count();

            $flags = CompletionVerification::where('quest_participant_task_id', $task->id)
                ->where('type', 'flag')
                ->count();

            $existingVerification = CompletionVerification::where('user_id', auth()->id())
                ->where('quest_participant_task_id', $task->id)
                ->first();

            if ($existingVerification) {
                return $this->error('Completion submission already verified', 400);
            }

            if ($request->verify) {
                $verify = CompletionVerification::create([
                    'type' => 'verification',
                    'user_id' => auth()->id(),
                    'quest_participant_task_id' => $task->id
                ]);

                if (($verifies > 10)&&($verifies > $task->flags)&&($task->completion_status !== 'completed')) {
                    $task->update([
                        'completion_status' => 'community_verified'
                    ]);
                }
            } elseif ($request->flag) {
                $flag = CompletionVerification::create([
                    'type' => 'flag',
                    'user_id' => auth()->id(),
                    'quest_participant_task_id' => $task->id
                ]);

                if (($flags > 10)&&($verifies <= $flags)&&($task->completion_status !== 'completed')) {
                    $task->update([
                        'completion_status' => 'flagged'
                    ]);
                }
            }

            return $this->success('Completion verification/flag submitted', $verify ?? $flag, 200);         
        }
    }
}
