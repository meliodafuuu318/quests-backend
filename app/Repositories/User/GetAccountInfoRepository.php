<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;

use App\Models\User;

class GetAccountInfoRepository extends BaseRepository
{
    public function execute(){
        $user = User::find(auth()->user()->id);
        
        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User info fetched successfully', $user, 200);
    }
}
