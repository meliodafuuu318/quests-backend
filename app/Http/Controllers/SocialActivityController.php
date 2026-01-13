<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\SocialActivity\{
    CreateCommentRequest,
    CreatePostRequest,
    UpdateCommentRequest,
    UpdatePostRequest
};

use App\Repositories\SocialActivity\{
    Comment\CreateCommentRepository,
    Comment\UpdateCommentRepository,
    Comment\DeleteCommentRepository,
    Post\CreatePostRepository,
    Post\ShowPostRepository,
    Post\UpdatePostRepository,
    Post\DeletePostRepository,
    IndexPostsRepository,
    ReactRepository,
};

class SocialActivityController extends Controller
{
    protected $createPost, $showPost, $updatePost, $deletePost, $createComment, $updateComment, $deleteComment, $react, $indexPosts;

    public function __construct(
        CreateCommentRepository $createComment,
        UpdateCommentRepository $updateComment,
        DeleteCommentRepository $deleteComment,
        CreatePostRepository $createPost,
        ShowPostRepository $showPost,
        UpdatePostRepository $updatePost,
        DeletePostRepository $deletePost,
        IndexPostsRepository $indexPosts,
        ReactRepository $react
    ) {
        $this->createComment = $createComment;
        $this->updateComment = $updateComment;
        $this->deleteComment = $deleteComment;
        $this->createPost = $createPost;
        $this->showPost = $showPost;
        $this->updatePost = $updatePost;
        $this->deletePost = $deletePost;
        $this->indexPosts = $indexPosts;
        $this->react = $react;
    }
    public function createPost(CreatePostRequest $request) {
        return $this->createPost->execute($request);
    }

    public function updatePost(UpdatePostRequest $request) {
        return $this->updatePost->execute($request);
    }

    public function deletePost(Request $request) {
        return $this->deletePost->execute($request);
    }

    public function createComment(CreateCommentRequest $request) {
        return $this->createComment->execute($request);
    }

    public function showPost(Request $request) {
        return $this->showPost->execute($request);
    }

    public function updateComment(UpdateCommentRequest $request) {
        return $this->updateComment->execute($request);
    }

    public function deleteComment(Request $request) {
        return $this->deleteComment->execute($request);
    }

    public function react(Request $request) {
        return $this->react->execute($request);
    }

    public function indexPosts(Request $request) {
        return $this->indexPosts->execute($request);
    }
}
