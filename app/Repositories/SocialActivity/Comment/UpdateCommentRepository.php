<?php

namespace App\Repositories\SocialActivity\Comment;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};

class UpdateCommentRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);
        $comment = SocialActivity::where('id', $request->commentId)
            ->where('user_id', $user->id)
            ->where('type', 'comment')
            ->first();

        if (!$comment) {
            return $this->error('Comment not found', 404);
        }

        $comment->update([
            'content' => $request->content
        ]);

        return $this->success('Comment updated', $comment, 200);
    }
}
