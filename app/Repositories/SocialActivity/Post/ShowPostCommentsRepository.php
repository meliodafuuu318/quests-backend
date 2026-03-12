<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    SocialActivity,
    Media,
    QuestParticipant,
    QuestParticipantTask,
    CompletionVerification
};

class ShowPostCommentsRepository extends BaseRepository
{
    public function execute($request)
    {
        $request->validate(['postId' => 'required']);

        $post = SocialActivity::where('type', 'post')
            ->where('id', $request->postId)
            ->first();

        if (!$post) {
            return $this->error('Post not found', 404);
        }

        $userId = auth()->id();

        $postComments = SocialActivity::where('type', 'comment')
            ->where('comment_target', $post->id)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $transformed = $postComments->getCollection()->map(function ($comment) use ($userId, $post) {
            $media = Media::where('social_activity_id', $comment->id)
                ->get()
                ->map(fn($m) => ['filepath' => $m->filepath])
                ->values();

            // MySQL tinyint(1) comes back as int 0/1 in PHP — cast properly
            $isVerification = (int) $comment->verification_submission === 1;

            // For verification submissions, pull task status + vote counts
            $completionStatus     = null;
            $approveCount         = 0;
            $flagCount            = 0;
            $myVote               = null; // 'approved' | 'flagged' | null
            $questParticipantTaskId = null;

            if ($isVerification) {
                $participant = QuestParticipant::where('user_id', $comment->user_id)
                    ->whereHas('quest', fn($q) => $q->where('post_id', $post->id))
                    ->first();

                if ($participant) {
                    // Pick the task that was submitted most recently by this participant
                    // whose completion comment content matches (best-effort: just grab submitted/later)
                    $task = QuestParticipantTask::where('quest_participant_id', $participant->id)
                        ->where('completion_comment_id', $comment->id)
                        ->whereNotNull('completion_status')
                        ->orderBy('completed_at', 'desc')
                        ->first();

                    if ($task) {
                        $questParticipantTaskId = $task->id;
                        $completionStatus       = $task->completion_status;

                        $approveCount = CompletionVerification::where('quest_participant_task_id', $task->id)
                            ->where('type', 'verification')
                            ->count();

                        $flagCount = CompletionVerification::where('quest_participant_task_id', $task->id)
                            ->where('type', 'flag')
                            ->count();

                        $myVerification = CompletionVerification::where('quest_participant_task_id', $task->id)
                            ->where('user_id', $userId)
                            ->first();

                        if ($myVerification) {
                            $myVote = $myVerification->type === 'verification' ? 'approved' : 'flagged';
                        }
                    }
                }
            }

            return [
                'id'                       => $comment->id,
                'username'                 => $comment->user->username,
                'postId'                   => $comment->comment_target,
                'content'                  => $comment->content,
                'media'                    => $media,
                'createdAt'                => $comment->created_at->format('Y-m-d h:i'),
                // verification fields
                'is_verification'          => $isVerification,
                'completion_status'        => $completionStatus,
                'approve_count'            => $approveCount,
                'flag_count'               => $flagCount,
                'my_vote'                  => $myVote,        // null | 'approved' | 'flagged'
                'quest_participant_task_id' => $questParticipantTaskId,
            ];
        });

        $postComments->setCollection($transformed);

        return $this->success('Post comments fetched successfully', $postComments, 200);
    }
}