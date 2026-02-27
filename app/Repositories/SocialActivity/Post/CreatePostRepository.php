<?php

namespace App\Repositories\SocialActivity\Post;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity,
    Quest,
    QuestTask,
    Friend,
    Media
};
use App\Events\PostEvent;
use Illuminate\Support\Facades\DB;

class CreatePostRepository extends BaseRepository
{
    public function execute($request)
    {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'post') {
            try {
                $post = SocialActivity::create([
                    'user_id'    => $user->id,
                    'type'       => 'post',
                    'title'      => $request->title,
                    'content'    => $request->content,
                    'visibility' => $request->visibility,
                ]);

                // ── Handle media uploads ──────────────────────────────────────
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $file) {
                        $filePath = $file->storeAs(
                            'media/' . now()->format('Y/m/d'),
                            'upload-' . $user->username . '-' . uniqid() . '.' . $file->extension(),
                            'public'
                        );
                        Media::create([
                            'filepath'           => '/storage/' . $filePath,
                            'user_id'            => $user->id,
                            'social_activity_id' => $post->id,
                        ]);
                    }
                }

                $quest = Quest::create([
                    'code'          => $this->questCode(),
                    'post_id'       => $post->id,
                    'creator_id'    => $user->id,
                    'reward_exp'    => $request->rewardExp,
                    'reward_points' => $request->rewardPoints,
                ]);

                foreach ($request->tasks as $task) {
                    QuestTask::create([
                        'quest_id'      => $quest->id,
                        'title'         => $task['title'],
                        'description'   => $task['description'],
                        'reward_exp'    => $task['rewardExp'],
                        'reward_points' => $task['rewardPoints'],
                        'order'         => $task['order'],
                    ]);
                }

                DB::commit();

                // ── Broadcast via Pusher ──────────────────────────────────────
                $friendIds = Friend::where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->orWhere('friend_id', $user->id);
                    })
                    ->where('status', 'friend')
                    ->get()
                    ->map(fn($f) => $f->user_id === $user->id ? $f->friend_id : $f->user_id)
                    ->values()
                    ->toArray();

                $broadcastData = [
                    'id'         => $post->id,
                    'title'      => $post->title,
                    'username'   => $user->username,
                    'visibility' => $post->visibility,
                    'is_friend'  => true,   // recipients are friends by definition
                    'created_at' => $post->created_at->toIso8601String(),
                ];

                // Only broadcast to friends' private channels when visibility allows
                $notifyFriends = in_array($post->visibility, ['public', 'friends'])
                    ? $friendIds
                    : [];

                event(new PostEvent($broadcastData, $notifyFriends));

                return $this->success('Post created successfully.', [
                    'post'  => $post,
                    'quest' => $quest,
                    'tasks' => $quest->questTask,
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('Something went wrong', 500, $e->getMessage());
            }
        }
    }
}