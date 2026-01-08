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

                if (!$target) {
                    return $this->error('Content not found', 404);
                } else {
                    $like = SocialActivity::create([
                        'user_id' => $user->id,
                        'visibility' => 'public',
                        'type' => 'like',
                        'like_target' => $request->likeTarget
                    ]);
                }
            }
        }
    }
}
