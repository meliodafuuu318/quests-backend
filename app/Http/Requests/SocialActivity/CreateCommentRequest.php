<?php

namespace App\Http\Requests\SocialActivity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\SocialActivity;

class CreateCommentRequest extends FormRequest
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
            'commentTarget' => 'required', Rule::in(SocialActivity::where('type', 'post')->pluck('id')),
            'content' => 'required|string',
            'media' => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif'
        ];
    }
}
