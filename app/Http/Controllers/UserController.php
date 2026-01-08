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
            ->where('status', 'friend')
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
        $user = User::find(auth()->user()->id);
        $targetUser = User::where('username', $request->username)->first();

        $blockExists = Friend::where(function ($self) use ($user) {
                $self->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);                
            })->where(function ($target) use ($targetUser) {
                $target->where('user_id', $targetUser->id)
                    ->orWhere('friend_id', $targetUser->id);
            })->where('status', 'blocked')
            ->first();
            
        if ($blockExists) {
            return $this->error('Blocked user', 400);
        }

        if (!$user) {
            return $this->error('User not found', 404);
        }

        return $this->success('User details fetched successfully', $user, 200);
    }

    public function indexUserPosts(Request $request) {
        $user = User::find(auth()->user()->id);
        $targetUser = User::where('username', $request->username)->first();

        if (!$user) {
            return $this->error('User not found', 404);
        }

        $blockExists = Friend::where('status', 'blocked')
            ->where(function ($self) use ($user) {
                $self->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);                
            })->where(function ($target) use ($targetUser) {
                $target->where('user_id', $targetUser->id)
                    ->orWhere('friend_id', $targetUser->id);
            })->first();
            
        if ($blockExists) {
            return $this->error('Blocked user', 400);
        }

        $userPosts = SocialActivity::where('user_id', $targetUser->id)
            ->where('type', 'post')
            ->orderBy('created_at', 'desc')
            ->get();

        $trasformedUserPosts = $userPosts->map(function ($post) {
            return [
                'username' => $post->user->username,
                'id' => $post->id,
                // 'title' => $post->quest->title,
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

            $blockExists = Friend::where(function ($self) use ($user) {
                    $self->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);                
                })->where(function ($target) use ($friend) {
                    $target->where('user_id', $friend->id)
                        ->orWhere('friend_id', $friend->id);
                })->where('status', 'blocked')
                ->first();
            
            if ($blockExists) {
                return $this->error('Blocked user', 400);
            }

            $requestExists = Friend::where(function ($self) use ($user) {
                    $self->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);                
                })->where(function ($target) use ($friend) {
                    $target->where('user_id', $friend->id)
                        ->orWhere('friend_id', $friend->id);
                })->whereIn('status', ['pending_request', 'friend'])
                ->first();

            if ($requestExists) {
                return $this->error('Friend request already exists', 400);
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
                $friendRequest = Friend::where('user_id', $friend->id)
                    ->where('friend_id', $user->id)
                    ->where('status', 'pending_request')
                    ->first();
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

        $blocked = Friend::where('status', 'blocked')
            ->where('user_id', $user->id)
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

        $transformedBlocked = $blocked->map(function ($block) use ($user) {
            return [
                'username' => $block->friend->username,
                'blockedSince' => $block->updated_at
            ];
        });

        return $this->success('Friend list fetched successfully', ['friends' => $transformedFriends, 'blocked' => $transformedBlocked], 200);
    }

    public function blockUser(Request $request) {
        if ($request->block === true) {
            $user = User::find(auth()->user()->id);
            $blockedUser = User::find($request->userId);

            if (!$blockedUser) {
                return $this->error('User not found', 404);
            }

            $friendExists = Friend::where('user_id', $user->id)
                ->orWhere('friend_id', $user->id)
                ->whereNot('status', 'blocked')
                ->first();

            $friendExists = Friend::where(function ($friend) use ($request, $user) {
                    $friend->where('user_id', $user->id)
                        ->where('friend_id', $request->userId);
                })->orWhere(function ($friend) use ($request, $user) {
                    $friend->where('friend_id', $user->id)
                        ->where('user_id', $request->userId);
                })->first();
            
            if ($friendExists) {
                $friendExists->update([
                    'user_id' => auth()->user()->id,
                    'friend_id' => $blockedUser->id,
                    'status' => 'blocked'
                ]);

                return $this->success('User has been blocked', 200);
            }

            $blockExists = Friend::where(function ($self) use ($user) {
                    $self->where('user_id', $user->id)
                        ->orWhere('friend_id', $user->id);                
                })->where(function ($target) use ($friend) {
                    $target->where('user_id', $friend->id)
                        ->orWhere('friend_id', $friend->id);
                })->where('status', 'blocked')
                ->first();
                
            if ($blockExists) {
                return $this->error('User already blocked', 400);
            }

            $block = Friend::create([
                'user_id' => $user->id,
                'friend_id' => $blockedUser->id,
                'status' => 'blocked'
            ]);

            return $this->success('User has been blocked', 200);
        }
    }
}
