<?php

namespace App\Repositories\SocialActivity\Comment;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};
use Illuminate\Support\Facades\DB;

class CreateCommentRepository extends BaseRepository
{
    public function execute(){
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'comment') {
            try {
                $post = SocialActivity::where('id', $request->commentTarget)
                    ->where('type', 'post')
                    ->first();

                if (!$post) {
                    return $this->error('Post not found', 404);
                } else {
                    $comment = SocialActivity::create([
                        'user_id' => $user->id,
                        'visibility' => 'public',
                        'type' => 'comment',
                        'comment_target' => $request->commentTarget,
                        'content' => $request->content
                    ]);
                }
                DB::commit();
                return $this->success('Comment created successfully', $comment, 200);
            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('Something went wrong', 500, $e);
            }
        }
    }
}
