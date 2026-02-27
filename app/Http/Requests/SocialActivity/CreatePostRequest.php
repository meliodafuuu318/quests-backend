<?php

namespace App\Http\Requests\SocialActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'         => ['required', Rule::in(['post', 'comment', 'like'])],
            'title'        => 'sometimes|string',
            'content'      => 'sometimes|string',
            'visibility'   => ['required', Rule::in(['public', 'friends', 'private'])],
            'rewardExp'    => 'required|numeric|min:0',
            'rewardPoints' => 'required|numeric|min:0',

            'tasks.*.title'        => 'string',
            'tasks.*.description'  => 'string',
            'tasks.*.rewardExp'    => 'numeric|min:0',
            'tasks.*.rewardPoints' => 'numeric|min:0',
            'tasks.*.order'        => 'numeric|min:1',

            // Original was: 'media' => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif'
            // That rule expects a SINGLE UploadedFile and rejects an array — causing the
            // 422 "must be a file" error every time Flutter sends media[].
            // Fix: validate as array and check each element individually.
            // Video mimes added so videos no longer cause a connection-level error.
            'media'   => 'sometimes|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:102400',
        ];
    }
}