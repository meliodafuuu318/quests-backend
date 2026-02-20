<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->user->username,
            'postId' => $this->comment_target,
            'content' => $this->content,
            'media' => $this->media->filepath,
            'createdAt' => $this->created_at->format('Y-m-d h:i')
        ];
    }
}
