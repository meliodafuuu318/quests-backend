<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    QuestParticipant,
    QuestParticipantTask,
    SocialActivity,
    CompletionVerification,
    Media
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
            $request->validate([
                'file' => 'required|file'
            ]);

            $completionComment = SocialActivity::create([
                'user_id' => auth()->id(),
                'type' => 'comment',
                'visibility' => 'public',
                'content' => 'Task: ' . $task->questTask->title . ' completed.',
                'comment_target' => $task->questTask->quest->socialActivity->id,
            ]);

            if ($request->has('file')) {
                $file = $request->file;
                $filePath = $file->storeAs(
                    'media/' . Carbon::now()->format('Y/m/d'),
                    'upload-' . auth()->user()->username . '-' . uniqid() . '.' . $file->extension(),
                    'public'
                );

                $proof = Media::create([
                    'filepath' => '/storage/' . $filePath,
                    'user_id' => auth()->id(),
                    'social_activity_id' => $completionComment->id
                ]);
            }

            $task->update([
                'completion_status' => 'submitted',
                'completed_at' => Carbon::now()
            ]);

            $data = [
                'task' => $task,
                'proof' => $proof
            ];

            return $this->success('Task completion submitted for approval', $data, 200);

        } elseif (($task->questParticipant->user_id === auth()->id())&&($task->completion_status !== null)) {

            return $this->error('Task completion already submitted', 401);

        } else {
            if (auth()->id() === $task->questTask->quest->creator_id) {
                if (isset($request->approve)) {
                    $approvalExists = $task->completion_status === 'completed' || CompletionVerification::where('user_id', auth()->id())->where('quest_participant_task_id', $task->id)->exists();
                    if ($approvalExists) {
                        return $this->error('Submission already approved');
                    }
                    if ($task->completion_status === 'community_verified') {
                        CompletionVerification::create([
                            'type' => 'verification',
                            'user_id' => auth()->id(),
                            'quest_participant_task_id' => $task->id
                        ]);
                        $task->update([
                            'completion_status' => 'completed',
                            'approved_at' => Carbon::now()
                        ]);

                        $questParticipant = $task->questParticipant;
                        $participantUser = User::find($questParticipant->user_id);

                        $participantUser->update([
                            'exp' => $participantUser->exp + $task->questTask->reward_exp
                        ]);
                        $points = floatval($task->questTask->reward_points);
                        $participantUser->creditAdd($points, 'Completed task');

                        return $this->success('Task completion approved', [], 200);
                    } else {
                        return $this->error('Completion not yet community verified', 401);
                    }
                }
            } else {
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

                    if (($verifies > 5)&&($verifies > $task->flags)&&($task->completion_status !== 'completed')) {
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

                    if (($flags > 5)&&($verifies <= $flags)&&($task->completion_status !== 'completed')) {
                        $task->update([
                            'completion_status' => 'flagged'
                        ]);
                    }
                }

                return $this->success('Completion verification/flag submitted', $verify ?? $flag ?? null, 200);
            }
        }
    }
}
