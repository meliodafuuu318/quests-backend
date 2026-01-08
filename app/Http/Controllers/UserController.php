<?php

namespace App\Http\Controllers;

use Controllers\Controller;
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
        $this->getAccountInfo->execute();
    }

    public function editAccountInfo(EditAccountInfoRequest $request) {
        $this->editAccountInfo->execute();
    }

    public function indexUsers() {
        $this->indexUsers->execute();
    }

    public function searchUsers(Request $request) {
        $this->searchUsers->execute();
    }

    public function showUser(Request $request) {
        $this->showUser->execute();
    }

    public function indexUserPosts(Request $request) {
        $this->indexUserPosts->execute();
    }

    public function sendFriendRequest(Request $request) {
        $this->sendFriendRequest->execute();
    }

    public function acceptFriendRequest(Request $request) {
        $this->acceptFriendRequest->execute();
    }

    public function indexFriendRequests() {
        $this->indexFriendRequests->execute();
    }

    public function indexFriends() {
        $this->indexFriends->execute();
    }

    public function blockUser(Request $request) {
        $this->blockUser->execute();
    }
}
