<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User,
    Friend
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

    public function indexUsers() {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $friendIds = Friend::where('user_id', $user->id)
            ->pluck('friend_id')
            ->merge(
                Friend::where('friend_id', $user->id)->pluck('user_id')
            )
            ->unique()
            ->values();

        $mutualIds = Friend::whereIn('user_id', $friendIds)
            ->orWhereIn('friend_id', $friendIds)
            ->get()
            ->flatMap(function ($friend) {
                return [$friend->user_id, $friend->friend_id];
            })
            ->unique()
            ->reject(fn ($id) => $id == auth()->id())
            ->reject(fn ($id) => $friendIds->contains($id))
            ->values();

        $recommended = User::whereIn('id', $mutualIds)
            ->get();
            // ->paginate(10);

        $transformedRecommended = $recommended->map(function ($rec) {
            return [
                'username' => $rec->username,
                'firstName' => $rec->first_name,
                'lastName' => $rec->last_name,
            ];
        });

        return $this->success('Recommended friends fetched successfully.', $transformedRecommended, 200);
    }

    public function searchUsers(Request $request) {
        $query = User::query();

        if ($request->filled('name')) {
            $query->where('username', 'like', '%' . $request->name . '%')
                ->orwhere('first_name', 'like', '%' . $request->name . '%')
                ->orwhere('last_name', 'like', '%' . $request->name . '%');
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

    public function showUser(Request $request) {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User details fetched successfully', $user, 200);
    }

    public function indexUserPosts() {
        //
    }

    public function sendFriendRequest() {
        //
    }

    public function acceptFriendRequest() {
        //
    }
}
