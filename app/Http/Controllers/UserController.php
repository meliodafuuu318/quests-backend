<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User
};
use App\Requests\User\{
    EditAccountInfoRequest
};

class UserController extends Controller
{
    public function getAccountInfo() {
        $user = User::find(auth()->user()->id);
        
        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User info fetched successfully', $user, 200);
    }

    public function editAccountInfo(EditAccountInfoRequest $request) {
        DB::beginTransaction();
        try {
            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollback();
            return 0;
        }
    }

    public function indexUsers() {
        //
    }

    public function showUser() {
        //
    }

    public function indexUserPosts() {
        //
    }
}
