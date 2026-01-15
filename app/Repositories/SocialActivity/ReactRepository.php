<?php

namespace App\Repositories\SocialActivity;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity
};

class ReactRepository extends BaseRepository
{
    public function execute($request){
        $user = User::find(auth()->user()->id);

        if ($user) {
            if ($request->type === 'like') {
                $target = SocialActivity::whereIn('type', ['post', 'comment'])
                    ->where('id', $request->likeTarget)
                    ->first();
                
                $likeExists = SocialActivity::where('like_target', $target->id)
                    ->where('user_id', $user->id)
                    ->where('type', 'like')
                    ->first();

                if ($likeExists) {
                    $likeExists->delete();
                    return $this->success('Like removed', 200);
                }

                if (!$target) {
                    return $this->error('Content not found', 404);
                } else {
                    $like = SocialActivity::create([
                        'user_id' => $user->id,
                        'visibility' => 'public',
                        'type' => 'like',
                        'like_target' => $target->id
                    ]);
                }
            }
        }
    }
}
