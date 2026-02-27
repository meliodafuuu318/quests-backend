<?php

namespace App\Http\Requests\SocialActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Original had:  'commentTarget' => 'required', Rule::in(...)
            // The comma makes Rule::in() a SEPARATE top-level array entry with no key,
            // which Laravel ignores — so commentTarget only got 'required', not the
            // existence check. Fixed by using proper array syntax.
            'commentTarget' => ['required', 'exists:social_activities,id'],

            // Original was 'required|string' — blocked media-only comments.
            // Changed to 'sometimes' so a comment with only attached media is valid.
            'content' => 'sometimes|nullable|string',

            // Same fix as CreatePostRequest: was single-file rule, now array.
            'media'   => 'sometimes|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:102400',
        ];
    }
}