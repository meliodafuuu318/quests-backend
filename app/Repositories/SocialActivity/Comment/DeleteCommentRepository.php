<?php

namespace App\Repositories\SocialActivity\Comment;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};

class DeleteCommentRepository extends BaseRepository
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

        $comment->delete();

        return $this->success('Comment deleted', 200);
    }
}
