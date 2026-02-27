<?php

namespace App\Repositories\SocialActivity;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};
use App\Events\ReactEvent;

class ReactRepository extends BaseRepository
{
    public function execute($request)
    {
        $user = User::find(auth()->user()->id);

        if ($user && $request->type === 'like') {

            $target = SocialActivity::whereIn('type', ['post', 'comment'])
                ->where('id', $request->likeTarget)
                ->first();

            if (!$target) {
                return $this->error('Content not found', 404);
            }

            $likeExists = SocialActivity::where('like_target', $target->id)
                ->where('user_id', $user->id)
                ->where('type', 'like')
                ->first();

            if ($likeExists) {
                $likeExists->delete();
                $action = 'removed';
            } else {
                SocialActivity::create([
                    'user_id'     => $user->id,
                    'visibility'  => 'public',
                    'type'        => 'like',
                    'like_target' => $target->id,
                ]);
                $action = 'added';
            }

            // Fresh count after toggle
            $likeCount = SocialActivity::where('type', 'like')
                ->where('like_target', $target->id)
                ->count();

            // Broadcast to the post channel
            $postId = $target->type === 'post'
                ? $target->id
                : $target->comment_target;   // comment's parent post

            event(new ReactEvent([
                'post_id'    => $postId,
                'target_id'  => $target->id,
                'likes_count' => $likeCount,
                'action'     => $action,
            ]));

            return $this->success(
                $action === 'removed' ? 'Like removed' : 'Like added',
                ['likes_count' => $likeCount, 'liked' => $action === 'added'],
                200
            );
        }

        return $this->error('Invalid request', 400);
    }
}