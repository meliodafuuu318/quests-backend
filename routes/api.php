<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    UserController,
    SocialActivityController,
    QuestController,
};

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'auth'
], function ($route) {
    $route->post('/register', [AuthController::class, 'register']);
    $route->post('/login', [AuthController::class, 'login']);
    $route->post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group([
    'prefix' => 'user',
    'middleware' => 'auth:sanctum'
], function ($route) {
    $route->get('/account-info', [UserController::class, 'getAccountInfo']);
    $route->put('/account-info', [UserController::class, 'editAccountInfo']);
    $route->get('/', [UserController::class, 'indexUsers']);
    $route->get('/search', [UserController::class, 'searchUsers']);
    $route->get('/show', [UserController::class, 'showUser']);
    $route->get('/posts', [UserController::class, 'indexUserPosts']);
});

Route::group([
    'prefix' => 'user/friend',
    'middleware' => 'auth:sanctum'
], function ($route) {
    $route->get('/requests', [UserController::class, 'indexFriendRequests']);
    $route->get('/', [UserController::class, 'indexFriends']);
    $route->put('/accept', [UserController::class, 'acceptFriendRequest']);
    $route->post('/send', [UserController::class, 'sendFriendRequest']);
    $route->post('/block', [UserController::class, 'blockUser']);
});

Route::group([
    'prefix' => 'post',
    'middleware' => 'auth:sanctum'
], function ($route) {
    $route->get('/', [SocialActivityController::class, 'indexPosts']);
    $route->post('/', [SocialActivityController::class, 'createPost']);
    $route->get('/show', [SocialActivityController::class, 'showPost']);
    $route->put('/update', [SocialActivityController::class, 'updatePost']);
    $route->delete('/delete', [SocialActivityController::class, 'deletePost']);
    $route->get('/comments', [SocialActivityController::class, 'showPostComments']);
    $route->get('/reacts', [SocialActivityController::class, 'showPostReacts']);
});

Route::group([
    'prefix' => 'comment',
    'middleware' => 'auth:sanctum'
], function ($route) {
    $route->post('/create', [SocialActivityController::class, 'createComment']);
    $route->put('/update', [SocialActivityController::class, 'updateComment']);
    $route->delete('/delete', [SocialActivityController::class, 'deleteComment']);
});

Route::post('/react', [SocialActivityController::class, 'react'])->middleware('auth:sanctum');

Route::group([
    'prefix' => 'quest',
    'middleware' => 'auth:sanctum'
], function ($route) {
    $route->put('/update', [QuestController::class, 'updateQuest']);
    $route->post('/join', [QuestController::class, 'joinQuest']);
    $route->put('/task/update', [QuestController::class, 'updateQuestTask']);
});