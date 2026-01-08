<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\User\{
    EditAccountInfoRequest
};

use App\Repositories\User\{
    GetAccountInfoRepository,
    EditAccountInfoRepository,
    IndexUsersRepository,
    SearchUsersRepository,
    ShowUserRepository,
    IndexUserPostsRepository,
    SendFriendRequestRepository,
    AcceptFriendRequestRepository,
    IndexFriendRequestsRepository,
    IndexFriendsRepository,
    BlockUserRepository,
};

class UserController extends Controller
{
    protected $getAccountInfo, $editAccountInfo, $indexUsers, $searchUsers, $showUser, $indexUserPosts, $sendFriendRequest, $acceptFriendRequest, $indexFriendRequests, $indexFriends, $blockUser;
    
    public function __construct(
        GetAccountInfoRepository $getAccountInfo,
        EditAccountInfoRepository $editAccountInfo,
        IndexUsersRepository $indexUsers,
        SearchUsersRepository $searchUsers,
        ShowUserRepository $showUser,
        IndexUserPostsRepository $indexUserPosts,
        SendFriendRequestRepository $sendFriendRequest,
        AcceptFriendRequestRepository $acceptFriendRequest,
        IndexFriendRequestsRepository $indexFriendRequests,
        IndexFriendsRepository $indexFriends,
        BlockUserRepository $blockUser,
    ) {
        $this->getAccountInfo = $getAccountInfo;
        $this->editAccountInfo = $editAccountInfo;
        $this->indexUsers = $indexUsers;
        $this->searchUsers = $searchUsers;
        $this->showUser = $showUser;
        $this->indexUserPosts = $indexUserPosts;
        $this->sendFriendRequest = $sendFriendRequest;
        $this->acceptFriendRequest = $acceptFriendRequest;
        $this->indexFriendRequests = $indexFriendRequests;
        $this->indexFriends = $indexFriends;
        $this->blockUser = $blockUser;
    }
    public function getAccountInfo() {
        return $this->getAccountInfo->execute();
    }

    public function editAccountInfo(EditAccountInfoRequest $request) {
        return $this->editAccountInfo->execute($request);
    }

    public function indexUsers() {
        return $this->indexUsers->execute();
    }

    public function searchUsers(Request $request) {
        return $this->searchUsers->execute($request);
    }

    public function showUser(Request $request) {
        return $this->showUser->execute($request);
    }

    public function indexUserPosts(Request $request) {
        return $this->indexUserPosts->execute($request);
    }

    public function sendFriendRequest(Request $request) {
        return $this->sendFriendRequest->execute($request);
    }

    public function acceptFriendRequest(Request $request) {
        return $this->acceptFriendRequest->execute($request);
    }

    public function indexFriendRequests() {
        return $this->indexFriendRequests->execute($request);
    }

    public function indexFriends(Request $request) {
        return $this->indexFriends->execute($request);
    }

    public function blockUser(Request $request) {
        return $this->blockUser->execute($request);
    }
}
