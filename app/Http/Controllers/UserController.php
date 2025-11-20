<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User
};
use App\Http\Requests\User\{
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
            $user = User::find(auth()->user()->id);

            if (!$user) {
                return $this->error('User not found', 404);
            }

            $user->update([
                'username' => $request->username ?? $user->username,
                'first_name' => $request->firstName ?? $user->first_name,
                'last_name' => $request->lastName ?? $user->last_name,
                'birthdate' => $request->birthdate ?? $user->birthdate,
                'gender' => $request->gender ?? $user->gender,
                'city' => $request->city ?? $user->city,
                'province' => $request->province ?? $user->province,
                'country' => $request->country ?? $user->country,
                'contact_number' => $request->contactNumber ?? $user->contact_number,
                'bio' => $request->bio ?? $user->bio,
            ]);

            DB::commit();
            return $this->success('User info updated successfully', $user, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('User info update failed', 500, $e);
        }
    }

    public function indexUsers(Request $request) {
        $query = User::query();

        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }
        if ($request->filled('firstName')) {
            $query->where('first_name', 'like', '%' . $request->firstName . '%');
        }
        if ($request->filled('lastName')) {
            $query->where('last_name', 'like', '%' . $request->lastName . '%');
        }
        $filteredUsers = $query->get();
        $transformedUsers = $filteredUsers->map(function ($user) {
            return [
                'username' => $user->username,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name
            ];
        });

        return $this->success('Users fetched successfully', $transformedUsers, 200);
    }

    public function showUser() {
        //
    }

    public function indexUserPosts() {
        //
    }
}
