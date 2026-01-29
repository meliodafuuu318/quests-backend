<?php

namespace App\Http\Requests\SocialActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required', Rule::in(['post', 'comment', 'like']),
            'title' => 'sometimes|string',
            'content' => 'sometimes|string',
            'visibility' => 'required', Rule::in(['public', 'friends', 'private']),
            'rewardExp' => 'required|numeric|min:0',
            'rewardPoints'=> 'required|numeric|min:0',
            'tasks.*.title' => 'string',
            'tasks.*.description' => 'string',
            'tasks.*.rewardExp' => 'numeric|min:0',
            'tasks.*.rewardPoints' => 'numeric|min:0',
            'tasks.*.order' => 'numeric|min:1',
            'media' => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif'
        ];
    }
}
