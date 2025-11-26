<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User,
    Friend,
    SocialActivity
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
        if (!$request->filled('name')) {
            return $this->success('Users fetched successfully', [], 200);
        }

        $keyword = $request->name;

        $meiliResults = User::search($keyword)->get();

        $soundexResults = User::whereRaw("SOUNDEX(username) = SOUNDEX(?)", [$keyword])
            ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$keyword])
            ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$keyword])
            ->get();


        $combined = $meiliResults
            ->merge($soundexResults)
            ->unique('id')
            ->values();

        $users = $combined->map(function ($user) {
            return [
                'username'  => $user->username,
                'firstName' => $user->first_name,
                'lastName'  => $user->last_name,
            ];
        });

        return $this->success('Users fetched successfully', $users, 200);
    }

    public function showUser(Request $request) {
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User details fetched successfully', $user, 200);
    }

    public function indexUserPosts(Request $request) {
        $user = User::find($request->username);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $userPosts = SocialActivity::where('user_id', $user->id)
            ->where('type', 'post')
            ->orderBy('created_at', 'desc')
            ->get();

        $trasformedUserPosts = $userPosts->map(function ($post) {
            return [
                'username' => $post->user->username,
                'id' => $post->id,
                'title' => $post->quest->title,
                'datetime' => $post->created_at,
            ];
        });

        return $this->success('User posts fetched', $trasformedUserPosts, 200);
    }

    public function sendFriendRequest(Request $request) {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        if ($request->has('username')) {
            $friend = User::where('username', $request->username)->first();

            if (!$friend) {
                return $this->error('User not found', 404);
            }

            $friendRequest = Friend::create([
                'user_id' => $user->id,
                'friend_id' => $friend->id,
            ]);

            return $this->success('Friend request sent', $friendRequest, 200);
        }
    }

    public function acceptFriendRequest(Request $request) {
        $user = User::find(auth()->user()->id);

        $friendIds = Friend::where('friend_id', $user->id)
            ->where('status', 'pending_request')
            ->pluck('user_id');
        
        // $friendUsernames = [];
        // $friendUsernames = collect($friendUsernames);

        // foreach ($friendIds as $id) {
        //     $friend = User::where('id', $id)->first();

        //     $friendUsernames->append($friend->username);
        // }

        $friendUsernames = User::whereIn('id', function ($query) use ($user) {
            $query->select('user_id')
                ->from('friends')
                ->where('friend_id', $user->id)
                ->where('status', 'pending_request');
        })->pluck('username');

        $friendRequest = null;

        foreach ($friendUsernames as $username) {
            if ($username !== $request->username) {
                continue;
            } else {
                $friend = User::where('username', $username)->first();
                $friendRequest = Friend::where('user_id', $friend->id)->first();
            }
        }

        if ($friendRequest === null) {
            return $this->error('Friend request not found', 404);
        } else {
            $friendRequest->update(['status' => 'friend']);
            return $this->success('Friend request accepted', 200);
        }
    }

    public function indexFriendRequests() {
        $user = User::find(auth()->user()->id);

        $incomingRequests = Friend::where('friend_id', $user->id)
            ->where('status', 'pending_request')
            ->get();
        $sentRequests = Friend::where('user_id', $user->id)
            ->where('status', 'pending_request')
            ->get();
        
        $allRequests = [
            'incomingRequests' => $incomingRequests->map(function ($friendRequest) {
                    return  [
                        'sender' => $friendRequest->user->username,
                        'sentOn' => $friendRequest->created_at,
                    ];
                }),
            'sentRequests' => $sentRequests->map(function ($friendRequest) {
                    return [
                        'sentTo' => $friendRequest->friend->username,
                        'sentOn' => $friendRequest->created_at,
                    ];
                }),
        ];

        return $this->success('Friend requests fetched successfully', $allRequests, 200);
    }

    public function indexFriends() {
        $user = User::find(auth()->user()->id);

        $friends = Friend::where('status', 'friend')
            ->where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)
            ->get();

        $transformedFriends = $friends->map(function ($friend) use ($user) {
            if ($user->id === $friend->user_id) {
                $username = $friend->friend->username;
            } elseif ($user->id === $friend->friend_id) {
                $username = $friend->user->username;
            }
            return [
                'username' => $username,
                'friendsSince' => $friend->updated_at
            ];
        });

        return $this->success('Friend list fetched successfully', $transformedFriends, 200);
    }
}
