<?php

namespace App\Http\Controllers;

use Controllers\Controller;
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
    Post\UpdatePostRepository,
    Post\DeletePostRepository,
    IndexPostsRepository,
    ReactRepository,
};

class SocialActivityController extends Controller
{
    protected $createPost, $updatePost, $deletePost, $createComment, $updateComment, $deleteComment, $react, $indexPosts;

    public function __construct(
        CreateCommentRepository $createComment,
        UpdateCommentRepository $updateComment,
        DeleteCommentRepository $deleteComment,
        CreatePostRepository $createPost,
        UpdatePostRepository $updatePost,
        DeletePostRepository $deletePost,
        IndexPostsRepository $indexPosts,
        ReactRepository $react
    ) {
        $this->createComment = $createComment;
        $this->updateComment = $updateComment;
        $this->deleteComment = $deleteComment;
        $this->createPost = $createPost;
        $this->updatePost = $updatePost;
        $this->deletePost = $deletePost;
        $this->indexPosts = $indexPosts;
        $this->react = $react;
    }
    public function createPost(CreatePostRequest $request) {
        $this->createPost->execute();
    }

    public function updatePost(UpdatePostRequest $request) {
        $this->updatePost->execute();
    }

    public function deletePost(Request $request) {
        $this->deletePost->execute();
    }

    public function createComment(CreateCommentRequest $request) {
        $this->createComment->execute();
    }

    public function updateComment(UpdateCommentRequest $request) {
        $this->updateComment->execute();
    }

    public function deleteComment(Request $request) {
        $this->deleteComment->execute();
    }

    public function react(Request $request) {
        $this->react->execute();
    }

    public function indexPosts(Request $request) {
        $this->indexPosts->execute();
    }
}
